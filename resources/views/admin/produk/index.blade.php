@extends('layouts.admin')
@section('title', 'Produk Tahu - SiTahu')

@section('content')
<style>
    /* Styling Standar E-Commerce */
    .sc-box { border: 1px solid #e5e7eb; border-radius: 0.75rem; background: #fff; margin-bottom: 1.5rem; overflow: hidden; box-shadow: 0 1px 2px rgba(0,0,0,0.02); }
    
    /* Search Bar Modern */
    .search-bar-modern { background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 0.75rem; transition: all 0.2s; }
    .search-bar-modern:focus-within { background-color: #ffffff; border-color: var(--brand-color, #dfba68); box-shadow: 0 0 0 4px rgba(223, 186, 104, 0.15); }
    .search-bar-modern input { background: transparent; border: none; box-shadow: none; outline: none; width: 100%; }
    
    /* Tabel Enterprise */
    .table-enterprise th { border-bottom: 2px solid #e5e7eb; color: #6b7280; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 1rem 1.25rem; font-weight: 600; background: #fafafa; }
    .table-enterprise td { vertical-align: middle; padding: 1rem 1.25rem; border-bottom: 1px solid #f3f4f6; color: #111827; }
    .table-enterprise tbody tr:hover { background-color: #f9fafb; }
    
    .product-thumb { width: 44px; height: 44px; border-radius: 0.5rem; object-fit: cover; border: 1px solid #e5e7eb; background: #f3f4f6; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #9ca3af; }
    
    /* Tombol Aksi */
    .btn-action { width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; border-radius: 0.5rem; transition: all 0.2s; border: 1px solid #e5e7eb; background: #fff; color: #4b5563; }
    .btn-action:hover { background-color: #f3f4f6; color: #111827; }

    /* Modal Detail Produk Khusus */
    .detail-img-box { position: relative; border-radius: 0.75rem; overflow: hidden; background: #f9fafb; border: 1px solid #e5e7eb; aspect-ratio: 1/1; display: flex; align-items: center; justify-content: center; }
    .detail-img-box img { width: 100%; height: 100%; object-fit: cover; }
    .detail-metric-card { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1rem; text-align: center; }
    .detail-list-item { display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.75rem 0; border-bottom: 1px dashed #e5e7eb; }
    .detail-list-item:last-child { border-bottom: none; padding-bottom: 0; }
</style>

<!-- HEADER UTAMA -->
<div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3 mb-4">
    <div>
        <h1 class="h4 fw-bold text-dark mb-1">Daftar Produk</h1>
        <p class="text-muted small mb-0">Kelola katalog produk, harga, batas minimal, dan ketersediaan stok.</p>
    </div>
    <div>
        <button class="btn shadow-sm fw-bold px-4 text-white d-flex align-items-center gap-2" type="button" data-bs-toggle="modal" data-bs-target="#modalProdukCreate" style="background: var(--brand-color, #dfba68);">
            <i class="bi bi-plus-lg"></i> Tambah Produk
        </button>
    </div>
</div>

<!-- KARTU UTAMA (FILTER & TABEL) -->
<div class="sc-box mb-4">
    <!-- Filter Section -->
    <div class="bg-white border-bottom p-3 p-md-4">
        <form id="page-filter" class="js-instant-filter" method="GET">
            <div class="row g-3 align-items-center">
                <div class="col-12 col-lg">
                    <div class="search-bar-modern d-flex align-items-center px-3 py-2">
                        <i class="bi bi-search text-muted me-2"></i>
                        <input name="q" value="{{ request('q') }}" placeholder="Cari nama produk...">
                    </div>
                </div>
                <div class="col-12 col-md-4 col-lg-3">
                    <select class="form-select bg-light border-light fw-medium" name="status" style="border-radius: 0.75rem; min-height: 42px;">
                        <option value="">Semua Status Tampil</option>
                        <option value="aktif" @selected(request('status')==='aktif')>Aktif Ditampilkan</option>
                        <option value="nonaktif" @selected(request('status')==='nonaktif')>Sembunyi</option>
                        <option value="habis" @selected(request('status')==='habis')>Stok Habis</option>
                    </select>
                </div>
                <div class="col-12 col-md-4 col-lg-3">
                    <select class="form-select bg-light border-light fw-medium" name="sort" style="border-radius: 0.75rem; min-height: 42px;">
                        <option value="">Urutkan: Terbaru</option>
                        <option value="harga_terendah" @selected(request('sort')==='harga_terendah')>Harga Terendah</option>
                        <option value="harga_tertinggi" @selected(request('sort')==='harga_tertinggi')>Harga Tertinggi</option>
                        <option value="stok_terendah" @selected(request('sort')==='stok_terendah')>Stok Terendah</option>
                    </select>
                </div>
            </div>
            <div class="text-muted mt-3 d-flex align-items-center gap-2" style="font-size: 0.75rem;">
                <span class="spinner-grow spinner-grow-sm text-success" style="width: 10px; height: 10px;" role="status"></span>
                Pencarian dan filter otomatis berjalan instan tanpa tombol.
            </div>
        </form>
    </div>

    <!-- Tabel Data -->
    <div class="table-responsive bg-white">
        <table class="table table-enterprise table-borderless mb-0">
            <thead>
                <tr>
                    <th class="ps-4">Informasi Produk</th>
                    <th>Harga Jual</th>
                    <th>Ketersediaan Stok</th>
                    <th>Status Display</th>
                    <th class="pe-4 text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($produk as $item)
                @php
                    $minStok = (int)($item->min_stok ?? 20);
                    $stokClass = $item->stok <= 0 ? 'bg-danger-subtle text-danger-emphasis' : ($item->stok <= $minStok ? 'bg-warning-subtle text-warning-emphasis' : 'bg-success-subtle text-success-emphasis');
                    $stokLabel = $item->stok <= 0 ? 'Habis' : ($item->stok <= $minStok ? 'Menipis' : 'Aman');
                @endphp
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-3">
                            @if($item->gambarUtama)
                                <img src="{{ asset('storage/'.$item->gambarUtama->url_gambar) }}" class="product-thumb shadow-sm" alt="{{ $item->nama }}">
                            @else
                                <div class="product-thumb shadow-sm">PR</div>
                            @endif
                            <div>
                                <strong class="d-block text-dark mb-1" style="font-size: 0.95rem;">{{ $item->nama }}</strong>
                                <span class="text-muted small">Isi {{ $item->isi_per_satuan ?? '-' }} pcs • {{ $item->berat ?? '-' }} gr/{{ $item->satuan }}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <strong class="text-dark">{{ $rupiah($item->harga) }}</strong>
                    </td>
                    <td>
                        <div class="d-flex flex-column align-items-start gap-1">
                            <span class="badge rounded-pill fw-medium px-2 py-1 {{ $stokClass }}">
                                {{ $item->stok }} Unit · {{ $stokLabel }}
                            </span>
                            <span class="text-muted" style="font-size: 0.7rem;">Min. batas: {{ $minStok }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="badge rounded-pill px-2 py-1 fw-medium {{ $item->aktif ? 'bg-info-subtle text-info-emphasis' : 'bg-secondary-subtle text-secondary-emphasis' }}">
                            {{ $item->aktif ? 'Aktif Tampil' : 'Disembunyikan' }}
                        </span>
                    </td>
                    <td class="pe-4 text-end">
                        <div class="d-flex justify-content-end gap-1">
                            <!-- Tombol Detail -->
                            <button class="btn-action" type="button" title="Lihat Detail Produk" data-bs-toggle="modal" data-bs-target="#modalProdukDetail{{ $item->id }}">
                                <i class="bi bi-info-circle text-primary"></i>
                            </button>
                            <!-- Tombol Edit -->
                            <button class="btn-action" type="button" title="Edit Produk" data-bs-toggle="modal" data-bs-target="#modalProdukEdit{{ $item->id }}">
                                <i class="bi bi-pencil text-secondary"></i>
                            </button>
                            <!-- Tombol Toggle -->
                            <form method="POST" action="{{ route('admin.produk.toggle',$item) }}" class="d-inline" data-confirm-title="Ubah Status Produk" data-confirm-message="Yakin ingin mengubah status produk {{ $item->nama }}?" data-confirm-button="Ubah Status">
                                @csrf @method('PATCH')
                                <button class="btn-action" type="submit" title="{{ $item->aktif ? 'Sembunyikan' : 'Tampilkan' }}">
                                    <i class="bi {{ $item->aktif ? 'bi-eye-slash text-warning' : 'bi-eye text-success' }}"></i>
                                </button>
                            </form>
                            <!-- Tombol Hapus -->
                            <form method="POST" action="{{ route('admin.produk.destroy',$item) }}" class="d-inline" data-confirm-title="Hapus Produk" data-confirm-message="Yakin ingin menghapus produk {{ $item->nama }} secara permanen? Data yang dihapus tidak dapat dikembalikan." data-confirm-button="Hapus">
                                @csrf @method('DELETE')
                                <button class="btn-action" type="submit" title="Hapus">
                                    <i class="bi bi-trash text-danger"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <i class="bi bi-box-seam fs-1 text-muted mb-3 d-block"></i>
                        <strong class="text-dark d-block mb-1">Belum ada produk</strong>
                        <span class="text-muted small">Klik Tambah Produk untuk memulai katalog Anda.</span>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    
    @if($produk->hasPages())
        <div class="bg-light border-top p-3">{{ $produk->links() }}</div>
    @endif
</div>


<!-- ============================================== -->
<!-- MODAL: TAMBAH PRODUK                           -->
<!-- ============================================== -->
<div class="modal fade" id="modalProdukCreate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form method="POST" action="{{ route('admin.produk.store') }}" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            <div class="modal-header border-bottom p-4 bg-white">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-1">Tambah Produk Baru</h5>
                    <div class="text-muted small">Stok diubah dari menu Stok. Form ini memakai perhitungan minimal stok.</div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-4 p-md-5" style="background-color: #f9fafb;">
                @include('admin.produk._form', ['produk' => new \App\Models\Produk()])
            </div>
            <div class="modal-footer border-top p-4 bg-white">
                <button class="btn btn-light border fw-medium px-4" type="button" data-bs-dismiss="modal">Batal</button>
                <button class="btn fw-bold px-4 shadow-sm text-white" type="submit" style="background: var(--brand-color, #dfba68);">Simpan Produk</button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================== -->
<!-- MODAL: DETAIL PRODUK (Di-looping)              -->
<!-- ============================================== -->
@foreach($produk as $item)
<div class="modal fade" id="modalProdukDetail{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            
            <div class="modal-header border-bottom p-4 bg-white d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-1">Informasi Detail Produk</h5>
                    <div class="text-muted small">SKU ID: #{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            
            <div class="modal-body p-4 bg-white">
                <div class="row g-4">
                    <!-- Sisi Kiri: Foto Utama -->
                    <div class="col-md-5 col-lg-4">
                        <div class="detail-img-box shadow-sm mb-3">
                            @if($item->gambarUtama)
                                <img src="{{ asset('storage/'.$item->gambarUtama->url_gambar) }}" alt="{{ $item->nama }}">
                            @else
                                <div class="text-muted fw-bold d-flex flex-column align-items-center gap-2">
                                    <i class="bi bi-image fs-1"></i>
                                    <span>Tanpa Foto</span>
                                </div>
                            @endif
                            
                            <!-- Floating Status Badge -->
                            <span class="position-absolute top-0 end-0 m-2 badge rounded-pill {{ $item->aktif ? 'bg-success text-white' : 'bg-secondary text-white' }}">
                                {{ $item->aktif ? 'Aktif Tampil' : 'Sembunyi' }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Sisi Kanan: Spesifikasi -->
                    <div class="col-md-7 col-lg-8">
                        <h4 class="fw-bold text-dark mb-2">{{ $item->nama }}</h4>
                        <div class="fs-4 fw-bold text-dark mb-3" style="color: var(--brand-color) !important;">
                            {{ $rupiah($item->harga) }}
                        </div>
                        
                        <p class="text-muted small lh-lg mb-4">
                            {{ $item->deskripsi ?: 'Produk ini belum memiliki deskripsi yang dituliskan.' }}
                        </p>

                        <!-- Grid Metrik Kecil -->
                        <div class="row g-2 mb-4">
                            <div class="col-6 col-sm-3">
                                <div class="detail-metric-card">
                                    <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.65rem;">Stok Saat Ini</div>
                                    <strong class="text-dark fs-5">{{ $item->stok }}</strong>
                                </div>
                            </div>
                            <div class="col-6 col-sm-3">
                                <div class="detail-metric-card">
                                    <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.65rem;">Minimal Stok</div>
                                    <strong class="text-dark fs-5">{{ $item->min_stok ?? 20 }}</strong>
                                </div>
                            </div>
                            <div class="col-6 col-sm-3">
                                <div class="detail-metric-card">
                                    <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.65rem;">Satuan Jual</div>
                                    <strong class="text-dark fs-6">{{ $item->satuan }}</strong>
                                </div>
                            </div>
                            <div class="col-6 col-sm-3">
                                <div class="detail-metric-card">
                                    <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.65rem;">Berat</div>
                                    <strong class="text-dark fs-6">{{ $item->berat ?? '-' }}g</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Instruksi Accordion/List -->
                        <div class="bg-light border rounded-3 p-3">
                            <div class="detail-list-item pt-0">
                                <i class="bi bi-thermometer-snow text-primary mt-1"></i>
                                <div>
                                    <strong class="d-block text-dark small mb-1">Saran Penyimpanan</strong>
                                    <span class="text-muted small">{{ $item->saran_penyimpanan ?: 'Tidak ada instruksi khusus.' }}</span>
                                </div>
                            </div>
                            <div class="detail-list-item">
                                <i class="bi bi-fire text-danger mt-1"></i>
                                <div>
                                    <strong class="d-block text-dark small mb-1">Saran Penyajian</strong>
                                    <span class="text-muted small">{{ $item->saran_penyajian ?: 'Tidak ada instruksi khusus.' }}</span>
                                </div>
                            </div>
                            <div class="detail-list-item border-bottom-0 pb-0">
                                <i class="bi bi-calendar-check text-success mt-1"></i>
                                <div>
                                    <strong class="d-block text-dark small mb-1">Estimasi Masa Simpan</strong>
                                    <span class="text-muted small">{{ $item->masa_simpan ? $item->masa_simpan.' Hari' : 'Tidak ditentukan.' }}</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            
            <div class="modal-footer border-top bg-light p-3 d-flex justify-content-between">
                <button class="btn btn-light border fw-medium" type="button" data-bs-dismiss="modal">Tutup</button>
                <!-- Tombol Edit yang men-trigger Modal Edit -->
                <button class="btn fw-bold px-4 shadow-sm text-white d-flex align-items-center gap-2" type="button" data-bs-target="#modalProdukEdit{{ $item->id }}" data-bs-toggle="modal" style="background: var(--brand-color, #dfba68);">
                    <i class="bi bi-pencil-square"></i> Edit Data Produk
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================== -->
<!-- MODAL: EDIT PRODUK (Di-looping)                -->
<!-- ============================================== -->
<div class="modal fade" id="modalProdukEdit{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form method="POST" action="{{ route('admin.produk.update',$item) }}" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-4">
            @csrf @method('PUT')
            <div class="modal-header border-bottom p-4 bg-white">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-1">Edit Produk: {{ $item->nama }}</h5>
                    <div class="text-muted small">Perbarui data produk tahu. Perubahan langsung aktif.</div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-4 p-md-5" style="background-color: #f9fafb;">
                @include('admin.produk._form', ['produk' => $item])
            </div>
            <div class="modal-footer border-top p-4 bg-white d-flex justify-content-end gap-2">
                <!-- Tombol Batal ini diarahkan kembali ke Modal Detail agar UX-nya nyambung -->
                <button class="btn btn-light border fw-medium px-4" type="button" data-bs-target="#modalProdukDetail{{ $item->id }}" data-bs-toggle="modal">Batal Edit</button>
                <button class="btn fw-bold px-4 shadow-sm text-white" type="submit" style="background: var(--brand-color, #dfba68);">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endforeach

@endsection