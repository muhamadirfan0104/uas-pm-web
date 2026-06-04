@extends('layouts.pembeli')

@section('title', 'Profil Toko - SiTahu')

@push('styles')
<style>
    .profile-hero {
        padding: 32px;
        margin-bottom: 22px;
        display: grid;
        grid-template-columns: 1fr 330px;
        gap: 24px;
        align-items: center;
        background:
            radial-gradient(circle at top right, rgba(223, 186, 104, 0.26), transparent 34%),
            linear-gradient(135deg, #ffffff 0%, #fff8e8 100%);
    }

    .profile-hero h1 {
        margin: 14px 0 0;
        color: var(--heading);
        font-size: clamp(32px, 4.8vw, 54px);
        line-height: 1;
        letter-spacing: -0.075em;
    }

    .profile-hero h1 span {
        color: var(--brand-text);
    }

    .profile-hero p {
        margin: 14px 0 0;
        max-width: 680px;
        color: var(--muted);
        line-height: 1.75;
        font-size: 15px;
    }

    .hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 22px;
    }

    .profile-logo-card {
        padding: 22px;
        text-align: center;
        border-radius: 22px;
        background: rgba(255, 255, 255, 0.76);
        border: 1px solid var(--line);
    }

    .profile-logo {
        width: 92px;
        height: 92px;
        margin: 0 auto 14px;
        border-radius: 26px;
        display: grid;
        place-items: center;
        overflow: hidden;
        background: linear-gradient(135deg, var(--brand-color), #c89335);
        color: #ffffff;
        font-size: 34px;
        font-weight: 950;
        box-shadow: 0 14px 28px rgba(223, 186, 104, 0.28);
    }

    .profile-logo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-logo-card strong {
        display: block;
        color: var(--heading);
        font-size: 20px;
        letter-spacing: -0.04em;
    }

    .profile-logo-card span {
        display: block;
        margin-top: 4px;
        color: var(--muted);
        font-size: 13px;
    }

    .profile-layout {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 20px;
        align-items: start;
    }

    .content-stack {
        display: grid;
        gap: 20px;
    }

    .panel-card {
        padding: 24px;
    }

    .panel-card h2 {
        margin: 0 0 12px;
        color: var(--heading);
        font-size: 24px;
        letter-spacing: -0.05em;
    }

    .panel-card p {
        margin: 0;
        color: var(--muted);
        line-height: 1.75;
        font-size: 14px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
        margin-top: 14px;
    }

    .info-card {
        padding: 16px;
        border-radius: 17px;
        background: #f9fafb;
        border: 1px solid var(--line);
    }

    .info-icon {
        width: 38px;
        height: 38px;
        border-radius: 14px;
        display: grid;
        place-items: center;
        margin-bottom: 10px;
        background: var(--brand-soft);
        color: var(--brand-text);
        font-size: 18px;
    }

    .info-card strong {
        display: block;
        color: var(--heading);
        font-size: 14px;
        margin-bottom: 5px;
    }

    .info-card span {
        display: block;
        color: var(--muted);
        font-size: 13px;
        line-height: 1.55;
    }

    .step-list {
        display: grid;
        gap: 12px;
        margin-top: 14px;
    }

    .step-item {
        display: grid;
        grid-template-columns: 42px 1fr;
        gap: 12px;
        align-items: start;
        padding: 15px;
        border-radius: 17px;
        background: #f9fafb;
        border: 1px solid var(--line);
    }

    .step-number {
        width: 42px;
        height: 42px;
        display: grid;
        place-items: center;
        border-radius: 15px;
        background: var(--brand-soft);
        color: var(--brand-text);
        font-weight: 950;
    }

    .step-item strong {
        display: block;
        color: var(--heading);
        font-size: 14px;
        margin-bottom: 4px;
    }

    .step-item span {
        display: block;
        color: var(--muted);
        font-size: 13px;
        line-height: 1.55;
    }

    .side-card {
        position: sticky;
        top: 96px;
        padding: 22px;
    }

    .side-card h2 {
        margin: 0 0 14px;
        color: var(--heading);
        font-size: 22px;
        letter-spacing: -0.045em;
    }

    .contact-list {
        display: grid;
        gap: 11px;
    }

    .contact-item {
        padding: 14px;
        border-radius: 16px;
        background: #f9fafb;
        border: 1px solid var(--line);
    }

    .contact-item span {
        display: block;
        color: var(--muted);
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .contact-item strong {
        display: block;
        color: var(--heading);
        font-size: 14px;
        line-height: 1.5;
        word-break: break-word;
    }

    .side-actions {
        display: grid;
        gap: 10px;
        margin-top: 16px;
    }

    .notice-box {
        margin-top: 16px;
        padding: 14px;
        border-radius: 16px;
        background: var(--brand-soft);
        border: 1px solid rgba(223, 186, 104, 0.38);
        color: var(--brand-text);
        font-size: 13px;
        line-height: 1.6;
        font-weight: 700;
    }

    .faq-list {
        display: grid;
        gap: 12px;
        margin-top: 14px;
    }

    .faq-item {
        padding: 15px;
        border-radius: 17px;
        background: #f9fafb;
        border: 1px solid var(--line);
    }

    .faq-item strong {
        display: block;
        color: var(--heading);
        font-size: 14px;
        margin-bottom: 5px;
    }

    .faq-item span {
        display: block;
        color: var(--muted);
        font-size: 13px;
        line-height: 1.6;
    }

    @media (max-width: 980px) {
        .profile-hero,
        .profile-layout {
            grid-template-columns: 1fr;
        }

        .side-card {
            position: static;
        }
    }

    @media (max-width: 650px) {
        .profile-hero,
        .panel-card,
        .side-card {
            padding: 20px;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .hero-actions {
            flex-direction: column;
        }

        .hero-actions .btn,
        .side-actions .btn {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
@php
    $namaToko = $pengaturan->nama ?: 'SiTahu';
    $tentangToko = $pengaturan->tentang ?: 'Toko tahu rumahan yang menyediakan berbagai pilihan tahu segar untuk kebutuhan harian.';
    $alamatToko = $pengaturan->alamat ?: 'Alamat toko belum diatur.';
    $teleponToko = $pengaturan->telepon ?: 'Nomor WhatsApp belum tersedia.';
    $emailToko = $pengaturan->email ?: 'Email toko belum tersedia.';
    $areaPengiriman = $pengaturan->area_pengiriman ?: 'Area pengiriman akan diinformasikan saat pemesanan.';
    $infoPembayaran = $pengaturan->info_pembayaran ?: 'Pembayaran dapat dilakukan melalui QRIS atau tunai.';

    $jamBuka = $pengaturan->jam_buka ?: null;
    $jamTutup = $pengaturan->jam_tutup ?: null;
    $jamOperasional = $jamBuka && $jamTutup ? $jamBuka . ' - ' . $jamTutup : ($jamBuka ?: 'Jam operasional akan segera diperbarui.');

    $nomorWa = preg_replace('/[^0-9]/', '', $pengaturan->telepon ?: '');

    if ($nomorWa && str_starts_with($nomorWa, '0')) {
        $nomorWa = '62' . substr($nomorWa, 1);
    }

    $pesanWa = 'Halo ' . $namaToko . ', saya ingin bertanya tentang produk tahu.';
    $linkWa = $nomorWa ? 'https://wa.me/' . $nomorWa . '?text=' . urlencode($pesanWa) : route('pembeli-web.coming-soon');
@endphp

<section class="page-card profile-hero">
    <div>
        <div class="badge">Profil Toko</div>

        <h1>
            Kenalan dulu dengan <span>{{ $namaToko }}</span>
        </h1>

        <p>
            {{ $tentangToko }}
            Kami menyediakan pilihan tahu segar yang cocok untuk lauk keluarga, camilan harian,
            dan stok praktis di rumah.
        </p>

        <div class="hero-actions">
            <a href="{{ route('pembeli-web.produk') }}" class="btn btn-primary">
                Lihat Produk
            </a>

            <a href="{{ $linkWa }}" target="{{ $nomorWa ? '_blank' : '_self' }}" class="btn btn-whatsapp">
                Hubungi WhatsApp
            </a>

            <a href="{{ route('pembeli-web.pesanan.index') }}" class="btn btn-outline">
                Cek Pesanan
            </a>
        </div>
    </div>

    <div class="profile-logo-card">
        <div class="profile-logo">
            @if($pengaturan->logo_url)
                <img src="{{ asset('storage/' . $pengaturan->logo_url) }}" alt="{{ $namaToko }}">
            @else
                ST
            @endif
        </div>

        <strong>{{ $namaToko }}</strong>
        <span>Etalase produk tahu</span>
    </div>
</section>

<section class="profile-layout">
    <div class="content-stack">
        <div class="page-card panel-card">
            <h2>Tentang toko</h2>

            <p>
                {{ $tentangToko }}
                Setiap produk ditampilkan dengan informasi harga, stok, satuan, dan detail produk agar kamu lebih mudah memilih sebelum memesan.
            </p>

            <div class="info-grid">
                <div class="info-card">
                    <div class="info-icon">🍳</div>
                    <strong>Produk tahu segar</strong>
                    <span>Cocok untuk lauk harian, camilan, atau olahan keluarga.</span>
                </div>

                <div class="info-card">
                    <div class="info-icon">🛒</div>
                    <strong>Pesan dari browser</strong>
                    <span>Pilih produk, masukkan ke keranjang, lalu lanjut checkout.</span>
                </div>

                <div class="info-card">
                    <div class="info-icon">📦</div>
                    <strong>Ambil atau antar</strong>
                    <span>Pilih ambil di toko atau kurir toko saat checkout.</span>
                </div>

                <div class="info-card">
                    <div class="info-icon">💳</div>
                    <strong>QRIS atau tunai</strong>
                    <span>Pilih metode pembayaran yang paling nyaman untukmu.</span>
                </div>
            </div>
        </div>

        <div class="page-card panel-card">
            <h2>Cara pesan</h2>

            <p>
                Belanja di web SiTahu dibuat sederhana, jadi kamu bisa pesan tanpa bingung.
            </p>

            <div class="step-list">
                <div class="step-item">
                    <div class="step-number">1</div>
                    <div>
                        <strong>Pilih produk</strong>
                        <span>Lihat daftar produk tahu dan pilih yang kamu inginkan.</span>
                    </div>
                </div>

                <div class="step-item">
                    <div class="step-number">2</div>
                    <div>
                        <strong>Masukkan ke keranjang</strong>
                        <span>Atur jumlah produk sesuai kebutuhan sebelum checkout.</span>
                    </div>
                </div>

                <div class="step-item">
                    <div class="step-number">3</div>
                    <div>
                        <strong>Isi data checkout</strong>
                        <span>Lengkapi nama, nomor WhatsApp, email, dan cara menerima pesanan.</span>
                    </div>
                </div>

                <div class="step-item">
                    <div class="step-number">4</div>
                    <div>
                        <strong>Cek pesanan</strong>
                        <span>Simpan nomor invoice untuk melihat status pesananmu.</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="page-card panel-card">
            <h2>Pertanyaan yang sering muncul</h2>

            <div class="faq-list">
                <div class="faq-item">
                    <strong>Apakah bisa ambil langsung di toko?</strong>
                    <span>Bisa. Pilih metode Ambil di Toko saat checkout.</span>
                </div>

                <div class="faq-item">
                    <strong>Apakah bisa diantar?</strong>
                    <span>Bisa, jika alamatmu masuk area pengiriman toko.</span>
                </div>

                <div class="faq-item">
                    <strong>Bagaimana cara cek pesanan?</strong>
                    <span>Masuk ke menu Pesanan, lalu cari memakai nomor invoice, email, atau nomor WhatsApp.</span>
                </div>
            </div>
        </div>
    </div>

    <aside class="page-card side-card">
        <h2>Info toko</h2>

        <div class="contact-list">
            <div class="contact-item">
                <span>Alamat</span>
                <strong>{{ $alamatToko }}</strong>
            </div>

            <div class="contact-item">
                <span>Jam operasional</span>
                <strong>{{ $jamOperasional }}</strong>
            </div>

            <div class="contact-item">
                <span>WhatsApp</span>
                <strong>{{ $teleponToko }}</strong>
            </div>

            <div class="contact-item">
                <span>Email</span>
                <strong>{{ $emailToko }}</strong>
            </div>

            <div class="contact-item">
                <span>Area pengiriman</span>
                <strong>{{ $areaPengiriman }}</strong>
            </div>

            <div class="contact-item">
                <span>Pembayaran</span>
                <strong>{{ $infoPembayaran }}</strong>
            </div>
        </div>

        <div class="side-actions">
            <a href="{{ $linkWa }}" target="{{ $nomorWa ? '_blank' : '_self' }}" class="btn btn-whatsapp">
                Tanya via WhatsApp
            </a>

            <a href="{{ route('pembeli-web.produk') }}" class="btn btn-primary">
                Pilih Produk
            </a>
        </div>

        <div class="notice-box">
            Untuk pemesanan, pastikan nomor WhatsApp aktif agar toko mudah menghubungi kamu jika diperlukan.
        </div>
    </aside>
</section>
@endsection
