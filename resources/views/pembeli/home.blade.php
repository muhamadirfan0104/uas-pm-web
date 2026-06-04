@extends('layouts.pembeli')

@section('title', 'Beranda Pembeli - SiTahu')

@push('styles')
<style>
    .home-hero {
        display: grid;
        grid-template-columns: 1.08fr 0.92fr;
        gap: 24px;
        align-items: stretch;
        margin-bottom: 24px;
    }

    .hero-main {
        padding: 34px;
        overflow: hidden;
        position: relative;
        background:
            radial-gradient(circle at top right, rgba(223, 186, 104, 0.28), transparent 34%),
            linear-gradient(135deg, #ffffff 0%, #fff8e8 100%);
    }

    .hero-main::after {
        content: "";
        position: absolute;
        width: 230px;
        height: 230px;
        right: -70px;
        bottom: -85px;
        border-radius: 999px;
        background: rgba(223, 186, 104, 0.20);
    }

    .hero-content {
        position: relative;
        z-index: 2;
    }

    .hero-title {
        margin: 16px 0 0;
        max-width: 680px;
        font-size: clamp(34px, 5vw, 58px);
        line-height: 0.98;
        letter-spacing: -0.075em;
        color: var(--heading);
    }

    .hero-title span {
        color: var(--brand-text);
    }

    .hero-desc {
        margin: 16px 0 0;
        max-width: 620px;
        color: var(--muted);
        line-height: 1.75;
        font-size: 15px;
    }

    .hero-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 24px;
    }

    .hero-mini-info {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        margin-top: 28px;
    }

    .mini-card {
        padding: 14px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.72);
        border: 1px solid rgba(229, 231, 235, 0.95);
    }

    .mini-card strong {
        display: block;
        color: var(--heading);
        font-size: 14px;
        margin-bottom: 4px;
    }

    .mini-card span {
        display: block;
        color: var(--muted);
        font-size: 12px;
        line-height: 1.45;
    }

    .hero-side {
        display: grid;
        gap: 16px;
    }

    .store-card {
        padding: 24px;
    }

    .store-header {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 18px;
    }

    .store-logo {
        width: 58px;
        height: 58px;
        border-radius: 18px;
        display: grid;
        place-items: center;
        background: linear-gradient(135deg, var(--brand-color), #c89335);
        color: white;
        font-weight: 900;
        font-size: 20px;
        box-shadow: 0 12px 22px rgba(223, 186, 104, 0.28);
        overflow: hidden;
    }

    .store-logo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .store-header h2 {
        margin: 0;
        color: var(--heading);
        font-size: 21px;
        letter-spacing: -0.04em;
    }

    .store-header p {
        margin: 4px 0 0;
        color: var(--muted);
        font-size: 13px;
    }

    .info-list {
        display: grid;
        gap: 11px;
    }

    .info-item {
        display: grid;
        grid-template-columns: 34px 1fr;
        gap: 10px;
        align-items: start;
        padding: 12px;
        border-radius: 15px;
        background: #f9fafb;
        border: 1px solid var(--line);
    }

    .info-icon {
        width: 34px;
        height: 34px;
        display: grid;
        place-items: center;
        border-radius: 12px;
        background: var(--brand-soft);
        color: var(--brand-text);
    }

    .info-item strong {
        display: block;
        color: var(--heading);
        font-size: 13px;
        margin-bottom: 2px;
    }

    .info-item span {
        display: block;
        color: var(--muted);
        font-size: 13px;
        line-height: 1.5;
    }

    .feature-card {
        padding: 20px;
        background: #ffffff;
    }

    .feature-row {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .feature-item {
        padding: 14px;
        border-radius: 16px;
        background: #f9fafb;
        border: 1px solid var(--line);
    }

    .feature-item strong {
        display: block;
        color: var(--heading);
        font-size: 14px;
        margin-bottom: 4px;
    }

    .feature-item span {
        color: var(--muted);
        font-size: 12px;
        line-height: 1.45;
    }

    .section-head {
        display: flex;
        align-items: end;
        justify-content: space-between;
        gap: 16px;
        margin: 34px 0 16px;
    }

    .section-head h2 {
        margin: 0;
        color: var(--heading);
        font-size: 28px;
        letter-spacing: -0.055em;
    }

    .section-head p {
        margin: 6px 0 0;
        color: var(--muted);
        font-size: 14px;
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }

    .product-card {
        overflow: hidden;
        transition: 0.18s ease;
    }

    .product-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 28px rgba(17, 24, 39, 0.10);
    }

    .product-img {
        height: 168px;
        background:
            radial-gradient(circle at top right, rgba(223, 186, 104, 0.22), transparent 34%),
            #f9fafb;
        border-bottom: 1px solid var(--line);
        display: grid;
        place-items: center;
        color: var(--brand-text);
        font-weight: 900;
        font-size: 13px;
        letter-spacing: 0.06em;
    }

    .product-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-body {
        padding: 16px;
    }

    .product-name {
        margin: 0;
        color: var(--heading);
        font-size: 15px;
        font-weight: 850;
        line-height: 1.35;
    }

    .product-meta {
        margin-top: 6px;
        color: var(--muted);
        font-size: 12px;
        line-height: 1.45;
    }

    .product-price {
        margin-top: 12px;
        color: var(--brand-text);
        font-size: 17px;
        font-weight: 900;
    }

    .product-action {
        margin-top: 12px;
        display: flex;
        gap: 8px;
    }

    .product-action .btn {
        min-height: 38px;
        padding: 9px 12px;
        font-size: 13px;
        width: 100%;
    }

    .empty-card {
        padding: 22px;
        text-align: center;
        color: var(--muted);
    }

    .empty-card strong {
        display: block;
        color: var(--heading);
        margin-bottom: 6px;
    }

    .simple-banner {
        margin-top: 34px;
        padding: 24px;
        display: flex;
        justify-content: space-between;
        gap: 16px;
        align-items: center;
        background:
            linear-gradient(135deg, #ffffff 0%, #fff8e8 100%);
    }

    .simple-banner h2 {
        margin: 0;
        color: var(--heading);
        font-size: 24px;
        letter-spacing: -0.05em;
    }

    .simple-banner p {
        margin: 6px 0 0;
        color: var(--muted);
        line-height: 1.6;
        font-size: 14px;
    }

    @media (max-width: 960px) {
        .home-hero {
            grid-template-columns: 1fr;
        }

        .product-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 720px) {
        .hero-main,
        .store-card,
        .feature-card,
        .simple-banner {
            padding: 20px;
        }

        .hero-mini-info,
        .feature-row,
        .product-grid {
            grid-template-columns: 1fr;
        }

        .section-head {
            align-items: start;
            flex-direction: column;
        }

        .simple-banner {
            align-items: start;
            flex-direction: column;
        }
    }
</style>
@endpush

@section('content')
@php
    $namaToko = $pengaturan->nama ?: 'SiTahu';
    $alamatToko = $pengaturan->alamat ?: 'Alamat toko belum diatur.';
    $tentangToko = $pengaturan->tentang ?: 'Toko tahu rumahan yang menyediakan berbagai pilihan tahu segar untuk kebutuhan harian.';
    $areaPengiriman = $pengaturan->area_pengiriman ?: 'Area pengiriman akan diinformasikan saat pemesanan.';
    $jamBuka = $pengaturan->jam_buka ?: null;
    $jamTutup = $pengaturan->jam_tutup ?: null;
    $jamOperasional = $jamBuka && $jamTutup ? $jamBuka . ' - ' . $jamTutup : ($jamBuka ?: 'Jam operasional akan segera diperbarui.');

    $teleponToko = $pengaturan->telepon ?: '';
    $nomorWa = preg_replace('/[^0-9]/', '', $teleponToko);

    if ($nomorWa && str_starts_with($nomorWa, '0')) {
        $nomorWa = '62' . substr($nomorWa, 1);
    }

    $pesanWa = 'Halo ' . $namaToko . ', saya ingin tanya produk tahu.';
    $linkWa = $nomorWa ? 'https://wa.me/' . $nomorWa . '?text=' . urlencode($pesanWa) : route('pembeli-web.coming-soon');
@endphp

<section class="home-hero">
    <div class="page-card hero-main">
        <div class="hero-content">
            <div class="badge">Tahu segar pilihan keluarga</div>

            <h1 class="hero-title">
                Belanja produk tahu segar dari <span>{{ $namaToko }}</span>
            </h1>

            <p class="hero-desc">
                {{ $tentangToko }}
                Temukan pilihan tahu segar untuk lauk harian, camilan keluarga, atau teman makan yang praktis dan enak.
            </p>

            <div class="hero-actions">
                <a href="{{ route('pembeli-web.produk') }}" class="btn btn-primary">
                    Lihat Produk
                </a>

                <a href="{{ $linkWa }}" target="{{ $nomorWa ? '_blank' : '_self' }}" class="btn btn-whatsapp">
                    WhatsApp Toko
                </a>

                <a href="{{ route('pembeli-web.pesanan.index') }}" class="btn btn-outline">
                    Pesanan Saya
                </a>
            </div>

            <div class="hero-mini-info">
                <div class="mini-card">
                    <strong>Pilihan segar</strong>
                    <span>Beragam produk tahu siap kamu pilih</span>
                </div>

                <div class="mini-card">
                    <strong>Mudah diterima</strong>
                    <span>Bisa ambil di toko atau pakai kurir toko</span>
                </div>

                <div class="mini-card">
                    <strong>Pesan praktis</strong>
                    <span>Lihat produk dan mulai pesan dari browser</span>
                </div>
            </div>
        </div>
    </div>

    <div class="hero-side">
        <div class="page-card store-card">
            <div class="store-header">
                <div class="store-logo">
                    @if($pengaturan->logo_url)
                        <img src="{{ asset('storage/' . $pengaturan->logo_url) }}" alt="{{ $namaToko }}">
                    @else
                        ST
                    @endif
                </div>

                <div>
                    <h2>{{ $namaToko }}</h2>
                    <p>Kenali toko kami lebih dekat</p>
                </div>
            </div>

            <div class="info-list">
                <div class="info-item">
                    <div class="info-icon">📍</div>
                    <div>
                        <strong>Alamat toko</strong>
                        <span>{{ $alamatToko }}</span>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">🕘</div>
                    <div>
                        <strong>Jam operasional</strong>
                        <span>{{ $jamOperasional }}</span>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">🚚</div>
                    <div>
                        <strong>Area pengiriman</strong>
                        <span>{{ $areaPengiriman }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="page-card feature-card">
            <div class="feature-row">
                <div class="feature-item">
                    <strong>Selalu fresh</strong>
                    <span>Produk tahu disiapkan untuk kebutuhan harianmu.</span>
                </div>

                <div class="feature-item">
                    <strong>Belanja nyaman</strong>
                    <span>Pilih produk, cek informasi, lalu pesan dengan mudah.</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section>
    <div class="section-head">
        <div>
            <h2>Produk pilihan hari ini</h2>
            <p>Pilihan tahu segar yang cocok untuk lauk, camilan, atau stok di rumah.</p>
        </div>

        <a href="{{ route('pembeli-web.produk') }}" class="btn btn-outline">
            Lihat Semua
        </a>
    </div>

    @if($produkTerbaru->count())
        <div class="product-grid">
            @foreach($produkTerbaru as $produk)
                <article class="page-card product-card">
                    <div class="product-img">
                        @if($produk->gambarUtama && $produk->gambarUtama->url_gambar)
                            <img src="{{ asset('storage/' . $produk->gambarUtama->url_gambar) }}" alt="{{ $produk->nama }}">
                        @else
                            PRODUK TAHU
                        @endif
                    </div>

                    <div class="product-body">
                        <h3 class="product-name">{{ $produk->nama }}</h3>

                        <div class="product-meta">
                            {{ $produk->satuan ?: 'Satuan belum tersedia' }}
                            @if($produk->isi_per_satuan)
                                · isi {{ $produk->isi_per_satuan }}
                            @endif
                            · stok {{ $produk->stok }}
                        </div>

                        <div class="product-price">
                            Rp {{ number_format($produk->harga, 0, ',', '.') }}
                        </div>

                        <div class="product-action">
                            <a href="{{ route('pembeli-web.produk') }}" class="btn btn-outline">
                                Lihat Produk
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="page-card empty-card">
            <strong>Produk belum tersedia</strong>
            Kami sedang menyiapkan pilihan tahu terbaik untuk kamu. Coba cek lagi nanti ya.
        </div>
    @endif
</section>

@if($produkTerlaris->count())
    <section>
        <div class="section-head">
            <div>
                <h2>Favorit pelanggan</h2>
                <p>Produk yang banyak disukai dan sering jadi pilihan pembeli.</p>
            </div>
        </div>

        <div class="product-grid">
            @foreach($produkTerlaris as $produk)
                <article class="page-card product-card">
                    <div class="product-img">
                        @if($produk->gambarUtama && $produk->gambarUtama->url_gambar)
                            <img src="{{ asset('storage/' . $produk->gambarUtama->url_gambar) }}" alt="{{ $produk->nama }}">
                        @else
                            FAVORIT
                        @endif
                    </div>

                    <div class="product-body">
                        <h3 class="product-name">{{ $produk->nama }}</h3>

                        <div class="product-meta">
                            Sering dipilih pelanggan · stok {{ $produk->stok }}
                        </div>

                        <div class="product-price">
                            Rp {{ number_format($produk->harga, 0, ',', '.') }}
                        </div>

                        <div class="product-action">
                            <a href="{{ route('pembeli-web.produk') }}" class="btn btn-outline">
                                Lihat Produk
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endif

<section class="page-card simple-banner">
    <div>
        <h2>Siap pilih tahu favoritmu?</h2>
        <p>
            Yuk lihat pilihan produk tahu yang tersedia. Cocok untuk lauk keluarga,
            camilan sore, atau stok praktis di rumah.
        </p>
    </div>

    <a href="{{ route('pembeli-web.produk') }}" class="btn btn-primary">
        Pilih Produk Sekarang
    </a>
</section>
@endsection
