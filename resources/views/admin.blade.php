<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Admin</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f3f4f6; margin: 0; padding: 32px; }
        .container { max-width: 900px; margin: 0 auto; background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 8px 20px rgba(0,0,0,0.08); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .menu { display: flex; gap: 12px; flex-wrap: wrap; }
        .btn { display: inline-block; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-weight: 600; }
        .btn-primary { background: #2563eb; color: white; }
        .btn-secondary { background: #e5e7eb; color: #111827; }
        .btn-danger { background: #dc2626; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>Selamat Datang di Halaman Admin</h1>
                <p>Panel utama untuk pengelolaan minimarket.</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
        </div>

        <div class="menu">
            <a class="btn btn-primary" href="{{ route('products.index') }}">Kelola Produk</a>
            <a class="btn btn-secondary" href="{{ route('kasir') }}">Halaman Kasir</a>
        </div>
    </div>
</body>
</html>
