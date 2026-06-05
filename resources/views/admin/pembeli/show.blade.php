@extends('layouts.admin')

@section('title', 'Detail Pembeli - SiTahu')
@section('page_title', 'Detail Pembeli')

@section('content')
@php
    $initial = strtoupper(substr($pembeli->name ?? 'P', 0, 2));

    $statusClass = [
        'menunggu_pembayaran' => 'bg-warning-subtle text-warning-emphasis',
        'dibayar' => 'bg-primary-subtle text-primary-emphasis',
        'diproses' => 'bg-info-subtle text-info-emphasis',
        'siap_diambil' => 'bg-success-subtle text-success-emphasis',
        'dalam_pengantaran' => 'bg-primary-subtle text-primary-emphasis',
        'selesai' => 'bg-success-subtle text-success-emphasis',
        'dibatalkan' => 'bg-danger-subtle text-danger-emphasis',
    ];

    $formatStatus = function ($value) {
        return ucwords(str_replace('_', ' ', (string) $value));
    };
@endphp

<style>
    .buyer-hero {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 18px;
        align-items: center;
        margin-bottom: 20px;
        padding: 24px;
        border-radius: 24px;
        border: 1px solid var(--border);
        background:
            radial-gradient(circle at top right, rgba(223, 186, 104, .24), transparent 35%),
            linear-gradient(135deg, #ffffff, #fff8e8);
        box-shadow: var(--shadow-soft);
    }

    .buyer-profile {
        display: flex;
        align-items: center;
        gap: 16px;
        min-width: 0;
    }

    .buyer-avatar {
        width: 72px;
        height: 72px;
        border-radius: 24px;
        display: grid;
        place-items: center;
        background: linear-gradient(135deg, var(--brand), #c89335);
        color: #fff;
        font-size: 1.45rem;
        font-weight: 950;
        letter-spacing: -.06em;
        box-shadow: 0 14px 28px rgba(223, 186, 104, .28);
        flex-shrink: 0;
    }

    .buyer-profile h1 {
        margin: 0;
        color: var(--text);
        font-size: clamp(1.45rem, 3vw, 2rem);
        font-weight: 950;
        letter-spacing: -.06em;
        line-height: 1.1;
    }

    .buyer-profile p {
        margin: 7px 0 0;
        color: var(--muted);
        font-size: .9rem;
        font-weight: 650;
        line-height: 1.5;
    }

    .buyer-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .buyer-card {
        border: 1px solid var(--border);
        border-radius: 22px;
        background: #fff;
        box-shadow: var(--shadow-soft);
        overflow: hidden;
    }

    .buyer-card-head {
        padding: 18px;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: flex-start;
    }

    .buyer-card-head h2 {
        margin: 0;
        color: var(--text);
        font-size: 1.02rem;
        font-weight: 950;
        letter-spacing: -.035em;
    }

    .buyer-card-head p {
        margin: 5px 0 0;
        color: var(--muted);
        font-size: .8rem;
        font-weight: 650;
        line-height: 1.5;
    }

    .buyer-info-list {
        padding: 6px 18px 18px;
        display: grid;
        gap: 10px;
    }

    .buyer-info-row {
        display: grid;
        grid-template-columns: 160px minmax(0, 1fr);
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px dashed #eef0f3;
        font-size: .88rem;
    }

    .buyer-info-row:last-child {
        border-bottom: 0;
    }

    .buyer-info-row span {
        color: var(--muted);
        font-weight: 850;
    }

    .buyer-info-row strong {
        color: var(--text);
        font-weight: 900;
        min-width: 0;
        overflow-wrap: anywhere;
    }

    .buyer-metric {
        min-height: 116px;
        padding: 18px;
        border: 1px solid var(--border);
        border-radius: 20px;
        background: #fff;
        box-shadow: var(--shadow-soft);
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
    }

    .buyer-metric-label {
        color: var(--muted);
        font-size: .75rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .055em;
    }

    .buyer-metric-value {
        margin-top: 8px;
        color: var(--text);
        font-size: 1.55rem;
        font-weight: 950;
        line-height: 1;
        letter-spacing: -.055em;
    }

    .buyer-metric-note {
        display: inline-block;
        margin-top: 8px;
        font-size: .76rem;
        font-weight: 850;
    }

    .buyer-metric-icon {
        width: 44px;
        height: 44px;
        border-radius: 15px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
        font-size: 1.1rem;
    }

    .address-list,
    .order-list {
        display: grid;
    }

    .address-row,
    .order-row {
        padding: 16px 18px;
        border-top: 1px solid #f1f2f4;
        display: grid;
        gap: 8px;
        transition: .16s ease;
    }

    .order-row {
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: center;
        text-decoration: none;
    }

    .order-row:hover {
        background: #fafafa;
    }

    .address-title,
    .order-title {
        color: var(--text);
        font-size: .93rem;
        font-weight: 950;
        letter-spacing: -.02em;
    }

    .address-sub,
    .order-sub {
        color: var(--muted);
        font-size: .79rem;
        font-weight: 700;
        line-height: 1.55;
    }

    .address-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 4px;
    }

    .empty-box {
        padding: 34px 18px;
        text-align: center;
        color: var(--muted);
        font-size: .85rem;
        font-weight: 700;
    }

    @media (max-width: 900px) {
        .buyer-hero {
            grid-template-columns: 1fr;
        }

        .buyer-actions {
            justify-content: flex-start;
        }

        .order-row {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 600px) {
        .buyer-hero {
            padding: 18px;
        }

        .buyer-profile {
            align-items: flex-start;
        }

        .buyer-avatar {
            width: 58px;
            height: 58px;
            border-radius: 20px;
        }

        .buyer-info-row {
            grid-template-columns: 1fr;
            gap: 5px;
        }
    }
</style>

<section class="buyer-hero">
    <div class="buyer-profile">
        <div class="buyer-avatar">
            {{ $initial }}
        </div>

        <div class="min-w-0">
            <h1>{{ $pembeli->name }}</h1>

            <p>
                {{ $pembeli->email }}
                @if($pembeli->telepon)
                    · {{ $pembeli->telepon }}
                @endif
            </p>

            <div class="mt-2">
                <span class="badge {{ $pembeli->aktif ? 'bg-success-subtle text-success-emphasis' : 'bg-danger-subtle text-danger-emphasis' }}">
                    {{ $pembeli->aktif ? 'Akun Aktif' : 'Akun Nonaktif' }}
                </span>

                <span class="badge bg-warning-subtle text-warning-emphasis">
                    Pembeli
                </span>
            </div>
        </div>
    </div>

    <div class="buyer-actions">
        <a href="{{ route('admin.pembeli.index') }}" class="btn btn-light border fw-bold px-3">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali
        </a>

        <form
            method="POST"
            action="{{ route('admin.pembeli.toggle', $pembeli) }}"
            class="inline-form"
            data-confirm-title="Ubah Status Pembeli"
            data-confirm-message="Yakin ingin mengubah status akun pembeli ini?"
            data-confirm-button="Ubah Status"
        >
            @csrf
            @method('PATCH')

            <button type="submit" class="btn btn-brand px-3">
                @if($pembeli->aktif)
                    <i class="bi bi-person-x me-1"></i>
                    Nonaktifkan
                @else
                    <i class="bi bi-person-check me-1"></i>
                    Aktifkan
                @endif
            </button>
        </form>
    </div>
</section>

<div class="grid g4 mb-4">
    <div class="buyer-metric">
        <div>
            <div class="buyer-metric-label">Total Pesanan</div>
            <div class="buyer-metric-value">{{ $totalPesanan }}</div>
            <span class="buyer-metric-note text-primary">Semua transaksi</span>
        </div>

        <div class="buyer-metric-icon bg-primary-subtle text-primary-emphasis">
            <i class="bi bi-bag-check-fill"></i>
        </div>
    </div>

    <div class="buyer-metric">
        <div>
            <div class="buyer-metric-label">Pesanan Aktif</div>
            <div class="buyer-metric-value">{{ $pesananAktif }}</div>
            <span class="buyer-metric-note text-warning">Belum selesai</span>
        </div>

        <div class="buyer-metric-icon bg-warning-subtle text-warning-emphasis">
            <i class="bi bi-hourglass-split"></i>
        </div>
    </div>

    <div class="buyer-metric">
        <div>
            <div class="buyer-metric-label">Pesanan Selesai</div>
            <div class="buyer-metric-value">{{ $pesananSelesai }}</div>
            <span class="buyer-metric-note text-success">Sudah diterima</span>
        </div>

        <div class="buyer-metric-icon bg-success-subtle text-success-emphasis">
            <i class="bi bi-check-circle-fill"></i>
        </div>
    </div>

    <div class="buyer-metric">
        <div>
            <div class="buyer-metric-label">Total Belanja</div>
            <div class="buyer-metric-value" style="font-size:1.08rem;">
                {{ $rupiah($totalBelanja) }}
            </div>
            <span class="buyer-metric-note text-success">Pembayaran dibayar</span>
        </div>

        <div class="buyer-metric-icon bg-success-subtle text-success-emphasis">
            <i class="bi bi-cash-stack"></i>
        </div>
    </div>
</div>

<div class="grid g2 mb-4">
    <section class="buyer-card">
        <div class="buyer-card-head">
            <div>
                <h2>Data Akun Pembeli</h2>
                <p>Informasi akun yang digunakan pembeli untuk login.</p>
            </div>
        </div>

        <div class="buyer-info-list">
            <div class="buyer-info-row">
                <span>Nama</span>
                <strong>{{ $pembeli->name }}</strong>
            </div>

            <div class="buyer-info-row">
                <span>Email</span>
                <strong>{{ $pembeli->email }}</strong>
            </div>

            <div class="buyer-info-row">
                <span>Telepon</span>
                <strong>{{ $pembeli->telepon ?: '-' }}</strong>
            </div>

            <div class="buyer-info-row">
                <span>Status</span>
                <strong>{{ $pembeli->aktif ? 'Aktif' : 'Nonaktif' }}</strong>
            </div>

            <div class="buyer-info-row">
                <span>Terdaftar</span>
                <strong>{{ optional($pembeli->created_at)->format('d/m/Y H:i') ?? '-' }}</strong>
            </div>

            <div class="buyer-info-row">
                <span>Total Ulasan</span>
                <strong>{{ $totalUlasan }} ulasan</strong>
            </div>
        </div>
    </section>

    <section class="buyer-card">
        <div class="buyer-card-head">
            <div>
                <h2>Alamat Utama</h2>
                <p>Alamat utama pembeli untuk pengiriman kurir toko.</p>
            </div>
        </div>

        @if($alamatUtama)
            <div class="buyer-info-list">
                <div class="buyer-info-row">
                    <span>Penerima</span>
                    <strong>{{ $alamatUtama->nama_penerima }}</strong>
                </div>

                <div class="buyer-info-row">
                    <span>Telepon</span>
                    <strong>{{ $alamatUtama->telepon }}</strong>
                </div>

                <div class="buyer-info-row">
                    <span>Alamat</span>
                    <strong>{{ $alamatUtama->alamat_lengkap }}</strong>
                </div>

                <div class="buyer-info-row">
                    <span>Koordinat</span>
                    <strong>
                        @if($alamatUtama->latitude && $alamatUtama->longitude)
                            {{ $alamatUtama->latitude }}, {{ $alamatUtama->longitude }}
                            <br>
                            <a
                                href="https://www.google.com/maps?q={{ $alamatUtama->latitude }},{{ $alamatUtama->longitude }}"
                                target="_blank"
                                class="small-btn mt-2"
                            >
                                <i class="bi bi-map"></i>
                                Buka Maps
                            </a>
                        @else
                            -
                        @endif
                    </strong>
                </div>
            </div>
        @else
            <div class="empty-box">
                Pembeli belum memiliki alamat utama.
            </div>
        @endif
    </section>
</div>

<div class="grid g2">
    <section class="buyer-card">
        <div class="buyer-card-head">
            <div>
                <h2>Daftar Alamat</h2>
                <p>Semua alamat yang tersimpan pada akun pembeli.</p>
            </div>
        </div>

        <div class="address-list">
            @forelse($pembeli->alamat as $alamat)
                <div class="address-row">
                    <div>
                        <div class="address-title">
                            {{ $alamat->nama_penerima }}
                            @if($alamat->utama)
                                <span class="badge bg-warning-subtle text-warning-emphasis ms-1">
                                    Utama
                                </span>
                            @endif
                        </div>

                        <div class="address-sub">
                            {{ $alamat->telepon }} · {{ $alamat->alamat_lengkap }}
                        </div>

                        @if($alamat->latitude && $alamat->longitude)
                            <div class="address-actions">
                                <a
                                    href="https://www.google.com/maps?q={{ $alamat->latitude }},{{ $alamat->longitude }}"
                                    target="_blank"
                                    class="small-btn"
                                >
                                    <i class="bi bi-map"></i>
                                    Buka Maps
                                </a>

                                <span class="small-btn">
                                    {{ $alamat->latitude }}, {{ $alamat->longitude }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="empty-box">
                    Belum ada alamat tersimpan.
                </div>
            @endforelse
        </div>
    </section>

    <section class="buyer-card">
        <div class="buyer-card-head">
            <div>
                <h2>Riwayat Pesanan</h2>
                <p>Pesanan terbaru yang dibuat oleh pembeli ini.</p>
            </div>
        </div>

        <div class="order-list">
            @forelse($pembeli->pesanan as $order)
                <a href="{{ route('admin.pesanan.show', $order) }}" class="order-row">
                    <div class="min-w-0">
                        <div class="order-title">
                            {{ $order->nomor_invoice }}
                        </div>

                        <div class="order-sub">
                            {{ optional($order->tanggal_pesanan)->format('d/m/Y H:i') ?? '-' }}
                            · {{ $order->item->count() }} produk
                            · {{ $formatStatus($order->metode_pengambilan) }}
                        </div>
                    </div>

                    <div class="text-end">
                        <div class="fw-bold text-dark">
                            {{ $rupiah($order->total_bayar ?? 0) }}
                        </div>

                        <span class="badge {{ $statusClass[$order->status] ?? 'bg-secondary-subtle text-secondary-emphasis' }}">
                            {{ $formatStatus($order->status) }}
                        </span>
                    </div>
                </a>
            @empty
                <div class="empty-box">
                    Pembeli ini belum pernah membuat pesanan.
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection