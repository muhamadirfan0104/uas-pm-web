@extends('layouts.admin')

@section('title', 'Pengaturan Toko - SiTahu')
@section('page_title', 'Pengaturan Toko')

@section('content')
<style>
    .setting-hero {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 260px;
        gap: 18px;
        align-items: center;
        margin-bottom: 20px;
        padding: 24px;
        border-radius: 24px;
        border: 1px solid var(--border);
        background:
            radial-gradient(circle at top right, rgba(223, 186, 104, .24), transparent 35%),
            linear-gradient(135deg, #ffffff, #fff8e8);
        box-shadow: var(--shadow-soft);
    }

    .setting-hero h1 {
        margin: 0;
        color: var(--text);
        font-size: clamp(1.55rem, 3vw, 2.25rem);
        font-weight: 950;
        letter-spacing: -.065em;
        line-height: 1.05;
    }

    .setting-hero p {
        max-width: 760px;
        margin: 10px 0 0;
        color: var(--muted);
        font-size: .93rem;
        font-weight: 650;
        line-height: 1.6;
    }

    .setting-logo-preview {
        padding: 16px;
        border-radius: 22px;
        border: 1px solid rgba(223, 186, 104, .34);
        background: rgba(255,255,255,.82);
        text-align: center;
        box-shadow: var(--shadow-soft);
    }

    .logo-box {
        width: 82px;
        height: 82px;
        margin: 0 auto 10px;
        border-radius: 24px;
        display: grid;
        place-items: center;
        overflow: hidden;
        background: linear-gradient(135deg, var(--brand), #c89335);
        color: #fff;
        font-size: 1.55rem;
        font-weight: 950;
        letter-spacing: -.06em;
    }

    .logo-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .setting-box {
        border: 1px solid var(--border);
        border-radius: 22px;
        background: #fff;
        box-shadow: var(--shadow-soft);
        overflow: hidden;
        margin-bottom: 18px;
    }

    .setting-box-head {
        padding: 18px;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: flex-start;
    }

    .setting-box-head h2 {
        margin: 0;
        color: var(--text);
        font-size: 1.04rem;
        font-weight: 950;
        letter-spacing: -.035em;
    }

    .setting-box-head p {
        margin: 5px 0 0;
        color: var(--muted);
        font-size: .82rem;
        font-weight: 650;
        line-height: 1.5;
    }

    .setting-box-body {
        padding: 18px;
    }

    .form-grid-2 {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .form-grid-3 {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    .form-group {
        display: grid;
        gap: 7px;
    }

    .form-group label {
        color: var(--text);
        font-size: .82rem;
        font-weight: 900;
    }

    .form-note {
        color: var(--muted);
        font-size: .76rem;
        font-weight: 650;
        line-height: 1.45;
    }

    .form-control,
    .form-select {
        min-height: 44px;
        border-radius: 14px;
        border: 1px solid var(--border);
        background-color: #fff;
        font-size: .88rem;
        font-weight: 650;
    }

    textarea.form-control {
        min-height: 110px;
        resize: vertical;
        line-height: 1.6;
    }

    .setting-summary {
        display: grid;
        gap: 12px;
    }

    .summary-row {
        padding: 13px 14px;
        border-radius: 16px;
        background: #fafafa;
        border: 1px solid #f1f2f4;
        display: flex;
        justify-content: space-between;
        gap: 12px;
    }

    .summary-row span {
        color: var(--muted);
        font-size: .8rem;
        font-weight: 850;
    }

    .summary-row strong {
        color: var(--text);
        font-size: .86rem;
        font-weight: 950;
        text-align: right;
    }

    .map-helper {
        padding: 14px;
        border-radius: 18px;
        background: #f9fafb;
        border: 1px solid var(--border);
        color: var(--muted);
        font-size: .82rem;
        font-weight: 650;
        line-height: 1.6;
    }

    .setting-actions {
        position: sticky;
        bottom: 0;
        z-index: 5;
        padding: 14px 18px;
        border-top: 1px solid var(--border);
        background: rgba(255,255,255,.92);
        backdrop-filter: blur(12px);
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        flex-wrap: wrap;
    }

    .alert-setting {
        margin-bottom: 16px;
        padding: 13px 15px;
        border-radius: 16px;
        font-size: .88rem;
        font-weight: 800;
        border: 1px solid transparent;
    }

    .alert-success-setting {
        background: #ecfdf5;
        color: #15803d;
        border-color: #bbf7d0;
    }

    .alert-error-setting {
        background: #fef2f2;
        color: #b91c1c;
        border-color: #fecaca;
    }

    @media (max-width: 1000px) {
        .setting-hero {
            grid-template-columns: 1fr;
        }

        .form-grid-3 {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 760px) {
        .setting-hero,
        .setting-box-body {
            padding: 18px;
        }

        .form-grid-2 {
            grid-template-columns: 1fr;
        }

        .setting-actions {
            justify-content: stretch;
        }

        .setting-actions .btn {
            width: 100%;
        }
    }
</style>

@php
    $initial = strtoupper(substr($pengaturan->nama ?? 'ST', 0, 2));

    $mapsLink = null;

    if ($pengaturan->latitude_toko && $pengaturan->longitude_toko) {
        $mapsLink = 'https://www.google.com/maps?q=' . $pengaturan->latitude_toko . ',' . $pengaturan->longitude_toko;
    }
@endphp

<section class="setting-hero">
    <div>
        <div class="badge bg-warning-subtle text-warning-emphasis mb-3">
            <i class="bi bi-shop me-1"></i>
            Pengaturan Toko
        </div>

        <h1>Atur identitas toko dan aturan pengiriman SiTahu.</h1>

        <p>
            Data ini akan dipakai di halaman pembeli, checkout, invoice, pengiriman kurir toko,
            dan informasi pembayaran. Jadi bagian ini wajib rapi supaya alur admin dan pembeli nyambung.
        </p>
    </div>

    <div class="setting-logo-preview">
        <div class="logo-box">
            @if($pengaturan->logo_url)
                <img src="{{ asset('storage/' . $pengaturan->logo_url) }}" alt="{{ $pengaturan->nama }}">
            @else
                {{ $initial }}
            @endif
        </div>

        <strong class="d-block text-dark">
            {{ $pengaturan->nama ?? 'SiTahu' }}
        </strong>

        <span class="text-muted small fw-semibold">
            {{ $pengaturan->jam_buka ?? 'Jam buka belum diatur' }}
        </span>
    </div>
</section>

<form action="{{ route('admin.pengaturan.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="grid g2">
        <div>
            <section class="setting-box">
                <div class="setting-box-head">
                    <div>
                        <h2>Identitas Toko</h2>
                        <p>Informasi dasar yang ditampilkan ke pembeli.</p>
                    </div>
                </div>

                <div class="setting-box-body">
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="nama">Nama Toko</label>
                            <input
                                type="text"
                                name="nama"
                                id="nama"
                                class="form-control"
                                value="{{ old('nama', $pengaturan->nama) }}"
                                placeholder="Contoh: SiTahu Kediri"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="logo_url">Logo Toko</label>
                            <input
                                type="file"
                                name="logo_url"
                                id="logo_url"
                                class="form-control"
                                accept="image/*"
                            >
                            <div class="form-note">
                                Format jpg, jpeg, png, webp. Maksimal 4 MB.
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="telepon">Nomor WhatsApp / Telepon</label>
                            <input
                                type="text"
                                name="telepon"
                                id="telepon"
                                class="form-control"
                                value="{{ old('telepon', $pengaturan->telepon) }}"
                                placeholder="Contoh: 081234567890"
                            >
                        </div>

                        <div class="form-group">
                            <label for="email">Email Toko</label>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                class="form-control"
                                value="{{ old('email', $pengaturan->email) }}"
                                placeholder="Contoh: sitahu@gmail.com"
                            >
                        </div>

                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="jam_buka">Jam Operasional</label>
                            <input
                                type="text"
                                name="jam_buka"
                                id="jam_buka"
                                class="form-control"
                                value="{{ old('jam_buka', $pengaturan->jam_buka) }}"
                                placeholder="Contoh: Senin - Sabtu, 08.00 - 17.00 WIB"
                            >
                        </div>

                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="alamat">Alamat Toko</label>
                            <textarea
                                name="alamat"
                                id="alamat"
                                class="form-control"
                                placeholder="Masukkan alamat lengkap toko"
                                required
                            >{{ old('alamat', $pengaturan->alamat) }}</textarea>
                        </div>

                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="tentang">Tentang Toko</label>
                            <textarea
                                name="tentang"
                                id="tentang"
                                class="form-control"
                                placeholder="Deskripsi singkat tentang toko dan produk tahu"
                            >{{ old('tentang', $pengaturan->tentang) }}</textarea>
                        </div>
                    </div>
                </div>
            </section>

            <section class="setting-box">
                <div class="setting-box-head">
                    <div>
                        <h2>Lokasi Toko</h2>
                        <p>Koordinat dipakai untuk maps dan perhitungan jarak pengiriman.</p>
                    </div>
                </div>

                <div class="setting-box-body">
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="latitude_toko">Latitude Toko</label>
                            <input
                                type="text"
                                name="latitude_toko"
                                id="latitude_toko"
                                class="form-control"
                                value="{{ old('latitude_toko', $pengaturan->latitude_toko) }}"
                                placeholder="Contoh: -7.8166"
                            >
                        </div>

                        <div class="form-group">
                            <label for="longitude_toko">Longitude Toko</label>
                            <input
                                type="text"
                                name="longitude_toko"
                                id="longitude_toko"
                                class="form-control"
                                value="{{ old('longitude_toko', $pengaturan->longitude_toko) }}"
                                placeholder="Contoh: 112.0119"
                            >
                        </div>
                    </div>

                    <div class="map-helper mt-3">
                        <strong class="text-dark d-block mb-1">Cara cepat ambil koordinat:</strong>
                        Buka Google Maps, klik kanan titik lokasi toko, lalu salin angka latitude dan longitude.
                        Masukkan latitude di kolom kiri, longitude di kolom kanan.

                        @if($mapsLink)
                            <div class="mt-3">
                                <a href="{{ $mapsLink }}" target="_blank" class="small-btn">
                                    <i class="bi bi-map"></i>
                                    Buka Lokasi Toko
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </section>
        </div>

        <div>
            <section class="setting-box">
                <div class="setting-box-head">
                    <div>
                        <h2>Aturan Pengiriman</h2>
                        <p>Dipakai saat pembeli memilih kurir toko.</p>
                    </div>
                </div>

                <div class="setting-box-body">
                    <div class="form-grid-3">
                        <div class="form-group">
                            <label for="tarif_per_km">Tarif per KM</label>
                            <input
                                type="number"
                                name="tarif_per_km"
                                id="tarif_per_km"
                                class="form-control"
                                value="{{ old('tarif_per_km', $pengaturan->tarif_per_km) }}"
                                min="0"
                                step="100"
                                placeholder="3000"
                            >
                            <div class="form-note">Contoh: 3000</div>
                        </div>

                        <div class="form-group">
                            <label for="biaya_minimum_pengiriman">Biaya Minimum</label>
                            <input
                                type="number"
                                name="biaya_minimum_pengiriman"
                                id="biaya_minimum_pengiriman"
                                class="form-control"
                                value="{{ old('biaya_minimum_pengiriman', $pengaturan->biaya_minimum_pengiriman) }}"
                                min="0"
                                step="100"
                                placeholder="5000"
                            >
                            <div class="form-note">Contoh: 5000</div>
                        </div>

                        <div class="form-group">
                            <label for="radius_maksimal_km">Radius Maksimal</label>
                            <input
                                type="number"
                                name="radius_maksimal_km"
                                id="radius_maksimal_km"
                                class="form-control"
                                value="{{ old('radius_maksimal_km', $pengaturan->radius_maksimal_km) }}"
                                min="0"
                                step="0.1"
                                placeholder="10"
                            >
                            <div class="form-note">Dalam kilometer.</div>
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <label for="area_pengiriman">Area Pengiriman</label>
                        <textarea
                            name="area_pengiriman"
                            id="area_pengiriman"
                            class="form-control"
                            placeholder="Contoh: Area Kota Kediri dan sekitarnya"
                        >{{ old('area_pengiriman', $pengaturan->area_pengiriman) }}</textarea>
                    </div>
                </div>
            </section>

            <section class="setting-box">
                <div class="setting-box-head">
                    <div>
                        <h2>Informasi Pembayaran</h2>
                        <p>Informasi ini membantu pembeli memahami metode pembayaran.</p>
                    </div>
                </div>

                <div class="setting-box-body">
                    <div class="form-grid-3">
                        <div class="form-group">
                            <label for="bank_nama">Nama Bank</label>
                            <input
                                type="text"
                                name="bank_nama"
                                id="bank_nama"
                                class="form-control"
                                value="{{ old('bank_nama', $pengaturan->bank_nama) }}"
                                placeholder="Contoh: BCA / BRI / Mandiri"
                            >
                        </div>

                        <div class="form-group">
                            <label for="bank_nomor_rekening">Nomor Rekening</label>
                            <input
                                type="text"
                                name="bank_nomor_rekening"
                                id="bank_nomor_rekening"
                                class="form-control"
                                value="{{ old('bank_nomor_rekening', $pengaturan->bank_nomor_rekening) }}"
                                placeholder="Contoh: 1234567890"
                            >
                        </div>

                        <div class="form-group">
                            <label for="bank_atas_nama">Atas Nama</label>
                            <input
                                type="text"
                                name="bank_atas_nama"
                                id="bank_atas_nama"
                                class="form-control"
                                value="{{ old('bank_atas_nama', $pengaturan->bank_atas_nama) }}"
                                placeholder="Contoh: SiTahu Premium"
                            >
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <label for="info_pembayaran">Catatan Pembayaran</label>
                        <textarea
                            name="info_pembayaran"
                            id="info_pembayaran"
                            class="form-control"
                            placeholder="Contoh: Transfer sesuai total bayar, lalu unggah bukti pembayaran saat checkout."
                        >{{ old('info_pembayaran', $pengaturan->info_pembayaran) }}</textarea>
                        <div class="form-note">Nomor rekening ini akan tampil di checkout saat pembeli memilih Transfer Bank.</div>
                    </div>
                </div>
            </section>

            <section class="setting-box">
                <div class="setting-box-head">
                    <div>
                        <h2>Ringkasan Saat Ini</h2>
                        <p>Preview aturan toko yang sedang aktif.</p>
                    </div>
                </div>

                <div class="setting-box-body">
                    <div class="setting-summary">
                        <div class="summary-row">
                            <span>Tarif / KM</span>
                            <strong>Rp {{ number_format((float) $pengaturan->tarif_per_km, 0, ',', '.') }}</strong>
                        </div>

                        <div class="summary-row">
                            <span>Biaya Minimum</span>
                            <strong>Rp {{ number_format((float) $pengaturan->biaya_minimum_pengiriman, 0, ',', '.') }}</strong>
                        </div>

                        <div class="summary-row">
                            <span>Radius Maksimal</span>
                            <strong>{{ number_format((float) $pengaturan->radius_maksimal_km, 1, ',', '.') }} km</strong>
                        </div>

                        <div class="summary-row">
                            <span>Koordinat Toko</span>
                            <strong>
                                @if($pengaturan->latitude_toko && $pengaturan->longitude_toko)
                                    {{ $pengaturan->latitude_toko }}, {{ $pengaturan->longitude_toko }}
                                @else
                                    Belum diatur
                                @endif
                            </strong>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <div class="setting-actions">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-light border fw-bold px-3">
            <i class="bi bi-arrow-left me-1 text-muted"></i>
            Kembali
        </a>

        <button type="submit" class="btn btn-brand px-4">
            <i class="bi bi-save me-1"></i>
            Simpan Pengaturan
        </button>
    </div>
</form>
@endsection