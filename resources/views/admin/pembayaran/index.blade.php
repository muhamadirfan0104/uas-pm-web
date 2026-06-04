@extends('layouts.admin')

@section('title', 'Pembayaran - SiTahu')
@section('page_title', 'Pembayaran')

@section('content')
<style>
    .payment-box {
        border: 1px solid var(--border);
        border-radius: 18px;
        background: #fff;
        margin-bottom: 1.5rem;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }

    .payment-metric {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 18px;
        padding: 18px;
        min-height: 116px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        transition: 0.18s ease;
        position: relative;
        overflow: hidden;
    }

    .payment-metric:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow);
        border-color: rgba(223, 186, 104, 0.42);
    }

    .payment-metric-label {
        color: var(--muted);
        font-size: 0.74rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .payment-metric-value {
        margin-top: 8px;
        color: var(--text);
        font-size: 1.75rem;
        line-height: 1;
        font-weight: 950;
        letter-spacing: -0.05em;
    }

    .payment-metric-note {
        display: inline-block;
        margin-top: 8px;
        font-size: 0.76rem;
        font-weight: 850;
    }

    .payment-metric-icon {
        width: 46px;
        height: 46px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.15rem;
    }

    .payment-filter {
        padding: 16px;
        border-bottom: 1px solid var(--border);
        background: #fff;
    }

    .payment-search {
        min-height: 44px;
        padding: 0 14px;
        border: 1px solid var(--border);
        border-radius: 15px;
        background: #f9fafb;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: 0.16s ease;
    }

    .payment-search:focus-within {
        background: #fff;
        border-color: var(--brand);
        box-shadow: 0 0 0 4px rgba(223, 186, 104, 0.16);
    }

    .payment-search input {
        width: 100%;
        border: 0;
        outline: 0;
        background: transparent;
        color: var(--text);
        font-size: 0.9rem;
        font-weight: 650;
    }

    .payment-invoice {
        color: var(--text);
        font-size: 0.92rem;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .buyer-avatar {
        width: 36px;
        height: 36px;
        border-radius: 999px;
        background: var(--brand-soft);
        color: var(--brand-dark);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.76rem;
        font-weight: 900;
        flex-shrink: 0;
    }

    .payment-ref {
        max-width: 180px;
        padding: 7px 10px;
        border-radius: 999px;
        background: #f9fafb;
        border: 1px solid var(--border);
        color: #4b5563;
        font-size: 0.76rem;
        font-weight: 850;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .method-pill {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 10px;
        border-radius: 999px;
        border: 1px solid var(--border);
        background: #fff;
        color: #374151;
        font-size: 0.78rem;
        font-weight: 900;
        white-space: nowrap;
    }

    .payment-total {
        color: var(--text);
        font-size: 0.92rem;
        font-weight: 950;
        white-space: nowrap;
    }

    .payment-status-form {
        min-width: 260px;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
    }

    .payment-status-form select {
        min-width: 150px;
        min-height: 36px;
        border-radius: 12px;
        font-size: 0.78rem;
        font-weight: 800;
        padding: 6px 10px;
    }

    .empty-payment {
        padding: 48px 16px;
        text-align: center;
    }

    .empty-payment-icon {
        width: 58px;
        height: 58px;
        margin: 0 auto 12px;
        border-radius: 18px;
        background: #f3f4f6;
        color: var(--muted);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.45rem;
    }

    @media (max-width: 992px) {
        .payment-status-form {
            min-width: 220px;
        }
    }

    @media (max-width: 640px) {
        .payment-status-form {
            align-items: stretch;
            flex-direction: column;
        }

        .payment-status-form select,
        .payment-status-form button {
            width: 100%;
        }
    }
</style>

@php
    $totalPembayaranHalaman = collect($pembayaran->items())->sum('jumlah');
@endphp

<div class="hero">
    <div>
        <h1>Pembayaran</h1>
        <p>Monitoring pembayaran dari pesanan mobile, termasuk status, metode, reference, dan total tagihan.</p>
    </div>

    <a href="{{ route('admin.pesanan.index') }}" class="btn btn-light border fw-bold px-3">
        <i class="bi bi-receipt me-1 text-muted"></i>
        Lihat Pesanan
    </a>
</div>

<div class="grid g4 mb-4">
    <div class="payment-metric">
        <div>
            <div class="payment-metric-label">Dibayar</div>
            <div class="payment-metric-value">{{ $stats['dibayar'] ?? 0 }}</div>
            <span class="payment-metric-note text-success">Berhasil</span>
        </div>

        <div class="payment-metric-icon bg-success-subtle text-success-emphasis">
            <i class="bi bi-check-circle-fill"></i>
        </div>
    </div>

    <div class="payment-metric">
        <div>
            <div class="payment-metric-label">Menunggu</div>
            <div class="payment-metric-value">{{ $stats['menunggu'] ?? 0 }}</div>
            <span class="payment-metric-note text-warning">Belum bayar</span>
        </div>

        <div class="payment-metric-icon bg-warning-subtle text-warning-emphasis">
            <i class="bi bi-hourglass-split"></i>
        </div>
    </div>

    <div class="payment-metric">
        <div>
            <div class="payment-metric-label">Gagal</div>
            <div class="payment-metric-value">{{ $stats['gagal'] ?? 0 }}</div>
            <span class="payment-metric-note text-danger">Transaksi gagal</span>
        </div>

        <div class="payment-metric-icon bg-danger-subtle text-danger-emphasis">
            <i class="bi bi-x-circle-fill"></i>
        </div>
    </div>

    <div class="payment-metric">
        <div>
            <div class="payment-metric-label">Kedaluwarsa</div>
            <div class="payment-metric-value">{{ $stats['kedaluwarsa'] ?? 0 }}</div>
            <span class="payment-metric-note text-danger">Expired</span>
        </div>

        <div class="payment-metric-icon bg-danger-subtle text-danger-emphasis">
            <i class="bi bi-clock-history"></i>
        </div>
    </div>
</div>

<div class="payment-box">
    <div class="payment-filter">
        <form id="page-filter" class="js-instant-filter" method="GET">
            <div class="row g-3 align-items-center">
                <div class="col-12 col-lg">
                    <div class="payment-search">
                        <i class="bi bi-search text-muted"></i>
                        <input name="q"
                               value="{{ request('q') }}"
                               placeholder="Cari invoice, nama pembeli, atau reference pembayaran...">
                    </div>
                </div>

                <div class="col-12 col-md-5 col-lg-3">
                    <select class="form-select" name="status">
                        <option value="">Semua status</option>
                        @foreach(['menunggu_pembayaran','dibayar','gagal','kedaluwarsa','dibatalkan'] as $s)
                            <option value="{{ $s }}" @selected(request('status') === $s)>
                                {{ $statusLabel($s) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-3 col-lg-2">
                    <a href="{{ route('admin.pembayaran.index') }}" class="btn btn-light border fw-bold w-100">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th>Invoice & Pembeli</th>
                <th>Reference</th>
                <th>Metode</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th>Total</th>
                <th class="text-end">Update Status</th>
            </tr>
            </thead>

            <tbody>
            @forelse($pembayaran as $pay)
                @php
                    $namaPembeli = $pay->pesanan?->user?->name ?? 'Pembeli';
                    $initialPembeli = strtoupper(substr($namaPembeli, 0, 2));
                    $metode = strtoupper($pay->metode_pembayaran ?? '-');
                @endphp

                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="buyer-avatar">
                                {{ $initialPembeli }}
                            </div>

                            <div class="min-w-0">
                                <div class="payment-invoice text-truncate">
                                    {{ $pay->pesanan?->nomor_invoice ?? '-' }}
                                </div>

                                <span class="sub">
                                    {{ $namaPembeli }}
                                </span>
                            </div>
                        </div>
                    </td>

                    <td>
                        <div class="payment-ref" title="{{ $pay->referensi_pembayaran ?? '-' }}">
                            {{ $pay->referensi_pembayaran ?? '-' }}
                        </div>
                    </td>

                    <td>
                        <span class="method-pill">
                            @if(($pay->metode_pembayaran ?? '') === 'qris')
                                <i class="bi bi-qr-code text-primary"></i>
                            @elseif(($pay->metode_pembayaran ?? '') === 'va')
                                <i class="bi bi-bank text-success"></i>
                            @elseif(($pay->metode_pembayaran ?? '') === 'ewallet')
                                <i class="bi bi-wallet2 text-warning"></i>
                            @else
                                <i class="bi bi-credit-card text-muted"></i>
                            @endif

                            {{ $metode }}
                        </span>
                    </td>

                    <td>
                        <span class="chip {{ $statusClass($pay->status) }}">
                            {{ $statusLabel($pay->status) }}
                        </span>
                    </td>

                    <td>
                        <strong>
                            {{ optional($pay->created_at)->format('d M Y') }}
                        </strong>
                        <span class="sub">
                            {{ optional($pay->created_at)->format('H:i') }}
                        </span>
                    </td>

                    <td>
                        <div class="payment-total">
                            {{ $rupiah($pay->jumlah) }}
                        </div>
                    </td>

                    <td class="text-end">
                        <form method="POST"
                              action="{{ route('admin.pembayaran.status', $pay) }}"
                              class="payment-status-form"
                              data-confirm-title="Ubah Status Pembayaran"
                              data-confirm-message="Yakin ingin menyimpan perubahan status pembayaran invoice {{ $pay->pesanan?->nomor_invoice ?? '-' }}?"
                              data-confirm-button="Simpan Status">
                            @csrf
                            @method('PATCH')

                            <select class="form-select" name="status">
                                @foreach(['menunggu_pembayaran','dibayar','gagal','kedaluwarsa','dibatalkan'] as $s)
                                    <option value="{{ $s }}" @selected($pay->status === $s)>
                                        {{ $statusLabel($s) }}
                                    </option>
                                @endforeach
                            </select>

                            <button class="small-btn" type="submit">
                                <i class="bi bi-check2"></i>
                                Update
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-payment">
                            <div class="empty-payment-icon">
                                <i class="bi bi-credit-card"></i>
                            </div>
                            <strong class="d-block text-dark mb-1">Belum ada pembayaran</strong>
                            <span class="text-muted small">
                                Data pembayaran akan muncul setelah pembeli melakukan checkout dari aplikasi mobile.
                            </span>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap p-3 border-top bg-white">
        <div class="text-muted small fw-bold">
            Total pembayaran di halaman ini:
            <span class="text-dark">{{ $rupiah($totalPembayaranHalaman) }}</span>
        </div>

        <div>
            {{ $pembayaran->links() }}
        </div>
    </div>
</div>
@endsection
