@extends('layouts.admin')
@section('title', 'Stok - SiTahu Admin')

@section('content')
@php
    $rupiah = fn ($value) => 'Rp'.number_format((float) $value, 0, ',', '.');
    $stokMeta = function ($item) {
        $min = (int) ($item->min_stok ?? 20);
        if ((int) $item->stok <= 0) return ['Stok habis', 'c-red', 'bi-x-circle'];
        if ((int) $item->stok <= $min) return ['Stok menipis', 'c-yellow', 'bi-exclamation-triangle'];
        return ['Stok aman', 'c-green', 'bi-check-circle'];
    };
    $tipeMeta = fn ($tipe) => match($tipe) {
        'tambah' => ['Masuk', 'c-green', '+'],
        'kurang' => ['Keluar', 'c-red', ''],
        default => ['Penyesuaian', 'c-yellow', '±'],
    };
    $openHistoryModal = request()->filled('riwayat_q') || request()->filled('tipe') || request()->filled('tanggal_mulai') || request()->filled('tanggal_selesai') || request()->filled('riwayat_page');
@endphp

<style>
    .stock-header { display: grid; grid-template-columns: minmax(0,1fr) auto; gap: 18px; align-items: end; padding: 20px; border: 1px solid #f1d49c; border-radius: 24px; background: radial-gradient(circle at 100% 100%, rgba(200,147,53,.14), transparent 18rem), linear-gradient(135deg,#fff,#fff8ea); box-shadow: var(--shadow-soft); margin-bottom: 18px; }
    .stock-header h1 { margin: 0; font-size: 1.26rem; font-weight: 950; letter-spacing: -.04em; }
    .stock-header p { margin: 6px 0 0; color: var(--muted); font-size: .84rem; font-weight: 700; }
    .stock-stat { min-height: 106px; padding: 16px; border: 1px solid var(--border); border-radius: 20px; background: #fff; box-shadow: var(--shadow-soft); }
    .stock-stat .label { color: var(--muted); font-size: .72rem; font-weight: 950; text-transform: uppercase; letter-spacing: .06em; }
    .stock-stat .value { margin-top: 8px; font-size: 1.36rem; font-weight: 950; letter-spacing: -.04em; line-height: 1; }
    .stock-stat .note { margin-top: 7px; color: var(--muted); font-size: .72rem; font-weight: 800; }
    .filter-panel { padding: 15px; border-bottom: 1px solid var(--border); background: #fff; }
    .product-photo { width: 54px; height: 54px; border-radius: 16px; border: 1px solid var(--border); object-fit: cover; background: #f8fafc; }
    .photo-empty { width: 54px; height: 54px; border-radius: 16px; border: 1px solid var(--border); background: linear-gradient(135deg,#fff8ea,#f8fafc); color: var(--brand-dark); display: inline-flex; align-items: center; justify-content: center; }
    .stock-bar { width: 150px; height: 8px; border-radius: 999px; background: #eef2f7; overflow: hidden; }
    .stock-bar span { display: block; height: 100%; border-radius: 999px; background: var(--brand); }
    .history-line { display: grid; grid-template-columns: minmax(0,1fr) auto; gap: 12px; align-items: center; padding: 13px 0; border-bottom: 1px solid #f0f1f3; }
    .history-line:last-child { border-bottom: 0; padding-bottom: 0; }
    .modal-history .modal-content { max-height: calc(100vh - 40px); overflow: hidden; }
    .modal-history .modal-body { overflow-y: auto; }
    @media (max-width: 991px) { .stock-header { grid-template-columns: 1fr; } }
</style>

<div class="stock-header">
    <div>
        <span class="chip c-yellow mb-3"><i class="bi bi-boxes"></i> Kontrol stok</span>
        <h1>Stok produk</h1>
        <p>Data stok produk.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap justify-content-lg-end">
        <a href="{{ route('admin.produk.index') }}" class="btn btn-light border"><i class="bi bi-basket2 me-1"></i> Katalog Produk</a>
        <button class="btn btn-brand" type="button" data-bs-toggle="modal" data-bs-target="#modalRiwayatStok"><i class="bi bi-clock-history me-1"></i> Riwayat Stok</button>
    </div>
</div>

<div class="grid g4 mb-3">
    <div class="stock-stat">
        <div class="label">Total stok</div>
        <div class="value">{{ number_format($stats['total_stok']) }}</div>
        <div class="note">Akumulasi seluruh produk</div>
    </div>
    <div class="stock-stat">
        <div class="label">Stok aman</div>
        <div class="value">{{ $stats['aman'] }}</div>
        <div class="note">Produk di atas batas minimal</div>
    </div>
    <div class="stock-stat">
        <div class="label">Stok menipis</div>
        <div class="value">{{ $stats['menipis'] }}</div>
        <div class="note">Perlu ditambah sebelum habis</div>
    </div>
    <div class="stock-stat">
        <div class="label">Stok habis</div>
        <div class="value">{{ $stats['habis'] }}</div>
        <div class="note">Prioritas restock hari ini</div>
    </div>
</div>

<div class="page-card overflow-hidden mb-4">
    <form id="page-filter" class="js-instant-filter filter-panel" method="GET">
        <div class="row g-3 align-items-end">
            <div class="col-12 col-xl-5">
                <label class="form-label small fw-bold text-muted">Cari produk</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input class="form-control border-start-0" type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama produk yang ingin diperbarui stoknya">
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <label class="form-label small fw-bold text-muted">Kondisi stok</label>
                <select class="form-select" name="filter">
                    <option value="">Semua kondisi</option>
                    <option value="aman" @selected(request('filter') === 'aman')>Stok aman</option>
                    <option value="menipis" @selected(request('filter') === 'menipis')>Stok menipis</option>
                    <option value="habis" @selected(request('filter') === 'habis')>Stok habis</option>
                </select>
            </div>
            <div class="col-12 col-sm-6 col-xl-2">
                <label class="form-label small fw-bold text-muted">Urutkan</label>
                <select class="form-select" name="sort">
                    <option value="">Terbaru</option>
                    <option value="nama" @selected(request('sort') === 'nama')>Nama A-Z</option>
                    <option value="stok_terendah" @selected(request('sort') === 'stok_terendah')>Stok terendah</option>
                    <option value="stok_terbanyak" @selected(request('sort') === 'stok_terbanyak')>Stok terbanyak</option>
                </select>
            </div>
            <div class="col-12 col-xl-2">
                <a href="{{ route('admin.stok.index') }}" class="btn btn-light border w-100"><i class="bi bi-arrow-counterclockwise me-1"></i> Reset</a>
            </div>
        </div>
    </form>

    <div class="table-wrap bg-white">
        <table class="mb-0">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Stok aktual</th>
                    <th>Batas minimal</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($produk as $item)
                @php
                    [$label, $class, $icon] = $stokMeta($item);
                    $min = max(1, (int) ($item->min_stok ?? 20));
                    $bar = $item->stok <= 0 ? 0 : min(100, round(($item->stok / max($min * 2, 1)) * 100));
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
                                <div class="fw-black">{{ $item->nama }}</div>
                                <span class="sub">{{ $rupiah($item->harga) }} / {{ $item->satuan }}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <strong class="fs-6">{{ $item->stok }}</strong>
                            <span class="text-muted small fw-bold">{{ $item->satuan }}</span>
                        </div>
                        <div class="stock-bar"><span style="width: {{ $bar }}%"></span></div>
                    </td>
                    <td>
                        <strong>{{ $item->min_stok ?? 20 }}</strong>
                        <span class="sub">Batas peringatan</span>
                    </td>
                    <td><span class="chip {{ $class }}"><i class="bi {{ $icon }}"></i> {{ $label }}</span></td>
                    <td>
                        <div class="actions justify-content-end">
                            <button class="btn btn-brand btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#modalUpdateStok{{ $item->id }}">
                                <i class="bi bi-pencil-square me-1"></i> Update
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="fw-bold mb-1">Data stok belum ditemukan</div>
                        <div class="text-muted small">Ubah filter atau tambahkan produk terlebih dahulu.</div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3 mb-4">{{ $produk->links() }}</div>

<div class="modal fade" id="modalRiwayatStok" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-history">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 px-4 pt-4">
                <div>
                    <h5 class="modal-title fw-black">Riwayat pergerakan stok</h5>
                    <p class="text-muted small mb-0">Mutasi stok.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body px-0 pt-0">
                <form class="filter-panel" method="GET">
                    <input type="hidden" name="q" value="{{ request('q') }}">
                    <input type="hidden" name="filter" value="{{ request('filter') }}">
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-lg-4">
                            <label class="form-label small fw-bold text-muted">Cari produk</label>
                            <input class="form-control" type="search" name="riwayat_q" value="{{ request('riwayat_q') }}" placeholder="Nama produk">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-2">
                            <label class="form-label small fw-bold text-muted">Tipe</label>
                            <select class="form-select" name="tipe">
                                <option value="">Semua tipe</option>
                                <option value="tambah" @selected(request('tipe') === 'tambah')>Masuk</option>
                                <option value="kurang" @selected(request('tipe') === 'kurang')>Keluar</option>
                                <option value="penyesuaian" @selected(request('tipe') === 'penyesuaian')>Penyesuaian</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-2">
                            <label class="form-label small fw-bold text-muted">Dari</label>
                            <input class="form-control" type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-2">
                            <label class="form-label small fw-bold text-muted">Sampai</label>
                            <input class="form-control" type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-2 d-flex gap-2">
                            <button class="btn btn-brand flex-grow-1" type="submit"><i class="bi bi-funnel me-1"></i> Filter</button>
                            <a href="{{ route('admin.stok.index') }}" class="btn btn-light border" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a>
                        </div>
                    </div>
                </form>

                <div class="px-4 py-3 bg-white">
                    @forelse($riwayat as $log)
                        @php
                            [$tipeLabel, $tipeClass, $prefix] = $tipeMeta($log->tipe);
                        @endphp
                        <div class="history-line">
                            <div class="min-w-0">
                                <div class="d-flex gap-2 align-items-center flex-wrap">
                                    <span class="chip {{ $tipeClass }}">{{ $tipeLabel }}</span>
                                    <strong>{{ $log->produk->nama ?? 'Produk dihapus' }}</strong>
                                    <span class="text-muted small">{{ $log->created_at?->format('d M Y H:i') }}</span>
                                </div>
                                <div class="text-muted small mt-1">{{ $log->catatan ?: '-' }}</div>
                            </div>
                            <div class="text-end fw-black {{ $log->perubahan < 0 ? 'text-danger' : 'text-success' }}">
                                {{ $log->perubahan > 0 ? '+' : '' }}{{ $log->perubahan }}
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <div class="fw-bold mb-1">Belum ada riwayat stok</div>
                            <div class="text-muted small">Riwayat akan muncul setelah admin memperbarui stok produk.</div>
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="modal-footer border-0 bg-light px-4 py-3 d-block">
                {{ $riwayat->links() }}
            </div>
        </div>
    </div>
</div>

@foreach($produk as $item)
<div class="modal fade" id="modalUpdateStok{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <form method="POST" action="{{ route('admin.stok.update', $item) }}">
                @csrf
                @method('PATCH')
                <div class="modal-header border-0 px-4 pt-4">
                    <div>
                        <h5 class="modal-title fw-black">Update stok</h5>
                        <p class="text-muted small mb-0">{{ $item->nama }} · stok saat ini {{ $item->stok }} {{ $item->satuan }}</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Jenis perubahan</label>
                        <select class="form-select" name="tipe" required>
                            <option value="tambah">Tambah stok masuk</option>
                            <option value="kurang">Kurangi stok keluar/rusak</option>
                            <option value="penyesuaian">Penyesuaian stok final</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Jumlah</label>
                        <input class="form-control" type="number" name="jumlah" min="0" required placeholder="0">
                        <div class="text-muted small mt-2">Untuk penyesuaian, isi jumlah stok akhir yang benar.</div>
                    </div>
                    <div>
                        <label class="form-label small fw-bold text-muted">Catatan</label>
                        <textarea class="form-control" name="catatan" rows="3" placeholder="Catatan stok"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light px-4 py-3">
                    <button class="btn btn-light border" type="button" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-brand px-4" type="submit"><i class="bi bi-save me-1"></i> Simpan Stok</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@if($openHistoryModal)
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('modalRiwayatStok');
        if (modal && window.bootstrap) {
            bootstrap.Modal.getOrCreateInstance(modal).show();
        }
    });
</script>
@endif
@endsection
