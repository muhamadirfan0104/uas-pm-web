@extends('layouts.admin')

@section('title', 'Pengaturan Toko - SiTahu')
@section('page_title', 'Pengaturan Toko')
@section('page_subtitle', 'Atur identitas toko, lokasi, pengiriman, dan rekening pembayaran.')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIINfQuc+gHkGmDCbLxJYmiZYoBBf+zg8Q0=" crossorigin="">
<style>
    .settings-shell { display: grid; grid-template-columns: 260px minmax(0, 1fr); gap: 16px; align-items: start; }
    .settings-nav {
        position: sticky;
        top: calc(var(--topbar-height) + 18px);
        border-radius: 22px;
        border: 1px solid var(--border);
        background: #fff;
        box-shadow: var(--shadow-soft);
        padding: 12px;
    }
    .settings-nav a {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 11px 12px;
        border-radius: 15px;
        text-decoration: none;
        color: #475467;
        font-size: .84rem;
        font-weight: 900;
    }
    .settings-nav a:hover { background: var(--brand-soft); color: var(--brand-dark); }
    .setting-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 22px;
        border-radius: 24px;
        border: 1px solid #f1d49c;
        background:
            radial-gradient(circle at right top, rgba(200,147,53,.2), transparent 18rem),
            linear-gradient(135deg, #fff, #fff8ea);
        box-shadow: var(--shadow-soft);
        margin-bottom: 16px;
    }
    .setting-hero h1 { margin: 0; font-size: 1.52rem; font-weight: 950; letter-spacing: -.055em; }
    .setting-hero p { margin: 7px 0 0; color: var(--muted); font-size: .86rem; font-weight: 650; line-height: 1.55; }
    .logo-preview {
        width: 74px; height: 74px; border-radius: 24px; overflow: hidden;
        display: grid; place-items: center; flex-shrink: 0;
        background: linear-gradient(135deg, var(--brand), #ad7a24); color: #fff;
        font-size: 1.4rem; font-weight: 950; box-shadow: 0 16px 35px rgba(200,147,53,.22);
    }
    .logo-preview img { width: 100%; height: 100%; object-fit: cover; }
    .setting-section {
        border-radius: 22px;
        border: 1px solid var(--border);
        background: #fff;
        box-shadow: var(--shadow-soft);
        overflow: hidden;
        margin-bottom: 16px;
    }
    .setting-section-head {
        padding: 17px 18px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }
    .setting-section-head h2 { margin: 0; font-size: 1rem; font-weight: 950; letter-spacing: -.035em; }
    .setting-section-head p { margin: 4px 0 0; color: var(--muted); font-size: .78rem; font-weight: 650; line-height: 1.5; }
    .setting-section-body { padding: 18px; }
    .form-grid-2 { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: 14px; }
    .form-grid-3 { display: grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap: 14px; }
    .setting-field label { margin-bottom: 7px; color: var(--text); font-size: .8rem; font-weight: 950; }
    .setting-field .hint { margin-top: 6px; color: var(--muted); font-size: .73rem; font-weight: 650; line-height: 1.45; }
    .setting-field textarea { min-height: 104px; resize: vertical; line-height: 1.55; }
    .map-box { height: 360px; border-radius: 20px; border: 1px solid var(--border); overflow: hidden; background: #f3f4f6; }
    .coord-pill {
        display: flex; align-items: center; justify-content: space-between; gap: 10px;
        padding: 11px 13px; border-radius: 15px; border: 1px solid var(--border); background: #fbfcfd;
        color: var(--muted); font-size: .78rem; font-weight: 800;
    }
    .summary-card { padding: 14px; border: 1px solid var(--border); border-radius: 18px; background: #fff; }
    .delivery-simulator { margin-top: 14px; padding: 14px; border-radius: 18px; border: 1px dashed rgba(200,147,53,.36); background: #fffdf8; }
    .delivery-simulator strong { color: var(--brand-dark); }
    .summary-card .label { color: var(--muted); font-size: .72rem; font-weight: 950; text-transform: uppercase; letter-spacing: .05em; }
    .summary-card .value { margin-top: 6px; color: var(--text); font-weight: 950; font-size: .94rem; }
    .sticky-save {
        position: sticky; bottom: 16px; z-index: 10;
        display: flex; justify-content: flex-end; gap: 10px; margin-top: 12px;
        padding: 12px; border: 1px solid var(--border); border-radius: 20px; background: rgba(255,255,255,.86); backdrop-filter: blur(16px); box-shadow: var(--shadow-soft);
    }
    @media(max-width: 1100px){ .settings-shell{ grid-template-columns:1fr; } .settings-nav{ position: static; display:flex; overflow-x:auto; } .settings-nav a{ white-space:nowrap; } }
    @media(max-width: 760px){ .setting-hero{ align-items:flex-start; flex-direction:column; } .form-grid-2,.form-grid-3{ grid-template-columns:1fr; } .map-box{ height:300px; } }
</style>
@endpush

@section('content')
@php
    $mapsLink = ($pengaturan->latitude_toko && $pengaturan->longitude_toko)
        ? 'https://www.google.com/maps?q=' . $pengaturan->latitude_toko . ',' . $pengaturan->longitude_toko
        : null;
@endphp

<form method="POST" action="{{ route('admin.pengaturan.update') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="setting-hero">
        <div>
            <span class="chip c-yellow mb-2">Data aktif di halaman pembeli</span>
            <h1>Pengaturan toko</h1>
            <p>Informasi di halaman ini dipakai untuk tampilan toko, checkout, rekening transfer, titik maps, dan perhitungan kurir toko.</p>
        </div>
        <div class="logo-preview">
            @if($pengaturan->logo_url)
                <img src="{{ asset('storage/' . $pengaturan->logo_url) }}" alt="Logo {{ $pengaturan->nama }}">
            @else
                {{ strtoupper(substr($pengaturan->nama ?? 'ST', 0, 2)) }}
            @endif
        </div>
    </div>

    <div class="settings-shell">
        <aside class="settings-nav">
            <a href="#identitas"><i class="bi bi-shop-window"></i> Identitas</a>
            <a href="#lokasi"><i class="bi bi-geo-alt"></i> Lokasi toko</a>
            <a href="#pengiriman"><i class="bi bi-truck"></i> Pengiriman</a>
            <a href="#pembayaran"><i class="bi bi-credit-card"></i> Pembayaran</a>
            <a href="#preview"><i class="bi bi-eye"></i> Ringkasan</a>
        </aside>

        <main class="min-w-0">
            <section class="setting-section" id="identitas">
                <div class="setting-section-head">
                    <div><h2>Identitas toko</h2><p>Nama, logo, kontak, alamat tertulis, dan deskripsi singkat toko.</p></div>
                    <span class="chip c-gray">Wajib dicek</span>
                </div>
                <div class="setting-section-body">
                    <div class="form-grid-2">
                        <div class="setting-field">
                            <label for="nama">Nama toko</label>
                            <input class="form-control" id="nama" name="nama" value="{{ old('nama', $pengaturan->nama) }}" placeholder="Contoh: SiTahu Kediri" required>
                        </div>
                        <div class="setting-field">
                            <label for="logo_url">Logo toko</label>
                            <input class="form-control" type="file" id="logo_url" name="logo_url" accept="image/*">
                            <div class="hint">JPG, PNG, WEBP. Maksimal 4 MB.</div>
                        </div>
                        <div class="setting-field">
                            <label for="telepon">Nomor WhatsApp / Telepon</label>
                            <input class="form-control" id="telepon" name="telepon" value="{{ old('telepon', $pengaturan->telepon) }}" placeholder="081234567890">
                        </div>
                        <div class="setting-field">
                            <label for="email">Email toko</label>
                            <input class="form-control" type="email" id="email" name="email" value="{{ old('email', $pengaturan->email) }}" placeholder="sitahu@gmail.com">
                        </div>
                        <div class="setting-field">
                            <label for="jam_buka">Jam buka</label>
                            <input class="form-control" id="jam_buka" name="jam_buka" value="{{ old('jam_buka', $pengaturan->jam_buka) }}" placeholder="08.00 WIB">
                        </div>
                        <div class="setting-field">
                            <label for="jam_tutup">Jam tutup</label>
                            <input class="form-control" id="jam_tutup" name="jam_tutup" value="{{ old('jam_tutup', $pengaturan->jam_tutup) }}" placeholder="17.00 WIB">
                        </div>
                        <div class="setting-field" style="grid-column:1 / -1;">
                            <label for="alamat">Alamat tertulis</label>
                            <textarea class="form-control" id="alamat" name="alamat" placeholder="Masukkan alamat lengkap toko" required>{{ old('alamat', $pengaturan->alamat) }}</textarea>
                        </div>
                        <div class="setting-field" style="grid-column:1 / -1;">
                            <label for="tentang">Tentang toko</label>
                            <textarea class="form-control" id="tentang" name="tentang" placeholder="Deskripsi singkat toko dan produk tahu">{{ old('tentang', $pengaturan->tentang) }}</textarea>
                        </div>
                    </div>
                </div>
            </section>

            <section class="setting-section" id="lokasi">
                <div class="setting-section-head">
                    <div><h2>Lokasi toko</h2><p>Klik titik di maps. Latitude dan longitude akan terisi otomatis.</p></div>
                    @if($mapsLink)<a href="{{ $mapsLink }}" target="_blank" class="small-btn"><i class="bi bi-map"></i> Buka Maps</a>@endif
                </div>
                <div class="setting-section-body">
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <button class="small-btn" type="button" id="btnUseCurrent"><i class="bi bi-crosshair"></i> Lokasi Saya</button>
                        <div class="input-group" style="max-width: 420px;">
                            <input class="form-control" type="search" id="mapSearch" placeholder="Cari lokasi toko...">
                            <button class="btn btn-light border" type="button" id="btnMapSearch"><i class="bi bi-search"></i></button>
                        </div>
                    </div>
                    <div id="storeMap" class="map-box"></div>
                    <div class="form-grid-2 mt-3">
                        <div class="setting-field">
                            <label for="latitude_toko">Latitude</label>
                            <input class="form-control" id="latitude_toko" name="latitude_toko" value="{{ old('latitude_toko', $pengaturan->latitude_toko) }}" placeholder="Pilih dari maps" readonly>
                        </div>
                        <div class="setting-field">
                            <label for="longitude_toko">Longitude</label>
                            <input class="form-control" id="longitude_toko" name="longitude_toko" value="{{ old('longitude_toko', $pengaturan->longitude_toko) }}" placeholder="Pilih dari maps" readonly>
                        </div>
                    </div>
                </div>
            </section>

            <section class="setting-section" id="pengiriman">
                <div class="setting-section-head">
                    <div><h2>Aturan pengiriman</h2><p>Dipakai saat pembeli memilih Kurir Toko di checkout.</p></div>
                </div>
                <div class="setting-section-body">
                    <div class="form-grid-3">
                        <div class="setting-field">
                            <label for="tarif_per_km">Tarif per KM</label>
                            <input class="form-control" type="number" id="tarif_per_km" name="tarif_per_km" min="0" step="100" value="{{ old('tarif_per_km', $pengaturan->tarif_per_km) }}" placeholder="3000">
                        </div>
                        <div class="setting-field">
                            <label for="biaya_minimum_pengiriman">Biaya minimum</label>
                            <input class="form-control" type="number" id="biaya_minimum_pengiriman" name="biaya_minimum_pengiriman" min="0" step="100" value="{{ old('biaya_minimum_pengiriman', $pengaturan->biaya_minimum_pengiriman) }}" placeholder="5000">
                        </div>
                        <div class="setting-field">
                            <label for="radius_maksimal_km">Radius maksimal km</label>
                            <input class="form-control" type="number" id="radius_maksimal_km" name="radius_maksimal_km" min="0" step="0.1" value="{{ old('radius_maksimal_km', $pengaturan->radius_maksimal_km) }}" placeholder="10">
                        </div>
                    </div>
                    <div class="setting-field mt-3">
                        <label for="area_pengiriman">Area pengiriman</label>
                        <textarea class="form-control" id="area_pengiriman" name="area_pengiriman" placeholder="Contoh: Area Kota Kediri dan sekitarnya">{{ old('area_pengiriman', $pengaturan->area_pengiriman) }}</textarea>
                    </div>
                    <div class="delivery-simulator">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                            <div>
                                <div class="fw-black"><i class="bi bi-calculator me-1"></i>Simulasi ongkir kurir toko</div>
                                <div class="text-muted small fw-semibold mt-1">Biaya dihitung dari jarak x tarif/km, lalu dibandingkan dengan biaya minimum.</div>
                            </div>
                            <div class="input-group" style="max-width:260px;">
                                <input class="form-control" type="number" min="0" step="0.1" id="simulasiJarak" value="3">
                                <span class="input-group-text">km</span>
                            </div>
                        </div>
                        <div class="mt-3 text-muted fw-semibold small">Estimasi ongkir: <strong id="simulasiOngkir">-</strong></div>
                    </div>
                </div>
            </section>

            <section class="setting-section" id="pembayaran">
                <div class="setting-section-head">
                    <div><h2>Rekening transfer bank</h2><p>Nomor rekening ini muncul di popup pembayaran setelah pembeli membuat pesanan transfer.</p></div>
                </div>
                <div class="setting-section-body">
                    <div class="form-grid-3">
                        <div class="setting-field">
                            <label for="bank_nama">Nama bank</label>
                            <input class="form-control" id="bank_nama" name="bank_nama" value="{{ old('bank_nama', $pengaturan->bank_nama) }}" placeholder="BCA / BRI / Mandiri">
                        </div>
                        <div class="setting-field">
                            <label for="bank_nomor_rekening">Nomor rekening</label>
                            <input class="form-control" id="bank_nomor_rekening" name="bank_nomor_rekening" value="{{ old('bank_nomor_rekening', $pengaturan->bank_nomor_rekening) }}" placeholder="1234567890">
                        </div>
                        <div class="setting-field">
                            <label for="bank_atas_nama">Atas nama</label>
                            <input class="form-control" id="bank_atas_nama" name="bank_atas_nama" value="{{ old('bank_atas_nama', $pengaturan->bank_atas_nama) }}" placeholder="SiTahu Premium">
                        </div>
                    </div>
                    <div class="setting-field mt-3">
                        <label for="info_pembayaran">Catatan pembayaran</label>
                        <textarea class="form-control" id="info_pembayaran" name="info_pembayaran" placeholder="Instruksi pembayaran untuk pembeli">{{ old('info_pembayaran', $pengaturan->info_pembayaran) }}</textarea>
                    </div>
                </div>
            </section>

            <section class="setting-section" id="preview">
                <div class="setting-section-head">
                    <div><h2>Ringkasan aktif</h2><p>Preview data penting yang sedang digunakan sistem.</p></div>
                </div>
                <div class="setting-section-body">
                    <div class="grid g4">
                        <div class="summary-card"><div class="label">Toko</div><div class="value">{{ $pengaturan->nama }}</div></div>
                        <div class="summary-card"><div class="label">Telepon</div><div class="value">{{ $pengaturan->telepon ?: '-' }}</div></div>
                        <div class="summary-card"><div class="label">Tarif/km</div><div class="value">Rp {{ number_format((float) $pengaturan->tarif_per_km, 0, ',', '.') }}</div></div>
                        <div class="summary-card"><div class="label">Radius</div><div class="value">{{ number_format((float) $pengaturan->radius_maksimal_km, 1, ',', '.') }} km</div></div>
                    </div>
                    <div class="coord-pill mt-3">
                        <span><i class="bi bi-geo-alt me-1"></i>Koordinat toko</span>
                        <strong id="coordPreview">{{ $pengaturan->latitude_toko && $pengaturan->longitude_toko ? $pengaturan->latitude_toko . ', ' . $pengaturan->longitude_toko : 'Belum dipilih' }}</strong>
                    </div>
                </div>
            </section>

            <div class="sticky-save">
                <button type="submit" class="btn btn-brand px-4"><i class="bi bi-save me-1"></i>Simpan Pengaturan</button>
            </div>
        </main>
    </div>
</form>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const latInput = document.getElementById('latitude_toko');
    const lngInput = document.getElementById('longitude_toko');
    const coordPreview = document.getElementById('coordPreview');
    const mapEl = document.getElementById('storeMap');

    if (!mapEl || typeof L === 'undefined') {
        window.showSitahuToast?.('Maps belum bisa dimuat. Periksa koneksi internet.', 'warning');
        return;
    }

    const defaultLat = parseFloat(latInput.value) || -7.8480;
    const defaultLng = parseFloat(lngInput.value) || 112.0178;
    const map = L.map('storeMap').setView([defaultLat, defaultLng], latInput.value && lngInput.value ? 16 : 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    let marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);
    marker.bindTooltip('Geser atau klik maps untuk menandai titik toko', {permanent: false, direction: 'top'});

    const updatePoint = (lat, lng, zoom = null) => {
        const cleanLat = Number(lat).toFixed(8);
        const cleanLng = Number(lng).toFixed(8);
        latInput.value = cleanLat;
        lngInput.value = cleanLng;
        coordPreview.textContent = `${cleanLat}, ${cleanLng}`;
        marker.setLatLng([cleanLat, cleanLng]);
        if (zoom) map.setView([cleanLat, cleanLng], zoom);
    };

    marker.on('dragend', (event) => {
        const pos = event.target.getLatLng();
        updatePoint(pos.lat, pos.lng);
    });

    map.on('click', (event) => {
        updatePoint(event.latlng.lat, event.latlng.lng);
    });

    document.getElementById('btnUseCurrent')?.addEventListener('click', () => {
        if (!navigator.geolocation) {
            window.showSitahuToast?.('Browser tidak mendukung lokasi otomatis.', 'warning');
            return;
        }
        navigator.geolocation.getCurrentPosition(
            (position) => {
                updatePoint(position.coords.latitude, position.coords.longitude, 17);
                window.showSitahuToast?.('Titik toko berhasil diambil dari lokasi saat ini.', 'success');
            },
            () => window.showSitahuToast?.('Gagal mengambil lokasi. Pastikan izin lokasi aktif.', 'warning')
        );
    });

    const searchLocation = async () => {
        const keyword = document.getElementById('mapSearch')?.value?.trim();
        if (!keyword) return;
        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(keyword)}&limit=1`);
            const data = await response.json();
            if (!data.length) {
                window.showSitahuToast?.('Lokasi tidak ditemukan.', 'warning');
                return;
            }
            updatePoint(data[0].lat, data[0].lon, 16);
        } catch (error) {
            window.showSitahuToast?.('Pencarian lokasi belum bisa digunakan.', 'warning');
        }
    };

    const updateSimulasiOngkir = () => {
        const jarak = Number(document.getElementById('simulasiJarak')?.value || 0);
        const tarif = Number(document.getElementById('tarif_per_km')?.value || 0);
        const minimum = Number(document.getElementById('biaya_minimum_pengiriman')?.value || 0);
        const biaya = Math.max(minimum, Math.ceil((jarak * tarif) / 100) * 100);
        const target = document.getElementById('simulasiOngkir');
        if (target) target.textContent = 'Rp ' + biaya.toLocaleString('id-ID');
    };

    ['simulasiJarak', 'tarif_per_km', 'biaya_minimum_pengiriman'].forEach((id) => {
        document.getElementById(id)?.addEventListener('input', updateSimulasiOngkir);
    });
    updateSimulasiOngkir();

    document.getElementById('btnMapSearch')?.addEventListener('click', searchLocation);
    document.getElementById('mapSearch')?.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            searchLocation();
        }
    });
});
</script>
@endpush
