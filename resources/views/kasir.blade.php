<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir POS</title>
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
        @media (max-width: 900px) {
            .pos-container { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="topbar">
        <div>
            <h2>Kasir POS</h2>
            <div class="muted">Toko Retail Makmur</div>
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
            <p class="muted">Printer struk ESC/POS: {{ config('services.receipt.printer') }}</p>
            </form>
        </div>
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

        document.getElementById('checkoutForm').addEventListener('submit', event => {
            const subtotal = Object.values(cart).reduce((sum, item) => sum + item.qty * item.price, 0);
            const payment = Number(paymentInput.value || 0);

            if (subtotal === 0) {
                event.preventDefault();
                alert('Silakan pilih barang terlebih dahulu.');
                return;
            }

            if (payment < subtotal) {
                event.preventDefault();
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

            const bayarButton = document.getElementById('bayarBtn');
            bayarButton.disabled = true;
            bayarButton.textContent = 'Memproses & mencetak...';
        });

        document.getElementById('tahanBtn').addEventListener('click', () => {
            alert('Transaksi ditahan.');
        });
    </script>
</body>
</html>
