@extends('layouts.admin')

@section('title', 'Laporan - SiTahu')
@section('page_title', 'Laporan')

@section('content')
@php
    $formatStatus = function ($value) {
        return ucwords(str_replace('_', ' ', (string) $value));
    };

    $statusClass = [
        'menunggu_pembayaran' => 'bg-warning-subtle text-warning-emphasis',
        'dibayar' => 'bg-primary-subtle text-primary-emphasis',
        'diproses' => 'bg-info-subtle text-info-emphasis',
        'siap_diambil' => 'bg-success-subtle text-success-emphasis',
        'dalam_pengantaran' => 'bg-primary-subtle text-primary-emphasis',
        'selesai' => 'bg-success-subtle text-success-emphasis',
        'dibatalkan' => 'bg-danger-subtle text-danger-emphasis',
        'gagal' => 'bg-danger-subtle text-danger-emphasis',
        'kedaluwarsa' => 'bg-secondary-subtle text-secondary-emphasis',
    ];

    $periodeText = $tanggalMulai->format('d/m/Y') . ' - ' . $tanggalSelesai->format('d/m/Y');

    $maxPendapatan = max((float) $laporanHarian->max('pendapatan'), 1);
@endphp

<style>
    .report-hero {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 360px;
        gap: 18px;
        margin-bottom: 18px;
    }

    .report-welcome {
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

    .report-kicker {
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

    .report-title {
        margin: 0;
        color: var(--text);
        font-size: clamp(1.55rem, 3vw, 2.25rem);
        line-height: 1.05;
        letter-spacing: -.065em;
        font-weight: 950;
    }

    .report-desc {
        max-width: 750px;
        margin: 10px 0 0;
        color: var(--muted);
        font-size: .93rem;
        line-height: 1.6;
        font-weight: 650;
    }

    .report-filter {
        padding: 18px;
        border-radius: 24px;
        border: 1px solid var(--border);
        background: #fff;
        box-shadow: var(--shadow-soft);
    }

    .report-filter h2 {
        margin: 0 0 14px;
        color: var(--text);
        font-size: 1rem;
        font-weight: 950;
        letter-spacing: -.035em;
    }

    .report-filter label {
        margin-bottom: 7px;
        color: var(--muted);
        font-size: .74rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .report-metric {
        min-height: 118px;
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
    }

    .report-metric:hover {
        transform: translateY(-2px);
        border-color: rgba(223, 186, 104, .45);
        box-shadow: var(--shadow);
    }

    .metric-label {
        color: var(--muted);
        font-size: .75rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .055em;
    }

    .metric-value {
        margin-top: 8px;
        color: var(--text);
        font-size: 1.55rem;
        line-height: 1;
        font-weight: 950;
        letter-spacing: -.055em;
    }

    .metric-value.money {
        font-size: 1.1rem;
        line-height: 1.2;
    }

    .metric-note {
        display: inline-block;
        margin-top: 8px;
        font-size: .76rem;
        font-weight: 850;
    }

    .metric-icon {
        width: 44px;
        height: 44px;
        border-radius: 15px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
        font-size: 1.1rem;
    }

    .report-section {
        border-radius: 22px;
        border: 1px solid var(--border);
        background: #fff;
        box-shadow: var(--shadow-soft);
        overflow: hidden;
    }

    .section-head {
        padding: 18px;
        border-bottom: 1px solid var(--border);
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
        height: 260px;
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
        padding: 14px 18px 18px;
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
        font-size: 1.05rem;
        font-weight: 950;
    }

    .best-product-row,
    .stock-row {
        display: flex;
        align-items: center;
        gap: 13px;
        padding: 15px 18px;
        border-top: 1px solid #f1f2f4;
    }

    .rank-badge {
        width: 34px;
        height: 34px;
        border-radius: 13px;
        background: var(--brand-soft);
        color: var(--brand-dark);
        display: grid;
        place-items: center;
        font-size: .8rem;
        font-weight: 950;
        flex-shrink: 0;
    }

    .stock-badge {
        width: 42px;
        height: 42px;
        border-radius: 15px;
        background: #fef2f2;
        color: #b91c1c;
        display: grid;
        place-items: center;
        font-size: .9rem;
        font-weight: 950;
        flex-shrink: 0;
    }

    .row-main {
        min-width: 0;
        flex: 1;
    }

    .row-title {
        color: var(--text);
        font-size: .9rem;
        font-weight: 950;
        letter-spacing: -.02em;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .row-sub {
        margin-top: 4px;
        color: var(--muted);
        font-size: .76rem;
        font-weight: 700;
    }

    .row-price {
        color: var(--brand-dark);
        font-size: .9rem;
        font-weight: 950;
        white-space: nowrap;
        text-align: right;
    }

    .report-table-wrap {
        overflow-x: auto;
    }

    .report-table {
        width: 100%;
        border-collapse: collapse;
    }

    .report-table th {
        padding: 14px 16px;
        background: #f9fafb;
        border-bottom: 1px solid var(--border);
        color: var(--muted);
        font-size: .74rem;
        font-weight: 950;
        text-transform: uppercase;
        letter-spacing: .055em;
        white-space: nowrap;
    }

    .report-table td {
        padding: 15px 16px;
        border-bottom: 1px solid #f1f2f4;
        color: var(--text);
        font-size: .86rem;
        font-weight: 650;
        vertical-align: top;
    }

    .report-table tbody tr:hover {
        background: #fafafa;
    }

    .empty-box {
        padding: 36px 18px;
        text-align: center;
        color: var(--muted);
        font-size: .85rem;
        font-weight: 700;
    }

    @media (max-width: 1080px) {
        .report-hero {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 700px) {
        .report-welcome,
        .report-filter {
            padding: 18px;
            border-radius: 20px;
        }

        .chart-box {
            justify-content: flex-start;
        }

        .metric-value.money {
            font-size: 1rem;
        }

        .section-head {
            flex-direction: column;
        }
    }
</style>

<div class="report-hero">
    <section class="report-welcome">
        <div class="report-kicker">
            <i class="bi bi-file-earmark-bar-graph"></i>
            Laporan {{ $periodeText }}
        </div>

        <h1 class="report-title">
            Laporan penjualan, pesanan, stok, dan produk terlaris.
        </h1>

        <p class="report-desc">
            Gunakan filter tanggal untuk melihat performa toko pada periode tertentu.
            Data pendapatan dihitung dari pembayaran dengan status dibayar.
        </p>
    </section>

    <section class="report-filter">
        <h2>
            <i class="bi bi-calendar-range me-1 text-warning"></i>
            Filter Periode
        </h2>

        <form method="GET">
            <div class="mb-3">
                <label for="tanggal_mulai">Tanggal Mulai</label>
                <input
                    type="date"
                    id="tanggal_mulai"
                    name="tanggal_mulai"
                    value="{{ request('tanggal_mulai', $tanggalMulai->format('Y-m-d')) }}"
                    class="form-control"
                >
            </div>

            <div class="mb-3">
                <label for="tanggal_selesai">Tanggal Selesai</label>
                <input
                    type="date"
                    id="tanggal_selesai"
                    name="tanggal_selesai"
                    value="{{ request('tanggal_selesai', $tanggalSelesai->format('Y-m-d')) }}"
                    class="form-control"
                >
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-brand">
                    <i class="bi bi-funnel me-1"></i>
                    Terapkan Filter
                </button>

                <a href="{{ route('admin.laporan.export.csv', request()->query()) }}" class="btn btn-light border fw-bold">
                    <i class="bi bi-download me-1 text-muted"></i>
                    Export CSV
                </a>
            </div>
        </form>
    </section>
</div>

<div class="grid g4 mb-4">
    <div class="report-metric">
        <div>
            <div class="metric-label">Total Pendapatan</div>
            <div class="metric-value money">{{ $rupiah($stats['total_pendapatan'] ?? 0) }}</div>
            <span class="metric-note text-success">Pembayaran dibayar</span>
        </div>

        <div class="metric-icon bg-success-subtle text-success-emphasis">
            <i class="bi bi-cash-stack"></i>
        </div>
    </div>

    <div class="report-metric">
        <div>
            <div class="metric-label">Total Pesanan</div>
            <div class="metric-value">{{ $stats['total_pesanan'] ?? 0 }}</div>
            <span class="metric-note text-primary">Dalam periode</span>
        </div>

        <div class="metric-icon bg-primary-subtle text-primary-emphasis">
            <i class="bi bi-receipt"></i>
        </div>
    </div>

    <div class="report-metric">
        <div>
            <div class="metric-label">Produk Terjual</div>
            <div class="metric-value">{{ $stats['total_produk_terjual'] ?? 0 }}</div>
            <span class="metric-note text-warning">Total item keluar</span>
        </div>

        <div class="metric-icon bg-warning-subtle text-warning-emphasis">
            <i class="bi bi-box2-heart"></i>
        </div>
    </div>

    <div class="report-metric">
        <div>
            <div class="metric-label">Stok Bermasalah</div>
            <div class="metric-value">{{ ($stats['stok_menipis'] ?? 0) + ($stats['stok_habis'] ?? 0) }}</div>
            <span class="metric-note text-danger">Menipis / habis</span>
        </div>

        <div class="metric-icon bg-danger-subtle text-danger-emphasis">
            <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
    </div>
</div>

<div class="grid g2 mb-4">
    <section class="report-section">
        <div class="section-head">
            <div>
                <h2>Grafik Pendapatan Harian</h2>
                <p>Pendapatan harian dari pembayaran berstatus dibayar.</p>
            </div>
        </div>

        <div class="chart-box">
            @foreach($laporanHarian as $item)
                @php
                    $height = max(8, round(((float) $item['pendapatan'] / $maxPendapatan) * 100));
                @endphp

                <div class="chart-item">
                    <div
                        class="chart-bar"
                        style="height: {{ $height }}%;"
                        data-value="{{ $rupiah($item['pendapatan']) }}"
                    ></div>

                    <div class="chart-label">
                        {{ \Illuminate\Support\Str::before($item['tanggal'], '/') }}
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="report-section">
        <div class="section-head">
            <div>
                <h2>Status Pesanan</h2>
                <p>Jumlah pesanan berdasarkan status dalam periode ini.</p>
            </div>
        </div>

        <div class="status-list">
            @forelse($statusPesanan as $status => $total)
                <div class="status-row">
                    <span>{{ $formatStatus($status) }}</span>
                    <strong>{{ $total }}</strong>
                </div>
            @empty
                <div class="empty-box">
                    Belum ada data status pesanan.
                </div>
            @endforelse
        </div>
    </section>
</div>

<div class="grid g2 mb-4">
    <section class="report-section">
        <div class="section-head">
            <div>
                <h2>Produk Terlaris</h2>
                <p>Produk dengan jumlah penjualan terbanyak pada periode ini.</p>
            </div>
        </div>

        @forelse($produkTerlaris as $index => $produk)
            <div class="best-product-row">
                <div class="rank-badge">{{ $index + 1 }}</div>

                <div class="row-main">
                    <div class="row-title">{{ $produk->nama }}</div>
                    <div class="row-sub">
                        Terjual {{ (int) ($produk->total_terjual ?? 0) }} item
                        · Stok {{ $produk->stok }}
                    </div>
                </div>

                <div class="row-price">
                    {{ $rupiah($produk->total_pendapatan_produk ?? 0) }}
                </div>
            </div>
        @empty
            <div class="empty-box">
                Belum ada produk terjual pada periode ini.
            </div>
        @endforelse
    </section>

    <section class="report-section">
        <div class="section-head">
            <div>
                <h2>Stok Perlu Diperhatikan</h2>
                <p>Produk yang stoknya habis atau sudah menyentuh batas minimum.</p>
            </div>
        </div>

        @forelse($produkStokPerhatian as $produk)
            <div class="stock-row">
                <div class="stock-badge">
                    {{ $produk->stok }}
                </div>

                <div class="row-main">
                    <div class="row-title">{{ $produk->nama }}</div>
                    <div class="row-sub">
                        Minimum {{ $produk->min_stok ?? 0 }}
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
            </div>
        @empty
            <div class="empty-box">
                Aman, belum ada stok yang habis atau menipis.
            </div>
        @endforelse
    </section>
</div>

<section class="report-section">
    <div class="section-head">
        <div>
            <h2>Daftar Transaksi</h2>
            <p>Pesanan yang masuk pada periode {{ $periodeText }}.</p>
        </div>

        <a href="{{ route('admin.laporan.export.csv', request()->query()) }}" class="small-btn">
            <i class="bi bi-download"></i>
            Export CSV
        </a>
    </div>

    <div class="report-table-wrap">
        <table class="report-table">
            <thead>
            <tr>
                <th>Invoice</th>
                <th>Pembeli</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Pembayaran</th>
                <th class="text-end">Total</th>
                <th class="text-end">Aksi</th>
            </tr>
            </thead>

            <tbody>
            @forelse($pesanan as $order)
                <tr>
                    <td>
                        <strong>{{ $order->nomor_invoice }}</strong>
                    </td>

                    <td>
                        {{ $order->user?->name ?? 'Pembeli' }}
                        <div class="text-muted small">
                            {{ $order->user?->email ?? '-' }}
                        </div>
                    </td>

                    <td>
                        {{ optional($order->tanggal_pesanan)->format('d/m/Y H:i') ?? '-' }}
                    </td>

                    <td>
                        <span class="badge {{ $statusClass[$order->status] ?? 'bg-secondary-subtle text-secondary-emphasis' }}">
                            {{ $formatStatus($order->status) }}
                        </span>
                    </td>

                    <td>
                        <span class="badge {{ $statusClass[$order->status_pembayaran] ?? 'bg-secondary-subtle text-secondary-emphasis' }}">
                            {{ $formatStatus($order->status_pembayaran) }}
                        </span>
                    </td>

                    <td class="text-end">
                        <strong>{{ $rupiah($order->total_bayar ?? 0) }}</strong>
                    </td>

                    <td class="text-end">
                        <a href="{{ route('admin.pesanan.show', $order) }}" class="small-btn">
                            Detail
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-box">
                            Belum ada transaksi pada periode ini.
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($pesanan->hasPages())
        <div class="p-3 border-top bg-white">
            {{ $pesanan->links() }}
        </div>
    @endif
</section>
@endsection