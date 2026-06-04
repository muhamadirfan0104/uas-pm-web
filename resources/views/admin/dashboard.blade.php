@extends('layouts.admin')
@section('title', 'Dashboard Admin - SiTahu')

@section('content')

<style>
    /* Tipografi & Warna Khusus E-commerce */
    .text-title { color: #111827; font-weight: 700; letter-spacing: -0.02em; }
    .text-subtitle { color: #6b7280; font-size: 0.85rem; }
    
    /* Kartu To-Do (Operasional) */
    .todo-card {
        transition: all 0.2s ease;
        border: 1px solid #e5e7eb;
        background-color: #ffffff;
        text-decoration: none;
    }
    .todo-card:hover {
        border-color: var(--brand-color, #dfba68);
        box-shadow: 0 4px 12px rgba(223, 186, 104, 0.15);
        transform: translateY(-2px);
    }
    .todo-value { font-size: 1.75rem; font-weight: 800; color: #111827; line-height: 1; }
    
    /* Summary Panel (Samping Grafik) */
    .summary-block { padding: 1.25rem 0; border-bottom: 1px dashed #e5e7eb; }
    .summary-block:last-child { border-bottom: none; }
    
    /* Grafik Batang */
    .chart-container { height: 240px; display: flex; align-items: flex-end; gap: 0.75rem; padding: 1rem 0; }
    .bar-wrapper { flex: 1; height: 100%; display: flex; flex-direction: column; justify-content: flex-end; align-items: center; gap: 0.5rem; }
    .bar-fill { 
        width: 100%; max-width: 40px; border-radius: 4px 4px 0 0; 
        background: var(--brand-active-bg, #fbf1d4); 
        position: relative; transition: height 0.4s ease; 
    }
    .bar-fill:hover { background: var(--brand-color, #dfba68); }
    .bar-fill::after { 
        content: attr(data-v); position: absolute; top: -22px; left: 50%; transform: translateX(-50%); 
        font-size: 0.7rem; color: #6b7280; font-weight: 700; opacity: 0; transition: opacity 0.2s;
    }
    .bar-fill:hover::after { opacity: 1; }
    .bar-label { font-size: 0.75rem; color: #9ca3af; font-weight: 500; text-align: center; }

    /* List Item Modern */
    .list-row { transition: background-color 0.15s ease; border-bottom: 1px solid #f3f4f6; }
    .list-row:last-child { border-bottom: none; }
    .list-row:hover { background-color: #f9fafb; }
    
    .thumb-box { 
        width: 44px; height: 44px; border-radius: 0.5rem; background-color: #f8f9fa; border: 1px solid #e5e7eb;
        display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700; color: #9ca3af; flex-shrink: 0;
    }
</style>

<div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3 mb-4">
    <div>
        <h1 class="h4 text-title mb-1">Tinjauan Toko</h1>
        <p class="text-subtitle mb-0">Pantau operasional dan performa SiTahu untuk periode {{ $periodeLabel }}.</p>
    </div>
    <div class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center gap-2">
        <form class="js-instant-filter d-flex gap-2 bg-white border rounded-3 shadow-sm p-2" method="GET">
            <select class="form-select form-select-sm border-0 bg-light fw-medium" name="bulan" aria-label="Filter bulan">
                @foreach($daftarBulan as $nomorBulan => $namaBulan)
                    <option value="{{ $nomorBulan }}" @selected($filterBulan == $nomorBulan)>{{ $namaBulan }}</option>
                @endforeach
            </select>
            <select class="form-select form-select-sm border-0 bg-light fw-medium" name="tahun" aria-label="Filter tahun">
                @foreach($daftarTahun as $tahun)
                    <option value="{{ $tahun }}" @selected($filterTahun == $tahun)>{{ $tahun }}</option>
                @endforeach
            </select>
        </form>

        <a class="btn btn-light border shadow-sm fw-medium px-3" href="{{ route('admin.stok.index') }}">
            <i class="bi bi-box-seam me-1 text-muted"></i> Kelola Stok
        </a>
        <a class="btn shadow-sm fw-bold px-3" href="{{ route('admin.produk.create') }}" style="background: var(--brand-color, #dfba68); color: #fff;">
            <i class="bi bi-plus-lg me-1"></i> Produk
        </a>
    </div>
</div>

<h2 class="h6 fw-bold mb-3 text-dark">Perlu Tindakan</h2>
<div class="row g-3 mb-5">
    <div class="col-6 col-xl-3">
        <a href="{{ route('admin.pembayaran.index') }}" class="todo-card d-block p-3 p-xl-4 rounded-4">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-subtitle fw-medium">Menunggu Bayar</span>
                <i class="bi bi-credit-card text-warning fs-5"></i>
            </div>
            <div class="todo-value">{{ $stats['menunggu_pembayaran'] }}</div>
        </a>
    </div>
    
    <div class="col-6 col-xl-3">
        <a href="{{ route('admin.pesanan.index') }}" class="todo-card d-block p-3 p-xl-4 rounded-4">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-subtitle fw-medium">Pesanan Diproses</span>
                <i class="bi bi-box-seam text-info fs-5"></i>
            </div>
            <div class="todo-value">{{ $stats['pesanan_diproses'] }}</div>
        </a>
    </div>

    <div class="col-6 col-xl-3">
        <a href="{{ route('admin.stok.index') }}" class="todo-card d-block p-3 p-xl-4 rounded-4">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-subtitle fw-medium">Stok Menipis</span>
                <i class="bi bi-exclamation-triangle text-danger fs-5"></i>
            </div>
            <div class="todo-value">{{ $stats['stok_menipis'] + $stats['stok_habis'] }}</div>
        </a>
    </div>

    <div class="col-6 col-xl-3">
        <a href="{{ route('admin.ulasan.index') }}" class="todo-card d-block p-3 p-xl-4 rounded-4">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-subtitle fw-medium">Ulasan Pembeli</span>
                <i class="bi bi-star text-success fs-5"></i>
            </div>
            <div class="todo-value">{{ $stats['total_ulasan'] }}</div>
        </a>
    </div>
</div>

<h2 class="h6 fw-bold mb-3 text-dark">Performa Bisnis</h2>
<div class="row g-3 mb-5">
    
    <div class="col-12 col-xl-8">
        <div class="card h-100 border border-light shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-bottom-0 p-4 pb-0 d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="h6 mb-0 fw-bold text-dark">Penjualan Harian {{ $periodeLabel }}</h3>
                    <p class="text-subtitle mt-1 mb-0">Pendapatan dibayar berdasarkan bulan dan tahun terpilih.</p>
                </div>
                <a href="{{ route('admin.laporan.index') }}" class="btn btn-sm btn-light border fw-medium px-3">Laporan</a>
            </div>
            <div class="card-body px-4 pb-4 pt-0">
                <div class="chart-container">
                    @foreach($penjualanMingguan as $item)
                        @php($height = max(5, round(($item['total'] / $maxPenjualan) * 100)))
                        <div class="bar-wrapper">
                            <div class="bar-fill" data-v="{{ number_format($item['total']/1000,0,',','.') }}k" style="height: {{ $height }}%;"></div>
                            <div class="bar-label">{{ $item['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-4">
        <div class="card h-100 border border-light shadow-sm rounded-4 overflow-hidden p-4">
            <h3 class="h6 mb-4 fw-bold text-dark">Ringkasan {{ $periodeLabel }}</h3>
            
            <div class="d-flex flex-column h-100 justify-content-center">
                <div class="summary-block pt-0">
                    <div class="text-subtitle mb-1">Total Pendapatan</div>
                    <div class="d-flex align-items-end justify-content-between">
                        <div class="fs-3 fw-bold text-dark lh-1">{{ $rupiah($stats['penjualan_bulan_ini']) }}</div>
                        <span class="badge bg-success-subtle text-success-emphasis rounded-pill">Semua: {{ $rupiah($stats['total_penjualan_all']) }}</span>
                    </div>
                </div>

                <div class="summary-block">
                    <div class="text-subtitle mb-1">Pesanan Berhasil</div>
                    <div class="d-flex align-items-end justify-content-between">
                        <div class="fs-3 fw-bold text-dark lh-1">{{ $stats['pesanan_hari_ini'] }} <span class="fs-6 fw-normal text-muted">hari ini</span></div>
                        <span class="badge bg-light text-dark border rounded-pill">Semua: {{ $stats['total_pesanan_all'] }}</span>
                    </div>
                </div>

                <div class="summary-block pb-0 border-0">
                    <div class="text-subtitle mb-1">Basis Pelanggan</div>
                    <div class="d-flex align-items-end justify-content-between">
                        <div class="fs-3 fw-bold text-dark lh-1">{{ $stats['total_pembeli'] }} <span class="fs-6 fw-normal text-muted">user</span></div>
                        <span class="badge bg-light text-dark border rounded-pill">{{ $stats['produk_aktif'] }} Produk Aktif</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-xl-6">
        <div class="card h-100 border border-light shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-bottom p-4 d-flex align-items-center justify-content-between">
                <h3 class="h6 mb-0 fw-bold text-dark">Pesanan Terbaru</h3>
                <a class="text-decoration-none fw-medium" style="color: var(--brand-color); font-size: 0.85rem;" href="{{ route('admin.pesanan.index') }}">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                @forelse($pesananTerbaru as $order)
                    <a href="#" class="list-row p-3 px-4 d-flex justify-content-between align-items-center gap-3 text-decoration-none">
                        <div>
                            <strong class="d-block text-dark mb-1" style="font-size: 0.9rem;">{{ $order->nomor_invoice }}</strong>
                            <div class="text-subtitle">{{ $order->user?->name ?? 'Pembeli' }}</div>
                        </div>
                        <div class="text-end">
                            <strong class="d-block text-dark mb-1" style="font-size: 0.9rem;">{{ $rupiah($order->total_bayar) }}</strong>
                            <span class="badge rounded-pill fw-medium {{ str_replace('text-bg-', 'bg-', $statusClass($order->status)) }}-subtle text-{{ str_replace('text-bg-', '', $statusClass($order->status)) }}-emphasis" style="font-size: 0.7rem;">
                                {{ $statusLabel($order->status) }}
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="p-5 text-center">
                        <div class="text-muted mb-2"><i class="bi bi-inbox fs-2"></i></div>
                        <strong class="d-block text-dark mb-1">Belum ada pesanan</strong>
                        <span class="text-subtitle">Menunggu pembeli melakukan checkout.</span>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-6">
        <div class="card h-100 border border-light shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-bottom p-4 d-flex align-items-center justify-content-between">
                <h3 class="h6 mb-0 fw-bold text-dark">Produk Terlaris</h3>
                <span class="text-subtitle">Berdasarkan unit terjual</span>
            </div>
            <div class="card-body p-0">
                @forelse($produkTerlaris as $produk)
                    <div class="list-row p-3 px-4 d-flex align-items-center gap-3">
                        <div class="text-muted fw-bold me-1" style="width: 20px;">#{{ $loop->iteration }}</div>
                        <div class="thumb-box"><i class="bi bi-box-seam"></i></div>
                        <div class="flex-grow-1 min-w-0">
                            <strong class="d-block text-dark text-truncate mb-1" style="font-size: 0.9rem;">{{ $produk->nama }}</strong>
                            <div class="text-subtitle">{{ $rupiah($produk->harga) }}</div>
                        </div>
                        <div class="text-end">
                            <strong class="d-block text-dark" style="font-size: 1rem;">{{ (int)($produk->total_terjual ?? 0) }}</strong>
                            <span class="text-subtitle" style="font-size: 0.7rem;">Terjual</span>
                        </div>
                    </div>
                @empty
                    <div class="p-5 text-center">
                        <div class="text-muted mb-2"><i class="bi bi-bar-chart fs-2"></i></div>
                        <strong class="d-block text-dark mb-1">Belum ada data</strong>
                        <span class="text-subtitle">Peringkat akan muncul setelah transaksi.</span>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection