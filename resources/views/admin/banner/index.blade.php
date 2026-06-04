@extends('layouts.admin')
@section('title','Banner - SiTahu')
@section('content')
<div class="page-hero d-flex align-items-start justify-content-between gap-3">
    <div>
        <h1 class="page-title">Banner</h1>
        <p class="page-description">Kelola banner beranda mobile pembeli. Tambah dan edit banner memakai Bootstrap Modal.</p>
    </div>
    <div>
        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#modalBannerCreate">+ Tambah Banner</button>
    </div>
</div>

<div class="row g-3">
@forelse($banner as $item)
    <div class="col-12 col-md-6 col-xl-4">
        <div class="card h-100 border-0 shadow-sm rounded-4">
            <div class="card-body">
                @if($item->url_gambar)
                    <img src="{{ asset('storage/'.$item->url_gambar) }}" alt="{{ $item->judul }}" class="w-100 rounded-4 border" style="height:170px;object-fit:cover">
                @else
                    <div class="upload-box">Banner</div>
                @endif

                <div class="mt-3">
                    <strong>{{ $item->judul }}</strong>
                    <span class="sub">Urutan {{ $item->urutan }} · {{ $item->deskripsi ?? '-' }}</span>
                </div>

                <div class="mt-2">
                    <span class="badge rounded-pill {{ $item->aktif ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $item->aktif ? 'Aktif' : 'Nonaktif' }}</span>
                </div>

                <div class="d-flex gap-2 flex-wrap mt-3">
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#modalBannerEdit{{ $item->id }}">Edit</button>
                    <form method="POST" action="{{ route('admin.banner.toggle',$item) }}" data-confirm-title="Ubah Status Banner" data-confirm-message="Yakin ingin mengubah status banner ini?" data-confirm-button="Ubah Status">
                        @csrf
                        @method('PATCH')
                        <button class="btn btn-sm btn-outline-secondary" type="submit">{{ $item->aktif ? 'Nonaktif' : 'Aktifkan' }}</button>
                    </form>
                    <form method="POST" action="{{ route('admin.banner.destroy',$item) }}" data-confirm-title="Hapus Banner" data-confirm-message="Yakin ingin menghapus banner ini? Data yang dihapus tidak dapat dikembalikan." data-confirm-button="Hapus">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body">
                <strong>Belum ada banner</strong>
                <p class="sub mb-0">Klik tambah banner untuk mulai.</p>
            </div>
        </div>
    </div>
@endforelse
</div>

<div class="mt-3">{{ $banner->links() }}</div>

<div class="modal fade" id="modalBannerCreate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.banner.store') }}" enctype="multipart/form-data">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">Tambah Banner</h5>
                        <p class="text-muted mb-0 small">Upload banner baru untuk beranda mobile pembeli.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    @include('admin.banner._form', ['banner' => new \App\Models\Banner()])
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" type="submit">Simpan Banner</button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach($banner as $item)
<div class="modal fade" id="modalBannerEdit{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.banner.update',$item) }}" enctype="multipart/form-data">
                @method('PUT')
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">Edit Banner</h5>
                        <p class="text-muted mb-0 small">Perbarui banner yang tampil di beranda mobile pembeli.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    @include('admin.banner._form', ['banner' => $item])
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" type="submit">Simpan Banner</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection
