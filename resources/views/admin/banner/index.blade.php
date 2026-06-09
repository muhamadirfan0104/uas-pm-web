@extends('layouts.admin')
@section('title', 'Banner - SiTahu Admin')

@section('content')
<style>
    .banner-hero { display: grid; grid-template-columns: minmax(0,1fr) auto; gap: 18px; align-items: end; padding: 22px; border: 1px solid #f1d49c; border-radius: 24px; background: radial-gradient(circle at 100% 100%, rgba(200,147,53,.16), transparent 18rem), linear-gradient(135deg,#fff,#fff8ea); box-shadow: var(--shadow-soft); margin-bottom: 18px; }
    .banner-hero h1 { margin: 0; font-size: 1.34rem; font-weight: 950; letter-spacing: -.04em; }
    .banner-hero p { margin: 6px 0 0; color: var(--muted); font-size: .86rem; font-weight: 700; }
    .banner-stat { min-height: 102px; padding: 16px; border: 1px solid var(--border); border-radius: 20px; background: #fff; box-shadow: var(--shadow-soft); }
    .banner-stat .label { color: var(--muted); font-size: .72rem; font-weight: 950; text-transform: uppercase; letter-spacing: .06em; }
    .banner-stat .value { margin-top: 8px; font-size: 1.38rem; font-weight: 950; letter-spacing: -.04em; line-height: 1; }
    .banner-stat .note { margin-top: 7px; color: var(--muted); font-size: .72rem; font-weight: 800; }
    .filter-panel { padding: 15px; border-bottom: 1px solid var(--border); background: #fff; }
    .banner-card { height: 100%; border: 1px solid var(--border); border-radius: 22px; background: #fff; box-shadow: var(--shadow-soft); overflow: hidden; }
    .banner-image { position: relative; height: 185px; background: linear-gradient(135deg,#fff8ea,#f8fafc); border-bottom: 1px solid var(--border); overflow: hidden; }
    .banner-image img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .banner-placeholder { height: 100%; display: flex; align-items: center; justify-content: center; color: var(--brand-dark); font-size: 2rem; }
    .banner-order { position: absolute; left: 14px; top: 14px; padding: 6px 10px; border-radius: 999px; background: rgba(255,255,255,.92); border: 1px solid rgba(231,234,240,.8); color: var(--brand-dark); font-size: .72rem; font-weight: 950; }
    .banner-body { padding: 16px; }
    .banner-title { color: var(--text); font-size: .95rem; font-weight: 950; letter-spacing: -.02em; }
    .banner-desc { min-height: 40px; color: var(--muted); font-size: .78rem; font-weight: 650; line-height: 1.55; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    @media (max-width: 991px) { .banner-hero { grid-template-columns: 1fr; } }
</style>

<div class="banner-hero">
    <div>
        <span class="chip c-yellow mb-3"><i class="bi bi-images"></i> Banner beranda</span>
        <h1>Banner toko</h1>
        <p>Atur visual promosi yang muncul di halaman awal pembeli. Banner aktif akan tampil sebagai slider.</p>
    </div>
    <button class="btn btn-brand" type="button" data-bs-toggle="modal" data-bs-target="#modalBannerCreate">
        <i class="bi bi-plus-lg me-1"></i> Tambah Banner
    </button>
</div>

<div class="grid g4 mb-3">
    <div class="banner-stat"><div class="label">Total banner</div><div class="value">{{ $stats['total'] }}</div><div class="note">Semua banner</div></div>
    <div class="banner-stat"><div class="label">Aktif</div><div class="value">{{ $stats['aktif'] }}</div><div class="note">Tampil di pembeli</div></div>
    <div class="banner-stat"><div class="label">Draft</div><div class="value">{{ $stats['nonaktif'] }}</div><div class="note">Belum ditampilkan</div></div>
    <div class="banner-stat"><div class="label">Urutan berikutnya</div><div class="value">{{ $stats['urutan_berikutnya'] }}</div><div class="note">Rekomendasi nomor urut</div></div>
</div>

<div class="page-card overflow-hidden mb-3">
    <form class="js-instant-filter filter-panel" method="GET">
        <div class="row g-3 align-items-end">
            <div class="col-12 col-lg-5">
                <label class="form-label small fw-bold text-muted">Cari banner</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input class="form-control border-start-0" type="search" name="q" value="{{ request('q') }}" placeholder="Judul atau deskripsi banner">
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label small fw-bold text-muted">Status</label>
                <select class="form-select" name="status">
                    <option value="">Semua status</option>
                    <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected(request('status') === 'nonaktif')>Draft/nonaktif</option>
                </select>
            </div>
            <div class="col-12 col-sm-6 col-lg-2">
                <label class="form-label small fw-bold text-muted">Urutkan</label>
                <select class="form-select" name="sort">
                    <option value="">Urutan slider</option>
                    <option value="terbaru" @selected(request('sort') === 'terbaru')>Terbaru</option>
                    <option value="terlama" @selected(request('sort') === 'terlama')>Terlama</option>
                </select>
            </div>
            <div class="col-12 col-lg-2">
                <a href="{{ route('admin.banner.index') }}" class="btn btn-light border w-100"><i class="bi bi-arrow-counterclockwise me-1"></i> Reset</a>
            </div>
        </div>
    </form>
</div>

<div class="row g-3">
    @forelse($banner as $item)
        <div class="col-12 col-md-6 col-xl-4">
            <div class="banner-card">
                <div class="banner-image">
                    @if($item->url_gambar)
                        <img src="{{ asset('storage/'.$item->url_gambar) }}" alt="{{ $item->judul }}">
                    @else
                        <div class="banner-placeholder"><i class="bi bi-image"></i></div>
                    @endif
                    <span class="banner-order">Urutan {{ $item->urutan }}</span>
                </div>
                <div class="banner-body">
                    <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                        <div class="min-w-0">
                            <div class="banner-title">{{ $item->judul }}</div>
                            <div class="banner-desc mt-1">{{ $item->deskripsi ?: 'Belum ada deskripsi banner.' }}</div>
                        </div>
                        <span class="chip {{ $item->aktif ? 'c-green' : 'c-gray' }} flex-shrink-0">{{ $item->aktif ? 'Aktif' : 'Draft' }}</span>
                    </div>
                    <div class="actions mt-3">
                        <button class="small-btn" type="button" data-bs-toggle="modal" data-bs-target="#modalBannerPreview{{ $item->id }}"><i class="bi bi-eye"></i> Preview</button>
                        <button class="small-btn" type="button" data-bs-toggle="modal" data-bs-target="#modalBannerEdit{{ $item->id }}"><i class="bi bi-pencil-square"></i> Edit</button>
                        <form class="inline-form" method="POST" action="{{ route('admin.banner.toggle', $item) }}" data-confirm-title="Ubah Status Banner" data-confirm-message="Banner akan {{ $item->aktif ? 'disembunyikan dari' : 'ditampilkan di' }} halaman pembeli." data-confirm-button="Ubah Status">
                            @csrf
                            @method('PATCH')
                            <button class="small-btn" type="submit"><i class="bi {{ $item->aktif ? 'bi-eye-slash' : 'bi-eye' }}"></i></button>
                        </form>
                        <form class="inline-form" method="POST" action="{{ route('admin.banner.destroy', $item) }}" data-confirm-title="Hapus Banner" data-confirm-message="Banner yang dihapus tidak dapat dikembalikan." data-confirm-button="Hapus Banner">
                            @csrf
                            @method('DELETE')
                            <button class="small-btn text-danger" type="submit"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="page-card p-5 text-center">
                <div class="fw-bold mb-1">Belum ada banner</div>
                <div class="text-muted small mb-3">Tambahkan banner untuk mengisi area utama di beranda pembeli.</div>
                <button class="btn btn-brand" type="button" data-bs-toggle="modal" data-bs-target="#modalBannerCreate">Tambah Banner</button>
            </div>
        </div>
    @endforelse
</div>

<div class="mt-3">{{ $banner->links() }}</div>

<div class="modal fade" id="modalBannerCreate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <form method="POST" action="{{ route('admin.banner.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-0 px-4 pt-4">
                    <div>
                        <h5 class="modal-title fw-black">Tambah banner</h5>
                        <p class="text-muted small mb-0">Banner aktif akan tampil di slider beranda pembeli.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body px-4">
                    @include('admin.banner._form', ['banner' => new \App\Models\Banner()])
                </div>
                <div class="modal-footer border-0 bg-light px-4 py-3">
                    <button class="btn btn-light border" type="button" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-brand px-4" type="submit"><i class="bi bi-check2-circle me-1"></i> Simpan Banner</button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach($banner as $item)
<div class="modal fade" id="modalBannerPreview{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="position-relative" style="min-height:320px;background:linear-gradient(135deg,#fff8ea,#fff);">
                @if($item->url_gambar)
                    <img src="{{ asset('storage/'.$item->url_gambar) }}" alt="{{ $item->judul }}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;">
                    <div style="position:absolute;inset:0;background:linear-gradient(90deg,rgba(0,0,0,.52),rgba(0,0,0,.05));"></div>
                @endif
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Tutup"></button>
                <div class="position-relative p-4 p-md-5 text-white" style="max-width:520px;">
                    <span class="badge rounded-pill text-bg-light text-dark mb-3">Urutan {{ $item->urutan }} · {{ $item->aktif ? 'Aktif' : 'Draft' }}</span>
                    <h3 class="fw-black">{{ $item->judul }}</h3>
                    <p class="mb-0 opacity-75">{{ $item->deskripsi ?: 'Banner belum memiliki deskripsi.' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalBannerEdit{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <form method="POST" action="{{ route('admin.banner.update', $item) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header border-0 px-4 pt-4">
                    <div>
                        <h5 class="modal-title fw-black">Edit banner</h5>
                        <p class="text-muted small mb-0">Perbarui banner {{ $item->judul }}.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body px-4">
                    @include('admin.banner._form', ['banner' => $item])
                </div>
                <div class="modal-footer border-0 bg-light px-4 py-3">
                    <button class="btn btn-light border" type="button" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-brand px-4" type="submit"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection
