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
        <header class="center">
            <strong>POLIJE MART</strong><br>
            <small>Terima kasih telah berbelanja</small>
        </header>
        <div class="line"></div>
        <div class="meta"><span>No. Transaksi</span><span>{{ $receiptNumber }}</span></div>
        <div class="meta"><span>Kasir</span><span>{{ $cashier }}</span></div>
        <div class="meta"><span>Tanggal</span><span>{{ $printedAt->format('d-m-Y H:i:s') }}</span></div>
        <div class="line"></div>

        @foreach ($items as $item)
            <div class="item">
                <div>{{ $item['name'] }}</div>
                <div class="item-total">
                    <span>{{ $item['qty'] }} x Rp {{ number_format($item['price'], 0, ',', '.') }}</span>
                    <strong>Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</strong>
                </div>
            </div>
        @endforeach

        <div class="line"></div>
        <div class="summary-row total">
            <span>TOTAL</span>
            <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
        </div>
        <div class="summary-row">
            <span>Uang Dibayar</span>
            <span>Rp {{ number_format($payment, 0, ',', '.') }}</span>
        </div>
        <div class="summary-row">
            <span>Kembalian</span>
            <span>Rp {{ number_format($change, 0, ',', '.') }}</span>
        </div>
        <div class="line"></div>
        <footer class="center"><small>Selamat berbelanja kembali</small></footer>
    </main>
    @if ($printerError)
        <p class="print-status" role="alert" style="max-width:360px; margin:16px auto; color:#b91c1c; text-align:center;">
            {{ $printerError }}<br>
            Membuka dialog cetak Windows sebagai alternatif. Pilih printer VSC MP-80M Pro jika tersedia.
        </p>
    @else
        <p class="print-status" role="status" style="max-width:360px; margin:16px auto; color:#15803d; text-align:center;">
            Struk berhasil dikirim ke printer.
        </p>
    @endif
    <nav class="actions">
        <a href="{{ route('kasir') }}">Kembali ke Kasir</a>
        <button type="button" onclick="window.print()">Cetak Struk</button>
    </nav>
    @if ($printerError)
        <script>
            window.addEventListener('load', () => window.print());
        </script>
    @endif
</body>
</html>
