@extends('layouts.pembeli')
@section('title','Web Pembeli - SiTahu')
@section('content')
<section class="hero">
    <div>
        <h1>Web pembeli sudah disiapkan untuk pengembangan berikutnya.</h1>
        <p>Folder ini sengaja dipisah dari admin. Tampilan web pembeli mobile friendly dan membaca data dari endpoint API Laravel yang sama dengan aplikasi Android.</p>
        <a class="btn" href="{{ route('pembeli-web.produk') }}">Lihat Produk</a>
    </div>
    <div class="card card-pad"><div class="notice"><strong>Catatan arsitektur</strong><br>Android Kotlin memakai Volley ke /api/products, /api/orders, /api/reviews, dan endpoint lain. Web pembeli juga bisa memakai API yang sama.</div></div>
</section>
@endsection
