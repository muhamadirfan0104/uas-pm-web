@extends('layouts.pembeli')

@section('title', ($alamat->exists ? 'Edit Alamat' : 'Tambah Alamat') . ' - SiTahu')

@push('styles')
<style>
    .address-form-page {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 340px;
        gap: 18px;
        align-items: start;
    }

    .form-hero {
        padding: 24px;
        margin-bottom: 18px;
        background:
            radial-gradient(circle at top right, rgba(223, 186, 104, 0.24), transparent 34%),
            linear-gradient(135deg, #ffffff 0%, #fff8e8 100%);
    }

    .form-hero h1 {
        margin: 10px 0 0;
        color: var(--heading);
        font-size: clamp(30px, 4vw, 44px);
        line-height: 1.04;
        letter-spacing: -0.07em;
    }

    .form-hero h1 span {
        color: var(--brand-dark);
    }

    .form-hero p {
        margin: 10px 0 0;
        max-width: 720px;
        color: var(--muted);
        font-size: 14px;
        line-height: 1.7;
    }

    .form-card {
        padding: 24px;
    }

    .form-grid {
        display: grid;
        gap: 16px;
    }

    .form-row-2 {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: var(--heading);
        font-size: 13px;
        font-weight: 900;
    }

    .form-control {
        width: 100%;
        min-height: 48px;
        padding: 12px 14px;
        border-radius: 15px;
        border: 1px solid var(--line);
        background: #ffffff;
        color: var(--heading);
        outline: none;
        font-size: 14px;
    }

    .form-control:focus {
        border-color: rgba(223, 186, 104, 0.75);
        box-shadow: 0 0 0 4px rgba(223, 186, 104, 0.16);
    }

    textarea.form-control {
        min-height: 130px;
        resize: vertical;
        line-height: 1.7;
    }

    .checkbox-row {
        padding: 13px 14px;
        border-radius: 16px;
        border: 1px solid var(--line);
        background: #f9fafb;
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }

    .checkbox-row input {
        margin-top: 3px;
    }

    .checkbox-row strong {
        display: block;
        color: var(--heading);
        font-size: 13.5px;
        font-weight: 950;
    }

    .checkbox-row span {
        display: block;
        margin-top: 4px;
        color: var(--muted);
        font-size: 12.5px;
        line-height: 1.5;
    }

    .alert-error {
        margin-bottom: 16px;
        padding: 13px 15px;
        border-radius: 16px;
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
        font-size: 13.5px;
        font-weight: 800;
    }

    .form-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .helper-card {
        padding: 20px;
        position: sticky;
        top: 94px;
    }

    .helper-card h2 {
        margin: 0;
        color: var(--heading);
        font-size: 21px;
        letter-spacing: -0.045em;
    }

    .helper-card p {
        margin: 8px 0 0;
        color: var(--muted);
        font-size: 13.5px;
        line-height: 1.65;
    }

    .helper-list {
        margin-top: 16px;
        display: grid;
        gap: 10px;
    }

    .helper-item {
        padding: 13px;
        border-radius: 16px;
        background: #f9fafb;
        border: 1px solid var(--line);
        color: var(--muted);
        font-size: 13px;
        line-height: 1.55;
    }

    .helper-item strong {
        display: block;
        margin-bottom: 4px;
        color: var(--heading);
    }

    @media (max-width: 920px) {
        .address-form-page {
            grid-template-columns: 1fr;
        }

        .helper-card {
            position: static;
        }
    }

    @media (max-width: 560px) {
        .form-hero,
        .form-card,
        .helper-card {
            padding: 18px;
        }

        .form-row-2 {
            grid-template-columns: 1fr;
        }

        .form-actions .btn {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
@php
    $isEdit = $alamat->exists;

    $action = $isEdit
        ? route('pembeli-web.alamat.update', $alamat)
        : route('pembeli-web.alamat.store');
@endphp

<section class="page-card form-hero">
    <div class="badge">Alamat Saya</div>

    <h1>
        {{ $isEdit ? 'Edit' : 'Tambah' }} <span>alamat pengiriman</span>
    </h1>

    <p>
        Lengkapi nama penerima, nomor telepon, alamat lengkap, dan koordinat jika tersedia.
        Koordinat membantu admin membuka titik lokasi melalui maps.
    </p>
</section>

@if($errors->any())
    <div class="alert-error">
        {{ $errors->first() }}
    </div>
@endif

<div class="address-form-page">
    <main class="page-card form-card">
        <form action="{{ $action }}" method="POST" class="form-grid">
            @csrf

            @if($isEdit)
                @method('PUT')
            @endif

            <div class="form-row-2">
                <div class="form-group">
                    <label for="nama_penerima">Nama Penerima</label>
                    <input
                        type="text"
                        name="nama_penerima"
                        id="nama_penerima"
                        class="form-control"
                        value="{{ old('nama_penerima', $alamat->nama_penerima) }}"
                        placeholder="Contoh: Sabrina Martha"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="telepon">Nomor Telepon</label>
                    <input
                        type="text"
                        name="telepon"
                        id="telepon"
                        class="form-control"
                        value="{{ old('telepon', $alamat->telepon) }}"
                        placeholder="Contoh: 081234567890"
                        required
                    >
                </div>
            </div>

            <div class="form-group">
                <label for="alamat_lengkap">Alamat Lengkap</label>
                <textarea
                    name="alamat_lengkap"
                    id="alamat_lengkap"
                    class="form-control"
                    placeholder="Contoh: Jl. ..., RT/RW, Kelurahan, Kecamatan, Kota"
                    required
                >{{ old('alamat_lengkap', $alamat->alamat_lengkap) }}</textarea>
            </div>

            <div class="form-row-2">
                <div class="form-group">
                    <label for="latitude">Latitude</label>
                    <input
                        type="text"
                        name="latitude"
                        id="latitude"
                        class="form-control"
                        value="{{ old('latitude', $alamat->latitude) }}"
                        placeholder="Contoh: -7.8166"
                    >
                </div>

                <div class="form-group">
                    <label for="longitude">Longitude</label>
                    <input
                        type="text"
                        name="longitude"
                        id="longitude"
                        class="form-control"
                        value="{{ old('longitude', $alamat->longitude) }}"
                        placeholder="Contoh: 112.0119"
                    >
                </div>
            </div>

            <label class="checkbox-row">
                <input
                    type="checkbox"
                    name="utama"
                    value="1"
                    {{ old('utama', $alamat->utama) ? 'checked' : '' }}
                >

                <div>
                    <strong>Jadikan alamat utama</strong>
                    <span>
                        Alamat utama akan diprioritaskan saat checkout menggunakan kurir toko.
                    </span>
                </div>
            </label>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Alamat' }}
                </button>

                <a href="{{ route('pembeli-web.alamat.index') }}" class="btn btn-outline">
                    Kembali
                </a>
            </div>
        </form>
    </main>

    <aside class="page-card helper-card">
        <h2>Bantuan Koordinat</h2>

        <p>
            Latitude dan longitude boleh dikosongkan, tapi kalau diisi,
            admin bisa langsung membuka alamat pembeli melalui maps.
        </p>

        <div class="helper-list">
            <div class="helper-item">
                <strong>Cara ambil koordinat</strong>
                Buka Google Maps, klik kanan titik alamat, lalu salin angka latitude dan longitude.
            </div>

            <div class="helper-item">
                <strong>Contoh format</strong>
                Latitude: -7.8166<br>
                Longitude: 112.0119
            </div>

            <div class="helper-item">
                <strong>Catatan</strong>
                Untuk demo, input manual koordinat sudah cukup. Maps interaktif bisa jadi pengembangan berikutnya.
            </div>
        </div>
    </aside>
</div>
@endsection