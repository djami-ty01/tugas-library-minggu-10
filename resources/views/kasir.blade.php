<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir POS</title>
    @vite('resources/js/app.js')
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #eef2ff;
            color: #1f2937;
        }
        .topbar {
            background: #1f2937;
            color: #fff;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .topbar h2 { margin: 0; }
        .topbar form { margin: 0; }
        .realtime-status { display: inline-flex; align-items: center; gap: 6px; margin-top: 5px; color: #cbd5e1; font-size: 11px; }
        .realtime-status::before { width: 7px; height: 7px; border-radius: 50%; background: #f59e0b; content: ""; }
        .realtime-status.is-connected::before { background: #22c55e; }
        .btn { border: none; border-radius: 8px; padding: 10px 16px; cursor: pointer; font-weight: 600; }
        .btn-danger { background: #dc2626; color: white; }
        .btn-primary { background: #2563eb; color: white; }
        .btn-success { background: #16a34a; color: white; }
        .pos-container { display: grid; grid-template-columns: 1.5fr 0.9fr; gap: 20px; padding: 20px; }
        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.06);
            padding: 18px;
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 14px;
            margin-top: 16px;
        }
        .product-item {
            border: 1px solid #dbe2ea;
            border-radius: 10px;
            padding: 12px;
            cursor: pointer;
            transition: 0.2s;
            background: #f8fafc;
        }
        .product-item:hover { transform: translateY(-2px); border-color: #60a5fa; }
        .product-image { display: block; width: 100%; height: 100px; margin-bottom: 10px; border-radius: 8px; object-fit: cover; }
        .product-item h4 { margin: 0 0 8px; font-size: 15px; }
        .product-item p { margin: 0; color: #475569; }
        .cart-table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        .cart-table th, .cart-table td { border-bottom: 1px solid #e5e7eb; padding: 8px 6px; text-align: left; }
        .summary { margin-top: 18px; }
        .summary-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5e7eb; }
        .summary-row.total { font-size: 18px; font-weight: bold; }
        .payment-box { margin-top: 18px; }
        .payment-box input { width: 100%; padding: 12px; font-size: 18px; border: 1px solid #cbd5e1; border-radius: 8px; }
        .payment-actions { margin-top: 16px; display: flex; gap: 10px; flex-wrap: wrap; }
        .payment-actions .btn { flex: 1; }
        .muted { color: #64748b; font-size: 12px; }
        .receipt-modal[hidden] { display: none; }
        .receipt-modal { position: fixed; inset: 0; z-index: 1000; display: grid; place-items: center; padding: 20px; background: rgba(15, 23, 42, .65); }
        .receipt-dialog { width: min(100%, 480px); max-height: 90vh; overflow-y: auto; padding: 20px; border-radius: 12px; background: #f1f5f9; box-shadow: 0 20px 50px rgba(0,0,0,.25); }
        .receipt-dialog h2 { margin: 0 0 14px; }
        .receipt { width: 320px; max-width: 100%; margin: 0 auto; padding: 20px; background: #fff; color: #111827; font-family: Arial, sans-serif; }
        .center { text-align: center; }
        .line { border-top: 1px dashed #111827; margin: 12px 0; }
        .meta, .item-total, .receipt .summary-row { display: flex; justify-content: space-between; gap: 12px; }
        .meta { font-size: 12px; margin: 5px 0; }
        .item { display: block; margin: 10px 0; }
        .item-total, .receipt .summary-row { font-size: 13px; }
        .receipt .summary-row { margin: 7px 0; }
        .receipt .total { font-size: 16px; font-weight: bold; }
        .receipt-notice { margin: 14px 0 0; text-align: center; }
        .receipt-modal-actions { display: flex; justify-content: center; flex-wrap: wrap; gap: 10px; margin-top: 18px; }
        .receipt-modal-actions .btn { min-width: 120px; }
        .btn-secondary { background: #475569; color: #fff; }
        .btn-download { background: #15803d; color: #fff; text-decoration: none; text-align: center; }
        @media print {
            body.printing-receipt > :not(#receiptModal) { display: none !important; }
            body.printing-receipt #receiptModal { position: static; display: block; padding: 0; background: #fff; }
            body.printing-receipt .receipt-dialog { width: auto; max-height: none; overflow: visible; padding: 0; box-shadow: none; }
            body.printing-receipt .receipt-dialog h2, body.printing-receipt .receipt-notice, body.printing-receipt .receipt-modal-actions,
            body.printing-receipt label[for="receiptPrinter"], body.printing-receipt #receiptPrinter { display: none !important; }
            body.printing-receipt .receipt { width: 72mm; max-width: none; padding: 0; }
            @page { size: 80mm auto; margin: 4mm; }
        }
        @media (max-width: 900px) {
            .pos-container { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    @include('components.realtime-notifications')
    <div class="topbar">
        <div>
            <h2>Kasir POS</h2>
            <div class="muted">Toko Retail Makmur</div>
            <div class="realtime-status is-disconnected" data-realtime-status role="status" aria-live="polite"><span data-realtime-status-text>Memeriksa notifikasi</span></div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-danger">Logout</button>
        </form>
    </div>

    <div style="padding: 24px 20px 0;">
        <h1>Selamat Datang, Kasir</h1>
        <p>Anda berhasil login sebagai kasir.</p>
    </div>

    <div class="pos-container">
        <div>
            <div class="card">
                <h3>Tambah Barang</h3>
                <div class="product-grid" id="productGrid">
                    @foreach ($products as $product)
                        <div class="product-item" data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->price }}" data-stock="{{ $product->stock }}" @if ($product->stock < 1) aria-disabled="true" style="opacity:.55; cursor:not-allowed;" @endif>
                            @if ($product->image)
                                <img class="product-image" src="{{ asset('storage/'.$product->image) }}" alt="Gambar {{ $product->name }}">
                            @endif
                            <h4>{{ $product->name }}</h4>
                            <p>Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                            <p class="muted">Stok: {{ $product->stock }}{{ $product->stock < 1 ? ' (habis)' : '' }}</p>
                        </div>
                    @endforeach
                </div>
                @if ($products->isEmpty())
                    <p class="muted">Belum ada produk aktif dengan stok tersedia. Tambahkan produk melalui halaman admin.</p>
                @endif
            </div>
        </div>

        <div class="card">
            <h3>Keranjang Belanja</h3>
            @if ($errors->any())
                <div role="alert" style="color:#b91c1c; margin-bottom:12px;">
                    {{ $errors->first() }}
                </div>
            @endif
            <form id="checkoutForm" method="POST" action="{{ route('struk.cetak') }}">
                @csrf
                <div id="checkoutItems"></div>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody id="cartBody">
                    <tr>
                        <td colspan="3" class="muted" style="text-align:center; padding:16px;">Belum ada barang</td>
                    </tr>
                </tbody>
            </table>

            <div class="summary">
                <div class="summary-row">
                    <span>Total Item</span>
                    <strong id="totalItems">0</strong>
                </div>
                <div class="summary-row">
                    <span>Subtotal</span>
                    <strong id="subtotal">Rp 0</strong>
                </div>
                <div class="summary-row total">
                    <span>Total Akhir</span>
                    <strong id="totalAkhir">Rp 0</strong>
                </div>
            </div>

            <div class="payment-box">
                <label for="paymentInput" class="muted">Uang Dibayar</label>
                <input id="paymentInput" name="payment" type="number" min="0" step="0.01" placeholder="Rp 0" value="{{ old('payment', 0) }}" required>
            </div>

            <div class="summary">
                <div class="summary-row">
                    <span>Kembalian</span>
                    <strong id="kembalian">Rp 0</strong>
                </div>
            </div>

            <div class="payment-actions">
                <button class="btn btn-primary" type="submit" id="bayarBtn">Bayar & Cetak</button>
                <button class="btn btn-success" type="button" id="tahanBtn">Tahan</button>
            </div>
            </form>
        </div>
    </div>

    <div id="receiptModal" class="receipt-modal" role="dialog" aria-modal="true" aria-labelledby="receiptModalTitle" hidden>
        <section class="receipt-dialog">
            <h2 id="receiptModalTitle">Pembayaran berhasil</h2>
            <div id="receiptContent" class="receipt"></div>
            <p id="receiptNotice" class="receipt-notice" role="status"></p>
            <label for="receiptPrinter">Pilih printer struk</label>
            <select id="receiptPrinter" class="payment-box" style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:8px;">
                @foreach (config('services.receipt.printers', []) as $printer)
                    <option value="{{ $printer }}">{{ $printer }}</option>
                @endforeach
            </select>
            <p id="printerActionNotice" class="receipt-notice" role="status" aria-live="polite"></p>
            <div class="receipt-modal-actions">
                <button type="button" class="btn btn-primary" id="printReceiptBtn">Pilih Printer Browser</button>
                <button type="button" class="btn btn-secondary" id="printEscposBtn">Cetak ke Printer Struk</button>
                <a class="btn btn-download" id="downloadInvoiceBtn" href="#" download>Unduh Invoice PDF</a>
                <button type="button" class="btn btn-secondary" id="closeReceiptBtn">Tutup</button>
            </div>
        </section>
    </div>

    <script>
        const cart = {};

        const cartBody = document.getElementById('cartBody');
        const totalItems = document.getElementById('totalItems');
        const subtotalEl = document.getElementById('subtotal');
        const totalAkhirEl = document.getElementById('totalAkhir');
        const paymentInput = document.getElementById('paymentInput');
        const kembalianEl = document.getElementById('kembalian');

        document.querySelectorAll('.product-item').forEach(item => {
            item.addEventListener('click', () => {
                const id = Number(item.dataset.id);
                const name = item.dataset.name;
                const price = Number(item.dataset.price);
                const stock = Number(item.dataset.stock);

                if (stock < 1) {
                    alert('Stok ' + name + ' sedang habis.');
                    return;
                }

                if (!cart[id]) {
                    cart[id] = { id, name, price, qty: 0 };
                }

                if (cart[id].qty >= stock) {
                    alert('Stok ' + name + ' tidak mencukupi.');
                    return;
                }

                cart[id].qty += 1;
                renderCart();
            });
        });

        function formatCurrency(value) {
            return 'Rp ' + Number(value).toLocaleString('id-ID');
        }

        function escapeHtml(value) {
            return String(value).replace(/[&<>"']/g, character => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            })[character]);
        }

        function renderCart() {
            const entries = Object.values(cart);

            if (entries.length === 0) {
                cartBody.innerHTML = '<tr><td colspan="3" class="muted" style="text-align:center; padding:16px;">Belum ada barang</td></tr>';
                totalItems.textContent = '0';
                subtotalEl.textContent = 'Rp 0';
                totalAkhirEl.textContent = 'Rp 0';
                kembalianEl.textContent = 'Rp 0';
                return;
            }

            let subtotal = 0;
            let totalItemsCount = 0;

            cartBody.innerHTML = entries.map((item, index) => {
                const itemSubtotal = item.qty * item.price;
                subtotal += itemSubtotal;
                totalItemsCount += item.qty;

                return `
                    <tr>
                        <td>${index + 1}. ${escapeHtml(item.name)}</td>
                        <td>
                            <button type="button" data-action="minus" data-id="${item.id}" style="margin-right:4px;">-</button>
                            ${item.qty}
                            <button type="button" data-action="plus" data-id="${item.id}" style="margin-left:4px;">+</button>
                        </td>
                        <td>${formatCurrency(itemSubtotal)}</td>
                    </tr>
                `;
            }).join('');

            totalItems.textContent = totalItemsCount;
            subtotalEl.textContent = formatCurrency(subtotal);
            totalAkhirEl.textContent = formatCurrency(subtotal);

            const payment = Number(paymentInput.value || 0);
            const change = payment - subtotal;
            kembalianEl.textContent = formatCurrency(Math.max(change, 0));

            document.querySelectorAll('[data-action]').forEach(button => {
                button.addEventListener('click', () => {
                    const id = Number(button.dataset.id);
                    const action = button.dataset.action;

                    if (!cart[id]) return;

                    if (action === 'plus') {
                        const product = document.querySelector(`.product-item[data-id="${id}"]`);
                        if (cart[id].qty >= Number(product.dataset.stock)) {
                            alert('Stok ' + cart[id].name + ' tidak mencukupi.');
                            return;
                        }
                        cart[id].qty += 1;
                    }
                    if (action === 'minus') {
                        cart[id].qty -= 1;
                        if (cart[id].qty <= 0) delete cart[id];
                    }

                    renderCart();
                });
            });
        }

        paymentInput.addEventListener('input', renderCart);

        const checkoutForm = document.getElementById('checkoutForm');
        const bayarButton = document.getElementById('bayarBtn');
        const receiptModal = document.getElementById('receiptModal');
        const receiptContent = document.getElementById('receiptContent');
        const receiptNotice = document.getElementById('receiptNotice');
        const downloadInvoiceButton = document.getElementById('downloadInvoiceBtn');
        const printerActionNotice = document.getElementById('printerActionNotice');
        let currentPrintUrl = null;

        checkoutForm.addEventListener('submit', async event => {
            event.preventDefault();
            const subtotal = Object.values(cart).reduce((sum, item) => sum + item.qty * item.price, 0);
            const payment = Number(paymentInput.value || 0);

            if (subtotal === 0) {
                alert('Silakan pilih barang terlebih dahulu.');
                return;
            }

            if (payment < subtotal) {
                alert('Uang yang dibayar belum cukup.');
                return;
            }

            const checkoutItems = document.getElementById('checkoutItems');
            checkoutItems.replaceChildren();
            Object.values(cart).forEach((item, index) => {
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = `items[${index}][id]`;
                idInput.value = item.id;

                const quantityInput = document.createElement('input');
                quantityInput.type = 'hidden';
                quantityInput.name = `items[${index}][qty]`;
                quantityInput.value = item.qty;

                checkoutItems.append(idInput, quantityInput);
            });

            bayarButton.disabled = true;
            bayarButton.textContent = 'Memproses pembayaran...';

            try {
                const response = await fetch(checkoutForm.action, {
                    method: 'POST',
                    body: new FormData(checkoutForm),
                    headers: { Accept: 'application/json' },
                    credentials: 'same-origin'
                });
                const result = await response.json();

                if (!response.ok) {
                    const message = Object.values(result.errors || {}).flat()[0] || 'Pembayaran gagal diproses.';
                    throw new Error(message);
                }

                receiptContent.innerHTML = result.receiptHtml;
                downloadInvoiceButton.href = result.invoiceDownloadUrl;
                currentPrintUrl = result.printUrl;
                receiptNotice.textContent = `Transaksi berhasil! No. transaksi: ${result.receiptNumber}.`;
                receiptNotice.style.color = '#15803d';
                printerActionNotice.textContent = '';
                receiptModal.hidden = false;
                document.body.style.overflow = 'hidden';

                Object.values(cart).forEach(item => {
                    const product = document.querySelector(`.product-item[data-id="${item.id}"]`);
                    if (product) product.dataset.stock = Math.max(0, Number(product.dataset.stock) - item.qty);
                });
                for (const id of Object.keys(cart)) delete cart[id];
                renderCart();
                paymentInput.value = '0';
            } catch (error) {
                alert(error.message || 'Pembayaran gagal diproses. Silakan coba kembali.');
            } finally {
                bayarButton.disabled = false;
                bayarButton.textContent = 'Bayar & Cetak';
            }
        });

        document.getElementById('printReceiptBtn').addEventListener('click', () => {
            document.body.classList.add('printing-receipt');
            window.print();
        });
        document.getElementById('printEscposBtn').addEventListener('click', async event => {
            const button = event.currentTarget;
            button.disabled = true;
            printerActionNotice.textContent = 'Mengirim struk ke printer...';
            printerActionNotice.style.color = '#475569';

            try {
                const response = await fetch(currentPrintUrl, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': checkoutForm.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({ printer: document.getElementById('receiptPrinter').value }),
                    credentials: 'same-origin'
                });
                const result = await response.json();
                if (!response.ok) throw new Error(result.message || 'Cetak struk gagal.');

                printerActionNotice.textContent = result.message;
                printerActionNotice.style.color = '#15803d';
            } catch (error) {
                printerActionNotice.textContent = error.message || 'Cetak struk gagal.';
                printerActionNotice.style.color = '#b91c1c';
            } finally {
                button.disabled = false;
            }
        });
        document.getElementById('closeReceiptBtn').addEventListener('click', () => {
            receiptModal.hidden = true;
            document.body.style.overflow = '';
            window.location.reload();
        });
        window.addEventListener('afterprint', () => {
            document.body.classList.remove('printing-receipt');
        });

        document.getElementById('tahanBtn').addEventListener('click', () => {
            alert('Transaksi ditahan.');
        });
    </script>
</body>
</html>
