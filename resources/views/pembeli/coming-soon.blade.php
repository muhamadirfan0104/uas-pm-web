@extends('layouts.pembeli')
@section('title','Coming Soon - SiTahu')
@section('content')
<div class="card card-pad" style="margin:40px 0;text-align:center">
    <div class="mark" style="margin:0 auto 18px;width:64px;height:64px">TK</div>
    <h1 style="margin:0;font-size:34px;letter-spacing:-.05em">Coming Soon</h1>
    <p class="sub">Halaman web pembeli ini sudah disiapkan sebagai folder dan route, tetapi fitur utama pembeli saat ini tetap ditargetkan untuk aplikasi mobile Android.</p>
    <a class="btn" href="{{ route('pembeli-web.home') }}">Kembali</a>
</div>
@endsection
