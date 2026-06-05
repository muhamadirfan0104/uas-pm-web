@extends('layouts.pembeli')

@section('title', 'Checkout - SiTahu')

@push('styles')
<style>
    .checkout-hero {
        padding: 28px;
        margin-bottom: 22px;
        background:
            radial-gradient(circle at top right, rgba(223, 186, 104, 0.25), transparent 32%),
            linear-gradient(135deg, #ffffff 0%, #fff8e8 100%);
    }

    .checkout-hero h1 {
        margin: 12px 0 0;
        color: var(--heading);
        font-size: clamp(30px, 4.5vw, 48px);
        line-height: 1;
        letter-spacing: -0.075em;
    }

    .checkout-hero h1 span {
        color: var(--brand-dark);
    }

    .checkout-hero p {
        margin: 12px 0 0;
        max-width: 720px;
        color: var(--muted);
        line-height: 1.7;
        font-size: 15px;
    }

    .alert {
        margin-bottom: 16px;
        padding: 13px 15px;
        border-radius: 14px;
        font-size: 14px;
        font-weight: 700;
        border: 1px solid transparent;
    }

    .alert-error {
        background: #fef2f2;
        color: #b91c1c;
        border-color: #fecaca;
    }

    .checkout-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 380px;
        gap: 20px;
        align-items: start;
    }

    .form-card,
    .summary-card {
        padding: 22px;
    }

    .form-section {
        padding-bottom: 20px;
        margin-bottom: 20px;
        border-bottom: 1px solid var(--line);
    }

    .form-section:last-child {
        padding-bottom: 0;
        margin-bottom: 0;
        border-bottom: 0;
    }

    .form-section h2,
    .summary-card h2 {
        margin: 0 0 14px;
        color: var(--heading);
        font-size: 22px;
        letter-spacing: -0.045em;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .form-grid.full {
        grid-template-columns: 1fr;
    }

    .form-group label {
        display: block;
        margin-bottom: 7px;
        color: var(--heading);
        font-size: 13px;
        font-weight: 800;
    }

    .form-control {
        width: 100%;
        min-height: 44px;
        border: 1px solid var(--line);
        border-radius: 12px;
        background: #ffffff;
        color: var(--text);
        padding: 10px 13px;
        outline: none;
        transition: 0.16s ease;
    }

    textarea.form-control {
        min-height: 96px;
        resize: vertical;
    }

    .form-control:focus {
        border-color: rgba(223, 186, 104, 0.95);
        box-shadow: 0 0 0 4px rgba(223, 186, 104, 0.16);
    }

    .option-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .option-card {
        position: relative;
        display: block;
        padding: 15px;
        border-radius: 16px;
        border: 1px solid var(--line);
        background: #f9fafb;
        cursor: pointer;
        transition: 0.16s ease;
    }

    .option-card:hover {
        border-color: rgba(223, 186, 104, 0.75);
        background: #fffdf8;
    }

    .option-card input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .option-card strong {
        display: block;
        color: var(--heading);
        font-size: 14px;
        margin-bottom: 4px;
    }

    .option-card span {
        display: block;
        color: var(--muted);
        font-size: 13px;
        line-height: 1.5;
    }

    .option-card:has(input:checked) {
        background: var(--brand-soft);
        border-color: rgba(223, 186, 104, 0.95);
        box-shadow: 0 0 0 4px rgba(223, 186, 104, 0.12);
    }

    .address-box {
        margin-top: 14px;
        padding: 16px;
        border-radius: 18px;
        background: #f9fafb;
        border: 1px solid var(--line);
    }

    .saved-address-list {
        display: grid;
        gap: 10px;
    }

    .saved-address-card {
        position: relative;
        display: block;
        padding: 14px 14px 14px 44px;
        border-radius: 16px;
        border: 1px solid var(--line);
        background: #ffffff;
        cursor: pointer;
        transition: 0.16s ease;
    }

    .saved-address-card:hover {
        border-color: rgba(223, 186, 104, 0.72);
        box-shadow: var(--shadow-soft);
    }

    .saved-address-card input {
        position: absolute;
        left: 15px;
        top: 19px;
        width: 17px;
        height: 17px;
        accent-color: #c89335;
    }

    .saved-address-card:has(input:checked) {
        background: var(--brand-soft);
        border-color: rgba(223, 186, 104, 0.95);
        box-shadow: 0 0 0 4px rgba(223, 186, 104, 0.12);
    }

    .saved-address-card strong {
        display: block;
        color: var(--heading);
        font-size: 14px;
        font-weight: 950;
    }

    .saved-address-card span {
        display: block;
        margin-top: 5px;
        color: var(--muted);
        font-size: 12.5px;
        line-height: 1.55;
    }

    .address-mini-badge {
        display: inline-flex;
        width: fit-content;
        margin-top: 8px;
        padding: 5px 9px;
        border-radius: 999px;
        background: #ffffff;
        color: var(--brand-dark);
        border: 1px solid rgba(223, 186, 104, 0.45);
        font-size: 11.5px;
        font-weight: 900;
    }

    .empty-address-warning {
        padding: 14px;
        border-radius: 16px;
        background: #fff8e8;
        border: 1px solid rgba(223, 186, 104, 0.5);
        color: var(--brand-dark);
        font-size: 13.5px;
        line-height: 1.65;
        font-weight: 750;
    }

    .payment-list {
        display: grid;
        gap: 12px;
    }

    .payment-option {
        display: flex;
        align-items: center;
        gap: 10px;
        width: fit-content;
        cursor: pointer;
        color: var(--heading);
        font-size: 15px;
        font-weight: 800;
    }

    .payment-option input[type="radio"] {
        appearance: none;
        -webkit-appearance: none;
        width: 18px;
        height: 18px;
        margin: 0;
        border-radius: 50%;
        border: 2px solid #cbd5e1;
        background: #ffffff;
        display: grid;
        place-items: center;
        cursor: pointer;
        transition: 0.16s ease;
    }

    .payment-option input[type="radio"]::before {
        content: "";
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--brand-dark);
        transform: scale(0);
        transition: 0.16s ease;
    }

    .payment-option input[type="radio"]:checked {
        border-color: var(--brand-dark);
        background: var(--brand-soft);
    }

    .payment-option input[type="radio"]:checked::before {
        transform: scale(1);
    }

    .payment-option:hover {
        color: var(--brand-dark);
    }

    .checkbox-row {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        color: var(--muted);
        font-size: 14px;
        line-height: 1.6;
    }

    .checkbox-row input {
        margin-top: 4px;
    }

    .summary-card {
        position: sticky;
        top: 96px;
    }

    .summary-list {
        display: grid;
        gap: 12px;
        margin-top: 12px;
    }

    .summary-item {
        display: grid;
        grid-template-columns: 54px 1fr;
        gap: 10px;
        align-items: center;
    }

    .summary-img {
        width: 54px;
        height: 54px;
        border-radius: 14px;
        overflow: hidden;
        background: #f9fafb;
        border: 1px solid var(--line);
        display: grid;
        place-items: center;
        color: var(--brand-dark);
        font-size: 10px;
        font-weight: 900;
    }

    .summary-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .summary-item h3 {
        margin: 0;
        color: var(--heading);
        font-size: 14px;
        line-height: 1.35;
    }

    .summary-item p {
        margin: 3px 0 0;
        color: var(--muted);
        font-size: 12px;
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
        text-align: right;
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
        color: var(--brand-dark);
        text-align: right;
    }

    .help-box {
        margin-top: 14px;
        padding: 13px;
        border-radius: 14px;
        background: #f9fafb;
        border: 1px solid var(--line);
        color: var(--muted);
        font-size: 13px;
        line-height: 1.55;
    }

    .error-text {
        margin-top: 6px;
        color: #b91c1c;
        font-size: 12px;
        font-weight: 700;
    }

    @media (max-width: 960px) {
        .checkout-layout {
            grid-template-columns: 1fr;
        }

        .summary-card {
            position: static;
        }
    }

    @media (max-width: 650px) {
        .checkout-hero,
        .form-card,
        .summary-card {
            padding: 20px;
        }

        .form-grid,
        .option-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
@php
    $biayaKurir = (float) ($pengaturan->biaya_minimum_pengiriman ?? 0);
    $totalAmbil = $subtotal;
    $totalKurir = $subtotal + $biayaKurir;

    $oldAlamatId = old('alamat_id', $alamatUtama?->id);
@endphp

<section class="page-card checkout-hero">
    <div class="badge">Checkout Pesanan</div>

    <h1>
        Tinggal pilih alamat, <span>pesanan siap dibuat</span>
    </h1>

    <p>
        Data akun sudah otomatis terisi. Kalau memilih kurir toko, gunakan alamat yang sudah tersimpan
        dari menu Alamat Saya.
    </p>
</section>

@if(session('error'))
    <div class="alert alert-error">
        {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-error">
        Ada data yang belum sesuai. Cek lagi bagian form yang bertanda merah ya.
    </div>
@endif

<form action="{{ route('pembeli-web.checkout.store') }}" method="POST" id="checkoutForm">
    @csrf

    <section class="checkout-layout">
        <div class="page-card form-card">
            <div class="form-section">
                <h2>Data pemesan</h2>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="nama">Nama lengkap</label>
                        <input
                            type="text"
                            id="nama"
                            name="nama"
                            class="form-control"
                            value="{{ old('nama', $user?->name) }}"
                            placeholder="Masukkan nama lengkap"
                        >
                        @error('nama') <div class="error-text">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="telepon">Nomor WhatsApp</label>
                        <input
                            type="text"
                            id="telepon"
                            name="telepon"
                            class="form-control"
                            value="{{ old('telepon', $user?->telepon) }}"
                            placeholder="Contoh: 081234567890"
                        >
                        @error('telepon') <div class="error-text">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label for="email">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            value="{{ old('email', $user?->email) }}"
                            placeholder="Contoh: nama@email.com"
                        >
                        @error('email') <div class="error-text">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2>Cara menerima pesanan</h2>

                <div class="option-grid">
                    <label class="option-card">
                        <input
                            type="radio"
                            name="metode_pengambilan"
                            value="ambil_toko"
                            {{ old('metode_pengambilan', 'ambil_toko') === 'ambil_toko' ? 'checked' : '' }}
                        >
                        <strong>Ambil di toko</strong>
                        <span>Pesanan diambil langsung ke alamat toko.</span>
                    </label>

                    <label class="option-card">
                        <input
                            type="radio"
                            name="metode_pengambilan"
                            value="kurir_toko"
                            {{ old('metode_pengambilan') === 'kurir_toko' ? 'checked' : '' }}
                        >
                        <strong>Kurir toko</strong>
                        <span>Pesanan diantar oleh kurir toko sesuai alamat tujuan.</span>
                    </label>
                </div>

                <div class="address-box" id="alamatKurirBox">
                    <h2 style="font-size:18px;margin-bottom:10px;">Pilih alamat pengiriman</h2>

                    @if($alamatPembeli->count())
                        <div class="saved-address-list">
                            @foreach($alamatPembeli as $alamat)
                                <label class="saved-address-card">
                                    <input
                                        type="radio"
                                        name="alamat_id"
                                        value="{{ $alamat->id }}"
                                        {{ (string) $oldAlamatId === (string) $alamat->id ? 'checked' : '' }}
                                    >

                                    <strong>
                                        {{ $alamat->nama_penerima }}
                                        @if($alamat->utama)
                                            <span class="address-mini-badge">Utama</span>
                                        @endif
                                    </strong>

                                    <span>
                                        {{ $alamat->telepon }}<br>
                                        {{ $alamat->alamat_lengkap }}
                                    </span>

                                    @if($alamat->latitude && $alamat->longitude)
                                        <span>
                                            Koordinat: {{ $alamat->latitude }}, {{ $alamat->longitude }}
                                        </span>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-address-warning">
                            Kamu belum punya alamat tersimpan. Tambahkan alamat dulu supaya bisa memilih
                            metode kurir toko.

                            <div style="margin-top:12px;">
                                <a href="{{ route('pembeli-web.alamat.create') }}" class="btn btn-primary">
                                    Tambah Alamat
                                </a>
                            </div>
                        </div>
                    @endif

                    @error('alamat_id') <div class="error-text">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-section">
                <h2>Metode pembayaran</h2>

                <div class="payment-list">
                    <label class="payment-option">
                        <input
                            type="radio"
                            name="metode_pembayaran"
                            value="qris"
                            {{ old('metode_pembayaran', 'qris') === 'qris' ? 'checked' : '' }}
                        >
                        <span>QRIS</span>
                    </label>

                    <label class="payment-option">
                        <input
                            type="radio"
                            name="metode_pembayaran"
                            value="tunai"
                            {{ old('metode_pembayaran') === 'tunai' ? 'checked' : '' }}
                        >
                        <span>Tunai</span>
                    </label>
                </div>

                @error('metode_pembayaran') <div class="error-text">{{ $message }}</div> @enderror
            </div>

            <div class="form-section">
                <h2>Catatan pesanan</h2>

                <div class="form-grid full">
                    <div class="form-group">
                        <label for="catatan">Catatan tambahan</label>
                        <textarea
                            id="catatan"
                            name="catatan"
                            class="form-control"
                            placeholder="Contoh: hubungi dulu sebelum antar, jangan terlalu siang, dan lain-lain"
                        >{{ old('catatan') }}</textarea>
                        @error('catatan') <div class="error-text">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div style="margin-top: 14px;">
                    <label class="checkbox-row">
                        <input
                            type="checkbox"
                            name="setuju"
                            value="1"
                            {{ old('setuju') ? 'checked' : '' }}
                        >
                        <span>Saya sudah mengecek produk, jumlah pesanan, dan data pengambilan/pengantaran.</span>
                    </label>
                    @error('setuju') <div class="error-text">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <aside class="page-card summary-card">
            <h2>Ringkasan pesanan</h2>

            <div class="summary-list">
                @foreach($items as $item)
                    @php
                        $produk = $item['produk'];
                        $gambar = $produk->gambarUtama?->url_gambar;
                    @endphp

                    <div class="summary-item">
                        <div class="summary-img">
                            @if($gambar)
                                <img src="{{ asset('storage/' . $gambar) }}" alt="{{ $produk->nama }}">
                            @else
                                TAHU
                            @endif
                        </div>

                        <div>
                            <h3>{{ $produk->nama }}</h3>
                            <p>{{ $item['jumlah'] }} × Rp {{ number_format($item['harga'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 16px;">
                <div class="summary-row">
                    <span>Total item</span>
                    <strong>{{ $totalItem }} produk</strong>
                </div>

                <div class="summary-row">
                    <span>Subtotal</span>
                    <strong>Rp {{ number_format($subtotal, 0, ',', '.') }}</strong>
                </div>

                <div class="summary-row">
                    <span>Biaya pengantaran</span>
                    <strong id="biayaPengirimanText">Rp 0</strong>
                </div>

                <div class="summary-total">
                    <span>Total bayar</span>
                    <span id="totalBayarText">Rp {{ number_format($totalAmbil, 0, ',', '.') }}</span>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;" id="submitCheckoutBtn">
                Buat Pesanan
            </button>

            <a href="{{ route('pembeli-web.keranjang.index') }}" class="btn btn-outline" style="width: 100%; margin-top: 10px;">
                Kembali ke Keranjang
            </a>

            <div class="help-box">
                Untuk QRIS, informasi pembayaran muncul setelah invoice dibuat.
                Untuk tunai, pembayaran dilakukan saat pesanan diambil atau diterima.
            </div>
        </aside>
    </section>
</form>
@endsection

@push('scripts')
<script>
    const biayaKurir = {{ $biayaKurir }};
    const totalAmbil = {{ $totalAmbil }};
    const totalKurir = {{ $totalKurir }};
    const punyaAlamat = {{ $alamatPembeli->count() ? 'true' : 'false' }};

    const alamatKurirBox = document.getElementById('alamatKurirBox');
    const biayaPengirimanText = document.getElementById('biayaPengirimanText');
    const totalBayarText = document.getElementById('totalBayarText');
    const submitCheckoutBtn = document.getElementById('submitCheckoutBtn');

    function rupiah(value) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0
        }).format(value).replace(/\s/g, ' ');
    }

    function updateMetodePengambilan() {
        const selected = document.querySelector('input[name="metode_pengambilan"]:checked')?.value;

        if (selected === 'kurir_toko') {
            alamatKurirBox.style.display = 'block';
            biayaPengirimanText.textContent = rupiah(biayaKurir);
            totalBayarText.textContent = rupiah(totalKurir);

            if (!punyaAlamat) {
                submitCheckoutBtn.disabled = true;
                submitCheckoutBtn.classList.add('btn-disabled');
                submitCheckoutBtn.textContent = 'Tambahkan Alamat Dulu';
            } else {
                submitCheckoutBtn.disabled = false;
                submitCheckoutBtn.classList.remove('btn-disabled');
                submitCheckoutBtn.textContent = 'Buat Pesanan';
            }
        } else {
            alamatKurirBox.style.display = 'none';
            biayaPengirimanText.textContent = rupiah(0);
            totalBayarText.textContent = rupiah(totalAmbil);

            submitCheckoutBtn.disabled = false;
            submitCheckoutBtn.classList.remove('btn-disabled');
            submitCheckoutBtn.textContent = 'Buat Pesanan';
        }
    }

    document.querySelectorAll('input[name="metode_pengambilan"]').forEach(input => {
        input.addEventListener('change', updateMetodePengambilan);
    });

    updateMetodePengambilan();
</script>
@endpush