@php
    $compact = $compact ?? false;
    $image = $produk->gambarUtama?->url_gambar;
    $rating = round((float) ($produk->rata_rating ?? 0), 1);
    $jumlahUlasan = (int) ($produk->jumlah_ulasan ?? 0);
    $terjual = (int) ($produk->total_terjual ?? 0);
    $stok = (int) $produk->stok;
@endphp
<div class="shop-card d-flex flex-column js-product-card" data-url="{{ route('pembeli-web.produk.detail', $produk) }}" role="link" tabindex="0">
    <a href="{{ route('pembeli-web.produk.detail', $produk) }}" class="product-media">
        <span class="product-badge {{ $stok <= 0 ? 'empty' : '' }}">
            <i class="bi {{ $stok > 0 ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
            {{ $stok > 0 ? 'Tersedia' : 'Habis' }}
        </span>
        @if($image)
            <img src="{{ asset('storage/' . $image) }}" alt="{{ $produk->nama }}" loading="lazy">
        @else
            <span class="product-placeholder">
                <span>
                    <i class="bi bi-box-seam d-block fs-1 mb-2"></i>
                    {{ $produk->nama }}
                </span>
            </span>
        @endif
    </a>

    <div class="p-3 p-md-4 d-flex flex-column flex-grow-1">
        <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
            <span class="meta-chip"><i class="bi bi-basket2"></i>{{ $produk->satuan ?: 'produk' }}</span>
            @if($terjual > 0)
                <span class="small fw-bold text-muted">{{ $terjual }} terjual</span>
            @endif
        </div>

        <a href="{{ route('pembeli-web.produk.detail', $produk) }}" class="text-decoration-none">
            <h3 class="h6 fw-bold text-dark line-clamp-2 mb-2" style="min-height: {{ $compact ? 'auto' : '42px' }};">{{ $produk->nama }}</h3>
        </a>

        @if(! $compact)
            <p class="small text-muted line-clamp-2 mb-3">{{ $produk->deskripsi ?: 'Produk tahu segar yang cocok untuk hidangan harian keluarga.' }}</p>
        @endif

        <div class="d-flex align-items-center justify-content-between gap-2 mt-auto mb-3">
            <div>
                <div class="price-text fs-5">{{ $rupiah($produk->harga) }}</div>
                <div class="small text-muted fw-semibold">Stok {{ $stok }} {{ $produk->satuan }}</div>
            </div>
            <div class="text-end small">
                <div class="rating-stars"><i class="bi bi-star-fill"></i> {{ $rating > 0 ? $rating : '-' }}</div>
                <div class="text-muted fw-semibold">{{ $jumlahUlasan }} ulasan</div>
            </div>
        </div>

        <div class="d-grid gap-2">
            @if($stok > 0)
                <form action="{{ route('pembeli-web.keranjang.store', $produk) }}" method="POST" class="js-add-cart-form">
                    @csrf
                    <input type="hidden" name="jumlah" value="1">
                    <button class="btn btn-brand w-100 py-2" type="submit"><i class="bi bi-bag-plus me-1"></i> Tambah Keranjang</button>
                </form>
            @else
                <button class="btn btn-light border w-100 py-2 fw-bold text-muted" disabled>Stok Habis</button>
            @endif
        </div>
    </div>
</div>
