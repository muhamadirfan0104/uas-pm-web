@extends('layouts.admin')

@section('title', 'Pesanan - SiTahu')
@section('page_title', 'Pesanan')

@section('content')
<style>
    .sc-box {
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        background: #fff;
        margin-bottom: 1.5rem;
        overflow: hidden;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
    }

    .metric-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1.35rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
        overflow: hidden;
        transition: all 0.2s;
        min-height: 110px;
    }

    .metric-card:hover {
        border-color: var(--brand-color, #dfba68);
        box-shadow: 0 4px 12px rgba(223, 186, 104, 0.15);
        transform: translateY(-2px);
    }

    .metric-label {
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6b7280;
        margin-bottom: 0.5rem;
    }

    .metric-value {
        font-size: 1.8rem;
        font-weight: 800;
        letter-spacing: -0.03em;
        color: #111827;
        line-height: 1.1;
    }

    .metric-note {
        margin-top: 0.35rem;
        font-size: 0.78rem;
        font-weight: 700;
    }

    .metric-icon {
        position: absolute;
        right: -8px;
        bottom: -18px;
        font-size: 5rem;
        opacity: 0.045;
        color: #111827;
    }

    .search-bar-modern {
        background-color: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        transition: all 0.2s;
    }

    .search-bar-modern:focus-within {
        background-color: #ffffff;
        border-color: var(--brand-color, #dfba68);
        box-shadow: 0 0 0 4px rgba(223, 186, 104, 0.15);
    }

    .search-bar-modern input {
        background: transparent;
        border: none;
        box-shadow: none;
        outline: none;
        width: 100%;
        font-size: 0.9rem;
    }

    .form-select-modern {
        background-color: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 0.65rem 0.85rem;
        font-size: 0.9rem;
        font-weight: 600;
        color: #374151;
        transition: all 0.2s;
    }

    .form-select-modern:focus {
        background-color: #fff;
        border-color: var(--brand-color, #dfba68);
        box-shadow: 0 0 0 4px rgba(223, 186, 104, 0.15);
        outline: none;
    }

    .table-enterprise th {
        border-bottom: 2px solid #e5e7eb;
        color: #6b7280;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 1rem 1.25rem;
        font-weight: 700;
        background: #fafafa;
        white-space: nowrap;
    }

    .table-enterprise td {
        vertical-align: middle;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f3f4f6;
        color: #111827;
        font-size: 0.9rem;
    }

    .table-enterprise tbody tr:hover {
        background-color: #f9fafb;
    }

    .invoice-code {
        font-weight: 800;
        color: #111827;
        letter-spacing: -0.02em;
    }

    .buyer-avatar {
        width: 38px;
        height: 38px;
        border-radius: 999px;
        background: rgba(223, 186, 104, 0.18);
        color: #8a6321;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.78rem;
        font-weight: 800;
        flex-shrink: 0;
    }

    .order-method {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        font-size: 0.82rem;
        font-weight: 700;
        color: #374151;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 999px;
        padding: 0.42rem 0.7rem;
        white-space: nowrap;
    }

    .total-text {
        font-weight: 800;
        color: #111827;
        white-space: nowrap;
    }

    .btn-action-text {
        min-height: 34px;
        padding: 0.45rem 0.75rem;
        border-radius: 0.55rem;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #374151;
        text-decoration: none;
        font-size: 0.8rem;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        transition: all 0.2s;
    }

    .btn-action-text:hover {
        background: #f3f4f6;
        color: #111827;
    }

    .empty-state-box {
        padding: 3rem 1rem;
        text-align: center;
    }

    .empty-state-icon {
        width: 56px;
        height: 56px;
        margin: 0 auto 0.8rem;
        border-radius: 1rem;
        background: #f3f4f6;
        color: #9ca3af;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.45rem;
    }

    @media (max-width: 768px) {
        .metric-card {
            min-height: 95px;
        }
    }
</style>

@php
    $pesananCollection = collect($pesanan->items());

    $totalHalaman = $pesananCollection->count();
    $menungguBayarHalaman = $pesananCollection->where('status_pembayaran', 'menunggu_pembayaran')->count();
    $diprosesHalaman = $pesananCollection->where('status', 'diproses')->count();
    $selesaiHalaman = $pesananCollection->where('status', 'selesai')->count();
@endphp

<div class="hero">
    <div>
        <h1>Pesanan</h1>
        <p>Data pesanan real dari tabel pesanan, item pesanan, pembayaran, dan pengiriman.</p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-md-6 col-xl-3">
        <div class="metric-card">
            <div class="metric-label">Pesanan Halaman Ini</div>
            <div class="metric-value">{{ $totalHalaman }}</div>
            <div class="metric-note text-primary">Data checkout</div>
            <i class="bi bi-receipt-cutoff metric-icon"></i>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-3">
        <div class="metric-card">
            <div class="metric-label">Menunggu Bayar</div>
            <div class="metric-value">{{ $menungguBayarHalaman }}</div>
            <div class="metric-note text-warning">Perlu dipantau</div>
            <i class="bi bi-credit-card metric-icon"></i>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-3">
        <div class="metric-card">
            <div class="metric-label">Diproses</div>
            <div class="metric-value">{{ $diprosesHalaman }}</div>
            <div class="metric-note text-info">Pesanan berjalan</div>
            <i class="bi bi-bag-check metric-icon"></i>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-3">
        <div class="metric-card">
            <div class="metric-label">Selesai</div>
            <div class="metric-value">{{ $selesaiHalaman }}</div>
            <div class="metric-note text-success">Transaksi tuntas</div>
            <i class="bi bi-check-circle metric-icon"></i>
        </div>
    </div>
</div>

<div class="sc-box">
    <div class="p-3 border-bottom bg-white">
        <form id="page-filter" class="js-instant-filter" method="GET">
            <div class="row g-3 align-items-center">
                <div class="col-12 col-lg">
                    <div class="search-bar-modern d-flex align-items-center gap-2 px-3 py-2">
                        <i class="bi bi-search text-muted"></i>
                        <input name="q"
                               value="{{ request('q') }}"
                               placeholder="Cari invoice atau nama pembeli...">
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <select class="form-select form-select-modern" name="status">
                        <option value="">Semua status pesanan</option>
                        @foreach(['menunggu_pembayaran','dibayar','diproses','siap_diambil','dalam_pengantaran','selesai','dibatalkan'] as $s)
                            <option value="{{ $s }}" @selected(request('status') === $s)>
                                {{ $statusLabel($s) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <select class="form-select form-select-modern" name="status_pembayaran">
                        <option value="">Semua pembayaran</option>
                        @foreach(['menunggu_pembayaran','dibayar','gagal','kedaluwarsa','dibatalkan'] as $s)
                            <option value="{{ $s }}" @selected(request('status_pembayaran') === $s)>
                                {{ $statusLabel($s) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-enterprise mb-0">
            <thead>
            <tr>
                <th>Invoice</th>
                <th>Pembeli</th>
                <th>Item</th>
                <th>Penerimaan</th>
                <th>Pembayaran</th>
                <th>Status</th>
                <th>Total</th>
                <th class="text-end">Aksi</th>
            </tr>
            </thead>

            <tbody>
            @forelse($pesanan as $order)
                <tr>
                    <td>
                        <div class="invoice-code">{{ $order->nomor_invoice }}</div>
                        <div class="text-muted small fw-semibold mt-1">
                            {{ optional($order->tanggal_pesanan)->format('d M Y H:i') }}
                        </div>
                    </td>

                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="buyer-avatar">
                                {{ strtoupper(substr($order->user?->name ?? 'PB', 0, 2)) }}
                            </div>

                            <div class="min-w-0">
                                <div class="fw-bold text-dark text-truncate">
                                    {{ $order->user?->name ?? '-' }}
                                </div>
                                <div class="text-muted small fw-semibold text-truncate">
                                    {{ $order->user?->email ?? 'Email tidak tersedia' }}
                                </div>
                            </div>
                        </div>
                    </td>

                    <td>
                        <strong>{{ $order->item->sum('jumlah') }} item</strong>
                        <div class="text-muted small fw-semibold">
                            {{ $order->item->count() }} produk
                        </div>
                    </td>

                    <td>
                        <span class="order-method">
                            @if($order->metode_pengambilan === 'kurir_toko')
                                <i class="bi bi-truck text-primary"></i>
                            @else
                                <i class="bi bi-shop text-warning"></i>
                            @endif

                            {{ $statusLabel($order->metode_pengambilan) }}
                        </span>
                    </td>

                    <td>
                        <span class="chip {{ $statusClass($order->status_pembayaran) }}">
                            {{ $statusLabel($order->status_pembayaran) }}
                        </span>
                    </td>

                    <td>
                        <span class="chip {{ $statusClass($order->status) }}">
                            {{ $statusLabel($order->status) }}
                        </span>
                    </td>

                    <td>
                        <span class="total-text">{{ $rupiah($order->total_bayar) }}</span>
                    </td>

                    <td class="text-end">
                        <a class="btn-action-text" href="{{ route('admin.pesanan.show', $order) }}">
                            <i class="bi bi-eye"></i>
                            Detail
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state-box">
                            <div class="empty-state-icon">
                                <i class="bi bi-inbox"></i>
                            </div>
                            <strong class="d-block text-dark mb-1">Belum ada pesanan</strong>
                            <span class="text-muted small">
                                Pesanan dari aplikasi mobile akan muncul di sini setelah pembeli checkout.
                            </span>
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
</div>
@endsection
