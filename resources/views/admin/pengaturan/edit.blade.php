@extends('layouts.admin')

@section('title', 'Pengaturan Toko - SiTahu')
@section('page_title', 'Pengaturan Toko')

@section('content')
<style>
    .setting-layout {
        display: grid;
        grid-template-columns: minmax(0, 0.78fr) minmax(0, 1.22fr);
        gap: 18px;
        align-items: start;
    }

    .setting-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 18px;
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }

    .setting-card-head {
        padding: 18px;
        border-bottom: 1px solid var(--border);
        background: #fff;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
    }

    .setting-card-title {
        margin: 0;
        color: var(--text);
        font-size: 1rem;
        font-weight: 900;
        letter-spacing: -0.03em;
    }

    .setting-card-desc {
        margin: 4px 0 0;
        color: var(--muted);
        font-size: 0.82rem;
        font-weight: 650;
        line-height: 1.45;
    }

    .setting-card-body {
        padding: 18px;
    }

    .store-preview {
        position: sticky;
        top: calc(var(--topbar-height) + 24px);
    }

    .preview-cover {
        position: relative;
        overflow: hidden;
        border-radius: 22px;
        border: 1px solid var(--border);
        background:
            radial-gradient(circle at top right, rgba(223, 186, 104, 0.24), transparent 36%),
            linear-gradient(135deg, #ffffff, #fff8e8);
        box-shadow: var(--shadow-sm);
        padding: 22px;
    }

    .preview-logo {
        width: 96px;
        height: 96px;
        border-radius: 26px;
        background: #fff;
        border: 1px solid rgba(223, 186, 104, 0.28);
        box-shadow: 0 14px 28px rgba(223, 186, 104, 0.18);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        margin-bottom: 16px;
    }

    .preview-logo img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 8px;
    }

    .preview-logo i {
        color: var(--brand-dark);
        font-size: 2.1rem;
    }

    .preview-title {
        margin: 0;
        color: var(--text);
        font-size: 1.35rem;
        font-weight: 950;
        letter-spacing: -0.05em;
    }

    .preview-desc {
        margin: 8px 0 0;
        color: var(--muted);
        font-size: 0.88rem;
        font-weight: 650;
        line-height: 1.55;
    }

    .preview-info-list {
        display: grid;
        gap: 10px;
        margin-top: 18px;
    }

    .preview-info {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        padding: 12px;
        border-radius: 16px;
        background: rgba(255,255,255,0.76);
        border: 1px solid rgba(229,231,235,0.9);
    }

    .preview-info-icon {
        width: 34px;
        height: 34px;
        border-radius: 13px;
        background: var(--brand-soft);
        color: var(--brand-dark);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .preview-info-label {
        color: var(--muted);
        font-size: 0.72rem;
        font-weight: 850;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .preview-info-value {
        margin-top: 2px;
        color: var(--text);
        font-size: 0.84rem;
        font-weight: 750;
        line-height: 1.4;
    }

    .setting-section {
        padding: 18px;
        border-bottom: 1px solid var(--border);
    }

    .setting-section:last-child {
        border-bottom: 0;
    }

    .section-title {
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-title-icon {
        width: 38px;
        height: 38px;
        border-radius: 14px;
        background: var(--brand-soft);
        color: var(--brand-dark);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .section-title h3 {
        margin: 0;
        color: var(--text);
        font-size: 0.98rem;
        font-weight: 900;
        letter-spacing: -0.03em;
    }

    .section-title p {
        margin: 3px 0 0;
        color: var(--muted);
        font-size: 0.78rem;
        font-weight: 650;
    }

    .form-label-modern {
        display: block;
        margin-bottom: 7px;
        color: #374151;
        font-size: 0.78rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .form-help {
        margin-top: 6px;
        color: var(--muted);
        font-size: 0.74rem;
        font-weight: 650;
        line-height: 1.4;
    }

    .input-icon-group {
        position: relative;
    }

    .input-icon-group i {
        position: absolute;
        top: 50%;
        left: 13px;
        transform: translateY(-50%);
        color: var(--muted);
        pointer-events: none;
    }

    .input-icon-group .form-control {
        padding-left: 40px;
    }

    .logo-upload {
        position: relative;
        border: 2px dashed #d1d5db;
        border-radius: 18px;
        background: #f9fafb;
        padding: 22px;
        text-align: center;
        transition: 0.16s ease;
        min-height: 148px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .logo-upload:hover {
        border-color: var(--brand);
        background: rgba(223, 186, 104, 0.06);
    }

    .logo-upload input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
    }

    .upload-icon {
        width: 52px;
        height: 52px;
        margin: 0 auto 10px;
        border-radius: 18px;
        background: #fff;
        color: var(--brand-dark);
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
    }

    .setting-footer {
        position: sticky;
        bottom: 0;
        z-index: 5;
        padding: 14px 18px;
        background: rgba(255,255,255,0.88);
        backdrop-filter: blur(14px);
        border-top: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
    }

    .coordinate-box {
        padding: 13px;
        border-radius: 16px;
        background: #f9fafb;
        border: 1px solid var(--border);
    }

    @media (max-width: 1100px) {
        .setting-layout {
            grid-template-columns: 1fr;
        }

        .store-preview {
            position: static;
        }
    }

    @media (max-width: 640px) {
        .setting-footer {
            flex-direction: column;
            align-items: stretch;
        }

        .setting-footer .btn {
            width: 100%;
        }
    }
</style>

<div class="hero">
    <div>
        <h1>Pengaturan Toko</h1>
        <p>Atur identitas toko, alamat, jam operasional, pengiriman, dan informasi pembayaran untuk aplikasi SiTahu.</p>
    </div>

    <a href="{{ route('admin.dashboard') }}" class="btn btn-light border fw-bold px-3">
        <i class="bi bi-grid-1x2 me-1 text-muted"></i>
        Dashboard
    </a>
</div>

<form id="formPengaturan" method="POST" action="{{ route('admin.pengaturan.update') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="setting-layout">
        <aside class="store-preview">
            <div class="preview-cover">
                <div class="preview-logo">
                    @if($pengaturan->logo_url)
                        <img src="{{ asset('storage/' . $pengaturan->logo_url) }}" alt="Logo Toko">
                    @else
                        <i class="bi bi-shop-window"></i>
                    @endif
                </div>

                <h2 class="preview-title">
                    {{ old('nama', $pengaturan->nama) ?: 'Nama Toko' }}
                </h2>

                <p class="preview-desc">
                    {{ old('tentang', $pengaturan->tentang) ?: 'Deskripsi toko akan tampil di sini. Isi bagian tentang toko agar pembeli tahu profil UMKM tahu kamu.' }}
                </p>

                <div class="preview-info-list">
                    <div class="preview-info">
                        <div class="preview-info-icon">
                            <i class="bi bi-whatsapp"></i>
                        </div>
                        <div>
                            <div class="preview-info-label">WhatsApp</div>
                            <div class="preview-info-value">
                                {{ old('telepon', $pengaturan->telepon) ?: '-' }}
                            </div>
                        </div>
                    </div>

                    <div class="preview-info">
                        <div class="preview-info-icon">
                            <i class="bi bi-clock"></i>
                        </div>
                        <div>
                            <div class="preview-info-label">Jam Operasional</div>
                            <div class="preview-info-value">
                                {{ old('jam_buka', $pengaturan->jam_buka) ?: '--:--' }}
                                -
                                {{ old('jam_tutup', $pengaturan->jam_tutup) ?: '--:--' }}
                            </div>
                        </div>
                    </div>

                    <div class="preview-info">
                        <div class="preview-info-icon">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <div>
                            <div class="preview-info-label">Alamat</div>
                            <div class="preview-info-value">
                                {{ old('alamat', $pengaturan->alamat) ?: 'Alamat toko belum diisi.' }}
                            </div>
                        </div>
                    </div>

                    <div class="preview-info">
                        <div class="preview-info-icon">
                            <i class="bi bi-truck"></i>
                        </div>
                        <div>
                            <div class="preview-info-label">Area Pengiriman</div>
                            <div class="preview-info-value">
                                {{ old('area_pengiriman', $pengaturan->area_pengiriman) ?: 'Belum diatur.' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <main class="setting-card">
            <div class="setting-card-head">
                <div>
                    <h2 class="setting-card-title">Form Pengaturan Toko</h2>
                    <p class="setting-card-desc">
                        Data ini aman untuk web admin dan mobile pembeli. Tidak mengubah alur database atau logic backend.
                    </p>
                </div>
            </div>

            <div class="setting-section">
                <div class="section-title">
                    <div class="section-title-icon">
                        <i class="bi bi-shop-window"></i>
                    </div>
                    <div>
                        <h3>Identitas Toko</h3>
                        <p>Nama, logo, kontak, dan deskripsi singkat toko.</p>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label-modern">Logo Toko</label>

                        <div class="logo-upload">
                            <input type="file" name="logo" accept="image/*">

                            <div>
                                <div class="upload-icon">
                                    <i class="bi bi-cloud-upload"></i>
                                </div>
                                <div class="fw-bold text-dark">Klik untuk pilih logo</div>
                                <div class="text-muted small fw-semibold mt-1">
                                    Format JPG/PNG, maksimal 2MB. Logo lama tetap dipakai kalau tidak upload baru.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label-modern">Nama Toko</label>
                        <input class="form-control"
                               name="nama"
                               value="{{ old('nama', $pengaturan->nama) }}"
                               placeholder="Contoh: SiTahu Kediri">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-modern">WhatsApp / Telepon</label>
                        <div class="input-icon-group">
                            <i class="bi bi-whatsapp"></i>
                            <input class="form-control"
                                   name="telepon"
                                   value="{{ old('telepon', $pengaturan->telepon) }}"
                                   placeholder="081234567890">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-modern">Email Toko</label>
                        <div class="input-icon-group">
                            <i class="bi bi-envelope"></i>
                            <input class="form-control"
                                   type="email"
                                   name="email"
                                   value="{{ old('email', $pengaturan->email) }}"
                                   placeholder="toko@email.com">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-modern">Jam Buka</label>
                        <div class="input-icon-group">
                            <i class="bi bi-clock"></i>
                            <input class="form-control"
                                   name="jam_buka"
                                   value="{{ old('jam_buka', $pengaturan->jam_buka) }}"
                                   placeholder="07.00">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-modern">Jam Tutup</label>
                        <div class="input-icon-group">
                            <i class="bi bi-clock-history"></i>
                            <input class="form-control"
                                   name="jam_tutup"
                                   value="{{ old('jam_tutup', $pengaturan->jam_tutup) }}"
                                   placeholder="17.00">
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label-modern">Tentang Toko</label>
                        <textarea class="form-control"
                                  rows="4"
                                  name="tentang"
                                  placeholder="Ceritakan singkat tentang toko tahu kamu...">{{ old('tentang', $pengaturan->tentang) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="setting-section">
                <div class="section-title">
                    <div class="section-title-icon">
                        <i class="bi bi-geo-alt"></i>
                    </div>
                    <div>
                        <h3>Alamat & Titik Lokasi</h3>
                        <p>Dipakai untuk info toko dan perhitungan pengiriman kurir toko.</p>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label-modern">Alamat Toko</label>
                        <textarea class="form-control"
                                  rows="3"
                                  name="alamat"
                                  placeholder="Masukkan alamat lengkap toko...">{{ old('alamat', $pengaturan->alamat) }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-modern">Latitude Toko</label>
                        <input class="form-control"
                               name="latitude_toko"
                               value="{{ old('latitude_toko', $pengaturan->latitude_toko) }}"
                               placeholder="-7.8160000">
                        <div class="form-help">Isi titik latitude dari maps/OpenStreetMap.</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-modern">Longitude Toko</label>
                        <input class="form-control"
                               name="longitude_toko"
                               value="{{ old('longitude_toko', $pengaturan->longitude_toko) }}"
                               placeholder="112.0110000">
                        <div class="form-help">Isi titik longitude dari maps/OpenStreetMap.</div>
                    </div>

                    <div class="col-12">
                        <div class="coordinate-box">
                            <div class="fw-bold text-dark mb-1">
                                <i class="bi bi-info-circle text-warning me-1"></i>
                                Catatan titik toko
                            </div>
                            <div class="text-muted small fw-semibold">
                                Untuk sekarang cukup input manual latitude dan longitude. Nanti di mobile, alamat pembeli yang pakai GPS/OpenStreetMap akan dibandingkan dengan titik toko ini.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="setting-section">
                <div class="section-title">
                    <div class="section-title-icon">
                        <i class="bi bi-truck"></i>
                    </div>
                    <div>
                        <h3>Aturan Pengiriman</h3>
                        <p>Dipakai saat pembeli memilih metode Kurir Toko.</p>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label-modern">Tarif per KM</label>
                        <div class="input-icon-group">
                            <i class="bi bi-cash"></i>
                            <input class="form-control"
                                   name="tarif_per_km"
                                   value="{{ old('tarif_per_km', $pengaturan->tarif_per_km) }}"
                                   placeholder="2000">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label-modern">Biaya Minimum</label>
                        <div class="input-icon-group">
                            <i class="bi bi-cash-stack"></i>
                            <input class="form-control"
                                   name="biaya_minimum_pengiriman"
                                   value="{{ old('biaya_minimum_pengiriman', $pengaturan->biaya_minimum_pengiriman) }}"
                                   placeholder="5000">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label-modern">Radius Maksimal KM</label>
                        <div class="input-icon-group">
                            <i class="bi bi-signpost-split"></i>
                            <input class="form-control"
                                   name="radius_maksimal_km"
                                   value="{{ old('radius_maksimal_km', $pengaturan->radius_maksimal_km) }}"
                                   placeholder="10">
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label-modern">Area Pengiriman</label>
                        <input class="form-control"
                               name="area_pengiriman"
                               value="{{ old('area_pengiriman', $pengaturan->area_pengiriman) }}"
                               placeholder="Contoh: Kota Kediri dan sekitarnya">
                    </div>
                </div>
            </div>

            <div class="setting-section">
                <div class="section-title">
                    <div class="section-title-icon">
                        <i class="bi bi-credit-card-2-front"></i>
                    </div>
                    <div>
                        <h3>Informasi Pembayaran</h3>
                        <p>Informasi ini bisa ditampilkan di checkout atau profil toko mobile.</p>
                    </div>
                </div>

                <label class="form-label-modern">Info Pembayaran</label>
                <textarea class="form-control"
                          rows="4"
                          name="info_pembayaran"
                          placeholder="Contoh: Pembayaran tersedia melalui QRIS. Pesanan diproses setelah pembayaran berhasil.">{{ old('info_pembayaran', $pengaturan->info_pembayaran) }}</textarea>
            </div>

            <div class="setting-footer">
                <div class="text-muted small fw-semibold">
                    <i class="bi bi-shield-check text-success me-1"></i>
                    Perubahan hanya menyimpan data pengaturan toko.
                </div>

                <button class="btn btn-brand fw-bold px-4" type="submit">
                    <i class="bi bi-save me-1"></i>
                    Simpan Pengaturan
                </button>
            </div>
        </main>
    </div>
</form>
@endsection
