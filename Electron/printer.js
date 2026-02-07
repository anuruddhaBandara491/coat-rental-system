const fs = require('fs');
const path = require('path');
const os = require('os');
const { spawn } = require('child_process');

const PRINTER_NAME = 'XP-80C';

// Your existing printReceipt function
    async function printReceipt(receiptData) {
    try {
        const receipt = generateReceiptText(receiptData);
        const tempFile = path.join(os.tmpdir(), 'receipt_' + Date.now() + '.prn');

        // Write receipt to temp file
        fs.writeFileSync(tempFile, receipt, 'ascii');
        console.log('Receipt file created:', tempFile);

        await new Promise((resolve, reject) => {
        // Use PowerShell to write RAW bytes directly to printer
        const psScript = `
            $filePath = "${tempFile.replace(/\\/g, '\\\\')}"
            $printerName = "${PRINTER_NAME}"

            # Read file content as bytes
            $bytes = [System.IO.File]::ReadAllBytes($filePath)

            # Open printer
            Add-Type -AssemblyName System.Drawing
            Add-Type -AssemblyName System.Printing

            # Send RAW data to printer
            $printerSettings = New-Object System.Drawing.Printing.PrinterSettings
            $printerSettings.PrinterName = $printerName

            $printDocument = New-Object System.Drawing.Printing.PrintDocument
            $printDocument.PrinterSettings = $printerSettings

            # Create stream from bytes
            $stream = New-Object System.IO.MemoryStream(,$bytes)
            $reader = New-Object System.IO.StreamReader($stream)

            $printDocument.add_PrintPage({
                param($sender, $ev)
                $ev.Graphics.DrawString(
                    $reader.ReadToEnd(),
                    (New-Object System.Drawing.Font("Courier New", 10)),
                    [System.Drawing.Brushes]::Black,
                    0, 0
                )
                $ev.HasMorePages = $false
            })

            $printDocument.Print()
            $reader.Close()
            $stream.Close()
        `;

        const psFile = path.join(os.tmpdir(), 'print_' + Date.now() + '.ps1');
        fs.writeFileSync(psFile, psScript, 'utf8');

        const command = `powershell.exe -ExecutionPolicy Bypass -File "${psFile}"`;

        console.log('Executing PowerShell print script...');

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

            // Clean up temp files
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

    function generateReceiptText(receiptData) {
          const LINE_WIDTH = 32;

        // Helper function to center text
        function centerText(text) {
            const padding = Math.floor((LINE_WIDTH - text.length) / 2);
            return ' '.repeat(padding) + text + '\n';
        }

        // Plain text, no formatting
        let receipt = '';

        // Centered header
        receipt += centerText('SANJAYA TAILOR');
        receipt += centerText('Wellawaya Road,');
        receipt += centerText('Monaragala, Sri Lanka');
        receipt += centerText('Tel: +94 77 818 1630');
        receipt += '==========================================\n';
        receipt += '\n';

        receipt += 'Invoice No: ' + (receiptData.receipt_no || 'N/A') + '\n';
        receipt += 'Date: ' + (receiptData.date || new Date().toLocaleString()) + '\n';
        receipt += '------------------------------------------\n';
        receipt += '\n';

        receipt += 'Customer: ' + receiptData.customer_name + '\n';
        receipt += '\n';

        // Handle items array from your data structure
        if (Array.isArray(receiptData.items) && receiptData.items.length > 0) {
            receipt += 'ITEMS RENTED:\n';
            receipt += '------------------------------------------\n';

            receiptData.items.forEach((item, index) => {
                receipt += 'Item ' + (index + 1) + ':\n';

                // Display items that are not null
                if (item.coat) {
                    receipt += '  Coat: ' + item.coat + '\n';
                }
                if (item.trouser) {
                    receipt += '  Trouser: ' + item.trouser + '\n';
                }
                if (item.west) {
                    receipt += '  West: ' + item.west + '\n';
                }
                if (item.national) {
                    receipt += '  National: ' + item.national + '\n';
                }

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

        receipt += '------------------------------------------\n';
        receipt += 'Payment Received: Rs. ' + parseFloat(receiptData.payment_received).toFixed(2) + '\n';
        receipt += 'Remaining Payment: Rs. ' + parseFloat(receiptData.remaining_payment).toFixed(2) + '\n';
        receipt += 'Total Amount: Rs. ' + parseFloat(receiptData.rent_amount).toFixed(2) + '\n';
        receipt += '------------------------------------------\n';
        receipt += '\n';

        receipt += 'Thank you for your business!\n';
        receipt += 'Please return items on time\n';
        receipt += '==========================================\n';
        receipt += '\n';

        receipt += 'Powered by Anuruddha Bandara\n';
        receipt += 'Contact Number - +94 71 877 8844\n';
        receipt += '\n\n\n';

        return receipt;

        }

module.exports = {
  printReceipt
};
