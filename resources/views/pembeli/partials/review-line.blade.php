@php
    $mediaUlasan = collect();
    if ($item->foto_ulasan) {
        $mediaUlasan->push((object) ['jenis' => 'foto', 'path' => $item->foto_ulasan, 'legacy' => true]);
    }
    if ($item->video_ulasan) {
        $mediaUlasan->push((object) ['jenis' => 'video', 'path' => $item->video_ulasan, 'legacy' => true]);
    }
    foreach ($item->media as $media) {
        if (! $mediaUlasan->first(fn ($m) => $m->path === $media->path)) {
            $mediaUlasan->push($media);
        }
    }
    $produkUlasan = $item->produk ?? ($produk ?? null);
    $namaProdukUlasan = $produkUlasan?->nama ?: 'Produk SiTahu';
    $showProductInfo = $showProductInfo ?? false;
    $namaPembeli = $item->user?->name ?: 'Pembeli';
@endphp

<article class="review-line">
    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
        <div class="d-flex align-items-center gap-3 min-w-0">
            <div class="review-avatar"><i class="bi bi-person-fill"></i></div>
            <div class="min-w-0">
                <div class="fw-black line-clamp-1">{{ $namaPembeli }}</div>
                <div class="small text-muted fw-semibold">{{ optional($item->created_at)->translatedFormat('d M Y') }}</div>
            </div>
        </div>
        <div class="rating-stars small flex-shrink-0">
            @for($i=1;$i<=5;$i++)
                <i class="bi {{ $i <= (int) $item->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
            @endfor
        </div>
    </div>

    @if($showProductInfo && $produkUlasan)
        <a href="{{ route('pembeli-web.produk.detail', $produkUlasan) }}" class="d-flex align-items-center gap-3 text-decoration-none p-2 rounded-4 bg-light border mb-3">
            <div style="width:48px;height:48px;border-radius:14px;overflow:hidden;background:#fff;display:grid;place-items:center;flex:0 0 auto;">
                @if($produkUlasan->gambarUtama?->url_gambar)
                    <img src="{{ asset('storage/' . $produkUlasan->gambarUtama->url_gambar) }}" alt="{{ $namaProdukUlasan }}" style="width:100%;height:100%;object-fit:cover;">
                @else
                    <i class="bi bi-box-seam text-brand fs-5"></i>
                @endif
            </div>
            <div class="min-w-0">
                <div class="small text-muted fw-bold">Produk yang diulas</div>
                <div class="fw-bold line-clamp-1">{{ $namaProdukUlasan }}</div>
            </div>
        </a>
    @endif

    <p class="review-comment mb-0">“{{ $item->komentar ?: 'Pembeli belum menambahkan komentar.' }}”</p>

    @if($mediaUlasan->count())
        <div class="review-media-grid">
            @foreach($mediaUlasan as $media)
                @php
                    $mediaAda = \Illuminate\Support\Facades\Storage::disk('public')->exists($media->path);
                @endphp
                @if($media->jenis === 'foto')
                    @if($mediaAda)
                        <a href="{{ asset('storage/' . $media->path) }}" target="_blank" class="review-media-item" rel="noopener" aria-label="Lihat foto ulasan">
                            <img src="{{ asset('storage/' . $media->path) }}" alt="Foto ulasan {{ $namaProdukUlasan }}">
                            <span class="review-media-badge"><i class="bi bi-image"></i> Foto</span>
                        </a>
                    @else
                        <div class="review-media-item d-flex align-items-center justify-content-center text-center p-3">
                            <div class="small fw-bold text-muted"><i class="bi bi-image fs-3 d-block text-brand mb-1"></i>Foto ulasan</div>
                            <span class="review-media-badge"><i class="bi bi-image"></i> Foto</span>
                        </div>
                    @endif
                @else
                    @if($mediaAda)
                        <div class="review-media-item">
                            <video src="{{ asset('storage/' . $media->path) }}" controls preload="metadata"></video>
                            <span class="review-media-badge"><i class="bi bi-play-circle"></i> Video</span>
                        </div>
                    @else
                        <div class="review-media-item d-flex align-items-center justify-content-center text-center p-3">
                            <div class="small fw-bold text-muted"><i class="bi bi-play-btn fs-3 d-block text-brand mb-1"></i>Video ulasan</div>
                            <span class="review-media-badge"><i class="bi bi-play-circle"></i> Video</span>
                        </div>
                    @endif
                @endif
            @endforeach
        </div>
    @endif
</article>
