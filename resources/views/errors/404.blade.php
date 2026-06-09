<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Tidak Ditemukan - SiTahu</title>
    <style>
        :root{--bg:#f6f2ea;--panel:#fff;--text:#2f261d;--muted:#8b7b68;--line:#eadfce;--primary:#c89b3c;--shadow:0 12px 30px rgba(73,53,27,.07)}
        *{box-sizing:border-box}
        body{margin:0;min-height:100vh;display:grid;place-items:center;background:var(--bg);color:var(--text);font-family:Inter,system-ui,-apple-system,"Segoe UI",sans-serif;padding:24px}
        .card{width:min(640px,100%);background:var(--panel);border:1px solid var(--line);border-radius:26px;padding:34px;box-shadow:var(--shadow)}
        .logo{width:56px;height:56px;border-radius:18px;background:linear-gradient(135deg,#dfba68,var(--primary));display:grid;place-items:center;color:#fff;font-weight:900;margin-bottom:18px}
        h1{margin:0 0 10px;font-size:34px;letter-spacing:-.05em}.muted{margin:0;color:var(--muted);line-height:1.75}
        .actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:22px}
        .btn{border-radius:13px;padding:12px 16px;text-decoration:none;font-weight:800}
        .primary{background:var(--primary);color:#fff}.secondary{background:#fff;color:var(--text);border:1px solid var(--line)}
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">S</div>
        <h1>Halaman tidak ditemukan</h1>
        <p class="muted">URL yang kamu buka tidak tersedia. Jika ingin masuk ke dashboard admin, silakan login terlebih dahulu.</p>
        <div class="actions">
            <a class="btn primary" href="{{ route('login') }}">Ke Halaman Login</a>
            <a class="btn secondary" href="{{ route('pembeli-web.home') }}">Web Pembeli</a>
        </div>
    </div>
</body>
</html>
