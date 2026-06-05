@extends('layouts.admin')

@section('title', 'Dashboard Admin - SiTahu')
@section('page_title', 'Dashboard Admin')

@section('content')
@php
    $stokBermasalah = ($stats['stok_menipis'] ?? 0) + ($stats['stok_habis'] ?? 0);

    $statusLabels = [
        'dibayar' => 'Dibayar',
        'diproses' => 'Diproses',
        'siap_diambil' => 'Siap Diambil',
        'dalam_pengantaran' => 'Dalam Pengantaran',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
    ];

    $statusClass = [
        'menunggu_pembayaran' => 'bg-warning-subtle text-warning-emphasis',
        'dibayar' => 'bg-primary-subtle text-primary-emphasis',
        'diproses' => 'bg-info-subtle text-info-emphasis',
        'siap_diambil' => 'bg-success-subtle text-success-emphasis',
        'dalam_pengantaran' => 'bg-primary-subtle text-primary-emphasis',
        'selesai' => 'bg-success-subtle text-success-emphasis',
        'dibatalkan' => 'bg-danger-subtle text-danger-emphasis',
    ];
@endphp

<style>
    .dash-hero {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 320px;
        gap: 18px;
        margin-bottom: 18px;
    }

    .dash-welcome {
        position: relative;
        overflow: hidden;
        padding: 24px;
        border-radius: 24px;
        border: 1px solid var(--border);
        background:
            radial-gradient(circle at top right, rgba(223, 186, 104, 0.25), transparent 36%),
            linear-gradient(135deg, #ffffff, #fff8e8);
        box-shadow: var(--shadow-soft);
    }

    .dash-welcome::after {
        content: "";
        position: absolute;
        right: -70px;
        bottom: -100px;
        width: 210px;
        height: 210px;
        border-radius: 999px;
        background: rgba(223, 186, 104, 0.16);
    }

    .dash-kicker {
        position: relative;
        z-index: 1;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 11px;
        border-radius: 999px;
        background: rgba(255,255,255,.82);
        border: 1px solid rgba(223, 186, 104, .34);
        color: var(--brand-dark);
        font-size: .76rem;
        font-weight: 900;
        margin-bottom: 13px;
    }

    .dash-title {
        position: relative;
        z-index: 1;
        max-width: 760px;
        margin: 0;
        color: var(--text);
        font-size: clamp(1.55rem, 3vw, 2.25rem);
        line-height: 1.05;
        letter-spacing: -.065em;
        font-weight: 950;
    }

    .dash-desc {
        position: relative;
        z-index: 1;
        max-width: 720px;
        margin: 10px 0 0;
        color: var(--muted);
        font-size: .93rem;
        line-height: 1.6;
        font-weight: 650;
    }

    .dash-actions {
        position: relative;
        z-index: 1;
        margin-top: 18px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .dash-filter {
        padding: 18px;
        border-radius: 24px;
        border: 1px solid var(--border);
        background: #fff;
        box-shadow: var(--shadow-soft);
    }

    .dash-filter h2 {
        margin: 0 0 14px;
        color: var(--text);
        font-size: 1rem;
        font-weight: 950;
        letter-spacing: -.035em;
    }

    .dash-filter label {
        margin-bottom: 7px;
        color: var(--muted);
        font-size: .74rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .dash-metric {
        min-height: 128px;
        padding: 18px;
        border-radius: 20px;
        border: 1px solid var(--border);
        background: #fff;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        box-shadow: var(--shadow-soft);
        transition: .18s ease;
        text-decoration: none;
    }

    .dash-metric:hover {
        transform: translateY(-2px);
        border-color: rgba(223, 186, 104, .45);
        box-shadow: var(--shadow);
    }

    .metric-label {
        color: var(--muted);
        font-size: .76rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .055em;
    }

    .metric-value {
        margin-top: 9px;
        color: var(--text);
        font-size: 1.75rem;
        line-height: 1;
        font-weight: 950;
        letter-spacing: -.06em;
    }

    .metric-value.money {
        font-size: 1.22rem;
        line-height: 1.15;
        letter-spacing: -.04em;
    }

    .metric-note {
        display: inline-block;
        margin-top: 9px;
        font-size: .75rem;
        font-weight: 850;
    }

    .metric-icon {
        width: 46px;
        height: 46px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.18rem;
    }

    .dash-section {
        border-radius: 22px;
        border: 1px solid var(--border);
        background: #fff;
        box-shadow: var(--shadow-soft);
        overflow: hidden;
    }

    .section-head {
        padding: 18px 18px 0;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .section-head h2 {
        margin: 0;
        color: var(--text);
        font-size: 1.02rem;
        font-weight: 950;
        letter-spacing: -.035em;
    }

    .section-head p {
        margin: 5px 0 0;
        color: var(--muted);
        font-size: .82rem;
        line-height: 1.5;
        font-weight: 650;
    }

    .chart-box {
        height: 285px;
        padding: 32px 20px 22px;
        display: flex;
        align-items: flex-end;
        justify-content: center;
        gap: 12px;
        overflow-x: auto;
    }

    .chart-item {
        min-width: 28px;
        max-width: 42px;
        flex: 1;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex-direction: column;
        gap: 8px;
    }

    .chart-bar {
        width: 100%;
        max-width: 34px;
        border-radius: 999px 999px 8px 8px;
        background: linear-gradient(180deg, var(--brand), #f0d58d);
        min-height: 8px;
        position: relative;
        transition: .16s ease;
    }

    .chart-bar:hover {
        transform: translateY(-3px);
        filter: brightness(.98);
    }

    .chart-bar::before {
        content: attr(data-value);
        position: absolute;
        left: 50%;
        top: -27px;
        transform: translateX(-50%);
        padding: 4px 8px;
        border-radius: 999px;
        background: #111827;
        color: #fff;
        font-size: .68rem;
        font-weight: 850;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: .16s ease;
    }

    .chart-bar:hover::before {
        opacity: 1;
    }

    .chart-label {
        color: #9ca3af;
        font-size: .68rem;
        font-weight: 850;
    }

    .status-list {
        padding: 12px 18px 18px;
        display: grid;
        gap: 10px;
    }

    .status-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px;
        border: 1px solid #f1f2f4;
        border-radius: 16px;
        background: #fafafa;
    }

    .status-row span:first-child {
        color: var(--muted);
        font-size: .83rem;
        font-weight: 850;
    }

    .status-row strong {
        color: var(--text);
        font-size: 1.1rem;
        font-weight: 950;
    }

    .list-row {
        display: flex;
        align-items: center;
        gap: 13px;
        padding: 15px 18px;
        border-top: 1px solid #f1f2f4;
        text-decoration: none;
        transition: .16s ease;
    }

    .list-row:hover {
        background: #fafafa;
    }

    .list-main {
        min-width: 0;
        flex: 1;
    }

    .list-title {
        color: var(--text);
        font-size: .9rem;
        font-weight: 950;
        letter-spacing: -.02em;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .list-sub {
        margin-top: 4px;
        color: var(--muted);
        font-size: .76rem;
        font-weight: 700;
    }

    .list-price {
        color: var(--brand-dark);
        font-size: .9rem;
        font-weight: 950;
        white-space: nowrap;
        text-align: right;
    }

    .rank-badge {
        width: 32px;
        height: 32px;
        border-radius: 12px;
        background: var(--brand-soft);
        color: var(--brand-dark);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: .78rem;
        font-weight: 950;
        flex-shrink: 0;
    }

    .stok-badge {
        width: 42px;
        height: 42px;
        border-radius: 15px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #fef2f2;
        color: #b91c1c;
        font-weight: 950;
        flex-shrink: 0;
    }

    .empty-box {
        padding: 40px 18px;
        text-align: center;
        color: var(--muted);
        font-size: .86rem;
        font-weight: 700;
    }

    @media (max-width: 1120px) {
        .dash-hero {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 700px) {
        .dash-welcome,
        .dash-filter {
            padding: 18px;
            border-radius: 20px;
        }

        .chart-box {
            justify-content: flex-start;
        }

        .metric-value.money {
            font-size: 1rem;
        }
    }
</style>

<div class="dash-hero">
    <section class="dash-welcome">
        <div class="dash-kicker">
            <i class="bi bi-stars"></i>
            Dashboard periode {{ $periodeLabel }}
        </div>

        <h1 class="dash-title">
            Pantau pesanan, pembayaran, stok, dan performa produk dari satu tempat.
        </h1>

        <p class="dash-desc">
            Dashboard ini dibuat lebih ringkas supaya admin tidak bingung:
            bagian atas untuk kondisi penting, bagian bawah untuk grafik, pesanan terbaru,
            produk terlaris, dan stok yang perlu diperhatikan.
        </p>

        <div class="dash-actions">
            <a href="{{ route('admin.pesanan.index') }}" class="btn btn-brand px-3">
                <i class="bi bi-receipt me-1"></i>
                Cek Pesanan
            </a>

            <a href="{{ route('admin.produk.create') }}" class="btn btn-light border px-3 fw-bold">
                <i class="bi bi-plus-lg me-1 text-muted"></i>
                Tambah Produk
            </a>

            <a href="{{ route('admin.stok.index') }}" class="btn btn-light border px-3 fw-bold">
                <i class="bi bi-box-seam me-1 text-muted"></i>
                Kelola Stok
            </a>
        </div>
    </section>

    <section class="dash-filter">
        <h2>
            <i class="bi bi-calendar3 me-1 text-warning"></i>
            Filter Dashboard
        </h2>

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
    <a href="{{ route('admin.pembayaran.index') }}" class="dash-metric">
        <div>
            <div class="metric-label">Menunggu Bayar</div>
            <div class="metric-value">{{ $stats['menunggu_pembayaran'] ?? 0 }}</div>
            <span class="metric-note text-warning">Perlu dicek admin</span>
        </div>

        <div class="metric-icon bg-warning-subtle text-warning-emphasis">
            <i class="bi bi-credit-card-2-front-fill"></i>
        </div>
    </a>

    <a href="{{ route('admin.pesanan.index') }}" class="dash-metric">
        <div>
            <div class="metric-label">Pesanan Hari Ini</div>
            <div class="metric-value">{{ $stats['pesanan_hari_ini'] ?? 0 }}</div>
            <span class="metric-note text-primary">Checkout masuk</span>
        </div>

        <div class="metric-icon bg-primary-subtle text-primary-emphasis">
            <i class="bi bi-bag-check-fill"></i>
        </div>
    </a>

    <div class="dash-metric">
        <div>
            <div class="metric-label">Penjualan Hari Ini</div>
            <div class="metric-value money">{{ $rupiah($stats['penjualan_hari_ini'] ?? 0) }}</div>
            <span class="metric-note text-success">Pembayaran dibayar</span>
        </div>

        <div class="metric-icon bg-success-subtle text-success-emphasis">
            <i class="bi bi-cash-stack"></i>
        </div>
    </div>

    <a href="{{ route('admin.stok.index') }}" class="dash-metric">
        <div>
            <div class="metric-label">Stok Bermasalah</div>
            <div class="metric-value">{{ $stokBermasalah }}</div>
            <span class="metric-note text-danger">Menipis / habis</span>
        </div>

        <div class="metric-icon bg-danger-subtle text-danger-emphasis">
            <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
    </a>
</div>

<div class="grid g4 mb-4">
    <div class="dash-metric">
        <div>
            <div class="metric-label">Produk Aktif</div>
            <div class="metric-value">{{ $stats['produk_aktif'] ?? 0 }}</div>
            <span class="metric-note text-success">Tampil ke pembeli</span>
        </div>

        <div class="metric-icon bg-success-subtle text-success-emphasis">
            <i class="bi bi-box2-heart-fill"></i>
        </div>
    </div>

    <a href="{{ route('admin.pembeli.index') }}" class="dash-metric">
        <div>
            <div class="metric-label">Total Pembeli</div>
            <div class="metric-value">{{ $stats['total_pembeli'] ?? 0 }}</div>
            <span class="metric-note text-primary">Akun pembeli</span>
        </div>

        <div class="metric-icon bg-primary-subtle text-primary-emphasis">
            <i class="bi bi-people-fill"></i>
        </div>
    </a>

    <a href="{{ route('admin.ulasan.index') }}" class="dash-metric">
        <div>
            <div class="metric-label">Total Ulasan</div>
            <div class="metric-value">{{ $stats['total_ulasan'] ?? 0 }}</div>
            <span class="metric-note text-warning">{{ $stats['ulasan_video'] ?? 0 }} ulasan video</span>
        </div>

        <div class="metric-icon bg-warning-subtle text-warning-emphasis">
            <i class="bi bi-star-fill"></i>
        </div>
    </a>

    <div class="dash-metric">
        <div>
            <div class="metric-label">Penjualan Periode</div>
            <div class="metric-value money">{{ $rupiah($stats['penjualan_periode'] ?? 0) }}</div>
            <span class="metric-note text-success">{{ $periodeLabel }}</span>
        </div>

        <div class="metric-icon bg-success-subtle text-success-emphasis">
            <i class="bi bi-graph-up-arrow"></i>
        </div>
    </div>
</div>

<div class="grid g2 mb-4">
    <section class="dash-section">
        <div class="section-head">
            <div>
                <h2>Grafik Penjualan Harian</h2>
                <p>Pendapatan dari pembayaran berstatus dibayar pada {{ $periodeLabel }}.</p>
            </div>

            <a href="{{ route('admin.laporan.index') }}" class="small-btn">
                <i class="bi bi-bar-chart"></i>
                Laporan
            </a>
        </div>

        <div class="chart-box">
            @foreach($penjualanHarian as $item)
                @php
                    $total = (float) ($item['total'] ?? 0);
                    $height = max(8, round(($total / $maxPenjualan) * 100));
                @endphp

                <div class="chart-item">
                    <div
                        class="chart-bar"
                        style="height: {{ $height }}%;"
                        data-value="{{ $rupiah($total) }}"
                    ></div>
                    <div class="chart-label">{{ $item['label'] }}</div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="dash-section">
        <div class="section-head">
            <div>
                <h2>Status Pesanan</h2>
                <p>Ringkasan status pesanan yang perlu dipantau admin.</p>
            </div>
        </div>

        <div class="status-list">
            <div class="status-row">
                <span>Menunggu Pembayaran</span>
                <strong>{{ $stats['menunggu_pembayaran'] ?? 0 }}</strong>
            </div>

            @foreach($statusLabels as $key => $label)
                <div class="status-row">
                    <span>{{ $label }}</span>
                    <strong>{{ $stats[$key] ?? 0 }}</strong>
                </div>
            @endforeach
        </div>
    </section>
</div>

<div class="grid g2 mb-4">
    <section class="dash-section">
        <div class="section-head">
            <div>
                <h2>Pesanan Terbaru</h2>
                <p>Pesanan terbaru dari pembeli, tidak dibatasi periode filter.</p>
            </div>

            <a href="{{ route('admin.pesanan.index') }}" class="small-btn">
                <i class="bi bi-arrow-right"></i>
                Semua
            </a>
        </div>

        @forelse($pesananTerbaru as $order)
            <a href="{{ route('admin.pesanan.show', $order) }}" class="list-row">
                <div class="rank-badge">
                    <i class="bi bi-receipt"></i>
                </div>

                <div class="list-main">
                    <div class="list-title">{{ $order->nomor_invoice }}</div>
                    <div class="list-sub">
                        {{ $order->user?->name ?? 'Pembeli' }}
                        · {{ $order->tanggal_pesanan?->format('d/m/Y H:i') }}
                    </div>
                </div>

                <div class="text-end">
                    <div class="list-price">{{ $rupiah($order->total_bayar ?? 0) }}</div>
                    <span class="badge {{ $statusClass[$order->status] ?? 'bg-secondary-subtle text-secondary-emphasis' }}">
                        {{ ucwords(str_replace('_', ' ', $order->status)) }}
                    </span>
                </div>
            </a>
        @empty
            <div class="empty-box">
                Belum ada pesanan terbaru.
            </div>
        @endforelse
    </section>

    <section class="dash-section">
        <div class="section-head">
            <div>
                <h2>Produk Terlaris</h2>
                <p>Produk dengan penjualan terbanyak pada {{ $periodeLabel }}.</p>
            </div>

            <a href="{{ route('admin.produk.index') }}" class="small-btn">
                <i class="bi bi-arrow-right"></i>
                Produk
            </a>
        </div>

        @forelse($produkTerlaris as $index => $produk)
            <a href="{{ route('admin.produk.edit', $produk) }}" class="list-row">
                <div class="rank-badge">
                    {{ $index + 1 }}
                </div>

                <div class="list-main">
                    <div class="list-title">{{ $produk->nama }}</div>
                    <div class="list-sub">
                        Terjual {{ (int) ($produk->total_terjual ?? 0) }} item
                        · Stok {{ $produk->stok }}
                    </div>
                </div>

                <div class="list-price">
                    {{ $rupiah($produk->harga ?? 0) }}
                </div>
            </a>
        @empty
            <div class="empty-box">
                Belum ada produk terjual pada periode ini.
            </div>
        @endforelse
    </section>
</div>

<section class="dash-section">
    <div class="section-head">
        <div>
            <h2>Stok Perlu Diperhatikan</h2>
            <p>Produk habis atau stoknya sudah menyentuh batas minimum.</p>
        </div>

        <a href="{{ route('admin.stok.index') }}" class="small-btn">
            <i class="bi bi-box-seam"></i>
            Kelola Stok
        </a>
    </div>

    @forelse($stokPerhatian as $produk)
        <a href="{{ route('admin.stok.index') }}" class="list-row">
            <div class="stok-badge">
                {{ $produk->stok }}
            </div>

            <div class="list-main">
                <div class="list-title">{{ $produk->nama }}</div>
                <div class="list-sub">
                    Minimum stok {{ $produk->min_stok ?? 0 }}
                    · {{ $produk->satuan ?? 'satuan' }}
                </div>
            </div>

            <div class="text-end">
                @if($produk->stok <= 0)
                    <span class="badge bg-danger-subtle text-danger-emphasis">
                        Habis
                    </span>
                @else
                    <span class="badge bg-warning-subtle text-warning-emphasis">
                        Menipis
                    </span>
                @endif
            </div>
        </a>
    @empty
        <div class="empty-box">
            Aman, belum ada stok yang habis atau menipis.
        </div>
    @endforelse
</section>
@endsection