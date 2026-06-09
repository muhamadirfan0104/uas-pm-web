@extends('layouts.pembeli')

@section('title', 'Alamat Saya - SiTahu')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIINfQz36ySLBQOjaYj9n2L8Kt2nHLxH0wM=" crossorigin="">
<style>
    .address-card-clickable {
        cursor: pointer;
        transition: .2s ease;
        border: 1px solid var(--line);
    }
    .address-card-clickable:hover {
        transform: translateY(-2px);
        border-color: rgba(200,147,53,.34);
        box-shadow: var(--shadow-sm);
    }
    .address-detail-row {
        border: 1px solid var(--line);
        border-radius: 18px;
        padding: 14px 16px;
        background: #fff;
    }
    .address-detail-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .04em;
        font-weight: 900;
        color: var(--muted);
        margin-bottom: 5px;
    }
    .address-detail-map {
        height: 260px;
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid var(--line);
        background: var(--brand-soft);
    }
    .address-action-button { position: relative; z-index: 2; }

    .profile-icon {
        width: 42px;
        height: 42px;
        border-radius: 16px;
        display: grid;
        place-items: center;
        background: var(--brand-soft);
        border: 1px solid rgba(200,147,53,.20);
        color: var(--brand-dark);
        flex: 0 0 auto;
    }
</style>
@endpush

@section('content')
<div class="container py-4 py-lg-5">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3 mb-4">
        <div>
            <div class="breadcrumb-modern">
                <a href="{{ route('pembeli-web.home') }}">Beranda</a>
                <i class="bi bi-chevron-right small"></i>
                <span>Alamat</span>
            </div>
            <span class="eyebrow mb-2"><i class="bi bi-geo-alt-fill"></i> Alamat penerima</span>
            <h1 class="section-heading h2 mb-2">Alamat Saya</h1>
            <p class="section-subtitle mb-0">Simpan beberapa alamat dengan nama penerima berbeda. Tekan card alamat untuk melihat detail lengkap.</p>
        </div>
        <a href="{{ route('pembeli-web.alamat.create') }}" class="btn btn-brand px-4 py-3"><i class="bi bi-plus-circle me-2"></i> Tambah Alamat</a>
    </div>

    @if($alamat->count())
        <div class="row g-3 g-lg-4">
            @foreach($alamat as $item)
                @php
                    $modalId = 'alamatDetailModal' . $item->id;
                    $hasCoordinate = filled($item->latitude) && filled($item->longitude);
                    $mapsUrl = $hasCoordinate ? 'https://www.google.com/maps?q=' . $item->latitude . ',' . $item->longitude : null;
                @endphp
                <div class="col-lg-6">
                    <article class="surface-strong p-4 h-100 address-card-clickable" role="button" tabindex="0" data-bs-toggle="modal" data-bs-target="#{{ $modalId }}">
                        <div class="d-flex justify-content-between gap-3 mb-3">
                            <div class="min-w-0">
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                    <h2 class="h4 fw-bold mb-0 text-truncate">{{ $item->nama_penerima }}</h2>
                                    @if($item->utama)
                                        <span class="badge rounded-pill text-bg-success fw-bold px-3 py-2">Utama</span>
                                    @endif
                                </div>
                                <div class="text-muted fw-semibold"><i class="bi bi-phone me-1"></i>{{ $item->telepon }}</div>
                                @if($item->email_penerima)
                                    <div class="text-muted small fw-semibold"><i class="bi bi-envelope me-1"></i>{{ $item->email_penerima }}</div>
                                @endif
                            </div>
                            <div class="profile-icon flex-shrink-0"><i class="bi bi-pin-map"></i></div>
                        </div>

                        <div class="p-3 rounded-4 bg-light border mb-3">
                            <div class="small text-muted fw-bold text-uppercase mb-1">Alamat lengkap</div>
                            <div class="fw-semibold">{{ \Illuminate\Support\Str::limit($item->alamat_lengkap, 150) }}</div>
                        </div>

                        <div class="d-flex flex-wrap gap-2 address-action-button" onclick="event.stopPropagation();">
                            <button type="button" class="btn btn-soft-brand btn-sm px-3" data-bs-toggle="modal" data-bs-target="#{{ $modalId }}">Detail</button>
                            <a href="{{ route('pembeli-web.alamat.edit', $item) }}" class="btn btn-plain btn-sm px-3">Edit</a>
                            @if(! $item->utama)
                                <form action="{{ route('pembeli-web.alamat.utama', $item) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-plain btn-sm px-3" type="submit">Jadikan Utama</button>
                                </form>
                            @endif
                            <form action="{{ route('pembeli-web.alamat.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus alamat ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-link text-danger fw-bold text-decoration-none btn-sm px-2" type="submit">Hapus</button>
                            </form>
                        </div>
                    </article>
                </div>

                <div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content border-0" style="border-radius:26px; overflow:hidden;">
                            <div class="modal-header border-0 pb-0 px-4 pt-4">
                                <div>
                                    <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                                        <span class="eyebrow mb-0"><i class="bi bi-geo-alt-fill"></i> Detail alamat</span>
                                        @if($item->utama)
                                            <span class="badge rounded-pill text-bg-success fw-bold px-3 py-2">Alamat utama</span>
                                        @endif
                                    </div>
                                    <h2 class="h4 fw-black mb-0">{{ $item->nama_penerima }}</h2>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <div class="address-detail-row h-100">
                                            <div class="address-detail-label">Nomor HP</div>
                                            <div class="fw-black"><i class="bi bi-phone text-brand me-2"></i>{{ $item->telepon }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="address-detail-row h-100">
                                            <div class="address-detail-label">Email penerima</div>
                                            <div class="fw-black text-break"><i class="bi bi-envelope text-brand me-2"></i>{{ $item->email_penerima ?: '-' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="address-detail-row">
                                            <div class="address-detail-label">Alamat lengkap</div>
                                            <div class="fw-semibold lh-lg">{{ $item->alamat_lengkap }}</div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        @if($hasCoordinate)
                                            <div class="address-detail-map js-address-detail-map" id="alamatMapDetail{{ $item->id }}" data-lat="{{ $item->latitude }}" data-lng="{{ $item->longitude }}"></div>
                                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
                                                <div class="small text-muted fw-semibold"><i class="bi bi-crosshair me-1"></i>{{ $item->latitude }}, {{ $item->longitude }}</div>
                                                <a href="{{ $mapsUrl }}" target="_blank" rel="noopener" class="btn btn-soft-brand btn-sm px-3"><i class="bi bi-map me-1"></i>Buka Maps</a>
                                            </div>
                                        @else
                                            <div class="address-detail-row text-center py-4">
                                                <div class="profile-icon mx-auto mb-3"><i class="bi bi-map"></i></div>
                                                <h3 class="h6 fw-black mb-1">Titik maps belum dipilih</h3>
                                                <p class="small text-muted fw-semibold mb-3">Edit alamat ini untuk menentukan titik lokasi langsung dari peta.</p>
                                                <a href="{{ route('pembeli-web.alamat.edit', $item) }}" class="btn btn-brand btn-sm px-3">Pilih Titik Maps</a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-0 pt-0 px-4 pb-4">
                                <a href="{{ route('pembeli-web.alamat.edit', $item) }}" class="btn btn-brand px-4"><i class="bi bi-pencil-square me-2"></i>Edit Alamat</a>
                                <button type="button" class="btn btn-plain px-4" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="surface-strong p-4 p-lg-5 text-center">
            <div class="stat-icon mx-auto mb-3"><i class="bi bi-geo-alt"></i></div>
            <h2 class="h3 fw-bold">Belum ada alamat.</h2>
            <p class="text-muted mb-4">Tambahkan alamat agar checkout lebih cepat.</p>
            <a href="{{ route('pembeli-web.alamat.create') }}" class="btn btn-brand px-4 py-3">Tambah Alamat</a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const initializedMaps = new Set();
        document.querySelectorAll('.modal').forEach(function (modalEl) {
            modalEl.addEventListener('shown.bs.modal', function () {
                const mapEl = modalEl.querySelector('.js-address-detail-map');
                if (!mapEl || initializedMaps.has(mapEl.id) || typeof L === 'undefined') return;

                const lat = parseFloat(mapEl.dataset.lat);
                const lng = parseFloat(mapEl.dataset.lng);
                const map = L.map(mapEl.id, { scrollWheelZoom: false }).setView([lat, lng], 16);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap'
                }).addTo(map);
                L.marker([lat, lng]).addTo(map);
                initializedMaps.add(mapEl.id);
                setTimeout(function () { map.invalidateSize(); }, 180);
            });
        });
    });
</script>
@endpush
