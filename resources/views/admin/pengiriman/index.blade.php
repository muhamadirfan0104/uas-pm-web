@extends('layouts.admin')
@section('title', 'Pengambilan & Pengantaran - SiTahu')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    /* Styling Standar Enterprise */
    .sc-box { border: 1px solid #e5e7eb; border-radius: 0.75rem; background: #fff; margin-bottom: 1.5rem; overflow: hidden; box-shadow: 0 1px 2px rgba(0,0,0,0.02); }
    .sc-header { padding: 1.25rem 1.5rem; font-weight: 700; font-size: 1rem; border-bottom: 1px solid #f3f4f6; color: #111827; display: flex; align-items: center; gap: 0.5rem; }
    
    /* Tabel Enterprise */
    .table-enterprise th { border-bottom: 2px solid #e5e7eb; color: #6b7280; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 1rem 1.5rem; font-weight: 600; background: #fafafa; }
    .table-enterprise td { vertical-align: middle; padding: 1rem 1.5rem; border-bottom: 1px solid #f3f4f6; color: #111827; }
    .table-enterprise tbody tr:hover { background-color: #f9fafb; }
    
    /* Form & Input Modern */
    .form-label-modern { font-size: 0.85rem; font-weight: 700; color: #374151; margin-bottom: 0.4rem; display: block; }
    .form-control-modern, .form-select-modern { background-color: #f9fafb; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.6rem 0.75rem; font-size: 0.9rem; transition: all 0.2s; box-shadow: none; width: 100%; }
    .form-control-modern:focus, .form-select-modern:focus { background-color: #ffffff; border-color: var(--brand-color, #dfba68); box-shadow: 0 0 0 3px rgba(223, 186, 104, 0.15); outline: none; }
    .form-select-inline { background-color: #f9fafb; border: 1px solid #e5e7eb; font-size: 0.85rem; font-weight: 500; color: #374151; box-shadow: none; transition: all 0.2s; }
    .form-select-inline:focus { background-color: #ffffff; border-color: var(--brand-color, #dfba68); box-shadow: 0 0 0 3px rgba(223, 186, 104, 0.15); }
    
    /* Peta Styling */
    .leaflet-container { font-family: inherit; z-index: 1; }
    #mapToko { height: 100%; min-height: 280px; width: 100%; background: #e5e7eb; }
    #mapPickerToko { height: 360px; width: 100%; border-radius: 0.5rem; border: 1px solid #e5e7eb; }
    
    /* List Item Custom */
    .info-list-item { padding: 1rem 0; border-bottom: 1px dashed #e5e7eb; display: flex; justify-content: space-between; align-items: center; }
    .info-list-item:last-child { border-bottom: none; padding-bottom: 0; }
</style>
@endpush

@section('content')
@php
    $mapLat = $pengaturan->latitude_toko ?: -6.917464;
    $mapLng = $pengaturan->longitude_toko ?: 107.619123;
@endphp

<!-- HEADER UTAMA -->
<div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3 mb-4">
    <div>
        <h1 class="h4 fw-bold text-dark mb-1">Pengambilan & Pengantaran</h1>
        <p class="text-muted small mb-0">Kelola status pesanan logistik dan area jangkauan toko.</p>
    </div>
    <div>
        <button class="btn shadow-sm fw-bold px-4 text-white d-flex align-items-center gap-2" type="button" data-bs-toggle="modal" data-bs-target="#modalLogistikToko" style="background: var(--brand-color, #dfba68);">
            <i class="bi bi-gear-fill"></i> Atur Logistik Toko
        </button>
    </div>
</div>

<!-- BARIS 1: INFO KONFIGURASI & PETA -->
<div class="row g-4 mb-4">
    <div class="col-12 col-xl-6">
        <div class="sc-box h-100 mb-0 d-flex flex-column">
            <div class="sc-header bg-light">
                <i class="bi bi-sliders text-muted"></i> Konfigurasi Logistik Saat Ini
            </div>
            <div class="p-4 flex-grow-1 d-flex flex-column justify-content-center">
                <div class="info-list-item pt-0">
                    <span class="text-muted small fw-bold text-uppercase"><i class="bi bi-shop me-2"></i>Alamat Titik Toko</span>
                    <span class="text-dark fw-medium text-end" style="max-width: 60%; font-size: 0.9rem;">{{ $pengaturan->alamat ?? 'Belum diatur' }}</span>
                </div>
                <div class="info-list-item">
                    <span class="text-muted small fw-bold text-uppercase"><i class="bi bi-cash me-2"></i>Biaya Min. Pengiriman</span>
                    <span class="text-dark fw-bold fs-5">{{ $rupiah($pengaturan->biaya_minimum_pengiriman) }}</span>
                </div>
                <div class="info-list-item">
                    <span class="text-muted small fw-bold text-uppercase"><i class="bi bi-signpost-split me-2"></i>Tarif per KM</span>
                    <span class="text-dark fw-bold fs-5">{{ $rupiah($pengaturan->tarif_per_km) }}</span>
                </div>
                <div class="info-list-item pb-0">
                    <span class="text-muted small fw-bold text-uppercase"><i class="bi bi-radar me-2"></i>Radius Maksimal</span>
                    <span class="badge bg-info-subtle text-info-emphasis rounded-pill px-3 py-2 fs-6 shadow-sm">{{ $pengaturan->radius_maksimal_km ?? '0' }} KM</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-6">
        <div class="sc-box h-100 mb-0 d-flex flex-column">
            <div class="sc-header bg-light">
                <i class="bi bi-geo-alt text-danger"></i> Titik Toko di Maps
            </div>
            <div class="flex-grow-1 position-relative">
                <div id="mapToko" data-lat="{{ $mapLat }}" data-lng="{{ $mapLng }}" data-title="{{ $pengaturan->nama ?? 'SiTahu' }}" data-address="{{ $pengaturan->alamat ?? 'Alamat belum diatur' }}"></div>
            </div>
            <div class="bg-light p-2 text-center border-top text-muted" style="font-size: 0.75rem;">
                Koordinat GPS Aktif: <strong class="text-dark">{{ $mapLat }}, {{ $mapLng }}</strong>
            </div>
        </div>
    </div>
</div>

<!-- BARIS 2: TABEL DATA PENGIRIMAN -->
<div class="sc-box mb-4">
    <!-- Filter Bar -->
    <div class="bg-white border-bottom p-3 p-md-4">
        <form id="page-filter" class="js-instant-filter" method="GET">
            <div class="row g-3 align-items-center">
                <div class="col-12 col-md-5 col-lg-4">
                    <select class="form-select form-select-modern fw-medium" name="metode" style="min-height: 44px;" onchange="this.form.submit()">
                        <option value="">Semua Metode Logistik</option>
                        <option value="ambil_toko" @selected(request('metode')==='ambil_toko')>Ambil di Toko</option>
                        <option value="kurir_toko" @selected(request('metode')==='kurir_toko')>Kurir Toko (Diantar)</option>
                    </select>
                </div>
                <div class="col-12 col-md-4 col-lg-3">
                    <select class="form-select form-select-modern fw-medium" name="status" style="min-height: 44px;" onchange="this.form.submit()">
                        <option value="">Semua Status Pengiriman</option>
                        @foreach(['siap_diambil','dalam_pengantaran','selesai'] as $s)
                            <option value="{{ $s }}" @selected(request('status')===$s)>{{ $statusLabel($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3 d-none d-md-block">
                    <div class="text-muted small"><i class="bi bi-lightning-charge me-1"></i>Filter otomatis.</div>
                </div>
            </div>
        </form>
    </div>

    <!-- Tabel -->
    <div class="table-responsive bg-white">
        <table class="table table-enterprise table-borderless mb-0" style="min-width: 900px;">
            <thead>
                <tr>
                    <th class="ps-4">No. Pesanan</th>
                    <th>Detail Pengiriman / Alamat</th>
                    <th>Biaya Logistik</th>
                    <th>Status Saat Ini</th>
                    <th class="pe-4 text-end">Update Status</th>
                </tr>
            </thead>
            <tbody>
            @forelse($pengiriman as $ship)
                @php
                    $badgeStatus = match($ship->status_pengiriman) {
                        'selesai' => 'bg-success-subtle text-success-emphasis',
                        'dalam_pengantaran' => 'bg-primary-subtle text-primary-emphasis',
                        default => 'bg-warning-subtle text-warning-emphasis',
                    };
                @endphp
                <tr>
                    <td class="ps-4">
                        <strong class="d-block text-dark mb-1" style="font-size: 0.95rem;">{{ $ship->pesanan?->nomor_invoice ?? 'INV-UNKNOWN' }}</strong>
                        <div class="text-muted small">Pembeli: <span class="fw-medium text-dark">{{ $ship->pesanan?->user?->name ?? 'Anonim' }}</span></div>
                    </td>
                    <td>
                        <div class="d-flex align-items-start gap-3">
                            <div class="mt-1 flex-shrink-0">
                                @if($ship->metode === 'ambil_toko')
                                    <div class="bg-warning-subtle text-warning-emphasis rounded-3 d-flex align-items-center justify-content-center" style="width:36px;height:36px;"><i class="bi bi-shop fs-5"></i></div>
                                @else
                                    <div class="bg-primary-subtle text-primary-emphasis rounded-3 d-flex align-items-center justify-content-center" style="width:36px;height:36px;"><i class="bi bi-truck fs-5"></i></div>
                                @endif
                            </div>
                            <div>
                                <span class="badge bg-light text-secondary border rounded-pill mb-1 fw-medium" style="font-size: 0.7rem;">{{ $statusLabel($ship->metode) }}</span>
                                <div class="text-dark small lh-sm" style="max-width: 250px;">{{ $ship->alamat_tujuan ?: $ship->alamat_toko }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($ship->biaya > 0)
                            <strong class="text-dark fs-6">{{ $rupiah($ship->biaya) }}</strong>
                        @else
                            <span class="badge bg-success-subtle text-success-emphasis rounded-pill">Gratis</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge rounded-pill fw-medium px-3 py-2 shadow-sm {{ $badgeStatus }}" style="font-size: 0.75rem;">{{ $statusLabel($ship->status_pengiriman) }}</span>
                    </td>
                    <td class="pe-4">
                        <form method="POST" action="{{ route('admin.pengiriman.status', $ship) }}" class="d-flex justify-content-end align-items-center gap-2 m-0" data-confirm-title="Ubah Status Pengiriman" data-confirm-message="Yakin ingin menyimpan perubahan status pengiriman pesanan {{ $ship->pesanan?->nomor_invoice ?? 'ini' }}?" data-confirm-button="Simpan Status">
                            @csrf @method('PATCH')
                            <select class="form-select form-select-sm form-select-inline rounded-3" name="status_pengiriman" style="width: 170px; height: 36px;">
                                @foreach(['siap_diambil','dalam_pengantaran','selesai'] as $s)
                                    <option value="{{ $s }}" @selected($ship->status_pengiriman === $s)>Ubah ke: {{ $statusLabel($s) }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-sm btn-dark rounded-3 px-3 fw-medium" type="submit" style="height: 36px;">Simpan</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <i class="bi bi-truck-flatbed fs-1 text-muted mb-3 d-block"></i>
                        <strong class="text-dark d-block mb-1">Belum ada data pengiriman aktif.</strong>
                        <span class="text-muted small">Pesanan dengan metode ambil/kurir akan otomatis muncul di sini.</span>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($pengiriman->hasPages())
        <div class="bg-light border-top p-3">{{ $pengiriman->links() }}</div>
    @endif
</div>

<!-- ============================================== -->
<!-- MODAL: ATUR LOGISTIK TOKO                      -->
<!-- ============================================== -->
<div class="modal fade" id="modalLogistikToko" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form method="POST" action="{{ route('admin.pengiriman.pengaturan.update') }}" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            @csrf @method('PUT')
            
            <div class="modal-header bg-white border-bottom p-4">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-1">Pengaturan Logistik Toko</h5>
                    <div class="text-muted small">Titik koordinat digunakan untuk API Maps dan penentuan tarif.</div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            
            <div class="modal-body p-4 p-md-5" style="background-color:#f9fafb;">
                <div class="row g-4">
                    
                    <!-- Informasi Alamat & Jam -->
                    <div class="col-12">
                        <div class="bg-white p-4 rounded-4 shadow-sm border border-light">
                            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-shop text-muted me-2"></i>Informasi Fisik Toko</h6>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label-modern">Alamat Lengkap / Titik Pengambilan</label>
                                    <textarea class="form-control form-control-modern" rows="2" name="alamat">{{ old('alamat', $pengaturan->alamat) }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-modern">Jam Buka</label>
                                    <input class="form-control form-control-modern" type="time" name="jam_buka" value="{{ old('jam_buka', $pengaturan->jam_buka) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-modern">Jam Tutup</label>
                                    <input class="form-control form-control-modern" type="time" name="jam_tutup" value="{{ old('jam_tutup', $pengaturan->jam_tutup) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pemilih Peta -->
                    <div class="col-12">
                        <div class="bg-white p-4 rounded-4 shadow-sm border border-light">
                            <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-3">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1"><i class="bi bi-geo-alt text-danger me-2"></i>Titik Lokasi GPS</h6>
                                    <span class="text-muted small">Klik peta atau geser marker untuk menentukan lokasi presisi.</span>
                                </div>
                                <button class="btn btn-sm btn-light border fw-medium align-self-start shadow-sm" type="button" id="btnUseMyLocation">
                                    <i class="bi bi-crosshair me-1 text-primary"></i> Gunakan GPS Saya
                                </button>
                            </div>

                            <input type="hidden" name="latitude_toko" id="latitudeTokoInput" value="{{ old('latitude_toko', $mapLat) }}">
                            <input type="hidden" name="longitude_toko" id="longitudeTokoInput" value="{{ old('longitude_toko', $mapLng) }}">

                            <div id="mapPickerToko" class="shadow-sm" data-lat="{{ old('latitude_toko', $mapLat) }}" data-lng="{{ old('longitude_toko', $mapLng) }}"></div>

                            <div class="row g-2 mt-3 p-3 bg-light rounded-3 border">
                                <div class="col-6">
                                    <div class="small text-muted fw-bold text-uppercase" style="font-size:0.7rem;">Latitude Terpilih</div>
                                    <div class="font-monospace text-dark" id="latitudeTokoPreview">{{ old('latitude_toko', $mapLat) }}</div>
                                </div>
                                <div class="col-6 border-start">
                                    <div class="small text-muted fw-bold text-uppercase ms-2" style="font-size:0.7rem;">Longitude Terpilih</div>
                                    <div class="font-monospace text-dark ms-2" id="longitudeTokoPreview">{{ old('longitude_toko', $mapLng) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Parameter Tarif -->
                    <div class="col-12">
                        <div class="bg-white p-4 rounded-4 shadow-sm border border-light">
                            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-calculator text-muted me-2"></i>Parameter Tarif & Jangkauan</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label-modern">Tarif per Kilometer (Rp)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-light text-muted fw-bold">Rp</span>
                                        <input class="form-control form-control-modern border-start-0" style="border-top-left-radius:0; border-bottom-left-radius:0;" type="number" min="0" name="tarif_per_km" value="{{ old('tarif_per_km', $pengaturan->tarif_per_km) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-modern">Biaya Minimum (Rp)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-light text-muted fw-bold">Rp</span>
                                        <input class="form-control form-control-modern border-start-0" style="border-top-left-radius:0; border-bottom-left-radius:0;" type="number" min="0" name="biaya_minimum_pengiriman" value="{{ old('biaya_minimum_pengiriman', $pengaturan->biaya_minimum_pengiriman) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-modern">Radius Maksimal Antar</label>
                                    <div class="input-group">
                                        <input class="form-control form-control-modern border-end-0" style="border-top-right-radius:0; border-bottom-right-radius:0;" type="number" min="0" step="0.01" name="radius_maksimal_km" value="{{ old('radius_maksimal_km', $pengaturan->radius_maksimal_km) }}">
                                        <span class="input-group-text bg-light border-light text-muted fw-bold">KM</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-modern">Area Teks (Opsional)</label>
                                    <input class="form-control form-control-modern" name="area_pengiriman" value="{{ old('area_pengiriman', $pengaturan->area_pengiriman) }}" placeholder="Misal: Kota Bandung">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            
            <div class="modal-footer bg-white border-top p-4 d-flex justify-content-between">
                <button class="btn btn-light border fw-medium px-4" type="button" data-bs-dismiss="modal">Batal</button>
                <button class="btn fw-bold px-4 shadow-sm text-white" type="submit" style="background: var(--brand-color, #dfba68);">Simpan Konfigurasi Logistik</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const mapElement = document.getElementById('mapToko');
    const pickerElement = document.getElementById('mapPickerToko');

    if (!mapElement || typeof L === 'undefined') return;

    const lat = parseFloat(mapElement.dataset.lat || '-6.917464');
    const lng = parseFloat(mapElement.dataset.lng || '107.619123');
    const title = mapElement.dataset.title || 'SiTahu';
    const address = mapElement.dataset.address || 'Alamat belum diatur';

    // Map Utama Statis
    const map = L.map('mapToko').setView([lat, lng], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    L.marker([lat, lng]).addTo(map)
        .bindPopup('<strong class="text-dark">' + title + '</strong><br><span class="small text-muted">' + address + '</span>')
        .openPopup();

    setTimeout(() => map.invalidateSize(), 250);

    // Map Picker di dalam Modal
    let pickerMap = null;
    let pickerMarker = null;

    const latInput = document.getElementById('latitudeTokoInput');
    const lngInput = document.getElementById('longitudeTokoInput');
    const latPreview = document.getElementById('latitudeTokoPreview');
    const lngPreview = document.getElementById('longitudeTokoPreview');
    const btnUseMyLocation = document.getElementById('btnUseMyLocation');
    const modalLogistik = document.getElementById('modalLogistikToko');

    function updatePickedLocation(newLat, newLng) {
        const cleanLat = Number(newLat).toFixed(7);
        const cleanLng = Number(newLng).toFixed(7);

        if (latInput) latInput.value = cleanLat;
        if (lngInput) lngInput.value = cleanLng;
        if (latPreview) latPreview.textContent = cleanLat;
        if (lngPreview) lngPreview.textContent = cleanLng;

        if (pickerMarker) {
            pickerMarker.setLatLng([cleanLat, cleanLng]);
        }
    }

    function initPickerMap() {
        if (!pickerElement || pickerMap) {
            if (pickerMap) setTimeout(() => pickerMap.invalidateSize(), 200);
            return;
        }

        const startLat = parseFloat(latInput?.value || pickerElement.dataset.lat || lat);
        const startLng = parseFloat(lngInput?.value || pickerElement.dataset.lng || lng);

        pickerMap = L.map('mapPickerToko').setView([startLat, startLng], 16);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(pickerMap);

        pickerMarker = L.marker([startLat, startLng], { draggable: true }).addTo(pickerMap)
            .bindPopup('<span class="small fw-medium">Geser marker untuk ubah titik</span>')
            .openPopup();

        pickerMap.on('click', function (event) {
            updatePickedLocation(event.latlng.lat, event.latlng.lng);
        });

        pickerMarker.on('dragend', function () {
            const pos = pickerMarker.getLatLng();
            updatePickedLocation(pos.lat, pos.lng);
        });

        updatePickedLocation(startLat, startLng);
        setTimeout(() => pickerMap.invalidateSize(), 250);
    }

    modalLogistik?.addEventListener('shown.bs.modal', function () {
        initPickerMap();
    });

    btnUseMyLocation?.addEventListener('click', function () {
        if (!navigator.geolocation) {
            alert('Browser tidak mendukung fitur lokasi.');
            return;
        }

        btnUseMyLocation.disabled = true;
        btnUseMyLocation.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Mencari...';

        navigator.geolocation.getCurrentPosition(
            function (position) {
                const currentLat = position.coords.latitude;
                const currentLng = position.coords.longitude;

                initPickerMap();
                pickerMap.setView([currentLat, currentLng], 17);
                updatePickedLocation(currentLat, currentLng);

                btnUseMyLocation.disabled = false;
                btnUseMyLocation.innerHTML = '<i class="bi bi-crosshair me-1 text-primary"></i> Gunakan GPS Saya';
            },
            function () {
                alert('Gagal mengambil lokasi. Pastikan izin lokasi browser aktif.');
                btnUseMyLocation.disabled = false;
                btnUseMyLocation.innerHTML = '<i class="bi bi-crosshair me-1 text-primary"></i> Gunakan GPS Saya';
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    });
});
</script>
@endpush