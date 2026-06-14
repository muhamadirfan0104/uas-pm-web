@php
    use Illuminate\Support\Str;

    $miniCartItems = collect($miniCartItems ?? []);
    $miniCartTotalItem = (int) ($miniCartTotalItem ?? 0);
    $miniCartTotalBelanja = (float) ($miniCartTotalBelanja ?? 0);
    $miniCartMoreCount = max(0, $miniCartTotalItem - (int) $miniCartItems->sum('jumlah'));
@endphp

<div class="mini-cart-inner">
    <div class="mini-cart-head">Baru ditambahkan</div>

    @if($miniCartItems->isNotEmpty())
        <div class="mini-cart-list">
            @foreach($miniCartItems as $item)
                @php
                    $produkMini = $item['produk'];
                    $gambarMini = $produkMini->gambarUtama?->url_gambar;
                @endphp
                <a href="{{ route('pembeli-web.produk.detail', $produkMini) }}" class="mini-cart-item">
                    <span class="mini-cart-img">
                        @if($gambarMini)
                            <img src="{{ asset('storage/' . $gambarMini) }}" alt="{{ $produkMini->nama }}">
                        @else
                            {{ Str::upper(Str::substr($produkMini->nama, 0, 1)) }}
                        @endif
                    </span>
                    <span class="min-w-0">
                        <span class="mini-cart-title">{{ Str::limit($produkMini->nama, 42) }}</span>
                        <span class="mini-cart-meta">{{ $item['jumlah'] }} {{ $produkMini->satuan ?: 'item' }}</span>
                    </span>
                    <span class="mini-cart-price">Rp{{ number_format((float) $item['subtotal'], 0, ',', '.') }}</span>
                </a>
            @endforeach
        </div>

        <div class="mini-cart-footer">
            <div class="small text-muted fw-bold">
                @if($miniCartMoreCount > 0)
                    {{ $miniCartMoreCount }} item lainnya
                @else
                    Total {{ $miniCartTotalItem }} item
                @endif
                <div class="text-dark fw-black">Rp{{ number_format($miniCartTotalBelanja, 0, ',', '.') }}</div>
            </div>
            <a href="{{ route('pembeli-web.keranjang.index') }}" class="btn btn-brand px-4 py-2">Lihat Keranjang</a>
        </div>
    @else
        <div class="mini-cart-empty">
            <div class="mini-cart-empty-icon"><i class="bi bi-bag"></i></div>
            <h6 class="fw-black mb-1">Keranjang masih kosong</h6>
            <p class="text-muted mb-3 small fw-semibold">Keranjang kosong.</p>
            <a href="{{ route('pembeli-web.produk') }}" class="btn btn-brand px-4">Belanja Sekarang</a>
        </div>
    @endif
</div>
