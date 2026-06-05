@extends('layouts.pembeli')

@section('title', 'Login Pembeli - SiTahu')

@push('styles')
<style>
    .auth-wrap {
        min-height: 70vh;
        display: grid;
        place-items: center;
    }

    .auth-card {
        width: min(980px, 100%);
        display: grid;
        grid-template-columns: 0.95fr 1.05fr;
        overflow: hidden;
        background: #ffffff;
        border: 1px solid var(--line);
        border-radius: 28px;
        box-shadow: var(--shadow);
    }

    .auth-side {
        padding: 34px;
        background:
            radial-gradient(circle at top right, rgba(223, 186, 104, 0.34), transparent 34%),
            linear-gradient(135deg, #fff8e8, #ffffff);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 24px;
    }

    .auth-logo {
        width: 64px;
        height: 64px;
        border-radius: 22px;
        display: grid;
        place-items: center;
        background: linear-gradient(135deg, var(--brand-color), #c89335);
        color: white;
        font-weight: 950;
        font-size: 22px;
        box-shadow: 0 12px 26px rgba(223, 186, 104, 0.30);
    }

    .auth-side h1 {
        margin: 18px 0 0;
        color: var(--heading);
        font-size: clamp(32px, 4vw, 48px);
        line-height: 1;
        letter-spacing: -0.075em;
    }

    .auth-side h1 span {
        color: var(--brand-text);
    }

    .auth-side p {
        margin: 14px 0 0;
        color: var(--muted);
        line-height: 1.75;
        font-size: 14px;
    }

    .auth-feature {
        display: grid;
        gap: 10px;
    }

    .auth-feature div {
        padding: 12px 14px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.72);
        border: 1px solid rgba(229, 231, 235, 0.95);
        color: var(--heading);
        font-size: 13px;
        font-weight: 800;
    }

    .auth-form {
        padding: 34px;
    }

    .auth-form h2 {
        margin: 0;
        color: var(--heading);
        font-size: 28px;
        letter-spacing: -0.055em;
    }

    .auth-form > p {
        margin: 8px 0 24px;
        color: var(--muted);
        font-size: 14px;
        line-height: 1.6;
    }

    .form-grid {
        display: grid;
        gap: 14px;
    }

    .form-group label {
        display: block;
        margin-bottom: 7px;
        color: var(--heading);
        font-size: 13px;
        font-weight: 850;
    }

    .form-control {
        width: 100%;
        min-height: 48px;
        border: 1px solid var(--line);
        border-radius: 14px;
        background: #ffffff;
        color: var(--text);
        padding: 11px 14px;
        outline: none;
        transition: 0.16s ease;
    }

    .form-control:focus {
        border-color: rgba(223, 186, 104, 0.95);
        box-shadow: 0 0 0 4px rgba(223, 186, 104, 0.16);
    }

    .error-text {
        margin-top: 7px;
        color: #b91c1c;
        font-size: 12px;
        font-weight: 750;
    }

    .alert {
        margin-bottom: 16px;
        padding: 13px 15px;
        border-radius: 15px;
        font-size: 13px;
        font-weight: 800;
        line-height: 1.5;
    }

    .alert-success {
        background: #ecfdf5;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .alert-error {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .remember-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 2px;
        color: var(--muted);
        font-size: 13px;
        font-weight: 700;
    }

    .remember-row label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }

    .auth-bottom {
        margin-top: 18px;
        padding-top: 18px;
        border-top: 1px solid var(--line);
        color: var(--muted);
        font-size: 14px;
        text-align: center;
    }

    .auth-bottom a {
        color: var(--brand-text);
        font-weight: 900;
    }

    @media (max-width: 860px) {
        .auth-card {
            grid-template-columns: 1fr;
        }

        .auth-side {
            padding: 26px;
        }

        .auth-feature {
            display: none;
        }

        .auth-form {
            padding: 26px;
        }
    }
</style>
@endpush

@section('content')
<section class="auth-wrap">
    <div class="auth-card">
        <div class="auth-side">
            <div>
                <div class="auth-logo">ST</div>

                <h1>
                    Masuk dulu, <span>baru belanja tahu</span>
                </h1>

                <p>
                    Login pembeli dipakai untuk menyimpan keranjang, checkout, melihat pesanan,
                    dan nanti memberi ulasan produk setelah pesanan selesai.
                </p>
            </div>

            <div class="auth-feature">
                <div>🛒 Keranjang tersimpan selama sesi belanja</div>
                <div>📦 Pesanan bisa dipantau dari akun pembeli</div>
                <div>⭐ Ulasan hanya untuk pembeli yang sudah menyelesaikan pesanan</div>
            </div>
        </div>

        <div class="auth-form">
            <h2>Login Pembeli</h2>
            <p>Masukkan email atau nomor HP yang sudah terdaftar.</p>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('pembeli-web.login.post') }}" method="POST" class="form-grid">
                @csrf

                <div class="form-group">
                    <label for="login">Email atau Nomor HP</label>
                    <input
                        type="text"
                        id="login"
                        name="login"
                        class="form-control"
                        value="{{ old('login') }}"
                        placeholder="Contoh: martha@email.com / 081234567890"
                        autofocus
                    >
                    @error('login') <div class="error-text">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        placeholder="Masukkan password"
                    >
                    @error('password') <div class="error-text">{{ $message }}</div> @enderror
                </div>

                <div class="remember-row">
                    <label>
                        <input type="checkbox" name="remember" value="1">
                        Ingat saya
                    </label>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    Login Sekarang
                </button>
            </form>

            <div class="auth-bottom">
                Belum punya akun?
                <a href="{{ route('pembeli-web.register') }}">Daftar sebagai pembeli</a>
            </div>
        </div>
    </div>
</section>
@endsection