<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SiTahu Admin')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --brand: #c89335;
            --brand-2: #d6a64b;
            --brand-dark: #7a5618;
            --brand-soft: #fff8ea;
            --brand-soft-2: #fff3d8;
            --body: #f6f7f9;
            --surface: #ffffff;
            --surface-soft: #fbfcfd;
            --text: #111827;
            --muted: #667085;
            --border: #e7eaf0;
            --danger: #dc2626;
            --success: #16a34a;
            --warning: #ca8a04;
            --info: #2563eb;
            --sidebar-width: 284px;
            --sidebar-mini: 84px;
            --topbar-height: 76px;
            --radius: 22px;
            --shadow: 0 18px 45px rgba(16, 24, 40, .10);
            --shadow-soft: 0 10px 28px rgba(16, 24, 40, .07);
        }

        * { box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            min-height: 100vh;
            margin: 0;
            color: var(--text);
            font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background:
                radial-gradient(circle at 4% 4%, rgba(200,147,53,.12), transparent 28rem),
                radial-gradient(circle at 92% 8%, rgba(255,248,234,.95), transparent 24rem),
                linear-gradient(180deg, #f9fafb 0%, var(--body) 58%, #f3f5f8 100%);
        }

        a { color: inherit; }
        .fw-black { font-weight: 950 !important; }

        .admin-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: var(--sidebar-width) minmax(0, 1fr);
            transition: grid-template-columns .22s ease;
        }

        body.sidebar-mini .admin-shell { grid-template-columns: var(--sidebar-mini) minmax(0, 1fr); }

        .admin-sidebar {
            position: sticky;
            top: 0;
            z-index: 1040;
            height: 100vh;
            display: flex;
            flex-direction: column;
            border-right: 1px solid rgba(231,234,240,.88);
            background: rgba(255,255,255,.86);
            backdrop-filter: blur(22px);
            overflow-y: auto;
        }

        .admin-sidebar::-webkit-scrollbar { width: 5px; }
        .admin-sidebar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 999px; }

        .brand-area {
            min-height: var(--topbar-height);
            padding: 0 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--border);
        }

        .brand-button {
            width: 46px;
            height: 46px;
            border: 0;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--brand), #ad7a24);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 18px 38px rgba(200,147,53,.24);
            flex-shrink: 0;
        }

        .brand-text-title {
            color: #0f172a;
            font-size: 1.1rem;
            font-weight: 950;
            letter-spacing: -.045em;
            line-height: 1;
        }

        .brand-text-subtitle {
            margin-top: 5px;
            color: var(--muted);
            font-size: .72rem;
            font-weight: 800;
        }

        .sidebar-menu {
            flex: 1;
            padding: 16px 12px 18px;
        }

        .sidebar-section {
            margin-bottom: 15px;
        }

        .sidebar-heading {
            margin: 13px 10px 8px;
            color: #9ca3af;
            font-size: .66rem;
            font-weight: 950;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .sidebar-link,
        .sidebar-parent {
            position: relative;
            width: 100%;
            min-height: 46px;
            padding: 10px 12px;
            border: 1px solid transparent;
            border-radius: 16px;
            background: transparent;
            color: #4b5563;
            display: flex;
            align-items: center;
            gap: 11px;
            text-decoration: none;
            font-size: .88rem;
            font-weight: 900;
            line-height: 1.1;
            transition: .18s ease;
        }

        .sidebar-link:hover,
        .sidebar-parent:hover {
            background: #fff;
            border-color: var(--border);
            color: var(--text);
            transform: translateX(2px);
            box-shadow: 0 10px 22px rgba(16,24,40,.05);
        }

        .sidebar-link.active,
        .sidebar-parent.active {
            background: linear-gradient(135deg, #fff8ea, #ffffff);
            border-color: #f1d49c;
            color: var(--brand-dark);
            box-shadow: 0 12px 28px rgba(200,147,53,.12);
        }

        .sidebar-link.active::before,
        .sidebar-parent.active::before {
            content: "";
            position: absolute;
            left: -12px;
            top: 12px;
            bottom: 12px;
            width: 4px;
            border-radius: 999px;
            background: var(--brand);
        }

        .sidebar-icon {
            width: 25px;
            height: 25px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.02rem;
            flex-shrink: 0;
        }

        .sidebar-link.active .sidebar-icon,
        .sidebar-parent.active .sidebar-icon {
            background: #fff2d6;
        }

        .sidebar-label {
            flex: 1;
            min-width: 0;
            text-align: left;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-badge {
            min-width: 22px;
            height: 22px;
            padding: 0 7px;
            border-radius: 999px;
            background: #fff1f2;
            color: #b91c1c;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .68rem;
            font-weight: 950;
            border: 1px solid #fecdd3;
        }

        .sidebar-note {
            margin: 16px 6px 0;
            padding: 12px;
            border-radius: 18px;
            border: 1px solid #f1d49c;
            background: linear-gradient(135deg, #fff8ea, #fff);
            color: var(--brand-dark);
            font-size: .75rem;
            font-weight: 850;
        }

        .admin-main {
            min-width: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 1030;
            min-height: var(--topbar-height);
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            border-bottom: 1px solid rgba(231,234,240,.88);
            background: rgba(255,255,255,.84);
            backdrop-filter: blur(22px);
        }

        .topbar-title {
            margin: 0;
            color: #111827;
            font-size: 1rem;
            font-weight: 950;
            letter-spacing: -.035em;
        }

        .topbar-subtitle {
            margin: 4px 0 0;
            color: var(--muted);
            font-size: .77rem;
            font-weight: 750;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .icon-button {
            width: 42px;
            height: 42px;
            border-radius: 15px;
            border: 1px solid var(--border);
            background: #fff;
            color: #475467;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: .18s ease;
        }

        .icon-button:hover {
            background: var(--brand-soft);
            border-color: #f1d49c;
            color: var(--brand-dark);
            transform: translateY(-1px);
        }

        .quick-pill,
        .date-pill {
            min-height: 42px;
            padding: 0 14px;
            border-radius: 999px;
            border: 1px solid var(--border);
            background: #fff;
            color: #344054;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            font-size: .8rem;
            font-weight: 900;
            white-space: nowrap;
            transition: .18s ease;
        }

        .quick-pill:hover {
            color: var(--brand-dark);
            border-color: #f1d49c;
            background: var(--brand-soft);
            transform: translateY(-1px);
        }

        .quick-pill.primary {
            color: #fff;
            border-color: transparent;
            background: linear-gradient(135deg, var(--brand), #ad7a24);
            box-shadow: 0 12px 25px rgba(200,147,53,.20);
        }

        .quick-pill.primary:hover { color: #fff; }

        .avatar,
        .mini-avatar {
            border-radius: 999px;
            background: linear-gradient(135deg, var(--brand), #ad7a24);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 950;
            flex-shrink: 0;
        }

        .avatar {
            width: 42px;
            height: 42px;
            font-size: .83rem;
            box-shadow: 0 10px 22px rgba(200,147,53,.22);
        }

        .content-area {
            flex: 1;
            width: 100%;
            max-width: 1560px;
            margin: 0 auto;
            padding: 24px;
        }

        .page-card,
        .card,
        .sc-box {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-soft);
        }

        .card { overflow: hidden; }
        .card-pad { padding: 18px; }

        .hero {
            margin-bottom: 18px;
            padding: 22px;
            border-radius: 23px;
            border: 1px solid #f1d49c;
            background:
                radial-gradient(circle at 90% 100%, rgba(200,147,53,.16), transparent 17rem),
                linear-gradient(135deg, #ffffff, #fff8e9);
            box-shadow: var(--shadow-soft);
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
        }

        .hero h1 {
            margin: 0 0 5px;
            font-size: 1.42rem;
            font-weight: 950;
            letter-spacing: -.05em;
        }

        .hero p {
            margin: 0;
            color: var(--muted);
            font-size: .9rem;
            font-weight: 650;
        }

        .grid { display: grid; gap: 16px; }
        .g2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .g3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .g4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }

        .stat {
            min-height: 118px;
            padding: 17px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .stat-label {
            color: var(--muted);
            font-size: .74rem;
            font-weight: 950;
            letter-spacing: .05em;
            text-transform: uppercase;
        }

        .stat-value {
            margin-top: 8px;
            color: var(--text);
            font-size: 1.48rem;
            font-weight: 950;
            line-height: 1;
            letter-spacing: -.05em;
        }

        .stat-note {
            display: inline-block;
            margin-top: 8px;
            font-size: .74rem;
            font-weight: 850;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 17px;
            background: var(--brand-soft);
            color: var(--brand-dark);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
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

        .form-control,
        .form-select,
        .field {
            min-height: 42px;
            border-radius: 14px;
            border-color: var(--border);
            color: var(--text);
            font-size: .88rem;
            font-weight: 700;
        }

        .field {
            border: 1px solid var(--border);
            background: #fff;
            padding: 9px 12px;
            outline: 0;
        }

        .form-control:focus,
        .form-select:focus,
        .field:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 4px rgba(200,147,53,.16);
        }

        .btn { border-radius: 13px; font-weight: 900; }

        .btn-brand,
        .btn-primary {
            background: var(--brand);
            border-color: var(--brand);
            color: #fff;
        }

        .btn-brand:hover,
        .btn-primary:hover {
            background: #ad7a24;
            border-color: #ad7a24;
            color: #fff;
        }

        .btn-dark-soft { background: #111827; border-color: #111827; color: #fff; }

        .small-btn,
        .btn-action {
            min-height: 34px;
            padding: 7px 10px;
            border-radius: 11px;
            border: 1px solid var(--border);
            background: #fff;
            color: #374151;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            text-decoration: none;
            font-size: .78rem;
            font-weight: 900;
            transition: .16s ease;
        }

        .small-btn:hover,
        .btn-action:hover {
            background: var(--brand-soft);
            border-color: #f1d49c;
            color: var(--brand-dark);
            transform: translateY(-1px);
        }

        .actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .inline-form { display: inline; }
        .min-w-0 { min-width: 0; }

        .table-wrap { width: 100%; overflow-x: auto; }

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
            background: #fbfbfc;
            color: var(--muted);
            font-size: .72rem;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .06em;
            white-space: nowrap;
        }

        .table-wrap td,
        table td {
            color: var(--text);
            font-size: .88rem;
            font-weight: 650;
        }

        .table-wrap tbody tr:hover,
        table tbody tr:hover { background: #fcfcfc; }

        .sub {
            display: block;
            margin-top: 4px;
            color: var(--muted);
            font-size: .75rem;
            font-weight: 650;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: .72rem;
            font-weight: 950;
            white-space: nowrap;
        }

        .chip::before {
            content: "";
            width: 6px;
            height: 6px;
            border-radius: 999px;
            background: currentColor;
        }

        .c-green { color: #15803d; background: #dcfce7; }
        .c-blue { color: #1d4ed8; background: #dbeafe; }
        .c-purple { color: #7e22ce; background: #f3e8ff; }
        .c-yellow { color: #92400e; background: #fef3c7; }
        .c-red { color: #b91c1c; background: #fee2e2; }
        .c-gray { color: #4b5563; background: #f3f4f6; }

        .cover {
            width: 50px;
            height: 50px;
            border-radius: 15px;
            object-fit: cover;
            border: 1px solid var(--border);
            background: #f3f4f6;
        }

        .pagination { margin: 0; }
        .pagination .page-link {
            margin: 0 2px;
            border-radius: 10px;
            border-color: var(--border);
            color: var(--brand-dark);
            font-weight: 850;
        }
        .pagination .active .page-link {
            background: var(--brand);
            border-color: var(--brand);
            color: #fff;
        }

        .dropdown-menu {
            border-radius: 19px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
        }

        .dropdown-item { border-radius: 12px; font-weight: 800; }
        .dropdown-item:active { background: var(--brand); color: #fff; }

        body.sidebar-mini .brand-info,
        body.sidebar-mini .sidebar-label,
        body.sidebar-mini .sidebar-heading,
        body.sidebar-mini .sidebar-badge,
        body.sidebar-mini .sidebar-note {
            display: none !important;
        }

        body.sidebar-mini .brand-area { padding: 0; justify-content: center; }
        body.sidebar-mini .sidebar-menu { padding-left: 10px; padding-right: 10px; }
        body.sidebar-mini .sidebar-link,
        body.sidebar-mini .sidebar-parent {
            width: 48px;
            height: 48px;
            min-height: 48px;
            padding: 0;
            margin-left: auto;
            margin-right: auto;
            justify-content: center;
        }
        body.sidebar-mini .sidebar-link.active::before,
        body.sidebar-mini .sidebar-parent.active::before { display: none; }

        .mobile-overlay {
            position: fixed;
            inset: 0;
            z-index: 1035;
            background: rgba(15, 23, 42, .42);
        }

        .sitahu-pagination {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
            padding: 14px 16px;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 18px;
            box-shadow: var(--shadow-soft);
        }
        .sitahu-page-info {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: var(--muted);
            font-size: .82rem;
            font-weight: 800;
        }
        .sitahu-page-info strong { color: var(--text); font-weight: 900; }
        .sitahu-page-list {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex-wrap: wrap;
            gap: 7px;
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sitahu-page-link {
            min-width: 38px;
            height: 38px;
            padding: 0 12px;
            border-radius: 999px;
            border: 1px solid var(--border);
            background: #fff;
            color: var(--text);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: .82rem;
            font-weight: 900;
            transition: .18s ease;
        }
        .sitahu-page-link:hover {
            color: var(--brand-dark);
            border-color: rgba(223,186,104,.55);
            background: var(--brand-soft);
            transform: translateY(-1px);
        }
        .sitahu-page-item.active .sitahu-page-link {
            color: #fff;
            border-color: transparent;
            background: linear-gradient(135deg, var(--brand), var(--brand-dark));
            box-shadow: 0 10px 22px rgba(223,186,104,.28);
        }
        .sitahu-page-item.disabled .sitahu-page-link {
            color: #9ca3af;
            background: #f9fafb;
            cursor: not-allowed;
            transform: none;
        }
        .sitahu-page-link.dots {
            border-color: transparent;
            background: transparent;
            min-width: 24px;
            padding: 0 4px;
        }



        .modal-dialog-scrollable .modal-content {
            max-height: calc(100vh - 2rem);
        }
        .modal-dialog-scrollable .modal-body {
            overflow-y: auto;
        }
        .modal-xl.modal-dialog-scrollable .modal-body,
        .modal-lg.modal-dialog-scrollable .modal-body {
            max-height: calc(100vh - 13rem);
        }
        #globalConfirmModal .modal-content {
            max-height: calc(100vh - 2rem);
        }
        #globalConfirmModal .modal-body {
            overflow-y: auto;
        }

        @media (max-width: 1200px) {
            .g4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .g3 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .quick-pill span { display: none; }
        }

        @media (max-width: 900px) {
            .admin-shell,
            body.sidebar-mini .admin-shell { grid-template-columns: 1fr; }

            .admin-sidebar {
                position: fixed;
                inset: 0 auto 0 0;
                width: var(--sidebar-width);
                transform: translateX(-105%);
                transition: transform .2s ease;
                box-shadow: 18px 0 40px rgba(15, 23, 42, .15);
            }
            .admin-sidebar.open { transform: translateX(0); }
            .topbar { padding: 0 14px; }
            .content-area { padding: 16px; }
            .hero { align-items: flex-start; flex-direction: column; }

            body.sidebar-mini .brand-info,
            body.sidebar-mini .sidebar-label,
            body.sidebar-mini .sidebar-heading,
            body.sidebar-mini .sidebar-badge,
            body.sidebar-mini .sidebar-note { display: block !important; }
            body.sidebar-mini .brand-area { padding: 0 18px; justify-content: flex-start; }
            body.sidebar-mini .sidebar-link,
            body.sidebar-mini .sidebar-parent {
                width: 100%;
                height: auto;
                min-height: 46px;
                padding: 10px 12px;
                justify-content: flex-start;
            }
        }

        @media (max-width: 640px) {
            .g4, .g3, .g2 { grid-template-columns: 1fr; }
            .toolbar, .toolbar-left { align-items: stretch; flex-direction: column; }
            .field, .toolbar .btn { width: 100%; }
            .topbar-title-wrap, .date-pill, .topbar .quick-pill { display: none !important; }
            .sitahu-pagination { justify-content: center; text-align: center; }
            .sitahu-page-info { width: 100%; justify-content: center; }
            .sitahu-page-list { width: 100%; justify-content: center; }
        }
    </style>

    @stack('styles')
</head>

<body>
<div class="mobile-overlay d-none" id="sidebarOverlay"></div>

<div class="admin-shell">
    <aside class="admin-sidebar" id="sidebar">
        <div class="brand-area">
            <button class="brand-button" id="sidebarToggle" type="button" title="Kecilkan menu">
                <i class="bi bi-box-seam-fill fs-5"></i>
            </button>

            <div class="brand-info min-w-0">
                <div class="brand-text-title">SiTahu</div>
                <div class="brand-text-subtitle">Pusat Operasional Toko</div>
            </div>
        </div>

        <nav class="sidebar-menu" aria-label="Menu Admin SiTahu">
            <div class="sidebar-section">
                <div class="sidebar-heading">Kontrol</div>

                <a href="{{ route('admin.dashboard') }}"
                   class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="bi bi-grid-1x2-fill"></i></span>
                    <span class="sidebar-label">Dashboard</span>
                </a>
            </div>

            <div class="sidebar-section">
                <div class="sidebar-heading">Operasional Harian</div>

                <a href="{{ route('admin.pembayaran.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.pembayaran.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="bi bi-credit-card-2-front-fill"></i></span>
                    <span class="sidebar-label">Pembayaran</span>
                </a>

                <a href="{{ route('admin.pesanan.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.pesanan.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="bi bi-receipt-cutoff"></i></span>
                    <span class="sidebar-label">Pesanan</span>
                </a>

                <a href="{{ route('admin.pengiriman.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.pengiriman.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="bi bi-truck"></i></span>
                    <span class="sidebar-label">Pengambilan & Kirim</span>
                </a>

                <a href="{{ route('admin.semua-pesanan.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.semua-pesanan.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="bi bi-journal-text"></i></span>
                    <span class="sidebar-label">Semua Pesanan</span>
                </a>
            </div>

            <div class="sidebar-section">
                <div class="sidebar-heading">Katalog</div>

                <a href="{{ route('admin.produk.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.produk.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="bi bi-basket2-fill"></i></span>
                    <span class="sidebar-label">Produk</span>
                </a>

                <a href="{{ route('admin.stok.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.stok.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="bi bi-boxes"></i></span>
                    <span class="sidebar-label">Stok</span>
                </a>

                <a href="{{ route('admin.banner.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.banner.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="bi bi-images"></i></span>
                    <span class="sidebar-label">Banner</span>
                </a>
            </div>

            <div class="sidebar-section">
                <div class="sidebar-heading">Pelanggan</div>

                <a href="{{ route('admin.pembeli.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.pembeli.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="bi bi-people-fill"></i></span>
                    <span class="sidebar-label">Pembeli</span>
                </a>

                <a href="{{ route('admin.ulasan.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.ulasan.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="bi bi-star-fill"></i></span>
                    <span class="sidebar-label">Ulasan</span>
                </a>
            </div>

            <div class="sidebar-section">
                <div class="sidebar-heading">Bisnis & Sistem</div>

                <a href="{{ route('admin.laporan.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="bi bi-bar-chart-fill"></i></span>
                    <span class="sidebar-label">Laporan</span>
                </a>

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
            </div>

            <div class="sidebar-note">
                <i class="bi bi-lightning-charge-fill me-1"></i>
                Kerjakan dari pembayaran aktif, proses pesanan, lalu ambil/kirim.
            </div>
        </nav>
    </aside>

    <main class="admin-main">
        <header class="topbar">
            <div class="d-flex align-items-center gap-3 min-w-0">
                <button class="icon-button d-lg-none" type="button" id="mobileMenuToggle" aria-label="Buka menu">
                    <i class="bi bi-list fs-5"></i>
                </button>

                <div class="topbar-title-wrap min-w-0">
                    <h6 class="topbar-title text-truncate">@yield('page_title', 'Dashboard Admin')</h6>
                    <p class="topbar-subtitle text-truncate">@yield('page_subtitle', 'Kelola operasional toko dari pesanan sampai stok.')</p>
                </div>
            </div>

            <div class="topbar-actions">
                <a class="quick-pill d-none d-xl-inline-flex" href="{{ route('pembeli-web.home') }}" target="_blank" rel="noopener">
                    <i class="bi bi-globe2"></i>
                    <span>Lihat Toko</span>
                </a>

                <a class="quick-pill primary d-none d-md-inline-flex" href="{{ route('admin.produk.create') }}">
                    <i class="bi bi-plus-lg"></i>
                    <span>Tambah Produk</span>
                </a>

                <div class="date-pill d-none d-lg-inline-flex">
                    <i class="bi bi-calendar3"></i>
                    {{ now()->translatedFormat('d M Y') }}
                </div>

                <div class="dropdown">
                    <button class="icon-button position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifikasi">
                        <i class="bi bi-bell"></i>
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-white rounded-circle mt-1"></span>
                    </button>

                    <div class="dropdown-menu dropdown-menu-end p-0 mt-2" style="width: 330px;">
                        <div class="px-3 py-3 border-bottom">
                            <div class="fw-bold text-dark">Pusat Pengecekan</div>
                            <div class="text-muted small">Akses cepat untuk pekerjaan admin.</div>
                        </div>

                        <a href="{{ route('admin.pembayaran.index') }}" class="dropdown-item p-3 text-wrap">
                            <div class="d-flex gap-3">
                                <div class="text-warning"><i class="bi bi-credit-card-2-front-fill fs-5"></i></div>
                                <div>
                                    <div class="small fw-bold text-dark">Verifikasi pembayaran</div>
                                    <div class="text-muted" style="font-size: .75rem;">Cek bukti transfer dan ubah status pesanan.</div>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('admin.pesanan.index') }}" class="dropdown-item p-3 text-wrap border-top">
                            <div class="d-flex gap-3">
                                <div class="text-success"><i class="bi bi-bag-check-fill fs-5"></i></div>
                                <div>
                                    <div class="small fw-bold text-dark">Proses pesanan</div>
                                    <div class="text-muted" style="font-size: .75rem;">Lihat pesanan baru, siap kirim, atau selesai.</div>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('admin.stok.index') }}" class="dropdown-item p-3 text-wrap border-top">
                            <div class="d-flex gap-3">
                                <div class="text-danger"><i class="bi bi-box-seam fs-5"></i></div>
                                <div>
                                    <div class="small fw-bold text-dark">Pantau stok</div>
                                    <div class="text-muted" style="font-size: .75rem;">Pastikan stok produk cukup sebelum pembeli checkout.</div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="dropdown">
                    <button class="btn border-0 p-0 d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="text-end d-none d-md-block">
                            <div class="fw-bold small text-dark lh-1">{{ auth()->user()->name ?? 'Admin' }}</div>
                            <div class="text-muted mt-1" style="font-size: .72rem;">{{ auth()->user()->email ?? 'admin@sitahu.com' }}</div>
                        </div>

                        <div class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}</div>
                    </button>

                    <div class="dropdown-menu dropdown-menu-end mt-2 p-2">
                        <div class="px-2 py-2 d-md-none">
                            <div class="fw-bold small text-dark">{{ auth()->user()->name ?? 'Admin' }}</div>
                            <div class="text-muted" style="font-size: .72rem;">{{ auth()->user()->email ?? 'admin@sitahu.com' }}</div>
                        </div>

                        <a class="dropdown-item small py-2 d-flex align-items-center gap-2" href="{{ route('admin.akun.edit') }}">
                            <i class="bi bi-person-gear text-muted"></i>
                            Akun Saya
                        </a>

                        <a class="dropdown-item small py-2 d-flex align-items-center gap-2" href="{{ route('admin.pengaturan.edit') }}">
                            <i class="bi bi-shop-window text-muted"></i>
                            Pengaturan Toko
                        </a>

                        <a class="dropdown-item small py-2 d-flex align-items-center gap-2" href="{{ route('pembeli-web.home') }}" target="_blank" rel="noopener">
                            <i class="bi bi-globe2 text-muted"></i>
                            Lihat Toko
                        </a>

                        <div class="dropdown-divider"></div>

                        <form method="POST"
                              action="{{ route('logout') }}"
                              data-confirm-title="Keluar Akun"
                              data-confirm-message="Yakin ingin keluar dari dashboard SiTahu?"
                              data-confirm-button="Keluar">
                            @csrf

                            <button class="dropdown-item small py-2 d-flex align-items-center gap-2 text-danger fw-bold" type="submit">
                                <i class="bi bi-box-arrow-right"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <section class="content-area">
            @yield('content')
        </section>
    </main>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1080">
    @if(session('success'))
        <div class="toast align-items-center text-bg-success border-0 shadow js-auto-toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="toast align-items-center text-bg-danger border-0 shadow js-auto-toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body"><i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="toast text-bg-danger border-0 shadow js-auto-toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <strong class="d-block mb-1"><i class="bi bi-exclamation-triangle me-2"></i>Periksa input</strong>
                    <ul class="mb-0 small ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 mt-2" data-bs-dismiss="toast" aria-label="Tutup"></button>
            </div>
        </div>
    @endif
</div>

<div class="modal fade" id="globalConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-body p-4 text-center">
                <div class="mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center text-warning-emphasis bg-warning-subtle" style="width:64px;height:64px;">
                    <i class="bi bi-exclamation-triangle fs-3"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2" id="globalConfirmTitle">Konfirmasi Tindakan</h5>
                <p class="text-muted small mb-0" id="globalConfirmMessage">Apakah kamu yakin ingin melanjutkan tindakan ini?</p>
            </div>
            <div class="modal-footer border-0 bg-light p-3 justify-content-center gap-2">
                <button type="button" class="btn btn-light border fw-bold px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-brand fw-bold px-4" id="globalConfirmButton">Lanjutkan</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const body = document.body;
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');

    if (localStorage.getItem('sitahu-sidebar-mini') === '1' && window.innerWidth > 900) {
        body.classList.add('sidebar-mini');
    }

    sidebarToggle?.addEventListener('click', () => {
        if (window.innerWidth > 900) {
            body.classList.toggle('sidebar-mini');
            localStorage.setItem('sitahu-sidebar-mini', body.classList.contains('sidebar-mini') ? '1' : '0');
        }
    });

    mobileMenuToggle?.addEventListener('click', () => {
        sidebar?.classList.add('open');
        sidebarOverlay?.classList.remove('d-none');
    });

    sidebarOverlay?.addEventListener('click', () => {
        sidebar?.classList.remove('open');
        sidebarOverlay?.classList.add('d-none');
    });

    document.querySelectorAll('.admin-sidebar a').forEach((link) => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 900) {
                sidebar?.classList.remove('open');
                sidebarOverlay?.classList.add('d-none');
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

    window.showSitahuToast = function (message, type = 'info') {
        const container = document.querySelector('.toast-container');
        if (!container) return;

        const bgClass = type === 'success' ? 'text-bg-success' : type === 'error' || type === 'danger' ? 'text-bg-danger' : type === 'warning' ? 'text-bg-warning' : 'text-bg-dark';
        const icon = type === 'success' ? 'bi-check-circle' : type === 'error' || type === 'danger' ? 'bi-exclamation-triangle' : type === 'warning' ? 'bi-info-circle' : 'bi-bell';
        const toastEl = document.createElement('div');
        toastEl.className = `toast align-items-center ${bgClass} border-0 shadow js-dynamic-toast`;
        toastEl.setAttribute('role', 'alert');
        toastEl.setAttribute('aria-live', 'assertive');
        toastEl.setAttribute('aria-atomic', 'true');
        toastEl.innerHTML = `<div class="d-flex"><div class="toast-body"><i class="bi ${icon} me-2"></i>${message || 'Informasi berhasil diperbarui.'}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button></div>`;
        container.appendChild(toastEl);
        const toast = new bootstrap.Toast(toastEl, { delay: 4200 });
        toast.show();
        toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
    };

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
