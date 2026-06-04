@extends('layouts.admin')
@section('title', 'Tambah Produk - SiTahu')

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.produk.index') }}" class="btn btn-light border rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h1 class="h4 fw-bold text-dark mb-1">Tambah Produk Baru</h1>
        <p class="text-muted small mb-0">Data akan tersimpan langsung ke database MySQL.</p>
    </div>
</div>

<div class="row">
    <div class="col-12 col-xl-9">
        <form method="POST" action="{{ route('admin.produk.store') }}" enctype="multipart/form-data">
            @csrf
            
            @include('admin.produk._form', ['produk' => new \App\Models\Produk()])
            
            <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top">
                <a href="{{ route('admin.produk.index') }}" class="btn btn-light border fw-medium px-4">Batal</a>
                <button class="btn fw-bold px-5 shadow-sm text-white" type="submit" style="background: var(--brand-color, #dfba68);">
                    Simpan ke Database
                </button>
            </div>
        </form>
    </div>
</div>
@endsection