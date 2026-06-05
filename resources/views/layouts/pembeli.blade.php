@php
    use Illuminate\Support\Str;
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SiTahu Web Pembeli')</title>

    <style>
        :root {
            --brand-color: #dfba68;
            --brand-dark: #8a6321;
            --brand-soft: #fff4d6;
            --brand-soft-2: #fff8e8;

            --bg: #f6f7fb;
            --panel: #ffffff;
            --panel-soft: #fffdf8;

            --heading: #111827;
            --text: #374151;
            --muted: #6b7280;
            --line: #e5e7eb;

            --success: #16a34a;
            --success-soft: #ecfdf5;
            --danger: #b91c1c;
            --danger-soft: #fef2f2;

            --shadow: 0 14px 36px rgba(17, 24, 39, 0.07);
            --shadow-soft: 0 8px 20px rgba(17, 24, 39, 0.05);

            --radius-xl: 26px;
            --radius-lg: 20px;
            --radius-md: 15px;
            --radius-sm: 12px;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            min-height: 100vh;
            margin: 0;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(223, 186, 104, 0.18), transparent 30%),
                linear-gradient(180deg, #fffdf8 0%, var(--bg) 44%, #ffffff 100%);
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
            width: min(1180px, 100%);
            margin: 0 auto;
            padding: 0 22px;
        }

        .top-info {
            background: #ffffff;
            border-bottom: 1px solid var(--line);
            color: var(--muted);
            font-size: 12.5px;
        }

        .top-info-inner {
            min-height: 36px;
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
            white-space: nowrap;
        }

        .top-info strong {
            color: var(--brand-dark);
        }

        .navbar {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--line);
            box-shadow: 0 6px 20px rgba(17, 24, 39, 0.04);
        }

        .navbar-inner {
            min-height: 70px;
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 18px;
            align-items: center;
        }

        .brand-link {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .brand-logo {
            width: 44px;
            height: 44px;
            border-radius: 15px;
            display: grid;
            place-items: center;
            color: #ffffff;
            font-weight: 950;
            letter-spacing: -0.04em;
            background: linear-gradient(135deg, var(--brand-color), #c89335);
            box-shadow: 0 12px 24px rgba(223, 186, 104, 0.30);
            flex: 0 0 auto;
        }

        .brand-text {
            min-width: 0;
            display: flex;
            flex-direction: column;
            line-height: 1.05;
        }

        .brand-text strong {
            color: var(--heading);
            font-size: 20px;
            font-weight: 950;
            letter-spacing: -0.045em;
        }

        .brand-text small {
            margin-top: 4px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 750;
            white-space: nowrap;
        }

        .nav-menu {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            min-width: 0;
        }

        .nav-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 38px;
            padding: 9px 13px;
            border-radius: 999px;
            color: var(--muted);
            font-size: 13.5px;
            font-weight: 800;
            transition: 0.16s ease;
            white-space: nowrap;
        }

        .nav-link:hover {
            background: #f9fafb;
            color: var(--heading);
        }

        .nav-link.active {
            background: var(--brand-soft);
            color: var(--brand-dark);
            box-shadow: 0 8px 18px rgba(223, 186, 104, 0.16);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
        }

        .cart-button {
            min-height: 39px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 9px 13px;
            border-radius: 999px;
            border: 1px solid rgba(223, 186, 104, 0.45);
            background: var(--brand-soft);
            color: var(--brand-dark);
            font-weight: 900;
            font-size: 13px;
            box-shadow: var(--shadow-soft);
            cursor: pointer;
            transition: 0.16s ease;
            white-space: nowrap;
        }

        .cart-button:hover {
            transform: translateY(-1px);
            background: #fff0c7;
        }

        .cart-button.light {
            background: #ffffff;
            border-color: var(--line);
            color: var(--heading);
            box-shadow: none;
        }

        .menu-toggle {
            display: none;
            width: 42px;
            height: 42px;
            border: 1px solid var(--line);
            border-radius: 14px;
            background: #ffffff;
            color: var(--heading);
            cursor: pointer;
            font-size: 22px;
            box-shadow: var(--shadow-soft);
        }

        .main-content {
            flex: 1;
            padding: 24px 0 46px;
        }

        .page-card {
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid var(--line);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow);
        }

        .section-title {
            margin: 0;
            color: var(--heading);
            font-size: clamp(26px, 3.5vw, 40px);
            line-height: 1.05;
            letter-spacing: -0.06em;
        }

        .section-subtitle {
            margin: 10px 0 0;
            color: var(--muted);
            line-height: 1.7;
            font-size: 14px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            width: fit-content;
            padding: 7px 11px;
            border-radius: 999px;
            background: var(--brand-soft);
            color: var(--brand-dark);
            font-weight: 900;
            font-size: 12px;
            border: 1px solid rgba(223, 186, 104, 0.38);
        }

        .btn {
            min-height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 15px;
            border-radius: 999px;
            border: 1px solid transparent;
            cursor: pointer;
            font-weight: 900;
            font-size: 13.5px;
            transition: 0.16s ease;
            text-align: center;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--brand-color), #c89335);
            color: #ffffff;
            box-shadow: 0 10px 22px rgba(223, 186, 104, 0.28);
        }

        .btn-outline {
            background: #ffffff;
            color: var(--heading);
            border-color: var(--line);
        }

        .btn-outline:hover {
            border-color: rgba(223, 186, 104, 0.65);
            color: var(--brand-dark);
            box-shadow: var(--shadow-soft);
        }

        .btn-whatsapp {
            background: var(--success);
            color: #ffffff;
            box-shadow: 0 10px 22px rgba(22, 163, 74, 0.18);
        }

        .btn-disabled {
            background: #f3f4f6 !important;
            color: #9ca3af !important;
            border-color: #e5e7eb !important;
            cursor: not-allowed !important;
            box-shadow: none !important;
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

        .produk-hero,
        .cart-hero,
        .checkout-hero,
        .order-hero,
        .detail-hero,
        .profile-hero,
        .success-hero {
            padding: 20px 24px !important;
            margin-bottom: 18px !important;
            border-radius: 22px !important;
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
            color: var(--heading);
            font-size: clamp(25px, 3.1vw, 36px) !important;
            line-height: 1.1 !important;
            letter-spacing: -0.055em !important;
        }

        .produk-hero p,
        .cart-hero p,
        .checkout-hero p,
        .order-hero p,
        .detail-hero p,
        .profile-hero p,
        .success-hero p {
            margin-top: 8px !important;
            max-width: 780px !important;
            color: var(--muted);
            font-size: 14px !important;
            line-height: 1.65 !important;
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

        .checkout-hero,
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

        .alert,
        .alert-box {
            border-radius: 16px;
        }

        .footer {
            margin-top: auto;
            background: #ffffff;
            border-top: 1px solid var(--line);
            color: var(--text);
            padding: 34px 0 20px;
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
            color: var(--brand-dark);
            font-size: 15px;
            font-weight: 950;
        }

        .footer p,
        .footer a,
        .footer li {
            color: var(--muted);
            font-size: 13.5px;
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
            color: var(--brand-dark);
        }

        .footer-bottom {
            margin-top: 24px;
            padding-top: 16px;
            border-top: 1px solid var(--line);
            display: flex;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            color: var(--muted);
            font-size: 12.5px;
        }

        @media (max-width: 1020px) {
            .navbar-inner {
                grid-template-columns: auto auto;
            }

            .nav-menu {
                position: absolute;
                left: 18px;
                right: 18px;
                top: calc(100% + 10px);
                display: none;
                flex-direction: column;
                align-items: stretch;
                justify-content: flex-start;
                padding: 12px;
                border-radius: 20px;
                background: #ffffff;
                border: 1px solid var(--line);
                box-shadow: var(--shadow);
            }

            .nav-menu.open {
                display: flex;
            }

            .nav-link {
                justify-content: flex-start;
                border-radius: 14px;
                min-height: 42px;
                padding: 11px 13px;
            }

            .menu-toggle {
                display: inline-grid;
                place-items: center;
            }
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

            .top-info span:first-child,
            .top-info span:last-child {
                display: none;
            }

            .navbar-inner {
                min-height: 66px;
                gap: 12px;
            }

            .main-content {
                padding-top: 18px;
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

            .cart-button span {
                display: none;
            }
        }

        @media (max-width: 540px) {
            .brand-text small {
                display: none;
            }

            .brand-logo {
                width: 42px;
                height: 42px;
                border-radius: 14px;
            }

            .cart-button {
                padding: 9px 11px;
            }

            .footer-bottom {
                flex-direction: column;
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
            <span><strong>SiTahu</strong> — Etalase Web Pembeli</span>
            <span>Ambil di toko / Kurir toko</span>
        </div>
    </div>

    <header class="navbar">
        <div class="container navbar-inner">
            <a href="{{ route('pembeli-web.home') }}" class="brand-link">
                <div class="brand-logo">ST</div>
                <div class="brand-text">
                    <strong>SiTahu</strong>
                    <small>Produk tahu segar</small>
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

                @auth
                    @if(auth()->user()->role === 'pembeli')
                        <a href="{{ route('pembeli-web.keranjang.index') }}"
                           class="nav-link {{ request()->routeIs('pembeli-web.keranjang.*') ? 'active' : '' }}">
                            Keranjang
                        </a>

                        <a href="{{ route('pembeli-web.pesanan.index') }}"
                           class="nav-link {{ request()->routeIs('pembeli-web.pesanan.*') || request()->routeIs('pembeli-web.checkout.*') ? 'active' : '' }}">
                            Pesanan
                        </a>

                        <a href="{{ route('pembeli-web.alamat.index') }}"
                        class="nav-link {{ request()->routeIs('pembeli-web.alamat.*') ? 'active' : '' }}">
                            Alamat
                        </a>

                        <a href="{{ route('pembeli-web.profil') }}"
                        class="nav-link {{ request()->routeIs('pembeli-web.profil') ? 'active' : '' }}">
                            Profil
                        </a>
                    @else
                        <a href="{{ route('pembeli-web.login') }}"
                           class="nav-link {{ request()->routeIs('pembeli-web.login') ? 'active' : '' }}">
                            Login Pembeli
                        </a>
                    @endif
                @else
                    <a href="{{ route('pembeli-web.login') }}"
                       class="nav-link {{ request()->routeIs('pembeli-web.login') ? 'active' : '' }}">
                        Login
                    </a>

                    <a href="{{ route('pembeli-web.register') }}"
                       class="nav-link {{ request()->routeIs('pembeli-web.register') ? 'active' : '' }}">
                        Daftar
                    </a>
                @endauth
            </nav>

            <div class="nav-actions">
                @auth
                    @if(auth()->user()->role === 'pembeli')
                        <a href="{{ route('pembeli-web.keranjang.index') }}" class="cart-button">
                            🛒 <span>Keranjang</span>
                        </a>

                        <form action="{{ route('pembeli-web.logout') }}" method="POST" style="margin:0;">
                            @csrf
                            <button type="submit" class="cart-button light">
                                👤 <span>{{ Str::limit(auth()->user()->name, 12) }} / Logout</span>
                            </button>
                        </form>
                    @else
                        <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                            @csrf
                            <button type="submit" class="cart-button light">
                                🔁 <span>Logout Admin</span>
                            </button>
                        </form>

                        <a href="{{ route('pembeli-web.login') }}" class="cart-button">
                            🔐 <span>Login Pembeli</span>
                        </a>
                    @endif
                @else
                    <a href="{{ route('pembeli-web.login') }}" class="cart-button light">
                        🔐 <span>Login</span>
                    </a>

                    <a href="{{ route('pembeli-web.register') }}" class="cart-button">
                        ✨ <span>Daftar</span>
                    </a>
                @endauth

                <button type="button" class="menu-toggle" id="menuToggle" aria-label="Buka menu">
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
                            <strong style="font-size:20px;color:var(--heading);">SiTahu</strong>
                            <p style="margin:2px 0 0;">Aplikasi penjualan produk tahu berbasis web dan mobile.</p>
                        </div>
                    </div>

                    <p>
                        Web pembeli ini dibuat sebagai etalase belanja. Pengunjung bisa melihat produk,
                        sedangkan login diperlukan saat menambahkan keranjang, checkout, dan melihat pesanan.
                    </p>
                </div>

                <div>
                    <h3>Menu Pembeli</h3>
                    <ul>
                        <li><a href="{{ route('pembeli-web.home') }}">Beranda</a></li>
                        <li><a href="{{ route('pembeli-web.produk') }}">Produk Tahu</a></li>

                        @auth
                            @if(auth()->user()->role === 'pembeli')
                                <li><a href="{{ route('pembeli-web.keranjang.index') }}">Keranjang</a></li>
                                <li><a href="{{ route('pembeli-web.pesanan.index') }}">Pesanan Saya</a></li>
                                <li><a href="{{ route('pembeli-web.profil') }}">Profil Pembeli</a></li>
                            @endif
                        @else
                            <li><a href="{{ route('pembeli-web.login') }}">Login Pembeli</a></li>
                            <li><a href="{{ route('pembeli-web.register') }}">Daftar Pembeli</a></li>
                        @endauth
                    </ul>
                </div>

                <div>
                    <h3>Info Toko</h3>
                    <ul>
                        <li>Produk tahu ditampilkan dari data admin.</li>
                        <li>Metode penerimaan: ambil toko atau kurir toko.</li>
                        <li>Pembayaran mengikuti metode yang tersedia di checkout.</li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <span>© {{ date('Y') }} SiTahu. Web Pembeli Laravel.</span>
                <span>Warna senada admin, tampilan lebih ramah pembeli.</span>
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