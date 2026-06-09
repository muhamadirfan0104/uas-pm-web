@extends('layouts.pembeli')

@section('title', 'Beri Ulasan - SiTahu')

@push('styles')
<style>
    .review-product { position: sticky; top: 112px; }
    .review-img { aspect-ratio: 1 / 1; border-radius: 28px; overflow: hidden; background: var(--brand-soft); display:grid; place-items:center; color: var(--brand-dark); }
    .review-img img { width:100%; height:100%; object-fit:cover; }
    .rating-pick { display:grid; grid-template-columns: repeat(5, minmax(0,1fr)); gap:10px; }
    .rating-pick input { display:none; }
    .rating-pick label { min-height:62px; border-radius:18px; border:1px solid var(--line); background:#fff; color:#f59e0b; display:grid; place-items:center; font-size:26px; cursor:pointer; transition:.22s ease; }
    .rating-pick input:checked + label { background: var(--brand-soft); border-color: rgba(200,147,53,.55); box-shadow: 0 0 0 4px rgba(200,147,53,.10); transform: translateY(-2px); }
    .media-help { border: 1px dashed rgba(200,147,53,.35); border-radius: 18px; background: var(--brand-soft); padding: 14px; color: var(--brand-dark); font-size: 13px; font-weight: 750; }
    .old-media-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); gap: 10px; }
    .old-media-item { aspect-ratio: 1 / 1; border-radius: 16px; overflow: hidden; border: 1px solid var(--line); background: #f3f4f6; position: relative; }
    .old-media-item img, .old-media-item video { width: 100%; height: 100%; object-fit: cover; }
    .old-media-item span { position: absolute; left: 8px; bottom: 8px; border-radius: 999px; padding: 4px 8px; color: #fff; background: rgba(17,24,39,.72); font-size: 11px; font-weight: 850; }
</style>
@endpush

@section('content')
@php
    $image = $produk->gambarUtama?->url_gambar;
    $ratingLama = old('rating', $ulasan?->rating ?: 5);
@endphp
<div class="container py-4 py-lg-5">
    <div class="breadcrumb-modern"><a href="{{ route('pembeli-web.home') }}">Beranda</a><i class="bi bi-chevron-right small"></i><a href="{{ route('pembeli-web.pesanan.show', $pesanan->nomor_invoice) }}">Pesanan</a><i class="bi bi-chevron-right small"></i><span>Ulasan</span></div>
    <div class="row g-4 align-items-start">
        <div class="col-lg-5">
            <aside class="surface-strong p-4 review-product">
                <div class="review-img mb-3">@if($image)<img src="{{ asset('storage/' . $image) }}" alt="{{ $produk->nama }}">@else<i class="bi bi-box-seam display-3"></i>@endif</div>
                <span class="eyebrow mb-3"><i class="bi bi-receipt"></i> {{ $pesanan->nomor_invoice }}</span>
                <h1 class="h3 fw-bold mb-2">{{ $produk->nama }}</h1>
                <p class="text-muted fw-semibold mb-0">Ulasan membantu pembeli lain memahami kualitas produk dan membantu toko meningkatkan layanan.</p>
            </aside>
        </div>
        <div class="col-lg-7">
            <section class="surface-strong p-4 p-lg-5">
                <span class="eyebrow mb-2"><i class="bi bi-star-fill"></i> Penilaian produk</span>
                <h2 class="section-heading display-6 mb-3">Bagikan pengalaman Anda.</h2>
                <p class="section-subtitle mb-4">Pilih rating, tulis komentar, dan tambahkan foto/video jika ada.</p>

                <form action="{{ route('pembeli-web.ulasan.store', [$pesanan->nomor_invoice, $produk]) }}" method="POST" enctype="multipart/form-data" class="d-grid gap-4">
                    @csrf
                    <div>
                        <label class="form-label fw-bold">Rating</label>
                        <div class="rating-pick">
                            @for($i=1;$i<=5;$i++)
                                <input type="radio" name="rating" id="rating{{ $i }}" value="{{ $i }}" {{ (int) $ratingLama === $i ? 'checked' : '' }}>
                                <label for="rating{{ $i }}" title="{{ $i }} bintang">{{ $i }} <i class="bi bi-star-fill ms-1"></i></label>
                            @endfor
                        </div>
                    </div>
                    <div>
                        <label class="form-label fw-bold">Komentar</label>
                        <textarea name="komentar" rows="5" class="form-control" style="border-radius:18px;" placeholder="Contoh: Produk segar, pengemasan rapi, dan rasanya enak...">{{ old('komentar', $ulasan?->komentar) }}</textarea>
                    </div>
                    <div class="media-help">
                        <i class="bi bi-images me-1"></i> Anda bisa menambahkan beberapa foto dan video sekaligus. Maksimal 5 foto dan 2 video untuk satu ulasan.
                    </div>

                    @php
                        $mediaLama = collect();
                        if ($ulasan?->foto_ulasan) {
                            $mediaLama->push((object) ['jenis' => 'foto', 'path' => $ulasan->foto_ulasan]);
                        }
                        if ($ulasan?->video_ulasan) {
                            $mediaLama->push((object) ['jenis' => 'video', 'path' => $ulasan->video_ulasan]);
                        }
                        foreach (($ulasan?->media ?? collect()) as $media) {
                            if (! $mediaLama->first(fn ($m) => $m->path === $media->path)) {
                                $mediaLama->push($media);
                            }
                        }
                    @endphp

                    @if($mediaLama->count())
                        <div>
                            <label class="form-label fw-bold">Media ulasan yang sudah tersimpan</label>
                            <div class="old-media-grid">
                                @foreach($mediaLama as $media)
                                    @php
                                        $mediaAda = \Illuminate\Support\Facades\Storage::disk('public')->exists($media->path);
                                    @endphp
                                    <div class="old-media-item">
                                        @if($media->jenis === 'foto')
                                            @if($mediaAda)
                                                <img src="{{ asset('storage/' . $media->path) }}" alt="Foto ulasan">
                                            @else
                                                <div class="w-100 h-100 d-flex align-items-center justify-content-center text-center small fw-bold text-muted p-2"><i class="bi bi-image text-brand me-1"></i> Foto</div>
                                            @endif
                                            <span><i class="bi bi-image"></i> Foto</span>
                                        @else
                                            @if($mediaAda)
                                                <video src="{{ asset('storage/' . $media->path) }}" controls preload="metadata"></video>
                                            @else
                                                <div class="w-100 h-100 d-flex align-items-center justify-content-center text-center small fw-bold text-muted p-2"><i class="bi bi-play-btn text-brand me-1"></i> Video</div>
                                            @endif
                                            <span><i class="bi bi-play-circle"></i> Video</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Foto ulasan <span class="text-muted fw-semibold">opsional</span></label>
                            <input type="file" name="foto_ulasan[]" accept="image/*" class="form-control" style="border-radius:16px;" multiple>
                            <div class="small text-muted fw-semibold mt-2">Bisa pilih beberapa foto sekaligus.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Video ulasan <span class="text-muted fw-semibold">opsional</span></label>
                            <input type="file" name="video_ulasan[]" accept="video/*" class="form-control" style="border-radius:16px;" multiple>
                            <div class="small text-muted fw-semibold mt-2">Bisa pilih sampai dua video.</div>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-brand px-4 py-3" type="submit"><i class="bi bi-send me-2"></i> Kirim Ulasan</button>
                        <a href="{{ route('pembeli-web.pesanan.show', $pesanan->nomor_invoice) }}" class="btn btn-plain px-4 py-3">Batal</a>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>
@endsection
