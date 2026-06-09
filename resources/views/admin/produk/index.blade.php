@extends('layouts.admin')
@section('title', 'Produk - SiTahu Admin')

@section('content')
@php
    $rupiah = fn ($value) => 'Rp'.number_format((float) $value, 0, ',', '.');
@endphp

<style>
    .catalog-hero {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 18px;
        align-items: end;
        margin-bottom: 18px;
        padding: 20px;
        border: 1px solid #f1d49c;
        border-radius: 24px;
        background: radial-gradient(circle at 100% 100%, rgba(200,147,53,.14), transparent 18rem), linear-gradient(135deg, #fff, #fff8ea);
        box-shadow: var(--shadow-soft);
    }
    .catalog-hero h1 { margin: 0; font-size: 1.25rem; font-weight: 950; letter-spacing: -.04em; }
    .catalog-hero p { margin: 6px 0 0; color: var(--muted); font-size: .84rem; font-weight: 700; }
    .metric-mini { padding: 16px; border: 1px solid var(--border); border-radius: 20px; background: #fff; box-shadow: var(--shadow-soft); min-height: 102px; }
    .metric-mini .label { color: var(--muted); font-size: .72rem; font-weight: 950; text-transform: uppercase; letter-spacing: .06em; }
    .metric-mini .value { margin-top: 8px; color: var(--text); font-size: 1.34rem; font-weight: 950; letter-spacing: -.04em; line-height: 1; }
    .metric-mini .note { margin-top: 7px; color: var(--muted); font-size: .72rem; font-weight: 800; }
    .filter-panel { padding: 15px; border-bottom: 1px solid var(--border); background: #fff; }
    .product-photo { width: 58px; height: 58px; border-radius: 16px; border: 1px solid var(--border); object-fit: cover; background: #f8fafc; }
    .photo-empty { width: 58px; height: 58px; border-radius: 16px; border: 1px solid var(--border); background: linear-gradient(135deg,#fff8ea,#f8fafc); color: var(--brand-dark); display: inline-flex; align-items: center; justify-content: center; font-weight: 950; }
    .product-name { color: var(--text); font-size: .92rem; font-weight: 950; letter-spacing: -.015em; }
    .product-desc { max-width: 370px; color: var(--muted); font-size: .76rem; font-weight: 650; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .modal-preview { width: 100%; min-height: 220px; border-radius: 22px; border: 1px solid var(--border); background: linear-gradient(135deg,#fff8ea,#fff); display: flex; align-items: center; justify-content: center; overflow: hidden; }
    .modal-preview img { width: 100%; height: 100%; object-fit: cover; }
    .info-box { padding: 14px; border: 1px solid var(--border); border-radius: 18px; background: #fff; }
    .admin-modal-scroll .modal-content { max-height: calc(100vh - 48px); overflow: hidden; }
    .admin-modal-scroll .modal-body { overflow-y: auto; }
    @media (max-width: 991px) { .catalog-hero { grid-template-columns: 1fr; } }
</style>

<div class="catalog-hero">
    <div>
        <span class="chip c-yellow mb-3"><i class="bi bi-basket2"></i> Manajemen katalog</span>
        <h1>Produk toko</h1>
        <p>Kelola informasi produk yang dilihat pembeli: nama, foto, harga, satuan, deskripsi, dan status tampil.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap justify-content-lg-end">
        <button class="btn btn-brand px-3" type="button" data-bs-toggle="modal" data-bs-target="#modalProdukCreate">
            <i class="bi bi-plus-lg me-1"></i> Tambah Produk
        </button>
    </div>
</div>

<div class="grid g4 mb-3">
    <div class="metric-mini">
        <div class="label">Total produk</div>
        <div class="value">{{ $stats['total'] }}</div>
        <div class="note">SKU tersimpan</div>
    </div>
    <div class="metric-mini">
        <div class="label">Aktif tampil</div>
        <div class="value">{{ $stats['aktif'] }}</div>
        <div class="note">Muncul di katalog pembeli</div>
    </div>
    <div class="metric-mini">
        <div class="label">Disembunyikan</div>
        <div class="value">{{ $stats['nonaktif'] }}</div>
        <div class="note">Tidak tampil untuk pembeli</div>
    </div>
    <div class="metric-mini">
        <div class="label">Total terjual</div>
        <div class="value">{{ number_format($stats['terjual']) }}</div>
        <div class="note">Akumulasi item terpesan</div>
    </div>
</div>

<div class="page-card overflow-hidden">
    <form id="page-filter" class="js-instant-filter filter-panel" method="GET">
        <div class="row g-3 align-items-end">
            <div class="col-12 col-xl-5">
                <label class="form-label small fw-bold text-muted">Cari produk</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input class="form-control border-start-0" type="search" name="q" value="{{ request('q') }}" placeholder="Nama, deskripsi, atau satuan produk">
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <label class="form-label small fw-bold text-muted">Status tampil</label>
                <select class="form-select" name="status">
                    <option value="">Semua status</option>
                    <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected(request('status') === 'nonaktif')>Disembunyikan</option>
                </select>
            </div>
            <div class="col-12 col-sm-6 col-xl-2">
                <label class="form-label small fw-bold text-muted">Urutkan</label>
                <select class="form-select" name="sort">
                    <option value="">Terbaru</option>
                    <option value="nama" @selected(request('sort') === 'nama')>Nama A-Z</option>
                    <option value="harga_terendah" @selected(request('sort') === 'harga_terendah')>Harga terendah</option>
                    <option value="harga_tertinggi" @selected(request('sort') === 'harga_tertinggi')>Harga tertinggi</option>
                    <option value="terlaris" @selected(request('sort') === 'terlaris')>Terlaris</option>
                    <option value="rating" @selected(request('sort') === 'rating')>Rating tertinggi</option>
                </select>
            </div>
            <div class="col-12 col-xl-2">
                <a href="{{ route('admin.produk.index') }}" class="btn btn-light border w-100"><i class="bi bi-arrow-counterclockwise me-1"></i> Reset</a>
            </div>
        </div>
    </form>

    <div class="table-wrap bg-white">
        <table class="mb-0">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Harga & satuan</th>
                    <th>Detail jual</th>
                    <th>Performa</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($produk as $item)
                @php
                    $rating = $item->ulasan_avg_rating ? number_format((float) $item->ulasan_avg_rating, 1) : '-';
                    $terjual = (int) ($item->total_terjual ?? 0);
                @endphp
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3 min-w-0">
                            @if($item->gambarUtama)
                                <img class="product-photo" src="{{ asset('storage/'.$item->gambarUtama->url_gambar) }}" alt="{{ $item->nama }}">
                            @else
                                <span class="photo-empty"><i class="bi bi-image"></i></span>
                            @endif
                            <div class="min-w-0">
                                <div class="product-name">{{ $item->nama }}</div>
                                <div class="product-desc">{{ $item->deskripsi ?: 'Belum ada deskripsi produk.' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <strong>{{ $rupiah($item->harga) }}</strong>
                        <span class="sub">/{{ $item->satuan ?: 'satuan' }}</span>
                    </td>
                    <td>
                        <strong>{{ $item->isi_per_satuan ?: '-' }} pcs</strong>
                        <span class="sub">Berat {{ $item->berat ?: '-' }} kg · simpan {{ $item->masa_simpan ?: '-' }} hari</span>
                    </td>
                    <td>
                        <strong>{{ $terjual }}</strong> terjual
                        <span class="sub"><i class="bi bi-star-fill text-warning"></i> {{ $rating }} · {{ $item->ulasan_count }} ulasan</span>
                    </td>
                    <td>
                        <span class="chip {{ $item->aktif ? 'c-green' : 'c-gray' }}">{{ $item->aktif ? 'Tampil' : 'Sembunyi' }}</span>
                    </td>
                    <td>
                        <div class="actions justify-content-end">
                            <button class="small-btn" type="button" data-bs-toggle="modal" data-bs-target="#modalProdukDetail{{ $item->id }}" title="Detail"><i class="bi bi-eye"></i></button>
                            <button class="small-btn" type="button" data-bs-toggle="modal" data-bs-target="#modalProdukEdit{{ $item->id }}" title="Edit"><i class="bi bi-pencil-square"></i></button>
                            <form class="inline-form" method="POST" action="{{ route('admin.produk.toggle', $item) }}" data-confirm-title="Ubah Status Produk" data-confirm-message="Produk akan {{ $item->aktif ? 'disembunyikan dari' : 'ditampilkan di' }} katalog pembeli." data-confirm-button="Ubah Status">
                                @csrf
                                @method('PATCH')
                                <button class="small-btn" type="submit" title="Ubah status"><i class="bi {{ $item->aktif ? 'bi-eye-slash' : 'bi-eye' }}"></i></button>
                            </form>
                            <form class="inline-form" method="POST" action="{{ route('admin.produk.destroy', $item) }}" data-confirm-title="Hapus Produk" data-confirm-message="Produk yang dihapus tidak dapat dikembalikan. Lanjutkan?" data-confirm-button="Hapus Produk">
                                @csrf
                                @method('DELETE')
                                <button class="small-btn text-danger" type="submit" title="Hapus"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="fw-bold mb-1">Produk belum ditemukan</div>
                        <div class="text-muted small">Ubah filter pencarian atau tambahkan produk baru.</div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $produk->links() }}</div>

<div class="modal fade" id="modalProdukCreate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable admin-modal-scroll">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <form method="POST" action="{{ route('admin.produk.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-0 px-4 pt-4">
                    <div>
                        <h5 class="modal-title fw-black">Tambah produk</h5>
                        <p class="text-muted small mb-0">Lengkapi informasi yang akan dilihat pembeli di katalog.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body px-4">
                    @include('admin.produk._form', ['produk' => new \App\Models\Produk()])
                </div>
                <div class="modal-footer border-0 bg-light px-4 py-3">
                    <button class="btn btn-light border" type="button" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-brand px-4" type="submit"><i class="bi bi-check2-circle me-1"></i> Simpan Produk</button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach($produk as $item)
<div class="modal fade" id="modalProdukDetail{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable admin-modal-scroll">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 px-4 pt-4">
                <div>
                    <h5 class="modal-title fw-black">Detail produk</h5>
                    <p class="text-muted small mb-0">Informasi produk yang tersimpan di katalog toko.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body px-4 pb-4">
                <div class="row g-4">
                    <div class="col-md-5">
                        <div class="modal-preview">
                            @if($item->gambarUtama)
                                <img src="{{ asset('storage/'.$item->gambarUtama->url_gambar) }}" alt="{{ $item->nama }}">
                            @else
                                <div class="text-center text-muted">
                                    <i class="bi bi-image fs-1 d-block mb-2"></i>
                                    Belum ada foto
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-7">
                        <span class="chip {{ $item->aktif ? 'c-green' : 'c-gray' }} mb-2">{{ $item->aktif ? 'Tampil di pembeli' : 'Disembunyikan' }}</span>
                        <h4 class="fw-black mb-2">{{ $item->nama }}</h4>
                        <div class="fs-3 fw-black mb-3" style="color: var(--brand-dark)">{{ $rupiah($item->harga) }}</div>
                        <p class="text-muted small lh-lg">{{ $item->deskripsi ?: 'Produk ini belum memiliki deskripsi.' }}</p>

                        <div class="grid g2 mt-3">
                            <div class="info-box"><span class="sub">Satuan</span><strong>{{ $item->satuan ?: '-' }}</strong></div>
                            <div class="info-box"><span class="sub">Isi</span><strong>{{ $item->isi_per_satuan ?: '-' }} pcs</strong></div>
                            <div class="info-box"><span class="sub">Berat</span><strong>{{ $item->berat ?: '-' }} kg</strong></div>
                            <div class="info-box"><span class="sub">Masa simpan</span><strong>{{ $item->masa_simpan ?: '-' }} hari</strong></div>
                        </div>

                        <div class="mt-3">
                            <div class="fw-bold small mb-1">Saran penyimpanan</div>
                            <div class="text-muted small">{{ $item->saran_penyimpanan ?: '-' }}</div>
                        </div>
                        <div class="mt-3">
                            <div class="fw-bold small mb-1">Saran penyajian</div>
                            <div class="text-muted small">{{ $item->saran_penyajian ?: '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalProdukEdit{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable admin-modal-scroll">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <form method="POST" action="{{ route('admin.produk.update', $item) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header border-0 px-4 pt-4">
                    <div>
                        <h5 class="modal-title fw-black">Edit produk</h5>
                        <p class="text-muted small mb-0">Perbarui informasi {{ $item->nama }}.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body px-4">
                    @include('admin.produk._form', ['produk' => $item])
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
