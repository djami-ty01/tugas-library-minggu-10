<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produk</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f7fb; margin: 0; padding: 32px; }
        .container { max-width: 700px; margin: 0 auto; background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        h1 { margin-top: 0; }
        .box { background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; }
        .row { display: flex; justify-content: space-between; gap: 12px; padding: 10px 0; border-bottom: 1px solid #e5e7eb; }
        .row:last-child { border-bottom: none; }
        .label { font-weight: 700; }
        .actions { margin-top: 22px; display: flex; gap: 12px; }
        .btn { display: inline-block; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-weight: 600; }
        .btn-primary { background: #2563eb; color: #fff; }
        .btn-secondary { background: #e5e7eb; color: #111827; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Detail Produk</h1>

        <div class="box">
            <div class="row">
                <span class="label">Nama</span>
                <span>{{ $product->name }}</span>
            </div>
            <div class="row">
                <span class="label">Kategori</span>
                <span>{{ $product->category ?? '-' }}</span>
            </div>
            <div class="row">
                <span class="label">Harga</span>
                <span>Rp {{ number_format($product->price, 0, ',', '.') }}</span>
            </div>
            <div class="row">
                <span class="label">Stok</span>
                <span>{{ $product->stock }}</span>
            </div>
            <div class="row">
                <span class="label">Status</span>
                <span>{{ $product->is_active ? 'Aktif' : 'Nonaktif' }}</span>
            </div>
            <div class="row">
                <span class="label">Deskripsi</span>
                <span>{{ $product->description ?? '-' }}</span>
            </div>
        </div>

        <div class="actions">
            <a href="{{ route('products.index') }}" class="btn btn-secondary">Kembali</a>
            <a href="{{ route('products.edit', $product) }}" class="btn btn-primary">Edit</a>
        </div>
    </div>
</body>
</html>
