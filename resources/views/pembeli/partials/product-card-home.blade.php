@php
    $gambar = $produk->gambarUtama?->url_gambar;
    $stokTersedia = (int) $produk->stok > 0;
    $rataRating = $produk->rata_rating ? round((float) $produk->rata_rating, 1) : null;
    $cardStyle = isset($lebar) ? 'width: ' . $lebar . '; min-width: ' . $lebar . ';' : '';
@endphp

<div class="product-card" style="{{ $cardStyle }}">
    <a href="{{ route('pembeli-web.produk.detail', $produk) }}" class="product-img text-decoration-none">
        <span class="product-badge"><i class="bi bi-stars me-1"></i> {{ $label ?? 'Produk' }}</span>
        @if($gambar)
            <img src="{{ asset('storage/' . $gambar) }}" alt="{{ $produk->nama }}">
        @else
            <div class="product-empty">PRODUK TAHU</div>
        @endif
    </a>
    <div class="p-3 d-flex flex-column flex-grow-1">
        <a href="{{ route('pembeli-web.produk.detail', $produk) }}" class="product-title text-decoration-none mb-2">{{ $produk->nama }}</a>
        <div class="d-flex align-items-center gap-2 mb-2 text-muted" style="font-size: 12px; font-weight: 700;">
            <span><i class="bi bi-star-fill text-warning me-1"></i>{{ $rataRating ?: 'Baru' }}</span>
            <span>•</span>
            <span>Stok {{ $produk->stok }}</span>
        </div>
        <div class="mt-auto d-flex justify-content-between align-items-center gap-2">
            <div>
                <div class="product-price">Rp {{ number_format($produk->harga, 0, ',', '.') }}</div>
                <div class="text-muted small fw-semibold">/{{ $produk->satuan ?: 'produk' }}</div>
            </div>
            @if($stokTersedia)
                <form action="{{ route('pembeli-web.keranjang.store', $produk) }}" method="POST" class="m-0">
                    @csrf
                    <input type="hidden" name="jumlah" value="1">
                    <button type="submit" class="btn-cart-sm" title="Tambah ke Keranjang">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </form>
            @else
                <button type="button" class="btn-cart-sm" disabled style="background:#d1d5db; cursor:not-allowed;" title="Stok habis">
                    <i class="bi bi-x-lg"></i>
                </button>
            @endif
        </div>
    </div>
</div>
