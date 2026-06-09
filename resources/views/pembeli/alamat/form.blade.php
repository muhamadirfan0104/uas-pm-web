@extends('layouts.pembeli')

@section('title', ($alamat->exists ? 'Edit Alamat' : 'Tambah Alamat') . ' - SiTahu')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIINfQz36ySLBQOjaYj9n2L8Kt2nHLxH0wM=" crossorigin="">
<style>
    .address-map-card {
        border: 1px solid var(--line);
        border-radius: 22px;
        overflow: hidden;
        background: #fff;
        box-shadow: var(--shadow-xs);
    }
    .address-map-toolbar {
        padding: 14px;
        border-bottom: 1px solid var(--line);
        background: linear-gradient(180deg, #fff, #fffdf7);
    }
    .address-map-search {
        min-height: 48px;
        border-radius: 15px;
        border: 1px solid var(--line);
        font-weight: 700;
    }
    .address-map-search:focus {
        border-color: rgba(200,147,53,.55);
        box-shadow: 0 0 0 .25rem rgba(200,147,53,.12);
    }
    #alamatMap {
        height: 390px;
        width: 100%;
        background: var(--brand-soft);
    }
    .address-coordinate-box {
        border: 1px dashed rgba(200,147,53,.38);
        background: #fffaf0;
        border-radius: 18px;
        padding: 14px 16px;
    }
    .leaflet-container { font-family: 'Plus Jakarta Sans', sans-serif; }
    @media (max-width: 767.98px) {
        #alamatMap { height: 320px; }
    }
</style>
@endpush

@section('content')
@php
    $isEdit = $alamat->exists;
    $action = $isEdit ? route('pembeli-web.alamat.update', $alamat) : route('pembeli-web.alamat.store');
    $latValue = old('latitude', $alamat->latitude);
    $lngValue = old('longitude', $alamat->longitude);
    $defaultLat = $latValue ?: -7.2575;
    $defaultLng = $lngValue ?: 112.7521;
@endphp
<div class="container py-4 py-lg-5">
    <div class="breadcrumb-modern">
        <a href="{{ route('pembeli-web.home') }}">Beranda</a>
        <i class="bi bi-chevron-right small"></i>
        <a href="{{ route('pembeli-web.alamat.index') }}">Alamat</a>
        <i class="bi bi-chevron-right small"></i>
        <span>{{ $isEdit ? 'Edit' : 'Tambah' }}</span>
    </div>

    <div class="row g-4 align-items-start">
        <div class="col-lg-8">
            <div class="surface-strong p-4 p-lg-5">
                <span class="eyebrow mb-2"><i class="bi bi-geo-alt-fill"></i> {{ $isEdit ? 'Edit alamat' : 'Alamat baru' }}</span>
                <h1 class="section-heading h2 mb-3">{{ $isEdit ? 'Perbarui alamat penerima' : 'Tambahkan alamat penerima' }}</h1>
                <p class="section-subtitle mb-4">Isi data penerima, tulis alamat lengkap, lalu tentukan titik lokasi dengan menekan area peta. Latitude dan longitude akan terisi otomatis dari titik yang dipilih.</p>

                <form action="{{ $action }}" method="POST" id="alamatForm">
                    @csrf
                    @if($isEdit) @method('PUT') @endif
                    @if(request('redirect') === 'checkout')
                        <input type="hidden" name="redirect" value="checkout">
                    @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nama penerima</label>
                            <input type="text" name="nama_penerima" value="{{ old('nama_penerima', $alamat->nama_penerima) }}" class="form-control field-modern" style="min-height:52px;border-radius:16px;" placeholder="Contoh: Siti Rahma" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nomor HP penerima</label>
                            <input type="text" name="telepon" value="{{ old('telepon', $alamat->telepon) }}" class="form-control field-modern" style="min-height:52px;border-radius:16px;" placeholder="08xxxxxxxxxx" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Email penerima</label>
                            <input type="email" name="email_penerima" value="{{ old('email_penerima', $alamat->email_penerima) }}" class="form-control field-modern" style="min-height:52px;border-radius:16px;" placeholder="email@example.com" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Alamat lengkap</label>
                            <textarea name="alamat_lengkap" rows="4" class="form-control" style="border-radius:16px;" placeholder="Nama jalan, nomor rumah, RT/RW, desa/kelurahan, kecamatan, kota/kabupaten, dan patokan" required>{{ old('alamat_lengkap', $alamat->alamat_lengkap) }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold mb-2">Titik lokasi di maps</label>
                            <div class="address-map-card">
                                <div class="address-map-toolbar">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-md">
                                            <div class="input-group">
                                                <span class="input-group-text bg-white border-end-0" style="border-radius:15px 0 0 15px;"><i class="bi bi-search"></i></span>
                                                <input type="text" id="mapSearchInput" class="form-control address-map-search border-start-0" placeholder="Cari lokasi, contoh: Blitar, Ponggok, Karang Anyar">
                                            </div>
                                        </div>
                                        <div class="col-md-auto d-flex gap-2">
                                            <button type="button" class="btn btn-soft-brand px-3" id="btnSearchMap"><i class="bi bi-search me-1"></i>Cari</button>
                                            <button type="button" class="btn btn-plain px-3" id="btnUseMyLocation"><i class="bi bi-crosshair me-1"></i>Lokasi Saya</button>
                                        </div>
                                    </div>
                                </div>
                                <div id="alamatMap" data-lat="{{ $defaultLat }}" data-lng="{{ $defaultLng }}" data-has-point="{{ $latValue && $lngValue ? '1' : '0' }}"></div>
                            </div>

                            <input type="hidden" name="latitude" id="latitudeInput" value="{{ $latValue }}">
                            <input type="hidden" name="longitude" id="longitudeInput" value="{{ $lngValue }}">

                            <div class="address-coordinate-box mt-3 d-flex flex-column flex-md-row justify-content-between gap-2">
                                <div>
                                    <div class="fw-black text-dark">Koordinat alamat</div>
                                    <div class="small text-muted fw-semibold" id="coordinateText">
                                        @if($latValue && $lngValue)
                                            {{ $latValue }}, {{ $lngValue }}
                                        @else
                                            Titik belum dipilih. Tekan area peta untuk menentukan lokasi.
                                        @endif
                                    </div>
                                </div>
                                <div class="small text-muted fw-semibold"><i class="bi bi-info-circle me-1"></i>Geser marker jika titik belum tepat.</div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="surface p-3 d-flex gap-3 align-items-start mb-0">
                                <input type="checkbox" name="utama" value="1" class="form-check-input mt-1" {{ old('utama', $alamat->utama) ? 'checked' : '' }}>
                                <span><span class="fw-bold d-block">Jadikan alamat utama</span><span class="small text-muted fw-semibold">Alamat utama otomatis dipilih saat checkout.</span></span>
                            </label>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <button class="btn btn-brand px-4 py-3" type="submit"><i class="bi bi-save me-2"></i> Simpan Alamat</button>
                        <a href="{{ route('pembeli-web.alamat.index') }}" class="btn btn-plain px-4 py-3">Batal</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="surface p-4" style="position: sticky; top: 112px;">
                <h2 class="h4 fw-bold mb-3">Cara menentukan lokasi</h2>
                <div class="d-grid gap-3 text-muted fw-semibold">
                    <div><i class="bi bi-check-circle-fill text-brand me-2"></i>Cari nama daerah atau gunakan tombol Lokasi Saya.</div>
                    <div><i class="bi bi-check-circle-fill text-brand me-2"></i>Tekan titik di peta sesuai alamat penerima.</div>
                    <div><i class="bi bi-check-circle-fill text-brand me-2"></i>Koordinat otomatis tersimpan untuk menghitung area kurir toko.</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mapEl = document.getElementById('alamatMap');
        if (!mapEl || typeof L === 'undefined') return;

        const latInput = document.getElementById('latitudeInput');
        const lngInput = document.getElementById('longitudeInput');
        const coordinateText = document.getElementById('coordinateText');
        const defaultLat = parseFloat(mapEl.dataset.lat || '-7.2575');
        const defaultLng = parseFloat(mapEl.dataset.lng || '112.7521');
        const hasInitialPoint = mapEl.dataset.hasPoint === '1';

        const map = L.map('alamatMap', { scrollWheelZoom: false }).setView([defaultLat, defaultLng], hasInitialPoint ? 16 : 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        let marker = null;
        function setPoint(lat, lng, zoom = 16) {
            const fixedLat = Number(lat).toFixed(7);
            const fixedLng = Number(lng).toFixed(7);
            latInput.value = fixedLat;
            lngInput.value = fixedLng;
            coordinateText.textContent = fixedLat + ', ' + fixedLng;

            if (!marker) {
                marker = L.marker([lat, lng], { draggable: true }).addTo(map);
                marker.on('dragend', function () {
                    const position = marker.getLatLng();
                    setPoint(position.lat, position.lng, map.getZoom());
                });
            } else {
                marker.setLatLng([lat, lng]);
            }
            map.setView([lat, lng], zoom);
        }

        if (hasInitialPoint) {
            setPoint(defaultLat, defaultLng, 16);
        }

        map.on('click', function (event) {
            setPoint(event.latlng.lat, event.latlng.lng, 16);
        });

        document.getElementById('btnUseMyLocation')?.addEventListener('click', function () {
            if (!navigator.geolocation) {
                coordinateText.textContent = 'Browser tidak mendukung akses lokasi. Silakan pilih titik langsung di peta.';
                return;
            }
            coordinateText.textContent = 'Mengambil lokasi perangkat...';
            navigator.geolocation.getCurrentPosition(function (position) {
                setPoint(position.coords.latitude, position.coords.longitude, 17);
            }, function () {
                coordinateText.textContent = 'Lokasi perangkat tidak bisa diambil. Silakan pilih titik langsung di peta.';
            }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 });
        });

        document.getElementById('btnSearchMap')?.addEventListener('click', searchLocation);
        document.getElementById('mapSearchInput')?.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                searchLocation();
            }
        });

        function searchLocation() {
            const input = document.getElementById('mapSearchInput');
            const query = (input?.value || '').trim();
            if (!query) return;
            coordinateText.textContent = 'Mencari lokasi...';
            fetch('https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' + encodeURIComponent(query), {
                headers: { 'Accept': 'application/json' }
            })
                .then(response => response.json())
                .then(results => {
                    if (!results.length) {
                        coordinateText.textContent = 'Lokasi tidak ditemukan. Coba kata kunci lain atau tekan titik langsung di peta.';
                        return;
                    }
                    setPoint(parseFloat(results[0].lat), parseFloat(results[0].lon), 16);
                })
                .catch(() => {
                    coordinateText.textContent = 'Pencarian lokasi gagal. Silakan tekan titik langsung di peta.';
                });
        }
    });
</script>
@endpush
