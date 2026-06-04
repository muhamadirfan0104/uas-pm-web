@extends('layouts.pembeli')

@section('title', 'Pesanan Berhasil - SiTahu')

@push('styles')
<style>
    .success-hero {
        padding: 34px;
        text-align: center;
        background:
            radial-gradient(circle at top right, rgba(34, 197, 94, 0.16), transparent 30%),
            linear-gradient(135deg, #ffffff 0%, #fff8e8 100%);
    }

    .success-icon {
        width: 78px;
        height: 78px;
        margin: 0 auto 18px;
        display: grid;
        place-items: center;
        border-radius: 24px;
        background: #ecfdf5;
        color: #15803d;
        font-size: 38px;
        border: 1px solid #bbf7d0;
    }

    .success-hero h1 {
        margin: 0;
        color: var(--heading);
        font-size: clamp(32px, 4.8vw, 52px);
        line-height: 1;
        letter-spacing: -0.075em;
    }

    .success-hero h1 span {
        color: var(--brand-text);
    }

    .success-hero p {
        margin: 14px auto 0;
        max-width: 650px;
        color: var(--muted);
        line-height: 1.75;
        font-size: 15px;
    }

    .success-layout {
        margin-top: 22px;
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 20px;
        align-items: start;
    }

    .info-card,
    .summary-card {
        padding: 22px;
    }

    .info-card h2,
    .summary-card h2 {
        margin: 0 0 14px;
        color: var(--heading);
        font-size: 22px;
        letter-spacing: -0.045em;
    }

    .info-grid {
        display: grid;
        gap: 12px;
    }

    .info-row {
        padding: 14px;
        border-radius: 16px;
        background: #f9fafb;
        border: 1px solid var(--line);
    }

    .info-row span {
        display: block;
        color: var(--muted);
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .info-row strong {
        display: block;
        color: var(--heading);
        font-size: 15px;
        line-height: 1.5;
    }

    .summary-list {
        display: grid;
        gap: 12px;
        margin-top: 12px;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--line);
        color: var(--muted);
        font-size: 14px;
    }

    .summary-item strong {
        color: var(--heading);
    }

    .summary-total {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding-top: 16px;
        color: var(--heading);
        font-weight: 900;
        font-size: 18px;
    }

    .summary-total span:last-child {
        color: var(--brand-text);
    }

    .payment-box {
        margin-top: 18px;
        padding: 16px;
        border-radius: 18px;
        background: var(--brand-soft);
        border: 1px solid rgba(223, 186, 104, 0.35);
    }

    .payment-box span {
        display: block;
        color: var(--muted);
        font-size: 13px;
        margin-bottom: 4px;
    }

    .payment-box strong {
        display: block;
        color: var(--brand-text);
        font-size: 18px;
        letter-spacing: -0.03em;
    }

    .action-row {
        margin-top: 22px;
        display: flex;
        justify-content: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    @media (max-width: 900px) {
        .success-layout {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 650px) {
        .success-hero,
        .info-card,
        .summary-card {
            padding: 20px;
        }
    }
</style>
@endpush

@section('content')
<section class="page-card success-hero">
    <div class="success-icon">✓</div>

    <h1>
        Pesanan <span>berhasil dibuat</span>
    </h1>

    <p>
        Terima kasih sudah memesan. Simpan nomor invoice ini untuk mengecek pesanan,
        lalu selesaikan pembayaran sesuai metode yang dipilih.
    </p>

    <div class="action-row">
        <a href="{{ route('pembeli-web.pesanan.show', $pesanan->nomor_invoice) }}" class="btn btn-primary">
            Lihat Detail Pesanan
        </a>

        <a href="{{ route('pembeli-web.produk') }}" class="btn btn-outline">
            Belanja Lagi
        </a>

        <a href="{{ route('pembeli-web.home') }}" class="btn btn-outline">
            Kembali ke Beranda
        </a>
    </div>
</section>

<section class="success-layout">
    <div class="page-card info-card">
        <h2>Detail pesanan</h2>

        <div class="info-grid">
            <div class="info-row">
                <span>Nomor invoice</span>
                <strong>{{ $pesanan->nomor_invoice }}</strong>
            </div>

            <div class="info-row">
                <span>Nama pemesan</span>
                <strong>{{ $pesanan->user?->name ?? 'Pembeli SiTahu' }}</strong>
            </div>

            <div class="info-row">
                <span>Metode penerimaan</span>
                <strong>{{ $pesanan->metode_pengambilan === 'kurir_toko' ? 'Kurir toko' : 'Ambil di toko' }}</strong>
            </div>

            @if($pesanan->alamatPengiriman)
                <div class="info-row">
                    <span>Alamat tujuan</span>
                    <strong>{{ $pesanan->alamatPengiriman->alamat_lengkap }}</strong>
                </div>
            @elseif($pesanan->pengiriman?->alamat_toko)
                <div class="info-row">
                    <span>Alamat toko</span>
                    <strong>{{ $pesanan->pengiriman->alamat_toko }}</strong>
                </div>
            @endif

            <div class="info-row">
                <span>Status pesanan</span>
                <strong>{{ str_replace('_', ' ', ucfirst($pesanan->status)) }}</strong>
            </div>

            <div class="info-row">
                <span>Status pembayaran</span>
                <strong>{{ str_replace('_', ' ', ucfirst($pesanan->status_pembayaran)) }}</strong>
            </div>
        </div>

        @if($pesanan->pembayaran)
            <div class="payment-box">
                <span>Metode pembayaran</span>
                <strong>{{ $pesanan->pembayaran->metode_pembayaran === 'tunai' ? 'Tunai' : 'QRIS' }}</strong>

                <span style="margin-top: 12px;">Referensi pembayaran</span>
                <strong>{{ $pesanan->pembayaran->referensi_pembayaran }}</strong>

                @if($pesanan->pembayaran->metode_pembayaran === 'qris' && $pesanan->pembayaran->qr_code)
                    <span style="margin-top: 12px;">Kode QR / invoice</span>
                    <strong>{{ $pesanan->pembayaran->qr_code }}</strong>
                @endif

                @if($pesanan->pembayaran->metode_pembayaran === 'tunai')
                    <span style="margin-top: 12px;">Catatan</span>
                    <strong>Pembayaran dilakukan saat pesanan diambil atau diterima.</strong>
                @endif
            </div>
        @endif
    </div>

    <aside class="page-card summary-card">
        <h2>Ringkasan produk</h2>

        <div class="summary-list">
            @foreach($pesanan->item as $item)
                <div class="summary-item">
                    <span>
                        {{ $item->produk?->nama ?? 'Produk' }}
                        <br>
                        <small>{{ $item->jumlah }} × Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</small>
                    </span>

                    <strong>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong>
                </div>
            @endforeach
        </div>

        <div style="margin-top: 14px;">
            <div class="summary-item">
                <span>Subtotal</span>
                <strong>Rp {{ number_format($pesanan->subtotal_produk, 0, ',', '.') }}</strong>
            </div>

            <div class="summary-item">
                <span>Biaya pengantaran</span>
                <strong>Rp {{ number_format($pesanan->biaya_pengiriman, 0, ',', '.') }}</strong>
            </div>

            <div class="summary-total">
                <span>Total bayar</span>
                <span>Rp {{ number_format($pesanan->total_bayar, 0, ',', '.') }}</span>
            </div>
        </div>
    </aside>
</section>
@endsection
