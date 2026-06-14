@extends('layouts.admin')
@section('title', 'Tambah Produk - SiTahu Admin')

@section('content')
<div class="hero">
    <div>
        <h1>Tambah produk</h1>
        <p></p>
    </div>
    <a href="{{ route('admin.produk.index') }}" class="btn btn-light border"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
</div>

<form method="POST" action="{{ route('admin.produk.store') }}" enctype="multipart/form-data">
    @csrf
    @include('admin.produk._form', ['produk' => $produk])

    <div class="d-flex justify-content-end gap-2 mt-3">
        <a href="{{ route('admin.produk.index') }}" class="btn btn-light border px-4">Batal</a>
        <button class="btn btn-brand px-4" type="submit"><i class="bi bi-check2-circle me-1"></i> Simpan Produk</button>
    </div>
</form>
@endsection
