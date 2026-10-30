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
