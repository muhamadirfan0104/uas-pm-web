@php
    $aktifValue = (string) old('aktif', $banner->exists ? (int) $banner->aktif : 1);
@endphp

<style>
    .banner-form-card { border: 1px solid var(--border); border-radius: 20px; background: #fff; overflow: hidden; }
    .banner-form-head { padding: 16px 18px; border-bottom: 1px solid var(--border); background: linear-gradient(135deg,#fff,#fff8ea); }
    .banner-form-head strong { display: block; color: var(--text); font-size: .95rem; font-weight: 950; }
    .banner-form-head span { color: var(--muted); font-size: .76rem; font-weight: 700; }
    .banner-form-body { padding: 18px; }
    .form-label-mini { margin-bottom: 7px; color: #475467; font-size: .74rem; font-weight: 950; letter-spacing: .04em; text-transform: uppercase; }
    .banner-upload { min-height: 230px; border: 1px dashed #e6c987; border-radius: 22px; background: linear-gradient(135deg,#fff8ea,#fff); display: flex; align-items: center; justify-content: center; padding: 18px; text-align: center; }
    .banner-preview { width: 100%; max-height: 220px; border-radius: 22px; object-fit: cover; border: 1px solid var(--border); }
</style>

<div class="row g-3">
    <div class="col-12 col-xl-7">
        <div class="banner-form-card h-100">
            <div class="banner-form-head">
                <strong>Isi banner</strong>
                <span>Judul dan deskripsi akan tampil di banner beranda pembeli.</span>
            </div>
            <div class="banner-form-body row g-3">
                <div class="col-12">
                    <label class="form-label-mini">Judul banner</label>
                    <input class="form-control" name="judul" value="{{ old('judul', $banner->judul) }}" maxlength="100" placeholder="Contoh: Tahu segar siap dipesan hari ini" required>
                </div>
                <div class="col-12">
                    <label class="form-label-mini">Deskripsi singkat</label>
                    <textarea class="form-control" rows="4" name="deskripsi" maxlength="500" placeholder="Tulis kalimat pendek yang menjelaskan promo, produk unggulan, atau info toko.">{{ old('deskripsi', $banner->deskripsi) }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label-mini">Urutan tampil</label>
                    <input class="form-control" type="number" min="0" name="urutan" value="{{ old('urutan', $banner->urutan ?? 0) }}" placeholder="0">
                    <div class="text-muted small mt-2">Angka kecil tampil lebih awal.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label-mini">Status banner</label>
                    <select class="form-select" name="aktif">
                        <option value="1" @selected($aktifValue === '1')>Aktif ditampilkan</option>
                        <option value="0" @selected($aktifValue === '0')>Simpan sebagai draft</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-5">
        <div class="banner-form-card h-100">
            <div class="banner-form-head">
                <strong>Gambar banner</strong>
                <span>Gunakan gambar horizontal agar tampil rapi di slider beranda.</span>
            </div>
            <div class="banner-form-body">
                @if($banner->exists && $banner->url_gambar)
                    <img src="{{ asset('storage/'.$banner->url_gambar) }}" class="banner-preview mb-3" alt="{{ $banner->judul }}">
                @endif
                <div class="banner-upload">
                    <div class="w-100">
                        <i class="bi bi-images fs-1" style="color: var(--brand-dark)"></i>
                        <div class="fw-bold mt-2">Upload gambar banner</div>
                        <div class="text-muted small mb-3">JPG, PNG, atau WEBP. Maksimal 2 MB.</div>
                        <input class="form-control" type="file" name="gambar" accept="image/*" {{ $banner->exists ? '' : 'required' }}>
                    </div>
                </div>
                <div class="text-muted small mt-3">Saran ukuran: rasio horizontal, misalnya 1440 × 520 px.</div>
            </div>
        </div>
    </div>
</div>
