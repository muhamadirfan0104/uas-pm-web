@extends('layouts.pembeli')

@section('title', ($pengaturan->nama ?: 'SiTahu') . ' - Tahu Segar Siap Pesan')

@push('styles')
<style>
    .home-banner-wrap {
        position: relative;
    }
    .home-banner-carousel,
    .home-banner-carousel .carousel-inner,
    .home-banner-slide {
        border-radius: 34px;
    }
    .home-banner-carousel .carousel-inner {
        overflow: hidden;
        border: 1px solid rgba(200,147,53,.18);
        box-shadow: var(--shadow-sm);
        background: #fff;
    }
    .home-banner-slide {
        min-height: 460px;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        padding: clamp(28px, 5vw, 70px);
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }
    .home-banner-slide::after {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at 80% 10%, rgba(255,255,255,.24), transparent 20rem),
            linear-gradient(90deg, rgba(16,24,40,.72) 0%, rgba(16,24,40,.46) 44%, rgba(16,24,40,.08) 100%);
        pointer-events: none;
    }
    .home-banner-content {
        position: relative;
        z-index: 2;
        max-width: 650px;
        color: #fff;
    }
    .home-banner-content .eyebrow {
        background: rgba(255,255,255,.16);
        border-color: rgba(255,255,255,.28);
        color: #fff;
        backdrop-filter: blur(10px);
    }
    .home-banner-title {
        font-size: clamp(2.25rem, 5vw, 4.5rem);
        line-height: .98;
        letter-spacing: -.07em;
        font-weight: 900;
        margin-bottom: 18px;
        text-shadow: 0 18px 38px rgba(0,0,0,.22);
    }
    .home-banner-desc {
        max-width: 560px;
        color: rgba(255,255,255,.88);
        font-size: clamp(1rem, 1.5vw, 1.18rem);
        line-height: 1.8;
        font-weight: 600;
        margin-bottom: 28px;
    }
    .home-banner-actions .btn {
        min-height: 48px;
        padding-left: 22px;
        padding-right: 22px;
    }
    .home-banner-fallback {
        min-height: 420px;
        border-radius: 34px;
        border: 1px solid rgba(200,147,53,.18);
        background:
            radial-gradient(circle at 85% 10%, rgba(200,147,53,.18), transparent 24rem),
            linear-gradient(135deg, #fff8ea 0%, #ffffff 48%, #f6efe2 100%);
        box-shadow: var(--shadow-sm);
        display: grid;
        place-items: center;
        text-align: center;
        padding: 48px 24px;
    }
    .home-banner-fallback-icon {
        width: 84px;
        height: 84px;
        margin: 0 auto 18px;
        border-radius: 28px;
        display: grid;
        place-items: center;
        color: var(--brand-dark);
        background: #fff;
        box-shadow: var(--shadow-sm);
        font-size: 38px;
    }
    .home-banner-carousel .carousel-indicators {
        right: auto;
        left: clamp(28px, 5vw, 70px);
        bottom: 22px;
        margin: 0;
        justify-content: flex-start;
        gap: 8px;
    }
    .home-banner-carousel .carousel-indicators [data-bs-target] {
        width: 10px;
        height: 10px;
        border-radius: 999px;
        border: 0;
        background: rgba(255,255,255,.72);
        opacity: 1;
        transition: .2s ease;
    }
    .home-banner-carousel .carousel-indicators .active {
        width: 34px;
        background: var(--brand-color);
    }
    .home-banner-control {
        width: 46px;
        height: 46px;
        top: 50%;
        transform: translateY(-50%);
        opacity: 1;
        background: rgba(255,255,255,.88);
        border: 1px solid rgba(255,255,255,.72);
        border-radius: 999px;
        box-shadow: var(--shadow-sm);
        backdrop-filter: blur(10px);
    }
    .home-banner-control:hover {
        background: #fff;
    }
    .home-banner-control.carousel-control-prev {
        left: 18px;
    }
    .home-banner-control.carousel-control-next {
        right: 18px;
    }
    .home-banner-control .carousel-control-prev-icon,
    .home-banner-control .carousel-control-next-icon {
        filter: invert(1) grayscale(1);
        width: 18px;
        height: 18px;
    }
    .home-banner-carousel .carousel-inner {
        cursor: grab;
        user-select: none;
    }
    .home-banner-carousel.is-dragging .carousel-inner {
        cursor: grabbing;
    }
    .simple-panel {
        border-radius: 30px;
        border: 1px solid var(--line);
        background: #fff;
        box-shadow: var(--shadow-xs);
    }
    .home-section-head {
        margin-bottom: 24px;
    }
    .home-section-head .section-subtitle {
        max-width: 720px;
    }
    .review-card {
        height: 100%;
        padding: 24px;
        border-radius: 26px;
        background: #fff;
        border: 1px solid var(--line);
        box-shadow: var(--shadow-xs);
    }
    .review-avatar {
        width: 46px;
        height: 46px;
        display: grid;
        place-items: center;
        border-radius: 16px;
        background: var(--brand-soft);
        color: var(--brand-dark);
        font-size: 20px;
        flex: 0 0 auto;
    }
    @media (max-width: 991.98px) {
        .home-banner-slide { min-height: 390px; }
        .home-banner-slide::after {
            background: linear-gradient(90deg, rgba(16,24,40,.78) 0%, rgba(16,24,40,.50) 100%);
        }
        .home-banner-control { display: none; }
    }
    @media (max-width: 575.98px) {
        .home-banner-carousel,
        .home-banner-carousel .carousel-inner,
        .home-banner-slide,
        .home-banner-fallback { border-radius: 26px; }
        .home-banner-slide { min-height: 360px; padding: 28px; align-items: flex-end; padding-bottom: 58px; }
        .home-banner-title { font-size: 2.15rem; }
        .home-banner-actions { flex-direction: column; align-items: stretch; }
        .home-banner-actions .btn { width: 100%; }
        .home-banner-carousel .carousel-indicators { left: 28px; bottom: 22px; }
    }
</style>
@endpush

@section('content')
@php
    $namaToko = $pengaturan->nama ?: 'SiTahu';
    $tentangToko = $pengaturan->tentang ?: 'Produk tahu segar berkualitas.';
    $teleponToko = $pengaturan->telepon ?: '';
    $nomorWa = preg_replace('/[^0-9]/', '', $teleponToko);
    if ($nomorWa && str_starts_with($nomorWa, '0')) { $nomorWa = '62' . substr($nomorWa, 1); }
    $linkWa = $nomorWa ? 'https://wa.me/' . $nomorWa . '?text=' . urlencode('Halo ' . $namaToko . ', saya ingin bertanya tentang pemesanan tahu.') : route('pembeli-web.produk');
@endphp

<div class="container py-4 py-lg-5">
    <section class="home-banner-wrap mb-4 mb-lg-5">
        <div class="home-banner-fallback">
            <div>
                <div class="home-banner-fallback-icon">
                    <i class="bi bi-shop-window"></i>
                </div>

                <span class="eyebrow mb-3">
                    <i class="bi bi-patch-check-fill"></i> {{ $namaToko }}
                </span>

                <h1 class="section-heading display-5 mb-3">
                    Tahu segar pilihan untuk kebutuhan harianmu.
                </h1>

                <p class="section-subtitle fs-5 mb-4 mx-auto" style="max-width: 620px;">
                    {{ $tentangToko }}
                </p>

                <a href="{{ route('pembeli-web.produk', ['stok' => 'tersedia']) }}" class="btn btn-brand btn-lg px-4">
                    <i class="bi bi-shop-window me-2"></i> Belanja Sekarang
                </a>
            </div>
        </div>
    </section>

    <section class="mb-5">
        <div class="home-section-head d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3">
            <div>
                <span class="eyebrow mb-2"><i class="bi bi-clock-history"></i> Terbaru</span>
                <h2 class="section-heading h1 mb-1">Baru tersedia di katalog.</h2>
                <p class="section-subtitle mb-0">Produk terbaru.</p>
            </div>
            <a href="{{ route('pembeli-web.produk', ['sort' => 'terbaru']) }}" class="btn btn-soft-brand px-4">Buka Semua Produk</a>
        </div>
        @if($produkTerbaru->count())
            <div class="row g-3 g-lg-4">
                @foreach($produkTerbaru->take(8) as $produk)
                    <div class="col-sm-6 col-lg-3">
                        @include('pembeli.partials.product-card', ['produk' => $produk, 'compact' => true])
                    </div>
                @endforeach
            </div>
        @else
            <div class="surface p-4 text-center text-muted fw-semibold">Belum ada produk terbaru yang aktif.</div>
        @endif
    </section>

    <section class="mb-5">
        <div class="home-section-head d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3">
            <div>
                <span class="eyebrow mb-2"><i class="bi bi-chat-heart-fill"></i> Ulasan pembeli</span>
                <h2 class="section-heading h1 mb-1">Kata pembeli tentang produk kami.</h2>
                <p class="section-subtitle mb-0">Ulasan dari pelanggan yang sudah menyelesaikan pesanan.</p>
            </div>
            <a href="{{ route('pembeli-web.ulasan') }}" class="btn btn-soft-brand px-4">
                Lihat Semua Ulasan <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
        @if($ulasanBeranda->count())
            <div class="row g-3 g-lg-4">
                @foreach($ulasanBeranda as $ulasan)
                    <div class="col-md-4">
                        <div class="review-card">
                            <div class="rating-stars mb-3">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi {{ $i <= (int) $ulasan->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                @endfor
                            </div>
                            <p class="line-clamp-3 text-muted mb-4">“{{ $ulasan->komentar ?: 'Produk sesuai harapan, segar, dan pelayanan toko baik.' }}”</p>
                            <div class="d-flex align-items-center gap-3">
                                <div class="review-avatar"><i class="bi bi-person-fill"></i></div>
                                <div>
                                    <div class="fw-bold">{{ $ulasan->user?->name ?: 'Pembeli' }}</div>
                                    <div class="small text-muted fw-semibold">{{ $ulasan->produk?->nama ?: 'Produk SiTahu' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="surface p-4 text-center">
                <i class="bi bi-chat-square-text fs-1 text-brand"></i>
                <h3 class="h5 fw-bold mt-2">Belum ada ulasan yang ditampilkan.</h3>
                <p class="text-muted mb-0">Ulasan akan muncul setelah pembeli memberikan penilaian pada pesanan selesai.</p>
            </div>
        @endif
    </section>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const banner = document.getElementById('homeBannerCarousel');
    if (!banner || typeof bootstrap === 'undefined') return;

    const carousel = bootstrap.Carousel.getOrCreateInstance(banner, {
        interval: 4500,
        ride: 'carousel',
        touch: true,
        wrap: true,
        pause: 'hover'
    });

    carousel.cycle();

    const track = banner.querySelector('.carousel-inner');
    if (!track) return;

    let startX = 0;
    let currentX = 0;
    let dragging = false;
    const minSwipe = 45;

    track.addEventListener('pointerdown', function (event) {
        if (event.pointerType === 'mouse' && event.button !== 0) return;
        if (event.target.closest('a, button')) return;
        startX = event.clientX;
        currentX = event.clientX;
        dragging = true;
        banner.classList.add('is-dragging');
        carousel.pause();
        try { track.setPointerCapture(event.pointerId); } catch (error) {}
    });

    track.addEventListener('pointermove', function (event) {
        if (!dragging) return;
        currentX = event.clientX;
    });

    const finishDrag = function (event) {
        if (!dragging) return;
        const diff = currentX - startX;
        dragging = false;
        banner.classList.remove('is-dragging');
        try { track.releasePointerCapture(event.pointerId); } catch (error) {}

        if (Math.abs(diff) >= minSwipe) {
            diff < 0 ? carousel.next() : carousel.prev();
        }
        carousel.cycle();
    };

    track.addEventListener('pointerup', finishDrag);
    track.addEventListener('pointercancel', finishDrag);
    track.addEventListener('pointerleave', function (event) {
        if (dragging && event.pointerType === 'mouse') finishDrag(event);
    });
});
</script>
@endpush

