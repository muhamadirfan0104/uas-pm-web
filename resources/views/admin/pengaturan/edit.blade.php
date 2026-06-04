@extends('layouts.admin')
@section('title','Pengaturan Toko - SiTahu')

@section('content')
<style>
    /* Styling Standar Form E-Commerce */
    .sc-box { border: 1px solid #e5e7eb; border-radius: 0.75rem; background: #fff; overflow: hidden; box-shadow: 0 1px 2px rgba(0,0,0,0.02); }
    .sc-header { padding: 1.25rem 1.5rem; font-weight: 700; font-size: 1rem; border-bottom: 1px solid #f3f4f6; color: #111827; display: flex; align-items: center; gap: 0.5rem; }
    .sc-body { padding: 2rem 1.5rem; }
    .sc-label { font-size: 0.85rem; font-weight: 700; color: #374151; margin-bottom: 0.4rem; display: block; }
    
    .sc-input { background-color: #f9fafb; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.6rem 0.75rem; font-size: 0.9rem; color: #111827; transition: all 0.2s; width: 100%; box-shadow: none; }
    .sc-input:focus { background-color: #ffffff; border-color: var(--brand-color, #dfba68); box-shadow: 0 0 0 3px rgba(223, 186, 104, 0.15); outline: none; }
    
    .sc-group-text { background-color: #f3f4f6; border: 1px solid #d1d5db; font-size: 0.9rem; color: #4b5563; font-weight: 600; }
    
    /* Area Upload Logo */
    .upload-area { border: 2px dashed #d1d5db; border-radius: 0.75rem; background-color: #f9fafb; padding: 2rem 1.5rem; text-align: center; cursor: pointer; position: relative; transition: all 0.2s; }
    .upload-area:hover { border-color: var(--brand-color, #dfba68); background-color: rgba(223, 186, 104, 0.05); }
    .upload-area input[type="file"] { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; }
</style>

<!-- HEADER -->
<div class="mb-4">
    <h1 class="h4 fw-bold text-dark mb-1">Pengaturan Toko</h1>
    <p class="text-muted small mb-0">Kelola profil dan identitas utama toko untuk ditampilkan di aplikasi.</p>
</div>

<form id="formPengaturan" method="POST" action="{{ route('admin.pengaturan.update') }}" enctype="multipart/form-data">
    @csrf @method('PUT')

    <!-- LAYOUT TERPUSAT -->
    <div class="row justify-content-center">
        <div class="col-12 col-xl-9">
            <div class="sc-box">
                <div class="sc-header bg-light">
                    <i class="bi bi-shop text-muted"></i> Profil & Identitas Utama
                </div>
                <div class="sc-body">
                    
                    <!-- Area Upload Logo -->
                    <label class="sc-label mb-2">Logo Toko Resmi</label>
                    <div class="d-flex flex-column flex-sm-row gap-4 align-items-center mb-4 pb-4 border-bottom">
                        @if($pengaturan->logo_url)
                            <div class="flex-shrink-0 bg-white border rounded-4 p-2 shadow-sm" style="width: 120px; height: 120px;">
                                <img src="{{ asset('storage/'.$pengaturan->logo_url) }}" style="width: 100%; height: 100%; object-fit: contain; border-radius: 0.5rem;" alt="Logo Toko">
                            </div>
                        @else
                            <div class="flex-shrink-0 bg-light border rounded-4 d-flex align-items-center justify-content-center text-muted shadow-sm" style="width: 120px; height: 120px;">
                                <i class="bi bi-image fs-1"></i>
                            </div>
                        @endif
                        
                        <div class="upload-area flex-grow-1 w-100">
                            <i class="bi bi-cloud-upload text-muted fs-3 mb-2 d-block"></i>
                            <strong class="text-dark">Pilih atau Tarik Logo Kesini</strong>
                            <div class="text-muted small mt-1">Format JPG/PNG maksimal 2MB. Diutamakan rasio kotak 1:1.</div>
                            <input type="file" name="logo" accept="image/*">
                        </div>
                    </div>

                    <!-- Form Identitas -->
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="sc-label">Nama Toko</label>
                            <input class="form-control sc-input form-control-lg fw-bold" name="nama" value="{{ old('nama', $pengaturan->nama) }}" placeholder="Masukkan nama toko...">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="sc-label">WhatsApp / Telepon</label>
                            <div class="input-group">
                                <span class="input-group-text sc-group-text border-end-0"><i class="bi bi-whatsapp text-success"></i></span>
                                <input class="form-control sc-input border-start-0" name="telepon" value="{{ old('telepon', $pengaturan->telepon) }}" placeholder="081234567890">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="sc-label">Email Operasional</label>
                            <div class="input-group">
                                <span class="input-group-text sc-group-text border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                                <input class="form-control sc-input border-start-0" type="email" name="email" value="{{ old('email', $pengaturan->email) }}" placeholder="toko@email.com">
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="sc-label">Deskripsi / Tentang Toko</label>
                            <textarea class="form-control sc-input" rows="5" name="tentang" placeholder="Ceritakan singkat tentang toko tahu Anda...">{{ old('tentang', $pengaturan->tentang) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Tombol Simpan (Satu-satunya tombol) -->
            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <button class="btn shadow-sm fw-bold px-5 text-white" type="submit" style="background: var(--brand-color, #dfba68);">
                    Simpan Pengaturan
                </button>
            </div>
        </div>
    </div>
</form>
@endsection