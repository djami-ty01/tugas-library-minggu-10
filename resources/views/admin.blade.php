<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f5f7fb">
    <title>Dashboard Admin | Polije Mart</title>
    @vite('resources/js/app.js')
    <style>
        :root {
            color-scheme: light;
            --ink: #172033;
            --muted: #788398;
            --line: #e9edf4;
            --surface: #fff;
            --canvas: #f5f7fb;
            --blue: #4263eb;
            --green: #159b72;
            --orange: #e18a32;
            --purple: #845ef7;
        }
        * { box-sizing: border-box; }
        body { margin: 0; background: var(--canvas); color: var(--ink); font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        a { color: inherit; text-decoration: none; }
        button { font: inherit; }
        .app-shell { display: grid; min-height: 100vh; grid-template-columns: 252px minmax(0, 1fr); }
        .sidebar { display: flex; flex-direction: column; padding: 27px 18px 20px; background: #fff; border-right: 1px solid var(--line); }
        .brand { display: flex; align-items: center; gap: 12px; padding: 0 10px 34px; }
        .brand-mark { display: grid; width: 42px; height: 42px; place-items: center; border-radius: 14px; background: linear-gradient(145deg, #5775f5, #3454d1); color: #fff; box-shadow: 0 7px 15px #4263eb36; }
        .brand-mark svg { width: 22px; height: 22px; }
        .brand-name { font-size: 15px; font-weight: 800; letter-spacing: -.3px; }
        .brand-caption { margin-top: 3px; color: var(--muted); font-size: 11px; }
        .nav-label { padding: 0 12px 10px; color: #a0a9b8; font-size: 10px; font-weight: 800; letter-spacing: 1.15px; text-transform: uppercase; }
        .nav-list { display: grid; gap: 6px; }
        .nav-link { display: flex; align-items: center; gap: 12px; min-height: 45px; padding: 0 12px; border-radius: 11px; color: #657187; font-size: 13px; font-weight: 650; transition: background .18s ease, color .18s ease, transform .18s ease; }
        .nav-link:hover { transform: translateX(2px); background: #f4f6ff; color: var(--blue); }
        .nav-link.active { background: #edf1ff; color: var(--blue); }
        .nav-link svg { width: 18px; height: 18px; flex: 0 0 auto; }
        .sidebar-spacer { flex: 1; min-height: 35px; }
        .sidebar-note { margin: 18px 2px; padding: 15px; border: 1px solid #e8ecff; border-radius: 14px; background: linear-gradient(145deg, #f8f9ff, #f0f3ff); }
        .note-dot { display: inline-block; width: 8px; height: 8px; margin-right: 7px; border-radius: 50%; background: #20b486; box-shadow: 0 0 0 4px #20b48620; }
        .sidebar-note strong { font-size: 12px; }
        .sidebar-note p { margin: 9px 0 0; color: var(--muted); font-size: 11px; line-height: 1.6; }
        .user-card { display: flex; align-items: center; gap: 11px; padding: 15px 7px 2px; border-top: 1px solid var(--line); }
        .avatar { display: grid; width: 37px; height: 37px; flex: 0 0 auto; place-items: center; border-radius: 13px; background: #fff0dd; color: #a35b15; font-size: 13px; font-weight: 800; }
        .user-copy { min-width: 0; flex: 1; }
        .user-name { overflow: hidden; font-size: 12px; font-weight: 750; text-overflow: ellipsis; white-space: nowrap; }
        .user-role { margin-top: 3px; color: var(--muted); font-size: 10px; }
        .logout-button { display: grid; width: 34px; height: 34px; place-items: center; border: 0; border-radius: 10px; background: transparent; color: #8c96a8; cursor: pointer; }
        .logout-button:hover { background: #fff0ef; color: #df5c56; }
        .logout-button svg { width: 17px; height: 17px; }
        .main-area { min-width: 0; padding: 0 36px 36px; }
        .topbar { display: flex; min-height: 76px; align-items: center; justify-content: space-between; gap: 16px; border-bottom: 1px solid var(--line); }
        .breadcrumb { color: var(--muted); font-size: 12px; }
        .breadcrumb strong { color: var(--ink); font-weight: 700; }
        .topbar-right { display: flex; align-items: center; gap: 12px; }
        .today-label { color: var(--muted); font-size: 12px; }
        .online-badge { display: inline-flex; align-items: center; gap: 8px; padding: 8px 11px; border: 1px solid #f5dfc4; border-radius: 999px; background: #fff9f0; color: #a35b15; font-size: 11px; font-weight: 700; }
        .online-badge.is-connected { border-color: #d9f1e7; background: #f2fbf6; color: #18835f; }
        .online-dot { width: 7px; height: 7px; border-radius: 50%; background: #e18a32; }
        .is-connected .online-dot { background: #20b486; }
        .content { width: 100%; max-width: 1330px; margin: 29px auto 0; }
        .welcome-banner { position: relative; display: flex; min-height: 192px; align-items: center; justify-content: space-between; gap: 25px; overflow: hidden; padding: 32px 38px; border-radius: 22px; background: linear-gradient(112deg, #314fce 0%, #4968e9 59%, #6f7df1 100%); color: #fff; box-shadow: 0 14px 28px #405ee52a; }
        .welcome-banner::before, .welcome-banner::after { position: absolute; border: 1px solid #ffffff20; border-radius: 50%; content: ""; pointer-events: none; }
        .welcome-banner::before { top: -102px; right: 118px; width: 275px; height: 275px; }
        .welcome-banner::after { right: -56px; bottom: -155px; width: 330px; height: 330px; }
        .welcome-copy, .banner-action { position: relative; z-index: 1; }
        .welcome-eyebrow { margin-bottom: 10px; color: #dce4ff; font-size: 11px; font-weight: 750; letter-spacing: 1.4px; text-transform: uppercase; }
        .welcome-banner h1 { margin: 0; font-size: clamp(24px, 3vw, 32px); font-weight: 760; letter-spacing: -.8px; }
        .welcome-banner p { margin: 10px 0 0; color: #e1e7ff; font-size: 13px; }
        .banner-action { display: inline-flex; min-height: 43px; align-items: center; justify-content: center; gap: 9px; padding: 0 16px; border: 1px solid #ffffff60; border-radius: 11px; background: #ffffff16; color: #fff; font-size: 12px; font-weight: 700; backdrop-filter: blur(8px); transition: background .18s ease, transform .18s ease; }
        .banner-action:hover { transform: translateY(-2px); background: #ffffff29; }
        .banner-action svg { width: 16px; height: 16px; }
        .section-heading { display: flex; align-items: end; justify-content: space-between; gap: 12px; margin: 31px 0 16px; }
        .section-heading h2 { margin: 0; font-size: 16px; font-weight: 780; letter-spacing: -.25px; }
        .section-heading p { margin: 5px 0 0; color: var(--muted); font-size: 11px; }
        .stats-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 15px; }
        .stat-card { min-width: 0; padding: 19px; border: 1px solid var(--line); border-radius: 16px; background: var(--surface); box-shadow: 0 3px 12px #24304a05; transition: transform .18s ease, box-shadow .18s ease; }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 24px #24304a0d; }
        .stat-top { display: flex; align-items: center; justify-content: space-between; gap: 10px; }
        .stat-label { color: var(--muted); font-size: 11px; font-weight: 650; }
        .stat-icon { display: grid; width: 36px; height: 36px; flex: 0 0 auto; place-items: center; border-radius: 12px; }
        .stat-icon svg { width: 18px; height: 18px; }
        .stat-icon.blue { background: #edf1ff; color: var(--blue); }
        .stat-icon.green { background: #e9f8f1; color: var(--green); }
        .stat-icon.orange { background: #fff4e8; color: var(--orange); }
        .stat-icon.purple { background: #f2edff; color: var(--purple); }
        .stat-value { overflow: hidden; margin-top: 13px; font-size: clamp(22px, 2.4vw, 28px); font-weight: 800; letter-spacing: -.8px; text-overflow: ellipsis; white-space: nowrap; }
        .stat-foot { margin-top: 7px; color: var(--muted); font-size: 10px; }
        .stat-foot strong { color: #526179; font-weight: 700; }
        .dashboard-grid { display: grid; grid-template-columns: minmax(0, 1.65fr) minmax(250px, .85fr); gap: 16px; margin-top: 19px; }
        .panel { min-width: 0; border: 1px solid var(--line); border-radius: 16px; background: var(--surface); box-shadow: 0 3px 12px #24304a05; }
        .panel-heading { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 19px 20px; border-bottom: 1px solid var(--line); }
        .panel-heading h2 { margin: 0; font-size: 13px; font-weight: 780; }
        .panel-heading p { margin: 5px 0 0; color: var(--muted); font-size: 10px; }
        .text-link { color: var(--blue); font-size: 11px; font-weight: 700; white-space: nowrap; }
        .text-link:hover { text-decoration: underline; }
        .table-wrap { overflow-x: auto; }
        .orders-table { width: 100%; border-collapse: collapse; text-align: left; }
        .orders-table th { padding: 12px 20px; background: #fbfcfe; color: #919bad; font-size: 9px; font-weight: 750; letter-spacing: .75px; text-transform: uppercase; white-space: nowrap; }
        .orders-table td { padding: 14px 20px; border-top: 1px solid #f0f2f6; font-size: 11px; white-space: nowrap; }
        .orders-table tbody tr:hover { background: #fcfdff; }
        .receipt-number { color: #36445f; font-weight: 720; }
        .order-cashier, .order-time { color: #7e899b; }
        .order-total { color: #26334c; font-weight: 750; }
        .status-paid { display: inline-flex; align-items: center; gap: 5px; padding: 5px 8px; border-radius: 999px; background: #eaf8f1; color: #18835f; font-size: 9px; font-weight: 750; }
        .status-paid::before { width: 5px; height: 5px; border-radius: 50%; background: #20b486; content: ""; }
        .empty-state { padding: 32px 20px; text-align: center; }
        .empty-icon { display: grid; width: 42px; height: 42px; margin: 0 auto 10px; place-items: center; border-radius: 14px; background: #f1f4fb; color: #8290a6; }
        .empty-icon svg { width: 20px; height: 20px; }
        .empty-state strong { display: block; font-size: 12px; }
        .empty-state p { margin: 6px 0 0; color: var(--muted); font-size: 10px; }
        .inventory-list { display: grid; gap: 0; padding: 0 19px; }
        .inventory-item { padding: 14px 0; border-bottom: 1px solid #f0f2f6; }
        .inventory-item:last-child { border-bottom: 0; }
        .inventory-top { display: flex; align-items: baseline; justify-content: space-between; gap: 10px; }
        .inventory-name { overflow: hidden; color: #39465f; font-size: 11px; font-weight: 700; text-overflow: ellipsis; white-space: nowrap; }
        .inventory-count { color: #d17532; font-size: 10px; font-weight: 750; white-space: nowrap; }
        .stock-track { height: 5px; margin-top: 10px; overflow: hidden; border-radius: 99px; background: #f0f2f6; }
        .stock-fill { height: 100%; min-width: 3px; border-radius: inherit; background: linear-gradient(90deg, #f2aa57, #e87955); }
        .inventory-footer { padding: 13px 19px 17px; border-top: 1px solid var(--line); }
        .quick-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 17px; }
        .quick-link { display: flex; align-items: center; gap: 12px; padding: 14px; border: 1px solid var(--line); border-radius: 14px; background: #fff; transition: border-color .18s ease, transform .18s ease; }
        .quick-link:hover { transform: translateY(-2px); border-color: #ccd5ff; }
        .quick-icon { display: grid; width: 36px; height: 36px; flex: 0 0 auto; place-items: center; border-radius: 11px; background: #edf1ff; color: var(--blue); }
        .quick-icon.green { background: #e9f8f1; color: var(--green); }
        .quick-icon svg { width: 18px; height: 18px; }
        .quick-title { font-size: 11px; font-weight: 750; }
        .quick-copy { margin-top: 4px; color: var(--muted); font-size: 9px; }
        @media (max-width: 1120px) {
            .app-shell { grid-template-columns: 220px minmax(0, 1fr); }
            .main-area { padding-right: 24px; padding-left: 24px; }
            .stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .dashboard-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 680px) {
            .app-shell { display: block; }
            .sidebar { padding: 12px 14px; border-right: 0; border-bottom: 1px solid var(--line); }
            .brand { padding: 0 4px 11px; }
            .nav-label, .sidebar-spacer, .sidebar-note, .user-card { display: none; }
            .nav-list { display: flex; gap: 6px; overflow-x: auto; padding-bottom: 1px; }
            .nav-link { min-height: 38px; flex: 0 0 auto; padding: 0 10px; font-size: 11px; }
            .nav-link svg { width: 15px; height: 15px; }
            .main-area { padding: 0 15px 25px; }
            .topbar { min-height: 58px; }
            .today-label { display: none; }
            .content { margin-top: 17px; }
            .welcome-banner { min-height: 180px; align-items: flex-start; flex-direction: column; justify-content: center; padding: 24px; }
            .welcome-banner h1 { font-size: 24px; }
            .welcome-banner p { max-width: 260px; line-height: 1.55; }
            .banner-action { min-height: 38px; }
            .section-heading { margin-top: 24px; }
            .stats-grid { gap: 10px; }
            .stat-card { padding: 14px; border-radius: 13px; }
            .stat-icon { width: 32px; height: 32px; }
            .stat-value { font-size: 22px; }
            .dashboard-grid { gap: 12px; margin-top: 13px; }
            .panel-heading { padding: 16px; }
            .orders-table th, .orders-table td { padding-right: 14px; padding-left: 14px; }
            .quick-actions { gap: 9px; }
            .quick-link { align-items: flex-start; flex-direction: column; gap: 8px; padding: 11px; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; }
        }
    </style>
</head>
<body>
    @include('components.realtime-notifications')
    <div class="app-shell">
        <aside class="sidebar" aria-label="Navigasi admin">
            <a class="brand" href="{{ route('admin') }}">
                <span class="brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 10h16l-1.4 10H5.4L4 10Z"/><path d="M8 10V7a4 4 0 0 1 8 0v3M9 14v2m6-2v2"/>
                    </svg>
                </span>
                <span>
                    <span class="brand-name">POLIJE MART</span>
                    <span class="brand-caption" style="display:block">Retail management</span>
                </span>
            </a>

            <div class="nav-label">Menu utama</div>
            <nav class="nav-list">
                <a class="nav-link active" href="{{ route('admin') }}" aria-current="page">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="3" width="8" height="8" rx="2"/><rect x="13" y="3" width="8" height="5" rx="2"/><rect x="13" y="10" width="8" height="11" rx="2"/><rect x="3" y="13" width="8" height="8" rx="2"/></svg>
                    Dashboard
                </a>
                <a class="nav-link" href="{{ route('products.index') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="m3 12 9 5 9-5M3 16l9 5 9-5"/></svg>
                    Kelola Produk
                </a>
                <a class="nav-link" href="{{ route('kasir') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="3"/><path d="M7 8h10M7 12h2m3 0h2m3 0h.01M7 16h.01m3 0h.01m3 0h.01m3 0h.01"/></svg>
                    Halaman Kasir
                </a>
            </nav>

            <div class="sidebar-spacer"></div>
            <div class="sidebar-note">
                <strong><span class="note-dot"></span>Ringkasan operasional</strong>
                <p>Pantau produk, transaksi, dan stok toko.</p>
            </div>
            <div class="user-card">
                <span class="avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                <span class="user-copy">
                    <span class="user-name">{{ auth()->user()->name }}</span>
                    <span class="user-role" style="display:block">Administrator</span>
                </span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="logout-button" type="submit" aria-label="Logout" title="Logout">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M10 17l5-5-5-5m5 5H3"/><path d="M12 3h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7"/></svg>
                    </button>
                </form>
            </div>
        </aside>

        <main class="main-area">
            <header class="topbar">
                <div class="breadcrumb">Workspace <span aria-hidden="true">/</span> <strong>Dashboard</strong></div>
                <div class="topbar-right">
                    <span class="today-label">{{ now()->translatedFormat('d M Y') }}</span>
                    <span class="online-badge is-disconnected" data-realtime-status role="status" aria-live="polite"><span class="online-dot"></span><span data-realtime-status-text>Memeriksa notifikasi</span></span>
                </div>
            </header>

            <div class="content">
                <section class="welcome-banner" aria-labelledby="dashboard-title">
                    <div class="welcome-copy">
                        <div class="welcome-eyebrow">Admin dashboard</div>
                        <h1 id="dashboard-title">Selamat Datang di Halaman Admin</h1>
                        <p>Pantau operasional toko dan kelola aktivitas minimarket dari satu tempat.</p>
                    </div>
                    <a class="banner-action" href="{{ route('products.create') }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 5v14m-7-7h14"/></svg>
                        Tambah produk
                    </a>
                </section>

                <div class="section-heading">
                    <div>
                        <h2>Ringkasan toko</h2>
                        <p>Performa operasional minimarket hari ini.</p>
                    </div>
                </div>

                <section class="stats-grid" aria-label="Ringkasan toko">
                    <article class="stat-card">
                        <div class="stat-top">
                            <span class="stat-label">Total produk</span>
                            <span class="stat-icon blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="m3 12 9 5 9-5M3 16l9 5 9-5"/></svg></span>
                        </div>
                        <div class="stat-value">{{ number_format($productCount, 0, ',', '.') }}</div>
                        <div class="stat-foot"><strong>{{ number_format($activeProductCount, 0, ',', '.') }}</strong> produk aktif</div>
                    </article>
                    <article class="stat-card">
                        <div class="stat-top">
                            <span class="stat-label">Penjualan hari ini</span>
                            <span class="stat-icon green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 3v18h18"/><path d="m7 14 4-4 3 3 6-7"/><path d="M15 6h5v5"/></svg></span>
                        </div>
                        <div class="stat-value">Rp {{ number_format($todaySales, 0, ',', '.') }}</div>
                        <div class="stat-foot">Dari transaksi yang sudah dibayar</div>
                    </article>
                    <article class="stat-card">
                        <div class="stat-top">
                            <span class="stat-label">Transaksi hari ini</span>
                            <span class="stat-icon purple"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8m-8 4h4"/></svg></span>
                        </div>
                        <div class="stat-value">{{ number_format($todayOrderCount, 0, ',', '.') }}</div>
                        <div class="stat-foot">Struk transaksi berhasil</div>
                    </article>
                    <article class="stat-card">
                        <div class="stat-top">
                            <span class="stat-label">Perlu restok</span>
                            <span class="stat-icon orange"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m12 3 10 18H2L12 3Z"/><path d="M12 9v5m0 3h.01"/></svg></span>
                        </div>
                        <div class="stat-value">{{ number_format($lowStockProducts->count(), 0, ',', '.') }}</div>
                        <div class="stat-foot">Produk aktif dengan stok ≤ 10</div>
                    </article>
                </section>

                <section class="dashboard-grid" aria-label="Aktivitas toko">
                    <article class="panel">
                        <div class="panel-heading">
                            <div>
                                <h2>Transaksi terbaru</h2>
                                <p>Lima transaksi terakhir yang masuk.</p>
                            </div>
                            <span class="text-link">Hari ini</span>
                        </div>
                        @if ($recentOrders->isNotEmpty())
                            <div class="table-wrap">
                                <table class="orders-table">
                                    <thead>
                                        <tr>
                                            <th>No. transaksi</th>
                                            <th>Kasir</th>
                                            <th>Waktu</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentOrders as $order)
                                            <tr>
                                                <td><a class="receipt-number" href="{{ route('orders.invoice', $order) }}">{{ $order->receipt_number }}</a></td>
                                                <td class="order-cashier">{{ $order->cashier?->name ?? 'Akun dihapus' }}</td>
                                                <td class="order-time">{{ $order->paid_at->format('d/m/Y H:i') }}</td>
                                                <td class="order-total">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                                <td><span class="status-paid">Lunas</span></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="empty-state">
                                <span class="empty-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8m-8 4h8m-8 4h4"/></svg></span>
                                <strong>Belum ada transaksi</strong>
                                <p>Transaksi yang berhasil akan muncul di sini.</p>
                            </div>
                        @endif
                    </article>

                    <article class="panel">
                        <div class="panel-heading">
                            <div>
                                <h2>Stok menipis</h2>
                                <p>Produk aktif dengan stok 10 atau kurang.</p>
                            </div>
                            <a class="text-link" href="{{ route('products.index') }}">Kelola produk</a>
                        </div>
                        @if ($lowStockProducts->isNotEmpty())
                            <div class="inventory-list">
                                @foreach ($lowStockProducts as $product)
                                    <div class="inventory-item">
                                        <div class="inventory-top">
                                            <span class="inventory-name">{{ $product->name }}</span>
                                            <span class="inventory-count">{{ $product->stock }} tersisa</span>
                                        </div>
                                        <div class="stock-track" role="progressbar" aria-label="Stok {{ $product->name }}" aria-valuemin="0" aria-valuemax="10" aria-valuenow="{{ min($product->stock, 10) }}">
                                            <div class="stock-fill" style="width: {{ min($product->stock * 10, 100) }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state">
                                <span class="empty-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m12 3 10 18H2L12 3Z"/><path d="M12 9v5m0 3h.01"/></svg></span>
                                <strong>Stok produk aman</strong>
                                <p>Tidak ada produk aktif yang perlu segera di-restok.</p>
                            </div>
                        @endif
                        <div class="inventory-footer">
                            <a class="text-link" href="{{ route('products.index') }}">Lihat semua produk <span aria-hidden="true">→</span></a>
                        </div>
                    </article>
                </section>

                <section class="quick-actions" aria-label="Aksi cepat">
                    <a class="quick-link" href="{{ route('products.create') }}">
                        <span class="quick-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 5v14m-7-7h14"/></svg></span>
                        <span><span class="quick-title" style="display:block">Tambah produk</span><span class="quick-copy" style="display:block">Masukkan barang baru ke katalog</span></span>
                    </a>
                    <a class="quick-link" href="{{ route('kasir') }}">
                        <span class="quick-icon green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="3"/><path d="M7 8h10M7 12h2m3 0h2m3 0h.01M7 16h.01m3 0h.01m3 0h.01m3 0h.01"/></svg></span>
                        <span><span class="quick-title" style="display:block">Buka halaman kasir</span><span class="quick-copy" style="display:block">Lihat katalog dan proses penjualan</span></span>
                    </a>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
