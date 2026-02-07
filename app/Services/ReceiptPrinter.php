<?php
// app/Services/ReceiptPrinter.php
namespace App\Services;

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Illuminate\Support\Facades\Log;

class ReceiptPrinter
{
    public function printReceipt($order)
    {
        try {
            // For Windows - print directly to LPT or USB port
            // Find which port your printer uses in Printer Properties → Ports tab

            $connector = new FilePrintConnector("COM3");  // or try these:
            // $connector = new FilePrintConnector("COM1");
            // $connector = new FilePrintConnector("\\\\.\\USB001");

            $printer = new Printer($connector);
dd($printer);
            // Your printing code here...
            $printer->text("Test Receipt\n");
            $printer->feed(3);
            $printer->cut();
            $printer->close();

            return true;

        } catch (\Exception $e) {
            Log::error('Print error: ' . $e->getMessage());
            return false;
        }
    }
}
