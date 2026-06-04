@extends('layouts.admin')
@section('title', 'Laporan - SiTahu')

@section('content')
<style>
    .sc-box { border: 1px solid #e5e7eb; border-radius: 0.75rem; background: #fff; margin-bottom: 1.5rem; overflow: hidden; box-shadow: 0 1px 2px rgba(0,0,0,0.02); }
    .sc-header { padding: 1.25rem 1.5rem; font-weight: 700; font-size: 1rem; border-bottom: 1px solid #f3f4f6; color: #111827; display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; }
    .metric-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1.5rem; display: flex; flex-direction: column; justify-content: center; position: relative; overflow: hidden; transition: all 0.2s; }
    .metric-card:hover { border-color: var(--brand-color, #dfba68); box-shadow: 0 4px 12px rgba(223, 186, 104, 0.15); transform: translateY(-2px); }
    .metric-label { font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; margin-bottom: 0.5rem; }
    .metric-value { font-size: 1.85rem; font-weight: 800; letter-spacing: -0.03em; color: #111827; line-height: 1.1; }
    .metric-icon { position: absolute; right: -10px; bottom: -15px; font-size: 5rem; opacity: 0.04; color: #111827; }
    .form-label-modern { font-size: 0.8rem; font-weight: 700; color: #4b5563; margin-bottom: 0.4rem; }
    .form-control-modern, .form-select-modern { background-color: #f9fafb; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.85rem; transition: all 0.2s; }
    .form-control-modern:focus, .form-select-modern:focus { background-color: #ffffff; border-color: var(--brand-color, #dfba68); box-shadow: 0 0 0 3px rgba(223, 186, 104, 0.15); outline: none; }
    .table-enterprise th { border-bottom: 2px solid #e5e7eb; color: #6b7280; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 1rem 1.5rem; font-weight: 600; background: #fafafa; }
    .table-enterprise td { vertical-align: middle; padding: 1rem 1.5rem; border-bottom: 1px solid #f3f4f6; color: #111827; font-size: 0.9rem; }
    .table-enterprise tbody tr:hover { background-color: #f9fafb; }
    .list-row { transition: background-color 0.15s ease; border-bottom: 1px solid #f3f4f6; padding: 1rem 1.5rem; }
    .list-row:last-child { border-bottom: none; }
    .list-row:hover { background-color: #f9fafb; }
    .thumb-box { width: 40px; height: 40px; border-radius: 0.5rem; background-color: #f3f4f6; border: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700; color: #9ca3af; flex-shrink: 0; }
</style>

<div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3 mb-4">
    <div>
        <h1 class="h4 fw-bold text-dark mb-1">Laporan & Analitik</h1>
        <p class="text-muted small mb-0">Pantau penjualan, pesanan, dan tren produk. Filter berjalan otomatis tanpa tombol.</p>
    </div>
    <div>
        <a class="btn shadow-sm fw-bold px-4 text-white d-flex align-items-center gap-2" href="{{ route('admin.laporan.export.csv', request()->query()) }}" style="background: var(--brand-color, #dfba68);">
            <i class="bi bi-file-earmark-spreadsheet"></i> Export ke CSV
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="metric-card h-100">
            <div class="metric-label">Total Penjualan</div>
            <div class="metric-value">{{ $rupiah($ringkasan['total_penjualan']) }}</div>
            <i class="bi bi-wallet2 metric-icon"></i>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="metric-card h-100">
            <div class="metric-label">Total Pesanan</div>
            <div class="metric-value">{{ $ringkasan['total_pesanan'] }} <span class="fs-6 fw-normal text-muted">Trx</span></div>
            <i class="bi bi-cart3 metric-icon"></i>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="metric-card h-100" style="border-left: 4px solid #10b981;">
            <div class="metric-label text-success">Pesanan Selesai</div>
            <div class="metric-value">{{ $ringkasan['pesanan_selesai'] }}</div>
            <i class="bi bi-check-circle metric-icon"></i>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="metric-card h-100" style="border-left: 4px solid #ef4444;">
            <div class="metric-label text-danger">Dibatalkan</div>
            <div class="metric-value">{{ $ringkasan['pesanan_dibatalkan'] }}</div>
            <i class="bi bi-x-circle metric-icon"></i>
        </div>
    </div>
</div>

<div class="sc-box mb-4">
    <div class="sc-header bg-light py-3">
        <span><i class="bi bi-funnel text-muted me-2"></i> Parameter Laporan</span>
    </div>
    <div class="p-3 p-md-4">
        <form id="page-filter" class="js-instant-filter row g-3 align-items-end" method="GET">
            <div class="col-12 col-md-4 col-xl-3">
                <label class="form-label-modern">Cari Invoice / Pembeli</label>
                <input class="form-control form-control-modern" type="search" name="q" value="{{ request('q') }}" placeholder="Ketik untuk mencari...">
            </div>
            <div class="col-12 col-md-3 col-xl-2">
                <label class="form-label-modern">Jenis Laporan</label>
                <select class="form-select form-select-modern fw-medium" name="jenis">
                    <option value="penjualan" @selected($jenis==='penjualan')>Penjualan Keseluruhan</option>
                    <option value="pesanan" @selected($jenis==='pesanan')>Data Pesanan</option>
                    <option value="produk" @selected($jenis==='produk')>Produk Terlaris</option>
                </select>
            </div>
            <div class="col-12 col-sm-6 col-md-2">
                <label class="form-label-modern">Tanggal Mulai</label>
                <input class="form-control form-control-modern" type="date" name="tanggal_mulai" value="{{ $tanggalMulai->format('Y-m-d') }}">
            </div>
            <div class="col-12 col-sm-6 col-md-2">
                <label class="form-label-modern">Tanggal Selesai</label>
                <input class="form-control form-control-modern" type="date" name="tanggal_selesai" value="{{ $tanggalSelesai->format('Y-m-d') }}">
            </div>
            <div class="col-12 col-md-3 col-xl-2">
                <label class="form-label-modern">Status</label>
                <select class="form-select form-select-modern fw-medium" name="status">
                    <option value="">Semua Status</option>
                    @foreach(['menunggu_pembayaran','diproses','siap_diambil','dalam_pengantaran','selesai','dibatalkan'] as $status)
                        <option value="{{ $status }}" @selected(request('status')===$status)>{{ $statusLabel($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <div class="text-muted small"><i class="bi bi-lightning-charge me-1"></i>Semua filter otomatis memuat ulang data.</div>
            </div>
        </form>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-xl-8">
        <div class="sc-box mb-0">
            <div class="sc-header">
                <span>Data Pesanan</span>
                <span class="badge bg-light text-secondary border rounded-pill">{{ $pesanan->total() }} data</span>
            </div>
            <div class="table-responsive">
                <table class="table table-enterprise table-borderless mb-0">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Pembeli</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($pesanan as $order)
                        @php
                            $badgeStatus = match($order->status) {
                                'selesai' => 'bg-success-subtle text-success-emphasis',
                                'dibatalkan' => 'bg-danger-subtle text-danger-emphasis',
                                'menunggu_pembayaran' => 'bg-warning-subtle text-warning-emphasis',
                                default => 'bg-primary-subtle text-primary-emphasis',
                            };
                        @endphp
                        <tr>
                            <td><strong>{{ $order->nomor_invoice }}</strong></td>
                            <td>
                                <div class="fw-semibold">{{ $order->user?->name ?? '-' }}</div>
                                <div class="small text-muted">{{ $order->user?->email ?? '-' }}</div>
                            </td>
                            <td>{{ optional($order->tanggal_pesanan)->format('d M Y H:i') }}</td>
                            <td><span class="badge rounded-pill {{ $badgeStatus }}">{{ $statusLabel($order->status) }}</span></td>
                            <td class="text-end"><strong>{{ $rupiah($order->total_bayar) }}</strong></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-5 text-muted">Tidak ada data pesanan.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @if($pesanan->hasPages())
                <div class="border-top bg-light p-3">{{ $pesanan->links() }}</div>
            @endif
        </div>
    </div>

    <div class="col-12 col-xl-4">
        <div class="sc-box mb-0">
            <div class="sc-header">
                <span>Produk Terlaris</span>
            </div>
            <div>
                @forelse($produkTerlaris as $index => $produk)
                    <div class="list-row d-flex align-items-center gap-3">
                        <div class="thumb-box">{{ $index + 1 }}</div>
                        <div class="flex-grow-1">
                            <strong class="d-block">{{ $produk->nama }}</strong>
                            <span class="small text-muted">{{ (int)($produk->total_terjual ?? 0) }} terjual</span>
                        </div>
                        <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill">{{ $rupiah($produk->harga) }}</span>
                    </div>
                @empty
                    <div class="p-4 text-center text-muted">Belum ada data produk.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
