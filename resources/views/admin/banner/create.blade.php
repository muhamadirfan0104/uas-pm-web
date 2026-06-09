@extends('layouts.admin')
@section('title', 'Tambah Banner - SiTahu Admin')

@section('content')
<div class="hero">
    <div>
        <h1>Tambah banner</h1>
        <p>Banner aktif akan tampil di slider beranda pembeli.</p>
    </div>
    <a href="{{ route('admin.banner.index') }}" class="btn btn-light border"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
</div>

<form method="POST" action="{{ route('admin.banner.store') }}" enctype="multipart/form-data">
    @csrf
    @include('admin.banner._form', ['banner' => $banner])

    <div class="d-flex justify-content-end gap-2 mt-3">
        <a href="{{ route('admin.banner.index') }}" class="btn btn-light border px-4">Batal</a>
        <button class="btn btn-brand px-4" type="submit"><i class="bi bi-check2-circle me-1"></i> Simpan Banner</button>
    </div>
</form>
@endsection
