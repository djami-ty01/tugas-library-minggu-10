<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk {{ $receiptNumber }}</title>
    <style>
        body { margin: 0; padding: 24px; background: #f1f5f9; color: #111827; font-family: Arial, sans-serif; }
        .receipt { width: 320px; max-width: 100%; margin: 0 auto; padding: 20px; background: #fff; }
        .center { text-align: center; }
        .line { border-top: 1px dashed #111827; margin: 12px 0; }
        .meta, .item, .summary-row { display: flex; justify-content: space-between; gap: 12px; }
        .meta { font-size: 12px; margin: 5px 0; }
        .item { display: block; margin: 10px 0; }
        .item-total, .summary-row { font-size: 13px; }
        .summary-row { margin: 7px 0; }
        .total { font-size: 16px; font-weight: bold; }
        .actions { display: flex; justify-content: center; gap: 10px; margin: 18px auto; }
        .actions a, .actions button { border: 0; border-radius: 6px; padding: 10px 14px; color: #fff; background: #2563eb; text-decoration: none; font-weight: bold; cursor: pointer; }
        .actions a { background: #475569; }
        .actions .pdf-link { background: #15803d; }
        @media print {
            @page { size: 80mm auto; margin: 4mm; }
            body { padding: 0; background: #fff; }
            .receipt { width: auto; max-width: none; padding: 0; }
            .actions { display: none; }
            .print-status { display: none; }
        }
    </style>
</head>
<body>
    <main class="receipt">
        @include('partials.receipt-content')
    </main>
    @if ($printerError)
        <p class="print-status" role="alert" style="max-width:360px; margin:16px auto; color:#b91c1c; text-align:center;">
            {{ $printerError }}<br>
            Gunakan tombol Cetak Struk untuk membuka dialog cetak browser.
        </p>
    @else
        <p class="print-status" role="status" style="max-width:360px; margin:16px auto; color:#15803d; text-align:center;">
            Pembayaran berhasil. Dialog cetak browser akan dibuka.
        </p>
    @endif
    @if ($broadcastError)
        <p class="print-status" role="alert" style="max-width:360px; margin:16px auto; color:#b45309; text-align:center;">
            {{ $broadcastError }}
        </p>
    @endif
    <nav class="actions">
        <a href="{{ route('kasir') }}">Kembali ke Kasir</a>
        <a class="pdf-link" href="{{ route('orders.invoice', $order) }}" target="_blank" rel="noopener">Buka PDF</a>
        <button type="button" onclick="window.print()">Cetak Struk</button>
    </nav>
    <script>
        window.addEventListener('load', () => window.print());
    </script>
</body>
</html>
