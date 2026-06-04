<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SiTahu Web Pembeli')</title>

    <style>
        :root {
            --brand-color: #dfba68;
            --brand-soft: rgba(223, 186, 104, 0.15);
            --brand-soft-2: #fff8e8;
            --brand-text: #8a6321;

            --bg: #f3f4f6;
            --panel: #ffffff;
            --panel-soft: #fffdf8;

            --text: #374151;
            --heading: #111827;
            --muted: #6b7280;
            --line: #e5e7eb;

            --success: #16a34a;
            --success-soft: #ecfdf5;

            --shadow: 0 10px 28px rgba(17, 24, 39, 0.07);
            --shadow-soft: 0 6px 16px rgba(17, 24, 39, 0.05);

            --radius-lg: 24px;
            --radius-md: 16px;
            --radius-sm: 12px;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background:
                radial-gradient(circle at top left, rgba(223, 186, 104, 0.20), transparent 28%),
                linear-gradient(180deg, #fffdf8 0%, var(--bg) 42%, #ffffff 100%);
            color: var(--text);
            min-height: 100vh;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button,
        input,
        select,
        textarea {
            font: inherit;
        }

        img {
            max-width: 100%;
            display: block;
        }

        .site-shell {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            width: min(1160px, 100%);
            margin: 0 auto;
            padding: 0 22px;
        }

        /* TOP INFO */
        .top-info {
            background: #ffffff;
            border-bottom: 1px solid var(--line);
            color: var(--muted);
            font-size: 13px;
        }

        .top-info-inner {
            min-height: 38px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
        }

        .top-info span {
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }

        .top-info strong {
            color: var(--brand-text);
        }

        /* NAVBAR */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--line);
            box-shadow: 0 4px 14px rgba(17, 24, 39, 0.04);
        }

        .navbar-inner {
            min-height: 72px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
        }

        .brand-link {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }

        .brand-logo {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            color: #ffffff;
            font-weight: 900;
            letter-spacing: -0.03em;
            background: linear-gradient(135deg, var(--brand-color), #c89335);
            box-shadow: 0 10px 22px rgba(223, 186, 104, 0.32);
        }

        .brand-text {
            display: flex;
            flex-direction: column;
            line-height: 1.05;
        }

        .brand-text strong {
            font-size: 20px;
            letter-spacing: -0.04em;
            color: var(--heading);
        }

        .brand-text small {
            margin-top: 4px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
        }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .nav-link {
            padding: 10px 14px;
            border-radius: 12px;
            color: var(--muted);
            font-size: 14px;
            font-weight: 700;
            transition: 0.18s ease;
        }

        .nav-link:hover {
            background: #f3f4f6;
            color: var(--heading);
        }

        .nav-link.active {
            background: var(--brand-soft);
            color: var(--brand-text);
            font-weight: 800;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .cart-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 12px;
            background: var(--brand-soft);
            border: 1px solid rgba(223, 186, 104, 0.45);
            color: var(--brand-text);
            font-weight: 800;
            box-shadow: var(--shadow-soft);
            transition: 0.18s ease;
        }

        .cart-button:hover {
            transform: translateY(-1px);
            background: #fff3d4;
        }

        .menu-toggle {
            display: none;
            width: 42px;
            height: 42px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: var(--panel);
            color: var(--heading);
            cursor: pointer;
            font-size: 22px;
            box-shadow: var(--shadow-soft);
        }

        /* MAIN */
        .main-content {
            flex: 1;
            padding: 26px 0 52px;
        }

        .page-card {
            background: rgba(255, 255, 255, 0.88);
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
        }

        .section-title {
            margin: 0;
            font-size: clamp(28px, 4vw, 44px);
            line-height: 1.04;
            letter-spacing: -0.06em;
            color: var(--heading);
        }

        .section-subtitle {
            margin: 12px 0 0;
            color: var(--muted);
            line-height: 1.75;
            font-size: 15px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            width: fit-content;
            padding: 8px 12px;
            border-radius: 999px;
            background: var(--brand-soft);
            color: var(--brand-text);
            font-weight: 800;
            font-size: 13px;
            border: 1px solid rgba(223, 186, 104, 0.35);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            min-height: 44px;
            padding: 11px 16px;
            border-radius: 12px;
            border: 1px solid transparent;
            cursor: pointer;
            font-weight: 800;
            transition: 0.18s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--brand-color), #c89335);
            color: #ffffff;
            box-shadow: 0 10px 22px rgba(223, 186, 104, 0.30);
        }

        .btn-outline {
            background: #ffffff;
            color: var(--heading);
            border-color: var(--line);
        }

        .btn-whatsapp {
            background: var(--success);
            color: #ffffff;
            box-shadow: 0 10px 22px rgba(22, 163, 74, 0.18);
        }

        .text-muted {
            color: var(--muted);
        }

        .grid {
            display: grid;
            gap: 18px;
        }

        .grid-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .grid-3 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .grid-4 {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        /*
        |--------------------------------------------------------------------------
        | COMPACT PAGE HEADER
        |--------------------------------------------------------------------------
        | Biar halaman selain home tidak punya hero/banner yang kegedean.
        | Home aman karena class home-hero tidak ikut kena.
        */

        .produk-hero,
        .cart-hero,
        .checkout-hero,
        .order-hero,
        .detail-hero,
        .profile-hero,
        .success-hero {
            padding: 18px 22px !important;
            margin-bottom: 18px !important;
            border-radius: 20px !important;
            min-height: auto !important;
        }

        .produk-hero h1,
        .cart-hero h1,
        .checkout-hero h1,
        .order-hero h1,
        .detail-hero h1,
        .profile-hero h1,
        .success-hero h1 {
            margin-top: 8px !important;
            font-size: clamp(24px, 3vw, 34px) !important;
            line-height: 1.12 !important;
            letter-spacing: -0.045em !important;
        }

        .produk-hero p,
        .cart-hero p,
        .checkout-hero p,
        .order-hero p,
        .detail-hero p,
        .profile-hero p,
        .success-hero p {
            margin-top: 8px !important;
            max-width: 760px !important;
            font-size: 14px !important;
            line-height: 1.6 !important;
        }

        .produk-hero .badge,
        .cart-hero .badge,
        .checkout-hero .badge,
        .order-hero .badge,
        .detail-hero .badge,
        .profile-hero .badge,
        .success-hero .badge {
            padding: 6px 10px !important;
            font-size: 12px !important;
        }

        .produk-hero {
            grid-template-columns: 1fr !important;
            gap: 12px !important;
        }

        .produk-hero .hero-note {
            display: none !important;
        }

        .cart-hero,
        .detail-hero {
            align-items: center !important;
        }

        .checkout-hero {
            display: block !important;
        }

        .order-hero {
            display: block !important;
        }

        .profile-hero {
            grid-template-columns: 1fr 220px !important;
            gap: 16px !important;
        }

        .profile-logo-card {
            padding: 14px !important;
            border-radius: 18px !important;
        }

        .profile-logo {
            width: 64px !important;
            height: 64px !important;
            border-radius: 20px !important;
            font-size: 24px !important;
            margin-bottom: 10px !important;
        }

        .success-icon {
            width: 58px !important;
            height: 58px !important;
            border-radius: 18px !important;
            font-size: 28px !important;
            margin-bottom: 12px !important;
        }

        /* FOOTER */
        .footer {
            margin-top: auto;
            background: #ffffff;
            border-top: 1px solid var(--line);
            color: var(--text);
            padding: 38px 0 22px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.25fr 0.75fr 0.85fr;
            gap: 24px;
            align-items: start;
        }

        .footer-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
        }

        .footer h3 {
            margin: 0 0 12px;
            font-size: 15px;
            color: var(--brand-text);
        }

        .footer p,
        .footer a,
        .footer li {
            color: var(--muted);
            font-size: 14px;
            line-height: 1.7;
        }

        .footer ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: grid;
            gap: 8px;
        }

        .footer a:hover {
            color: var(--brand-text);
        }

        .footer-bottom {
            margin-top: 26px;
            padding-top: 16px;
            border-top: 1px solid var(--line);
            display: flex;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            color: var(--muted);
            font-size: 13px;
        }

        @media (max-width: 900px) {
            .container {
                padding: 0 16px;
            }

            .top-info-inner {
                justify-content: center;
                text-align: center;
                padding: 8px 0;
            }

            .navbar-inner {
                min-height: 68px;
            }

            .menu-toggle {
                display: inline-grid;
                place-items: center;
            }

            .nav-menu {
                position: absolute;
                left: 16px;
                right: 16px;
                top: calc(100% + 10px);
                display: none;
                flex-direction: column;
                align-items: stretch;
                padding: 12px;
                border-radius: 18px;
                background: #ffffff;
                border: 1px solid var(--line);
                box-shadow: var(--shadow);
            }

            .nav-menu.open {
                display: flex;
            }

            .nav-link {
                border-radius: 12px;
                text-align: left;
                padding: 13px 14px;
            }

            .cart-button span:last-child {
                display: none;
            }

            .main-content {
                padding-top: 20px;
            }

            .grid-2,
            .grid-3,
            .grid-4 {
                grid-template-columns: 1fr;
            }

            .footer-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 700px) {
            .produk-hero,
            .cart-hero,
            .checkout-hero,
            .order-hero,
            .detail-hero,
            .profile-hero,
            .success-hero {
                padding: 16px !important;
            }

            .profile-hero {
                grid-template-columns: 1fr !important;
            }

            .profile-logo-card {
                display: none !important;
            }
        }

        @media (max-width: 520px) {
            .brand-text small {
                display: none;
            }

            .brand-logo {
                width: 42px;
                height: 42px;
                border-radius: 13px;
            }

            .cart-button {
                padding: 10px 12px;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
<div class="site-shell">

    <div class="top-info">
        <div class="container top-info-inner">
            <span>Produk tahu segar siap dipesan</span>
            <span><strong>SiTahu</strong> — Web Pembeli</span>
            <span>Ambil di toko / Kurir toko</span>
        </div>
    </div>

    <header class="navbar">
        <div class="container navbar-inner">

            <a href="{{ route('pembeli-web.home') }}" class="brand-link">
                <div class="brand-logo">ST</div>
                <div class="brand-text">
                    <strong>SiTahu</strong>
                    <small>Etalase Produk Tahu</small>
                </div>
            </a>

            <nav class="nav-menu" id="navMenu">
                <a href="{{ route('pembeli-web.home') }}"
                   class="nav-link {{ request()->routeIs('pembeli-web.home') ? 'active' : '' }}">
                    Beranda
                </a>

                <a href="{{ route('pembeli-web.produk') }}"
                   class="nav-link {{ request()->routeIs('pembeli-web.produk') || request()->routeIs('pembeli-web.produk.detail') ? 'active' : '' }}">
                    Produk
                </a>

                <a href="{{ route('pembeli-web.keranjang.index') }}"
                   class="nav-link {{ request()->routeIs('pembeli-web.keranjang.*') ? 'active' : '' }}">
                    Keranjang
                </a>

                <a href="{{ route('pembeli-web.pesanan.index') }}"
                   class="nav-link {{ request()->routeIs('pembeli-web.pesanan.*') || request()->routeIs('pembeli-web.checkout.*') ? 'active' : '' }}">
                    Pesanan
                </a>

                <a href="{{ route('pembeli-web.profil') }}"
                   class="nav-link {{ request()->routeIs('pembeli-web.profil') ? 'active' : '' }}">
                    Profil
                </a>
            </nav>

            <div class="nav-actions">
                <a href="{{ route('pembeli-web.keranjang.index') }}" class="cart-button">
                    🛒 <span>Keranjang</span>
                </a>

                <button type="button" class="menu-toggle" id="menuToggle">
                    ☰
                </button>
            </div>

        </div>
    </header>

    <main class="main-content">
        <div class="container">
            @yield('content')
        </div>
    </main>

    <footer class="footer">
        <div class="container">

            <div class="footer-grid">
                <div>
                    <div class="footer-brand">
                        <div class="brand-logo">ST</div>
                        <div>
                            <strong style="font-size: 20px; color: var(--heading);">SiTahu</strong>
                            <p style="margin: 2px 0 0;">Aplikasi penjualan produk tahu berbasis web dan mobile.</p>
                        </div>
                    </div>

                    <p>
                        Web pembeli ini dibuat sebagai opsi belanja lewat browser.
                        Tampilan dibuat lebih ringan dari admin, tapi tetap memakai identitas warna SiTahu.
                    </p>
                </div>

                <div>
                    <h3>Menu Pembeli</h3>
                    <ul>
                        <li><a href="{{ route('pembeli-web.home') }}">Beranda</a></li>
                        <li><a href="{{ route('pembeli-web.produk') }}">Produk Tahu</a></li>
                        <li><a href="{{ route('pembeli-web.keranjang.index') }}">Keranjang</a></li>
                        <li><a href="{{ route('pembeli-web.pesanan.index') }}">Pesanan</a></li>
                        <li><a href="{{ route('pembeli-web.profil') }}">Profil Toko</a></li>
                    </ul>
                </div>

                <div>
                    <h3>Info Toko</h3>
                    <ul>
                        <li>Jam operasional mengikuti pengaturan toko</li>
                        <li>Ambil di toko atau kurir toko</li>
                        <li>Pembayaran tersedia melalui QRIS atau tunai</li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <span>© {{ date('Y') }} SiTahu. Web Pembeli Laravel.</span>
                <span>Senada admin, tapi lebih ramah pembeli.</span>
            </div>

        </div>
    </footer>

</div>

<script>
    const menuToggle = document.getElementById('menuToggle');
    const navMenu = document.getElementById('navMenu');

    if (menuToggle && navMenu) {
        menuToggle.addEventListener('click', function () {
            navMenu.classList.toggle('open');
            menuToggle.textContent = navMenu.classList.contains('open') ? '×' : '☰';
        });

        document.addEventListener('click', function (event) {
            const clickInsideMenu = navMenu.contains(event.target);
            const clickToggle = menuToggle.contains(event.target);

            if (!clickInsideMenu && !clickToggle) {
                navMenu.classList.remove('open');
                menuToggle.textContent = '☰';
            }
        });
    }
</script>

@stack('scripts')
</body>
</html>
