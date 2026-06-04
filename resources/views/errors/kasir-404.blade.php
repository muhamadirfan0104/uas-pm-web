<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Kasir Tidak Ditemukan - SiTahu</title>
    <style>
        body{margin:0;min-height:100vh;display:grid;place-items:center;background:#f6f2ea;color:#2f261d;font-family:Inter,system-ui,-apple-system,"Segoe UI",sans-serif;padding:24px}
        .card{width:min(560px,100%);background:#fff;border:1px solid #eadfce;border-radius:24px;padding:28px;box-shadow:0 12px 30px rgba(73,53,27,.07)}
        h1{margin:0 0 8px;font-size:30px;letter-spacing:-.04em}.muted{color:#8b7b68;line-height:1.7}
        .btn{display:inline-flex;margin-top:18px;border-radius:12px;background:#c89b3c;color:#fff;padding:12px 16px;text-decoration:none;font-weight:800}
    </style>
</head>
<body>
    <div class="card">
        <h1>Halaman kasir tidak ditemukan</h1>
        <p class="muted">Fitur kasir masih coming soon. Silakan kembali ke halaman kasir utama.</p>
        <a class="btn" href="{{ route('kasir.dashboard') }}">Kembali ke Kasir</a>
    </div>
</body>
</html>
