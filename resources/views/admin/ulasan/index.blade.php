@extends('layouts.admin')

@section('title', 'Ulasan - SiTahu')
@section('page_title', 'Ulasan')

@section('content')
@php
    $ratingOptions = ['semua' => 'Semua rating', '5' => '5 bintang', '4' => '4 bintang', '3' => '3 bintang', '2' => '2 bintang', '1' => '1 bintang'];
    $statusOptions = ['semua' => 'Semua status', 'tampil' => 'Ditampilkan', 'sembunyi' => 'Disembunyikan'];
    $mediaOptions = ['semua' => 'Semua media', 'foto' => 'Ada foto', 'video' => 'Ada video', 'tanpa_media' => 'Tanpa media'];
    $sortOptions = ['terbaru' => 'Terbaru', 'rating_tinggi' => 'Rating tertinggi', 'rating_rendah' => 'Rating terendah'];
@endphp

<style>
    .review-stats {
        display: grid;
        grid-template-columns: 1.2fr repeat(4, minmax(0, .8fr));
        gap: 9px;
        margin-bottom: 16px;
    }
    .review-stat-card {
        border: 1px solid var(--border);
        border-radius: 18px;
        background: #fff;
        padding: 14px 15px;
        box-shadow: var(--shadow-soft);
        min-height: 86px;
    }
    .review-stat-card .label {
        color: var(--muted);
        font-size: .7rem;
        font-weight: 950;
        text-transform: uppercase;
        letter-spacing: .055em;
    }
    .review-stat-card .value {
        margin-top: 7px;
        color: var(--text);
        font-size: 1.35rem;
        font-weight: 950;
        letter-spacing: -.05em;
        line-height: 1;
    }
    .review-filter-grid {
        display: grid;
        grid-template-columns: minmax(230px, 1fr) 220px 135px 150px 150px 150px auto auto;
        gap: 10px;
        align-items: end;
    }
    .review-filter-label {
        display: block;
        margin-bottom: 6px;
        color: var(--muted);
        font-size: .7rem;
        font-weight: 950;
        text-transform: uppercase;
        letter-spacing: .055em;
    }
    .review-search {
        min-height: 42px;
        padding: 0 13px;
        border: 1px solid var(--border);
        border-radius: 14px;
        background: #fff;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .review-search input {
        width: 100%;
        border: 0;
        outline: 0;
        background: transparent;
        font-size: .78rem;
        font-weight: 750;
    }
    .review-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
    }
    .review-card {
        border: 1px solid var(--border);
        border-radius: 13px;
        background: #fff;
        overflow: hidden;
        box-shadow: var(--shadow-soft);
        display: flex;
        flex-direction: column;
        min-height: 100%;
    }
    .review-card-top {
        padding: 11px 12px;
        display: grid;
        grid-template-columns: 42px minmax(0, 1fr) auto;
        gap: 9px;
        align-items: start;
        border-bottom: 1px solid #f1f2f4;
    }
    .review-product-img {
        width: 42px;
        height: 42px;
        border-radius: 13px;
        object-fit: cover;
        background: #f3f4f6;
        border: 1px solid var(--border);
    }
    .review-product-empty {
        width: 42px;
        height: 42px;
        border-radius: 13px;
        display: grid;
        place-items: center;
        background: var(--brand-soft);
        color: var(--brand-dark);
        border: 1px solid #f1d49c;
    }
    .review-title {
        color: var(--text);
        font-size: .84rem;
        font-weight: 950;
        letter-spacing: -.03em;
        line-height: 1.25;
    }
    .review-sub {
        margin-top: 5px;
        color: var(--muted);
        font-size: .68rem;
        font-weight: 700;
        line-height: 1.45;
    }
    .review-rating {
        display: inline-flex;
        align-items: center;
        gap: 2px;
        color: #f59e0b;
        font-size: .78rem;
        white-space: nowrap;
    }
    .review-card-body { padding: 11px 12px; flex: 1; }
    .review-comment {
        color: #344054;
        font-size: .78rem;
        font-weight: 650;
        line-height: 1.5;
        min-height: 36px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;
    }
    .review-media-row {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 12px;
    }
    .review-media-thumb {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        border: 1px solid var(--border);
        object-fit: cover;
        background: #f3f4f6;
        cursor: pointer;
        transition: .16s ease;
    }
    .review-media-thumb:hover { transform: translateY(-1px) scale(1.03); box-shadow: var(--shadow-soft); }
    .review-video-box {
        width: 64px;
        height: 44px;
        border-radius: 12px;
        border: 1px solid var(--border);
        background: #111827;
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .review-video-box video { width: 100%; height: 100%; object-fit: cover; opacity: .72; }
    .review-video-box i { position: absolute; font-size: 1.35rem; }
    .review-card-footer {
        padding: 10px 12px;
        border-top: 1px solid #f1f2f4;
        background: #fbfcfd;
        display: flex;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
    }
    .review-empty {
        padding: 46px 16px;
        text-align: center;
        color: var(--muted);
        font-size: .78rem;
        font-weight: 750;
    }
    .rating-bar {
        display: grid;
        grid-template-columns: 48px 1fr 34px;
        gap: 8px;
        align-items: center;
        font-size: .75rem;
        font-weight: 850;
        color: var(--muted);
    }
    .rating-track {
        height: 8px;
        border-radius: 999px;
        background: #f1f2f4;
        overflow: hidden;
    }
    .rating-fill { height: 100%; border-radius: 999px; background: var(--brand); }

    .review-rating-strip {
        border: 1px solid var(--border);
        border-radius: 16px;
        background: #fffaf2;
        padding: 12px;
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 10px;
        margin-bottom: 12px;
    }
    .review-rating-mini {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 0;
    }

    .media-preview-img {
        width: 100%;
        max-height: 76vh;
        object-fit: contain;
        border-radius: 18px;
        background: #f9fafb;
    }
    .media-preview-video {
        width: 100%;
        max-height: 76vh;
        border-radius: 18px;
        background: #000;
    }
    @media (max-width: 1250px) {
        .review-filter-grid { grid-template-columns: 1fr 1fr 1fr; }
        .review-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .review-rating-strip { grid-template-columns: 1fr 1fr; }
        .review-stats { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }
    @media (max-width: 900px) {
        .review-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 680px) {
        .review-filter-grid, .review-stats, .review-rating-strip { grid-template-columns: 1fr; }
        .review-card-top { grid-template-columns: 48px minmax(0, 1fr); }
        .review-card-top .review-rating { grid-column: 2; justify-self: start; }
    }
</style>

<section class="hero">
    <div>
        <h1>Ulasan</h1>
        <p>Kelola penilaian pembeli, cek foto/video ulasan, dan sembunyikan komentar yang tidak layak tampil.</p>
    </div>
    <a href="{{ route('pembeli-web.ulasan') }}" target="_blank" rel="noopener" class="btn btn-light border fw-bold px-3">
        <i class="bi bi-box-arrow-up-right me-1 text-muted"></i>
        Lihat di Toko
    </a>
</section>

<div class="review-stats">
    <div class="review-stat-card">
        <div class="label">Rating rata-rata</div>
        <div class="value">{{ $stats['rata_rata'] ?? 0 }} <span class="fs-6 text-warning">/ 5</span></div>
        <div class="mt-2">
            @for($i = 1; $i <= 5; $i++)
                <i class="bi bi-star-fill text-warning small"></i>
            @endfor
        </div>
    </div>
    <div class="review-stat-card"><div class="label">Total ulasan</div><div class="value">{{ $stats['total'] ?? 0 }}</div></div>
    <div class="review-stat-card"><div class="label">Ditampilkan</div><div class="value">{{ $stats['tampil'] ?? 0 }}</div></div>
    <div class="review-stat-card"><div class="label">Foto / Video</div><div class="value">{{ $stats['foto'] ?? 0 }} / {{ $stats['video'] ?? 0 }}</div></div>
    <div class="review-stat-card"><div class="label">Perlu perhatian</div><div class="value">{{ $stats['rating_rendah'] ?? 0 }}</div></div>
</div>

<div class="page-card overflow-hidden mb-4">
    <div class="p-3 p-lg-4 border-bottom bg-white">
        <form id="page-filter" method="GET" class="review-filter-grid">
            <div>
                <label class="review-filter-label">Cari ulasan</label>
                <div class="review-search">
                    <i class="bi bi-search text-muted"></i>
                    <input name="q" value="{{ $search }}" placeholder="Produk, pembeli, invoice, atau komentar">
                </div>
            </div>
            <div>
                <label class="review-filter-label">Produk</label>
                <select name="produk_id" class="form-select">
                    <option value="semua" @selected($produkId === 'semua')>Semua produk</option>
                    @foreach($produkList as $produkFilter)
                        <option value="{{ $produkFilter->id }}" @selected((string) $produkId === (string) $produkFilter->id)>{{ $produkFilter->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="review-filter-label">Rating</label>
                <select name="rating" class="form-select">
                    @foreach($ratingOptions as $value => $label)
                        <option value="{{ $value }}" @selected($rating === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="review-filter-label">Status</label>
                <select name="status" class="form-select">
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="review-filter-label">Media</label>
                <select name="media" class="form-select">
                    @foreach($mediaOptions as $value => $label)
                        <option value="{{ $value }}" @selected($media === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="review-filter-label">Urutkan</label>
                <select name="sort" class="form-select">
                    @foreach($sortOptions as $value => $label)
                        <option value="{{ $value }}" @selected($sort === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="d-grid">
                <label class="review-filter-label opacity-0">Terapkan</label>
                <button class="btn btn-brand" type="submit"><i class="bi bi-funnel me-1"></i> Terapkan</button>
            </div>
            <div class="d-grid">
                <label class="review-filter-label opacity-0">Reset</label>
                <a href="{{ route('admin.ulasan.index') }}" class="btn btn-light border">Reset</a>
            </div>
        </form>
    </div>

    <div class="p-3 p-lg-4">
        @php $totalRating = max(1, array_sum($ratingDistribusi ?? [])); @endphp
        <div class="review-rating-strip">
            @foreach($ratingDistribusi as $bintang => $jumlah)
                <div class="review-rating-mini">
                    <div class="fw-black text-dark small text-nowrap">{{ $bintang }} <i class="bi bi-star-fill text-warning"></i></div>
                    <div class="rating-track flex-grow-1"><div class="rating-fill" style="width: {{ round(($jumlah / $totalRating) * 100) }}%"></div></div>
                    <div class="text-muted small fw-black text-end">{{ $jumlah }}</div>
                </div>
            @endforeach
        </div>

        <div class="review-grid">
            @forelse($ulasan as $item)
                @php
                    $fotoItems = collect();
                    $videoItems = collect();
                    if ($item->foto_ulasan) { $fotoItems->push($item->foto_ulasan); }
                    if ($item->video_ulasan) { $videoItems->push($item->video_ulasan); }
                    foreach ($item->media ?? [] as $mediaItem) {
                        if ($mediaItem->jenis === 'foto') { $fotoItems->push($mediaItem->path); }
                        if ($mediaItem->jenis === 'video') { $videoItems->push($mediaItem->path); }
                    }
                    $produkGambar = $item->produk?->gambarUtama?->path;
                @endphp
                <article class="review-card">
                    <div class="review-card-top">
                        @if($produkGambar)
                            <img src="{{ asset('storage/' . $produkGambar) }}" alt="{{ $item->produk->nama ?? 'Produk' }}" class="review-product-img">
                        @else
                            <div class="review-product-empty"><i class="bi bi-basket2-fill"></i></div>
                        @endif
                        <div class="min-w-0">
                            <div class="review-title text-truncate">{{ $item->produk->nama ?? 'Produk tidak tersedia' }}</div>
                            <div class="review-sub text-truncate">{{ $item->user->name ?? 'Pembeli' }}</div>
                            <div class="review-sub text-truncate">
                                {{ optional($item->created_at)->format('d/m/Y H:i') }}
                                @if($item->pesanan)
                                    · {{ $item->pesanan->nomor_invoice }}
                                @endif
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="review-rating">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi {{ $i <= $item->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                @endfor
                            </div>
                            <div class="mt-1"><span class="chip {{ $item->ditampilkan ? 'c-green' : 'c-gray' }}">{{ $item->ditampilkan ? 'Tampil' : 'Sembunyi' }}</span></div>
                        </div>
                    </div>
                    <div class="review-card-body">
                        <div class="review-comment">{{ $item->komentar ?: 'Pembeli hanya memberikan rating tanpa komentar.' }}</div>
                        @if($fotoItems->isNotEmpty() || $videoItems->isNotEmpty())
                            <div class="review-media-row">
                                @foreach($fotoItems->take(3) as $foto)
                                    <img src="{{ asset('storage/' . $foto) }}" alt="Foto ulasan" class="review-media-thumb js-open-media" data-type="image" data-src="{{ asset('storage/' . $foto) }}">
                                @endforeach
                                @foreach($videoItems->take(1) as $video)
                                    <button type="button" class="review-video-box js-open-media" data-type="video" data-src="{{ asset('storage/' . $video) }}" aria-label="Buka video ulasan">
                                        <video src="{{ asset('storage/' . $video) }}" muted preload="metadata"></video>
                                        <i class="bi bi-play-circle-fill"></i>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="review-card-footer">
                        <div class="actions">
                            <button class="small-btn" type="button" data-bs-toggle="modal" data-bs-target="#reviewDetailModal{{ $item->id }}">
                                <i class="bi bi-eye"></i> Detail
                            </button>
                            <form method="POST" action="{{ route('admin.ulasan.toggle', $item) }}" class="inline-form">
                                @csrf @method('PATCH')
                                <button type="submit" class="small-btn">
                                    <i class="bi {{ $item->ditampilkan ? 'bi-eye-slash' : 'bi-eye' }}"></i>
                                    {{ $item->ditampilkan ? 'Sembunyikan' : 'Tampilkan' }}
                                </button>
                            </form>
                        </div>
                        <form method="POST" action="{{ route('admin.ulasan.destroy', $item) }}" class="inline-form" data-confirm-title="Hapus Ulasan" data-confirm-message="Ulasan yang dihapus tidak bisa dikembalikan." data-confirm-button="Hapus">
                            @csrf @method('DELETE')
                            <button type="submit" class="small-btn text-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="review-card" style="grid-column:1/-1;">
                    <div class="review-empty">
                        <i class="bi bi-chat-square-text fs-1 d-block mb-2"></i>
                        <strong class="d-block text-dark mb-1">Ulasan tidak ditemukan.</strong>
                        Ubah kata kunci atau filter untuk melihat ulasan lain.
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    @if($ulasan->hasPages())
        <div class="p-3 border-top bg-white">{{ $ulasan->links() }}</div>
    @endif
</div>

@foreach($ulasan as $item)
    @php
        $fotoItems = collect();
        $videoItems = collect();
        if ($item->foto_ulasan) { $fotoItems->push($item->foto_ulasan); }
        if ($item->video_ulasan) { $videoItems->push($item->video_ulasan); }
        foreach ($item->media ?? [] as $mediaItem) {
            if ($mediaItem->jenis === 'foto') { $fotoItems->push($mediaItem->path); }
            if ($mediaItem->jenis === 'video') { $videoItems->push($mediaItem->path); }
        }
    @endphp
    <div class="modal fade" id="reviewDetailModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
                <div class="modal-header border-0 bg-light">
                    <div>
                        <h5 class="fw-black text-dark mb-1">Detail Ulasan</h5>
                        <div class="text-muted small fw-bold">{{ $item->pesanan->nomor_invoice ?? '-' }} · {{ optional($item->created_at)->format('d/m/Y H:i') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="p-3 rounded-4 bg-light h-100">
                                <div class="small text-muted fw-bold mb-1">Produk</div>
                                <div class="fw-black text-dark">{{ $item->produk->nama ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-4 bg-light h-100">
                                <div class="small text-muted fw-bold mb-1">Pembeli</div>
                                <div class="fw-black text-dark">{{ $item->user->name ?? '-' }}</div>
                                <div class="small text-muted fw-bold">{{ $item->user->email ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="p-3 rounded-4 border mb-3">
                        <div class="d-flex justify-content-between gap-3 flex-wrap align-items-center mb-2">
                            <div class="review-rating fs-6">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi {{ $i <= $item->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                @endfor
                            </div>
                            <span class="chip {{ $item->ditampilkan ? 'c-green' : 'c-gray' }}">{{ $item->ditampilkan ? 'Ditampilkan' : 'Disembunyikan' }}</span>
                        </div>
                        <div class="text-dark fw-bold lh-lg">{{ $item->komentar ?: 'Tidak ada komentar.' }}</div>
                    </div>
                    @if($fotoItems->isNotEmpty() || $videoItems->isNotEmpty())
                        <div class="fw-black text-dark mb-2">Media Ulasan</div>
                        <div class="review-media-row">
                            @foreach($fotoItems as $foto)
                                <img src="{{ asset('storage/' . $foto) }}" alt="Foto ulasan" class="review-media-thumb js-open-media" data-type="image" data-src="{{ asset('storage/' . $foto) }}">
                            @endforeach
                            @foreach($videoItems as $video)
                                <button type="button" class="review-video-box js-open-media" data-type="video" data-src="{{ asset('storage/' . $video) }}" aria-label="Buka video ulasan">
                                    <video src="{{ asset('storage/' . $video) }}" muted preload="metadata"></video>
                                    <i class="bi bi-play-circle-fill"></i>
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="modal-footer border-0 bg-light p-3">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Tutup</button>
                    <form method="POST" action="{{ route('admin.ulasan.toggle', $item) }}" class="inline-form">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-brand">{{ $item->ditampilkan ? 'Sembunyikan Ulasan' : 'Tampilkan Ulasan' }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach

<div class="modal fade" id="mediaPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden bg-white">
            <div class="modal-header border-0 bg-light">
                <h5 class="fw-black text-dark mb-0">Media Ulasan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-3 text-center" id="mediaPreviewBody"></div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('.js-open-media').forEach((button) => {
        button.addEventListener('click', () => {
            const type = button.dataset.type;
            const src = button.dataset.src;
            const body = document.getElementById('mediaPreviewBody');

            if (!body || !src) return;

            body.innerHTML = type === 'video'
                ? `<video class="media-preview-video" src="${src}" controls autoplay></video>`
                : `<img class="media-preview-img" src="${src}" alt="Media ulasan">`;

            bootstrap.Modal.getOrCreateInstance(document.getElementById('mediaPreviewModal')).show();
        });
    });

    document.getElementById('mediaPreviewModal')?.addEventListener('hidden.bs.modal', () => {
        const body = document.getElementById('mediaPreviewBody');
        if (body) body.innerHTML = '';
    });
</script>
@endpush
@endsection
