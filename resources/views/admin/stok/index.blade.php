@extends('layouts.admin')
@section('title', 'Manajemen Stok - SiTahu')

@section('content')
<style>
    /* Styling Standar E-Commerce Enterprise */
    .sc-box { border: 1px solid #e5e7eb; border-radius: 0.75rem; background: #fff; margin-bottom: 1.5rem; overflow: hidden; box-shadow: 0 1px 2px rgba(0,0,0,0.02); }
    
    /* Metrik Ringkasan di Atas */
    .metric-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1.5rem; display: flex; flex-direction: column; justify-content: center; position: relative; overflow: hidden; transition: all 0.2s; }
    .metric-card:hover { border-color: var(--brand-color, #dfba68); box-shadow: 0 4px 12px rgba(223, 186, 104, 0.15); transform: translateY(-2px); }
    .metric-label { font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; margin-bottom: 0.5rem; }
    .metric-value { font-size: 1.85rem; font-weight: 800; letter-spacing: -0.03em; color: #111827; line-height: 1.1; }
    .metric-icon { position: absolute; right: -10px; bottom: -15px; font-size: 5rem; opacity: 0.04; color: #111827; }

    /* Form & Search Bar Modern */
    .form-label-modern { font-size: 0.8rem; font-weight: 700; color: #374151; margin-bottom: 0.4rem; display: block; }
    .form-control-modern, .form-select-modern { background-color: #f9fafb; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.6rem 0.75rem; font-size: 0.9rem; transition: all 0.2s; box-shadow: none; }
    .form-control-modern:focus, .form-select-modern:focus { background-color: #ffffff; border-color: var(--brand-color, #dfba68); box-shadow: 0 0 0 3px rgba(223, 186, 104, 0.15); outline: none; }
    
    .search-bar-modern { background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 0.75rem; transition: all 0.2s; }
    .search-bar-modern:focus-within { background-color: #ffffff; border-color: var(--brand-color, #dfba68); box-shadow: 0 0 0 3px rgba(223, 186, 104, 0.15); }
    .search-bar-modern input { background: transparent; border: none; box-shadow: none; outline: none; width: 100%; }
    
    /* Tabel Enterprise & Ikon */
    .table-enterprise th { border-bottom: 2px solid #e5e7eb; color: #6b7280; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 1rem 1.5rem; font-weight: 600; background: #fafafa; }
    .table-enterprise td { vertical-align: middle; padding: 1rem 1.5rem; border-bottom: 1px solid #f3f4f6; color: #111827; }
    .table-enterprise tbody tr:hover { background-color: #f9fafb; }
    
    .thumb-box { width: 44px; height: 44px; border-radius: 0.5rem; background-color: #f3f4f6; border: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; color: #9ca3af; flex-shrink: 0; }
    
    /* List Riwayat Modal */
    .history-item { transition: background-color 0.15s ease; border-bottom: 1px solid #e5e7eb; padding: 1.25rem 1.5rem; }
    .history-item:last-child { border-bottom: none; }
    .history-item:hover { background-color: #f9fafb; }
</style>

<!-- HEADER UTAMA -->
<div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3 mb-4">
    <div>
        <h1 class="h4 text-title mb-1 fw-bold text-dark">Manajemen Stok</h1>
        <p class="text-muted small mb-0">Pantau stok aktual dan batas stok minimal untuk mendeteksi barang menipis.</p>
    </div>
    <div>
        <button class="btn shadow-sm fw-bold px-4 text-white d-flex align-items-center gap-2" type="button" data-bs-toggle="modal" data-bs-target="#modalRiwayatGlobal" style="background: var(--brand-color, #dfba68);">
            <i class="bi bi-clock-history"></i> Riwayat Pergerakan Stok
        </button>
    </div>
</div>

<!-- BARIS 1: KARTU METRIK -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="metric-card h-100">
            <div class="metric-label">Total Produk</div>
            <div class="metric-value">{{ $stats['total'] }} <span class="fs-6 fw-normal text-muted">SKU</span></div>
            <i class="bi bi-box-seam metric-icon"></i>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="metric-card h-100" style="border-left: 4px solid #10b981;">
            <div class="metric-label text-success">Stok Aman</div>
            <div class="metric-value">{{ $stats['aman'] }} <span class="fs-6 fw-normal text-muted">Item</span></div>
            <i class="bi bi-check-circle metric-icon"></i>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="metric-card h-100" style="border-left: 4px solid #f59e0b;">
            <div class="metric-label text-warning">Stok Menipis</div>
            <div class="metric-value">{{ $stats['menipis'] }} <span class="fs-6 fw-normal text-muted">Item</span></div>
            <i class="bi bi-exclamation-triangle metric-icon"></i>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="metric-card h-100" style="border-left: 4px solid #ef4444;">
            <div class="metric-label text-danger">Stok Habis</div>
            <div class="metric-value">{{ $stats['habis'] }} <span class="fs-6 fw-normal text-muted">Item</span></div>
            <i class="bi bi-x-octagon metric-icon"></i>
        </div>
    </div>
</div>

<!-- BARIS 2: TABEL & FILTER UTAMA -->
<div class="sc-box mb-4">
    <!-- Area Filter -->
    <div class="bg-white border-bottom p-3 p-md-4">
        <form id="page-filter" method="GET" class="row g-3 align-items-end">
            <div class="col-12 col-lg">
                <label class="form-label-modern">Cari Produk</label>
                <div class="search-bar-modern d-flex align-items-center px-3 py-1">
                    <i class="bi bi-search text-muted"></i>
                    <input class="form-control px-2 py-2" type="search" name="q" value="{{ request('q') }}" placeholder="Ketik nama produk yang ingin diupdate...">
                </div>
            </div>
            <div class="col-12 col-md-5 col-lg-3">
                <label class="form-label-modern">Saring Status Stok</label>
                <select class="form-select form-select-modern fw-medium" name="filter" onchange="this.form.submit()">
                    <option value="">Semua Kondisi</option>
                    <option value="menipis" @selected(request('filter')==='menipis')>Stok Menipis (Di Bawah Minimal)</option>
                    <option value="habis" @selected(request('filter')==='habis')>Stok Habis (Kosong)</option>
                </select>
            </div>
            <div class="col-12 col-md-auto d-none d-md-block">
                <button class="btn btn-dark fw-medium px-4" type="submit" style="border-radius: 0.5rem; min-height: 42px;">Terapkan</button>
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
                    <th>Sisa Stok Aktual</th>
                    <th>Batas Minimal</th>
                    <th class="pe-4 text-end">Aksi Logistik</th>
                </tr>
            </thead>
            <tbody>
            @forelse($produk as $item)
                @php
                    $minStok = (int)($item->min_stok ?? 20);
                    $statusClass = $item->stok <= 0 ? 'bg-danger-subtle text-danger-emphasis' : ($item->stok <= $minStok ? 'bg-warning-subtle text-warning-emphasis' : 'bg-success-subtle text-success-emphasis');
                    $statusLabelStok = $item->stok <= 0 ? 'Habis' : ($item->stok <= $minStok ? 'Menipis' : 'Aman');
                @endphp
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="thumb-box shadow-sm"><i class="bi bi-box-seam text-muted"></i></div>
                            <div>
                                <strong class="d-block text-dark mb-1" style="font-size: 0.95rem;">{{ $item->nama }}</strong>
                                <span class="text-muted small">ID: #{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</span>
                            </div>
                        </div>
                    </td>
                    <td><strong class="text-dark">{{ $rupiah($item->harga) }}</strong></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <strong class="fs-6 text-dark">{{ $item->stok }}</strong>
                            <span class="badge rounded-pill fw-medium {{ $statusClass }}">{{ $statusLabelStok }}</span>
                        </div>
                    </td>
                    <td><span class="text-muted fw-medium">{{ $minStok }} Unit</span></td>
                    <td class="pe-4 text-end">
                        <button class="btn btn-sm btn-light border rounded-3 px-3 py-2 fw-medium shadow-sm" type="button" data-bs-toggle="modal" data-bs-target="#modalUpdateStok{{ $item->id }}">
                            <i class="bi bi-pencil-square me-1 text-muted"></i> Sesuaikan Stok
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted mb-3 d-block"></i>
                        <strong class="text-dark d-block mb-1">Tidak ada produk ditemukan.</strong>
                        <span class="text-muted small">Coba ubah kata kunci atau bersihkan filter.</span>
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
<!-- MODAL: UPDATE STOK (Di-looping)                -->
<!-- ============================================== -->
@foreach($produk as $item)
<div class="modal fade" id="modalUpdateStok{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form method="POST" action="{{ route('admin.stok.update', $item) }}" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" data-confirm-title="Update Stok Produk" data-confirm-message="Yakin ingin menyimpan perubahan stok {{ $item->nama }}?" data-confirm-button="Simpan Stok">
            @csrf @method('PATCH')
            <div class="modal-header border-bottom p-4 bg-white">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-1">Sesuaikan Stok Produk</h5>
                    <p class="text-muted small mb-0">{{ $item->nama }}</p>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            
            <div class="modal-body p-4 bg-light">
                <div class="bg-white p-4 rounded-4 shadow-sm border border-light">
                    <!-- Highlight Sisa Stok -->
                    <div class="d-flex justify-content-between align-items-center p-3 rounded-3 mb-4" style="background: var(--brand-active-bg, #fbf1d4); border: 1px solid var(--brand-color, #dfba68);">
                        <span class="fw-semibold text-dark">Stok Tercatat Saat Ini</span>
                        <strong class="fs-3 lh-1 text-dark">{{ $item->stok }}</strong>
                    </div>
                    
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label-modern">Aktivitas Pembaruan <span class="text-danger">*</span></label>
                            <select class="form-select form-select-modern" name="tipe" required>
                                <option value="tambah">Penambahan Stok (+)</option>
                                <option value="kurang">Pengurangan Stok (-)</option>
                                <option value="penyesuaian">Sesuaikan Aktual Menjadi (=)</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label-modern">Jumlah Unit <span class="text-danger">*</span></label>
                            <input class="form-control form-control-modern" type="number" min="0" name="jumlah" placeholder="Contoh: 10" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label-modern">Catatan / Alasan (Opsional)</label>
                            <textarea class="form-control form-control-modern" rows="2" name="catatan" placeholder="Misal: Barang masuk dari supplier, atau ada barang rusak..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer border-top p-4 bg-white">
                <button class="btn btn-light border fw-medium px-4" type="button" data-bs-dismiss="modal">Batal</button>
                <button class="btn fw-bold px-4 text-white" type="submit" style="background: var(--brand-color, #dfba68);">Terapkan Stok Baru</button>
            </div>
        </form>
    </div>
</div>
@endforeach


<!-- ============================================== -->
<!-- MODAL: RIWAYAT STOK GLOBAL                     -->
<!-- ============================================== -->
<div class="modal fade" id="modalRiwayatGlobal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            
            <div class="modal-header border-bottom p-4 bg-white d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-1">Riwayat Pergerakan Stok</h5>
                    <p class="text-muted small mb-0">Catatan aktivitas masuk dan keluarnya barang ke gudang.</p>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="border-bottom bg-light p-3 p-md-4">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-lg">
                        <label class="form-label-modern">Cari Produk / Catatan</label>
                        <div class="search-bar-modern d-flex align-items-center px-3 py-1 bg-white">
                            <i class="bi bi-search text-muted"></i>
                            <input class="form-control px-2 py-2" type="search" id="filterRiwayatQ" placeholder="Ketik keyword di sini...">
                        </div>
                    </div>
                    <div class="col-12 col-md-4 col-lg-2">
                        <label class="form-label-modern">Jenis Transaksi</label>
                        <select class="form-select form-select-modern bg-white" id="filterRiwayatTipe">
                            <option value="">Semua Tipe</option>
                            <option value="tambah">Penambahan</option>
                            <option value="kurang">Pengurangan</option>
                            <option value="penyesuaian">Penyesuaian</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-4 col-lg-2">
                        <label class="form-label-modern">Dari Tanggal</label>
                        <input class="form-control form-control-modern bg-white" type="date" id="filterRiwayatMulai">
                    </div>
                    <div class="col-12 col-md-4 col-lg-2">
                        <label class="form-label-modern">Sampai Tanggal</label>
                        <input class="form-control form-control-modern bg-white" type="date" id="filterRiwayatSelesai">
                    </div>
                    <div class="col-12 col-lg-auto">
                        <button class="btn btn-dark w-100 fw-medium" type="button" id="resetFilterRiwayat" style="border-radius: 0.5rem; min-height: 42px;">
                            Reset Filter
                        </button>
                    </div>
                </div>
            </div>

            <div class="modal-body p-0 bg-white" id="riwayatStokList">
                @forelse($riwayat as $log)
                    @php
                        $namaProduk = $log->produk?->nama ?? 'Produk Dihapus';
                        $catatanLog = $log->catatan ?? 'Tanpa catatan';
                        $tanggalLog = $log->created_at->format('Y-m-d');
                    @endphp
                    <div class="history-item js-history-item"
                         data-name="{{ strtolower($namaProduk) }}"
                         data-note="{{ strtolower($catatanLog) }}"
                         data-type="{{ $log->tipe }}"
                         data-date="{{ $tanggalLog }}">
                        <div class="d-flex justify-content-between align-items-center gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="thumb-box bg-light shadow-sm"><i class="bi bi-clock-history text-muted"></i></div>
                                <div>
                                    <strong class="d-block text-dark mb-1" style="font-size: 0.95rem;">{{ $namaProduk }}</strong>
                                    <div class="text-muted d-flex align-items-center flex-wrap gap-2 mb-1" style="font-size: 0.8rem;">
                                        <span class="badge bg-light text-secondary border fw-medium px-2 py-1">{{ $statusLabel($log->tipe) }}</span>
                                        <span>•</span>
                                        <span class="text-truncate">{{ $catatanLog }}</span>
                                    </div>
                                    <div class="text-secondary small">
                                        <i class="bi bi-calendar3 me-1"></i> {{ $log->created_at->format('d M Y, H:i') }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-end flex-shrink-0">
                                <span class="badge rounded-pill px-3 py-2 fs-6 shadow-sm {{ $log->perubahan > 0 ? 'bg-success-subtle text-success-emphasis' : ($log->perubahan < 0 ? 'bg-danger-subtle text-danger-emphasis' : 'bg-light text-secondary border') }}">
                                    {{ $log->perubahan > 0 ? '+' : '' }}{{ $log->perubahan }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-5 text-center js-history-empty-source">
                        <i class="bi bi-clipboard-x fs-1 text-muted mb-3 d-block"></i>
                        <strong class="text-dark">Belum ada riwayat tercatat.</strong>
                    </div>
                @endforelse

                <div class="p-5 text-center d-none" id="riwayatStokEmptyFiltered">
                    <i class="bi bi-search fs-1 text-muted mb-3 d-block"></i>
                    <strong class="text-dark">Data riwayat tidak ditemukan.</strong>
                    <div class="text-muted small mt-1">Silakan sesuaikan filter pencarian atau rentang tanggal Anda.</div>
                </div>
            </div>

            <div class="modal-footer bg-light border-top p-4 d-flex justify-content-between align-items-center">
                <span class="text-muted small fw-medium" id="riwayatStokCounter">Menampilkan riwayat stok terbaru.</span>
                <span class="text-muted small">Max 250 Riwayat <i class="bi bi-info-circle ms-1" title="Untuk performa sistem, hanya 250 log terbaru yang ditampilkan di modal ini."></i></span>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPT FILTER RIWAYAT -->
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const q = document.getElementById('filterRiwayatQ');
    const tipe = document.getElementById('filterRiwayatTipe');
    const mulai = document.getElementById('filterRiwayatMulai');
    const selesai = document.getElementById('filterRiwayatSelesai');
    const reset = document.getElementById('resetFilterRiwayat');
    const items = Array.from(document.querySelectorAll('.js-history-item'));
    const emptyFiltered = document.getElementById('riwayatStokEmptyFiltered');
    const counter = document.getElementById('riwayatStokCounter');

    function filterRiwayatStok() {
        const keyword = (q?.value || '').toLowerCase().trim();
        const selectedTipe = tipe?.value || '';
        const startDate = mulai?.value || '';
        const endDate = selesai?.value || '';
        let visibleCount = 0;

        items.forEach((item) => {
            const textMatch = !keyword || item.dataset.name.includes(keyword) || item.dataset.note.includes(keyword);
            const typeMatch = !selectedTipe || item.dataset.type === selectedTipe;
            const dateValue = item.dataset.date || '';
            const startMatch = !startDate || dateValue >= startDate;
            const endMatch = !endDate || dateValue <= endDate;
            const visible = textMatch && typeMatch && startMatch && endMatch;

            item.classList.toggle('d-none', !visible);
            if (visible) visibleCount++;
        });

        emptyFiltered?.classList.toggle('d-none', visibleCount !== 0 || items.length === 0);
        if (counter) {
            counter.textContent = items.length
                ? `Menampilkan ${visibleCount} hasil dari riwayat stok.`
                : 'Belum ada riwayat stok.';
        }
    }

    [q, tipe, mulai, selesai].forEach((field) => {
        field?.addEventListener('input', filterRiwayatStok);
        field?.addEventListener('change', filterRiwayatStok);
    });

    reset?.addEventListener('click', () => {
        if (q) q.value = '';
        if (tipe) tipe.value = '';
        if (mulai) mulai.value = '';
        if (selesai) selesai.value = '';
        filterRiwayatStok();
    });

    filterRiwayatStok();
});
</script>
@endpush

@endsection