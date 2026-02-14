const fs = require('fs');
const path = require('path');
const os = require('os');
const { spawn } = require('child_process');

const PRINTER_NAME = 'XP-80C';
const LOGO_PATH = path.join(__dirname, '..', 'public', 'assets', 'img', 'sanjaya.png');

async function printReceipt(receiptData) {
    try {
        let rawData = Buffer.from([0x1B, 0x40]); // ESC @ - Initialize printer

        // Convert and add logo if exists
        if (fs.existsSync(LOGO_PATH)) {
            try {
                const sharp = require('sharp');
                const logoBuffer = await convertImageToESCPOS_GS(LOGO_PATH, sharp);
                rawData = Buffer.concat([rawData, logoBuffer]);
                console.log('Logo converted to ESC/POS');
            } catch (logoError) {
                console.error('Logo conversion failed:', logoError.message);
                console.log('Continuing without logo...');
            }
        }

        // Add receipt text
        const receiptText = generateReceiptText(receiptData);
        rawData = Buffer.concat([rawData, Buffer.from(receiptText, 'ascii')]);

        // Add paper cut command
        rawData = Buffer.concat([rawData, Buffer.from([0x1D, 0x56, 0x41, 0x03])]);

        // Write to temp file
        const tempFile = path.join(os.tmpdir(), 'receipt_' + Date.now() + '.prn');
        fs.writeFileSync(tempFile, rawData);

        console.log('Receipt file created:', tempFile);
        console.log('Total size:', rawData.length, 'bytes');

        await new Promise((resolve, reject) => {
            const psScript = `
$printerName = "${PRINTER_NAME}"
$filePath = "${tempFile.replace(/\\/g, '\\\\')}"

Add-Type -TypeDefinition @"
using System;
using System.IO;
using System.Runtime.InteropServices;

public class RawPrinterHelper {
    [StructLayout(LayoutKind.Sequential, CharSet = CharSet.Ansi)]
    public class DOCINFOA {
        [MarshalAs(UnmanagedType.LPStr)] public string pDocName;
        [MarshalAs(UnmanagedType.LPStr)] public string pOutputFile;
        [MarshalAs(UnmanagedType.LPStr)] public string pDataType;
    }

    [DllImport("winspool.Drv", EntryPoint = "OpenPrinterA", SetLastError = true, CharSet = CharSet.Ansi, ExactSpelling = true, CallingConvention = CallingConvention.StdCall)]
    public static extern bool OpenPrinter([MarshalAs(UnmanagedType.LPStr)] string szPrinter, out IntPtr hPrinter, IntPtr pd);

    [DllImport("winspool.Drv", EntryPoint = "ClosePrinter", SetLastError = true, ExactSpelling = true, CallingConvention = CallingConvention.StdCall)]
    public static extern bool ClosePrinter(IntPtr hPrinter);

    [DllImport("winspool.Drv", EntryPoint = "StartDocPrinterA", SetLastError = true, CharSet = CharSet.Ansi, ExactSpelling = true, CallingConvention = CallingConvention.StdCall)]
    public static extern bool StartDocPrinter(IntPtr hPrinter, int level, [In, MarshalAs(UnmanagedType.LPStruct)] DOCINFOA di);

    [DllImport("winspool.Drv", EntryPoint = "EndDocPrinter", SetLastError = true, ExactSpelling = true, CallingConvention = CallingConvention.StdCall)]
    public static extern bool EndDocPrinter(IntPtr hPrinter);

    [DllImport("winspool.Drv", EntryPoint = "StartPagePrinter", SetLastError = true, ExactSpelling = true, CallingConvention = CallingConvention.StdCall)]
    public static extern bool StartPagePrinter(IntPtr hPrinter);

    [DllImport("winspool.Drv", EntryPoint = "EndPagePrinter", SetLastError = true, ExactSpelling = true, CallingConvention = CallingConvention.StdCall)]
    public static extern bool EndPagePrinter(IntPtr hPrinter);

    [DllImport("winspool.Drv", EntryPoint = "WritePrinter", SetLastError = true, ExactSpelling = true, CallingConvention = CallingConvention.StdCall)]
    public static extern bool WritePrinter(IntPtr hPrinter, IntPtr pBytes, int dwCount, out int dwWritten);

    public static bool SendBytesToPrinter(string szPrinterName, byte[] pBytes) {
        IntPtr hPrinter = new IntPtr(0);
        DOCINFOA di = new DOCINFOA();
        bool bSuccess = false;
        int dwWritten = 0;

        di.pDocName = "Receipt Print";
        di.pDataType = "RAW";

        if (OpenPrinter(szPrinterName, out hPrinter, IntPtr.Zero)) {
            if (StartDocPrinter(hPrinter, 1, di)) {
                if (StartPagePrinter(hPrinter)) {
                    IntPtr pUnmanagedBytes = Marshal.AllocCoTaskMem(pBytes.Length);
                    Marshal.Copy(pBytes, 0, pUnmanagedBytes, pBytes.Length);
                    bSuccess = WritePrinter(hPrinter, pUnmanagedBytes, pBytes.Length, out dwWritten);
                    Marshal.FreeCoTaskMem(pUnmanagedBytes);
                    EndPagePrinter(hPrinter);
                }
                EndDocPrinter(hPrinter);
            }
            ClosePrinter(hPrinter);
        }

        if (!bSuccess) {
            int errorCode = Marshal.GetLastWin32Error();
            throw new Exception("Print failed with error code: " + errorCode);
        }

        return bSuccess;
    }
}
"@

try {
    $bytes = [System.IO.File]::ReadAllBytes($filePath)
    Write-Host "Read $($bytes.Length) bytes from file"

    $result = [RawPrinterHelper]::SendBytesToPrinter($printerName, $bytes)

    if ($result) {
        Write-Host "Print successful"
        exit 0
    } else {
        Write-Error "Print failed"
        exit 1
    }
} catch {
    Write-Error "Error: $($_.Exception.Message)"
    exit 1
}
`;

            const psFile = path.join(os.tmpdir(), 'print_' + Date.now() + '.ps1');
            fs.writeFileSync(psFile, psScript, 'utf8');

            console.log('Executing PowerShell RAW print script...');

            const child = spawn('powershell.exe', ['-ExecutionPolicy', 'Bypass', '-File', psFile], {
                stdio: 'pipe'
            });

            let output = '';
            let errorOutput = '';

            child.stdout.on('data', (data) => {
                output += data.toString();
            });

            child.stderr.on('data', (data) => {
                errorOutput += data.toString();
            });

            child.on('close', (code) => {
                console.log('PowerShell output:', output);
                if (errorOutput) console.error('PowerShell errors:', errorOutput);

                setTimeout(() => {
                    try {
                        fs.unlinkSync(tempFile);
                        fs.unlinkSync(psFile);
                        console.log('Temp files deleted');
                    } catch (e) {}
                }, 2000);

                if (code === 0) {
                    console.log('Print completed');
                    resolve();
                } else {
                    reject(new Error('Print failed with code ' + code));
                }
            });
        });

        console.log("Print success!");
        return { success: true, message: "Receipt printed successfully" };
    } catch (error) {
        console.error("Print failed:", error.message);
        return {
            success: false,
            message: `Printer error: ${error.message}`
        };
    }
}

// Updated to use full paper width (80mm = 576 pixels @ 180dpi for most thermal printers)
async function convertImageToESCPOS_GS(imagePath, sharp) {
    // For 80mm thermal printer: use 576 pixels width (full paper width)
    // If your printer is 58mm, use 384 pixels instead
    const PRINTER_WIDTH_PIXELS = 576; // Change to 384 for 58mm paper

    const processed = await sharp(imagePath)
        .resize({
            width: PRINTER_WIDTH_PIXELS,
            height: 150,  // Max height for logo
            fit: 'contain',  // Changed from 'inside' to 'contain' to fill width
            background: { r: 255, g: 255, b: 255, alpha: 1 }  // White background
        })
        .greyscale()
        .threshold(128)
        .raw()
        .toBuffer({ resolveWithObject: true });

    const { data, info } = processed;
    let width = info.width;
    let height = info.height;

    console.log(`Logo size: ${width}x${height}px`);

    // Width must be multiple of 8
    const widthBytes = Math.ceil(width / 8);
    width = widthBytes * 8;

    let buffers = [];

    // Left alignment (since we're now using full width)
    buffers.push(Buffer.from([0x1B, 0x61, 0x00]));

    // GS v 0 command - Print raster bit image
    const m = 0; // Normal mode
    const xL = widthBytes & 0xFF;
    const xH = (widthBytes >> 8) & 0xFF;
    const yL = height & 0xFF;
    const yH = (height >> 8) & 0xFF;

    buffers.push(Buffer.from([0x1D, 0x76, 0x30, m, xL, xH, yL, yH]));

    // Convert image to bitmap data
    const bitmapData = [];

    for (let y = 0; y < height; y++) {
        for (let x = 0; x < widthBytes; x++) {
            let byte = 0;
            for (let bit = 0; bit < 8; bit++) {
                const pixelX = x * 8 + bit;
                if (pixelX < info.width) {
                    const idx = y * info.width + pixelX;
                    if (data[idx] === 0) {
                        byte |= (1 << (7 - bit));
                    }
                }
            }
            bitmapData.push(byte);
        }
    }

    buffers.push(Buffer.from(bitmapData));
    buffers.push(Buffer.from([0x0A, 0x0A])); // Two line feeds

    return Buffer.concat(buffers);
}

function generateReceiptText(receiptData) {
    const THICK_SEPARATOR = '=============================================';
    const THIN_SEPARATOR = '----------------------------------------------';
    const LINE_WIDTH = THICK_SEPARATOR.length;

    function centerText(text) {
        const padding = Math.floor((LINE_WIDTH - text.length) / 2);
        return ' '.repeat(Math.max(0, padding)) + text + '\n';
    }

    function rightAlignLabelValue(label, value) {
        const left = label + ' ';
        const spaces = LINE_WIDTH - left.length - value.length;
        if (spaces <= 0) {
            return left + value + '\n';
        }
        return left + ' '.repeat(spaces) + value + '\n';
    }

    let receipt = '';

    receipt += centerText('SANJAYA TAILOR');
    receipt += centerText('Wellawaya Road,');
    receipt += centerText('Monaragala, Sri Lanka');
    receipt += centerText('Tel: +94 77 818 1630');
    receipt += THICK_SEPARATOR + '\n';
    receipt += '\n';

    receipt += 'Invoice No: ' + (receiptData.receipt_no || 'N/A') + '\n';
    receipt += 'Date: ' + (receiptData.date || new Date().toLocaleString()) + '\n';
    receipt += THIN_SEPARATOR + '\n';
    receipt += '\n';

    receipt += 'Customer: ' + receiptData.customer_name + '\n';
    receipt += '\n';

    if (Array.isArray(receiptData.items) && receiptData.items.length > 0) {
        receipt += 'ITEMS RENTED:\n';
        receipt += THIN_SEPARATOR + '\n';

        receiptData.items.forEach((item, index) => {
            receipt += 'Item ' + (index + 1) + ':\n';

            if (item.coat) receipt += '  Coat: ' + item.coat + '\n';
            if (item.trouser) receipt += '  Trouser: ' + item.trouser + '\n';
            if (item.west) receipt += '  West: ' + item.west + '\n';
            if (item.national) receipt += '  National: ' + item.national + '\n';

            receipt += '  Price: Rs. ' + parseFloat(item.price).toFixed(2) + '\n';
            receipt += '\n';
        });

        receipt += 'Total Items: ' + receiptData.items.length + '\n';
    }

    receipt += '\n';
    receipt += 'Rental Period:\n';
    receipt += 'From: ' + receiptData.rental_date + '\n';
    receipt += 'To:   ' + receiptData.return_date + '\n';
    receipt += '\n';

    receipt += THIN_SEPARATOR + '\n';
    receipt += rightAlignLabelValue('Total Amount:', 'Rs. ' + parseFloat(receiptData.rent_amount).toFixed(2));
    receipt += rightAlignLabelValue('Payment Received:', 'Rs. ' + parseFloat(receiptData.payment_received).toFixed(2));
    receipt += rightAlignLabelValue('Remaining Payment:', 'Rs. ' + parseFloat(receiptData.remaining_payment).toFixed(2));
    receipt += THIN_SEPARATOR + '\n';
    receipt += '\n';

    receipt += 'Thank you for your business!\n';
    receipt += 'Please return items on time\n';
    receipt += THICK_SEPARATOR + '\n';
    receipt += '\n';
    receipt += 'Powered by Anuruddha Bandara\n';
    receipt += 'Contact Number - +94 71 877 8844\n';
    receipt += '\n\n\n';

    return receipt;
}

module.exports = {
    printReceipt
};
