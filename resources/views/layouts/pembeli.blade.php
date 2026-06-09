@php
    use Illuminate\Support\Str;

    $pengaturanLayout = \App\Models\PengaturanToko::utama();
    $keranjangDataLayout = app(\App\Services\WebPembeli\KeranjangService::class)->data();
    $keranjangTotalLayout = $keranjangDataLayout['totalItem'] ?? 0;
    $keranjangTotalBelanjaLayout = $keranjangDataLayout['totalBelanja'] ?? 0;
    $keranjangPreviewLayout = collect($keranjangDataLayout['items'] ?? [])->take(5);
    $namaTokoLayout = $pengaturanLayout->nama ?: 'SiTahu';
    $alamatTokoLayout = $pengaturanLayout->alamat ?: 'Produksi langsung dari toko kami.';
    $jamBukaLayout = $pengaturanLayout->jam_buka ?: '08.00';
    $jamTutupLayout = $pengaturanLayout->jam_tutup ?: null;
    $jamOperasionalLayout = $jamTutupLayout ? $jamBukaLayout . ' - ' . $jamTutupLayout : $jamBukaLayout;
    $teleponLayout = $pengaturanLayout->telepon ?: '081234567890';
    $waLayout = preg_replace('/[^0-9]/', '', $teleponLayout);
    if ($waLayout && Str::startsWith($waLayout, '0')) {
        $waLayout = '62' . Str::after($waLayout, '0');
    }
    $authModalLayout = session('auth_modal');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $namaTokoLayout . ' - Tahu Segar Berkualitas')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --brand-color: #c89335;
            --brand-hover: #ad7a24;
            --brand-dark: #7a5618;
            --brand-soft: #fff8ea;
            --brand-pale: #fdf4df;
            --ink: #182230;
            --muted: #667085;
            --muted-2: #98a2b3;
            --line: #eaecf0;
            --body-bg: #f8fafc;
            --white: #ffffff;
            --danger: #ef4444;
            --success: #16a34a;
            --warning: #f59e0b;
            --radius-xl: 28px;
            --radius-lg: 22px;
            --radius-md: 16px;
            --shadow-xs: 0 1px 2px rgba(16, 24, 40, .05);
            --shadow-sm: 0 8px 22px rgba(16, 24, 40, .06);
            --shadow-md: 0 18px 45px rgba(16, 24, 40, .10);
            --shadow-brand: 0 18px 40px rgba(200, 147, 53, .22);
        }

        * { box-sizing: border-box; }
        html { overflow-y: scroll; scrollbar-gutter: stable; scroll-behavior: smooth; }
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at top left, rgba(200,147,53,.10), transparent 32rem),
                radial-gradient(circle at 90% 12%, rgba(255,248,234,.95), transparent 20rem),
                var(--body-bg);
        }
        a { color: inherit; }
        img { max-width: 100%; }
        .text-brand { color: var(--brand-color) !important; }
        .fw-black { font-weight: 900 !important; }
        .min-w-0 { min-width: 0 !important; }
        .text-muted-soft { color: var(--muted) !important; }
        .bg-brand-soft { background-color: var(--brand-soft) !important; }
        .border-soft { border-color: var(--line) !important; }
        .main-content { flex: 1; padding-bottom: 3.5rem; }
        .container { --bs-gutter-x: 1.25rem; }

        .top-strip {
            background: linear-gradient(90deg, #111827, #263241);
            color: rgba(255,255,255,.88);
            font-size: 12px;
            font-weight: 650;
            letter-spacing: .01em;
        }
        .top-strip .divider { opacity: .28; }

        .navbar-shop {
            background: rgba(255,255,255,.92);
            border-bottom: 1px solid rgba(234,236,240,.86);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            box-shadow: 0 10px 28px rgba(16,24,40,.04);
        }
        .brand-mark {
            width: 44px;
            height: 44px;
            border-radius: 16px;
            display: grid;
            place-items: center;
            color: #fff;
            font-weight: 900;
            letter-spacing: -.04em;
            background: linear-gradient(135deg, var(--brand-color), var(--brand-hover));
            box-shadow: var(--shadow-brand);
            overflow: hidden;
        }
        .brand-mark img { width: 100%; height: 100%; object-fit: cover; }
        .brand-title { font-weight: 900; letter-spacing: -.045em; line-height: 1; }
        .brand-subtitle { color: var(--muted); font-size: 11px; font-weight: 700; }
        .nav-pill {
            font-weight: 750;
            font-size: 14px;
            color: var(--muted) !important;
            padding: 9px 13px !important;
            border-radius: 999px;
            transition: .22s ease;
        }
        .nav-pill:hover,
        .nav-pill.active {
            color: var(--brand-dark) !important;
            background: var(--brand-soft);
        }
        .header-search {
            width: min(440px, 100%);
            position: relative;
        }
        .header-search .form-control {
            height: 44px;
            padding-left: 42px;
            padding-right: 108px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: #f9fafb;
            font-size: 14px;
            font-weight: 650;
            box-shadow: none;
        }
        .header-search .form-control:focus {
            border-color: rgba(200,147,53,.55);
            background: #fff;
            box-shadow: 0 0 0 .25rem rgba(200,147,53,.13);
        }
        .header-search .search-icon { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--muted-2); }
        .header-search .search-btn {
            position: absolute;
            right: 5px;
            top: 5px;
            height: 34px;
            border-radius: 999px;
            border: 0;
            background: var(--brand-color);
            color: #fff;
            padding: 0 15px;
            font-size: 12px;
            font-weight: 850;
        }
        .header-action {
            position: relative;
            width: 44px;
            height: 44px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--line);
            background: #fff;
            color: var(--ink);
            text-decoration: none;
            box-shadow: var(--shadow-xs);
            transition: .22s ease;
        }
        .header-action:hover { color: var(--brand-dark); border-color: rgba(200,147,53,.45); background: var(--brand-soft); transform: translateY(-1px); }
        .cart-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            min-width: 20px;
            height: 20px;
            padding: 0 5px;
            border-radius: 999px;
            display: grid;
            place-items: center;
            background: var(--danger);
            color: #fff;
            border: 2px solid #fff;
            font-size: 10px;
            font-weight: 900;
            line-height: 1;
        }

        .btn-brand,
        .btn-brand:focus {
            background: linear-gradient(135deg, var(--brand-color), var(--brand-hover));
            color: #fff;
            border: 0;
            border-radius: 999px;
            font-weight: 850;
            box-shadow: 0 13px 28px rgba(200,147,53,.20);
        }
        .btn-brand:hover { color: #fff; transform: translateY(-1px); box-shadow: var(--shadow-brand); }
        .btn-soft-brand {
            background: var(--brand-soft);
            color: var(--brand-dark);
            border: 1px solid rgba(200,147,53,.20);
            border-radius: 999px;
            font-weight: 800;
        }
        .btn-soft-brand:hover { background: var(--brand-pale); color: var(--brand-dark); border-color: rgba(200,147,53,.35); }
        .btn-plain {
            border: 1px solid var(--line);
            background: #fff;
            color: var(--ink);
            border-radius: 999px;
            font-weight: 800;
        }
        .btn-plain:hover { border-color: rgba(200,147,53,.45); background: var(--brand-soft); color: var(--brand-dark); }

        .surface {
            background: rgba(255,255,255,.92);
            border: 1px solid rgba(234,236,240,.90);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
        }
        .surface-strong {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-md);
        }
        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: var(--brand-soft);
            color: var(--brand-dark);
            border: 1px solid rgba(200,147,53,.20);
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .02em;
        }
        .section-heading { font-weight: 900; letter-spacing: -.04em; line-height: 1.08; color: var(--ink); }
        .section-subtitle { color: var(--muted); line-height: 1.75; }
        .line-clamp-1, .line-clamp-2, .line-clamp-3 { display: -webkit-box; -webkit-box-orient: vertical; overflow: hidden; }
        .line-clamp-1 { -webkit-line-clamp: 1; }
        .line-clamp-2 { -webkit-line-clamp: 2; }
        .line-clamp-3 { -webkit-line-clamp: 3; }

        .shop-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 22px;
            overflow: hidden;
            box-shadow: var(--shadow-xs);
            height: 100%;
            transition: .26s ease;
        }
        .shop-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-md); border-color: rgba(200,147,53,.25); }
        .shop-card.js-product-card { cursor: pointer; }
        .product-media {
            position: relative;
            display: block;
            aspect-ratio: 1 / 1;
            background:
                radial-gradient(circle at 78% 12%, rgba(200,147,53,.18), transparent 30%),
                #f3f4f6;
            overflow: hidden;
            color: var(--brand-dark);
            font-weight: 900;
            text-decoration: none;
        }
        .product-media img { width: 100%; height: 100%; object-fit: cover; transition: .45s ease; }
        .shop-card:hover .product-media img { transform: scale(1.06); }
        .product-placeholder { position: absolute; inset: 0; display: grid; place-items: center; text-align: center; padding: 18px; }
        .product-badge {
            position: absolute;
            left: 12px;
            top: 12px;
            z-index: 2;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(255,255,255,.92);
            border: 1px solid rgba(234,236,240,.92);
            color: var(--success);
            font-size: 11px;
            font-weight: 900;
            box-shadow: var(--shadow-xs);
            backdrop-filter: blur(8px);
        }
        .product-badge.empty { color: #b91c1c; background: rgba(254,242,242,.94); border-color: rgba(248,113,113,.24); }
        .price-text { color: var(--brand-dark); font-weight: 900; letter-spacing: -.02em; }
        .meta-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 9px;
            border-radius: 999px;
            background: #f9fafb;
            border: 1px solid var(--line);
            color: var(--muted);
            font-size: 11px;
            font-weight: 800;
        }
        .rating-stars { color: #f59e0b; letter-spacing: -1px; white-space: nowrap; }
        .qty-group { display: inline-flex; align-items: center; border: 1px solid var(--line); border-radius: 999px; overflow: hidden; background: #fff; }
        .qty-group button, .qty-group .qty-btn { width: 36px; height: 36px; border: 0; display: grid; place-items: center; background: #fff; color: var(--ink); }
        .qty-group input { width: 48px; height: 36px; border: 0; border-left: 1px solid var(--line); border-right: 1px solid var(--line); text-align: center; font-weight: 850; }

        .breadcrumb-modern {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            color: var(--muted);
            font-size: 13px;
            font-weight: 750;
            margin-bottom: 18px;
        }
        .breadcrumb-modern a { color: var(--brand-dark); text-decoration: none; }


        .sitahu-pagination {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            padding: 14px 16px;
            background: rgba(255,255,255,.92);
            border: 1px solid var(--line);
            border-radius: 22px;
            box-shadow: var(--shadow-xs);
        }
        .sitahu-page-info {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: var(--muted);
            font-size: 13px;
            font-weight: 750;
        }
        .sitahu-page-info strong { color: var(--ink); font-weight: 900; }
        .sitahu-page-list {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex-wrap: wrap;
            gap: 7px;
            padding: 0;
            margin: 0;
            list-style: none;
        }
        .sitahu-page-link {
            min-width: 38px;
            height: 38px;
            padding: 0 12px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: #fff;
            color: var(--ink);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 13px;
            font-weight: 900;
            transition: .18s ease;
            box-shadow: var(--shadow-xs);
        }
        .sitahu-page-link:hover {
            color: var(--brand-dark);
            border-color: rgba(200,147,53,.35);
            background: var(--brand-soft);
            transform: translateY(-1px);
        }
        .sitahu-page-item.active .sitahu-page-link {
            color: #fff;
            border-color: transparent;
            background: linear-gradient(135deg, var(--brand-color), var(--brand-hover));
            box-shadow: 0 10px 22px rgba(200,147,53,.24);
        }
        .sitahu-page-item.disabled .sitahu-page-link {
            color: var(--muted-2);
            background: #f9fafb;
            box-shadow: none;
            cursor: not-allowed;
            transform: none;
        }
        .sitahu-page-link.dots {
            border-color: transparent;
            background: transparent;
            box-shadow: none;
            min-width: 24px;
            padding: 0 4px;
        }
        .sitahu-toast-container { z-index: 1085; }
        .sitahu-toast {
            min-width: min(360px, calc(100vw - 32px));
            border: 1px solid var(--line) !important;
            border-radius: 18px;
            overflow: hidden;
            background: rgba(255,255,255,.96);
            box-shadow: 0 18px 50px rgba(16,24,40,.16);
            backdrop-filter: blur(12px);
        }
        .sitahu-toast-icon {
            width: 38px;
            height: 38px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            flex: 0 0 auto;
        }
        .sitahu-toast.success .sitahu-toast-icon { color: #15803d; background: #dcfce7; }
        .sitahu-toast.error .sitahu-toast-icon { color: #b91c1c; background: #fee2e2; }
        .sitahu-toast.warning .sitahu-toast-icon { color: #a16207; background: #fef3c7; }
        .sitahu-toast.info .sitahu-toast-icon { color: var(--brand-dark); background: var(--brand-soft); }
        .sitahu-toast-title { color: var(--ink); font-weight: 900; font-size: 14px; margin-bottom: 2px; }
        .sitahu-toast-message { color: var(--muted); font-size: 13px; font-weight: 650; line-height: 1.45; }
        @media (max-width: 575.98px) {
            .sitahu-pagination { justify-content: center; text-align: center; }
            .sitahu-page-info { width: 100%; justify-content: center; }
            .sitahu-page-list { width: 100%; justify-content: center; }
            .sitahu-page-link { min-width: 36px; height: 36px; padding: 0 10px; }
        }

        .alert-shop {
            border: 0;
            border-radius: 16px;
            box-shadow: var(--shadow-xs);
            font-weight: 650;
        }
        .footer-shop {
            background: #fff;
            border-top: 1px solid var(--line);
            margin-top: auto;
            padding: 4.5rem 0 2rem;
        }
        .footer-title { font-weight: 900; letter-spacing: -.02em; margin-bottom: 1.1rem; }
        .footer-link { display: block; text-decoration: none; color: var(--muted); font-size: 14px; font-weight: 650; margin-bottom: .75rem; }
        .footer-link:hover { color: var(--brand-dark); }
        .contact-line { display: flex; gap: 12px; color: var(--muted); font-size: 14px; line-height: 1.55; }
        .contact-line i { color: var(--brand-color); }

        .mini-cart-wrap {
            position: relative;
        }
        .mini-cart-panel {
            position: absolute;
            right: 0;
            top: calc(100% + 14px);
            width: 470px;
            max-width: min(470px, calc(100vw - 24px));
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 20px;
            box-shadow: 0 28px 70px rgba(16, 24, 40, .16);
            z-index: 1070;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transform: translateY(10px);
            transition: opacity .18s ease, transform .18s ease, visibility .18s ease;
            overflow: hidden;
        }
        .mini-cart-wrap:hover .mini-cart-panel,
        .mini-cart-wrap:focus-within .mini-cart-panel {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
            transform: translateY(0);
        }
        .mini-cart-panel::before {
            content: "";
            position: absolute;
            top: -9px;
            right: 23px;
            width: 18px;
            height: 18px;
            background: #fff;
            border-left: 1px solid var(--line);
            border-top: 1px solid var(--line);
            transform: rotate(45deg);
        }
        .mini-cart-inner {
            position: relative;
            background: #fff;
            z-index: 1;
        }
        .mini-cart-head {
            padding: 18px 18px 10px;
            color: var(--muted);
            font-size: 14px;
            font-weight: 800;
        }
        .mini-cart-list {
            max-height: 360px;
            overflow-y: auto;
            padding: 0 10px 8px;
        }
        .mini-cart-item {
            display: grid;
            grid-template-columns: 58px minmax(0, 1fr) auto;
            align-items: center;
            gap: 12px;
            padding: 10px 8px;
            border-radius: 16px;
            text-decoration: none;
            transition: background .18s ease;
        }
        .mini-cart-item:hover {
            background: var(--brand-soft);
        }
        .mini-cart-img {
            width: 58px;
            height: 58px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--brand-soft), #fff);
            border: 1px solid rgba(200, 147, 53, .18);
            display: grid;
            place-items: center;
            overflow: hidden;
            color: var(--brand-dark);
            font-weight: 900;
            font-size: 18px;
        }
        .mini-cart-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .mini-cart-title {
            color: var(--ink);
            font-size: 14px;
            font-weight: 850;
            line-height: 1.35;
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .mini-cart-meta {
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
        }
        .mini-cart-price {
            color: var(--brand-dark);
            font-weight: 900;
            font-size: 14px;
            white-space: nowrap;
            text-align: right;
        }
        .mini-cart-footer {
            border-top: 1px solid var(--line);
            padding: 14px 18px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            background: linear-gradient(180deg, #fff, #fffaf0);
        }
        .mini-cart-empty {
            padding: 26px 18px 28px;
            text-align: center;
        }
        .mini-cart-empty-icon {
            width: 54px;
            height: 54px;
            border-radius: 18px;
            display: grid;
            place-items: center;
            margin: 0 auto 12px;
            color: var(--brand-dark);
            background: var(--brand-soft);
            font-size: 24px;
        }


        .auth-modal .modal-dialog { max-width: 960px; }
        .auth-modal .modal-content { border: 0; border-radius: 32px; overflow: hidden; box-shadow: 0 30px 90px rgba(16,24,40,.24); }
        .auth-modal-hero { min-height: 100%; color: #fff; padding: 34px; background: radial-gradient(circle at 18% 12%, rgba(255,255,255,.22), transparent 15rem), linear-gradient(135deg, var(--brand-color), var(--brand-dark)); position: relative; overflow: hidden; }
        .auth-modal-hero.dark { background: radial-gradient(circle at 82% 12%, rgba(255,255,255,.20), transparent 15rem), linear-gradient(135deg, #182230, var(--brand-dark)); }
        .auth-modal-hero::after { content: ""; position: absolute; width: 260px; height: 260px; right: -120px; bottom: -120px; border-radius: 50%; background: rgba(255,255,255,.12); }
        .auth-modal-form { padding: 34px; background: #fff; }
        .auth-modal-field { min-height: 50px; border-radius: 16px; border-color: var(--line); font-weight: 700; }
        .auth-modal-field:focus { border-color: rgba(200,147,53,.55); box-shadow: 0 0 0 .25rem rgba(200,147,53,.12); }
        .auth-modal-benefit { display: flex; gap: 12px; align-items: flex-start; margin-top: 18px; position: relative; z-index: 1; }
        .auth-modal-benefit i { width: 38px; height: 38px; border-radius: 14px; display: grid; place-items: center; background: rgba(255,255,255,.16); flex: 0 0 auto; }
        @media (max-width: 767.98px) { .auth-modal-hero { display:none; } .auth-modal-form { padding: 24px; } }

        .dropdown-menu { border: 1px solid var(--line); border-radius: 18px; box-shadow: var(--shadow-md); padding: 10px; }
        .dropdown-item { border-radius: 12px; padding: 10px 12px; font-weight: 700; font-size: 14px; }
        .dropdown-item:hover { background: var(--brand-soft); color: var(--brand-dark); }

        @media (max-width: 991.98px) {

            .mini-cart-panel { display: none; }
            .header-search { width: 100%; margin-top: 14px; }
            .navbar-shop .navbar-collapse { padding-top: 10px; }
            .main-content { padding-bottom: 2rem; }
        }
        @media (max-width: 575.98px) {
            .brand-subtitle { display: none; }
            .brand-mark { width: 40px; height: 40px; border-radius: 14px; }
            .section-heading { letter-spacing: -.03em; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="top-strip py-2 d-none d-lg-block">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <span><i class="bi bi-shield-check me-1"></i> Produk segar, higienis, dan siap dipesan online</span>
                <span class="divider">|</span>
                <span><i class="bi bi-truck me-1"></i> Area layanan: {{ $pengaturanLayout->area_pengiriman ?: 'sekitar toko' }}</span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span><i class="bi bi-clock me-1"></i> Buka {{ $jamOperasionalLayout ?: 'setiap hari' }}</span>
                <span><i class="bi bi-telephone me-1"></i> {{ $teleponLayout }}</span>
            </div>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg navbar-shop sticky-top py-2">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 text-decoration-none me-3" href="{{ route('pembeli-web.home') }}">
                <span class="brand-mark">
                    @if($pengaturanLayout->logo_url)
                        <img src="{{ asset('storage/' . $pengaturanLayout->logo_url) }}" alt="{{ $namaTokoLayout }}">
                    @else
                        ST
                    @endif
                </span>
                <span class="d-flex flex-column">
                    <span class="brand-title fs-5">{{ $namaTokoLayout }}</span>
                    <span class="brand-subtitle">Tahu segar pilihan</span>
                </span>
            </a>

            <button class="navbar-toggler border-0 shadow-none ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarPembeli" aria-controls="navbarPembeli" aria-expanded="false" aria-label="Buka navigasi">
                <i class="bi bi-list fs-1"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarPembeli">
                <ul class="navbar-nav align-items-lg-center gap-lg-1 me-lg-3 mt-3 mt-lg-0">
                    <li class="nav-item"><a class="nav-link nav-pill {{ request()->routeIs('pembeli-web.home') ? 'active' : '' }}" href="{{ route('pembeli-web.home') }}">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link nav-pill {{ request()->routeIs('pembeli-web.produk') || request()->routeIs('pembeli-web.produk.detail') ? 'active' : '' }}" href="{{ route('pembeli-web.produk') }}">Produk</a></li>
                    @auth
                        @if(auth()->user()->role === 'pembeli')
                            <li class="nav-item"><a class="nav-link nav-pill {{ request()->routeIs('pembeli-web.pesanan.*') ? 'active' : '' }}" href="{{ route('pembeli-web.pesanan.index') }}">Pesanan</a></li>
                        @endif
                    @endauth
                </ul>

                <form class="header-search mx-lg-auto" action="{{ route('pembeli-web.produk') }}" method="GET">
                    <i class="bi bi-search search-icon"></i>
                    <input type="search" class="form-control" name="search" value="{{ request('search') }}" placeholder="Cari tahu putih, tahu kuning, paket hemat..." aria-label="Cari produk">
                    <button class="search-btn" type="submit">Cari</button>
                </form>

                <div class="d-flex align-items-center justify-content-lg-end gap-2 ms-lg-3 mt-3 mt-lg-0">
                    <div class="mini-cart-wrap js-mini-cart-wrap">
                        <a href="{{ route('pembeli-web.keranjang.index') }}" class="header-action" aria-label="Keranjang">
                            <i class="bi bi-bag fs-5"></i>
                            @if($keranjangTotalLayout > 0)
                                <span class="cart-badge js-cart-badge">{{ $keranjangTotalLayout > 99 ? '99+' : $keranjangTotalLayout }}</span>
                            @else
                                <span class="cart-badge js-cart-badge d-none">0</span>
                            @endif
                        </a>
                        <div class="mini-cart-panel js-mini-cart-panel">
                            @include('pembeli.partials.mini-cart-dropdown', [
                                'miniCartItems' => $keranjangPreviewLayout,
                                'miniCartTotalItem' => $keranjangTotalLayout,
                                'miniCartTotalBelanja' => $keranjangTotalBelanjaLayout,
                            ])
                        </div>
                    </div>

                    @auth
                        @if(auth()->user()->role === 'pembeli')
                            <div class="dropdown">
                                <a href="#" class="header-action" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Akun">
                                    <i class="bi bi-person fs-5"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end mt-2">
                                    <li class="px-2 py-2">
                                        <div class="small text-muted fw-bold">Masuk sebagai</div>
                                        <div class="fw-black fw-bold text-dark">{{ Str::limit(auth()->user()->name, 22) }}</div>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('pembeli-web.profil') }}"><i class="bi bi-person-circle me-2"></i> Profil Saya</a></li>
                                    <li><a class="dropdown-item" href="{{ route('pembeli-web.alamat.index') }}"><i class="bi bi-geo-alt me-2"></i> Alamat Pengiriman</a></li>
                                    <li><a class="dropdown-item" href="{{ route('pembeli-web.pesanan.index') }}"><i class="bi bi-receipt me-2"></i> Pesanan Saya</a></li>
                                    <li>
                                        <form action="{{ route('pembeli-web.logout') }}" method="POST">
                                            @csrf
                                            <button class="dropdown-item text-danger" type="submit"><i class="bi bi-box-arrow-right me-2"></i> Keluar</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @else
                            <span class="badge rounded-pill text-bg-warning fw-bold px-3 py-2">Admin Mode</span>
                            <form action="{{ route('logout') }}" method="POST">@csrf<button class="btn btn-plain btn-sm px-3" type="submit">Keluar</button></form>
                        @endif
                    @else
                        <button type="button" class="btn btn-plain px-3" data-bs-toggle="modal" data-bs-target="#loginModal">Masuk</button>
                        <button type="button" class="btn btn-brand px-3 px-md-4" data-bs-toggle="modal" data-bs-target="#registerModal">Daftar</button>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="main-content">
        @yield('content')
    </main>

    <footer class="footer-shop">
        <div class="container">
            <div class="row g-4 g-lg-5">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="brand-mark">@if($pengaturanLayout->logo_url)<img src="{{ asset('storage/' . $pengaturanLayout->logo_url) }}" alt="{{ $namaTokoLayout }}">@else ST @endif</span>
                        <div>
                            <div class="brand-title fs-4">{{ $namaTokoLayout }}</div>
                            <div class="brand-subtitle">Tahu segar untuk rumah, usaha, dan acara</div>
                        </div>
                    </div>
                    <p class="section-subtitle mb-4">{{ $pengaturanLayout->tentang ?: 'Kami menghadirkan produk tahu segar dengan proses produksi yang higienis, rasa yang konsisten, dan pemesanan online yang praktis.' }}</p>
                    <div class="d-flex gap-2">
                        @if($waLayout)
                            <a class="header-action" href="https://wa.me/{{ $waLayout }}" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                        @endif
                        <a class="header-action" href="{{ route('pembeli-web.produk') }}" aria-label="Produk"><i class="bi bi-shop"></i></a>
                        <a class="header-action" href="{{ route('pembeli-web.keranjang.index') }}" aria-label="Keranjang"><i class="bi bi-bag"></i></a>
                    </div>
                </div>
                <div class="col-6 col-lg-2">
                    <h6 class="footer-title">Belanja</h6>
                    <a class="footer-link" href="{{ route('pembeli-web.home') }}">Beranda</a>
                    <a class="footer-link" href="{{ route('pembeli-web.produk') }}">Katalog Produk</a>
                    <a class="footer-link" href="{{ route('pembeli-web.keranjang.index') }}">Keranjang</a>
                    @auth
                        @if(auth()->user()->role === 'pembeli')<a class="footer-link" href="{{ route('pembeli-web.pesanan.index') }}">Lacak Pesanan</a>@endif
                    @endauth
                </div>
                <div class="col-6 col-lg-2">
                    <h6 class="footer-title">Akun</h6>
                    @auth
                        @if(auth()->user()->role === 'pembeli')
                            <a class="footer-link" href="{{ route('pembeli-web.profil') }}">Profil Saya</a>
                            <a class="footer-link" href="{{ route('pembeli-web.alamat.index') }}">Alamat Saya</a>
                        @endif
                    @else
                        <button type="button" class="footer-link bg-transparent border-0 p-0 text-start" data-bs-toggle="modal" data-bs-target="#loginModal">Masuk</button>
                        <button type="button" class="footer-link bg-transparent border-0 p-0 text-start" data-bs-toggle="modal" data-bs-target="#registerModal">Buat Akun</button>
                    @endauth
                    <a class="footer-link" href="{{ route('pembeli-web.produk', ['stok' => 'tersedia']) }}">Produk Tersedia</a>
                </div>
                <div class="col-lg-4">
                    <h6 class="footer-title">Informasi Toko</h6>
                    <div class="d-grid gap-3">
                        <div class="contact-line"><i class="bi bi-geo-alt-fill"></i><span>{{ $alamatTokoLayout }}</span></div>
                        <div class="contact-line"><i class="bi bi-clock-fill"></i><span>Buka {{ $jamOperasionalLayout ?: 'setiap hari' }}</span></div>
                        <div class="contact-line"><i class="bi bi-telephone-fill"></i><span>{{ $teleponLayout }}</span></div>
                        <div class="contact-line"><i class="bi bi-truck"></i><span>{{ $pengaturanLayout->area_pengiriman ?: 'Pengiriman tersedia di area sekitar toko.' }}</span></div>
                    </div>
                </div>
            </div>
            <div class="border-top mt-5 pt-4 d-flex flex-column flex-md-row justify-content-between gap-2 text-muted small fw-semibold">
                <span>&copy; {{ date('Y') }} {{ $namaTokoLayout }}. Semua hak cipta dilindungi.</span>
                <span>Dibuat untuk pengalaman belanja yang cepat, jelas, dan terpercaya.</span>
            </div>
        </div>
    </footer>


    @guest
        <div class="modal fade auth-modal" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="row g-0">
                        <div class="col-md-5">
                            <div class="auth-modal-hero h-100 d-flex flex-column justify-content-between">
                                <div class="position-relative" style="z-index:1;">
                                    <span class="badge rounded-pill mb-3" style="background:rgba(255,255,255,.16); border:1px solid rgba(255,255,255,.18);">Akun pembeli</span>
                                    <h2 class="section-heading text-white h1 mb-3" id="loginModalLabel">Masuk untuk lanjut belanja.</h2>
                                    <p class="mb-0" style="color:rgba(255,255,255,.78); line-height:1.75;">Keranjang yang sudah Anda isi sebelum login akan otomatis masuk ke akun setelah berhasil masuk.</p>
                                </div>
                                <div>
                                    <div class="auth-modal-benefit"><i class="bi bi-bag-check"></i><div><div class="fw-bold">Keranjang aman</div><small style="color:rgba(255,255,255,.72);">Produk pilihan tetap tersimpan.</small></div></div>
                                    <div class="auth-modal-benefit"><i class="bi bi-receipt"></i><div><div class="fw-bold">Pantau pesanan</div><small style="color:rgba(255,255,255,.72);">Lihat status pembayaran dan pengiriman.</small></div></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="auth-modal-form">
                                <div class="d-flex justify-content-between gap-3 mb-4">
                                    <div>
                                        <h3 class="section-heading h2 mb-1">Masuk</h3>
                                        <p class="text-muted fw-semibold mb-0">Gunakan email atau nomor HP pembeli.</p>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                </div>

                                <form action="{{ route('pembeli-web.login.post') }}" method="POST" class="d-grid gap-3">
                                    @csrf
                                    <div>
                                        <label for="modal_login" class="form-label fw-bold">Email atau Nomor HP</label>
                                        <input id="modal_login" type="text" name="login" value="{{ old('login') }}" class="form-control auth-modal-field {{ $authModalLayout === 'login' && $errors->has('login') ? 'is-invalid' : '' }}" placeholder="email@email.com atau 08123456789" required>
                                    </div>
                                    <div>
                                        <label for="modal_password" class="form-label fw-bold">Password</label>
                                        <input id="modal_password" type="password" name="password" class="form-control auth-modal-field {{ $authModalLayout === 'login' && $errors->has('password') ? 'is-invalid' : '' }}" placeholder="Masukkan password" required>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center gap-3">
                                        <label class="form-check-label small fw-semibold text-muted"><input class="form-check-input me-1" type="checkbox" name="remember" value="1"> Ingat saya</label>
                                        <a href="{{ route('pembeli-web.produk') }}" class="small fw-bold text-brand text-decoration-none">Belanja dulu</a>
                                    </div>
                                    <button type="submit" class="btn btn-brand py-3"><i class="bi bi-box-arrow-in-right me-2"></i> Masuk</button>
                                </form>

                                <div class="surface p-3 mt-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                                    <span class="text-muted fw-semibold">Belum punya akun?</span>
                                    <button type="button" class="btn btn-soft-brand px-4" data-bs-target="#registerModal" data-bs-toggle="modal">Daftar Sekarang</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade auth-modal" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="row g-0">
                        <div class="col-md-5 order-md-2">
                            <div class="auth-modal-hero dark h-100 d-flex flex-column justify-content-between">
                                <div class="position-relative" style="z-index:1;">
                                    <span class="badge rounded-pill mb-3" style="background:rgba(255,255,255,.16); border:1px solid rgba(255,255,255,.18);">Daftar pembeli</span>
                                    <h2 class="section-heading text-white h1 mb-3" id="registerModalLabel">Buat akun belanja.</h2>
                                    <p class="mb-0" style="color:rgba(255,255,255,.78); line-height:1.75;">Setelah daftar, Anda langsung masuk dan bisa lanjut checkout dari keranjang.</p>
                                </div>
                                <div>
                                    <div class="auth-modal-benefit"><i class="bi bi-person-check"></i><div><div class="fw-bold">Checkout cepat</div><small style="color:rgba(255,255,255,.72);">Data kontak tersimpan untuk pesanan berikutnya.</small></div></div>
                                    <div class="auth-modal-benefit"><i class="bi bi-star"></i><div><div class="fw-bold">Beri ulasan</div><small style="color:rgba(255,255,255,.72);">Bagikan foto/video setelah pesanan selesai.</small></div></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7 order-md-1">
                            <div class="auth-modal-form">
                                <div class="d-flex justify-content-between gap-3 mb-4">
                                    <div>
                                        <h3 class="section-heading h2 mb-1">Daftar</h3>
                                        <p class="text-muted fw-semibold mb-0">Isi data pembeli dengan benar.</p>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                </div>

                                <form action="{{ route('pembeli-web.register.post') }}" method="POST" class="d-grid gap-3">
                                    @csrf
                                    <div>
                                        <label for="modal_name" class="form-label fw-bold">Nama lengkap</label>
                                        <input id="modal_name" type="text" name="name" value="{{ old('name') }}" class="form-control auth-modal-field {{ $authModalLayout === 'register' && $errors->has('name') ? 'is-invalid' : '' }}" placeholder="Nama pembeli" required>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="modal_email" class="form-label fw-bold">Email</label>
                                            <input id="modal_email" type="email" name="email" value="{{ old('email') }}" class="form-control auth-modal-field {{ $authModalLayout === 'register' && $errors->has('email') ? 'is-invalid' : '' }}" placeholder="nama@email.com" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="modal_telepon" class="form-label fw-bold">Nomor HP</label>
                                            <input id="modal_telepon" type="text" name="telepon" value="{{ old('telepon') }}" class="form-control auth-modal-field {{ $authModalLayout === 'register' && $errors->has('telepon') ? 'is-invalid' : '' }}" placeholder="08123456789" required>
                                        </div>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="modal_register_password" class="form-label fw-bold">Password</label>
                                            <input id="modal_register_password" type="password" name="password" class="form-control auth-modal-field {{ $authModalLayout === 'register' && $errors->has('password') ? 'is-invalid' : '' }}" placeholder="Minimal 6 karakter" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="modal_password_confirmation" class="form-label fw-bold">Konfirmasi</label>
                                            <input id="modal_password_confirmation" type="password" name="password_confirmation" class="form-control auth-modal-field" placeholder="Ulangi password" required>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-brand py-3"><i class="bi bi-person-plus me-2"></i> Daftar & Masuk</button>
                                </form>

                                <div class="surface p-3 mt-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                                    <span class="text-muted fw-semibold">Sudah punya akun?</span>
                                    <button type="button" class="btn btn-soft-brand px-4" data-bs-target="#loginModal" data-bs-toggle="modal">Masuk Akun</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endguest

    <div class="toast-container position-fixed top-0 end-0 p-3 sitahu-toast-container">
        @if(session('success'))
            <div class="toast sitahu-toast success js-auto-toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-body d-flex align-items-start gap-3 p-3">
                    <span class="sitahu-toast-icon"><i class="bi bi-check-circle-fill"></i></span>
                    <div class="min-w-0 flex-grow-1">
                        <div class="sitahu-toast-title">Berhasil</div>
                        <div class="sitahu-toast-message">{{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close ms-1" data-bs-dismiss="toast" aria-label="Tutup"></button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="toast sitahu-toast error js-auto-toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-body d-flex align-items-start gap-3 p-3">
                    <span class="sitahu-toast-icon"><i class="bi bi-exclamation-triangle-fill"></i></span>
                    <div class="min-w-0 flex-grow-1">
                        <div class="sitahu-toast-title">Belum berhasil</div>
                        <div class="sitahu-toast-message">{{ session('error') }}</div>
                    </div>
                    <button type="button" class="btn-close ms-1" data-bs-dismiss="toast" aria-label="Tutup"></button>
                </div>
            </div>
        @endif

        @if(session('warning'))
            <div class="toast sitahu-toast warning js-auto-toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-body d-flex align-items-start gap-3 p-3">
                    <span class="sitahu-toast-icon"><i class="bi bi-info-circle-fill"></i></span>
                    <div class="min-w-0 flex-grow-1">
                        <div class="sitahu-toast-title">Perhatian</div>
                        <div class="sitahu-toast-message">{{ session('warning') }}</div>
                    </div>
                    <button type="button" class="btn-close ms-1" data-bs-dismiss="toast" aria-label="Tutup"></button>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="toast sitahu-toast error js-auto-toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-body d-flex align-items-start gap-3 p-3">
                    <span class="sitahu-toast-icon"><i class="bi bi-exclamation-triangle-fill"></i></span>
                    <div class="min-w-0 flex-grow-1">
                        <div class="sitahu-toast-title">Periksa input</div>
                        <div class="sitahu-toast-message">{{ $errors->first() }}</div>
                    </div>
                    <button type="button" class="btn-close ms-1" data-bs-dismiss="toast" aria-label="Tutup"></button>
                </div>
            </div>
        @endif

        <div id="cartToast" class="toast sitahu-toast info" role="status" aria-live="polite" aria-atomic="true">
            <div class="toast-body d-flex align-items-start gap-3 p-3">
                <span class="sitahu-toast-icon"><i class="bi bi-bag-check-fill"></i></span>
                <div class="min-w-0 flex-grow-1">
                    <div class="sitahu-toast-title js-toast-title">Keranjang</div>
                    <div class="sitahu-toast-message js-toast-text">Produk berhasil masuk keranjang.</div>
                </div>
                <button type="button" class="btn-close ms-1" data-bs-dismiss="toast" aria-label="Tutup"></button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toastEl = document.getElementById('cartToast');
            const toastTitle = document.querySelector('.js-toast-title');
            const toastText = document.querySelector('.js-toast-text');
            const toast = toastEl ? new bootstrap.Toast(toastEl, { delay: 2400 }) : null;

            document.querySelectorAll('.js-auto-toast').forEach(function (toastNode) {
                new bootstrap.Toast(toastNode, { delay: 4200 }).show();
            });

            window.showSitahuToast = function (message, type = 'info', title = null) {
                if (!toast || !toastEl || !toastText) return;
                toastEl.classList.remove('success', 'error', 'warning', 'info');
                toastEl.classList.add(type);
                if (toastTitle) {
                    toastTitle.textContent = title || (type === 'success' ? 'Berhasil' : type === 'error' ? 'Belum berhasil' : type === 'warning' ? 'Perhatian' : 'Informasi');
                }
                toastText.textContent = message || 'Informasi berhasil diperbarui.';
                toast.show();
            };

            const authModalToOpen = @json($authModalLayout);
            if (authModalToOpen) {
                const modalEl = document.getElementById(authModalToOpen === 'register' ? 'registerModal' : 'loginModal');
                if (modalEl) new bootstrap.Modal(modalEl).show();
            }


            document.querySelectorAll('.js-product-card').forEach(function (card) {
                const openCard = function () {
                    const url = card.getAttribute('data-url');
                    if (url) window.location.href = url;
                };
                card.addEventListener('click', function (event) {
                    if (event.target.closest('a, button, form, input, select, textarea, label')) return;
                    openCard();
                });
                card.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter') openCard();
                });
            });

            document.querySelectorAll('.js-add-cart-form').forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!window.fetch) return;
                    event.preventDefault();
                    const button = form.querySelector('button[type="submit"]');
                    const original = button ? button.innerHTML : '';
                    if (button) { button.disabled = true; button.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menambah'; }

                    fetch(form.action, {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                        body: new FormData(form)
                    })
                    .then(async response => {
                        const payload = await response.json().catch(() => ({}));
                        if (!response.ok) throw payload;
                        return payload;
                    })
                    .then(payload => {
                        document.querySelectorAll('.js-cart-badge').forEach(function (badge) {
                            const total = payload.total_item || 0;
                            badge.textContent = total > 99 ? '99+' : total;
                            badge.classList.toggle('d-none', total <= 0);
                        });
                        if (payload.mini_cart_html) {
                            document.querySelectorAll('.js-mini-cart-panel').forEach(function (panel) {
                                panel.innerHTML = payload.mini_cart_html;
                            });
                        }
                        window.showSitahuToast(payload.message || 'Produk berhasil masuk keranjang.', 'success', 'Keranjang');
                    })
                    .catch(payload => {
                        window.showSitahuToast(payload.message || 'Produk belum bisa ditambahkan.', 'error');
                    })
                    .finally(() => {
                        if (button) { button.disabled = false; button.innerHTML = original; }
                    });
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
