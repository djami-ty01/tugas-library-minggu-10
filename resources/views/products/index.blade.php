<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Produk</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f7fb; margin: 0; padding: 32px; }
        .container { max-width: 1100px; margin: 0 auto; background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        h1 { margin: 0; font-size: 28px; }
        .actions a, .btn { display: inline-block; text-decoration: none; padding: 10px 16px; border-radius: 8px; font-weight: 600; }
        .btn-primary { background: #2563eb; color: #fff; }
        .btn-secondary { background: #e5e7eb; color: #111827; }
        .alert { background: #dcfce7; color: #166534; padding: 12px 14px; border-radius: 8px; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 18px; }
        th, td { border-bottom: 1px solid #e5e7eb; padding: 12px 10px; text-align: left; }
        th { background: #f8fafc; }
        .status { padding: 5px 10px; border-radius: 999px; font-size: 12px; font-weight: bold; }
        .status-active { background: #dcfce7; color: #166534; }
        .status-inactive { background: #fee2e2; color: #991b1b; }
        .small { font-size: 13px; }
        .row-actions { display: flex; gap: 8px; }
        form { display: inline; }
        .btn-danger { background: #dc2626; color: #fff; }
        .btn-info { background: #0ea5e9; color: #fff; }
        .btn-warning { background: #f59e0b; color: #fff; }
    </style>
</head>
<body>
    <div class="container">
        <div class="topbar">
            <div>
                <h1>Data Produk</h1>
                <p class="small">Kelola data barang minimarket</p>
            </div>
            <div class="actions">
                <a href="{{ route('admin') }}" class="btn btn-secondary">Kembali</a>
                <a href="{{ route('products.create') }}" class="btn btn-primary">Tambah Product</a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert">
                {{ session('success') }}
            </div>
        @endif

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category ?? '-' }}</td>
                        <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                        <td>{{ $product->stock }}</td>
                        <td>
                            @if ($product->is_active)
                                <span class="status status-active">Aktif</span>
                            @else
                                <span class="status status-inactive">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <div class="row-actions">
                                <a class="btn btn-info small" href="{{ route('products.show', $product) }}">Detail</a>
                                <a class="btn btn-warning small" href="{{ route('products.edit', $product) }}">Edit</a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger small">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">Belum ada data produk.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 16px;">
            {{ $products->links() }}
        </div>
    </div>
</body>
</html>
