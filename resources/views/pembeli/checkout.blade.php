@extends('layouts.pembeli')

@section('title', 'Checkout Pesanan - SiTahu')

@push('styles')
<style>
    .checkout-page { --checkout-gap: 1.15rem; }
    .checkout-top {
        border: 1px solid rgba(200,147,53,.22);
        border-radius: 30px;
        background:
            radial-gradient(circle at 8% 0%, rgba(200,147,53,.18), transparent 23rem),
            linear-gradient(135deg, #fff, #fff8ea 64%, #fff3d8);
        box-shadow: var(--shadow-sm);
        padding: clamp(22px, 3vw, 34px);
    }
    .checkout-title { font-size: clamp(1.55rem, 3vw, 2.35rem); font-weight: 900; letter-spacing: -.045em; line-height: 1.08; margin: 0; }
    .checkout-lead { color: var(--muted); font-weight: 650; line-height: 1.65; max-width: 780px; margin: 10px 0 0; }
    .checkout-steps { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .checkout-steps .step-pill {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 9px 12px;
        border-radius: 999px;
        background: rgba(255,255,255,.72);
        border: 1px solid rgba(200,147,53,.18);
        color: var(--brand-dark);
        font-size: 12px;
        font-weight: 900;
        box-shadow: var(--shadow-xs);
    }
    .checkout-steps .step-pill span {
        width: 20px; height: 20px; border-radius: 50%; display: grid; place-items: center;
        background: var(--brand-color); color: #fff; font-size: 11px; line-height: 1;
    }

    .checkout-card {
        background: rgba(255,255,255,.96);
        border: 1px solid var(--line);
        border-radius: 26px;
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }
    .checkout-card + .checkout-card { margin-top: var(--checkout-gap); }
    .checkout-card-head {
        display: flex; align-items: flex-start; justify-content: space-between; gap: 14px;
        padding: 22px 24px 16px;
        border-bottom: 1px solid #f1f3f5;
        background: linear-gradient(180deg, #fff, #fffdf8);
    }
    .checkout-card-title { display: flex; align-items: flex-start; gap: 12px; }
    .checkout-icon {
        width: 40px; height: 40px; border-radius: 15px;
        display: grid; place-items: center;
        background: var(--brand-soft);
        border: 1px solid rgba(200,147,53,.18);
        color: var(--brand-dark);
        flex: 0 0 auto;
    }
    .checkout-card-title h2 { font-size: 1.08rem; font-weight: 900; letter-spacing: -.02em; margin: 0 0 3px; }
    .checkout-card-title p { color: var(--muted); font-size: .88rem; font-weight: 650; line-height: 1.55; margin: 0; }
    .checkout-card-body { padding: 22px 24px 24px; }

    .checkout-field { min-height: 48px; border-radius: 16px; border-color: var(--line); font-weight: 750; background: #fff; }
    .checkout-field:focus { border-color: rgba(200,147,53,.55); box-shadow: 0 0 0 .25rem rgba(200,147,53,.12); }
    .form-label-mini { color: var(--muted); font-size: 12px; font-weight: 900; text-transform: uppercase; letter-spacing: .04em; margin-bottom: 8px; }

    .checkout-product {
        display: grid;
        grid-template-columns: 72px minmax(0,1fr) 160px 120px;
        align-items: center;
        gap: 16px;
        padding: 14px 0;
        border-bottom: 1px dashed #edf0f3;
    }
    .checkout-product:last-child { border-bottom: 0; padding-bottom: 0; }
    .checkout-product:first-child { padding-top: 0; }
    .checkout-product-img { width: 72px; height: 72px; border-radius: 18px; overflow: hidden; background: var(--brand-soft); display: grid; place-items: center; color: var(--brand-dark); border: 1px solid rgba(200,147,53,.15); }
    .checkout-product-img img { width: 100%; height: 100%; object-fit: cover; }
    .checkout-product-name { font-weight: 900; letter-spacing: -.018em; margin-bottom: 4px; }
    .checkout-product-meta { color: var(--muted); font-size: 12px; font-weight: 750; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .checkout-product-meta .dot { width: 4px; height: 4px; border-radius: 999px; background: #d0d5dd; }
    .checkout-product-price { text-align: right; font-weight: 850; }
    .checkout-product-total { text-align: right; color: var(--brand-dark); font-weight: 900; }

    .option-grid { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: 14px; }
    .checkout-option {
        position: relative;
        display: block;
        height: 100%;
        padding: 17px;
        border: 1px solid var(--line);
        border-radius: 20px;
        background: #fff;
        cursor: pointer;
        transition: .2s ease;
    }
    .checkout-option:hover { border-color: rgba(200,147,53,.36); background: #fffdf7; }
    .checkout-option:has(input:checked) { border-color: rgba(200,147,53,.82); background: var(--brand-soft); box-shadow: 0 0 0 4px rgba(200,147,53,.11); }
    .checkout-option input { position: absolute; top: 18px; right: 18px; }
    .checkout-option-icon { width: 38px; height: 38px; border-radius: 14px; display: grid; place-items: center; background: #fff; border: 1px solid rgba(200,147,53,.18); color: var(--brand-dark); margin-bottom: 12px; }
    .checkout-option-title { font-weight: 900; margin-bottom: 4px; padding-right: 26px; }
    .checkout-option-desc { color: var(--muted); font-size: 13px; font-weight: 650; line-height: 1.55; margin: 0; }
    .option-note { display: inline-flex; align-items: center; gap: 6px; margin-top: 12px; padding: 6px 10px; border-radius: 999px; color: var(--brand-dark); background: rgba(200,147,53,.10); font-size: 11px; font-weight: 900; }

    .address-panel {
        margin-top: 16px;
        padding: 16px;
        border-radius: 20px;
        border: 1px dashed rgba(200,147,53,.34);
        background: #fffdf8;
    }
    .selected-address-card { display: flex; gap: 13px; align-items: flex-start; padding: 16px; border: 1px solid rgba(200,147,53,.25); background: #fff; border-radius: 18px; }
    .selected-address-radio { width: 20px; height: 20px; border-radius: 50%; border: 5px solid var(--brand-color); flex: 0 0 auto; margin-top: 3px; }
    .selected-address-name { font-weight: 900; color: var(--ink); }
    .selected-address-separator { color: #c9ced6; margin: 0 8px; }
    .selected-address-text { color: var(--muted); font-weight: 650; line-height: 1.55; margin-top: 5px; }
    .address-modal-list { max-height: 58vh; overflow: auto; padding-right: 4px; }
    .address-choice { display: grid; grid-template-columns: 22px minmax(0,1fr) auto; gap: 14px; padding: 18px 0; border-bottom: 1px solid var(--line); align-items: flex-start; }
    .address-choice:last-child { border-bottom: 0; }
    .address-choice .form-check-input { width: 20px; height: 20px; margin-top: 4px; }
    .address-choice-name { font-weight: 900; color: var(--ink); }
    .address-choice-phone { color: var(--muted); font-weight: 750; }
    .address-choice-address { color: var(--muted); font-weight: 650; line-height: 1.55; margin-top: 6px; }
    .address-main-badge { display: inline-flex; margin-top: 8px; padding: 3px 8px; border-radius: 6px; border: 1px solid #f59e0b; color: #b45309; font-size: 12px; font-weight: 900; }
    .checkout-note {
        border: 1px solid var(--line);
        border-radius: 20px;
        background: #fff;
        padding: 16px;
    }
    .checkout-agreement {
        display: flex;
        align-items: flex-start;
        gap: 11px;
        padding: 14px 16px;
        border-radius: 18px;
        background: #f9fafb;
        border: 1px solid var(--line);
    }

    .transfer-bank-panel,
    .cod-payment-panel {
        border: 1px solid rgba(200,147,53,.24);
        border-radius: 22px;
        background: linear-gradient(180deg, #fffdf8, #fff);
        padding: 18px;
        margin-bottom: 16px;
    }
    .transfer-bank-panel.is-hidden,
    .cod-payment-panel.is-hidden { display: none; }
    .bank-account-card {
        display: grid;
        grid-template-columns: 44px minmax(0,1fr) auto;
        gap: 13px;
        align-items: center;
        padding: 14px;
        border-radius: 18px;
        background: #fff;
        border: 1px solid var(--line);
        margin-bottom: 14px;
    }
    .bank-account-icon {
        width: 44px; height: 44px; border-radius: 15px;
        display: grid; place-items: center;
        color: var(--brand-dark);
        background: var(--brand-soft);
        border: 1px solid rgba(200,147,53,.18);
    }
    .bank-name { color: var(--muted); font-size: 12px; font-weight: 900; text-transform: uppercase; letter-spacing: .04em; }
    .bank-number { color: var(--ink); font-size: 1.22rem; font-weight: 950; letter-spacing: -.02em; line-height: 1.15; }
    .bank-owner { color: var(--muted); font-size: 13px; font-weight: 750; margin-top: 2px; }
    .proof-upload-box {
        padding: 14px;
        border-radius: 18px;
        background: #f9fafb;
        border: 1px dashed rgba(200,147,53,.38);
    }
    .proof-upload-box .form-control { border-radius: 14px; min-height: 46px; font-weight: 750; }

    .summary-box { position: sticky; top: 112px; }
    .summary-card {
        background: #fff;
        border: 1px solid rgba(200,147,53,.20);
        border-radius: 28px;
        box-shadow: var(--shadow-md);
        overflow: hidden;
    }
    .summary-head { padding: 22px 22px 14px; background: linear-gradient(180deg, #fff8ea, #fff); border-bottom: 1px solid #f1f3f5; }
    .summary-head h2 { font-size: 1.08rem; font-weight: 900; margin: 0; letter-spacing: -.02em; }
    .summary-head p { color: var(--muted); font-size: 13px; font-weight: 650; margin: 5px 0 0; }
    .summary-body { padding: 18px 22px 22px; }
    .summary-mini-list { max-height: 236px; overflow: auto; padding-right: 4px; margin-bottom: 16px; }
    .summary-mini-item { display: grid; grid-template-columns: 52px minmax(0,1fr); gap: 11px; padding: 10px 0; border-bottom: 1px dashed #edf0f3; }
    .summary-mini-item:last-child { border-bottom: 0; }
    .summary-mini-img { width: 52px; height: 52px; border-radius: 15px; background: var(--brand-soft); display: grid; place-items: center; color: var(--brand-dark); overflow: hidden; border: 1px solid rgba(200,147,53,.12); }
    .summary-mini-img img { width: 100%; height: 100%; object-fit: cover; }
    .summary-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 8px 0; color: var(--muted); font-weight: 750; }
    .summary-row strong { color: var(--ink); font-weight: 900; }
    .summary-total { display: flex; align-items: flex-end; justify-content: space-between; gap: 12px; padding-top: 16px; margin-top: 8px; border-top: 1px solid var(--line); }
    .summary-total .total-label { font-weight: 900; color: var(--ink); }
    .summary-total .total-price { color: var(--brand-dark); font-weight: 900; font-size: clamp(1.45rem, 3vw, 1.85rem); letter-spacing: -.035em; line-height: 1; }
    .summary-safe { display: flex; gap: 10px; margin-top: 14px; padding: 13px; border-radius: 18px; background: #f9fafb; border: 1px solid var(--line); color: var(--muted); font-size: 12px; font-weight: 700; line-height: 1.5; }
    .summary-safe i { color: var(--brand-dark); flex: 0 0 auto; margin-top: 2px; }

    .checkout-mobile-bar { display: none; }

    @media (max-width: 991.98px) {
        .summary-box { position: static; }
        .checkout-product { grid-template-columns: 62px minmax(0,1fr); align-items: start; }
        .checkout-product-price, .checkout-product-total { grid-column: 2; text-align: left; }
        .option-grid { grid-template-columns: 1fr; }
        .checkout-mobile-bar { display: block; position: sticky; bottom: 0; z-index: 1030; margin: 0 -1.25rem; padding: 12px 1.25rem; background: rgba(255,255,255,.92); backdrop-filter: blur(12px); border-top: 1px solid var(--line); }
        .checkout-page { padding-bottom: 76px; }
    }
    @media (max-width: 575.98px) {
        .checkout-card-head, .checkout-card-body, .summary-head, .summary-body { padding-left: 18px; padding-right: 18px; }
        .checkout-top { border-radius: 24px; }
        .checkout-steps .step-pill { font-size: 11px; }
    }
</style>
@endpush

@section('content')
@php
    $deliveryQuote = $deliveryQuote ?? ['jarak' => null, 'biaya' => (float) ($pengaturan->biaya_minimum_pengiriman ?? 0)];
    $biayaKirim = (float) ($deliveryQuote['biaya'] ?? ($pengaturan->biaya_minimum_pengiriman ?? 0));
    $jarakKirim = $deliveryQuote['jarak'] ?? null;
    $initialShipping = old('metode_pengambilan') === 'kurir_toko' ? $biayaKirim : 0;
    $alamatToko = $pengaturan->alamat ?: 'Alamat toko belum diatur.';
    $bankNama = trim((string) ($pengaturan->bank_nama ?? '')) ?: 'Bank belum diatur';
    $bankNomor = trim((string) ($pengaturan->bank_nomor_rekening ?? '')) ?: 'Nomor rekening belum diatur';
    $bankAtasNama = trim((string) ($pengaturan->bank_atas_nama ?? '')) ?: ($pengaturan->nama ?: 'SiTahu');
    $catatanPembayaran = trim((string) ($pengaturan->info_pembayaran ?? '')) ?: 'Transfer dapat dilakukan setelah pesanan dibuat. Nomor rekening dan form upload bukti akan muncul pada pop up konfirmasi pembayaran.';
    $selectedAlamatId = (int) old('alamat_id', $alamatUtama?->id);
    $selectedAlamat = $alamatPembeli->firstWhere('id', $selectedAlamatId) ?? $alamatUtama;
@endphp

<div class="container checkout-page py-4 py-lg-5"
     data-store-lat="{{ $pengaturan->latitude_toko }}"
     data-store-lng="{{ $pengaturan->longitude_toko }}"
     data-rate-per-km="{{ (float) ($pengaturan->tarif_per_km ?? 0) }}"
     data-min-shipping="{{ (float) ($pengaturan->biaya_minimum_pengiriman ?? 0) }}"
     data-max-radius="{{ (float) ($pengaturan->radius_maksimal_km ?? 0) }}">
    <div class="breadcrumb-modern">
        <a href="{{ route('pembeli-web.keranjang.index') }}">Keranjang</a>
        <i class="bi bi-chevron-right small"></i>
        <span>Checkout</span>
    </div>

    <div class="checkout-top mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3">
            <div>
                <span class="eyebrow mb-3"><i class="bi bi-bag-check-fill"></i> Checkout pesanan</span>
                <h1 class="checkout-title">Selesaikan pesanan Anda.</h1>
                <p class="checkout-lead">Pastikan alamat penerima, produk, pilihan pengambilan, dan metode pembayaran sudah sesuai sebelum pesanan dibuat.</p>
            </div>
            <div class="checkout-steps">
                <span class="step-pill"><span>1</span> Produk</span>
                <span class="step-pill"><span>2</span> Pengambilan</span>
                <span class="step-pill"><span>3</span> Pembayaran</span>
            </div>
        </div>
    </div>

    <form id="checkoutForm" action="{{ route('pembeli-web.checkout.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-4 align-items-start">
            <div class="col-lg-8">
                <section class="checkout-card">
                    <div class="checkout-card-head">
                        <div class="checkout-card-title">
                            <div class="checkout-icon"><i class="bi bi-geo-alt"></i></div>
                            <div>
                                <h2>Alamat penerima</h2>
                                <p>Pilih nama, nomor HP, email, dan alamat yang akan dipakai untuk pesanan ini.</p>
                            </div>
                        </div>
                        <a href="{{ route('pembeli-web.alamat.create', ['redirect' => 'checkout']) }}" class="btn btn-soft-brand btn-sm px-3"><i class="bi bi-plus-circle me-1"></i> Tambah</a>
                    </div>
                    <div class="checkout-card-body">
                        @if($alamatPembeli->count())
                            <input type="hidden" name="alamat_id" id="selectedAddressId" value="{{ $selectedAlamat?->id }}">
                            <div class="selected-address-card" id="selectedAddressCard">
                                <span class="selected-address-radio"></span>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <span class="selected-address-name js-address-name">{{ $selectedAlamat?->nama_penerima }}</span>
                                        <span class="selected-address-separator">|</span>
                                        <span class="text-muted fw-semibold js-address-phone">{{ $selectedAlamat?->telepon }}</span>
                                        @if($selectedAlamat?->utama)
                                            <span class="address-main-badge js-address-main mt-0">Utama</span>
                                        @else
                                            <span class="address-main-badge js-address-main mt-0" style="display:none;">Utama</span>
                                        @endif
                                    </div>
                                    <div class="small text-muted fw-semibold mt-1 js-address-email">{{ $selectedAlamat?->email_penerima ?: $user->email }}</div>
                                    <div class="selected-address-text js-address-text">{{ $selectedAlamat?->alamat_lengkap }}</div>
                                </div>
                                <button type="button" class="btn btn-link fw-bold text-decoration-none px-0" data-bs-toggle="modal" data-bs-target="#addressModal">Ubah</button>
                            </div>
                            @error('alamat_id')<div class="invalid-feedback d-block mt-2">{{ $message }}</div>@enderror
                        @else
                            <input type="hidden" name="alamat_id" id="selectedAddressId" value="">
                            <div class="alert alert-warning alert-shop mb-0 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                                <div>
                                    <strong>Belum ada alamat penerima.</strong><br>
                                    Tambahkan alamat berisi nama penerima, nomor HP, email, dan alamat lengkap sebelum checkout.
                                </div>
                                <a href="{{ route('pembeli-web.alamat.create', ['redirect' => 'checkout']) }}" class="btn btn-brand px-3">Tambah Alamat</a>
                            </div>
                            @error('alamat_id')<div class="invalid-feedback d-block mt-2">{{ $message }}</div>@enderror
                        @endif
                    </div>
                </section>

                <section class="checkout-card">
                    <div class="checkout-card-head">
                        <div class="checkout-card-title">
                            <div class="checkout-icon"><i class="bi bi-basket2"></i></div>
                            <div>
                                <h2>Produk dipesan</h2>
                                <p>{{ $totalItem }} item akan diproses dalam pesanan ini.</p>
                            </div>
                        </div>
                        <a href="{{ route('pembeli-web.keranjang.index') }}" class="btn btn-soft-brand btn-sm px-3">Ubah</a>
                    </div>
                    <div class="checkout-card-body">
                        @foreach($items as $item)
                            @php
                                $produkItem = $item['produk'];
                                $imageItem = $produkItem->gambarUtama?->url_gambar;
                            @endphp
                            <div class="checkout-product">
                                <div class="checkout-product-img">
                                    @if($imageItem)
                                        <img src="{{ asset('storage/' . $imageItem) }}" alt="{{ $produkItem->nama }}">
                                    @else
                                        <i class="bi bi-box-seam fs-4"></i>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="checkout-product-name line-clamp-1">{{ $produkItem->nama }}</div>
                                    <div class="checkout-product-meta">
                                        <span>{{ $produkItem->satuan ?: 'produk' }}</span>
                                        <span class="dot"></span>
                                        <span>Stok {{ $item['stok_tersedia'] ?? $produkItem->stok }}</span>
                                        <span class="dot"></span>
                                        <span>{{ $item['jumlah'] }} item</span>
                                    </div>
                                </div>
                                <div class="checkout-product-price">
                                    <div class="small text-muted fw-bold">Harga satuan</div>
                                    <div>{{ $rupiah($item['harga']) }}</div>
                                </div>
                                <div class="checkout-product-total">
                                    <div class="small text-muted fw-bold">Subtotal</div>
                                    <div>{{ $rupiah($item['subtotal']) }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="checkout-card">
                    <div class="checkout-card-head">
                        <div class="checkout-card-title">
                            <div class="checkout-icon"><i class="bi bi-truck"></i></div>
                            <div>
                                <h2>Pengambilan pesanan</h2>
                                <p>Pilih ambil di toko atau gunakan kurir toko sesuai kebutuhan.</p>
                            </div>
                        </div>
                    </div>
                    <div class="checkout-card-body">
                        <div class="option-grid">
                            <label class="checkout-option">
                                <input type="radio" name="metode_pengambilan" value="ambil_toko" class="form-check-input js-shipping-choice" {{ old('metode_pengambilan', 'ambil_toko') === 'ambil_toko' ? 'checked' : '' }} data-shipping="0">
                                <div class="checkout-option-icon"><i class="bi bi-shop"></i></div>
                                <div class="checkout-option-title">Ambil di toko</div>
                                <p class="checkout-option-desc">Pesanan diambil langsung setelah diproses oleh toko.</p>
                                <span class="option-note"><i class="bi bi-check2-circle"></i> Tanpa ongkir</span>
                            </label>
                            <label class="checkout-option">
                                <input type="radio" name="metode_pengambilan" value="kurir_toko" class="form-check-input js-shipping-choice" {{ old('metode_pengambilan') === 'kurir_toko' ? 'checked' : '' }} data-shipping="{{ $biayaKirim }}">
                                <div class="checkout-option-icon"><i class="bi bi-scooter"></i></div>
                                <div class="checkout-option-title">Kurir toko</div>
                                <p class="checkout-option-desc">Pesanan dikirim ke alamat yang masih berada dalam area layanan toko.</p>
                                <span class="option-note"><i class="bi bi-cash-coin"></i> <span class="js-delivery-fee-label">{{ $rupiah($biayaKirim) }}</span></span>
                            </label>
                        </div>

                        <div class="address-panel js-delivery-note {{ old('metode_pengambilan') === 'kurir_toko' ? '' : 'd-none' }}">
                            <div class="d-flex gap-2 align-items-start">
                                <i class="bi bi-calculator text-brand mt-1"></i>
                                <div class="small fw-semibold text-muted">
                                    Ongkir dihitung otomatis dari titik toko ke titik alamat penerima. Estimasi jarak:
                                    <strong class="text-dark js-distance-text">{{ $jarakKirim ? number_format((float) $jarakKirim, 2, ',', '.') . ' km' : 'pilih alamat bertitik maps' }}</strong>.
                                    Tarif: <strong class="text-dark">{{ $rupiah($pengaturan->tarif_per_km ?? 0) }}/km</strong>, minimum <strong class="text-dark">{{ $rupiah($pengaturan->biaya_minimum_pengiriman ?? 0) }}</strong>.
                                    <span class="d-block mt-1 js-radius-warning text-danger fw-bold" style="display:none !important;">Alamat ini berada di luar radius layanan toko.</span>
                                </div>
                            </div>
                        </div>

                        <div class="address-panel js-pickup-note">
                            <div class="d-flex gap-3 align-items-start">
                                <div class="checkout-icon"><i class="bi bi-geo-alt"></i></div>
                                <div>
                                    <div class="fw-black mb-1">Alamat toko</div>
                                    <div class="text-muted fw-semibold small lh-lg">{{ $alamatToko }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="checkout-card">
                    <div class="checkout-card-head">
                        <div class="checkout-card-title">
                            <div class="checkout-icon"><i class="bi bi-wallet2"></i></div>
                            <div>
                                <h2>Pembayaran</h2>
                                <p>Pilih metode pembayaran yang tersedia untuk pesanan ini.</p>
                            </div>
                        </div>
                    </div>
                    <div class="checkout-card-body">
                        <div class="option-grid mb-3">
                            <label class="checkout-option">
                                <input type="radio" name="metode_pembayaran" value="transfer_bank" class="form-check-input js-payment-choice" {{ old('metode_pembayaran', 'transfer_bank') === 'transfer_bank' ? 'checked' : '' }}>
                                <div class="checkout-option-icon"><i class="bi bi-bank"></i></div>
                                <div class="checkout-option-title">Transfer Bank</div>
                                <p class="checkout-option-desc">Buat pesanan dulu, lalu rekening dan upload bukti akan muncul otomatis.</p>
                            </label>
                            <label class="checkout-option">
                                <input type="radio" name="metode_pembayaran" value="cod" class="form-check-input js-payment-choice" {{ old('metode_pembayaran') === 'cod' ? 'checked' : '' }}>
                                <div class="checkout-option-icon"><i class="bi bi-cash-coin"></i></div>
                                <div class="checkout-option-title">COD</div>
                                <p class="checkout-option-desc">Bayar langsung saat pesanan diambil atau diterima dari kurir toko.</p>
                            </label>
                        </div>

                        <div class="transfer-bank-panel js-transfer-bank-panel">
                            <div class="d-flex gap-3 align-items-start">
                                <div class="checkout-icon"><i class="bi bi-bank2"></i></div>
                                <div>
                                    <div class="fw-black mb-1">Transfer Bank</div>
                                    <div class="small text-muted fw-semibold lh-lg">Setelah pesanan dibuat, nomor rekening toko dan form upload bukti transfer akan muncul dalam pop up konfirmasi pembayaran.</div>
                                </div>
                            </div>
                        </div>

                        <div class="cod-payment-panel js-cod-payment-panel is-hidden">
                            <div class="d-flex gap-3 align-items-start">
                                <div class="checkout-icon"><i class="bi bi-cash-coin"></i></div>
                                <div>
                                    <div class="fw-black mb-1">Pembayaran COD</div>
                                    <div class="small text-muted fw-semibold lh-lg">Pembayaran dilakukan langsung saat pesanan diambil di toko atau diterima dari kurir toko.</div>
                                </div>
                            </div>
                        </div>

                        <div class="checkout-note mb-3">
                            <label class="form-label-mini">Catatan untuk toko</label>
                            <textarea name="catatan" rows="3" class="form-control checkout-field" placeholder="Contoh: ambil sore hari, jangan terlalu banyak air, atau catatan lain untuk toko.">{{ old('catatan') }}</textarea>
                        </div>

                        <label class="checkout-agreement" for="setuju">
                            <input class="form-check-input mt-1 @error('setuju') is-invalid @enderror" type="checkbox" value="1" id="setuju" name="setuju" {{ old('setuju') ? 'checked' : '' }} required>
                            <span>
                                <span class="fw-black d-block">Saya sudah mengecek pesanan.</span>
                                <span class="small text-muted fw-semibold">Produk, jumlah, alamat penerima, pengambilan, dan metode pembayaran sudah sesuai.</span>
                                @error('setuju')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </span>
                        </label>
                    </div>
                </section>
            </div>

            <div class="col-lg-4">
                <aside class="summary-box">
                    <div class="summary-card">
                        <div class="summary-head">
                            <h2><i class="bi bi-receipt me-2 text-brand"></i>Ringkasan pembayaran</h2>
                            <p>{{ $totalItem }} item dipilih dari keranjang.</p>
                        </div>
                        <div class="summary-body">
                            <div class="summary-mini-list">
                                @foreach($items as $item)
                                    @php
                                        $produk = $item['produk'];
                                        $image = $produk->gambarUtama?->url_gambar;
                                    @endphp
                                    <div class="summary-mini-item">
                                        <div class="summary-mini-img">
                                            @if($image)
                                                <img src="{{ asset('storage/' . $image) }}" alt="{{ $produk->nama }}">
                                            @else
                                                <i class="bi bi-box-seam"></i>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <div class="fw-black line-clamp-1">{{ $produk->nama }}</div>
                                            <div class="small text-muted fw-bold mt-1">{{ $item['jumlah'] }} x {{ $rupiah($item['harga']) }}</div>
                                            <div class="small fw-black text-brand mt-1">{{ $rupiah($item['subtotal']) }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="summary-row"><span>Subtotal produk</span><strong>{{ $rupiah($subtotal) }}</strong></div>
                            <div class="summary-row"><span>Jarak pengiriman</span><strong class="js-summary-distance">{{ old('metode_pengambilan') === 'kurir_toko' && $jarakKirim ? number_format((float) $jarakKirim, 2, ',', '.') . ' km' : '-' }}</strong></div>
                            <div class="summary-row"><span>Biaya pengiriman</span><strong class="js-shipping-text">{{ $rupiah($initialShipping) }}</strong></div>
                            <div class="summary-total">
                                <span class="total-label">Total bayar</span>
                                <span class="total-price js-total-text" data-subtotal="{{ $subtotal }}" data-shipping="{{ $initialShipping }}">{{ $rupiah($subtotal + $initialShipping) }}</span>
                            </div>

                            <button class="btn btn-brand w-100 py-3 mt-4" type="submit"><i class="bi bi-check2-circle me-2"></i> Buat Pesanan</button>
                            <div class="summary-safe">
                                <i class="bi bi-shield-check"></i>
                                <span>Pesanan baru tercatat setelah tombol ini ditekan. Stok akan otomatis menyesuaikan setelah checkout berhasil.</span>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>

        <div class="checkout-mobile-bar">
            <div class="d-flex align-items-center justify-content-between gap-3">
                <div>
                    <div class="small text-muted fw-bold">Total bayar</div>
                    <div class="fw-black text-brand js-total-text-mobile">{{ $rupiah($subtotal + $initialShipping) }}</div>
                </div>
                <button class="btn btn-brand px-4 py-2" type="submit">Buat Pesanan</button>
            </div>
        </div>
    </form>

    @if($alamatPembeli->count())
        <div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content" style="border:0;border-radius:22px;overflow:hidden;">
                    <div class="modal-header px-4 py-3">
                        <h5 class="modal-title fw-black" id="addressModalLabel">Alamat Saya</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body px-4 py-0">
                        <div class="address-modal-list">
                            @foreach($alamatPembeli as $alamat)
                                <label class="address-choice" data-address-id="{{ $alamat->id }}" data-address-name="{{ $alamat->nama_penerima }}" data-address-phone="{{ $alamat->telepon }}" data-address-email="{{ $alamat->email_penerima ?: $user->email }}" data-address-text="{{ $alamat->alamat_lengkap }}" data-address-main="{{ $alamat->utama ? '1' : '0' }}" data-address-lat="{{ $alamat->latitude }}" data-address-lng="{{ $alamat->longitude }}">
                                    <input type="radio" class="form-check-input js-address-choice" name="alamat_modal" value="{{ $alamat->id }}" {{ (int) old('alamat_id', $alamatUtama?->id) === (int) $alamat->id ? 'checked' : '' }}>
                                    <div class="min-w-0">
                                        <div>
                                            <span class="address-choice-name">{{ $alamat->nama_penerima }}</span>
                                            <span class="selected-address-separator">|</span>
                                            <span class="address-choice-phone">{{ $alamat->telepon }}</span>
                                        </div>
                                        <div class="small text-muted fw-semibold">{{ $alamat->email_penerima ?: $user->email }}</div>
                                        <div class="address-choice-address">{{ $alamat->alamat_lengkap }}</div>
                                        @if($alamat->utama)
                                            <span class="address-main-badge">Utama</span>
                                        @endif
                                    </div>
                                    <a href="{{ route('pembeli-web.alamat.edit', ['alamat' => $alamat, 'redirect' => 'checkout']) }}" class="btn btn-link fw-bold text-decoration-none px-0">Ubah</a>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer px-4 py-3 justify-content-between">
                        <a href="{{ route('pembeli-web.alamat.create', ['redirect' => 'checkout']) }}" class="btn btn-plain"><i class="bi bi-plus-circle me-1"></i> Tambah Alamat</a>
                        <button type="button" class="btn btn-brand px-4" data-bs-dismiss="modal">Pakai Alamat</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const page = document.querySelector('.checkout-page');
        const pickupNote = document.querySelector('.js-pickup-note');
        const deliveryNote = document.querySelector('.js-delivery-note');
        const shippingText = document.querySelector('.js-shipping-text');
        const distanceText = document.querySelector('.js-distance-text');
        const summaryDistance = document.querySelector('.js-summary-distance');
        const deliveryFeeLabel = document.querySelector('.js-delivery-fee-label');
        const radiusWarning = document.querySelector('.js-radius-warning');
        const totalText = document.querySelector('.js-total-text');
        const totalTextMobile = document.querySelector('.js-total-text-mobile');
        const rupiah = value => 'Rp ' + Number(value || 0).toLocaleString('id-ID');
        const selectedAddressId = document.getElementById('selectedAddressId');
        const selectedName = document.querySelector('.js-address-name');
        const selectedPhone = document.querySelector('.js-address-phone');
        const selectedEmail = document.querySelector('.js-address-email');
        const selectedText = document.querySelector('.js-address-text');
        const selectedMain = document.querySelector('.js-address-main');
        const transferPanel = document.querySelector('.js-transfer-bank-panel');
        const codPanel = document.querySelector('.js-cod-payment-panel');

        let selectedAddressLat = Number(document.querySelector('.js-address-choice:checked')?.closest('.address-choice')?.dataset.addressLat || 0);
        let selectedAddressLng = Number(document.querySelector('.js-address-choice:checked')?.closest('.address-choice')?.dataset.addressLng || 0);

        document.querySelectorAll('.js-address-choice').forEach(function (radio) {
            radio.addEventListener('change', function () {
                const wrapper = radio.closest('.address-choice');
                if (!wrapper || !selectedAddressId) return;

                selectedAddressId.value = wrapper.dataset.addressId || '';
                selectedAddressLat = Number(wrapper.dataset.addressLat || 0);
                selectedAddressLng = Number(wrapper.dataset.addressLng || 0);
                if (selectedName) selectedName.textContent = wrapper.dataset.addressName || '';
                if (selectedPhone) selectedPhone.textContent = wrapper.dataset.addressPhone || '';
                if (selectedEmail) selectedEmail.textContent = wrapper.dataset.addressEmail || '';
                if (selectedText) selectedText.textContent = wrapper.dataset.addressText || '';
                if (selectedMain) selectedMain.style.display = wrapper.dataset.addressMain === '1' ? 'inline-flex' : 'none';
                updateShipping();
            });
        });

        function updatePaymentPanel() {
            const checkedPayment = document.querySelector('.js-payment-choice:checked');
            const isTransfer = checkedPayment?.value === 'transfer_bank';

            if (transferPanel) transferPanel.classList.toggle('is-hidden', !isTransfer);
            if (codPanel) codPanel.classList.toggle('is-hidden', isTransfer);
        }

        document.querySelectorAll('.js-payment-choice').forEach(el => el.addEventListener('change', updatePaymentPanel));
        updatePaymentPanel();

        document.querySelectorAll('.js-copy-bank').forEach(function (button) {
            button.addEventListener('click', async function () {
                const value = button.dataset.copy || '';
                if (!value || value.includes('belum diatur')) return;

                try {
                    await navigator.clipboard.writeText(value);
                    const oldText = button.innerHTML;
                    button.innerHTML = '<i class="bi bi-check2 me-1"></i> Tersalin';
                    setTimeout(() => button.innerHTML = oldText, 1400);
                } catch (e) {
                    const input = document.createElement('input');
                    input.value = value;
                    document.body.appendChild(input);
                    input.select();
                    document.execCommand('copy');
                    input.remove();
                }
            });
        });

        function haversineKm(lat1, lng1, lat2, lng2) {
            const toRad = deg => deg * Math.PI / 180;
            const earth = 6371;
            const dLat = toRad(lat2 - lat1);
            const dLng = toRad(lng2 - lng1);
            const a = Math.sin(dLat / 2) ** 2 + Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLng / 2) ** 2;
            return earth * (2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a)));
        }

        function deliveryFee() {
            const storeLat = Number(page?.dataset.storeLat || 0);
            const storeLng = Number(page?.dataset.storeLng || 0);
            const rate = Number(page?.dataset.ratePerKm || 0);
            const minFee = Number(page?.dataset.minShipping || 0);
            const maxRadius = Number(page?.dataset.maxRadius || 0);
            if (!storeLat || !storeLng || !selectedAddressLat || !selectedAddressLng) {
                return { fee: minFee, distance: null };
            }
            const distance = haversineKm(storeLat, storeLng, selectedAddressLat, selectedAddressLng);
            const fee = Math.max(minFee, Math.ceil((distance * rate) / 100) * 100);
            return { fee, distance, outsideRadius: maxRadius > 0 && distance > maxRadius };
        }

        function updateShipping() {
            const checked = document.querySelector('.js-shipping-choice:checked');
            const subtotal = Number(totalText?.dataset.subtotal || 0);
            const isDelivery = checked?.value === 'kurir_toko';
            const quote = isDelivery ? deliveryFee() : { fee: 0, distance: null };
            const shipping = quote.fee;
            const distance = quote.distance;

            if (pickupNote) pickupNote.style.display = isDelivery ? 'none' : 'block';
            if (deliveryNote) deliveryNote.classList.toggle('d-none', !isDelivery);
            if (deliveryFeeLabel) deliveryFeeLabel.textContent = rupiah(shipping);
            if (shippingText) shippingText.textContent = rupiah(shipping);
            if (distanceText) distanceText.textContent = distance ? distance.toFixed(2).replace('.', ',') + ' km' : 'pilih alamat bertitik maps';
            if (summaryDistance) summaryDistance.textContent = isDelivery && distance ? distance.toFixed(2).replace('.', ',') + ' km' : '-';
            if (radiusWarning) radiusWarning.style.setProperty('display', isDelivery && quote.outsideRadius ? 'block' : 'none', 'important');
            if (totalText) {
                totalText.dataset.shipping = shipping;
                totalText.textContent = rupiah(subtotal + shipping);
            }
            if (totalTextMobile) totalTextMobile.textContent = rupiah(subtotal + shipping);
        }

        document.querySelectorAll('.js-shipping-choice').forEach(el => el.addEventListener('change', updateShipping));
        updateShipping();
    });
</script>
@endpush
