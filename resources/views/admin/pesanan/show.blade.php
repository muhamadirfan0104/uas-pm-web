@extends('layouts.admin')

@section('title', 'Detail Pesanan - SiTahu')
@section('page_title', 'Detail Pesanan')

@section('content')
<style>
    .detail-shell {
        display: grid;
        grid-template-columns: minmax(0, 1.25fr) minmax(340px, 0.75fr);
        gap: 18px;
        align-items: start;
    }

    .detail-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 18px;
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }

    .detail-card-head {
        padding: 18px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        background: #fff;
    }

    .detail-card-title {
        margin: 0;
        font-size: 1rem;
        font-weight: 900;
        color: var(--text);
        letter-spacing: -0.03em;
    }

    .detail-card-desc {
        margin: 4px 0 0;
        color: var(--muted);
        font-size: 0.82rem;
        font-weight: 650;
    }

    .detail-card-body {
        padding: 18px;
    }

    .invoice-panel {
        position: relative;
        overflow: hidden;
        border-radius: 22px;
        border: 1px solid var(--border);
        background:
            radial-gradient(circle at top right, rgba(223, 186, 104, 0.24), transparent 36%),
            linear-gradient(135deg, #ffffff, #fff8e8);
        box-shadow: var(--shadow-sm);
        padding: 22px;
        margin-bottom: 18px;
    }

    .invoice-panel::after {
        content: "";
        position: absolute;
        width: 180px;
        height: 180px;
        right: -68px;
        bottom: -92px;
        border-radius: 999px;
        background: rgba(223, 186, 104, 0.16);
    }

    .invoice-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 11px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.78);
        border: 1px solid rgba(223, 186, 104, 0.25);
        color: var(--brand-dark);
        font-size: 0.76rem;
        font-weight: 900;
        margin-bottom: 12px;
        position: relative;
        z-index: 1;
    }

    .invoice-title {
        position: relative;
        z-index: 1;
        margin: 0;
        color: var(--text);
        font-size: 1.55rem;
        font-weight: 950;
        letter-spacing: -0.05em;
        line-height: 1.1;
    }

    .invoice-desc {
        position: relative;
        z-index: 1;
        margin: 8px 0 0;
        color: var(--muted);
        font-size: 0.9rem;
        font-weight: 650;
    }

    .invoice-actions {
        position: relative;
        z-index: 1;
        margin-top: 18px;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .mini-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 18px;
    }

    .mini-summary {
        padding: 15px;
        border-radius: 17px;
        border: 1px solid var(--border);
        background: #fff;
        box-shadow: var(--shadow-sm);
        min-height: 96px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .mini-summary-label {
        color: var(--muted);
        font-size: 0.72rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .mini-summary-value {
        margin-top: 6px;
        color: var(--text);
        font-size: 1.05rem;
        font-weight: 950;
        letter-spacing: -0.04em;
        line-height: 1.1;
    }

    .mini-summary-icon {
        width: 42px;
        height: 42px;
        border-radius: 15px;
        background: var(--brand-soft);
        color: var(--brand-dark);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .info-list {
        display: grid;
        gap: 0;
    }

    .info-row {
        padding: 14px 0;
        border-bottom: 1px dashed var(--border);
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .info-row:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .info-icon {
        width: 38px;
        height: 38px;
        border-radius: 14px;
        background: #f9fafb;
        color: var(--brand-dark);
        border: 1px solid var(--border);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .info-label {
        color: var(--muted);
        font-size: 0.74rem;
        font-weight: 850;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .info-value {
        margin-top: 3px;
        color: var(--text);
        font-size: 0.9rem;
        font-weight: 750;
        line-height: 1.45;
    }

    .status-form-grid {
        display: grid;
        gap: 14px;
    }

    .status-form-grid label {
        color: var(--muted);
        font-size: 0.76rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 7px;
    }

    .timeline {
        display: grid;
        gap: 0;
        margin-top: 6px;
    }

    .timeline-item {
        position: relative;
        display: flex;
        gap: 12px;
        padding: 0 0 18px;
    }

    .timeline-item:last-child {
        padding-bottom: 0;
    }

    .timeline-item::before {
        content: "";
        position: absolute;
        left: 16px;
        top: 35px;
        bottom: 0;
        width: 2px;
        background: #f0f1f3;
    }

    .timeline-item:last-child::before {
        display: none;
    }

    .timeline-dot {
        width: 34px;
        height: 34px;
        border-radius: 999px;
        background: var(--brand-soft);
        color: var(--brand-dark);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.78rem;
        flex-shrink: 0;
        border: 1px solid rgba(223, 186, 104, 0.25);
    }

    .timeline-title {
        color: var(--text);
        font-size: 0.87rem;
        font-weight: 900;
    }

    .timeline-desc {
        margin-top: 3px;
        color: var(--muted);
        font-size: 0.76rem;
        font-weight: 650;
        line-height: 1.45;
    }

    .product-cell {
        min-width: 260px;
    }

    .product-avatar {
        width: 44px;
        height: 44px;
        border-radius: 15px;
        background: var(--brand-soft);
        color: var(--brand-dark);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .money-summary {
        display: flex;
        justify-content: flex-end;
        padding: 18px;
        border-top: 1px solid var(--border);
        background: #fff;
    }

    .money-box {
        width: min(100%, 390px);
        display: grid;
        gap: 10px;
    }

    .money-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        color: var(--muted);
        font-size: 0.88rem;
        font-weight: 750;
    }

    .money-row strong {
        color: var(--text);
        font-weight: 900;
    }

    .money-total {
        margin-top: 4px;
        padding-top: 12px;
        border-top: 1px dashed var(--border);
        color: var(--text);
        font-size: 1rem;
        font-weight: 950;
    }

    .money-total strong {
        font-size: 1.28rem;
        color: var(--brand-dark);
        letter-spacing: -0.04em;
    }

    @media (max-width: 1200px) {
        .detail-shell {
            grid-template-columns: 1fr;
        }

        .mini-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 640px) {
        .mini-summary-grid {
            grid-template-columns: 1fr;
        }

        .invoice-title {
            font-size: 1.25rem;
        }

        .invoice-actions {
            flex-direction: column;
        }

        .invoice-actions .btn,
        .invoice-actions .small-btn {
            width: 100%;
            justify-content: center;
        }

        .money-summary {
            justify-content: stretch;
        }
    }
</style>

@php
    $jumlahProduk = $pesanan->item->count();
    $jumlahItem = $pesanan->item->sum('jumlah');

    $alamatTujuan = $pesanan->alamatPengiriman?->alamat_lengkap
        ?? $pesanan->pengiriman?->alamat_tujuan
        ?? 'Ambil di toko';

    $namaPembeli = $pesanan->user?->name ?? 'Pembeli';
    $initialPembeli = strtoupper(substr($namaPembeli, 0, 2));
@endphp

<div class="invoice-panel">
    <div class="invoice-badge">
        <i class="bi bi-receipt-cutoff"></i>
        Invoice Pesanan
    </div>

    <h1 class="invoice-title">{{ $pesanan->nomor_invoice }}</h1>

    <p class="invoice-desc">
        Dibuat pada {{ optional($pesanan->tanggal_pesanan)->format('d M Y H:i') ?? '-' }}.
        Kelola status pesanan, pembayaran, dan detail produk yang dipesan pembeli.
    </p>

    <div class="invoice-actions">
        <a class="btn btn-light border fw-bold px-3" href="{{ route('admin.pesanan.index') }}">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali
        </a>

        <a class="btn btn-brand px-3" href="{{ route('admin.pembayaran.index') }}">
            <i class="bi bi-credit-card me-1"></i>
            Cek Pembayaran
        </a>
    </div>
</div>

<div class="mini-summary-grid">
    <div class="mini-summary">
        <div>
            <div class="mini-summary-label">Status Pesanan</div>
            <div class="mini-summary-value">
                <span class="chip {{ $statusClass($pesanan->status) }}">
                    {{ $statusLabel($pesanan->status) }}
                </span>
            </div>
        </div>

        <div class="mini-summary-icon">
            <i class="bi bi-bag-check"></i>
        </div>
    </div>

    <div class="mini-summary">
        <div>
            <div class="mini-summary-label">Pembayaran</div>
            <div class="mini-summary-value">
                <span class="chip {{ $statusClass($pesanan->status_pembayaran) }}">
                    {{ $statusLabel($pesanan->status_pembayaran) }}
                </span>
            </div>
        </div>

        <div class="mini-summary-icon">
            <i class="bi bi-credit-card-2-front"></i>
        </div>
    </div>

    <div class="mini-summary">
        <div>
            <div class="mini-summary-label">Total Item</div>
            <div class="mini-summary-value">{{ $jumlahItem }} item</div>
        </div>

        <div class="mini-summary-icon">
            <i class="bi bi-box-seam"></i>
        </div>
    </div>

    <div class="mini-summary">
        <div>
            <div class="mini-summary-label">Total Bayar</div>
            <div class="mini-summary-value">{{ $rupiah($pesanan->total_bayar) }}</div>
        </div>

        <div class="mini-summary-icon">
            <i class="bi bi-cash-stack"></i>
        </div>
    </div>
</div>

<div class="detail-shell mb-4">
    <div class="detail-card">
        <div class="detail-card-head">
            <div>
                <h2 class="detail-card-title">Informasi Pembeli & Penerimaan</h2>
                <p class="detail-card-desc">Data pembeli, alamat, dan metode pengambilan/pengantaran.</p>
            </div>
        </div>

        <div class="detail-card-body">
            <div class="info-list">
                <div class="info-row">
                    <div class="info-icon">
                        <i class="bi bi-person"></i>
                    </div>

                    <div class="min-w-0">
                        <div class="info-label">Pembeli</div>
                        <div class="info-value">
                            <strong>{{ $namaPembeli }}</strong><br>
                            <span class="text-muted">
                                {{ $pesanan->user?->email ?? '-' }}
                                @if($pesanan->user?->telepon)
                                    · {{ $pesanan->user?->telepon }}
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-icon">
                        @if($pesanan->metode_pengambilan === 'kurir_toko')
                            <i class="bi bi-truck"></i>
                        @else
                            <i class="bi bi-shop"></i>
                        @endif
                    </div>

                    <div class="min-w-0">
                        <div class="info-label">Metode Penerimaan</div>
                        <div class="info-value">
                            {{ $statusLabel($pesanan->metode_pengambilan) }}
                        </div>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-icon">
                        <i class="bi bi-geo-alt"></i>
                    </div>

                    <div class="min-w-0">
                        <div class="info-label">Alamat</div>
                        <div class="info-value">
                            {{ $alamatTujuan }}
                        </div>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-icon">
                        <i class="bi bi-signpost-split"></i>
                    </div>

                    <div class="min-w-0">
                        <div class="info-label">Jarak & Biaya Pengiriman</div>
                        <div class="info-value">
                            {{ $pesanan->jarak_km ?? 0 }} km
                            ·
                            {{ $rupiah($pesanan->biaya_pengiriman ?? 0) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="detail-card">
        <div class="detail-card-head">
            <div>
                <h2 class="detail-card-title">Update Status</h2>
                <p class="detail-card-desc">Ubah status pesanan dan pembayaran dari admin.</p>
            </div>
        </div>

        <div class="detail-card-body">
            <form method="POST"
                  action="{{ route('admin.pesanan.status', $pesanan) }}"
                  class="status-form-grid"
                  data-confirm-title="Ubah Status Pesanan"
                  data-confirm-message="Yakin ingin mengubah status pesanan {{ $pesanan->nomor_invoice }}?"
                  data-confirm-button="Ubah Status">
                @csrf
                @method('PATCH')

                <div>
                    <label>Status Pesanan</label>
                    <select class="form-select" name="status">
                        @foreach(['menunggu_pembayaran','dibayar','diproses','siap_diambil','dalam_pengantaran','selesai','dibatalkan'] as $s)
                            <option value="{{ $s }}" @selected($pesanan->status === $s)>
                                {{ $statusLabel($s) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label>Status Pembayaran</label>
                    <select class="form-select" name="status_pembayaran">
                        @foreach(['menunggu_pembayaran','dibayar','gagal','kedaluwarsa','dibatalkan'] as $s)
                            <option value="{{ $s }}" @selected($pesanan->status_pembayaran === $s)>
                                {{ $statusLabel($s) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button class="btn btn-brand fw-bold w-100 mt-1" type="submit">
                    <i class="bi bi-check2-circle me-1"></i>
                    Simpan Status
                </button>
            </form>
        </div>
    </div>
</div>

<div class="detail-shell">
    <div class="detail-card">
        <div class="detail-card-head">
            <div>
                <h2 class="detail-card-title">Item Pesanan</h2>
                <p class="detail-card-desc">{{ $jumlahProduk }} produk berbeda dalam pesanan ini.</p>
            </div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>Produk</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th class="text-end">Subtotal</th>
                </tr>
                </thead>

                <tbody>
                @forelse($pesanan->item as $item)
                    <tr>
                        <td class="product-cell">
                            <div class="d-flex align-items-center gap-3">
                                <div class="product-avatar">
                                    <i class="bi bi-box-seam"></i>
                                </div>

                                <div class="min-w-0">
                                    <div class="fw-bold text-dark text-truncate">
                                        {{ $item->produk?->nama ?? '-' }}
                                    </div>
                                    <div class="text-muted small fw-semibold">
                                        Produk ID: {{ $item->produk_id }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td>
                            <strong>{{ $item->jumlah }}</strong>
                            <span class="sub">unit</span>
                        </td>

                        <td>
                            <strong>{{ $rupiah($item->harga_satuan) }}</strong>
                            <span class="sub">harga checkout</span>
                        </td>

                        <td class="text-end">
                            <strong>{{ $rupiah($item->subtotal) }}</strong>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">
                            <div class="py-5 text-center">
                                <div class="empty-state-icon">
                                    <i class="bi bi-inbox"></i>
                                </div>
                                <strong class="d-block text-dark mb-1">Item pesanan kosong</strong>
                                <span class="text-muted small">Belum ada produk pada pesanan ini.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="money-summary">
            <div class="money-box">
                <div class="money-row">
                    <span>Subtotal Produk</span>
                    <strong>{{ $rupiah($pesanan->subtotal_produk) }}</strong>
                </div>

                <div class="money-row">
                    <span>Biaya Pengiriman</span>
                    <strong>{{ $rupiah($pesanan->biaya_pengiriman ?? 0) }}</strong>
                </div>

                <div class="money-row money-total">
                    <span>Total Bayar</span>
                    <strong>{{ $rupiah($pesanan->total_bayar) }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="detail-card">
        <div class="detail-card-head">
            <div>
                <h2 class="detail-card-title">Ringkasan Alur</h2>
                <p class="detail-card-desc">Panduan cepat status pesanan.</p>
            </div>
        </div>

        <div class="detail-card-body">
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-dot">
                        <i class="bi bi-cart-check"></i>
                    </div>
                    <div>
                        <div class="timeline-title">Checkout Dibuat</div>
                        <div class="timeline-desc">
                            Pembeli membuat pesanan melalui aplikasi mobile.
                        </div>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-dot">
                        <i class="bi bi-credit-card"></i>
                    </div>
                    <div>
                        <div class="timeline-title">Pembayaran</div>
                        <div class="timeline-desc">
                            Status saat ini:
                            <strong>{{ $statusLabel($pesanan->status_pembayaran) }}</strong>.
                        </div>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-dot">
                        @if($pesanan->metode_pengambilan === 'kurir_toko')
                            <i class="bi bi-truck"></i>
                        @else
                            <i class="bi bi-shop"></i>
                        @endif
                    </div>
                    <div>
                        <div class="timeline-title">Penerimaan</div>
                        <div class="timeline-desc">
                            Metode:
                            <strong>{{ $statusLabel($pesanan->metode_pengambilan) }}</strong>.
                        </div>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-dot">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                    <div>
                        <div class="timeline-title">Status Pesanan</div>
                        <div class="timeline-desc">
                            Status saat ini:
                            <strong>{{ $statusLabel($pesanan->status) }}</strong>.
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 p-3 rounded-4 border bg-light">
                <div class="fw-bold text-dark mb-1">
                    Catatan admin
                </div>
                <div class="text-muted small fw-semibold">
                    Setelah pembayaran valid, ubah status pesanan sesuai proses toko:
                    diproses, siap diambil, dalam pengantaran, lalu selesai.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
