<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SiTahu Web Pembeli')</title>
    <style>
        :root{--bg:#f3f6f9;--panel:#fff;--text:#2f3542;--muted:#8a94a6;--line:#e8edf3;--primary:#1e88ff;--primary-soft:#edf6ff;--accent:#12c48b;--accent-soft:#e9fbf4;--shadow:0 8px 24px rgba(29,39,59,.07)}
        *{box-sizing:border-box}body{margin:0;font-family:Inter,system-ui,-apple-system,"Segoe UI",sans-serif;background:var(--bg);color:var(--text)}a{text-decoration:none;color:inherit}.wrap{width:min(1120px,100%);margin:0 auto;padding:22px}.nav{position:sticky;top:0;z-index:10;background:#fff;border-bottom:1px solid var(--line);box-shadow:0 4px 14px rgba(29,39,59,.04)}.nav-inner{width:min(1120px,100%);margin:0 auto;padding:14px 22px;display:flex;align-items:center;justify-content:space-between;gap:16px}.brand{display:flex;align-items:center;gap:10px;font-weight:900;font-size:18px}.mark{width:40px;height:40px;border-radius:13px;background:linear-gradient(135deg,#d9a84b,var(--primary));display:grid;place-items:center;color:#fff}.menu{display:flex;gap:8px;flex-wrap:wrap}.menu a{padding:10px 13px;border-radius:12px;color:var(--muted);font-weight:760}.menu a.active,.menu a:hover{background:var(--primary-soft);color:var(--primary)}.hero{display:grid;grid-template-columns:1.1fr .9fr;gap:22px;align-items:center;padding:42px 0}.hero h1{font-size:44px;line-height:1.02;letter-spacing:-.06em;margin:0}.hero p{color:var(--muted);line-height:1.75}.btn{display:inline-flex;border:0;border-radius:13px;background:var(--primary);color:white;padding:13px 16px;font-weight:800;box-shadow:0 8px 18px rgba(200,137,47,.20)}.card{background:#fff;border:1px solid var(--line);box-shadow:var(--shadow);border-radius:22px;overflow:hidden}.card-pad{padding:20px}.grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}.product-img{height:170px;background:#f7f9fc;border-bottom:1px solid var(--line);display:grid;place-items:center;color:#687386;font-size:13px;font-weight:900}.price{font-weight:900;color:var(--primary);margin-top:8px}.sub{color:var(--muted);font-size:13px;line-height:1.5}.notice{padding:14px 16px;border-radius:16px;background:var(--accent-soft);color:#527831;border:1px solid #dcebd4;line-height:1.6}@media(max-width:800px){.hero{grid-template-columns:1fr;padding:26px 0}.hero h1{font-size:34px}.grid{grid-template-columns:1fr}.nav-inner{align-items:flex-start;flex-direction:column}.menu{width:100%}.menu a{flex:1;text-align:center}.wrap{padding:16px}}
    </style>
    @stack('styles')
</head>
<body>
<nav class="nav"><div class="nav-inner"><a class="brand" href="{{ route('pembeli-web.home') }}"><span class="mark">T</span><span>SiTahu</span></a><div class="menu"><a class="{{ request()->routeIs('pembeli-web.home') ? 'active' : '' }}" href="{{ route('pembeli-web.home') }}">Beranda</a><a class="{{ request()->routeIs('pembeli-web.produk') ? 'active' : '' }}" href="{{ route('pembeli-web.produk') }}">Produk</a><a href="{{ route('pembeli-web.coming-soon') }}">Keranjang</a><a href="{{ route('pembeli-web.coming-soon') }}">Pesanan</a></div></div></nav>
<main class="wrap">@yield('content')</main>
@stack('scripts')
</body>
</html>
