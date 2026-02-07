<!-- resources/views/receipts/print.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <style>
        @media print {
            body { width: 58mm; } /* XPrinter paper width */
        }
        body {
            font-family: monospace;
            font-size: 12px;
            width: 58mm;
        }
        .center { text-align: center; }
        .receipt { padding: 5px; }
    </style>
</head>
<body onload="window.print(); window.close();">
    <div class="receipt">
        <div class="center">
            <strong>YOUR STORE NAME</strong><br>
            Address Line<br>
            Tel: 123-456-7890
        </div>
        <hr>
        Order #: {{ $order->id }}<br>
        Date: {{ $order->created_at->format('Y-m-d H:i') }}
        <hr>
        @foreach($order->items as $item)
            {{ $item->name }} - ${{ number_format($item->price, 2) }}<br>
        @endforeach
        <hr>
        <strong>TOTAL: ${{ number_format($order->total, 2) }}</strong>
        <div class="center">
            <br>Thank you!<br><br>
        </div>
    </div>
</body>
</html>
