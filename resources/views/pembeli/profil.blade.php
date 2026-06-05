@extends('layouts.pembeli')

@section('title', 'Profil Pembeli - SiTahu')

@push('styles')
<style>
    .profile-page {
        display: grid;
        gap: 18px;
    }

    .profile-hero {
        padding: 24px;
        display: grid;
        grid-template-columns: minmax(0, 1fr) 260px;
        gap: 18px;
        align-items: center;
        background:
            radial-gradient(circle at top right, rgba(223, 186, 104, 0.24), transparent 34%),
            linear-gradient(135deg, #ffffff 0%, #fff8e8 100%);
    }

    .profile-hero h1 {
        margin: 10px 0 0;
        color: var(--heading);
        font-size: clamp(30px, 4vw, 44px);
        line-height: 1.04;
        letter-spacing: -0.07em;
    }

    .profile-hero h1 span {
        color: var(--brand-dark);
    }

    .profile-hero p {
        margin: 10px 0 0;
        max-width: 680px;
        color: var(--muted);
        font-size: 14px;
        line-height: 1.7;
    }

    .profile-avatar-card {
        padding: 18px;
        border-radius: 22px;
        background: rgba(255, 255, 255, 0.84);
        border: 1px solid rgba(223, 186, 104, 0.38);
        box-shadow: var(--shadow-soft);
        text-align: center;
    }

    .profile-avatar {
        width: 86px;
        height: 86px;
        margin: 0 auto 12px;
        border-radius: 28px;
        background: linear-gradient(135deg, var(--brand-color), #c89335);
        color: #ffffff;
        display: grid;
        place-items: center;
        font-size: 28px;
        font-weight: 950;
        letter-spacing: -0.06em;
        box-shadow: 0 16px 28px rgba(223, 186, 104, 0.28);
    }

    .profile-avatar-card strong {
        display: block;
        color: var(--heading);
        font-size: 16px;
        font-weight: 950;
        letter-spacing: -0.035em;
    }

    .profile-avatar-card span {
        display: block;
        margin-top: 4px;
        color: var(--muted);
        font-size: 12.5px;
        font-weight: 750;
    }

    .metric-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }

    .metric-card {
        padding: 17px;
        border-radius: 20px;
        background: #ffffff;
        border: 1px solid var(--line);
        box-shadow: var(--shadow-soft);
    }

    .metric-card span {
        display: block;
        color: var(--muted);
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .metric-card strong {
        display: block;
        margin-top: 9px;
        color: var(--heading);
        font-size: 24px;
        line-height: 1;
        letter-spacing: -0.06em;
        font-weight: 950;
    }

    .metric-card small {
        display: block;
        margin-top: 7px;
        color: var(--brand-dark);
        font-size: 12px;
        font-weight: 850;
    }

    .profile-grid {
        display: grid;
        grid-template-columns: minmax(0, 0.95fr) minmax(0, 1.25fr);
        gap: 18px;
        align-items: start;
    }

    .profile-card {
        padding: 22px;
    }

    .profile-card h2 {
        margin: 0;
        color: var(--heading);
        font-size: 22px;
        letter-spacing: -0.05em;
    }

    .profile-card p {
        margin: 8px 0 0;
        color: var(--muted);
        font-size: 13.5px;
        line-height: 1.65;
    }

    .info-list {
        margin-top: 18px;
        display: grid;
        gap: 10px;
    }

    .info-row {
        padding: 13px 14px;
        border-radius: 16px;
        background: #f9fafb;
        border: 1px solid var(--line);
        display: grid;
        grid-template-columns: 130px minmax(0, 1fr);
        gap: 12px;
        align-items: start;
    }

    .info-row span {
        color: var(--muted);
        font-size: 12.5px;
        font-weight: 900;
    }

    .info-row strong {
        color: var(--heading);
        font-size: 13.5px;
        font-weight: 900;
        overflow-wrap: anywhere;
    }

    .quick-actions {
        margin-top: 18px;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .quick-action {
        padding: 15px;
        border-radius: 18px;
        border: 1px solid var(--line);
        background: #ffffff;
        color: var(--heading);
        transition: 0.16s ease;
    }

    .quick-action:hover {
        transform: translateY(-2px);
        border-color: rgba(223, 186, 104, 0.56);
        box-shadow: var(--shadow-soft);
    }

    .quick-action strong {
        display: block;
        font-size: 14px;
        font-weight: 950;
    }

    .quick-action span {
        display: block;
        margin-top: 5px;
        color: var(--muted);
        font-size: 12.5px;
        line-height: 1.45;
    }

    .order-list {
        margin-top: 18px;
        display: grid;
        gap: 10px;
    }

    .order-row {
        padding: 14px;
        border-radius: 18px;
        background: #ffffff;
        border: 1px solid var(--line);
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 12px;
        align-items: center;
        transition: 0.16s ease;
    }

    .order-row:hover {
        transform: translateY(-1px);
        border-color: rgba(223, 186, 104, 0.56);
        box-shadow: var(--shadow-soft);
    }

    .order-title {
        color: var(--heading);
        font-size: 14px;
        font-weight: 950;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .order-meta {
        margin-top: 5px;
        color: var(--muted);
        font-size: 12.5px;
        font-weight: 750;
        line-height: 1.5;
    }

    .order-price {
        color: var(--brand-dark);
        font-size: 14px;
        font-weight: 950;
        text-align: right;
        white-space: nowrap;
    }

    .order-status {
        display: inline-flex;
        margin-top: 6px;
        padding: 5px 9px;
        border-radius: 999px;
        background: var(--brand-soft);
        color: var(--brand-dark);
        font-size: 11.5px;
        font-weight: 900;
    }

    .empty-orders {
        margin-top: 18px;
        padding: 26px 18px;
        border-radius: 18px;
        border: 1px dashed var(--line);
        background: #f9fafb;
        text-align: center;
        color: var(--muted);
        font-size: 13.5px;
        line-height: 1.6;
    }

    .store-info {
        margin-top: 18px;
        padding: 15px;
        border-radius: 18px;
        background: var(--brand-soft-2);
        border: 1px solid rgba(223, 186, 104, 0.38);
        color: var(--brand-dark);
        font-size: 13px;
        line-height: 1.65;
        font-weight: 750;
    }

    .profile-actions {
        margin-top: 18px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    @media (max-width: 980px) {
        .profile-hero,
        .profile-grid {
            grid-template-columns: 1fr;
        }

        .profile-avatar-card {
            text-align: left;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .profile-avatar {
            margin: 0;
            flex-shrink: 0;
        }

        .metric-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 620px) {
        .profile-card,
        .profile-hero {
            padding: 18px;
        }

        .profile-avatar-card {
            display: none;
        }

        .metric-grid,
        .quick-actions {
            grid-template-columns: 1fr;
        }

        .info-row {
            grid-template-columns: 1fr;
            gap: 5px;
        }

        .order-row {
            grid-template-columns: 1fr;
        }

        .order-price {
            text-align: left;
        }

        .profile-actions .btn {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
@php
    $namaUser = $user->name ?? 'Pembeli';
    $initial = strtoupper(substr($namaUser, 0, 2));

    $formatStatus = function ($value) {
        return ucwords(str_replace('_', ' ', (string) $value));
    };
@endphp

<div class="profile-page">
    <section class="page-card profile-hero">
        <div>
            <div class="badge">Profil Pembeli</div>

            <h1>
                Halo, <span>{{ $namaUser }}</span>
            </h1>

            <p>
                Ini adalah halaman akun pembeli. Dari sini kamu bisa melihat ringkasan pesanan,
                membuka keranjang, mengatur alamat pengiriman, dan cek status pesanan.
            </p>

            <div class="profile-actions">
                <a href="{{ route('pembeli-web.pesanan.index') }}" class="btn btn-primary">
                    Lihat Pesanan
                </a>

                <a href="{{ route('pembeli-web.alamat.index') }}" class="btn btn-outline">
                    Kelola Alamat
                </a>

                <a href="{{ route('pembeli-web.keranjang.index') }}" class="btn btn-outline">
                    Buka Keranjang
                </a>
            </div>
        </div>

        <div class="profile-avatar-card">
            <div class="profile-avatar">{{ $initial }}</div>
            <div>
                <strong>{{ $namaUser }}</strong>
                <span>{{ $user->email }}</span>
            </div>
        </div>
    </section>

    <section class="metric-grid">
        <div class="metric-card">
            <span>Total Pesanan</span>
            <strong>{{ $statProfil['total_pesanan'] ?? 0 }}</strong>
            <small>Semua pesanan</small>
        </div>

        <div class="metric-card">
            <span>Pesanan Aktif</span>
            <strong>{{ $statProfil['pesanan_aktif'] ?? 0 }}</strong>
            <small>Belum selesai</small>
        </div>

        <div class="metric-card">
            <span>Selesai</span>
            <strong>{{ $statProfil['pesanan_selesai'] ?? 0 }}</strong>
            <small>Sudah diterima</small>
        </div>

        <div class="metric-card">
            <span>Total Belanja</span>
            <strong style="font-size:18px;">
                Rp {{ number_format((float) ($statProfil['total_belanja'] ?? 0), 0, ',', '.') }}
            </strong>
            <small>Pembayaran dibayar</small>
        </div>
    </section>

    <div class="profile-grid">
        <section class="page-card profile-card">
            <h2>Data Akun</h2>

            <p>
                Data ini digunakan untuk identitas pembeli pada proses checkout dan riwayat pesanan.
            </p>

            <div class="info-list">
                <div class="info-row">
                    <span>Nama</span>
                    <strong>{{ $namaUser }}</strong>
                </div>

                <div class="info-row">
                    <span>Email</span>
                    <strong>{{ $user->email }}</strong>
                </div>

                <div class="info-row">
                    <span>Telepon</span>
                    <strong>{{ $user->telepon ?: 'Belum diisi' }}</strong>
                </div>

                <div class="info-row">
                    <span>Status Akun</span>
                    <strong>{{ $user->aktif ? 'Aktif' : 'Nonaktif' }}</strong>
                </div>
            </div>

            <div class="quick-actions">
                <a href="{{ route('pembeli-web.pesanan.index') }}" class="quick-action">
                    <strong>Pesanan Saya</strong>
                    <span>Lihat status pesanan, pembayaran, dan pengiriman.</span>
                </a>

                <a href="{{ route('pembeli-web.alamat.index') }}" class="quick-action">
                    <strong>Alamat Saya</strong>
                    <span>Kelola alamat pengiriman dan pilih alamat utama.</span>
                </a>

                <a href="{{ route('pembeli-web.keranjang.index') }}" class="quick-action">
                    <strong>Keranjang</strong>
                    <span>Cek produk yang akan kamu checkout.</span>
                </a>

                <a href="{{ route('pembeli-web.produk') }}" class="quick-action">
                    <strong>Belanja Lagi</strong>
                    <span>Lihat daftar produk tahu yang tersedia.</span>
                </a>
            </div>

            <div class="store-info">
                <strong>Info toko:</strong><br>
                {{ $pengaturan->nama ?? 'SiTahu' }}

                @if($pengaturan->jam_buka)
                    buka {{ $pengaturan->jam_buka }}.
                @endif

                @if($pengaturan->telepon)
                    Kontak toko: {{ $pengaturan->telepon }}.
                @endif
            </div>

            <form action="{{ route('pembeli-web.logout') }}" method="POST" style="margin-top:16px;">
                @csrf

                <button type="submit" class="btn btn-outline">
                    Logout Akun Pembeli
                </button>
            </form>
        </section>

        <section class="page-card profile-card">
            <h2>Pesanan Terbaru</h2>

            <p>
                Ringkasan pesanan terakhir yang kamu buat lewat web pembeli SiTahu.
            </p>

            @if($pesananTerbaru->count())
                <div class="order-list">
                    @foreach($pesananTerbaru as $pesanan)
                        <a href="{{ route('pembeli-web.pesanan.show', $pesanan->nomor_invoice) }}" class="order-row">
                            <div class="min-w-0">
                                <div class="order-title">
                                    {{ $pesanan->nomor_invoice }}
                                </div>

                                <div class="order-meta">
                                    {{ optional($pesanan->tanggal_pesanan)->format('d/m/Y H:i') ?? '-' }}
                                    · {{ $pesanan->item->count() }} produk
                                    · {{ $formatStatus($pesanan->metode_pengambilan) }}
                                </div>

                                <span class="order-status">
                                    {{ $formatStatus($pesanan->status) }}
                                </span>
                            </div>

                            <div class="order-price">
                                Rp {{ number_format((float) $pesanan->total_bayar, 0, ',', '.') }}
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="profile-actions">
                    <a href="{{ route('pembeli-web.pesanan.index') }}" class="btn btn-primary">
                        Lihat Semua Pesanan
                    </a>
                </div>
            @else
                <div class="empty-orders">
                    Kamu belum punya pesanan. Yuk lihat produk tahu yang tersedia dulu.

                    <div style="margin-top:14px;">
                        <a href="{{ route('pembeli-web.produk') }}" class="btn btn-primary">
                            Lihat Produk
                        </a>
                    </div>
                </div>
            @endif
        </section>
    </div>
</div>
@endsection