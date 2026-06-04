<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SiTahu</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body { 
            background-color: #f6f2ea; /* Warna latar asli bawaan SiTahu */
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, sans-serif; 
        }
        
        .card-login { 
            border-radius: 1.5rem; 
            border: none; 
            box-shadow: 0 12px 30px rgba(73, 53, 27, 0.08); 
        }
        
        .form-control { 
            min-height: 50px; 
            border-radius: 0.85rem; 
            background-color: #f8f9fa; /* Latar input abu-abu terang */
            border: 1px solid transparent;
            transition: all 0.2s;
        }
        
        .form-control:focus { 
            background-color: #ffffff;
            border-color: #c89b3c; 
            box-shadow: 0 0 0 0.25rem rgba(200, 155, 60, 0.15); 
        }
        
        .btn-custom { 
            background-color: #c89b3c; 
            border: none; 
            color: white; 
            min-height: 50px; 
            border-radius: 0.85rem; 
            font-weight: 700;
            transition: background-color 0.2s;
        }
        
        .btn-custom:hover { 
            background-color: #b7892f; 
            color: white;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 p-3">

    <div class="card card-login w-100" style="max-width: 440px;">
        <div class="card-body p-4 p-sm-5">
            
            <!-- Logo & Judul -->
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-4 mb-3 shadow-sm" style="width: 60px; height: 60px; background: linear-gradient(135deg, #dfba68, #c89b3c); color: white;">
                    <i class="bi bi-box-seam fs-2"></i>
                </div>
                <h3 class="fw-bold mb-1" style="color: #2f261d; letter-spacing: -0.03em;">Masuk ke SiTahu</h3>
                <p class="text-muted small">Kelola data toko dan pesanan</p>
            </div>

            <!-- Notifikasi Error/Success Blade -->
<!-- Form Utama -->
            <form method="POST" action="{{ route('login.post') }}" class="d-grid gap-3">
                @csrf
                
                <div>
                    <label class="form-label fw-semibold small" style="color: #2f261d;">Email</label>
                    <input class="form-control" type="email" name="email" value="{{ old('email') }}" placeholder="admin@gmail.com" required autofocus>
                </div>
                
                <div>
                    <label class="form-label fw-semibold small" style="color: #2f261d;">Password</label>
                    <input class="form-control" type="password" name="password" placeholder="••••••••" required>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mt-1">
                    <label class="d-flex align-items-center gap-2 small text-muted" style="cursor: pointer;">
                        <input class="form-check-input mt-0" type="checkbox" name="remember" value="1">
                        Ingat saya
                    </label>
                </div>
                
                <button class="btn btn-custom w-100 mt-3 shadow-sm" type="submit">Login</button>
            </form>

        </div>
        
        <!-- Informasi Tambahan (Footer Card) -->
        <div class="card-footer bg-light border-0 p-4 text-center" style="border-radius: 0 0 1.5rem 1.5rem;">
            <p class="small text-muted mb-2">
                Akses khusus <strong>Admin</strong> dan <strong>Kasir</strong>
            </p>
            <div class="badge text-wrap lh-base fw-normal text-muted" style="background: #e9dece; font-size: 0.75rem;">
                <i class="bi bi-info-circle me-1"></i> Akun pembeli disiapkan untuk aplikasi mobile
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1080">
    @if(session('success'))
        <div class="toast align-items-center text-bg-success border-0 shadow js-auto-toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="toast align-items-center text-bg-danger border-0 shadow js-auto-toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body"><i class="bi bi-exclamation-triangle me-2"></i>{{ $errors->first() }}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
            </div>
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('.js-auto-toast').forEach((toastEl) => {
        new bootstrap.Toast(toastEl, { delay: 4200 }).show();
    });
</script>

</body>
</html>