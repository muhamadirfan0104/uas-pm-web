@extends('layouts.pembeli')

@section('title', 'Detail Pesanan - SiTahu')

@push('styles')
<style>
    .breadcrumb {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        margin-bottom: 16px;
        color: var(--muted);
        font-size: 13px;
        font-weight: 700;
    }

    .breadcrumb a {
        color: var(--brand-text);
    }

    .detail-hero {
        padding: 30px;
        margin-bottom: 22px;
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 20px;
        align-items: center;
        background:
            radial-gradient(circle at top right, rgba(223, 186, 104, 0.25), transparent 32%),
            linear-gradient(135deg, #ffffff 0%, #fff8e8 100%);
    }

    .detail-hero h1 {
        margin: 12px 0 0;
        color: var(--heading);
        font-size: clamp(30px, 4.5vw, 48px);
        line-height: 1;
        letter-spacing: -0.075em;
    }

    .detail-hero h1 span {
        color: var(--brand-text);
    }

    .detail-hero p {
        margin: 12px 0 0;
        max-width: 720px;
        color: var(--muted);
        line-height: 1.7;
        font-size: 15px;
    }

    .invoice-card {
        padding: 16px;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.78);
        border: 1px solid var(--line);
        min-width: 250px;
    }

    .invoice-card span {
        display: block;
        color: var(--muted);
        font-size: 12px;
        margin-bottom: 4px;
        font-weight: 700;
    }

    .invoice-card strong {
        display: block;
        color: var(--heading);
        font-size: 18px;
        letter-spacing: -0.035em;
    }

    .detail-layout {
        display: grid;
        grid-template-columns: 1fr 370px;
        gap: 20px;
        align-items: start;
    }

    .panel-card {
        padding: 22px;
    }

    .panel-card h2 {
        margin: 0 0 14px;
        color: var(--heading);
        font-size: 22px;
        letter-spacing: -0.045em;
    }

    .status-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .status-box,
    .info-box {
        padding: 15px;
        border-radius: 16px;
        background: #f9fafb;
        border: 1px solid var(--line);
    }

    .status-box span,
    .info-box span {
        display: block;
        color: var(--muted);
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .status-box strong,
    .info-box strong {
        display: block;
        color: var(--heading);
        font-size: 15px;
        line-height: 1.5;
    }

    .status-box.highlight {
        background: var(--brand-soft);
        border-color: rgba(223, 186, 104, 0.45);
    }

    .status-box.highlight strong {
        color: var(--brand-text);
    }

    .product-list {
        display: grid;
        gap: 12px;
    }

    .product-item {
        display: grid;
        grid-template-columns: 72px 1fr auto;
        gap: 12px;
        align-items: center;
        padding: 12px;
        border-radius: 18px;
        background: #f9fafb;
        border: 1px solid var(--line);
    }

    .product-img {
        width: 72px;
        height: 72px;
        border-radius: 16px;
        overflow: hidden;
        background: #ffffff;
        border: 1px solid var(--line);
        display: grid;
        place-items: center;
        color: var(--brand-text);
        font-size: 10px;
        font-weight: 900;
    }

    .product-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-info h3 {
        margin: 0;
        color: var(--heading);
        font-size: 15px;
        line-height: 1.35;
    }

    .product-info p {
        margin: 4px 0 0;
        color: var(--muted);
        font-size: 13px;
    }

    .product-subtotal {
        text-align: right;
        color: var(--heading);
        font-weight: 900;
        font-size: 14px;
    }

    .side-card {
        padding: 22px;
        position: sticky;
        top: 96px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 13px 0;
        border-bottom: 1px solid var(--line);
        color: var(--muted);
        font-size: 14px;
    }

    .summary-row strong {
        color: var(--heading);
    }

    .summary-total {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 16px 0 18px;
        color: var(--heading);
        font-weight: 900;
        font-size: 18px;
    }

    .summary-total span:last-child {
        color: var(--brand-text);
    }

    .payment-box {
        margin-top: 16px;
        padding: 15px;
        border-radius: 16px;
        background: var(--brand-soft);
        border: 1px solid rgba(223, 186, 104, 0.45);
    }

    .payment-box span {
        display: block;
        color: var(--muted);
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .payment-box strong {
        display: block;
        color: var(--brand-text);
        font-size: 15px;
        line-height: 1.5;
    }

    .action-list {
        display: grid;
        gap: 10px;
        margin-top: 16px;
    }

    .info-grid {
        display: grid;
        gap: 12px;
    }

    @media (max-width: 940px) {
        .detail-hero,
        .detail-layout {
            grid-template-columns: 1fr;
        }

        .side-card {
            position: static;
        }
    }

    @media (max-width: 650px) {
        .detail-hero,
        .panel-card,
        .side-card {
            padding: 20px;
        }

        .status-grid {
            grid-template-columns: 1fr;
        }

        .product-item {
            grid-template-columns: 1fr;
        }

        .product-img {
            width: 100%;
            height: 190px;
        }

        .product-subtotal {
            text-align: left;
        }
    }
</style>
@endpush

@section('content')
@php
    $statusPesanan = ucwords(str_replace('_', ' ', $pesanan->status));
    $statusPembayaran = ucwords(str_replace('_', ' ', $pesanan->status_pembayaran));
    $metodePenerimaan = $pesanan->metode_pengambilan === 'kurir_toko' ? 'Kurir toko' : 'Ambil di toko';
    $metodePembayaran = '-';
    if ($pesanan->pembayaran) {
        $metodePembayaran = $pesanan->pembayaran->metode_pembayaran === 'tunai'
            ? 'Tunai'
            : 'QRIS';
    }
@endphp

<div class="breadcrumb">
    <a href="{{ route('pembeli-web.home') }}">Beranda</a>
    <span>/</span>
    <a href="{{ route('pembeli-web.pesanan.index') }}">Pesanan Saya</a>
    <span>/</span>
    <span>{{ $pesanan->nomor_invoice }}</span>
</div>

<section class="page-card detail-hero">
    <div>
        <div class="badge">Detail Pesanan</div>

        <h1>
            Pesananmu sedang <span>kami proses</span>
        </h1>

        <p>
            Di halaman ini kamu bisa melihat produk yang dipesan, status pembayaran,
            dan informasi pengambilan atau pengantaran.
        </p>
    </div>

    <div class="invoice-card">
        <span>Nomor invoice</span>
        <strong>{{ $pesanan->nomor_invoice }}</strong>
    </div>
</section>

<section class="detail-layout">
    <div style="display: grid; gap: 20px;">
        <div class="page-card panel-card">
            <h2>Status pesanan</h2>

            <div class="status-grid">
                <div class="status-box highlight">
                    <span>Status pesanan</span>
                    <strong>{{ $statusPesanan }}</strong>
                </div>

                <div class="status-box highlight">
                    <span>Status pembayaran</span>
                    <strong>{{ $statusPembayaran }}</strong>
                </div>

                <div class="status-box">
                    <span>Tanggal pesanan</span>
                    <strong>{{ optional($pesanan->tanggal_pesanan)->format('d M Y H:i') }}</strong>
                </div>

                <div class="status-box">
                    <span>Metode penerimaan</span>
                    <strong>{{ $metodePenerimaan }}</strong>
                </div>
            </div>
        </div>

        <div class="page-card panel-card">
            <h2>Produk yang dipesan</h2>

            <div class="product-list">
                @foreach($pesanan->item as $item)
                    @php
                        $produk = $item->produk;
                        $gambar = $produk?->gambarUtama?->url_gambar;
                    @endphp

                    <div class="product-item">
                        <div class="product-img">
                            @if($gambar)
                                <img src="{{ asset('storage/' . $gambar) }}" alt="{{ $produk?->nama ?? 'Produk' }}">
                            @else
                                TAHU
                            @endif
                        </div>

                        <div class="product-info">
                            <h3>{{ $produk?->nama ?? 'Produk' }}</h3>
                            <p>
                                {{ $item->jumlah }} × Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="product-subtotal">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="page-card panel-card">
            <h2>Informasi penerimaan</h2>

            <div class="info-grid">
                @if($pesanan->metode_pengambilan === 'kurir_toko')
                    <div class="info-box">
                        <span>Alamat tujuan</span>
                        <strong>
                            {{ $pesanan->alamatPengiriman?->alamat_lengkap ?? $pesanan->pengiriman?->alamat_tujuan ?? 'Alamat tujuan belum tersedia.' }}
                        </strong>
                    </div>

                    <div class="info-box">
                        <span>Status pengantaran</span>
                        <strong>
                            {{ $pesanan->pengiriman?->status_pengiriman
                                ? ucwords(str_replace('_', ' ', $pesanan->pengiriman->status_pengiriman))
                                : 'Menunggu proses toko' }}
                        </strong>
                    </div>
                @else
                    <div class="info-box">
                        <span>Alamat toko</span>
                        <strong>{{ $pesanan->pengiriman?->alamat_toko ?? 'Alamat toko belum tersedia.' }}</strong>
                    </div>

                    <div class="info-box">
                        <span>Informasi ambil pesanan</span>
                        <strong>Pesanan bisa diambil setelah toko menandai pesanan siap diambil.</strong>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <aside class="page-card side-card">
        <h2>Ringkasan bayar</h2>

        <div>
            <div class="summary-row">
                <span>Subtotal produk</span>
                <strong>Rp {{ number_format($pesanan->subtotal_produk, 0, ',', '.') }}</strong>
            </div>

            <div class="summary-row">
                <span>Biaya pengantaran</span>
                <strong>Rp {{ number_format($pesanan->biaya_pengiriman, 0, ',', '.') }}</strong>
            </div>

            <div class="summary-total">
                <span>Total bayar</span>
                <span>Rp {{ number_format($pesanan->total_bayar, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="payment-box">
            <span>Metode pembayaran</span>
            <strong>{{ $metodePembayaran }}</strong>

            @if($pesanan->pembayaran?->referensi_pembayaran)
                <span style="margin-top: 12px;">Referensi pembayaran</span>
                <strong>{{ $pesanan->pembayaran->referensi_pembayaran }}</strong>
            @endif

            @if($pesanan->pembayaran?->metode_pembayaran === 'qris' && $pesanan->pembayaran?->qr_code)
                <span style="margin-top: 12px;">Kode QR / invoice</span>
                <strong>{{ $pesanan->pembayaran->qr_code }}</strong>
            @endif

            @if($pesanan->pembayaran?->metode_pembayaran === 'tunai')
                <span style="margin-top: 12px;">Catatan pembayaran</span>
                <strong>Pembayaran tunai dilakukan saat pesanan diambil atau diterima.</strong>
            @endif
        </div>

        <div class="action-list">
            <a href="{{ route('pembeli-web.pesanan.index', ['keyword' => $pesanan->nomor_invoice]) }}" class="btn btn-outline">
                Kembali ke Pencarian
            </a>

            <a href="{{ route('pembeli-web.produk') }}" class="btn btn-primary">
                Belanja Lagi
            </a>
        </div>
    </aside>
</section>
@endsection
