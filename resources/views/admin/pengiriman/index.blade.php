@extends('layouts.admin')

@section('title', 'Pengambilan & Kirim - SiTahu')
@section('page_title', 'Pengambilan & Kirim')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endpush

@section('content')
@include('admin.partials.ops-page-style')

@php
    $pickupLabel = fn($method) => match($method){'ambil_toko'=>'Ambil toko','kurir_toko'=>'Kurir toko',default=>$statusLabel($method)};
    $paymentMethodLabel = fn($method) => match($method){'transfer_bank'=>'Transfer Bank','cod'=>'COD',default=>strtoupper((string)$method)};
    $nextShip = fn($ship) => \App\Support\OrderFlow::nextShippingStatus($ship);
    $nextOrder = fn($order) => \App\Support\OrderFlow::nextOrderStatus($order);
    $shipActionLabel = function($status, $ship = null) {
        return match($status) {
            'siap_diambil' => 'Siap diambil',
            'dalam_pengantaran' => 'Mulai antar',
            'selesai' => (($ship?->pesanan?->pembayaran?->metode_pembayaran ?? null) === 'cod') ? 'Selesai & bayar COD' : 'Selesaikan',
            default => 'Lanjut',
        };
    };
    $shipFlowText = fn($ship) => $ship?->metode === 'kurir_toko'
        ? 'Disiapkan → Dalam pengantaran → Selesai'
        : 'Disiapkan → Siap diambil → Selesai';
    $tab = request('tab', 'semua');
    $hasActiveFilter = request()->filled('q') || request()->filled('metode') || request()->filled('status') || request()->filled('tanggal_mulai') || request()->filled('tanggal_selesai');
@endphp

<div class="ops-page-head compact">
    <div>
        <h1 class="ops-title">Pengambilan & Kirim</h1>
        <p class="ops-subtitle">Khusus operasional barang yang masih aktif: pesanan sudah disiapkan, siap diambil, atau sedang diantar. Pesanan selesai tersimpan di menu Semua Pesanan.</p>
    </div>
    <button class="btn btn-soft-brand btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#modalLogistikToko"><i class="bi bi-gear me-1"></i> Pengaturan toko</button>
</div>

<div class="ops-tabs">
    <a class="ops-tab {{ $tab==='semua' ? 'active' : '' }}" href="{{ route('admin.pengiriman.index') }}">Semua aktif</a>
    <a class="ops-tab {{ $tab==='belum_diproses' ? 'active' : '' }}" href="{{ route('admin.pengiriman.index', ['tab'=>'belum_diproses']) }}">Siap logistik <b>{{ $stats['belum_diproses'] ?? 0 }}</b></a>
    <a class="ops-tab {{ $tab==='siap_diambil' ? 'active' : '' }}" href="{{ route('admin.pengiriman.index', ['tab'=>'siap_diambil']) }}">Siap diambil <b>{{ $stats['siap_diambil'] ?? 0 }}</b></a>
    <a class="ops-tab {{ $tab==='dalam_pengantaran' ? 'active' : '' }}" href="{{ route('admin.pengiriman.index', ['tab'=>'dalam_pengantaran']) }}">Diantar <b>{{ $stats['dalam_pengantaran'] ?? 0 }}</b></a>
    <a class="ops-tab {{ $tab==='kurir_toko' ? 'active' : '' }}" href="{{ route('admin.pengiriman.index', ['tab'=>'kurir_toko']) }}">Kurir toko <b>{{ $stats['kurir_toko'] ?? 0 }}</b></a>
</div>

<div class="ops-filter-card compact">
    <form id="page-filter" method="GET">
        @if($tab !== 'semua')<input type="hidden" name="tab" value="{{ $tab }}">@endif
        <div class="ops-filter-grid shipping-only">
            <div class="ops-field"><label class="ops-label">Cari</label><div class="ops-search"><i class="bi bi-search text-muted"></i><input name="q" value="{{ request('q') }}" placeholder="Invoice, penerima, HP, atau alamat"></div></div>
            <div class="ops-field"><label class="ops-label">Metode</label><select class="ops-control" name="metode"><option value="">Semua</option><option value="ambil_toko" @selected(request('metode')==='ambil_toko')>Ambil toko</option><option value="kurir_toko" @selected(request('metode')==='kurir_toko')>Kurir toko</option></select></div>
            <div class="ops-field"><label class="ops-label">Status</label><select class="ops-control" name="status"><option value="">Semua</option><option value="belum_diproses" @selected(request('status')==='belum_diproses')>Siap logistik</option>@foreach(['siap_diambil','dalam_pengantaran'] as $s)<option value="{{ $s }}" @selected(request('status')===$s)>{{ $statusLabel($s) }}</option>@endforeach</select></div>
            <div class="ops-field"><label class="ops-label">Dari</label><input type="date" class="ops-control" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}"></div>
            <div class="ops-field"><label class="ops-label">Sampai</label><input type="date" class="ops-control" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}"></div>
            <div class="ops-filter-actions"><button class="ops-btn-apply" type="submit"><i class="bi bi-funnel"></i> Terapkan</button><a href="{{ route('admin.pengiriman.index') }}" class="ops-btn-reset"><i class="bi bi-x-circle"></i> Reset</a></div>
        </div>
        @if($hasActiveFilter || $tab !== 'semua')<div class="ops-filter-note"><i class="bi bi-funnel text-brand"></i> Filter aktif. <a href="{{ route('admin.pengiriman.index') }}" class="text-brand fw-black text-decoration-none">Bersihkan</a></div>@endif
    </form>
</div>

@if($pengiriman->count())
    <div class="ops-table-card table-wrap">
        <table class="table align-middle ops-table-compact">
            <thead><tr><th>Invoice</th><th>Penerima</th><th>Metode</th><th>Alamat</th><th>Ongkir</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
            @foreach($pengiriman as $ship)
                @php
                    $order = $ship->pesanan;
                    $buyer = $order?->user;
                    $pay = $order?->pembayaran;
                    $alamat = $order?->alamatPengiriman;
                    $initial = strtoupper(substr($alamat?->nama_penerima ?: $buyer?->name ?: 'PB',0,2));
                    $next = $nextShip($ship);
                    $alamatTampil = $ship->metode === 'kurir_toko' ? ($ship->alamat_tujuan ?: $alamat?->alamat_lengkap ?: 'Alamat belum tersedia') : ($ship->alamat_toko ?: $pengaturan->alamat ?: 'Alamat toko belum diisi');
                @endphp
                <tr>
                    <td><button type="button" class="ops-link action-modal-btn text-start" data-bs-toggle="modal" data-bs-target="#shipDetail{{ $ship->id }}">{{ $order?->nomor_invoice ?? '-' }}</button><span class="ops-muted">{{ optional($order?->tanggal_pesanan)->format('d M Y H:i') }}</span></td>
                    <td><div class="d-flex align-items-center gap-2 min-w-0"><span class="ops-avatar">{{ $initial }}</span><div class="min-w-0"><div class="fw-black text-truncate">{{ $alamat?->nama_penerima ?: $buyer?->name ?: 'Pembeli' }}</div><span class="ops-muted text-truncate">{{ $alamat?->telepon ?: $buyer?->telepon ?: $buyer?->email }}</span></div></div></td>
                    <td><span class="ops-pill"><i class="bi {{ $ship->metode === 'kurir_toko' ? 'bi-truck text-primary' : 'bi-shop text-warning' }}"></i>{{ $pickupLabel($ship->metode) }}</span></td>
                    <td><div class="fw-bold">{{ $ship->metode === 'kurir_toko' ? 'Alamat tujuan' : 'Alamat toko' }}</div><span class="ops-muted address-one-line">{{ $alamatTampil }}</span></td>
                    <td><div class="fw-black">{{ $rupiah($ship->biaya) }}</div><span class="ops-muted">{{ $ship->jarak_km ? $ship->jarak_km.' km' : 'Tanpa jarak' }}</span></td>
                    <td><span class="chip {{ $statusClass($ship->status_pengiriman ?: 'disiapkan') }}">{{ $ship->status_pengiriman ? $statusLabel($ship->status_pengiriman) : 'Siap logistik' }}</span></td>
                    <td><div class="ops-actions">
                        @if($next)
                            <form method="POST" action="{{ route('admin.pengiriman.status', $ship) }}" data-confirm-title="Lanjutkan logistik" data-confirm-message="{{ $shipFlowText($ship) }}. Lanjutkan {{ $order?->nomor_invoice }} ke tahap {{ $statusLabel($next) }}?" data-confirm-button="Simpan">@csrf @method('PATCH')<input type="hidden" name="status_pengiriman" value="{{ $next }}"><button class="small-btn text-brand" type="submit"><i class="bi bi-arrow-right-circle"></i> {{ $shipActionLabel($next, $ship) }}</button></form>
                        @else
                            <span class="small-btn text-muted"><i class="bi bi-lock"></i> Menunggu tahap</span>
                        @endif
                        <button type="button" class="small-btn" data-bs-toggle="modal" data-bs-target="#shipDetail{{ $ship->id }}"><i class="bi bi-eye"></i> Detail</button>
                    </div></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="ops-empty"><i class="bi bi-truck fs-2 text-muted"></i><strong class="d-block mt-2">Belum ada pengambilan atau pengiriman aktif</strong><span class="text-muted fw-bold small">Pesanan muncul di sini setelah admin menandai pesanan sudah disiapkan.</span></div>
@endif

@foreach($pengiriman as $ship)
    @php
        $order = $ship->pesanan;
        $buyer = $order?->user;
        $pay = $order?->pembayaran;
        $alamat = $order?->alamatPengiriman;
        $alamatTampil = $ship->metode === 'kurir_toko' ? ($ship->alamat_tujuan ?: $alamat?->alamat_lengkap ?: 'Alamat belum tersedia') : ($ship->alamat_toko ?: $pengaturan->alamat ?: 'Alamat toko belum diisi');
        $next = $nextShip($ship);
        $shipSteps = $ship->metode === 'kurir_toko' ? ['disiapkan','dalam_pengantaran','selesai'] : ['disiapkan','siap_diambil','selesai'];
        $shipCurrent = $ship->status_pengiriman ?: 'disiapkan';
        $shipCurrentIndex = array_search($shipCurrent, $shipSteps, true);
    @endphp
    <div class="modal fade" id="shipDetail{{ $ship->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable"><div class="modal-content ops-modal"><div class="modal-header"><div><h5 class="modal-title fw-black">Detail pengambilan/kirim</h5><div class="ops-muted">{{ $order?->nomor_invoice ?? '-' }} · {{ $pickupLabel($ship->metode) }}</div></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body modal-body-soft"><div class="row g-3"><div class="col-md-7"><div class="detail-modal-card mb-3"><span class="detail-label">Penerima</span><div class="detail-value">{{ $alamat?->nama_penerima ?: $buyer?->name ?: '-' }}</div><div class="ops-muted">{{ $alamat?->telepon ?: $buyer?->telepon ?: '-' }} · {{ $alamat?->email_penerima ?: $buyer?->email ?: '-' }}</div></div><div class="detail-modal-card mb-3"><span class="detail-label">Alamat</span><div class="detail-value">{{ $ship->metode === 'kurir_toko' ? 'Alamat tujuan' : 'Alamat toko' }}</div><div class="ops-muted">{{ $alamatTampil }}</div></div><div class="detail-modal-card"><span class="detail-label">Produk</span><div class="detail-list">@foreach($order?->item ?? [] as $item)<div class="detail-product"><div><div class="fw-black">{{ $item->produk?->nama ?? 'Produk' }}</div><div class="ops-muted">{{ $item->jumlah }} item</div></div><strong>{{ $rupiah($item->subtotal) }}</strong></div>@endforeach</div></div></div><div class="col-md-5"><div class="detail-modal-card mb-3"><span class="detail-label">Alur logistik</span><div class="flow-steps">@foreach($shipSteps as $idx => $step)@php $done = $shipCurrentIndex !== false && $idx < $shipCurrentIndex; $current = $shipCurrent === $step; $isNextShipping = $next === $step; @endphp @if($isNextShipping)<form method="POST" action="{{ route('admin.pengiriman.status', $ship) }}" class="flow-step-form" data-confirm-title="Lanjutkan logistik" data-confirm-message="{{ $shipFlowText($ship) }}. Lanjutkan {{ $order?->nomor_invoice }} ke tahap {{ $statusLabel($step) }}?" data-confirm-button="Simpan">@csrf @method('PATCH')<input type="hidden" name="status_pengiriman" value="{{ $step }}"><button class="flow-step action" type="submit"><i class="bi bi-arrow-right-circle"></i>{{ $shipActionLabel($step, $ship) }}</button></form>@else<span class="flow-step {{ $done ? 'done' : ($current ? 'current' : 'locked') }}"><i class="bi {{ $done ? 'bi-check2-circle' : ($current ? 'bi-record-circle' : 'bi-lock') }}"></i>{{ $statusLabel($step) }}</span>@endif @endforeach</div></div><div class="detail-modal-card"><span class="detail-label">Ringkasan</span><div class="summary-row"><span>Metode bayar</span><strong>{{ $paymentMethodLabel($pay?->metode_pembayaran) }}</strong></div><div class="summary-row"><span>Ongkir</span><strong>{{ $rupiah($ship->biaya) }}</strong></div><div class="summary-row"><span>Jarak</span><strong>{{ $ship->jarak_km ? $ship->jarak_km.' km' : '-' }}</strong></div><div class="summary-row"><span>Total</span><strong>{{ $rupiah($order?->total_bayar) }}</strong></div></div></div></div></div></div></div>
    </div>
@endforeach

<div class="ops-footer"><div class="text-muted small fw-bold">Menampilkan {{ $pengiriman->count() }} dari {{ $pengiriman->total() }} pengambilan/kirim aktif.</div><div>{{ $pengiriman->links() }}</div></div>

<div class="modal fade" id="modalLogistikToko" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <form method="POST" action="{{ route('admin.pengiriman.pengaturan.update') }}" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            @csrf @method('PUT')
            <div class="modal-header bg-white border-bottom p-4"><div><h5 class="modal-title fw-black">Pengaturan Pengiriman</h5></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body p-4 modal-body-soft">
                <div class="row g-4">
                    <div class="col-lg-5">
                        <div class="modal-card mb-3"><label class="form-label-modern">Alamat toko</label><textarea class="form-control form-control-modern" rows="4" name="alamat" placeholder="Alamat lengkap titik pengambilan">{{ old('alamat', $pengaturan->alamat) }}</textarea></div>
                        <div class="modal-card"><div class="row g-3"><div class="col-6"><label class="form-label-modern">Jam buka</label><input class="form-control form-control-modern" type="time" name="jam_buka" value="{{ old('jam_buka', $pengaturan->jam_buka) }}"></div><div class="col-6"><label class="form-label-modern">Jam tutup</label><input class="form-control form-control-modern" type="time" name="jam_tutup" value="{{ old('jam_tutup', $pengaturan->jam_tutup) }}"></div><div class="col-6"><label class="form-label-modern">Tarif / km</label><input class="form-control form-control-modern" type="number" min="0" name="tarif_per_km" value="{{ old('tarif_per_km', $pengaturan->tarif_per_km) }}"></div><div class="col-6"><label class="form-label-modern">Minimum ongkir</label><input class="form-control form-control-modern" type="number" min="0" name="biaya_minimum_pengiriman" value="{{ old('biaya_minimum_pengiriman', $pengaturan->biaya_minimum_pengiriman) }}"></div><div class="col-6"><label class="form-label-modern">Radius maksimal</label><input class="form-control form-control-modern" type="number" min="0" step="0.01" name="radius_maksimal_km" value="{{ old('radius_maksimal_km', $pengaturan->radius_maksimal_km) }}"></div><div class="col-6"><label class="form-label-modern">Area layanan</label><input class="form-control form-control-modern" name="area_pengiriman" value="{{ old('area_pengiriman', $pengaturan->area_pengiriman) }}" placeholder="Kota/area"></div></div></div>
                    </div>
                    <div class="col-lg-7"><div class="modal-card"><div class="d-flex justify-content-between gap-2 align-items-start mb-3"><div><div class="fw-black">Titik Lokasi Toko</div></div><button type="button" id="btnUseMyLocation" class="btn btn-light border fw-bold rounded-4"><i class="bi bi-crosshair me-1"></i> Lokasi saya</button></div><input type="hidden" name="latitude_toko" id="latitudeTokoInput" value="{{ old('latitude_toko', $mapLat) }}"><input type="hidden" name="longitude_toko" id="longitudeTokoInput" value="{{ old('longitude_toko', $mapLng) }}"><div id="mapPickerToko" data-lat="{{ old('latitude_toko', $mapLat) }}" data-lng="{{ old('longitude_toko', $mapLng) }}"></div><div class="row g-2 mt-3"><div class="col-6"><div class="summary-row"><span>Latitude</span><strong id="latitudeTokoPreview">{{ old('latitude_toko', $mapLat) }}</strong></div></div><div class="col-6"><div class="summary-row"><span>Longitude</span><strong id="longitudeTokoPreview">{{ old('longitude_toko', $mapLng) }}</strong></div></div></div></div></div>
                </div>
            </div>
            <div class="modal-footer bg-white p-4"><button class="btn btn-light border fw-bold rounded-4" type="button" data-bs-dismiss="modal">Batal</button><button class="btn btn-brand px-4" type="submit">Simpan pengaturan</button></div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const pickerElement = document.getElementById('mapPickerToko');
    if (!pickerElement || typeof L === 'undefined') return;
    let pickerMap = null;
    let pickerMarker = null;
    const latInput = document.getElementById('latitudeTokoInput');
    const lngInput = document.getElementById('longitudeTokoInput');
    const latPreview = document.getElementById('latitudeTokoPreview');
    const lngPreview = document.getElementById('longitudeTokoPreview');
    const modalLogistik = document.getElementById('modalLogistikToko');
    const btnUseMyLocation = document.getElementById('btnUseMyLocation');
    function updatePickedLocation(newLat, newLng) {
        const cleanLat = Number(newLat).toFixed(7);
        const cleanLng = Number(newLng).toFixed(7);
        latInput.value = cleanLat;
        lngInput.value = cleanLng;
        latPreview.textContent = cleanLat;
        lngPreview.textContent = cleanLng;
        if (pickerMarker) pickerMarker.setLatLng([cleanLat, cleanLng]);
    }
    function initPickerMap() {
        if (pickerMap) { setTimeout(() => pickerMap.invalidateSize(), 200); return; }
        const startLat = parseFloat(latInput.value || pickerElement.dataset.lat || '-7.2575');
        const startLng = parseFloat(lngInput.value || pickerElement.dataset.lng || '112.7521');
        pickerMap = L.map('mapPickerToko').setView([startLat, startLng], 16);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom:19, attribution:'&copy; OpenStreetMap'}).addTo(pickerMap);
        pickerMarker = L.marker([startLat, startLng], {draggable:true}).addTo(pickerMap);
        pickerMap.on('click', e => updatePickedLocation(e.latlng.lat, e.latlng.lng));
        pickerMarker.on('dragend', () => { const pos = pickerMarker.getLatLng(); updatePickedLocation(pos.lat, pos.lng); });
        updatePickedLocation(startLat, startLng);
        setTimeout(() => pickerMap.invalidateSize(), 250);
    }
    modalLogistik?.addEventListener('shown.bs.modal', initPickerMap);
    btnUseMyLocation?.addEventListener('click', function () {
        if (!navigator.geolocation) { window.showSitahuToast?.('Browser tidak mendukung fitur lokasi.', 'warning'); return; }
        btnUseMyLocation.disabled = true;
        btnUseMyLocation.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Mencari...';
        navigator.geolocation.getCurrentPosition(function (position) {
            initPickerMap();
            pickerMap.setView([position.coords.latitude, position.coords.longitude], 17);
            updatePickedLocation(position.coords.latitude, position.coords.longitude);
            btnUseMyLocation.disabled = false;
            btnUseMyLocation.innerHTML = '<i class="bi bi-crosshair me-1"></i> Lokasi saya';
        }, function () {
            window.showSitahuToast?.('Gagal mengambil lokasi.', 'error');
            btnUseMyLocation.disabled = false;
            btnUseMyLocation.innerHTML = '<i class="bi bi-crosshair me-1"></i> Lokasi saya';
        }, {enableHighAccuracy:true, timeout:10000});
    });
});
</script>
@endpush
