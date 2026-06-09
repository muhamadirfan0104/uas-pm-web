@extends('layouts.pembeli')

@section('title', 'Masuk Akun Pembeli - SiTahu')

@push('styles')
<style>
    .auth-wrap { min-height: 680px; }
    .auth-panel { border-radius: 34px; overflow: hidden; background: #fff; border: 1px solid var(--line); box-shadow: var(--shadow-md); }
    .auth-hero { min-height: 100%; background: radial-gradient(circle at 20% 15%, rgba(255,255,255,.35), transparent 20rem), linear-gradient(135deg, var(--brand-color), var(--brand-dark)); color: #fff; padding: 42px; position: relative; overflow: hidden; }
    .auth-hero::after { content: ''; position: absolute; width: 320px; height: 320px; right: -140px; bottom: -140px; background: rgba(255,255,255,.12); border-radius: 50%; }
    .auth-form { padding: 38px; }
    .auth-field { min-height: 52px; border-radius: 16px; border-color: var(--line); font-weight: 700; }
    .auth-field:focus { border-color: rgba(200,147,53,.55); box-shadow: 0 0 0 .25rem rgba(200,147,53,.12); }
    .auth-benefit { display: flex; gap: 14px; align-items: flex-start; margin-top: 22px; position: relative; z-index: 1; }
    .auth-benefit i { width: 42px; height: 42px; border-radius: 16px; display: grid; place-items: center; background: rgba(255,255,255,.16); }
    @media (max-width: 991.98px) { .auth-form, .auth-hero { padding: 26px; } }
</style>
@endpush

@section('content')
<div class="container py-4 py-lg-5 auth-wrap d-flex align-items-center">
    <div class="auth-panel w-100">
        <div class="row g-0">
            <div class="col-lg-5">
                <div class="auth-hero h-100 d-flex flex-column justify-content-between">
                    <div class="position-relative" style="z-index:1;">
                        <span class="badge rounded-pill mb-3" style="background: rgba(255,255,255,.16); border:1px solid rgba(255,255,255,.18);">Akun pembeli</span>
                        <h1 class="section-heading text-white display-6 mb-3">Masuk untuk melanjutkan pesanan.</h1>
                        <p class="mb-0" style="color: rgba(255,255,255,.78); line-height: 1.75;">Keranjang yang sudah Anda isi sebelum login akan otomatis masuk ke akun pembeli setelah berhasil masuk.</p>
                    </div>
                    <div>
                        <div class="auth-benefit"><i class="bi bi-bag-check"></i><div><div class="fw-bold">Keranjang tersimpan</div><small style="color:rgba(255,255,255,.72);">Produk pilihan tetap aman setelah login.</small></div></div>
                        <div class="auth-benefit"><i class="bi bi-receipt"></i><div><div class="fw-bold">Riwayat pesanan</div><small style="color:rgba(255,255,255,.72);">Pantau status pembayaran, pengambilan, dan pengiriman.</small></div></div>
                        <div class="auth-benefit"><i class="bi bi-chat-heart"></i><div><div class="fw-bold">Beri ulasan</div><small style="color:rgba(255,255,255,.72);">Nilai produk setelah pesanan selesai.</small></div></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="auth-form">
                    <div class="mb-4">
                        <a href="{{ route('pembeli-web.home') }}" class="text-decoration-none text-brand fw-bold"><i class="bi bi-arrow-left me-1"></i> Kembali ke beranda</a>
                        <h2 class="section-heading h1 mt-3 mb-2">Selamat datang kembali.</h2>
                        <p class="text-muted mb-0">Gunakan email atau nomor HP yang terdaftar sebagai pembeli.</p>
                    </div>

                    <form action="{{ route('pembeli-web.login.post') }}" method="POST" class="d-grid gap-3">
                        @csrf
                        <div>
                            <label for="login" class="form-label fw-bold">Email atau Nomor HP</label>
                            <input id="login" type="text" name="login" value="{{ old('login') }}" class="form-control auth-field @error('login') is-invalid @enderror" placeholder="contoh@email.com / 08123456789" required autofocus>
                            @error('login')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label for="password" class="form-label fw-bold">Password</label>
                            <input id="password" type="password" name="password" class="form-control auth-field @error('password') is-invalid @enderror" placeholder="Masukkan password" required>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="d-flex justify-content-between align-items-center gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold text-muted" for="remember">Ingat saya</label>
                            </div>
                            <a href="{{ route('pembeli-web.produk') }}" class="fw-bold text-brand text-decoration-none">Belanja dulu</a>
                        </div>
                        <button type="submit" class="btn btn-brand btn-lg py-3"><i class="bi bi-box-arrow-in-right me-2"></i> Masuk</button>
                    </form>

                    <div class="surface p-3 mt-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                        <span class="text-muted fw-semibold">Belum punya akun pembeli?</span>
                        <button type="button" class="btn btn-soft-brand px-4" data-bs-toggle="modal" data-bs-target="#registerModal">Daftar Sekarang</button>
                    </div>

                    <div class="alert alert-light border mt-4 mb-0 rounded-4 small text-muted fw-semibold">
                        <i class="bi bi-info-circle text-brand me-1"></i> Halaman ini khusus untuk pembeli yang ingin berbelanja dan memantau pesanan.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
