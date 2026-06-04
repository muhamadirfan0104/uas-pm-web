@extends('layouts.admin')

@section('title', 'Dashboard Admin - SiTahu')
@section('page_title', 'Dashboard Admin')

@section('content')
@php
    $totalStokBermasalah = ($stats['stok_menipis'] ?? 0) + ($stats['stok_habis'] ?? 0);
    $totalPendapatanBulan = $stats['penjualan_bulan_ini'] ?? 0;
    $totalPendapatanAll = $stats['total_penjualan_all'] ?? 0;
    $totalPesananAll = $stats['total_pesanan_all'] ?? 0;
@endphp

<style>
    .dashboard-quick {
        display: grid;
        grid-template-columns: 1.35fr 0.65fr;
        gap: 16px;
        margin-bottom: 18px;
    }

    .welcome-panel {
        position: relative;
        overflow: hidden;
        padding: 24px;
        border-radius: 22px;
        border: 1px solid var(--border);
        background:
            radial-gradient(circle at top right, rgba(223, 186, 104, 0.24), transparent 38%),
            linear-gradient(135deg, #ffffff, #fff8e8);
        box-shadow: var(--shadow-sm);
    }

    .welcome-panel::after {
        content: "";
        position: absolute;
        width: 180px;
        height: 180px;
        right: -60px;
        bottom: -90px;
        border-radius: 999px;
        background: rgba(223, 186, 104, 0.18);
    }

    .welcome-kicker {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 11px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.8);
        border: 1px solid rgba(223, 186, 104, 0.25);
        color: var(--brand-dark);
        font-size: 0.76rem;
        font-weight: 850;
        margin-bottom: 13px;
    }

    .welcome-title {
        position: relative;
        margin: 0;
        max-width: 620px;
        color: var(--text);
        font-size: 1.65rem;
        font-weight: 950;
        letter-spacing: -0.05em;
        line-height: 1.08;
        z-index: 1;
    }

    .welcome-desc {
        position: relative;
        max-width: 620px;
        margin: 9px 0 0;
        color: var(--muted);
        font-size: 0.92rem;
        font-weight: 650;
        line-height: 1.6;
        z-index: 1;
    }

    .welcome-actions {
        position: relative;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 18px;
        z-index: 1;
    }

    .period-panel {
        padding: 18px;
        border-radius: 22px;
        border: 1px solid var(--border);
        background: #fff;
        box-shadow: var(--shadow-sm);
    }

    .period-panel label {
        color: var(--muted);
        font-size: 0.76rem;
        font-weight: 850;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 8px;
    }

    .period-title {
        margin-bottom: 14px;
        color: var(--text);
        font-weight: 900;
        letter-spacing: -0.03em;
    }

    .action-card {
        height: 100%;
        text-decoration: none;
        transition: 0.18s ease;
    }

    .action-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow);
        border-color: rgba(223, 186, 104, 0.42);
    }

    .action-icon {
        width: 44px;
        height: 44px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.12rem;
    }

   .action-value {
        margin-top: 10px;
        font-size: 1.65rem;
        line-height: 1;
        font-weight: 950;
        letter-spacing: -0.06em;
        color: var(--text);
    }

    .action-label {
        color: var(--muted);
        font-size: 0.82rem;
        font-weight: 800;
    }

    .dash-card-head {
        padding: 18px 18px 0;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .dash-card-title {
        margin: 0;
        color: var(--text);
        font-size: 1rem;
        font-weight: 900;
        letter-spacing: -0.03em;
    }

    .dash-card-desc {
        margin: 4px 0 0;
        color: var(--muted);
        font-size: 0.82rem;
        font-weight: 650;
    }

    .chart-box {
        height: 275px;
        padding: 28px 22px 20px;
        display: flex;
        align-items: flex-end;
        justify-content: center;
        gap: 18px;
        overflow: hidden;
        max-width: 100%;
    }

    .chart-item {
        flex: 1;
        max-width: 56px;
        height: 100%;
        min-width: 34px;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        align-items: center;
        gap: 8px;
    }

    .chart-bar {
        width: 100%;
        max-width: 42px;
        border-radius: 999px 999px 8px 8px;
        background: linear-gradient(180deg, var(--brand), #f0d58d);
        position: relative;
        min-height: 8px;
        transition: 0.18s ease;
    }

    .chart-bar:hover {
        transform: translateY(-3px);
        filter: brightness(0.98);
    }

    .chart-bar::before {
        content: attr(data-value);
        position: absolute;
        top: -27px;
        left: 50%;
        transform: translateX(-50%);
        padding: 4px 7px;
        border-radius: 999px;
        background: #111827;
        color: #fff;
        font-size: 0.68rem;
        font-weight: 850;
        opacity: 0;
        pointer-events: none;
        white-space: nowrap;
        transition: 0.16s ease;
    }

    .chart-bar:hover::before {
        opacity: 1;
    }

    .chart-label {
        color: var(--muted-2);
        font-size: 0.7rem;
        font-weight: 850;
    }

    .summary-list {
        padding: 4px 18px 18px;
    }

    .summary-item {
        padding: 15px 0;
        border-bottom: 1px dashed var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
    }

    .summary-item:last-child {
        border-bottom: 0;
    }

    .summary-label {
        color: var(--muted);
        font-size: 0.8rem;
        font-weight: 750;
    }

    .summary-value {
        margin-top: 3px;
        color: var(--text);
        font-size: 1.25rem;
        font-weight: 950;
        letter-spacing: -0.04em;
    }

    .summary-pill {
        padding: 7px 10px;
        border-radius: 999px;
        background: var(--brand-soft);
        color: var(--brand-dark);
        font-size: 0.72rem;
        font-weight: 900;
        white-space: nowrap;
    }

    .order-row,
    .product-row {
        padding: 15px 18px;
        border-bottom: 1px solid #f1f2f4;
        display: flex;
        align-items: center;
        gap: 13px;
        text-decoration: none;
        transition: 0.16s ease;
    }

    .order-row:last-child,
    .product-row:last-child {
        border-bottom: 0;
    }

    .order-row:hover,
    .product-row:hover {
        background: #fafafa;
    }

    .invoice-text,
    .product-name {
        color: var(--text);
        font-size: 0.9rem;
        font-weight: 900;
    }

    .small-muted {
        color: var(--muted);
        font-size: 0.76rem;
        font-weight: 650;
    }

    .rank-badge {
        width: 30px;
        height: 30px;
        border-radius: 11px;
        background: var(--gray-soft);
        color: var(--muted);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.78rem;
        font-weight: 950;
        flex-shrink: 0;
    }

    .empty-state {
        padding: 46px 18px;
        text-align: center;
    }

    .empty-state-icon {
        width: 56px;
        height: 56px;
        margin: 0 auto 12px;
        border-radius: 18px;
        background: var(--gray-soft);
        color: var(--muted);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.45rem;
    }

    @media (max-width: 1100px) {
        .dashboard-quick {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .welcome-panel,
        .period-panel {
            border-radius: 18px;
            padding: 18px;
        }

        .welcome-title {
            font-size: 1.35rem;
        }

        .chart-box {
            gap: 5px;
            overflow-x: auto;
            align-items: flex-end;
        }

        .chart-item {
            min-width: 28px;
        }
    }
</style>

<div class="dashboard-quick">
    <section class="welcome-panel">
        <div class="welcome-kicker">
            <i class="bi bi-stars"></i>
            Periode {{ $periodeLabel }}
        </div>

        <h1 class="welcome-title">
            Halo Admin, pantau toko tahumu dari satu dashboard.
        </h1>

        <p class="welcome-desc">
            Cek pesanan masuk, pembayaran, stok produk, ulasan pembeli, sampai performa penjualan tanpa perlu pindah-pindah alur.
        </p>

        <div class="welcome-actions">
            <a href="{{ route('admin.produk.create') }}" class="btn btn-brand px-3">
                <i class="bi bi-plus-lg me-1"></i>
                Tambah Produk
            </a>

            <a href="{{ route('admin.pesanan.index') }}" class="btn btn-light border px-3 fw-bold">
                <i class="bi bi-receipt me-1 text-muted"></i>
                Cek Pesanan
            </a>

            <a href="{{ route('admin.stok.index') }}" class="btn btn-light border px-3 fw-bold">
                <i class="bi bi-box-seam me-1 text-muted"></i>
                Kelola Stok
            </a>
        </div>
    </section>

    <section class="period-panel">
        <div class="period-title">
            <i class="bi bi-calendar3 me-1 text-warning"></i>
            Filter Dashboard
        </div>

        <form class="js-instant-filter" method="GET">
            <div class="mb-3">
                <label for="filterBulan">Bulan</label>
                <select class="form-select" id="filterBulan" name="bulan">
                    @foreach($daftarBulan as $nomorBulan => $namaBulan)
                        <option value="{{ $nomorBulan }}" @selected($filterBulan == $nomorBulan)>
                            {{ $namaBulan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="filterTahun">Tahun</label>
                <select class="form-select" id="filterTahun" name="tahun">
                    @foreach($daftarTahun as $tahun)
                        <option value="{{ $tahun }}" @selected($filterTahun == $tahun)>
                            {{ $tahun }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </section>
</div>

<div class="grid g4 mb-4">
    <a href="{{ route('admin.pembayaran.index') }}" class="page-card action-card stat">
        <div>
            <div class="action-label">Menunggu Bayar</div>
            <div class="action-value">{{ $stats['menunggu_pembayaran'] ?? 0 }}</div>
            <span class="stat-note text-warning">Perlu dicek</span>
        </div>

        <div class="action-icon bg-warning-subtle text-warning-emphasis">
            <i class="bi bi-credit-card-2-front"></i>
        </div>
    </a>

    <a href="{{ route('admin.pesanan.index') }}" class="page-card action-card stat">
        <div>
            <div class="action-label">Pesanan Hari Ini</div>
            <div class="action-value">{{ $stats['pesanan_hari_ini'] ?? 0 }}</div>
            <span class="stat-note text-primary">Transaksi masuk</span>
        </div>

        <div class="action-icon bg-primary-subtle text-primary-emphasis">
            <i class="bi bi-bag-check"></i>
        </div>
    </a>

    <div class="page-card stat">
        <div>
            <div class="action-label">Penjualan Hari Ini</div>
            <div class="action-value" style="font-size: 1.25rem;">
                {{ $rupiah($stats['penjualan_hari_ini'] ?? 0) }}
            </div>
            <span class="stat-note text-success">Pembayaran dibayar</span>
        </div>

        <div class="action-icon bg-success-subtle text-success-emphasis">
            <i class="bi bi-cash-stack"></i>
        </div>
    </div>

    <a href="{{ route('admin.stok.index') }}" class="page-card action-card stat">
        <div>
            <div class="action-label">Stok Bermasalah</div>
            <div class="action-value">{{ $totalStokBermasalah }}</div>
            <span class="stat-note text-danger">Menipis / habis</span>
        </div>

        <div class="action-icon bg-danger-subtle text-danger-emphasis">
            <i class="bi bi-exclamation-triangle"></i>
        </div>
    </a>
</div>

<div class="grid g2 mb-4">
    <section class="page-card">
        <div class="dash-card-head">
            <div>
                <h2 class="dash-card-title">Grafik Penjualan Harian</h2>
                <p class="dash-card-desc">Pendapatan pembayaran berstatus dibayar pada {{ $periodeLabel }}.</p>
            </div>

            <a href="{{ route('admin.laporan.index') }}" class="small-btn">
                <i class="bi bi-bar-chart"></i>
                Laporan
            </a>
        </div>

        <div class="chart-box">
            @php
                $grafikDashboard = collect($penjualanMingguan)->take(-7);
                $maxGrafikDashboard = max($grafikDashboard->max('total') ?? 1, 1);
            @endphp

            @foreach($grafikDashboard as $item)
                @php
                    $total = (float) ($item['total'] ?? 0);
                    $height = max(8, round(($total / $maxGrafikDashboard) * 100));
                @endphp

                <div class="chart-item">
                    <div class="chart-bar"
                        style="height: {{ $height }}%;"
                        data-value="{{ $rupiah($total) }}">
                    </div>
                    <div class="chart-label">{{ $item['label'] }}</div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="page-card">
        <div class="dash-card-head">
            <div>
                <h2 class="dash-card-title">Ringkasan Bisnis</h2>
                <p class="dash-card-desc">Rekap singkat performa toko pada periode terpilih.</p>
            </div>
        </div>

        <div class="summary-list">
            <div class="summary-item">
                <div>
                    <div class="summary-label">Pendapatan Periode Ini</div>
                    <div class="summary-value">{{ $rupiah($totalPendapatanBulan) }}</div>
                </div>
                <span class="summary-pill">Bulan ini</span>
            </div>

            <div class="summary-item">
                <div>
                    <div class="summary-label">Total Pendapatan Semua Periode</div>
                    <div class="summary-value">{{ $rupiah($totalPendapatanAll) }}</div>
                </div>
                <span class="summary-pill">All time</span>
            </div>

            <div class="summary-item">
                <div>
                    <div class="summary-label">Total Pesanan Semua Periode</div>
                    <div class="summary-value">{{ $totalPesananAll }} pesanan</div>
                </div>
                <span class="summary-pill">Checkout</span>
            </div>

            <div class="summary-item">
                <div>
                    <div class="summary-label">Stok Habis</div>
                    <div class="summary-value">{{ $stats['stok_habis'] ?? 0 }} produk</div>
                </div>
                <span class="summary-pill">Stok</span>
            </div>
        </div>
    </section>
</div>

<div class="grid g2">
    <section class="page-card">
        <div class="dash-card-head mb-3">
            <div>
                <h2 class="dash-card-title">Pesanan Terbaru</h2>
                <p class="dash-card-desc">Pesanan terbaru pada periode {{ $periodeLabel }}.</p>
            </div>

            <a href="{{ route('admin.pesanan.index') }}" class="small-btn">
                Lihat Semua
            </a>
        </div>

        <div>
            @forelse($pesananTerbaru as $order)
                <a href="{{ route('admin.pesanan.show', $order) }}" class="order-row">
                    <div class="rank-badge">
                        <i class="bi bi-receipt"></i>
                    </div>

                    <div class="flex-grow-1 min-w-0">
                        <div class="invoice-text text-truncate">{{ $order->nomor_invoice }}</div>
                        <div class="small-muted text-truncate">
                            {{ $order->user?->name ?? 'Pembeli' }}
                            ·
                            {{ optional($order->tanggal_pesanan)->format('d M Y H:i') }}
                        </div>
                    </div>

                    <div class="text-end">
                        <div class="fw-bold text-dark small mb-1">
                            {{ $rupiah($order->total_bayar) }}
                        </div>
                        <span class="chip {{ $statusClass($order->status) }}">
                            {{ $statusLabel($order->status) }}
                        </span>
                    </div>
                </a>
            @empty
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="bi bi-inbox"></i>
                    </div>
                    <strong class="d-block text-dark mb-1">Belum ada pesanan</strong>
                    <span class="small-muted">Pesanan dari mobile akan muncul di sini setelah pembeli checkout.</span>
                </div>
            @endforelse
        </div>
    </section>

    <section class="page-card">
        <div class="dash-card-head mb-3">
            <div>
                <h2 class="dash-card-title">Produk Terlaris</h2>
                <p class="dash-card-desc">Berdasarkan jumlah item pesanan pada periode terpilih.</p>
            </div>

            <a href="{{ route('admin.produk.index') }}" class="small-btn">
                Produk
            </a>
        </div>

        <div>
            @forelse($produkTerlaris as $produk)
                <div class="product-row">
                    <div class="rank-badge">#{{ $loop->iteration }}</div>

                    <div class="cover d-flex align-items-center justify-content-center">
                        <i class="bi bi-box-seam text-muted"></i>
                    </div>

                    <div class="flex-grow-1 min-w-0">
                        <div class="product-name text-truncate">{{ $produk->nama }}</div>
                        <div class="small-muted">
                            {{ $rupiah($produk->harga) }}
                            ·
                            Stok {{ $produk->stok ?? 0 }}
                        </div>
                    </div>

                    <div class="text-end">
                        <div class="fw-bold text-dark">
                            {{ (int) ($produk->total_terjual ?? 0) }}
                        </div>
                        <div class="small-muted">terjual</div>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="bi bi-bar-chart"></i>
                    </div>
                    <strong class="d-block text-dark mb-1">Belum ada data produk terlaris</strong>
                    <span class="small-muted">Data akan muncul setelah ada item pesanan.</span>
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection
