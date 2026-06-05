@extends('layouts.admin')

@section('title', 'Ulasan - SiTahu')
@section('page_title', 'Ulasan')

@section('content')
<style>
    .review-box {
        border: 1px solid var(--border);
        border-radius: 18px;
        background: #fff;
        margin-bottom: 1.5rem;
        overflow: hidden;
        box-shadow: var(--shadow-soft);
    }

    .review-metric {
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
    }

    .review-metric:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow);
        border-color: rgba(223, 186, 104, 0.42);
    }

    .review-metric-label {
        color: var(--muted);
        font-size: 0.74rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .review-metric-value {
        margin-top: 8px;
        color: var(--text);
        font-size: 1.75rem;
        line-height: 1;
        font-weight: 950;
        letter-spacing: -0.05em;
    }

    .review-metric-note {
        display: inline-block;
        margin-top: 8px;
        font-size: 0.76rem;
        font-weight: 850;
    }

    .review-metric-icon {
        width: 46px;
        height: 46px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.15rem;
    }

    .review-filter {
        padding: 16px;
        border-bottom: 1px solid var(--border);
        background: #fff;
    }

    .review-search {
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

    .review-search:focus-within {
        background: #fff;
        border-color: var(--brand);
        box-shadow: 0 0 0 4px rgba(223, 186, 104, 0.16);
    }

    .review-search input {
        width: 100%;
        border: 0;
        outline: 0;
        background: transparent;
        color: var(--text);
        font-size: 0.9rem;
        font-weight: 650;
    }

    .review-product {
        color: var(--text);
        font-size: 0.92rem;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .review-avatar {
        width: 38px;
        height: 38px;
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

    .rating-stars {
        display: inline-flex;
        align-items: center;
        gap: 2px;
        color: #f59e0b;
        font-size: 0.95rem;
        letter-spacing: 0.02em;
        white-space: nowrap;
    }

    .review-comment {
        max-width: 360px;
        color: #374151;
        font-size: 0.86rem;
        font-weight: 650;
        line-height: 1.5;
    }

    .review-media-stack {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .review-photo {
        width: 58px;
        height: 58px;
        border-radius: 16px;
        object-fit: cover;
        border: 1px solid var(--border);
        background: #f3f4f6;
        cursor: pointer;
        transition: 0.16s ease;
    }

    .review-photo:hover {
        transform: scale(1.04);
        box-shadow: var(--shadow-soft);
    }

    .review-video-thumb {
        width: 86px;
        height: 58px;
        border-radius: 16px;
        border: 1px solid var(--border);
        background: #111827;
        object-fit: cover;
        cursor: pointer;
        transition: 0.16s ease;
    }

    .review-video-thumb:hover {
        transform: scale(1.04);
        box-shadow: var(--shadow-soft);
    }

    .review-photo-empty {
        width: 58px;
        height: 58px;
        border-radius: 16px;
        border: 1px dashed var(--border);
        background: #f9fafb;
        color: var(--muted);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
    }

    .modal-review-photo {
        width: 100%;
        max-height: 72vh;
        object-fit: contain;
        border-radius: 18px;
        background: #f9fafb;
    }

    .modal-review-video {
        width: 100%;
        max-height: 72vh;
        border-radius: 18px;
        background: #000;
    }

    .empty-review {
        padding: 48px 16px;
        text-align: center;
    }

    .empty-review-icon {
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

    @media (max-width: 640px) {
        .review-comment {
            max-width: 220px;
        }

        .review-video-thumb {
            width: 70px;
        }
    }
</style>

<div class="hero">
    <div>
        <h1>Ulasan</h1>
        <p>
            Moderasi ulasan produk tahu dari pembeli. Admin bisa melihat rating,
            komentar, foto ulasan, dan video ulasan dari pembeli.
        </p>
    </div>

    <a href="{{ route('admin.produk.index') }}" class="btn btn-light border fw-bold px-3">
        <i class="bi bi-basket2 me-1 text-muted"></i>
        Lihat Produk
    </a>
</div>

<div class="grid g4 mb-4">
    <div class="review-metric">
        <div>
            <div class="review-metric-label">Rating Rata-rata</div>
            <div class="review-metric-value">{{ $stats['rata_rata'] ?? 0 }}</div>
            <span class="review-metric-note text-warning">Dari 5.0</span>
        </div>

        <div class="review-metric-icon bg-warning-subtle text-warning-emphasis">
            <i class="bi bi-star-fill"></i>
        </div>
    </div>

    <div class="review-metric">
        <div>
            <div class="review-metric-label">Total Ulasan</div>
            <div class="review-metric-value">{{ $stats['total'] ?? 0 }}</div>
            <span class="review-metric-note text-primary">Komentar pembeli</span>
        </div>

        <div class="review-metric-icon bg-primary-subtle text-primary-emphasis">
            <i class="bi bi-chat-square-text-fill"></i>
        </div>
    </div>

    <div class="review-metric">
        <div>
            <div class="review-metric-label">Ulasan Foto</div>
            <div class="review-metric-value">{{ $stats['foto'] ?? 0 }}</div>
            <span class="review-metric-note text-success">Dari kamera/galeri</span>
        </div>

        <div class="review-metric-icon bg-success-subtle text-success-emphasis">
            <i class="bi bi-camera-fill"></i>
        </div>
    </div>

    <div class="review-metric">
        <div>
            <div class="review-metric-label">Ulasan Video</div>
            <div class="review-metric-value">{{ $stats['video'] ?? 0 }}</div>
            <span class="review-metric-note text-danger">Review produk</span>
        </div>

        <div class="review-metric-icon bg-danger-subtle text-danger-emphasis">
            <i class="bi bi-play-btn-fill"></i>
        </div>
    </div>
</div>

<div class="review-box">
    <div class="review-filter">
        <form id="page-filter" class="js-instant-filter" method="GET">
            <div class="row g-3 align-items-center">
                <div class="col-12 col-lg">
                    <div class="review-search">
                        <i class="bi bi-search text-muted"></i>
                        <input
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Cari produk, pembeli, atau komentar ulasan..."
                        >
                    </div>
                </div>

                <div class="col-12 col-md-5 col-lg-3">
                    <select class="form-select" name="rating">
                        <option value="">Semua rating</option>

                        @for($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" @selected(request('rating') == $i)>
                                {{ $i }} Bintang
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="col-12 col-md-5 col-lg-3">
                    <select class="form-select" name="status">
                        <option value="">Semua status</option>
                        <option value="tampil" @selected(request('status') === 'tampil')>
                            Tampil
                        </option>
                        <option value="sembunyi" @selected(request('status') === 'sembunyi')>
                            Disembunyikan
                        </option>
                    </select>
                </div>

                <div class="col-12 col-md-2 col-lg-1">
                    <a href="{{ route('admin.ulasan.index') }}" class="btn btn-light border fw-bold w-100">
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
                <th>Produk</th>
                <th>Pembeli</th>
                <th>Rating</th>
                <th>Komentar</th>
                <th>Media</th>
                <th>Status</th>
                <th class="text-end">Aksi</th>
            </tr>
            </thead>

            <tbody>
            @forelse($ulasan as $review)
                @php
                    $namaPembeli = $review->user?->name ?? 'Pembeli';
                    $initialPembeli = strtoupper(substr($namaPembeli, 0, 2));
                @endphp

                <tr>
                    <td>
                        <div class="review-product">
                            {{ $review->produk?->nama ?? '-' }}
                        </div>
                        <span class="sub">
                            Pesanan #{{ $review->pesanan_id ?? '-' }}
                        </span>
                    </td>

                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="review-avatar">
                                {{ $initialPembeli }}
                            </div>

                            <div class="min-w-0">
                                <div class="fw-bold text-dark text-truncate">
                                    {{ $namaPembeli }}
                                </div>
                                <span class="sub">
                                    {{ $review->user?->email ?? 'Email tidak tersedia' }}
                                </span>
                            </div>
                        </div>
                    </td>

                    <td>
                        <div class="rating-stars" title="{{ $review->rating }} dari 5">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= (int) $review->rating)
                                    <i class="bi bi-star-fill"></i>
                                @else
                                    <i class="bi bi-star text-muted opacity-50"></i>
                                @endif
                            @endfor
                        </div>
                        <span class="sub">{{ $review->rating }}/5</span>
                    </td>

                    <td>
                        <div class="review-comment">
                            {{ $review->komentar ?: 'Tidak ada komentar.' }}
                        </div>
                    </td>

                    <td>
                        <div class="review-media-stack">
                            @if($review->foto_ulasan)
                                <img
                                    class="review-photo"
                                    src="{{ asset('storage/' . $review->foto_ulasan) }}"
                                    alt="Foto ulasan"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalFotoUlasan{{ $review->id }}"
                                >
                            @endif

                            @if($review->video_ulasan)
                                <video
                                    class="review-video-thumb"
                                    muted
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalVideoUlasan{{ $review->id }}"
                                >
                                    <source src="{{ asset('storage/' . $review->video_ulasan) }}">
                                </video>
                            @endif

                            @if(! $review->foto_ulasan && ! $review->video_ulasan)
                                <div class="review-photo-empty" title="Tidak ada media">
                                    <i class="bi bi-image"></i>
                                </div>
                            @endif
                        </div>
                    </td>

                    <td>
                        <span class="chip {{ $review->ditampilkan ? 'c-green' : 'c-gray' }}">
                            {{ $review->ditampilkan ? 'Tampil' : 'Disembunyikan' }}
                        </span>
                    </td>

                    <td class="text-end">
                        <div class="actions justify-content-end">
                            <form
                                class="inline-form"
                                method="POST"
                                action="{{ route('admin.ulasan.toggle', $review) }}"
                                data-confirm-title="Ubah Status Ulasan"
                                data-confirm-message="Yakin ingin mengubah status tampilan ulasan ini?"
                                data-confirm-button="Ubah Status"
                            >
                                @csrf
                                @method('PATCH')

                                <button class="small-btn" type="submit">
                                    @if($review->ditampilkan)
                                        <i class="bi bi-eye-slash text-warning"></i>
                                        Sembunyikan
                                    @else
                                        <i class="bi bi-eye text-success"></i>
                                        Tampilkan
                                    @endif
                                </button>
                            </form>

                            <form
                                class="inline-form"
                                method="POST"
                                action="{{ route('admin.ulasan.destroy', $review) }}"
                                data-confirm-title="Hapus Ulasan"
                                data-confirm-message="Yakin ingin menghapus ulasan ini secara permanen? Foto dan video ulasan juga akan ikut dihapus."
                                data-confirm-button="Hapus"
                            >
                                @csrf
                                @method('DELETE')

                                <button class="small-btn" type="submit">
                                    <i class="bi bi-trash text-danger"></i>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-review">
                            <div class="empty-review-icon">
                                <i class="bi bi-chat-square-text"></i>
                            </div>
                            <strong class="d-block text-dark mb-1">Belum ada ulasan</strong>
                            <span class="text-muted small">
                                Ulasan akan muncul setelah pembeli menyelesaikan pesanan dan memberi rating.
                            </span>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($ulasan->hasPages())
        <div class="p-3 border-top bg-white">
            {{ $ulasan->links() }}
        </div>
    @endif
</div>

@foreach($ulasan as $review)
    @if($review->foto_ulasan)
        <div class="modal fade" id="modalFotoUlasan{{ $review->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header border-0">
                        <div>
                            <h5 class="modal-title fw-bold">Foto Ulasan</h5>
                            <div class="text-muted small">
                                {{ $review->produk?->nama ?? '-' }} · {{ $review->user?->name ?? 'Pembeli' }}
                            </div>
                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>

                    <div class="modal-body pt-0">
                        <img
                            class="modal-review-photo"
                            src="{{ asset('storage/' . $review->foto_ulasan) }}"
                            alt="Foto ulasan"
                        >
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($review->video_ulasan)
        <div class="modal fade" id="modalVideoUlasan{{ $review->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header border-0">
                        <div>
                            <h5 class="modal-title fw-bold">Video Ulasan</h5>
                            <div class="text-muted small">
                                {{ $review->produk?->nama ?? '-' }} · {{ $review->user?->name ?? 'Pembeli' }}
                            </div>
                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>

                    <div class="modal-body pt-0">
                        <video class="modal-review-video" controls>
                            <source src="{{ asset('storage/' . $review->video_ulasan) }}">
                            Browser kamu belum mendukung video.
                        </video>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach
@endsection