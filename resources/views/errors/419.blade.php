<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesi Kasir Berakhir</title>
    <style>
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 24px; background: #f1f5f9; color: #1f2937; font-family: Arial, sans-serif; text-align: center; }
        main { max-width: 480px; padding: 32px; border-radius: 12px; background: #fff; box-shadow: 0 10px 24px rgba(0,0,0,.08); }
        h1 { margin-top: 0; }
        a { display: inline-block; margin-top: 12px; padding: 10px 16px; border-radius: 8px; background: #2563eb; color: #fff; font-weight: 600; text-decoration: none; }
    </style>
</head>
<body>
    <main>
        <h1>Sesi kasir telah berakhir</h1>
        <p>Transaksi belum diproses. Kembali ke halaman kasir, masuk kembali jika diminta, lalu pilih ulang barang sebelum membayar.</p>
        <a href="{{ route('kasir') }}">Kembali ke Kasir</a>
    </main>
</body>
</html>
