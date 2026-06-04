<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SiTahu Admin')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --brand: #dfba68;
            --brand-dark: #8a6321;
            --brand-soft: rgba(223, 186, 104, 0.16);

            --bg: #f5f6f8;
            --panel: #ffffff;
            --text: #111827;
            --muted: #6b7280;
            --muted-2: #9ca3af;
            --border: #e5e7eb;

            --green: #16a34a;
            --green-soft: #dcfce7;
            --blue: #2563eb;
            --blue-soft: #dbeafe;
            --yellow: #ca8a04;
            --yellow-soft: #fef3c7;
            --red: #dc2626;
            --red-soft: #fee2e2;
            --gray-soft: #f3f4f6;

            --sidebar-width: 270px;
            --sidebar-collapsed-width: 82px;
            --topbar-height: 66px;

            --radius: 18px;
            --shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
            --shadow-sm: 0 4px 14px rgba(15, 23, 42, 0.05);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(223, 186, 104, 0.13), transparent 34%),
                var(--bg);
            color: var(--text);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        a {
            color: inherit;
        }

        .app-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: var(--sidebar-width) minmax(0, 1fr);
            transition: grid-template-columns 0.22s ease;
        }

        body.sidebar-collapsed .app-shell {
            grid-template-columns: var(--sidebar-collapsed-width) minmax(0, 1fr);
        }

        .app-sidebar {
            position: sticky;
            top: 0;
            height: 100vh;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(16px);
            border-right: 1px solid var(--border);
            z-index: 1040;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .app-sidebar::-webkit-scrollbar {
            width: 5px;
        }

        .app-sidebar::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 999px;
        }

        .brand-block {
            min-height: var(--topbar-height);
            padding: 0 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--border);
        }

        .brand-logo-btn {
            width: 42px;
            height: 42px;
            border: 0;
            border-radius: 15px;
            background: linear-gradient(135deg, var(--brand), #f4d894);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 22px rgba(223, 186, 104, 0.32);
            transition: 0.18s ease;
            flex-shrink: 0;
        }

        .brand-logo-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 26px rgba(223, 186, 104, 0.38);
        }

        .brand-title {
            font-size: 1.16rem;
            font-weight: 850;
            letter-spacing: -0.04em;
            line-height: 1;
        }

        .brand-subtitle {
            margin-top: 4px;
            color: var(--muted);
            font-size: 0.73rem;
            font-weight: 600;
        }

        .sidebar-nav {
            padding: 16px 14px;
            display: flex;
            flex-direction: column;
            gap: 5px;
            flex: 1;
        }

        .sidebar-section-title {
            margin: 14px 10px 6px;
            color: var(--muted-2);
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 800;
        }

        .sidebar-link,
        .sidebar-parent {
            width: 100%;
            min-height: 44px;
            padding: 10px 12px;
            border: 0;
            border-radius: 14px;
            background: transparent;
            color: #4b5563;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 700;
            line-height: 1.1;
            transition: 0.16s ease;
            position: relative;
        }

        .sidebar-link:hover,
        .sidebar-parent:hover {
            background: #f7f7f7;
            color: var(--text);
        }

        .sidebar-link.active,
        .sidebar-parent.active {
            background: var(--brand-soft);
            color: var(--brand-dark);
        }

        .sidebar-link.active::before,
        .sidebar-parent.active::before {
            content: "";
            position: absolute;
            left: -14px;
            top: 10px;
            bottom: 10px;
            width: 4px;
            border-radius: 999px;
            background: var(--brand);
        }

        .sidebar-icon {
            width: 24px;
            height: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.08rem;
            flex-shrink: 0;
        }

        .sidebar-label {
            flex: 1;
            text-align: left;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-chevron {
            color: var(--muted-2);
            font-size: 0.76rem;
            transition: transform 0.16s ease;
        }

        .sidebar-parent[aria-expanded="true"] .sidebar-chevron {
            transform: rotate(180deg);
        }

        .sidebar-submenu {
            position: relative;
            margin: 3px 0 8px 24px;
            padding-left: 18px;
        }

        .sidebar-submenu::before {
            content: "";
            position: absolute;
            left: 5px;
            top: 4px;
            bottom: 6px;
            width: 1px;
            background: var(--border);
        }

        .sidebar-submenu a {
            display: block;
            padding: 8px 10px;
            border-radius: 11px;
            color: var(--muted);
            text-decoration: none;
            font-size: 0.84rem;
            font-weight: 700;
            transition: 0.16s ease;
        }

        .sidebar-submenu a:hover {
            background: #f7f7f7;
            color: var(--text);
        }

        .sidebar-submenu a.active {
            background: rgba(223, 186, 104, 0.12);
            color: var(--brand-dark);
        }

        .sidebar-footer {
            padding: 14px;
            border-top: 1px solid var(--border);
        }

        .mini-profile-card {
            padding: 12px;
            border-radius: 16px;
            background: #fafafa;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .mini-avatar,
        .user-avatar {
            border-radius: 999px;
            background: linear-gradient(135deg, var(--brand), #f0d58d);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 850;
            flex-shrink: 0;
        }

        .mini-avatar {
            width: 36px;
            height: 36px;
            font-size: 0.78rem;
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            font-size: 0.82rem;
            box-shadow: 0 8px 18px rgba(223, 186, 104, 0.26);
        }

        .app-main {
            min-width: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            height: var(--topbar-height);
            position: sticky;
            top: 0;
            z-index: 1030;
            background: rgba(255, 255, 255, 0.86);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .topbar-title {
            font-size: 0.92rem;
            font-weight: 850;
            letter-spacing: -0.02em;
            color: var(--text);
            margin: 0;
        }

        .topbar-subtitle {
            font-size: 0.76rem;
            font-weight: 600;
            color: var(--muted);
            margin: 2px 0 0;
        }

        .icon-btn {
            width: 38px;
            height: 38px;
            border-radius: 14px;
            border: 1px solid var(--border);
            background: #fff;
            color: #4b5563;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: 0.16s ease;
        }

        .icon-btn:hover {
            background: #f9fafb;
            color: var(--text);
            transform: translateY(-1px);
        }

        .topbar-date {
            padding: 9px 12px;
            border: 1px solid var(--border);
            background: #fff;
            border-radius: 999px;
            font-size: 0.78rem;
            color: var(--muted);
            font-weight: 750;
        }

        .content-wrapper {
            flex: 1;
            padding: 24px;
        }

        .page-card,
        .card,
        .sc-box {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
        }

        .card {
            overflow: hidden;
        }

        .card-pad {
            padding: 18px;
        }

        .hero {
            background: linear-gradient(135deg, #ffffff, #fff8e9);
            border: 1px solid var(--border);
            border-radius: 22px;
            padding: 22px;
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }

        .hero h1 {
            margin: 0 0 4px;
            font-size: 1.42rem;
            font-weight: 900;
            letter-spacing: -0.04em;
        }

        .hero p {
            margin: 0;
            color: var(--muted);
            font-size: 0.9rem;
            font-weight: 600;
        }

        .grid {
            display: grid;
            gap: 16px;
        }

        .g2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .g3 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .g4 {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .stat {
            padding: 16px 18px;
            min-height: 128px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .stat-label {
            color: var(--muted);
            font-size: 0.78rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .stat-value {
            margin-top: 7px;
            color: var(--text);
            font-size: 1.58rem;
            line-height: 1;
            font-weight: 950;
            letter-spacing: -0.05em;
        }

        .stat-note {
            display: inline-block;
            margin-top: 8px;
            font-size: 0.74rem;
            font-weight: 800;
        }

        .stat-icon {
            width: 46px;
            height: 46px;
            border-radius: 16px;
            background: var(--brand-soft);
            color: var(--brand-dark);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.86rem;
            font-weight: 950;
            flex-shrink: 0;
        }

        .toolbar {
            padding: 16px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .toolbar-left {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .field,
        .form-control,
        .form-select {
            border-radius: 13px;
            border-color: var(--border);
            min-height: 40px;
            font-size: 0.88rem;
        }

        .field {
            border: 1px solid var(--border);
            background: #fff;
            padding: 9px 12px;
            outline: none;
            color: var(--text);
            font-weight: 650;
        }

        .field:focus,
        .form-control:focus,
        .form-select:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 4px rgba(223, 186, 104, 0.16);
        }

        .btn {
            border-radius: 13px;
            font-weight: 800;
        }

        .btn-primary,
        .btn-brand {
            background: var(--brand);
            border-color: var(--brand);
            color: #fff;
        }

        .btn-primary:hover,
        .btn-brand:hover {
            background: #d5ac55;
            border-color: #d5ac55;
            color: #fff;
        }

        .btn-secondary {
            background: #111827;
            border-color: #111827;
            color: #fff;
        }

        .small-btn,
        .btn-action {
            min-height: 34px;
            padding: 7px 10px;
            border-radius: 11px;
            border: 1px solid var(--border);
            background: #fff;
            color: #374151;
            font-size: 0.78rem;
            font-weight: 850;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: 0.16s ease;
        }

        .small-btn:hover,
        .btn-action:hover {
            background: #f9fafb;
            color: var(--text);
            transform: translateY(-1px);
        }

        .actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .inline-form {
            display: inline;
        }

        .table-wrap {
            width: 100%;
            overflow-x: auto;
        }

        .table-wrap table,
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-wrap th,
        .table-wrap td,
        table th,
        table td {
            padding: 14px 16px;
            border-bottom: 1px solid #f0f1f3;
            vertical-align: middle;
        }

        .table-wrap th,
        table th {
            background: #fafafa;
            color: var(--muted);
            font-size: 0.72rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            white-space: nowrap;
        }

        .table-wrap td,
        table td {
            color: var(--text);
            font-size: 0.88rem;
            font-weight: 650;
        }

        .table-wrap tbody tr:hover,
        table tbody tr:hover {
            background: #fcfcfc;
        }

        .sub {
            display: block;
            margin-top: 4px;
            color: var(--muted);
            font-size: 0.75rem;
            font-weight: 650;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 900;
            white-space: nowrap;
        }

        .chip::before {
            content: "";
            width: 6px;
            height: 6px;
            border-radius: 999px;
            background: currentColor;
        }

        .c-green {
            color: #15803d;
            background: var(--green-soft);
        }

        .c-blue {
            color: #1d4ed8;
            background: var(--blue-soft);
        }

        .c-yellow {
            color: #92400e;
            background: var(--yellow-soft);
        }

        .c-red {
            color: #b91c1c;
            background: var(--red-soft);
        }

        .c-gray {
            color: #4b5563;
            background: var(--gray-soft);
        }

        .cover {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            object-fit: cover;
            border: 1px solid var(--border);
            background: #f3f4f6;
        }

        .pagination {
            margin: 0;
        }

        .pagination .page-link {
            border-radius: 10px;
            margin: 0 2px;
            color: var(--brand-dark);
            border-color: var(--border);
            font-weight: 800;
        }

        .pagination .active .page-link {
            background: var(--brand);
            border-color: var(--brand);
            color: #fff;
        }

        .dropdown-menu {
            border-radius: 17px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
        }

        .dropdown-item {
            border-radius: 10px;
            font-weight: 700;
        }

        body.sidebar-collapsed .brand-info,
        body.sidebar-collapsed .sidebar-label,
        body.sidebar-collapsed .sidebar-chevron,
        body.sidebar-collapsed .sidebar-submenu,
        body.sidebar-collapsed .sidebar-section-title,
        body.sidebar-collapsed .sidebar-footer {
            display: none !important;
        }

        body.sidebar-collapsed .brand-block {
            justify-content: center;
            padding: 0;
        }

        body.sidebar-collapsed .sidebar-nav {
            padding-left: 10px;
            padding-right: 10px;
        }

        body.sidebar-collapsed .sidebar-link,
        body.sidebar-collapsed .sidebar-parent {
            width: 46px;
            height: 46px;
            min-height: 46px;
            padding: 0;
            justify-content: center;
            margin-left: auto;
            margin-right: auto;
        }

        body.sidebar-collapsed .sidebar-link.active::before,
        body.sidebar-collapsed .sidebar-parent.active::before {
            display: none;
        }

        @media (max-width: 1200px) {
            .g4 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .g3 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 900px) {
            .app-shell,
            body.sidebar-collapsed .app-shell {
                grid-template-columns: 1fr;
            }

            .app-sidebar {
                position: fixed;
                inset: 0 auto 0 0;
                width: var(--sidebar-width);
                transform: translateX(-105%);
                transition: transform 0.2s ease;
                box-shadow: 18px 0 40px rgba(15, 23, 42, 0.13);
            }

            .app-sidebar.open {
                transform: translateX(0);
            }

            .topbar {
                padding: 0 14px;
            }

            .content-wrapper {
                padding: 16px;
            }

            body.sidebar-collapsed .brand-info,
            body.sidebar-collapsed .sidebar-label,
            body.sidebar-collapsed .sidebar-chevron,
            body.sidebar-collapsed .sidebar-section-title,
            body.sidebar-collapsed .sidebar-footer {
                display: block !important;
            }

            body.sidebar-collapsed .brand-block {
                justify-content: flex-start;
                padding: 0 18px;
            }

            body.sidebar-collapsed .sidebar-link,
            body.sidebar-collapsed .sidebar-parent {
                width: 100%;
                height: auto;
                min-height: 44px;
                padding: 10px 12px;
                justify-content: flex-start;
            }

            body.sidebar-collapsed .sidebar-link.active::before,
            body.sidebar-collapsed .sidebar-parent.active::before {
                display: block;
            }

            .hero {
                align-items: flex-start;
                flex-direction: column;
            }
        }

        @media (max-width: 640px) {
            .g4,
            .g3,
            .g2 {
                grid-template-columns: 1fr;
            }

            .toolbar,
            .toolbar-left {
                align-items: stretch;
                flex-direction: column;
            }

            .field,
            .toolbar .btn {
                width: 100%;
            }

            .topbar-title-wrap {
                display: none;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
<div class="offcanvas-backdrop fade d-none" id="sidebarOverlay"></div>

<div class="app-shell">
    <aside class="app-sidebar" id="sidebar">
        <div class="brand-block">
            <button class="brand-logo-btn" id="brandToggle" type="button" title="Kecilkan sidebar">
                <i class="bi bi-box-seam-fill fs-5"></i>
            </button>

            <div class="brand-info">
                <div class="brand-title">SiTahu</div>
                <div class="brand-subtitle">Web Admin Toko Tahu</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="sidebar-section-title">Utama</div>

            <a href="{{ route('admin.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="sidebar-icon"><i class="bi bi-grid-1x2-fill"></i></span>
                <span class="sidebar-label">Dashboard</span>
            </a>

            <div class="sidebar-section-title">Manajemen Toko</div>

            <div>
                <button type="button"
                        class="sidebar-parent {{ request()->routeIs('admin.produk.*') || request()->routeIs('admin.stok.*') ? 'active' : '' }}"
                        data-bs-toggle="collapse"
                        data-bs-target="#menuProduk"
                        aria-expanded="{{ request()->routeIs('admin.produk.*') || request()->routeIs('admin.stok.*') ? 'true' : 'false' }}">
                    <span class="sidebar-icon"><i class="bi bi-basket2-fill"></i></span>
                    <span class="sidebar-label">Produk & Stok</span>
                    <i class="bi bi-chevron-down sidebar-chevron"></i>
                </button>

                <div class="collapse {{ request()->routeIs('admin.produk.*') || request()->routeIs('admin.stok.*') ? 'show' : '' }}"
                     id="menuProduk">
                    <div class="sidebar-submenu">
                        <a href="{{ route('admin.produk.index') }}"
                           class="{{ request()->routeIs('admin.produk.*') ? 'active' : '' }}">
                            Produk Tahu
                        </a>
                        <a href="{{ route('admin.stok.index') }}"
                           class="{{ request()->routeIs('admin.stok.*') ? 'active' : '' }}">
                            Stok Produk
                        </a>
                    </div>
                </div>
            </div>

            <div>
                <button type="button"
                        class="sidebar-parent {{ request()->routeIs('admin.pesanan.*') || request()->routeIs('admin.pembayaran.*') ? 'active' : '' }}"
                        data-bs-toggle="collapse"
                        data-bs-target="#menuTransaksi"
                        aria-expanded="{{ request()->routeIs('admin.pesanan.*') || request()->routeIs('admin.pembayaran.*') ? 'true' : 'false' }}">
                    <span class="sidebar-icon"><i class="bi bi-receipt-cutoff"></i></span>
                    <span class="sidebar-label">Transaksi</span>
                    <i class="bi bi-chevron-down sidebar-chevron"></i>
                </button>

                <div class="collapse {{ request()->routeIs('admin.pesanan.*') || request()->routeIs('admin.pembayaran.*') ? 'show' : '' }}"
                     id="menuTransaksi">
                    <div class="sidebar-submenu">
                        <a href="{{ route('admin.pesanan.index') }}"
                           class="{{ request()->routeIs('admin.pesanan.*') ? 'active' : '' }}">
                            Pesanan
                        </a>
                        <a href="{{ route('admin.pembayaran.index') }}"
                           class="{{ request()->routeIs('admin.pembayaran.*') ? 'active' : '' }}">
                            Pembayaran
                        </a>
                    </div>
                </div>
            </div>

            <div>
                <button type="button"
                        class="sidebar-parent {{ request()->routeIs('admin.pengiriman.*') || request()->routeIs('admin.pembeli.*') || request()->routeIs('admin.ulasan.*') ? 'active' : '' }}"
                        data-bs-toggle="collapse"
                        data-bs-target="#menuOperasional"
                        aria-expanded="{{ request()->routeIs('admin.pengiriman.*') || request()->routeIs('admin.pembeli.*') || request()->routeIs('admin.ulasan.*') ? 'true' : 'false' }}">
                    <span class="sidebar-icon"><i class="bi bi-truck"></i></span>
                    <span class="sidebar-label">Operasional</span>
                    <i class="bi bi-chevron-down sidebar-chevron"></i>
                </button>

                <div class="collapse {{ request()->routeIs('admin.pengiriman.*') || request()->routeIs('admin.pembeli.*') || request()->routeIs('admin.ulasan.*') ? 'show' : '' }}"
                     id="menuOperasional">
                    <div class="sidebar-submenu">
                        <a href="{{ route('admin.pengiriman.index') }}"
                           class="{{ request()->routeIs('admin.pengiriman.*') ? 'active' : '' }}">
                            Pengambilan & Pengantaran
                        </a>
                        <a href="{{ route('admin.pembeli.index') }}"
                           class="{{ request()->routeIs('admin.pembeli.*') ? 'active' : '' }}">
                            Pembeli
                        </a>
                        <a href="{{ route('admin.ulasan.index') }}"
                           class="{{ request()->routeIs('admin.ulasan.*') ? 'active' : '' }}">
                            Ulasan
                        </a>
                    </div>
                </div>
            </div>

            <div class="sidebar-section-title">Laporan & Konten</div>

            <a href="{{ route('admin.laporan.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
                <span class="sidebar-icon"><i class="bi bi-bar-chart-fill"></i></span>
                <span class="sidebar-label">Laporan</span>
            </a>

            <a href="{{ route('admin.banner.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.banner.*') ? 'active' : '' }}">
                <span class="sidebar-icon"><i class="bi bi-images"></i></span>
                <span class="sidebar-label">Banner</span>
            </a>

            <div class="sidebar-section-title">Sistem</div>

            <a href="{{ route('admin.pengaturan.edit') }}"
               class="sidebar-link {{ request()->routeIs('admin.pengaturan.*') ? 'active' : '' }}">
                <span class="sidebar-icon"><i class="bi bi-shop-window"></i></span>
                <span class="sidebar-label">Pengaturan Toko</span>
            </a>

            <a href="{{ route('admin.pengguna-admin.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.pengguna-admin.*') ? 'active' : '' }}">
                <span class="sidebar-icon"><i class="bi bi-person-badge-fill"></i></span>
                <span class="sidebar-label">Pengguna Admin</span>
            </a>

            <a href="{{ route('admin.akun.edit') }}"
               class="sidebar-link {{ request()->routeIs('admin.akun.*') ? 'active' : '' }}">
                <span class="sidebar-icon"><i class="bi bi-person-gear"></i></span>
                <span class="sidebar-label">Akun Saya</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="mini-profile-card">
                <div class="mini-avatar">
                    {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <div class="fw-bold text-dark text-truncate small">
                        {{ auth()->user()->name ?? 'Admin' }}
                    </div>
                    <div class="text-muted text-truncate" style="font-size: 0.72rem;">
                        {{ auth()->user()->email ?? 'admin@sitahu.com' }}
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <main class="app-main">
        <header class="topbar">
            <div class="d-flex align-items-center gap-3 min-w-0">
                <button class="icon-btn d-lg-none" type="button" id="mobileMenuToggle" aria-label="Buka menu">
                    <i class="bi bi-list fs-5"></i>
                </button>

                <div class="topbar-title-wrap">
                    <h6 class="topbar-title">@yield('page_title', 'Dashboard Admin')</h6>
                    <p class="topbar-subtitle">Kelola penjualan produk tahu dari satu tempat.</p>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 gap-md-3">
                <div class="topbar-date d-none d-md-flex align-items-center gap-2">
                    <i class="bi bi-calendar3"></i>
                    {{ now()->translatedFormat('d M Y') }}
                </div>

                <div class="dropdown">
                    <button class="icon-btn position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell"></i>
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-white rounded-circle mt-1"></span>
                    </button>

                    <div class="dropdown-menu dropdown-menu-end p-0 mt-2" style="width: 310px;">
                        <div class="px-3 py-3 border-bottom">
                            <div class="fw-bold">Notifikasi</div>
                            <div class="text-muted small">Info cepat untuk admin toko</div>
                        </div>

                        <a href="{{ route('admin.pesanan.index') }}" class="dropdown-item p-3 text-wrap">
                            <div class="d-flex gap-3">
                                <div class="text-warning">
                                    <i class="bi bi-bag-check-fill fs-5"></i>
                                </div>
                                <div>
                                    <div class="small fw-bold text-dark">Cek pesanan terbaru</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">
                                        Pesanan dari mobile akan muncul di halaman pesanan admin.
                                    </div>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('admin.stok.index') }}" class="dropdown-item p-3 text-wrap border-top">
                            <div class="d-flex gap-3">
                                <div class="text-danger">
                                    <i class="bi bi-box-seam fs-5"></i>
                                </div>
                                <div>
                                    <div class="small fw-bold text-dark">Pantau stok produk</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">
                                        Pastikan stok tahu tetap aman sebelum pembeli checkout.
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="dropdown">
                    <button class="btn border-0 p-0 d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="text-end d-none d-md-block">
                            <div class="fw-bold small text-dark lh-1">
                                {{ auth()->user()->name ?? 'Admin' }}
                            </div>
                            <div class="text-muted mt-1" style="font-size: 0.72rem;">
                                {{ auth()->user()->email ?? 'admin@sitahu.com' }}
                            </div>
                        </div>

                        <div class="user-avatar">
                            {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}
                        </div>
                    </button>

                    <div class="dropdown-menu dropdown-menu-end mt-2 p-2">
                        <a class="dropdown-item small py-2 d-flex align-items-center gap-2"
                           href="{{ route('admin.akun.edit') }}">
                            <i class="bi bi-person-gear text-muted"></i>
                            Pengaturan Akun
                        </a>

                        <a class="dropdown-item small py-2 d-flex align-items-center gap-2"
                           href="{{ route('admin.pengaturan.edit') }}">
                            <i class="bi bi-shop-window text-muted"></i>
                            Pengaturan Toko
                        </a>

                        <div class="dropdown-divider"></div>

                        <form method="POST"
                              action="{{ route('logout') }}"
                              data-confirm-title="Keluar Akun"
                              data-confirm-message="Yakin ingin keluar dari dashboard SiTahu?"
                              data-confirm-button="Keluar">
                            @csrf

                            <button class="dropdown-item small py-2 d-flex align-items-center gap-2 text-danger fw-bold"
                                    type="submit">
                                <i class="bi bi-box-arrow-right"></i>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <div class="content-wrapper">
            @yield('content')

            <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1080">
                @if(session('success'))
                    <div class="toast align-items-center text-bg-success border-0 shadow js-auto-toast"
                         role="alert"
                         aria-live="assertive"
                         aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                            </div>
                            <button type="button"
                                    class="btn-close btn-close-white me-2 m-auto"
                                    data-bs-dismiss="toast"
                                    aria-label="Tutup"></button>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="toast text-bg-danger border-0 shadow js-auto-toast"
                         role="alert"
                         aria-live="assertive"
                         aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                <strong class="d-block mb-1">
                                    <i class="bi bi-exclamation-triangle me-2"></i>Periksa input
                                </strong>

                                <ul class="mb-0 small ps-3">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>

                            <button type="button"
                                    class="btn-close btn-close-white me-2 mt-2"
                                    data-bs-dismiss="toast"
                                    aria-label="Tutup"></button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </main>
</div>

<div class="modal fade" id="globalConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-body p-4 text-center">
                <div class="mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center text-warning-emphasis bg-warning-subtle"
                     style="width:64px;height:64px;">
                    <i class="bi bi-exclamation-triangle fs-3"></i>
                </div>

                <h5 class="fw-bold text-dark mb-2" id="globalConfirmTitle">
                    Konfirmasi Tindakan
                </h5>

                <p class="text-muted small mb-0" id="globalConfirmMessage">
                    Apakah kamu yakin ingin melanjutkan tindakan ini?
                </p>
            </div>

            <div class="modal-footer border-0 bg-light p-3 justify-content-center gap-2">
                <button type="button" class="btn btn-light border fw-bold px-4" data-bs-dismiss="modal">
                    Batal
                </button>

                <button type="button"
                        class="btn btn-brand fw-bold px-4"
                        id="globalConfirmButton">
                    Lanjutkan
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const body = document.body;
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const brandToggle = document.getElementById('brandToggle');
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');

    if (localStorage.getItem('sitahu-sidebar-collapsed') === '1' && window.innerWidth > 900) {
        body.classList.add('sidebar-collapsed');
    }

    brandToggle?.addEventListener('click', () => {
        if (window.innerWidth > 900) {
            body.classList.toggle('sidebar-collapsed');
            localStorage.setItem(
                'sitahu-sidebar-collapsed',
                body.classList.contains('sidebar-collapsed') ? '1' : '0'
            );
        }
    });

    mobileMenuToggle?.addEventListener('click', () => {
        sidebar?.classList.add('open');
        sidebarOverlay?.classList.remove('d-none');
        sidebarOverlay?.classList.add('show');
    });

    sidebarOverlay?.addEventListener('click', () => {
        sidebar?.classList.remove('open');
        sidebarOverlay?.classList.add('d-none');
        sidebarOverlay?.classList.remove('show');
    });

    document.querySelectorAll('.app-sidebar a').forEach((link) => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 900) {
                sidebar?.classList.remove('open');
                sidebarOverlay?.classList.add('d-none');
                sidebarOverlay?.classList.remove('show');
            }
        });
    });

    document.querySelectorAll('.app-sidebar [data-bs-toggle="collapse"]').forEach((button) => {
        button.addEventListener('click', () => {
            if (body.classList.contains('sidebar-collapsed') && window.innerWidth > 900) {
                body.classList.remove('sidebar-collapsed');
                localStorage.setItem('sitahu-sidebar-collapsed', '0');
            }
        });
    });

    document.querySelectorAll('.js-instant-filter').forEach((form) => {
        let timer = null;

        const submitForm = () => {
            window.clearTimeout(timer);
            timer = window.setTimeout(() => {
                if (typeof form.requestSubmit === 'function') {
                    form.requestSubmit();
                } else {
                    form.submit();
                }
            }, 450);
        };

        form.querySelectorAll('input[type="search"], input[type="text"], input[type="date"], select').forEach((field) => {
            field.addEventListener('input', submitForm);
            field.addEventListener('change', submitForm);
        });
    });

    document.querySelectorAll('.js-auto-toast').forEach((toastEl) => {
        new bootstrap.Toast(toastEl, { delay: 4200 }).show();
    });

    let pendingConfirmForm = null;

    const globalConfirmModalEl = document.getElementById('globalConfirmModal');
    const globalConfirmTitle = document.getElementById('globalConfirmTitle');
    const globalConfirmMessage = document.getElementById('globalConfirmMessage');
    const globalConfirmButton = document.getElementById('globalConfirmButton');
    const globalConfirmModal = globalConfirmModalEl ? new bootstrap.Modal(globalConfirmModalEl) : null;

    document.querySelectorAll('form[data-confirm-title], form[data-confirm-message]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (form.dataset.confirmed === '1') {
                form.dataset.confirmed = '0';
                return;
            }

            event.preventDefault();
            pendingConfirmForm = form;

            if (globalConfirmTitle) {
                globalConfirmTitle.textContent = form.dataset.confirmTitle || 'Konfirmasi Tindakan';
            }

            if (globalConfirmMessage) {
                globalConfirmMessage.textContent = form.dataset.confirmMessage || 'Apakah kamu yakin ingin melanjutkan tindakan ini?';
            }

            if (globalConfirmButton) {
                globalConfirmButton.textContent = form.dataset.confirmButton || 'Lanjutkan';
            }

            globalConfirmModal?.show();
        });
    });

    globalConfirmButton?.addEventListener('click', () => {
        if (!pendingConfirmForm) return;

        pendingConfirmForm.dataset.confirmed = '1';
        globalConfirmModal?.hide();

        if (typeof pendingConfirmForm.requestSubmit === 'function') {
            pendingConfirmForm.requestSubmit();
        } else {
            pendingConfirmForm.submit();
        }
    });
</script>

@stack('scripts')
</body>
</html>
