@extends('layouts.pembeli')

@section('title', 'Keranjang Belanja - SiTahu')

@push('styles')
<style>
    .cart-hero {
        padding: 28px;
        margin-bottom: 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        background:
            radial-gradient(circle at top right, rgba(223, 186, 104, 0.25), transparent 32%),
            linear-gradient(135deg, #ffffff 0%, #fff8e8 100%);
    }

    .cart-hero h1 {
        margin: 12px 0 0;
        color: var(--heading);
        font-size: clamp(30px, 4.5vw, 48px);
        line-height: 1;
        letter-spacing: -0.075em;
    }

    .cart-hero h1 span {
        color: var(--brand-text);
    }

    .cart-hero p {
        margin: 12px 0 0;
        max-width: 650px;
        color: var(--muted);
        line-height: 1.7;
        font-size: 15px;
    }

    .cart-summary-mini {
        min-width: 220px;
        padding: 18px;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.78);
        border: 1px solid var(--line);
    }

    .cart-summary-mini strong {
        display: block;
        color: var(--heading);
        font-size: 26px;
        letter-spacing: -0.05em;
    }

    .cart-summary-mini span {
        color: var(--muted);
        font-size: 13px;
    }

    .alert {
        margin-bottom: 16px;
        padding: 13px 15px;
        border-radius: 14px;
        font-size: 14px;
        font-weight: 700;
        border: 1px solid transparent;
    }

    .alert-success {
        background: #ecfdf5;
        color: #15803d;
        border-color: #bbf7d0;
    }

    .alert-error {
        background: #fef2f2;
        color: #b91c1c;
        border-color: #fecaca;
    }

    .cart-layout {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 20px;
        align-items: start;
    }

    .cart-list {
        display: grid;
        gap: 14px;
    }

    .cart-item {
        padding: 16px;
        display: grid;
        grid-template-columns: 112px 1fr auto;
        gap: 16px;
        align-items: center;
    }

    .cart-img {
        width: 112px;
        height: 112px;
        border-radius: 18px;
        overflow: hidden;
        background:
            radial-gradient(circle at top right, rgba(223, 186, 104, 0.20), transparent 34%),
            #f9fafb;
        border: 1px solid var(--line);
        display: grid;
        place-items: center;
        color: var(--brand-text);
        font-size: 11px;
        font-weight: 900;
        letter-spacing: 0.05em;
    }

    .cart-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .cart-info h2 {
        margin: 0;
        color: var(--heading);
        font-size: 17px;
        line-height: 1.35;
        letter-spacing: -0.035em;
    }

    .cart-info p {
        margin: 6px 0 0;
        color: var(--muted);
        font-size: 13px;
        line-height: 1.5;
    }

    .cart-price {
        margin-top: 10px;
        color: var(--brand-text);
        font-size: 17px;
        font-weight: 900;
    }

    .qty-box {
        display: flex;
        align-items: center;
        gap: 8px;
        justify-content: flex-end;
        margin-bottom: 12px;
    }

    .qty-button {
        width: 36px;
        height: 36px;
        border-radius: 12px;
        border: 1px solid var(--line);
        background: #ffffff;
        color: var(--heading);
        font-weight: 900;
        cursor: pointer;
        transition: 0.16s ease;
    }

    .qty-button:hover {
        background: var(--brand-soft);
        color: var(--brand-text);
    }

    .qty-button:disabled {
        opacity: 0.55;
        cursor: not-allowed;
    }

    .qty-value {
        min-width: 38px;
        height: 36px;
        border-radius: 12px;
        display: grid;
        place-items: center;
        background: #f9fafb;
        border: 1px solid var(--line);
        color: var(--heading);
        font-weight: 900;
    }

    .item-subtotal {
        text-align: right;
        color: var(--heading);
        font-size: 15px;
        font-weight: 900;
        margin-bottom: 10px;
    }

    .remove-button {
        border: 0;
        background: transparent;
        color: #b91c1c;
        font-size: 13px;
        font-weight: 800;
        cursor: pointer;
        padding: 0;
    }

    .remove-button:hover {
        text-decoration: underline;
    }

    .summary-card {
        padding: 20px;
        position: sticky;
        top: 96px;
    }

    .summary-card h2 {
        margin: 0;
        color: var(--heading);
        font-size: 22px;
        letter-spacing: -0.045em;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 13px 0;
        border-bottom: 1px solid var(--line);
        color: var(--muted);
        font-size: 14px;
    }

    .summary-row strong {
        color: var(--heading);
    }

    .summary-total {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 16px 0 18px;
        color: var(--heading);
        font-weight: 900;
        font-size: 18px;
    }

    .summary-total span:last-child {
        color: var(--brand-text);
    }

    .summary-actions {
        display: grid;
        gap: 10px;
    }

    .summary-note {
        margin-top: 14px;
        padding: 13px;
        border-radius: 14px;
        background: #f9fafb;
        border: 1px solid var(--line);
        color: var(--muted);
        font-size: 13px;
        line-height: 1.55;
    }

    .empty-cart {
        padding: 44px 24px;
        text-align: center;
    }

    .empty-icon {
        width: 66px;
        height: 66px;
        margin: 0 auto 16px;
        display: grid;
        place-items: center;
        border-radius: 20px;
        background: var(--brand-soft);
        color: var(--brand-text);
        font-size: 30px;
    }

    .empty-cart h2 {
        margin: 0;
        color: var(--heading);
        font-size: 25px;
        letter-spacing: -0.05em;
    }

    .empty-cart p {
        margin: 9px auto 0;
        max-width: 460px;
        color: var(--muted);
        line-height: 1.7;
        font-size: 14px;
    }

    .empty-actions {
        margin-top: 18px;
        display: flex;
        justify-content: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .hidden {
        display: none !important;
    }

    @media (max-width: 960px) {
        .cart-hero {
            align-items: flex-start;
            flex-direction: column;
        }

        .cart-summary-mini {
            width: 100%;
        }

        .cart-layout {
            grid-template-columns: 1fr;
        }

        .summary-card {
            position: static;
        }
    }

    @media (max-width: 650px) {
        .cart-hero,
        .summary-card {
            padding: 20px;
        }

        .cart-item {
            grid-template-columns: 1fr;
        }

        .cart-img {
            width: 100%;
            height: 210px;
        }

        .qty-box,
        .item-subtotal {
            justify-content: flex-start;
            text-align: left;
        }
    }
</style>
@endpush

@section('content')
<section class="page-card cart-hero">
    <div>
        <div class="badge">Keranjang Belanja</div>

        <h1>
            Produk pilihanmu <span>siap dipesan</span>
        </h1>

        <p>
            Cek lagi jumlah produk sebelum lanjut. Pastikan tahu favoritmu sudah sesuai
            untuk lauk keluarga, camilan, atau stok praktis di rumah.
        </p>
    </div>

    <div class="cart-summary-mini">
        <strong id="heroTotalItem">{{ $totalItem }}</strong>
        <span>Total produk di keranjang</span>
    </div>
</section>

<div id="cartAlert" class="alert hidden"></div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-error">
        {{ session('error') }}
    </div>
@endif

<section id="cartContent" class="{{ $items->count() ? '' : 'hidden' }}">
    <div class="cart-layout">
        <div class="cart-list" id="cartList">
            @foreach($items as $item)
                @php
                    $produk = $item['produk'];
                    $gambar = $produk->gambarUtama?->url_gambar;
                @endphp

                <article class="page-card cart-item" id="cartItem-{{ $produk->id }}" data-product-id="{{ $produk->id }}">
                    <a href="{{ route('pembeli-web.produk.detail', $produk) }}" class="cart-img">
                        @if($gambar)
                            <img src="{{ asset('storage/' . $gambar) }}" alt="{{ $produk->nama }}">
                        @else
                            PRODUK TAHU
                        @endif
                    </a>

                    <div class="cart-info">
                        <h2>{{ $produk->nama }}</h2>
                        <p>
                            {{ $produk->satuan ?: 'Satuan produk' }}
                            @if($produk->isi_per_satuan)
                                · isi {{ $produk->isi_per_satuan }}
                            @endif
                            · stok tersedia {{ $item['stok_tersedia'] }}
                        </p>

                        <div class="cart-price">
                            Rp {{ number_format($item['harga'], 0, ',', '.') }}
                        </div>
                    </div>

                    <div>
                        <div class="qty-box">
                            <button
                                type="button"
                                class="qty-button js-cart-update"
                                data-url="{{ route('pembeli-web.keranjang.update', $produk) }}"
                                data-action="kurang"
                            >

                            </button>

                            <div class="qty-value" id="qty-{{ $produk->id }}">{{ $item['jumlah'] }}</div>

                            <button
                                type="button"
                                class="qty-button js-cart-update"
                                data-url="{{ route('pembeli-web.keranjang.update', $produk) }}"
                                data-action="tambah"
                            >
                                +
                            </button>
                        </div>

                        <div class="item-subtotal" id="subtotal-{{ $produk->id }}">
                            Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                        </div>

                        <button
                            type="button"
                            class="remove-button js-cart-delete"
                            data-url="{{ route('pembeli-web.keranjang.destroy', $produk) }}"
                            data-product-id="{{ $produk->id }}"
                        >
                            Hapus produk
                        </button>
                    </div>
                </article>
            @endforeach
        </div>

        <aside class="page-card summary-card">
            <h2>Ringkasan belanja</h2>

            <div style="margin-top: 12px;">
                <div class="summary-row">
                    <span>Total item</span>
                    <strong id="summaryTotalItem">{{ $totalItem }} produk</strong>
                </div>

                <div class="summary-row">
                    <span>Subtotal</span>
                    <strong id="summarySubtotal">Rp {{ number_format($totalBelanja, 0, ',', '.') }}</strong>
                </div>

                <div class="summary-row">
                    <span>Pengiriman</span>
                    <strong>Dipilih saat checkout</strong>
                </div>

                <div class="summary-total">
                    <span>Total sementara</span>
                    <span id="summaryTotal">Rp {{ number_format($totalBelanja, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="summary-actions">
                <a href="{{ route('pembeli-web.checkout.index') }}" class="btn btn-primary">
                    Checkout
                </a>

                <a href="{{ route('pembeli-web.produk') }}" class="btn btn-outline">
                    Tambah Produk
                </a>

                <button
                    type="button"
                    class="btn btn-outline js-cart-clear"
                    data-url="{{ route('pembeli-web.keranjang.clear') }}"
                    style="width: 100%;"
                >
                    Kosongkan Keranjang
                </button>
            </div>

            <div class="summary-note">
                Total akhir akan menyesuaikan pilihan pengambilan atau pengantaran saat checkout.
            </div>
        </aside>
    </div>
</section>

<section id="emptyCart" class="page-card empty-cart {{ $items->count() ? 'hidden' : '' }}">
    <div class="empty-icon">🛒</div>

    <h2>Keranjangmu masih kosong</h2>

    <p>
        Yuk pilih produk tahu favoritmu dulu. Ada banyak pilihan tahu segar
        yang cocok untuk lauk harian atau camilan keluarga.
    </p>

    <div class="empty-actions">
        <a href="{{ route('pembeli-web.produk') }}" class="btn btn-primary">
            Lihat Produk
        </a>

        <a href="{{ route('pembeli-web.home') }}" class="btn btn-outline">
            Kembali ke Beranda
        </a>
    </div>
</section>
@endsection

@push('scripts')
<script>
    const csrfToken = '{{ csrf_token() }}';

    const cartAlert = document.getElementById('cartAlert');
    const heroTotalItem = document.getElementById('heroTotalItem');
    const summaryTotalItem = document.getElementById('summaryTotalItem');
    const summarySubtotal = document.getElementById('summarySubtotal');
    const summaryTotal = document.getElementById('summaryTotal');
    const cartContent = document.getElementById('cartContent');
    const emptyCart = document.getElementById('emptyCart');

    function showCartAlert(message, type = 'success') {
        if (!cartAlert) return;

        cartAlert.textContent = message;
        cartAlert.classList.remove('hidden', 'alert-success', 'alert-error');
        cartAlert.classList.add(type === 'success' ? 'alert-success' : 'alert-error');

        setTimeout(() => {
            cartAlert.classList.add('hidden');
        }, 2200);
    }

    function setButtonsLoading(isLoading) {
        document.querySelectorAll('.js-cart-update, .js-cart-delete, .js-cart-clear').forEach(button => {
            button.disabled = isLoading;
        });
    }

    async function sendCartRequest(url, method, body = null) {
        setButtonsLoading(true);

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                },
                body: body ? JSON.stringify(body) : null
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                showCartAlert(data.message || 'Keranjang gagal diperbarui.', 'error');
                return null;
            }

            return data;
        } catch (error) {
            showCartAlert('Koneksi bermasalah. Coba lagi ya.', 'error');
            return null;
        } finally {
            setButtonsLoading(false);
        }
    }

    function updateCartView(data) {
        if (!data) return;

        if (heroTotalItem) {
            heroTotalItem.textContent = data.total_item;
        }

        if (summaryTotalItem) {
            summaryTotalItem.textContent = data.total_item + ' produk';
        }

        if (summarySubtotal) {
            summarySubtotal.textContent = data.total_belanja_format;
        }

        if (summaryTotal) {
            summaryTotal.textContent = data.total_belanja_format;
        }

        const itemIds = data.items.map(item => String(item.produk_id));

        document.querySelectorAll('[id^="cartItem-"]').forEach(card => {
            const productId = card.dataset.productId;

            if (!itemIds.includes(String(productId))) {
                card.remove();
            }
        });

        data.items.forEach(item => {
            const qtyEl = document.getElementById('qty-' + item.produk_id);
            const subtotalEl = document.getElementById('subtotal-' + item.produk_id);

            if (qtyEl) {
                qtyEl.textContent = item.jumlah;
            }

            if (subtotalEl) {
                subtotalEl.textContent = item.subtotal_format;
            }
        });

        if (data.total_item <= 0) {
            cartContent.classList.add('hidden');
            emptyCart.classList.remove('hidden');
        } else {
            cartContent.classList.remove('hidden');
            emptyCart.classList.add('hidden');
        }
    }

    document.querySelectorAll('.js-cart-update').forEach(button => {
        button.addEventListener('click', async function () {
            const url = this.dataset.url;
            const aksi = this.dataset.action;

            const data = await sendCartRequest(url, 'PATCH', {
                aksi: aksi
            });

            if (data) {
                updateCartView(data);
            }
        });
    });

    document.querySelectorAll('.js-cart-delete').forEach(button => {
        button.addEventListener('click', async function () {
            const url = this.dataset.url;

            const data = await sendCartRequest(url, 'DELETE');

            if (data) {
                updateCartView(data);
                showCartAlert(data.message, 'success');
            }
        });
    });

    document.querySelectorAll('.js-cart-clear').forEach(button => {
        button.addEventListener('click', async function () {
            const url = this.dataset.url;

            const data = await sendCartRequest(url, 'DELETE');

            if (data) {
                updateCartView(data);
                showCartAlert(data.message, 'success');
            }
        });
    });
</script>
@endpush
