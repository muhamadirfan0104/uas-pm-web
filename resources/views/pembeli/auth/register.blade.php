@extends('layouts.pembeli')

@section('title', 'Daftar Pembeli - SiTahu')

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

    .form-row {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
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

    .alert-error {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
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

        .form-row {
            grid-template-columns: 1fr;
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
                    Daftar akun, <span>mulai pesan tahu</span>
                </h1>

                <p>
                    Akun pembeli dipakai untuk checkout, melihat status pesanan,
                    menyimpan data pembeli, dan memberi ulasan setelah pesanan selesai.
                </p>
            </div>

            <div class="auth-feature">
                <div>👤 Data pembeli tersimpan rapi</div>
                <div>📍 Bisa lanjut ke alamat pengiriman saat checkout</div>
                <div>⭐ Bisa memberi rating dan ulasan setelah pesanan selesai</div>
            </div>
        </div>

        <div class="auth-form">
            <h2>Daftar Pembeli</h2>
            <p>Isi data akun pembeli untuk mulai belanja di SiTahu.</p>

            @if($errors->any())
                <div class="alert alert-error">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('pembeli-web.register.post') }}" method="POST" class="form-grid">
                @csrf

                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control"
                        value="{{ old('name') }}"
                        placeholder="Masukkan nama lengkap"
                        autofocus
                    >
                    @error('name') <div class="error-text">{{ $message }}</div> @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            value="{{ old('email') }}"
                            placeholder="nama@email.com"
                        >
                        @error('email') <div class="error-text">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="telepon">Nomor HP</label>
                        <input
                            type="text"
                            id="telepon"
                            name="telepon"
                            class="form-control"
                            value="{{ old('telepon') }}"
                            placeholder="081234567890"
                        >
                        @error('telepon') <div class="error-text">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            placeholder="Minimal 6 karakter"
                        >
                        @error('password') <div class="error-text">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password</label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="form-control"
                            placeholder="Ulangi password"
                        >
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    Daftar Sekarang
                </button>
            </form>

            <div class="auth-bottom">
                Sudah punya akun?
                <a href="{{ route('pembeli-web.login') }}">Login pembeli</a>
            </div>
        </div>
    </div>
</section>
@endsection