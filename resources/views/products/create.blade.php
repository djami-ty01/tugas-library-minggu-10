<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f7fb; margin: 0; padding: 32px; }
        .container { max-width: 700px; margin: 0 auto; background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        h1 { margin-top: 0; }
        .field { margin-bottom: 16px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; }
        input, textarea, select { width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; }
        textarea { min-height: 110px; resize: vertical; }
        .checkbox { display: flex; align-items: center; gap: 10px; }
        .checkbox input { width: auto; }
        .actions { display: flex; gap: 12px; margin-top: 20px; }
        .btn { display: inline-block; padding: 10px 16px; border: none; border-radius: 8px; cursor: pointer; text-decoration: none; font-weight: 600; }
        .btn-primary { background: #2563eb; color: #fff; }
        .btn-secondary { background: #e5e7eb; color: #111827; }
        .error { color: #b91c1c; font-size: 12px; margin-top: 6px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Tambah Produk</h1>

        <form action="{{ route('products.store') }}" method="POST">
            @csrf

            <div class="field">
                <label for="name">Nama Produk</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required>
                @error('name') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="category">Kategori</label>
                <input id="category" name="category" type="text" value="{{ old('category') }}">
                @error('category') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="description">Deskripsi</label>
                <textarea id="description" name="description">{{ old('description') }}</textarea>
                @error('description') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="price">Harga</label>
                <input id="price" name="price" type="number" min="0" step="0.01" value="{{ old('price') }}" required>
                @error('price') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="stock">Stok</label>
                <input id="stock" name="stock" type="number" min="0" value="{{ old('stock', 0) }}" required>
                @error('stock') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="image">Gambar</label>
                <input id="image" name="image" type="text" value="{{ old('image') }}" placeholder="Nama file gambar (opsional)">
                @error('image') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field checkbox">
                <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                <label for="is_active">Produk aktif</label>
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</body>
</html>
