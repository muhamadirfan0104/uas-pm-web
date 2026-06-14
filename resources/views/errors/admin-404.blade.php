@extends('layouts.admin')
@section('title','Halaman Tidak Ditemukan - SiTahu')
@section('content')
<div class="page-hero d-flex align-items-start justify-content-between gap-3">
    <div>
        <h1>Halaman tidak ditemukan</h1>
        <p>Halaman admin yang Anda akses tidak tersedia.</p>
    </div>
    <div class="page-hero d-flex align-items-start justify-content-between gap-3-actions">
        <a class="btn btn-primary" href="{{ route('admin.dashboard') }}">Kembali ke Dashboard</a>
    </div>
</div>

<div class="card p-4">
    <div style="display:flex;align-items:flex-start;gap:16px;max-width:780px">
        <div class="stat-icon">404</div>
        <div>
            <h2 style="margin:0 0 8px">URL admin salah</h2>
            <p style="margin:0;color:var(--muted);line-height:1.7">
                Kembali ke dashboard atau masuk ulang ke akun admin.
            </p>
            <div class="actions" style="margin-top:18px">
                <a class="btn btn-outline-secondary" href="{{ route('admin.produk.index') }}">Produk</a>
                <a class="btn btn-outline-secondary" href="{{ route('admin.pesanan.index') }}">Pesanan</a>
                <a class="btn btn-outline-secondary" href="{{ route('admin.laporan.index') }}">Laporan</a>
            </div>
        </div>
    </div>
</div>
@endsection
