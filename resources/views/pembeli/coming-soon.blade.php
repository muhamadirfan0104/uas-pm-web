@extends('layouts.pembeli')
@section('title','Segera Hadir - SiTahu')
@section('content')
<div class="container py-5">
    <div class="surface-strong p-4 p-lg-5 text-center">
        <div class="stat-icon mx-auto mb-3"><i class="bi bi-hourglass-split"></i></div>
        <span class="eyebrow mb-3"><i class="bi bi-stars"></i> Segera hadir</span>
        <h1 class="section-heading display-5 mb-3">Fitur ini sedang disiapkan.</h1>
        <p class="section-subtitle mx-auto mb-4" style="max-width:640px;">Halaman sedang disiapkan.</p>
        <a class="btn btn-brand px-4 py-3" href="{{ route('pembeli-web.home') }}">Kembali ke Beranda</a>
    </div>
</div>
@endsection
