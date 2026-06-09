@extends('layouts.admin')
@section('title', 'Edit Banner - SiTahu Admin')

@section('content')
<div class="hero">
    <div>
        <h1>Edit banner</h1>
        <p>Perbarui banner {{ $banner->judul }}.</p>
    </div>
    <a href="{{ route('admin.banner.index') }}" class="btn btn-light border"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
</div>

<form method="POST" action="{{ route('admin.banner.update', $banner) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    @include('admin.banner._form', ['banner' => $banner])

    <div class="d-flex justify-content-end gap-2 mt-3">
        <a href="{{ route('admin.banner.index') }}" class="btn btn-light border px-4">Batal</a>
        <button class="btn btn-brand px-4" type="submit"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
    </div>
</form>
@endsection
