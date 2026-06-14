@extends('layouts.admin')

@section('title', 'Pengaturan Toko - SiTahu')
@section('page_title', 'Pengaturan Toko')
@push('styles')
<style>
    .settings-wrap { max-width: 1120px; margin: 0 auto; }
    .setting-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        padding: 22px;
        border-radius: 26px;
        border: 1px solid #f1d49c;
        background:
            radial-gradient(circle at right top, rgba(200,147,53,.18), transparent 18rem),
            linear-gradient(135deg, #fff, #fff8ea);
        box-shadow: var(--shadow-soft);
        margin-bottom: 18px;
    }
    .setting-hero h1 { margin: 0; font-size: 1.42rem; font-weight: 950; letter-spacing: -.045em; }
    .setting-hero p { margin: 7px 0 0; color: var(--muted); font-size: .86rem; font-weight: 650; line-height: 1.55; max-width: 760px; }
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
    .coord-card {
        display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px;
        padding: 15px; border-radius: 18px; border: 1px dashed rgba(200,147,53,.38);
        background: #fffaf0;
    }
    .summary-card { padding: 14px; border: 1px solid var(--border); border-radius: 18px; background: #fff; }
    .summary-card .label { color: var(--muted); font-size: .72rem; font-weight: 950; text-transform: uppercase; letter-spacing: .05em; }
    .summary-card .value { margin-top: 6px; color: var(--text); font-weight: 950; font-size: .94rem; }
    .rekening-card {
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: 14px;
        background: linear-gradient(180deg, #fff, #fffdf8);
    }
    .rekening-card + .rekening-card { margin-top: 12px; }
    .rekening-card-head { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom: 12px; }
    .rekening-card-title { font-size: .85rem; font-weight: 950; color: var(--brand-dark); }
    .sticky-save {
        position: sticky; bottom: 16px; z-index: 10;
        display: flex; justify-content: flex-end; gap: 10px; margin-top: 12px;
        padding: 12px; border: 1px solid var(--border); border-radius: 20px; background: rgba(255,255,255,.88); backdrop-filter: blur(16px); box-shadow: var(--shadow-soft);
    }
    .map-modal .modal-dialog { max-width: 900px; }
    .map-modal .modal-content { border: 0; border-radius: 28px; overflow: hidden; box-shadow: 0 30px 80px rgba(15,23,42,.24); }
    .map-modal-header { padding: 18px 22px; background: #fff; border-bottom: 1px solid var(--border); }
    .map-search-bar { padding: 14px; background: #fff; border-bottom: 1px solid var(--border); }
    .map-stage { position: relative; height: 520px; background: #f6efe0; overflow: hidden; isolation: isolate; }
    #storeMapPicker { position: absolute; inset: 0; width: 100%; height: 100%; overflow: hidden; background: #f6efe0; z-index: 1; }
    #storeMapPicker .leaflet-container, #storeMapPicker.leaflet-container { width: 100% !important; height: 100% !important; overflow: hidden !important; }
    .map-center-pin-wrap { position: absolute; inset: 0; pointer-events: none; display: flex; align-items: center; justify-content: center; z-index: 500; }
    .map-center-pin { transform: translateY(-30px); display: flex; flex-direction: column; align-items: center; gap: 10px; }
    .map-center-label { background: #f35a2a; color: #fff; font-weight: 850; font-size: .95rem; padding: 12px 18px; border-radius: 999px; box-shadow: 0 14px 28px rgba(243,90,42,.30); position: relative; line-height: 1; white-space: nowrap; }
    .map-center-label::after { content:''; position:absolute; left:50%; transform:translateX(-50%); bottom:-10px; border-left:10px solid transparent; border-right:10px solid transparent; border-top:12px solid #f35a2a; }
    .map-center-pin i { font-size: 2.45rem; color:#f35a2a; line-height:1; filter: drop-shadow(0 12px 18px rgba(0,0,0,.20)); }
    .map-modal-footer { padding: 16px 18px; background:#fff; border-top:1px solid var(--border); }
    .map-loading-cover { position:absolute; inset:0; z-index:50; display:flex; align-items:center; justify-content:center; background:#fffaf0; color:var(--brand-dark); font-weight:850; }
    .leaflet-container { font-family: 'Plus Jakarta Sans', sans-serif; }
    .leaflet-container img, .leaflet-container .leaflet-tile { max-width: none !important; max-height: none !important; }
    @media(max-width: 760px){
        .setting-hero{ align-items:flex-start; flex-direction:column; }
        .form-grid-2,.form-grid-3{ grid-template-columns:1fr; }
        .map-modal .modal-dialog { margin:0; max-width:100%; height:100%; }
        .map-modal .modal-content { min-height:100vh; border-radius:0; }
        .map-stage { height: calc(100vh - 245px); min-height:420px; }
    }
</style>
@endpush

@section('content')
@php
    $mapsLink = ($pengaturan->latitude_toko && $pengaturan->longitude_toko)
        ? 'https://www.google.com/maps?q=' . $pengaturan->latitude_toko . ',' . $pengaturan->longitude_toko
        : null;

    $rekeningRows = collect(old('rekening'));
    if ($rekeningRows->isEmpty()) {
        $rekeningRows = ($rekeningList ?? collect())->map(fn($rekening) => [
            'nama_bank' => $rekening->nama_bank,
            'nomor_rekening' => $rekening->nomor_rekening,
            'atas_nama' => $rekening->atas_nama,
            'aktif' => $rekening->aktif,
            'utama' => $rekening->utama,
        ]);
    }
    if ($rekeningRows->isEmpty() && ($pengaturan->bank_nama || $pengaturan->bank_nomor_rekening || $pengaturan->bank_atas_nama)) {
        $rekeningRows = collect([[
            'nama_bank' => $pengaturan->bank_nama,
            'nomor_rekening' => $pengaturan->bank_nomor_rekening,
            'atas_nama' => $pengaturan->bank_atas_nama,
            'aktif' => true,
            'utama' => true,
        ]]);
    }
    if ($rekeningRows->isEmpty()) {
        $rekeningRows = collect([[
            'nama_bank' => '',
            'nomor_rekening' => '',
            'atas_nama' => $pengaturan->nama ?: 'SiTahu',
            'aktif' => true,
            'utama' => true,
        ]]);
    }
    $rekeningUtamaIndex = (int) old('rekening_utama', max(0, $rekeningRows->search(fn($item) => (bool) ($item['utama'] ?? false))));
@endphp

<form method="POST" action="{{ route('admin.pengaturan.update') }}" enctype="multipart/form-data" class="settings-wrap">
    @csrf
    @method('PUT')

    <div class="setting-hero">
        <div>
            <span class="chip c-yellow mb-2">Data aktif di halaman pembeli</span>
            <h1>Pengaturan toko</h1>
            <p>Pengaturan operasional toko.</p>
        </div>
        <div class="logo-preview">
            @if($pengaturan->logo_url)
                <img src="{{ asset('storage/' . $pengaturan->logo_url) }}" alt="Logo {{ $pengaturan->nama }}">
            @else
                {{ strtoupper(substr($pengaturan->nama ?? 'ST', 0, 2)) }}
            @endif
        </div>
    </div>

    <section class="setting-section" id="identitas">
        <div class="setting-section-head">
            <div><h2>Identitas toko</h2><p>Nama, logo, kontak, alamat tertulis, dan deskripsi singkat toko.</p></div>
            <span class="chip c-gray">Wajib dicek</span>
        </div>
        <div class="setting-section-body">
            <div class="form-grid-2">
                <div class="setting-field">
                    <label for="nama">Nama toko</label>
                    <input class="form-control" id="nama" name="nama" value="{{ old('nama', $pengaturan->nama) }}" placeholder="Nama toko" required>
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
            <div><h2>Lokasi Toko</h2></div>
            @if($mapsLink)<a href="{{ $mapsLink }}" target="_blank" class="small-btn"><i class="bi bi-map"></i> Buka Google Maps</a>@endif
        </div>
        <div class="setting-section-body">
            <div class="coord-card">
                <div>
                    <div class="fw-black text-dark mb-1">Titik lokasi toko</div>
                    <div class="text-muted fw-semibold small" id="coordPreview">
                        {{ $pengaturan->latitude_toko && $pengaturan->longitude_toko ? $pengaturan->latitude_toko . ', ' . $pengaturan->longitude_toko : 'Belum dipilih' }}
                    </div>
                </div>
                <button type="button" class="btn btn-brand px-4 py-3" data-bs-toggle="modal" data-bs-target="#storeMapModal">
                    <i class="bi bi-geo-alt-fill me-2"></i>Pilih Titik Toko
                </button>
            </div>

            <input type="hidden" id="latitude_toko" name="latitude_toko" value="{{ old('latitude_toko', $pengaturan->latitude_toko) }}">
            <input type="hidden" id="longitude_toko" name="longitude_toko" value="{{ old('longitude_toko', $pengaturan->longitude_toko) }}">
        </div>
    </section>

    <section class="setting-section" id="pengiriman">
        <div class="setting-section-head">
            <div><h2>Pengiriman</h2></div>
        </div>
        <div class="setting-section-body">
            <div class="form-grid-2">
                <div class="setting-field">
                    <label for="tarif_per_km">Tarif per KM</label>
                    <input class="form-control" type="number" id="tarif_per_km" name="tarif_per_km" min="0" step="100" value="{{ old('tarif_per_km', $pengaturan->tarif_per_km) }}" placeholder="3000">
                    <div class="hint">Biaya kurir dihitung dari jarak alamat pembeli ke titik toko tanpa batas radius.</div>
                </div>
                <div class="setting-field">
                    <label for="biaya_minimum_pengiriman">Biaya minimum</label>
                    <input class="form-control" type="number" id="biaya_minimum_pengiriman" name="biaya_minimum_pengiriman" min="0" step="100" value="{{ old('biaya_minimum_pengiriman', $pengaturan->biaya_minimum_pengiriman) }}" placeholder="5000">
                </div>
            </div>
            <div class="setting-field mt-3">
                <label for="area_pengiriman">Area pengiriman</label>
                <textarea class="form-control" id="area_pengiriman" name="area_pengiriman" placeholder="Area layanan">{{ old('area_pengiriman', $pengaturan->area_pengiriman) }}</textarea>
            </div>
        </div>
    </section>

    <section class="setting-section" id="pembayaran">
        <div class="setting-section-head">
            <div><h2>Rekening transfer bank</h2><p>Admin bisa menambahkan lebih dari satu rekening. Rekening utama diprioritaskan tampil pertama ke pembeli.</p></div>
            <button type="button" class="btn btn-soft-brand btn-sm px-3" id="btnAddRekening"><i class="bi bi-plus-circle me-1"></i>Tambah Rekening</button>
        </div>
        <div class="setting-section-body">
            <div id="rekeningList">
                @foreach($rekeningRows as $index => $rekening)
                    <div class="rekening-card" data-rekening-row>
                        <div class="rekening-card-head">
                            <div class="rekening-card-title"><i class="bi bi-bank me-1"></i> Rekening {{ $index + 1 }}</div>
                            <button type="button" class="btn btn-link text-danger text-decoration-none fw-bold btn-sm" data-remove-rekening>Hapus</button>
                        </div>
                        <div class="form-grid-3">
                            <div class="setting-field">
                                <label>Nama bank</label>
                                <input class="form-control" name="rekening[{{ $index }}][nama_bank]" value="{{ old("rekening.$index.nama_bank", $rekening['nama_bank'] ?? '') }}" placeholder="BCA / BRI / Mandiri">
                            </div>
                            <div class="setting-field">
                                <label>Nomor rekening</label>
                                <input class="form-control" name="rekening[{{ $index }}][nomor_rekening]" value="{{ old("rekening.$index.nomor_rekening", $rekening['nomor_rekening'] ?? '') }}" placeholder="1234567890">
                            </div>
                            <div class="setting-field">
                                <label>Atas nama</label>
                                <input class="form-control" name="rekening[{{ $index }}][atas_nama]" value="{{ old("rekening.$index.atas_nama", $rekening['atas_nama'] ?? '') }}" placeholder="SiTahu Premium">
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-3 mt-3">
                            <label class="d-flex align-items-center gap-2 fw-bold mb-0">
                                <input type="radio" name="rekening_utama" value="{{ $index }}" class="form-check-input" {{ $rekeningUtamaIndex === $index ? 'checked' : '' }}>
                                Rekening utama
                            </label>
                            <label class="d-flex align-items-center gap-2 fw-bold mb-0">
                                <input type="checkbox" name="rekening[{{ $index }}][aktif]" value="1" class="form-check-input" {{ old("rekening.$index.aktif", $rekening['aktif'] ?? true) ? 'checked' : '' }}>
                                Aktif ditampilkan
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="setting-field mt-3">
                <label for="info_pembayaran">Catatan pembayaran</label>
                <textarea class="form-control" id="info_pembayaran" name="info_pembayaran" placeholder="Instruksi pembayaran untuk pembeli">{{ old('info_pembayaran', $pengaturan->info_pembayaran) }}</textarea>
            </div>
        </div>
    </section>

    <section class="setting-section" id="preview">
        <div class="setting-section-head">
            <div><h2>Ringkasan</h2></div>
        </div>
        <div class="setting-section-body">
            <div class="grid g4">
                <div class="summary-card"><div class="label">Toko</div><div class="value">{{ $pengaturan->nama }}</div></div>
                <div class="summary-card"><div class="label">Telepon</div><div class="value">{{ $pengaturan->telepon ?: '-' }}</div></div>
                <div class="summary-card"><div class="label">Tarif/km</div><div class="value">Rp {{ number_format((float) $pengaturan->tarif_per_km, 0, ',', '.') }}</div></div>
            </div>
        </div>
    </section>

    <div class="sticky-save">
        <button type="submit" class="btn btn-brand px-4"><i class="bi bi-save me-1"></i>Simpan Pengaturan</button>
    </div>

    <div class="modal fade map-modal" id="storeMapModal" tabindex="-1" aria-labelledby="storeMapModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="map-modal-header d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <h2 class="h5 fw-black mb-1" id="storeMapModalLabel">Pilih titik toko</h2>
                        <div class="small text-muted fw-semibold">Geser peta. Pin merah di tengah adalah lokasi toko yang akan disimpan.</div>
                    </div>
                    <button type="button" class="btn btn-plain rounded-circle" data-bs-dismiss="modal" aria-label="Tutup"><i class="bi bi-x-lg"></i></button>
                </div>
                <div class="map-search-bar">
                    <div class="row g-2 align-items-center">
                        <div class="col-lg">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0" style="border-radius:15px 0 0 15px;"><i class="bi bi-search"></i></span>
                                <input type="text" id="storeMapSearchInput" class="form-control border-start-0" style="min-height:48px;border-radius:0 15px 15px 0;font-weight:700;" placeholder="Cari lokasi toko...">
                            </div>
                        </div>
                        <div class="col-lg-auto d-flex gap-2 flex-column flex-sm-row">
                            <button type="button" class="btn btn-soft-brand px-3" id="btnStoreSearchMap"><i class="bi bi-search me-1"></i>Cari</button>
                            <button type="button" class="btn btn-plain px-3" id="btnUseStoreLocation"><i class="bi bi-crosshair me-1"></i>Lokasi Saya</button>
                        </div>
                    </div>
                </div>
                <div class="map-stage">
                    <div id="storeMapPicker" data-lat="{{ old('latitude_toko', $pengaturan->latitude_toko ?: -7.8480) }}" data-lng="{{ old('longitude_toko', $pengaturan->longitude_toko ?: 112.0178) }}" data-has-point="{{ old('latitude_toko', $pengaturan->latitude_toko) && old('longitude_toko', $pengaturan->longitude_toko) ? '1' : '0' }}"></div>
                    <div class="map-loading-cover" id="storeMapLoadingCover"><span><i class="bi bi-map me-2"></i>Menyiapkan maps...</span></div>
                    <div class="map-center-pin-wrap" aria-hidden="true">
                        <div class="map-center-pin">
                            <span class="map-center-label">Toko di sini</span>
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                    </div>
                </div>
                <div class="map-modal-footer">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
                        <div>
                            <div class="small text-muted fw-bold">Koordinat toko</div>
                            <div class="fw-black text-dark" id="storeModalCoordinateText">{{ $pengaturan->latitude_toko && $pengaturan->longitude_toko ? $pengaturan->latitude_toko . ', ' . $pengaturan->longitude_toko : 'Belum dipilih' }}</div>
                        </div>
                        <button type="button" class="btn btn-brand px-5 py-3" id="btnConfirmStorePoint" data-bs-dismiss="modal"><i class="bi bi-check-circle me-2"></i>Konfirmasi Lokasi</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<template id="rekeningTemplate">
    <div class="rekening-card" data-rekening-row>
        <div class="rekening-card-head">
            <div class="rekening-card-title"><i class="bi bi-bank me-1"></i> Rekening <span data-row-number></span></div>
            <button type="button" class="btn btn-link text-danger text-decoration-none fw-bold btn-sm" data-remove-rekening>Hapus</button>
        </div>
        <div class="form-grid-3">
            <div class="setting-field">
                <label>Nama bank</label>
                <input class="form-control" data-name="nama_bank" placeholder="BCA / BRI / Mandiri">
            </div>
            <div class="setting-field">
                <label>Nomor rekening</label>
                <input class="form-control" data-name="nomor_rekening" placeholder="1234567890">
            </div>
            <div class="setting-field">
                <label>Atas nama</label>
                <input class="form-control" data-name="atas_nama" placeholder="SiTahu Premium">
            </div>
        </div>
        <div class="d-flex flex-wrap gap-3 mt-3">
            <label class="d-flex align-items-center gap-2 fw-bold mb-0">
                <input type="radio" name="rekening_utama" class="form-check-input" data-name="utama">
                Rekening utama
            </label>
            <label class="d-flex align-items-center gap-2 fw-bold mb-0">
                <input type="checkbox" value="1" class="form-check-input" data-name="aktif" checked>
                Aktif ditampilkan
            </label>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const rekeningList = document.getElementById('rekeningList');
    const template = document.getElementById('rekeningTemplate');

    function refreshRekeningNames() {
        rekeningList?.querySelectorAll('[data-rekening-row]').forEach((row, index) => {
            row.querySelector('[data-row-number]')?.replaceChildren(document.createTextNode(index + 1));
            row.querySelectorAll('[data-name]').forEach((input) => {
                const name = input.dataset.name;
                if (name === 'utama') {
                    input.name = 'rekening_utama';
                    input.value = index;
                    return;
                }
                input.name = `rekening[${index}][${name}]`;
            });
            row.querySelector('.rekening-card-title').innerHTML = `<i class="bi bi-bank me-1"></i> Rekening ${index + 1}`;
        });

        const rows = rekeningList?.querySelectorAll('[data-rekening-row]') || [];
        if (rows.length === 1) {
            rows[0].querySelector('[data-remove-rekening]')?.classList.add('d-none');
            const utama = rows[0].querySelector('input[type="radio"][name="rekening_utama"]');
            if (utama) utama.checked = true;
        } else {
            rows.forEach(row => row.querySelector('[data-remove-rekening]')?.classList.remove('d-none'));
        }
    }

    document.getElementById('btnAddRekening')?.addEventListener('click', () => {
        const node = template.content.cloneNode(true);
        rekeningList.appendChild(node);
        refreshRekeningNames();
    });

    rekeningList?.addEventListener('click', (event) => {
        const btn = event.target.closest('[data-remove-rekening]');
        if (!btn) return;
        const rows = rekeningList.querySelectorAll('[data-rekening-row]');
        if (rows.length <= 1) return;
        btn.closest('[data-rekening-row]')?.remove();
        refreshRekeningNames();
    });

    refreshRekeningNames();

    const modalEl = document.getElementById('storeMapModal');
    const mapEl = document.getElementById('storeMapPicker');
    const latInput = document.getElementById('latitude_toko');
    const lngInput = document.getElementById('longitude_toko');
    const coordPreview = document.getElementById('coordPreview');
    const modalCoord = document.getElementById('storeModalCoordinateText');
    const loadingCover = document.getElementById('storeMapLoadingCover');
    let map = null;
    let mapReady = false;

    function loadLeaflet(callback) {
        if (window.L) { callback(); return; }
        if (!document.querySelector('link[data-leaflet-css]')) {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
            link.dataset.leafletCss = '1';
            document.head.appendChild(link);
        }
        const existing = document.querySelector('script[data-leaflet-js]');
        if (existing) {
            existing.addEventListener('load', callback, { once: true });
            return;
        }
        const script = document.createElement('script');
        script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
        script.defer = true;
        script.dataset.leafletJs = '1';
        script.onload = callback;
        document.body.appendChild(script);
    }

    function updateTexts(lat, lng) {
        const fixedLat = Number(lat).toFixed(8);
        const fixedLng = Number(lng).toFixed(8);
        latInput.value = fixedLat;
        lngInput.value = fixedLng;
        coordPreview.textContent = `${fixedLat}, ${fixedLng}`;
        modalCoord.textContent = `${fixedLat}, ${fixedLng}`;
    }

    function updateCenter() {
        if (!map) return;
        const center = map.getCenter();
        updateTexts(center.lat, center.lng);
    }

    function initStoreMap() {
        if (!mapEl) return;
        if (mapReady) {
            setTimeout(() => {
                map.invalidateSize();
                updateCenter();
            }, 220);
            return;
        }

        loadLeaflet(() => {
            if (!window.L || mapReady) return;
            const startLat = parseFloat(latInput.value || mapEl.dataset.lat || '-7.8480');
            const startLng = parseFloat(lngInput.value || mapEl.dataset.lng || '112.0178');
            const hasPoint = mapEl.dataset.hasPoint === '1' || (latInput.value && lngInput.value);

            map = L.map('storeMapPicker', {
                scrollWheelZoom: true,
                zoomControl: true,
                attributionControl: false
            }).setView([startLat, startLng], hasPoint ? 17 : 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

            map.on('moveend', updateCenter);
            map.on('click', (event) => {
                map.flyTo(event.latlng, Math.max(map.getZoom(), 17), { duration: .35 });
            });

            mapReady = true;
            setTimeout(() => {
                map.invalidateSize();
                updateCenter();
                if (loadingCover) loadingCover.style.display = 'none';
            }, 300);
        });
    }

    modalEl?.addEventListener('shown.bs.modal', initStoreMap);
    document.getElementById('btnConfirmStorePoint')?.addEventListener('click', updateCenter);

    document.getElementById('btnUseStoreLocation')?.addEventListener('click', () => {
        if (!navigator.geolocation) {
            modalCoord.textContent = 'Browser tidak mendukung akses lokasi. Geser peta secara manual.';
            return;
        }
        modalCoord.textContent = 'Mengambil lokasi perangkat...';
        navigator.geolocation.getCurrentPosition(
            (position) => {
                if (!map) return;
                map.setView([position.coords.latitude, position.coords.longitude], 18, { animate: true });
                setTimeout(updateCenter, 300);
            },
            () => { modalCoord.textContent = 'Lokasi perangkat tidak bisa diambil. Geser peta secara manual.'; },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    });

    async function searchStoreLocation() {
        const keyword = document.getElementById('storeMapSearchInput')?.value?.trim();
        if (!keyword) return;
        modalCoord.textContent = 'Mencari lokasi...';
        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(keyword)}&limit=1`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();
            if (!data.length) {
                modalCoord.textContent = 'Lokasi tidak ditemukan.';
                return;
            }
            if (!map) return;
            map.setView([parseFloat(data[0].lat), parseFloat(data[0].lon)], 17, { animate: true });
            setTimeout(updateCenter, 300);
        } catch (error) {
            modalCoord.textContent = 'Pencarian lokasi tidak tersedia.';
        }
    }

    document.getElementById('btnStoreSearchMap')?.addEventListener('click', searchStoreLocation);
    document.getElementById('storeMapSearchInput')?.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            searchStoreLocation();
        }
    });
});
</script>
@endpush
