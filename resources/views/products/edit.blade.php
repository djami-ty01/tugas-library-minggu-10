<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk</title>
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
        <h1>Edit Produk</h1>

        <form action="{{ route('products.update', $product) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="field">
                <label for="name">Nama Produk</label>
                <input id="name" name="name" type="text" value="{{ old('name', $product->name) }}" required>
                @error('name') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="category">Kategori</label>
                <input id="category" name="category" type="text" value="{{ old('category', $product->category) }}">
                @error('category') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="description">Deskripsi</label>
                <textarea id="description" name="description">{{ old('description', $product->description) }}</textarea>
                @error('description') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="price">Harga</label>
                <input id="price" name="price" type="number" min="0" step="0.01" value="{{ old('price', $product->price) }}" required>
                @error('price') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="stock">Stok</label>
                <input id="stock" name="stock" type="number" min="0" value="{{ old('stock', $product->stock) }}" required>
                @error('stock') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="image">Gambar</label>
                <input id="image" name="image" type="text" value="{{ old('image', $product->image) }}" placeholder="Nama file gambar (opsional)">
                @error('image') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field checkbox">
                <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                <label for="is_active">Produk aktif</label>
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary">Perbarui</button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</body>
</html>
