@extends('layouts.pembeli')

@section('title', $produk->nama . ' - SiTahu')

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

    .detail-grid {
        display: grid;
        grid-template-columns: 0.95fr 1.05fr;
        gap: 24px;
        align-items: start;
    }

    .gallery-card {
        padding: 16px;
        position: sticky;
        top: 98px;
    }

    .main-image {
        height: 420px;
        border-radius: 20px;
        overflow: hidden;
        display: grid;
        place-items: center;
        background:
            radial-gradient(circle at top right, rgba(223, 186, 104, 0.24), transparent 35%),
            #f9fafb;
        border: 1px solid var(--line);
        color: var(--brand-text);
        font-size: 15px;
        font-weight: 900;
        letter-spacing: 0.08em;
    }

    .main-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .thumb-row {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
        margin-top: 12px;
    }

    .thumb {
        height: 82px;
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid var(--line);
        background: #f9fafb;
        display: grid;
        place-items: center;
        color: var(--muted);
        font-size: 11px;
        font-weight: 800;
    }

    .thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .detail-card {
        padding: 28px;
        background:
            radial-gradient(circle at top right, rgba(223, 186, 104, 0.18), transparent 30%),
            #ffffff;
    }

    .product-status {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 16px;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 8px 11px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 850;
        border: 1px solid var(--line);
        background: #ffffff;
    }

    .status-ready {
        background: #ecfdf5;
        color: #15803d;
        border-color: #bbf7d0;
    }

    .status-empty {
        background: #fef2f2;
        color: #b91c1c;
        border-color: #fecaca;
    }

    .detail-title {
        margin: 0;
        color: var(--heading);
        font-size: clamp(32px, 4.5vw, 50px);
        line-height: 1;
        letter-spacing: -0.075em;
    }

    .detail-desc {
        margin: 14px 0 0;
        color: var(--muted);
        line-height: 1.8;
        font-size: 15px;
    }

    .detail-price {
        margin-top: 20px;
        color: var(--brand-text);
        font-size: 34px;
        font-weight: 950;
        letter-spacing: -0.055em;
    }

    .detail-meta-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 11px;
        margin-top: 20px;
    }

    .meta-box {
        padding: 14px;
        border-radius: 16px;
        background: #f9fafb;
        border: 1px solid var(--line);
    }

    .meta-box strong {
        display: block;
        color: var(--heading);
        font-size: 13px;
        margin-bottom: 4px;
    }

    .meta-box span {
        display: block;
        color: var(--muted);
        font-size: 13px;
        line-height: 1.45;
    }

    .detail-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 24px;
    }

    .detail-actions .btn {
        min-height: 46px;
    }

    .detail-actions form {
        margin: 0;
    }

    .section-block {
        margin-top: 24px;
    }

    .section-card {
        padding: 24px;
    }

    .section-card h2 {
        margin: 0;
        color: var(--heading);
        font-size: 24px;
        letter-spacing: -0.05em;
    }

    .section-card p {
        margin: 8px 0 0;
        color: var(--muted);
        line-height: 1.75;
        font-size: 14px;
    }

    .tips-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        margin-top: 14px;
    }

    .tip-card {
        padding: 16px;
        border-radius: 16px;
        background: #f9fafb;
        border: 1px solid var(--line);
    }

    .tip-card .icon {
        width: 38px;
        height: 38px;
        display: grid;
        place-items: center;
        border-radius: 13px;
        background: var(--brand-soft);
        color: var(--brand-text);
        margin-bottom: 10px;
    }

    .tip-card strong {
        display: block;
        color: var(--heading);
        font-size: 14px;
        margin-bottom: 5px;
    }

    .tip-card span {
        display: block;
        color: var(--muted);
        font-size: 13px;
        line-height: 1.55;
    }

    .review-head {
        display: flex;
        justify-content: space-between;
        align-items: end;
        gap: 16px;
        margin-bottom: 16px;
    }

    .rating-box {
        padding: 10px 13px;
        border-radius: 14px;
        background: var(--brand-soft);
        color: var(--brand-text);
        font-weight: 900;
        border: 1px solid rgba(223, 186, 104, 0.35);
    }

    .review-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .review-card {
        padding: 16px;
        border-radius: 16px;
        background: #f9fafb;
        border: 1px solid var(--line);
    }

    .review-top {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 8px;
    }

    .review-top strong {
        color: var(--heading);
        font-size: 14px;
    }

    .stars {
        color: #d99a20;
        font-size: 13px;
        letter-spacing: 1px;
        white-space: nowrap;
    }

    .review-card p {
        margin: 0;
        color: var(--muted);
        font-size: 13px;
        line-height: 1.65;
    }

    .review-photo {
        margin-top: 12px;
        height: 130px;
        border-radius: 14px;
        overflow: hidden;
        background: #ffffff;
        border: 1px solid var(--line);
    }

    .review-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .review-video {
    margin-top: 12px;
    border-radius: 14px;
    overflow: hidden;
    background: #ffffff;
    border: 1px solid var(--line);
    }

    .review-video video {
        width: 100%;
        max-height: 220px;
        display: block;
        background: #000;
    }

    .empty-review {
        padding: 18px;
        border-radius: 16px;
        background: #f9fafb;
        border: 1px dashed var(--line);
        color: var(--muted);
        font-size: 14px;
        line-height: 1.65;
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-top: 16px;
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
        height: 150px;
        background:
            radial-gradient(circle at top right, rgba(223, 186, 104, 0.22), transparent 34%),
            #f9fafb;
        border-bottom: 1px solid var(--line);
        display: grid;
        place-items: center;
        color: var(--brand-text);
        font-weight: 900;
        font-size: 12px;
        letter-spacing: 0.06em;
        overflow: hidden;
    }

    .product-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-body {
        padding: 14px;
    }

    .product-name {
        margin: 0;
        color: var(--heading);
        font-size: 14px;
        font-weight: 850;
        line-height: 1.35;
    }

    .product-price {
        margin-top: 8px;
        color: var(--brand-text);
        font-size: 16px;
        font-weight: 900;
    }

    @media (max-width: 980px) {
        .detail-grid {
            grid-template-columns: 1fr;
        }

        .gallery-card {
            position: static;
        }

        .tips-grid,
        .product-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 650px) {
        .detail-card,
        .section-card,
        .gallery-card {
            padding: 18px;
        }

        .main-image {
            height: 280px;
        }

        .thumb-row,
        .detail-meta-grid,
        .tips-grid,
        .review-grid,
        .product-grid {
            grid-template-columns: 1fr;
        }

        .review-head {
            align-items: start;
            flex-direction: column;
        }

        .detail-actions {
            flex-direction: column;
        }

        .detail-actions .btn {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
@php
    $gambarUtama = $produk->gambarUtama?->url_gambar;
    $gambarProduk = $produk->gambar ?? collect();

    $stokTersedia = (int) $produk->stok > 0;

    $deskripsi = $produk->deskripsi
        ?: 'Produk tahu segar yang cocok untuk kebutuhan harian keluarga. Nikmat diolah untuk lauk sederhana, camilan, maupun menu pendamping makan.';

    $masaSimpan = $produk->masa_simpan
        ? $produk->masa_simpan . ' hari'
        : 'Ikuti saran penyimpanan agar tahu tetap segar.';

    $saranPenyimpanan = $produk->saran_penyimpanan
        ?: 'Simpan di tempat sejuk atau lemari pendingin agar kualitas tahu tetap terjaga.';

    $saranPenyajian = $produk->saran_penyajian
        ?: 'Bisa digoreng hangat, ditumis, dijadikan campuran sayur, atau diolah sesuai selera keluarga.';

    $teleponToko = $pengaturan->telepon ?: '';
    $nomorWa = preg_replace('/[^0-9]/', '', $teleponToko);

    if ($nomorWa && str_starts_with($nomorWa, '0')) {
        $nomorWa = '62' . substr($nomorWa, 1);
    }

    $pesanWa = 'Halo ' . ($pengaturan->nama ?: 'SiTahu') . ', saya ingin tanya produk ' . $produk->nama . '.';
    $linkWa = $nomorWa ? 'https://wa.me/' . $nomorWa . '?text=' . urlencode($pesanWa) : route('pembeli-web.coming-soon');
@endphp

<div class="breadcrumb">
    <a href="{{ route('pembeli-web.home') }}">Beranda</a>
    <span>/</span>
    <a href="{{ route('pembeli-web.produk') }}">Produk</a>
    <span>/</span>
    <span>{{ $produk->nama }}</span>
</div>

<section class="detail-grid">
    <div class="page-card gallery-card">
        <div class="main-image">
            @if($gambarUtama)
                <img src="{{ asset('storage/' . $gambarUtama) }}" alt="{{ $produk->nama }}">
            @else
                PRODUK TAHU
            @endif
        </div>

        @if($gambarProduk->count())
            <div class="thumb-row">
                @foreach($gambarProduk->take(4) as $gambar)
                    <div class="thumb">
                        @if($gambar->url_gambar)
                            <img src="{{ asset('storage/' . $gambar->url_gambar) }}" alt="{{ $produk->nama }}">
                        @else
                            FOTO
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="page-card detail-card">
        <div class="product-status">
            @if($stokTersedia)
                <span class="status-pill status-ready">Tersedia</span>
            @else
                <span class="status-pill status-empty">Stok habis</span>
            @endif

            <span class="status-pill">Stok {{ $produk->stok }}</span>

            @if($rataRating)
                <span class="status-pill">⭐ {{ $rataRating }} dari {{ $ulasan->count() }} ulasan</span>
            @else
                <span class="status-pill">Produk pilihan SiTahu</span>
            @endif
        </div>

        <h1 class="detail-title">{{ $produk->nama }}</h1>

        <p class="detail-desc">{{ $deskripsi }}</p>

        <div class="detail-price">
            Rp {{ number_format($produk->harga, 0, ',', '.') }}
        </div>

        <div class="detail-meta-grid">
            <div class="meta-box">
                <strong>Satuan</strong>
                <span>{{ $produk->satuan ?: 'Satuan produk' }}</span>
            </div>

            <div class="meta-box">
                <strong>Isi</strong>
                <span>
                    @if($produk->isi_per_satuan)
                        {{ $produk->isi_per_satuan }} isi per satuan
                    @else
                        Informasi isi menyusul
                    @endif
                </span>
            </div>

            <div class="meta-box">
                <strong>Berat</strong>
                <span>
                    @if($produk->berat)
                        {{ number_format($produk->berat, 0, ',', '.') }} gram
                    @else
                        Berat produk menyusul
                    @endif
                </span>
            </div>
        </div>

        <div class="detail-actions">
            @if($stokTersedia)
                <form action="{{ route('pembeli-web.keranjang.store', $produk) }}" method="POST">
                    @csrf
                    <input type="hidden" name="jumlah" value="1">
                    <button type="submit" class="btn btn-primary">
                        Tambah ke Keranjang
                    </button>
                </form>
            @else
                <button type="button" class="btn btn-outline" disabled>
                    Stok Sedang Habis
                </button>
            @endif

            <a href="{{ $linkWa }}" target="{{ $nomorWa ? '_blank' : '_self' }}" class="btn btn-whatsapp">
                Tanya via WhatsApp
            </a>

            <a href="{{ route('pembeli-web.produk') }}" class="btn btn-outline">
                Lihat Produk Lain
            </a>
        </div>
    </div>
</section>

<section class="section-block">
    <div class="page-card section-card">
        <h2>Info penyimpanan dan penyajian</h2>
        <p>
            Biar tahu tetap enak saat dinikmati, perhatikan masa simpan dan cara penyajiannya ya.
        </p>

        <div class="tips-grid">
            <div class="tip-card">
                <div class="icon">⏳</div>
                <strong>Masa simpan</strong>
                <span>{{ $masaSimpan }}</span>
            </div>

            <div class="tip-card">
                <div class="icon">❄️</div>
                <strong>Saran penyimpanan</strong>
                <span>{{ $saranPenyimpanan }}</span>
            </div>

            <div class="tip-card">
                <div class="icon">🍳</div>
                <strong>Saran penyajian</strong>
                <span>{{ $saranPenyajian }}</span>
            </div>
        </div>
    </div>
</section>

<section class="section-block">
    <div class="page-card section-card">
        <div class="review-head">
            <div>
                <h2>Ulasan pelanggan</h2>
                <p>Pengalaman pembeli setelah menikmati produk ini.</p>
            </div>

            @if($rataRating)
                <div class="rating-box">⭐ {{ $rataRating }} / 5</div>
            @endif
        </div>

        @if($ulasan->count())
            <div class="review-grid">
                @foreach($ulasan as $item)
                    <article class="review-card">
                        <div class="review-top">
                            <strong>{{ $item->user?->name ?? $item->user?->nama ?? 'Pelanggan SiTahu' }}</strong>

                            <span class="stars">
                                @for($i = 1; $i <= 5; $i++)
                                    {{ $i <= (int) $item->rating ? '★' : '☆' }}
                                @endfor
                            </span>
                        </div>

                        <p>
                            {{ $item->komentar ?: 'Pembeli belum menulis komentar.' }}
                        </p>

                        @if($item->foto_ulasan)
                            <div class="review-photo">
                                <img src="{{ asset('storage/' . $item->foto_ulasan) }}" alt="Foto ulasan">
                            </div>
                        @endif

                        @if($item->video_ulasan)
                            <div class="review-video">
                                <video controls>
                                    <source src="{{ asset('storage/' . $item->video_ulasan) }}">
                                    Browser kamu belum mendukung video.
                                </video>
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>
        @else
            <div class="empty-review">
                Belum ada ulasan untuk produk ini. Nanti setelah pembeli memberi penilaian,
                ulasannya akan tampil di sini.
            </div>
        @endif
    </div>
</section>

@if($produkLain->count())
    <section class="section-block">
        <div class="page-card section-card">
            <h2>Produk lain yang bisa kamu lihat</h2>
            <p>Coba cek pilihan tahu lainnya yang mungkin cocok buat kamu.</p>

            <div class="product-grid">
                @foreach($produkLain as $item)
                    <a href="{{ route('pembeli-web.produk.detail', $item) }}" class="page-card product-card">
                        <div class="product-img">
                            @if($item->gambarUtama && $item->gambarUtama->url_gambar)
                                <img src="{{ asset('storage/' . $item->gambarUtama->url_gambar) }}" alt="{{ $item->nama }}">
                            @else
                                PRODUK TAHU
                            @endif
                        </div>

                        <div class="product-body">
                            <h3 class="product-name">{{ $item->nama }}</h3>
                            <div class="product-price">
                                Rp {{ number_format($item->harga, 0, ',', '.') }}
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif
@endsection
