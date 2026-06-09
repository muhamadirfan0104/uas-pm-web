@extends('layouts.admin')
@section('title', 'Edit Produk - SiTahu Admin')

@section('content')
<div class="hero">
    <div>
        <h1>Edit produk</h1>
        <p>Perbarui informasi {{ $produk->nama }}.</p>
    </div>
    <a href="{{ route('admin.produk.index') }}" class="btn btn-light border"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
</div>

<form method="POST" action="{{ route('admin.produk.update', $produk) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    @include('admin.produk._form', ['produk' => $produk])

    <div class="d-flex justify-content-end gap-2 mt-3">
        <a href="{{ route('admin.produk.index') }}" class="btn btn-light border px-4">Batal</a>
        <button class="btn btn-brand px-4" type="submit"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
    </div>
</form>
@endsection
