@extends('layouts.pembeli')

@section('title', 'Pesanan Saya - SiTahu')

@push('styles')
<style>
    .order-hero {
        padding: 30px;
        margin-bottom: 22px;
        background:
            radial-gradient(circle at top right, rgba(223, 186, 104, 0.25), transparent 32%),
            linear-gradient(135deg, #ffffff 0%, #fff8e8 100%);
    }

    .order-hero h1 {
        margin: 12px 0 0;
        color: var(--heading);
        font-size: clamp(30px, 4.5vw, 48px);
        line-height: 1;
        letter-spacing: -0.075em;
    }

    .order-hero h1 span {
        color: var(--brand-text);
    }

    .order-hero p {
        margin: 12px 0 0;
        max-width: 760px;
        color: var(--muted);
        line-height: 1.7;
        font-size: 15px;
    }

    .status-summary {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 20px;
    }

    .summary-card {
        padding: 16px;
        border-radius: 20px;
        border: 1px solid var(--line);
        background: #ffffff;
        box-shadow: 0 10px 24px rgba(17, 24, 39, 0.05);
    }

    .summary-card span {
        display: block;
        color: var(--muted);
        font-size: 12px;
        font-weight: 800;
        margin-bottom: 7px;
    }

    .summary-card strong {
        display: block;
        color: var(--heading);
        font-size: 24px;
        letter-spacing: -0.06em;
    }

    .filter-card {
        padding: 16px;
        margin-bottom: 20px;
    }

    .filter-row {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }

    .filter-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 38px;
        padding: 8px 14px;
        border-radius: 999px;
        border: 1px solid var(--line);
        background: #ffffff;
        color: var(--muted);
        text-decoration: none;
        font-size: 13px;
        font-weight: 850;
        transition: 0.16s ease;
    }

    .filter-chip:hover {
        transform: translateY(-1px);
        color: var(--heading);
        box-shadow: 0 8px 18px rgba(17, 24, 39, 0.06);
    }

    .filter-chip.active {
        background: var(--brand-soft);
        border-color: rgba(223, 186, 104, 0.55);
        color: var(--brand-text);
    }

    .order-list {
        display: grid;
        gap: 14px;
    }

    .order-card {
        padding: 18px;
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 18px;
        align-items: center;
        transition: 0.18s ease;
    }

    .order-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 28px rgba(17, 24, 39, 0.09);
    }

    .order-title {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }

    .order-title h2 {
        margin: 0;
        color: var(--heading);
        font-size: 18px;
        letter-spacing: -0.04em;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 850;
        border: 1px solid var(--line);
        background: #f9fafb;
        color: var(--muted);
    }

    .status-wait {
        background: #fff8e8;
        color: var(--brand-text);
        border-color: rgba(223, 186, 104, 0.45);
    }

    .status-success {
        background: #ecfdf5;
        color: #15803d;
        border-color: #bbf7d0;
    }

    .status-danger {
        background: #fef2f2;
        color: #b91c1c;
        border-color: #fecaca;
    }

    .status-info {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #bfdbfe;
    }

    .order-info {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        color: var(--muted);
        font-size: 13px;
        line-height: 1.5;
    }

    .order-info strong {
        color: var(--heading);
    }

    .order-products {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 13px;
    }

    .mini-product {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        max-width: 230px;
        padding: 7px 9px;
        border-radius: 999px;
        background: #f9fafb;
        border: 1px solid var(--line);
        color: var(--heading);
        font-size: 12px;
        font-weight: 800;
    }

    .mini-product-img {
        width: 26px;
        height: 26px;
        border-radius: 999px;
        overflow: hidden;
        display: grid;
        place-items: center;
        background: var(--brand-soft);
        color: var(--brand-text);
        font-size: 9px;
        font-weight: 900;
        flex: 0 0 auto;
    }

    .mini-product-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .mini-product span {
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .order-total {
        text-align: right;
        min-width: 190px;
    }

    .order-total span {
        display: block;
        color: var(--muted);
        font-size: 12px;
        margin-bottom: 4px;
        font-weight: 750;
    }

    .order-total strong {
        display: block;
        color: var(--brand-text);
        font-size: 20px;
        letter-spacing: -0.04em;
        margin-bottom: 10px;
    }

    .empty-card {
        padding: 46px 22px;
        text-align: center;
    }

    .empty-icon {
        width: 70px;
        height: 70px;
        margin: 0 auto 16px;
        display: grid;
        place-items: center;
        border-radius: 24px;
        background: var(--brand-soft);
        color: var(--brand-text);
        font-size: 32px;
    }

    .empty-card h2 {
        margin: 0;
        color: var(--heading);
        font-size: 24px;
        letter-spacing: -0.05em;
    }

    .empty-card p {
        margin: 9px auto 18px;
        max-width: 520px;
        color: var(--muted);
        line-height: 1.7;
        font-size: 14px;
    }

    .pagination-wrap {
        margin-top: 20px;
    }

    .pagination-wrap nav {
        display: flex;
        justify-content: center;
    }

    @media (max-width: 980px) {
        .status-summary {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 720px) {
        .order-hero,
        .filter-card,
        .order-card {
            padding: 20px;
        }

        .status-summary {
            grid-template-columns: 1fr;
        }

        .order-card {
            grid-template-columns: 1fr;
        }

        .order-total {
            text-align: left;
            min-width: 0;
        }
    }
</style>
@endpush

@section('content')
@php
    $statusLabels = [
        '' => 'Semua',
        'menunggu_pembayaran' => 'Menunggu Pembayaran',
        'dibayar' => 'Dibayar',
        'diproses' => 'Diproses',
        'siap_diambil' => 'Siap Diambil',
        'dalam_pengantaran' => 'Dalam Pengantaran',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
    ];

    $statusClassMap = [
        'menunggu_pembayaran' => 'status-wait',
        'dibayar' => 'status-info',
        'diproses' => 'status-info',
        'siap_diambil' => 'status-info',
        'dalam_pengantaran' => 'status-info',
        'selesai' => 'status-success',
        'dibatalkan' => 'status-danger',
    ];
@endphp

<section class="page-card order-hero">
    <div class="badge">Pesanan Saya</div>

    <h1>
        Pantau pesananmu <span>langsung dari akun</span>
    </h1>

    <p>
        Semua pesanan yang kamu buat setelah login akan tampil otomatis di halaman ini.
        Kamu tidak perlu mencari invoice atau memasukkan email lagi.
    </p>
</section>

<section class="status-summary">
    <div class="summary-card">
        <span>Semua pesanan</span>
        <strong>{{ $jumlahStatus['semua'] ?? 0 }}</strong>
    </div>

    <div class="summary-card">
        <span>Menunggu bayar</span>
        <strong>{{ $jumlahStatus['menunggu_pembayaran'] ?? 0 }}</strong>
    </div>

    <div class="summary-card">
        <span>Sedang diproses</span>
        <strong>{{ $jumlahStatus['diproses'] ?? 0 }}</strong>
    </div>

    <div class="summary-card">
        <span>Selesai</span>
        <strong>{{ $jumlahStatus['selesai'] ?? 0 }}</strong>
    </div>

    <div class="summary-card">
        <span>Dibatalkan</span>
        <strong>{{ $jumlahStatus['dibatalkan'] ?? 0 }}</strong>
    </div>
</section>

<section class="page-card filter-card">
    <div class="filter-row">
        @foreach($statusLabels as $value => $label)
            <a
                href="{{ $value === '' ? route('pembeli-web.pesanan.index') : route('pembeli-web.pesanan.index', ['status' => $value]) }}"
                class="filter-chip {{ $status === $value ? 'active' : '' }}"
            >
                {{ $label }}
            </a>
        @endforeach
    </div>
</section>

@if($pesananList->count())
    <section class="order-list">
        @foreach($pesananList as $pesanan)
            @php
                $statusPesanan = $pesanan->status;
                $statusPembayaran = $pesanan->status_pembayaran;
                $statusClass = $statusClassMap[$statusPesanan] ?? 'status-wait';

                if (in_array($statusPembayaran, ['gagal', 'kedaluwarsa', 'dibatalkan'])) {
                    $statusClass = 'status-danger';
                }

                $produkPreview = $pesanan->item->take(3);
                $sisaProduk = max(0, $pesanan->item->count() - 3);
            @endphp

            <article class="page-card order-card">
                <div>
                    <div class="order-title">
                        <h2>{{ $pesanan->nomor_invoice }}</h2>

                        <span class="status-pill {{ $statusClass }}">
                            {{ $statusLabels[$pesanan->status] ?? ucwords(str_replace('_', ' ', $pesanan->status)) }}
                        </span>
                    </div>

                    <div class="order-info">
                        <span>
                            Tanggal:
                            <strong>{{ optional($pesanan->tanggal_pesanan)->format('d M Y H:i') }}</strong>
                        </span>

                        <span>
                            Produk:
                            <strong>{{ $pesanan->item->sum('jumlah') }} item</strong>
                        </span>

                        <span>
                            Pembayaran:
                            <strong>{{ ucwords(str_replace('_', ' ', $pesanan->status_pembayaran)) }}</strong>
                        </span>

                        <span>
                            Penerimaan:
                            <strong>{{ $pesanan->metode_pengambilan === 'kurir_toko' ? 'Kurir Toko' : 'Ambil di Toko' }}</strong>
                        </span>
                    </div>

                    <div class="order-products">
                        @foreach($produkPreview as $item)
                            @php
                                $produk = $item->produk;
                                $gambar = $produk?->gambarUtama?->url_gambar;
                            @endphp

                            <div class="mini-product">
                                <div class="mini-product-img">
                                    @if($gambar)
                                        <img src="{{ asset('storage/' . $gambar) }}" alt="{{ $produk?->nama ?? 'Produk' }}">
                                    @else
                                        T
                                    @endif
                                </div>

                                <span>
                                    {{ $produk?->nama ?? 'Produk' }} × {{ $item->jumlah }}
                                </span>
                            </div>
                        @endforeach

                        @if($sisaProduk > 0)
                            <div class="mini-product">
                                <div class="mini-product-img">+</div>
                                <span>{{ $sisaProduk }} produk lain</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="order-total">
                    <span>Total bayar</span>
                    <strong>Rp {{ number_format($pesanan->total_bayar, 0, ',', '.') }}</strong>

                    <a href="{{ route('pembeli-web.pesanan.show', $pesanan->nomor_invoice) }}" class="btn btn-outline">
                        Detail Pesanan
                    </a>
                </div>
            </article>
        @endforeach
    </section>

    <div class="pagination-wrap">
        {{ $pesananList->links() }}
    </div>
@else
    <section class="page-card empty-card">
        <div class="empty-icon">🧾</div>

        <h2>Belum ada pesanan</h2>

        <p>
            Pesananmu akan muncul di sini setelah kamu melakukan checkout.
            Yuk pilih produk tahu dulu dan lanjutkan ke keranjang.
        </p>

        <a href="{{ route('pembeli-web.produk') }}" class="btn btn-primary">
            Lihat Produk Tahu
        </a>
    </section>
@endif
@endsection