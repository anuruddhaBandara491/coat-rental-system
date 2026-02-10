const { app, BrowserWindow, ipcMain } = require('electron');
const path = require('path');
const http = require('http');
const { spawn } = require('child_process');
const printer = require('./printer');

let mainWindow;
let phpServer;

// Start Laravel development server
function startLaravelServer() {
  return new Promise((resolve, reject) => {
    const laravelPath = app.isPackaged
      ? path.join(process.resourcesPath, 'laravel')
      : path.join(__dirname, '..');
    const phpBinary = process.env.PHP_PATH || 'php';
    let settled = false;

    phpServer = spawn(phpBinary, ['artisan', 'serve', '--host=127.0.0.1', '--port=8000'], {
      cwd: laravelPath,
      stdio: 'pipe'
    });

    phpServer.on('error', (error) => {
      if (settled) {
        return;
      }
      settled = true;
      reject(error);
    });

    phpServer.on('close', (code) => {
      if (settled) {
        return;
      }
      settled = true;
      reject(new Error(`Laravel server exited with code ${code}`));
    });

    phpServer.stdout.on('data', (data) => {
      console.log(`Laravel: ${data}`);
      if (data.toString().includes('started')) {
        resolve();
      }
    });

    phpServer.stderr.on('data', (data) => {
      console.error(`Laravel Error: ${data}`);
    });

    waitForServer('http://127.0.0.1:8000', 20000, 500)
      .then(() => {
        if (settled) {
          return;
        }
        settled = true;
        resolve();
      })
      .catch((error) => {
        if (settled) {
          return;
        }
        settled = true;
        reject(error);
      });
  });
}

function waitForServer(url, timeoutMs, intervalMs) {
  const startTime = Date.now();

  return new Promise((resolve, reject) => {
    const check = () => {
      const elapsed = Date.now() - startTime;
      if (elapsed >= timeoutMs) {
        reject(new Error('Laravel server did not respond in time'));
        return;
      }

      const request = http.get(url, (response) => {
        response.resume();
        resolve();
      });

      request.on('error', () => {
        setTimeout(check, intervalMs);
      });
    };

    check();
  });
}

function createWindow() {
  mainWindow = new BrowserWindow({
    width: 1200,
    height: 800,
    webPreferences: {
      preload: path.join(__dirname, 'preload.js'),
      nodeIntegration: false,
      contextIsolation: true,
    },
    autoHideMenuBar: true, // Hide menu bar for POS look
  });

  // Load Laravel app
  mainWindow.loadURL('http://127.0.0.1:8000');

  // Open DevTools in development
  // mainWindow.webContents.openDevTools();

  mainWindow.on('closed', () => {
    mainWindow = null;
  });
}

// Handle print requests from Laravel
ipcMain.handle('print-receipt', async (event, receiptData) => {
  try {
    console.log('Print request received:', receiptData);
    const result = await printer.printReceipt(receiptData);
    return result;
  } catch (error) {
    console.error('Print error:', error);
    return { success: false, message: error.message };
  }
});

// Handle test print
ipcMain.handle('test-printer', async () => {
  try {
    const result = await printer.testPrinter();
    return result;
  } catch (error) {
    return { success: false, message: error.message };
  }
});

app.on('ready', async () => {
  try {
    console.log('Starting Laravel server...');
    await startLaravelServer();
    console.log('Laravel server started');
    createWindow();
  } catch (error) {
    console.error('Failed to start:', error);
    console.error('Tip: ensure PHP is installed and available in PATH, or set PHP_PATH env var.');
    app.quit();
  }
});

app.on('window-all-closed', () => {
  if (phpServer) {
    phpServer.kill();
  }
  app.quit();
});

app.on('activate', () => {
  if (mainWindow === null) {
    createWindow();
  }
});
