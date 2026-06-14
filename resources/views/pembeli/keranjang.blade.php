@extends('layouts.pembeli')

@section('title', 'Keranjang Belanja - SiTahu')

@push('styles')
<style>
    .cart-shell { border-radius: 28px; border: 1px solid var(--line); background: #fff; box-shadow: var(--shadow-xs); overflow: hidden; }
    .cart-head { display: grid; grid-template-columns: 48px minmax(0, 1fr) 160px 170px 170px 120px; gap: 18px; align-items: center; padding: 18px 22px; background: #fff; border-bottom: 1px solid var(--line); color: var(--muted); font-weight: 800; }
    .cart-row { display: grid; grid-template-columns: 48px minmax(0, 1fr) 160px 170px 170px 120px; gap: 18px; align-items: center; padding: 22px; border-bottom: 1px solid var(--line); }
    .cart-row:last-child { border-bottom: 0; }
    .cart-check { width: 20px; height: 20px; border-radius: 6px; cursor: pointer; }
    .cart-check:checked { background-color: var(--brand-color); border-color: var(--brand-color); }
    .cart-product { display: flex; align-items: center; gap: 16px; min-width: 0; }
    .cart-img { width: 88px; height: 88px; border-radius: 18px; overflow: hidden; background: var(--brand-soft); display: grid; place-items: center; flex: 0 0 auto; color: var(--brand-dark); font-weight: 900; }
    .cart-img img { width: 100%; height: 100%; object-fit: cover; }
    .cart-title { color: var(--dark); font-weight: 900; text-decoration: none; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .cart-title:hover { color: var(--brand-dark); }
    .qty-inline { display: inline-flex; align-items: center; border: 1px solid var(--line); border-radius: 14px; overflow: hidden; background: #fff; }
    .qty-inline button { width: 38px; height: 38px; border: 0; background: #fff; font-weight: 900; color: var(--dark); }
    .qty-inline button:disabled { opacity: .45; }
    .qty-inline input { width: 54px; height: 38px; border: 0; border-left: 1px solid var(--line); border-right: 1px solid var(--line); text-align: center; font-weight: 900; }
    .cart-action-link { border: 0; background: transparent; color: var(--muted); font-weight: 800; text-decoration: none; padding: 0; }
    .cart-action-link:hover { color: #dc3545; }
    .cart-bottom-bar { position: sticky; bottom: 0; z-index: 20; border: 1px solid var(--line); border-radius: 26px 26px 0 0; background: rgba(255,255,255,.96); backdrop-filter: blur(16px); box-shadow: 0 -20px 60px rgba(15,23,42,.10); padding: 16px 22px; margin-top: 22px; }
    .selected-total { font-size: clamp(1.45rem, 2vw, 2rem); font-weight: 950; color: var(--brand-dark); letter-spacing: -.04em; }
    .empty-cart-box { border-radius: 30px; border: 1px solid var(--line); background: #fff; box-shadow: var(--shadow-xs); }
    @media (max-width: 991.98px) {
        .cart-head { display: none; }
        .cart-shell { background: transparent; border: 0; box-shadow: none; overflow: visible; }
        .cart-row { grid-template-columns: 34px minmax(0, 1fr); gap: 12px; border: 1px solid var(--line); border-radius: 24px; background: #fff; box-shadow: var(--shadow-xs); margin-bottom: 14px; padding: 16px; }
        .cart-row > .cart-price, .cart-row > .cart-qty, .cart-row > .cart-subtotal, .cart-row > .cart-action { grid-column: 2; }
        .cart-product { align-items: flex-start; }
        .cart-img { width: 76px; height: 76px; border-radius: 16px; }
        .cart-price, .cart-subtotal { display: flex; justify-content: space-between; align-items: center; }
        .cart-price::before { content: 'Harga'; color: var(--muted); font-weight: 700; }
        .cart-subtotal::before { content: 'Subtotal'; color: var(--muted); font-weight: 700; }
        .cart-bottom-bar { border-radius: 22px; margin-bottom: 12px; }
    }
</style>
@endpush

@section('content')
@php
    $selectedProductIds = collect($selectedProductIds ?? [])->map(fn($id) => (int) $id)->all();
    $selectedItems = $items->filter(fn($item) => in_array((int) $item['produk']->id, $selectedProductIds, true));
    $selectedTotalItem = (int) $selectedItems->sum('jumlah');
    $selectedTotalBelanja = (float) $selectedItems->sum('subtotal');
    $allChecked = $items->count() > 0 && count($selectedProductIds) === $items->count();
@endphp

<div class="container py-4 py-lg-5">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3 mb-4">
        <div>
            <div class="breadcrumb-modern"><a href="{{ route('pembeli-web.produk') }}">Produk</a><i class="bi bi-chevron-right small"></i><span>Keranjang</span></div>
            <span class="eyebrow mb-2"><i class="bi bi-bag-check-fill"></i> Keranjang belanja</span>
            <h1 class="section-heading h2 mb-2">Pilih produk yang ingin di-checkout.</h1>
            <p class="section-subtitle mb-0">Centang produk, cek jumlahnya, lalu lanjutkan checkout.</p>
        </div>
        <a href="{{ route('pembeli-web.produk') }}" class="btn btn-soft-brand px-4 py-3"><i class="bi bi-arrow-left me-2"></i> Lanjut Belanja</a>
    </div>

    @if($items->count())
        <form id="cartCheckoutForm" action="{{ route('pembeli-web.keranjang.checkout') }}" method="POST">
            @csrf
        </form>

        <div class="cart-shell">
            <div class="cart-head">
                <div class="text-center">
                    <input type="checkbox" class="form-check-input cart-check js-select-all" aria-label="Pilih semua produk" {{ $allChecked ? 'checked' : '' }}>
                </div>
                <div>Produk</div>
                <div class="text-end">Harga Satuan</div>
                <div class="text-center">Kuantitas</div>
                <div class="text-end">Total Harga</div>
                <div class="text-end">Aksi</div>
            </div>

            @foreach($items as $item)
                @php
                    $produk = $item['produk'];
                    $image = $produk->gambarUtama?->url_gambar;
                    $isSelected = in_array((int) $produk->id, $selectedProductIds, true);
                @endphp
                <div class="cart-row js-cart-row" data-subtotal="{{ $item['subtotal'] }}" data-jumlah="{{ $item['jumlah'] }}">
                    <div class="text-center">
                        <input type="checkbox" name="selected_produk[]" value="{{ $produk->id }}" form="cartCheckoutForm" class="form-check-input cart-check js-cart-select" aria-label="Pilih {{ $produk->nama }}" {{ $isSelected ? 'checked' : '' }}>
                    </div>

                    <div class="cart-product">
                        <a href="{{ route('pembeli-web.produk.detail', $produk) }}" class="cart-img text-decoration-none">
                            @if($image)
                                <img src="{{ asset('storage/' . $image) }}" alt="{{ $produk->nama }}">
                            @else
                                <i class="bi bi-box-seam fs-2"></i>
                            @endif
                        </a>
                        <div class="min-w-0">
                            <a href="{{ route('pembeli-web.produk.detail', $produk) }}" class="cart-title mb-2">{{ $produk->nama }}</a>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="meta-chip"><i class="bi bi-basket2"></i>{{ $produk->satuan }}</span>
                                <span class="meta-chip"><i class="bi bi-box"></i>Stok {{ $item['stok_tersedia'] }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="cart-price text-lg-end fw-bold">{{ $rupiah($item['harga']) }}</div>

                    <div class="cart-qty text-lg-center">
                        <div class="qty-inline">
                            <form action="{{ route('pembeli-web.keranjang.update', $produk) }}" method="POST" class="m-0">
                                @csrf @method('PATCH')
                                <input type="hidden" name="aksi" value="kurang">
                                <button type="submit" {{ $item['jumlah'] <= 1 ? 'disabled' : '' }} aria-label="Kurangi jumlah"><i class="bi bi-dash"></i></button>
                            </form>
                            <form action="{{ route('pembeli-web.keranjang.update', $produk) }}" method="POST" class="m-0">
                                @csrf @method('PATCH')
                                <input type="hidden" name="aksi" value="set">
                                <input type="number" name="jumlah" min="1" max="{{ max(1, $item['stok_tersedia']) }}" value="{{ $item['jumlah'] }}" aria-label="Jumlah {{ $produk->nama }}" onchange="this.form.submit()">
                            </form>
                            <form action="{{ route('pembeli-web.keranjang.update', $produk) }}" method="POST" class="m-0">
                                @csrf @method('PATCH')
                                <input type="hidden" name="aksi" value="tambah">
                                <button type="submit" {{ $item['jumlah'] >= $item['stok_tersedia'] ? 'disabled' : '' }} aria-label="Tambah jumlah"><i class="bi bi-plus"></i></button>
                            </form>
                        </div>
                    </div>

                    <div class="cart-subtotal text-lg-end fw-black text-brand">{{ $rupiah($item['subtotal']) }}</div>

                    <div class="cart-action text-lg-end">
                        <form action="{{ route('pembeli-web.keranjang.destroy', $produk) }}" method="POST" onsubmit="return confirm('Hapus produk ini dari keranjang?')">
                            @csrf @method('DELETE')
                            <button class="cart-action-link" type="submit">Hapus</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="cart-bottom-bar">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <label class="d-flex align-items-center gap-2 fw-bold mb-0">
                        <input type="checkbox" class="form-check-input cart-check js-select-all" {{ $allChecked ? 'checked' : '' }}>
                        Pilih Semua <span class="text-muted fw-semibold">({{ $items->count() }})</span>
                    </label>
                    <form action="{{ route('pembeli-web.keranjang.clear') }}" method="POST" onsubmit="return confirm('Kosongkan semua isi keranjang?')" class="m-0">
                        @csrf @method('DELETE')
                        <button class="cart-action-link text-danger" type="submit">Kosongkan Keranjang</button>
                    </form>
                </div>

                <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-3">
                    <div class="text-sm-end">
                        <div class="fw-bold">Total (<span class="js-selected-count">{{ $selectedTotalItem }}</span> produk)</div>
                        <div class="selected-total js-selected-total">{{ $rupiah($selectedTotalBelanja) }}</div>
                    </div>
                    <button class="btn btn-brand px-5 py-3 js-checkout-btn" type="submit" form="cartCheckoutForm" {{ $selectedTotalItem <= 0 ? 'disabled' : '' }}>
                        Checkout
                    </button>
                </div>
            </div>
        </div>
    @else
        <div class="empty-cart-box p-4 p-lg-5 text-center">
            <div class="stat-icon mx-auto mb-3"><i class="bi bi-bag-x"></i></div>
            <h2 class="h3 fw-bold">Keranjang masih kosong.</h2>
            <p class="text-muted mb-4">Keranjang masih kosong.</p>
            <a href="{{ route('pembeli-web.produk', ['stok' => 'tersedia']) }}" class="btn btn-brand px-4 py-3">Mulai Belanja</a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const itemChecks = [...document.querySelectorAll('.js-cart-select')];
        const selectAllChecks = [...document.querySelectorAll('.js-select-all')];
        const selectedCount = document.querySelector('.js-selected-count');
        const selectedTotal = document.querySelector('.js-selected-total');
        const checkoutBtn = document.querySelector('.js-checkout-btn');
        const rupiah = value => 'Rp ' + Number(value || 0).toLocaleString('id-ID');

        function updateSummary() {
            let count = 0;
            let total = 0;

            itemChecks.forEach(check => {
                const row = check.closest('.js-cart-row');
                if (check.checked && row) {
                    count += Number(row.dataset.jumlah || 0);
                    total += Number(row.dataset.subtotal || 0);
                }
            });

            if (selectedCount) selectedCount.textContent = count;
            if (selectedTotal) selectedTotal.textContent = rupiah(total);
            if (checkoutBtn) checkoutBtn.disabled = count <= 0;

            const allChecked = itemChecks.length > 0 && itemChecks.every(check => check.checked);
            selectAllChecks.forEach(check => check.checked = allChecked);
        }

        itemChecks.forEach(check => check.addEventListener('change', updateSummary));
        selectAllChecks.forEach(check => {
            check.addEventListener('change', function () {
                itemChecks.forEach(item => item.checked = check.checked);
                updateSummary();
            });
        });

        updateSummary();
    });
</script>
@endpush
