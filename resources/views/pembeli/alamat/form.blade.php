@extends('layouts.pembeli')

@section('title', ($alamat->exists ? 'Edit Alamat' : 'Tambah Alamat') . ' - SiTahu')

@push('styles')
<style>
    .address-picker-card {
        border: 1px solid var(--line);
        border-radius: 20px;
        background: linear-gradient(180deg, #fff, #fffdf8);
        box-shadow: var(--shadow-xs);
        padding: 18px;
    }
    .address-picker-preview {
        border: 1px dashed rgba(200,147,53,.42);
        background: #fffaf0;
        border-radius: 18px;
        padding: 16px;
    }
    .map-modal .modal-dialog {
        max-width: 900px;
    }
    .map-modal .modal-content {
        border: 0;
        border-radius: 28px;
        overflow: hidden;
        box-shadow: 0 30px 80px rgba(15, 23, 42, .24);
    }
    .map-modal-header {
        padding: 18px 22px;
        background: #fff;
        border-bottom: 1px solid var(--line);
    }
    .map-search-bar {
        padding: 14px;
        background: #fff;
        border-bottom: 1px solid var(--line);
    }
    .map-stage {
        position: relative;
        height: 520px;
        background: #f6efe0;
        overflow: hidden;
        isolation: isolate;
    }
    #alamatMapPicker {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        background: #f6efe0;
        z-index: 1;
    }
    #alamatMapPicker .leaflet-container,
    #alamatMapPicker.leaflet-container {
        width: 100% !important;
        height: 100% !important;
        overflow: hidden !important;
    }
    .map-center-pin-wrap {
        position: absolute;
        inset: 0;
        pointer-events: none;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 500;
    }
    .map-center-pin {
        transform: translateY(-30px);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }
    .map-center-label {
        background: #f35a2a;
        color: #fff;
        font-weight: 800;
        font-size: .95rem;
        padding: 12px 18px;
        border-radius: 999px;
        box-shadow: 0 14px 28px rgba(243, 90, 42, .30);
        position: relative;
        line-height: 1;
        white-space: nowrap;
    }
    .map-center-label::after {
        content: '';
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        bottom: -10px;
        border-left: 10px solid transparent;
        border-right: 10px solid transparent;
        border-top: 12px solid #f35a2a;
    }
    .map-center-pin i {
        font-size: 2.45rem;
        color: #f35a2a;
        line-height: 1;
        filter: drop-shadow(0 12px 18px rgba(0,0,0,.20));
    }
    .map-modal-footer {
        padding: 16px 18px;
        background: #fff;
        border-top: 1px solid var(--line);
    }
    .map-loading-cover {
        position: absolute;
        inset: 0;
        z-index: 50;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fffaf0;
        color: var(--brand-dark);
        font-weight: 800;
    }
    .leaflet-container {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .leaflet-container img,
    .leaflet-container .leaflet-tile {
        max-width: none !important;
        max-height: none !important;
    }
    @media (max-width: 767.98px) {
        .map-modal .modal-dialog {
            margin: 0;
            max-width: 100%;
            height: 100%;
        }
        .map-modal .modal-content {
            min-height: 100vh;
            border-radius: 0;
        }
        .map-stage {
            height: calc(100vh - 245px);
            min-height: 420px;
        }
        .map-search-bar .row {
            gap: 8px;
        }
        .map-search-bar .btn {
            width: 100%;
        }
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
                <p class="section-subtitle mb-4"></p>

                <form action="{{ $action }}" method="POST" id="alamatForm">
                    @csrf
                    @if($isEdit) @method('PUT') @endif
                    @if(request('redirect') === 'checkout')
                        <input type="hidden" name="redirect" value="checkout">
                    @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nama penerima</label>
                            <input type="text" name="nama_penerima" value="{{ old('nama_penerima', $alamat->nama_penerima) }}" class="form-control field-modern" style="min-height:52px;border-radius:16px;" placeholder="Nama penerima" required>
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
                            <div class="address-picker-card">
                                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
                                    <div>
                                        <div class="fw-black text-dark mb-1">Lokasi alamat</div>
                                        <div class="text-muted fw-semibold small" id="coordinateText">
                                            @if($latValue && $lngValue)
                                                {{ $latValue }}, {{ $lngValue }}
                                            @else
                                                Titik lokasi belum dipilih.
                                            @endif
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-brand px-4 py-3" data-bs-toggle="modal" data-bs-target="#mapPickerModal">
                                        <i class="bi bi-geo-alt-fill me-2"></i>Pilih Titik di Maps
                                    </button>
                                </div>
                                <div class="address-picker-preview mt-3 d-flex gap-2 align-items-start">
                                    <i class="bi bi-info-circle text-brand"></i>
                                    <div class="small text-muted fw-semibold">
                                        Maps tidak dimuat langsung supaya halaman tidak berat. Setelah popup terbuka, geser peta hingga pin merah berada tepat di alamat penerima.
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="latitude" id="latitudeInput" value="{{ $latValue }}">
                            <input type="hidden" name="longitude" id="longitudeInput" value="{{ $lngValue }}">
                        </div>

                        <div class="col-12">
                            <label class="surface p-3 d-flex gap-3 align-items-start mb-0">
                                <input type="checkbox" name="utama" value="1" class="form-check-input mt-1" {{ old('utama', $alamat->utama) ? 'checked' : '' }}>
                                <span><span class="fw-bold d-block">Jadikan alamat utama</span></span>
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
                <h2 class="h4 fw-bold mb-3">Lokasi</h2>
                <div class="d-grid gap-3 text-muted fw-semibold">
                    <div><i class="bi bi-check-circle-fill text-brand me-2"></i></div>
                    <div><i class="bi bi-check-circle-fill text-brand me-2"></i>Geser peta sampai pin merah tepat berada di alamat penerima.</div>
                    <div><i class="bi bi-check-circle-fill text-brand me-2"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade map-modal" id="mapPickerModal" tabindex="-1" aria-labelledby="mapPickerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="map-modal-header d-flex align-items-center justify-content-between gap-3">
                <div>
                    <h2 class="h5 fw-black mb-1" id="mapPickerModalLabel">Pilih titik alamat</h2>
                    <div class="small text-muted fw-semibold">Geser peta. Pin merah di tengah adalah lokasi yang akan disimpan.</div>
                </div>
                <button type="button" class="btn btn-plain rounded-circle" data-bs-dismiss="modal" aria-label="Tutup">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="map-search-bar">
                <div class="row g-2 align-items-center">
                    <div class="col-lg">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0" style="border-radius:15px 0 0 15px;"><i class="bi bi-search"></i></span>
                            <input type="text" id="mapSearchInput" class="form-control border-start-0" style="min-height:48px;border-radius:0 15px 15px 0;font-weight:700;" placeholder="Cari lokasi">
                        </div>
                    </div>
                    <div class="col-lg-auto d-flex gap-2 flex-column flex-sm-row">
                        <button type="button" class="btn btn-soft-brand px-3" id="btnSearchMap"><i class="bi bi-search me-1"></i>Cari</button>
                        <button type="button" class="btn btn-plain px-3" id="btnUseMyLocation"><i class="bi bi-crosshair me-1"></i>Lokasi Saya</button>
                    </div>
                </div>
            </div>
            <div class="map-stage">
                <div id="alamatMapPicker" data-lat="{{ $defaultLat }}" data-lng="{{ $defaultLng }}" data-has-point="{{ $latValue && $lngValue ? '1' : '0' }}"></div>
                <div class="map-loading-cover" id="mapLoadingCover">
                    <span><i class="bi bi-map me-2"></i>Menyiapkan maps...</span>
                </div>
                <div class="map-center-pin-wrap" aria-hidden="true">
                    <div class="map-center-pin">
                        <span class="map-center-label">Alamatmu di sini</span>
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                </div>
            </div>
            <div class="map-modal-footer">
                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
                    <div>
                        <div class="small text-muted fw-bold">Koordinat terpilih</div>
                        <div class="fw-black text-dark" id="modalCoordinateText">
                            @if($latValue && $lngValue)
                                {{ $latValue }}, {{ $lngValue }}
                            @else
                                Belum dipilih
                            @endif
                        </div>
                    </div>
                    <button type="button" class="btn btn-brand px-5 py-3" id="btnConfirmMapPoint" data-bs-dismiss="modal">
                        <i class="bi bi-check-circle me-2"></i>Konfirmasi Lokasi
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalEl = document.getElementById('mapPickerModal');
        const mapEl = document.getElementById('alamatMapPicker');
        const latInput = document.getElementById('latitudeInput');
        const lngInput = document.getElementById('longitudeInput');
        const coordinateText = document.getElementById('coordinateText');
        const modalCoordinateText = document.getElementById('modalCoordinateText');
        const loadingCover = document.getElementById('mapLoadingCover');

        if (!modalEl || !mapEl || !latInput || !lngInput) return;

        const defaultLat = parseFloat(mapEl.dataset.lat || '-7.2575');
        const defaultLng = parseFloat(mapEl.dataset.lng || '112.7521');
        const hasInitialPoint = mapEl.dataset.hasPoint === '1';
        let map = null;
        let mapReady = false;

        function loadLeafletAssets(callback) {
            if (window.L) {
                callback();
                return;
            }

            if (!document.querySelector('link[data-leaflet-css]')) {
                const link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                link.crossOrigin = '';
                link.dataset.leafletCss = '1';
                document.head.appendChild(link);
            }

            const existingScript = document.querySelector('script[data-leaflet-js]');
            if (existingScript) {
                existingScript.addEventListener('load', callback, { once: true });
                return;
            }

            const script = document.createElement('script');
            script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
            script.crossOrigin = '';
            script.defer = true;
            script.dataset.leafletJs = '1';
            script.onload = callback;
            document.body.appendChild(script);
        }

        function updateTexts(lat, lng) {
            const fixedLat = Number(lat).toFixed(7);
            const fixedLng = Number(lng).toFixed(7);
            latInput.value = fixedLat;
            lngInput.value = fixedLng;
            coordinateText.textContent = fixedLat + ', ' + fixedLng;
            modalCoordinateText.textContent = fixedLat + ', ' + fixedLng;
        }

        function updateCenterPoint() {
            if (!map) return;
            const center = map.getCenter();
            updateTexts(center.lat, center.lng);
        }

        function initMap() {
            if (mapReady) {
                setTimeout(() => {
                    map.invalidateSize();
                    updateCenterPoint();
                }, 220);
                return;
            }

            loadLeafletAssets(function () {
                if (!window.L || mapReady) return;

                const startLat = parseFloat(latInput.value || defaultLat);
                const startLng = parseFloat(lngInput.value || defaultLng);

                map = L.map('alamatMapPicker', {
                    scrollWheelZoom: true,
                    zoomControl: true,
                    attributionControl: false
                }).setView([startLat, startLng], hasInitialPoint || latInput.value ? 17 : 14);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19
                }).addTo(map);

                map.on('moveend', updateCenterPoint);
                map.on('click', function (event) {
                    map.flyTo(event.latlng, Math.max(map.getZoom(), 17), { duration: .35 });
                });

                mapReady = true;
                setTimeout(() => {
                    map.invalidateSize();
                    updateCenterPoint();
                    if (loadingCover) loadingCover.style.display = 'none';
                }, 300);
            });
        }

        modalEl.addEventListener('shown.bs.modal', initMap);

        document.getElementById('btnConfirmMapPoint')?.addEventListener('click', function () {
            updateCenterPoint();
        });

        document.getElementById('btnUseMyLocation')?.addEventListener('click', function () {
            if (!navigator.geolocation) {
                modalCoordinateText.textContent = 'Browser tidak mendukung akses lokasi. Geser peta secara manual.';
                return;
            }
            modalCoordinateText.textContent = 'Mengambil lokasi perangkat...';
            navigator.geolocation.getCurrentPosition(function (position) {
                if (!map) return;
                map.setView([position.coords.latitude, position.coords.longitude], 18, { animate: true });
                setTimeout(updateCenterPoint, 300);
            }, function () {
                modalCoordinateText.textContent = 'Lokasi perangkat tidak bisa diambil. Geser peta secara manual.';
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
            modalCoordinateText.textContent = 'Mencari lokasi...';
            fetch('https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' + encodeURIComponent(query), {
                headers: { 'Accept': 'application/json' }
            })
                .then(response => response.json())
                .then(results => {
                    if (!results.length) {
                        modalCoordinateText.textContent = 'Lokasi tidak ditemukan.';
                        return;
                    }
                    if (!map) return;
                    map.setView([parseFloat(results[0].lat), parseFloat(results[0].lon)], 17, { animate: true });
                    setTimeout(updateCenterPoint, 300);
                })
                .catch(() => {
                    modalCoordinateText.textContent = 'Pencarian lokasi gagal. Geser peta secara manual.';
                });
        }
    });
</script>
@endpush
