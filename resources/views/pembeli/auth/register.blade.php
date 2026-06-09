@extends('layouts.pembeli')

@section('title', 'Daftar Akun Pembeli - SiTahu')

@push('styles')
<style>
    .auth-wrap { min-height: 700px; }
    .auth-panel { border-radius: 34px; overflow: hidden; background: #fff; border: 1px solid var(--line); box-shadow: var(--shadow-md); }
    .auth-hero { min-height: 100%; background: radial-gradient(circle at 80% 18%, rgba(255,255,255,.36), transparent 20rem), linear-gradient(135deg, #182230, var(--brand-dark)); color: #fff; padding: 42px; position: relative; overflow: hidden; }
    .auth-hero::after { content: ''; position: absolute; width: 340px; height: 340px; left: -150px; bottom: -150px; background: rgba(200,147,53,.30); border-radius: 50%; }
    .auth-form { padding: 38px; }
    .auth-field { min-height: 52px; border-radius: 16px; border-color: var(--line); font-weight: 700; }
    .auth-field:focus { border-color: rgba(200,147,53,.55); box-shadow: 0 0 0 .25rem rgba(200,147,53,.12); }
    .benefit-row { display: flex; gap: 14px; align-items: flex-start; margin-top: 22px; position: relative; z-index: 1; }
    .benefit-row i { width: 42px; height: 42px; border-radius: 16px; display: grid; place-items: center; background: rgba(255,255,255,.16); }
    @media (max-width: 991.98px) { .auth-form, .auth-hero { padding: 26px; } }
</style>
@endpush

@section('content')
<div class="container py-4 py-lg-5 auth-wrap d-flex align-items-center">
    <div class="auth-panel w-100">
        <div class="row g-0">
            <div class="col-lg-5 order-lg-2">
                <div class="auth-hero h-100 d-flex flex-column justify-content-between">
                    <div class="position-relative" style="z-index:1;">
                        <span class="badge rounded-pill mb-3" style="background: rgba(255,255,255,.16); border:1px solid rgba(255,255,255,.18);">Daftar pembeli</span>
                        <h1 class="section-heading text-white display-6 mb-3">Akun baru untuk belanja lebih mudah.</h1>
                        <p class="mb-0" style="color: rgba(255,255,255,.78); line-height: 1.75;">Setelah daftar, Anda langsung masuk sebagai pembeli dan produk di keranjang tetap aman untuk dilanjutkan ke checkout.</p>
                    </div>
                    <div>
                        <div class="benefit-row"><i class="bi bi-person-check"></i><div><div class="fw-bold">Checkout lebih cepat</div><small style="color:rgba(255,255,255,.72);">Data kontak tersimpan untuk pesanan berikutnya.</small></div></div>
                        <div class="benefit-row"><i class="bi bi-geo-alt"></i><div><div class="fw-bold">Simpan alamat</div><small style="color:rgba(255,255,255,.72);">Mudah memilih alamat saat memakai kurir toko.</small></div></div>
                        <div class="benefit-row"><i class="bi bi-stars"></i><div><div class="fw-bold">Ulas produk</div><small style="color:rgba(255,255,255,.72);">Bagikan pengalaman setelah pesanan selesai.</small></div></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 order-lg-1">
                <div class="auth-form">
                    <div class="mb-4">
                        <a href="{{ route('pembeli-web.home') }}" class="text-decoration-none text-brand fw-bold"><i class="bi bi-arrow-left me-1"></i> Kembali ke beranda</a>
                        <h2 class="section-heading h1 mt-3 mb-2">Buat akun pembeli.</h2>
                        <p class="text-muted mb-0">Isi data dengan benar agar proses checkout dan konfirmasi pesanan lebih lancar.</p>
                    </div>

                    <form action="{{ route('pembeli-web.register.post') }}" method="POST" class="d-grid gap-3">
                        @csrf
                        <div>
                            <label for="name" class="form-label fw-bold">Nama lengkap</label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" class="form-control auth-field @error('name') is-invalid @enderror" placeholder="Nama pembeli" required autofocus>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-bold">Email</label>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control auth-field @error('email') is-invalid @enderror" placeholder="nama@email.com" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="telepon" class="form-label fw-bold">Nomor HP</label>
                                <input id="telepon" type="text" name="telepon" value="{{ old('telepon') }}" class="form-control auth-field @error('telepon') is-invalid @enderror" placeholder="08123456789" required>
                                @error('telepon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label fw-bold">Password</label>
                                <input id="password" type="password" name="password" class="form-control auth-field @error('password') is-invalid @enderror" placeholder="Minimal 6 karakter" required>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label fw-bold">Konfirmasi Password</label>
                                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control auth-field" placeholder="Ulangi password" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-brand btn-lg py-3"><i class="bi bi-person-plus me-2"></i> Daftar & Masuk</button>
                    </form>

                    <div class="surface p-3 mt-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                        <span class="text-muted fw-semibold">Sudah punya akun?</span>
                        <button type="button" class="btn btn-soft-brand px-4" data-bs-toggle="modal" data-bs-target="#loginModal">Masuk Akun</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
