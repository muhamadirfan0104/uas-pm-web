@extends('layouts.pembeli')

@section('title', 'Beri Ulasan - SiTahu')

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

    .review-form-layout {
        display: grid;
        grid-template-columns: 0.9fr 1.1fr;
        gap: 22px;
        align-items: start;
    }

    .product-preview {
        padding: 22px;
        position: sticky;
        top: 96px;
    }

    .product-img {
        width: 100%;
        height: 310px;
        border-radius: 22px;
        overflow: hidden;
        display: grid;
        place-items: center;
        background:
            radial-gradient(circle at top right, rgba(223, 186, 104, 0.22), transparent 35%),
            #f9fafb;
        border: 1px solid var(--line);
        color: var(--brand-text);
        font-weight: 950;
        letter-spacing: 0.08em;
    }

    .product-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-preview h1 {
        margin: 18px 0 6px;
        color: var(--heading);
        font-size: 30px;
        letter-spacing: -0.06em;
        line-height: 1.05;
    }

    .product-preview p {
        margin: 0;
        color: var(--muted);
        line-height: 1.65;
        font-size: 14px;
    }

    .invoice-box {
        margin-top: 16px;
        padding: 14px;
        border-radius: 16px;
        background: var(--brand-soft);
        border: 1px solid rgba(223, 186, 104, 0.42);
    }

    .invoice-box span {
        display: block;
        color: var(--muted);
        font-size: 12px;
        font-weight: 750;
        margin-bottom: 4px;
    }

    .invoice-box strong {
        display: block;
        color: var(--brand-text);
        font-size: 14px;
    }

    .form-card {
        padding: 28px;
    }

    .form-card h2 {
        margin: 0;
        color: var(--heading);
        font-size: 32px;
        line-height: 1;
        letter-spacing: -0.065em;
    }

    .form-card > p {
        margin: 10px 0 22px;
        color: var(--muted);
        line-height: 1.7;
        font-size: 14px;
    }

    .alert-box {
        margin-bottom: 16px;
        padding: 14px 16px;
        border-radius: 16px;
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
        font-size: 13px;
        font-weight: 800;
        line-height: 1.55;
    }

    .form-grid {
        display: grid;
        gap: 16px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: var(--heading);
        font-size: 13px;
        font-weight: 900;
    }

    .form-control {
        width: 100%;
        min-height: 48px;
        padding: 12px 14px;
        border-radius: 15px;
        border: 1px solid var(--line);
        background: #ffffff;
        color: var(--heading);
        outline: none;
        font-size: 14px;
        transition: 0.16s ease;
    }

    textarea.form-control {
        min-height: 130px;
        resize: vertical;
        line-height: 1.65;
    }

    .form-control:focus {
        border-color: rgba(223, 186, 104, 0.95);
        box-shadow: 0 0 0 4px rgba(223, 186, 104, 0.16);
    }

    .rating-options {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 10px;
    }

    .rating-options input {
        display: none;
    }

    .rating-options label {
        min-height: 58px;
        margin: 0;
        border-radius: 16px;
        border: 1px solid var(--line);
        background: #ffffff;
        display: grid;
        place-items: center;
        color: #d99a20;
        font-size: 22px;
        cursor: pointer;
        transition: 0.16s ease;
    }

    .rating-options input:checked + label {
        background: var(--brand-soft);
        border-color: rgba(223, 186, 104, 0.7);
        box-shadow: 0 10px 22px rgba(223, 186, 104, 0.18);
        transform: translateY(-1px);
    }

    .file-note {
        margin-top: 7px;
        color: var(--muted);
        font-size: 12px;
        line-height: 1.55;
    }

    .current-media {
        margin-top: 10px;
        display: grid;
        gap: 10px;
    }

    .current-media img,
    .current-media video {
        width: 100%;
        max-height: 240px;
        border-radius: 16px;
        border: 1px solid var(--line);
        background: #f9fafb;
        object-fit: cover;
    }

    .form-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 6px;
    }

    @media (max-width: 900px) {
        .review-form-layout {
            grid-template-columns: 1fr;
        }

        .product-preview {
            position: static;
        }
    }

    @media (max-width: 640px) {
        .product-preview,
        .form-card {
            padding: 20px;
        }

        .product-img {
            height: 240px;
        }

        .rating-options {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .form-actions {
            flex-direction: column;
        }

        .form-actions .btn {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
@php
    $gambar = $produk->gambarUtama?->url_gambar;
    $ratingLama = old('rating', $ulasan?->rating);
@endphp

<div class="breadcrumb">
    <a href="{{ route('pembeli-web.home') }}">Beranda</a>
    <span>/</span>
    <a href="{{ route('pembeli-web.pesanan.index') }}">Pesanan Saya</a>
    <span>/</span>
    <a href="{{ route('pembeli-web.pesanan.show', $pesanan->nomor_invoice) }}">{{ $pesanan->nomor_invoice }}</a>
    <span>/</span>
    <span>Beri Ulasan</span>
</div>

<section class="review-form-layout">
    <aside class="page-card product-preview">
        <div class="product-img">
            @if($gambar)
                <img src="{{ asset('storage/' . $gambar) }}" alt="{{ $produk->nama }}">
            @else
                PRODUK TAHU
            @endif
        </div>

        <h1>{{ $produk->nama }}</h1>

        <p>
            Ulasan kamu akan membantu pembeli lain untuk melihat kualitas produk sebelum membeli.
            Berikan rating sesuai pengalamanmu ya.
        </p>

        <div class="invoice-box">
            <span>Nomor invoice</span>
            <strong>{{ $pesanan->nomor_invoice }}</strong>
        </div>
    </aside>

    <main class="page-card form-card">
        <h2>{{ $ulasan ? 'Edit Ulasan' : 'Beri Ulasan' }}</h2>

        <p>
            Isi rating, komentar, foto, dan video ulasan produk. Foto dan video bersifat opsional,
            tapi bisa bikin ulasanmu lebih jelas untuk pembeli lain.
        </p>

        @if($errors->any())
            <div class="alert-box">
                {{ $errors->first() }}
            </div>
        @endif

        <form
            action="{{ route('pembeli-web.ulasan.store', [$pesanan->nomor_invoice, $produk]) }}"
            method="POST"
            enctype="multipart/form-data"
            class="form-grid"
        >
            @csrf

            <div class="form-group">
                <label>Rating Produk</label>

                <div class="rating-options">
                    @for($i = 1; $i <= 5; $i++)
                        <input
                            type="radio"
                            name="rating"
                            id="rating_{{ $i }}"
                            value="{{ $i }}"
                            {{ (int) $ratingLama === $i ? 'checked' : '' }}
                        >
                        <label for="rating_{{ $i }}" title="{{ $i }} bintang">
                            {{ str_repeat('★', $i) }}
                        </label>
                    @endfor
                </div>
            </div>

            <div class="form-group">
                <label for="komentar">Komentar Ulasan</label>
                <textarea
                    name="komentar"
                    id="komentar"
                    class="form-control"
                    placeholder="Contoh: Tahunya fresh, rasanya enak, dan kemasannya rapi."
                >{{ old('komentar', $ulasan?->komentar) }}</textarea>
            </div>

            <div class="form-group">
                <label for="foto_ulasan">Foto Ulasan</label>
                <input
                    type="file"
                    name="foto_ulasan"
                    id="foto_ulasan"
                    class="form-control"
                    accept="image/*"
                >
                <div class="file-note">Format: jpg, jpeg, png, webp. Maksimal 4 MB.</div>

                @if($ulasan?->foto_ulasan)
                    <div class="current-media">
                        <img src="{{ asset('storage/' . $ulasan->foto_ulasan) }}" alt="Foto ulasan saat ini">
                    </div>
                @endif
            </div>

            <div class="form-group">
                <label for="video_ulasan">Video Ulasan</label>
                <input
                    type="file"
                    name="video_ulasan"
                    id="video_ulasan"
                    class="form-control"
                    accept="video/mp4,video/quicktime,video/webm,video/x-msvideo"
                >
                <div class="file-note">
                    Format: mp4, mov, avi, webm. Maksimal 50 MB.
                    Video bisa berisi kondisi produk, kemasan, atau review singkat.
                </div>

                @if($ulasan?->video_ulasan)
                    <div class="current-media">
                        <video controls>
                            <source src="{{ asset('storage/' . $ulasan->video_ulasan) }}">
                            Browser kamu belum mendukung video.
                        </video>
                    </div>
                @endif
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    Simpan Ulasan
                </button>

                <a href="{{ route('pembeli-web.pesanan.show', $pesanan->nomor_invoice) }}" class="btn btn-outline">
                    Kembali ke Pesanan
                </a>
            </div>
        </form>
    </main>
</section>
@endsection