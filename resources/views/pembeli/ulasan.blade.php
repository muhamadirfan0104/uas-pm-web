@extends('layouts.pembeli')

@section('title', 'Semua Ulasan Pembeli - ' . ($pengaturan->nama ?: 'SiTahu'))

@push('styles')
<style>
    .reviews-hero {
        border-radius: 32px;
        border: 1px solid rgba(200,147,53,.18);
        background:
            radial-gradient(circle at 90% 10%, rgba(200,147,53,.18), transparent 22rem),
            linear-gradient(135deg, #fff8ea 0%, #fff 50%, #f8fafc 100%);
        box-shadow: var(--shadow-sm);
    }
    .review-stat-card {
        border-radius: 22px;
        border: 1px solid var(--line);
        background: #fff;
        padding: 18px;
        height: 100%;
        box-shadow: var(--shadow-xs);
    }
    .review-line { border: 1px solid var(--line); border-radius: 22px; padding: 18px; background: #fff; transition: .18s ease; box-shadow: var(--shadow-xs); height: 100%; }
    .review-line:hover { border-color: rgba(200,147,53,.28); box-shadow: var(--shadow-sm); transform: translateY(-1px); }
    .review-avatar { width: 42px; height: 42px; border-radius: 14px; display: grid; place-items: center; color: var(--brand-dark); background: var(--brand-soft); border: 1px solid rgba(200,147,53,.18); flex: 0 0 auto; }
    .review-comment { color: var(--ink); font-weight: 650; line-height: 1.65; font-size: 14px; }
    .review-filter { border: 1px solid var(--line); background: #fff; border-radius: 999px; padding: 9px 14px; color: var(--muted); font-size: 13px; font-weight: 850; transition: .18s ease; text-decoration:none; display:inline-flex; align-items:center; gap:7px; }
    .review-filter:hover, .review-filter.active { color: var(--brand-dark); background: var(--brand-soft); border-color: rgba(200,147,53,.35); }
    .review-media-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(118px, 1fr)); gap: 10px; margin-top: 14px; }
    .review-media-item { position: relative; display: block; aspect-ratio: 1 / 1; border-radius: 16px; overflow: hidden; background: #f3f4f6; border: 1px solid var(--line); }
    .review-media-item img, .review-media-item video { width: 100%; height: 100%; object-fit: cover; }
    .review-media-badge { position: absolute; left: 8px; bottom: 8px; display: inline-flex; align-items: center; gap: 5px; border-radius: 999px; padding: 5px 8px; background: rgba(17,24,39,.72); color: #fff; font-size: 11px; font-weight: 850; backdrop-filter: blur(8px); }
</style>
@endpush

@section('content')
<div class="container py-4 py-lg-5">
    <div class="breadcrumb-modern">
        <a href="{{ route('pembeli-web.home') }}">Beranda</a><i class="bi bi-chevron-right small"></i>
        <span>Ulasan Pembeli</span>
    </div>

    <section class="reviews-hero p-4 p-lg-5 mb-4">
        <div class="row g-4 align-items-center">
            <div class="col-lg-7">
                <span class="eyebrow mb-3"><i class="bi bi-chat-heart-fill"></i> Ulasan pembeli</span>
                <h1 class="section-heading display-5 mb-3">Semua penilaian dari pembeli.</h1>
                <p class="section-subtitle fs-5 mb-0">Lihat pengalaman pembeli setelah menerima pesanan, termasuk komentar, foto, dan video ulasan produk.</p>
            </div>
            <div class="col-lg-5">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="review-stat-card">
                            <div class="small text-muted fw-bold text-uppercase mb-1">Total Ulasan</div>
                            <div class="h2 fw-black mb-0">{{ $statUlasan['total'] ?? 0 }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="review-stat-card">
                            <div class="small text-muted fw-bold text-uppercase mb-1">Rating</div>
                            <div class="h2 fw-black mb-0">{{ ($statUlasan['rata_rating'] ?? 0) ?: '-' }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="review-stat-card">
                            <div class="small text-muted fw-bold text-uppercase mb-1">Foto</div>
                            <div class="h2 fw-black mb-0">{{ $statUlasan['foto'] ?? 0 }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="review-stat-card">
                            <div class="small text-muted fw-bold text-uppercase mb-1">Video</div>
                            <div class="h2 fw-black mb-0">{{ $statUlasan['video'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="surface p-3 p-lg-4 mb-4">
        <form action="{{ route('pembeli-web.ulasan') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-lg-5">
                <label class="form-label fw-bold">Cari ulasan</label>
                <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Cari nama produk, pembeli, atau isi komentar...">
            </div>
            <div class="col-sm-6 col-lg-3">
                <label class="form-label fw-bold">Media</label>
                <select name="media" class="form-select">
                    <option value="semua" @selected($filterMedia === 'semua')>Semua ulasan</option>
                    <option value="foto" @selected($filterMedia === 'foto')>Dengan foto</option>
                    <option value="video" @selected($filterMedia === 'video')>Dengan video</option>
                </select>
            </div>
            <div class="col-sm-6 col-lg-2">
                <label class="form-label fw-bold">Rating</label>
                <select name="rating" class="form-select">
                    <option value="semua" @selected($filterRating === 'semua')>Semua</option>
                    @for($i=5; $i>=1; $i--)
                        <option value="{{ $i }}" @selected($filterRating === (string) $i)>{{ $i }} bintang</option>
                    @endfor
                </select>
            </div>
            <div class="col-lg-2 d-grid">
                <button class="btn btn-brand" type="submit"><i class="bi bi-search me-1"></i> Terapkan</button>
            </div>
        </form>

        <div class="d-flex flex-wrap gap-2 mt-3">
            <a href="{{ route('pembeli-web.ulasan') }}" class="review-filter {{ $filterMedia === 'semua' && $filterRating === 'semua' && $search === '' ? 'active' : '' }}">Semua</a>
            <a href="{{ route('pembeli-web.ulasan', ['media' => 'foto']) }}" class="review-filter {{ $filterMedia === 'foto' ? 'active' : '' }}"><i class="bi bi-images"></i> Foto</a>
            <a href="{{ route('pembeli-web.ulasan', ['media' => 'video']) }}" class="review-filter {{ $filterMedia === 'video' ? 'active' : '' }}"><i class="bi bi-play-btn"></i> Video</a>
            <a href="{{ route('pembeli-web.ulasan', ['rating' => '5']) }}" class="review-filter {{ $filterRating === '5' ? 'active' : '' }}"><i class="bi bi-star-fill text-warning"></i> Bintang 5</a>
        </div>
    </section>

    @if($ulasanList->count())
        <div class="row g-3 g-lg-4">
            @foreach($ulasanList as $item)
                <div class="col-lg-4 col-md-6">
                    @include('pembeli.partials.review-line', ['item' => $item, 'showProductInfo' => true])
                </div>
            @endforeach
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $ulasanList->links() }}
        </div>
    @else
        <div class="surface p-5 text-center">
            <i class="bi bi-chat-square-text fs-1 text-brand"></i>
            <h2 class="h4 fw-bold mt-3">Belum ada ulasan yang sesuai.</h2>
            <p class="text-muted mb-4">Data tidak ditemukan.</p>
            <a href="{{ route('pembeli-web.ulasan') }}" class="btn btn-soft-brand px-4">Lihat Semua Ulasan</a>
        </div>
    @endif
</div>
@endsection
