const { contextBridge, ipcRenderer } = require('electron');

// Expose printer functions to Laravel frontend
contextBridge.exposeInMainWorld('electronAPI', {
  printReceipt: (receiptData) => ipcRenderer.invoke('print-receipt', receiptData),
  testPrinter: () => ipcRenderer.invoke('test-printer')
});

console.log('Preload script loaded - electronAPI available');
