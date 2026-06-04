<style>
    .sc-box { border: 1px solid #e5e7eb; border-radius: 0.75rem; background: #fff; margin-bottom: 1.5rem; overflow: hidden; box-shadow: 0 1px 2px rgba(0,0,0,0.02); }
    .sc-header { padding: 1rem 1.25rem; font-weight: 600; font-size: 1rem; border-bottom: 1px solid #f3f4f6; color: #111827; }
    .sc-body { padding: 1.25rem; }
    .sc-label { font-size: 0.85rem; font-weight: 600; color: #4b5563; margin-bottom: 0.4rem; display: block; }
    .sc-input { border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.6rem 0.75rem; font-size: 0.9rem; transition: all 0.2s; width: 100%; }
    .sc-input:focus { border-color: var(--brand-color, #dfba68); box-shadow: 0 0 0 3px rgba(223, 186, 104, 0.15); outline: none; }
    .sc-group-text { background: #f9fafb; border: 1px solid #d1d5db; font-size: 0.9rem; color: #6b7280; font-weight: 500; }
</style>

<div class="sc-box">
    <div class="sc-header">Informasi Dasar</div>
    <div class="sc-body row g-3">
        <div class="col-12">
            <label class="sc-label">Nama Produk <span class="text-danger">*</span></label>
            <input class="form-control sc-input" name="nama" value="{{ old('nama', $produk->nama) }}" placeholder="Contoh: Tahu Susu Lembang Besar" required>
        </div>
        <div class="col-12">
            <label class="sc-label">Deskripsi Lengkap</label>
            <textarea class="form-control sc-input" rows="4" name="deskripsi" placeholder="Tuliskan keunggulan produk ini untuk menarik pembeli...">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
        </div>
    </div>
</div>

<div class="sc-box">
    <div class="sc-header">Harga & Batas Minimal Stok</div>
    <div class="sc-body row g-4">
        <div class="col-md-6">
            <label class="sc-label">Harga Satuan <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text sc-group-text border-end-0">Rp</span>
                <input class="form-control sc-input" style="border-top-left-radius:0;border-bottom-left-radius:0;" type="number" min="0" name="harga" value="{{ old('harga', $produk->harga) }}" placeholder="0" required>
            </div>
        </div>
        <div class="col-md-6">
            <label class="sc-label">Minimal Stok <span class="text-danger">*</span></label>
            <input class="form-control sc-input" type="number" min="0" name="min_stok" value="{{ old('min_stok', $produk->min_stok ?? 20) }}" placeholder="20" required>
            <div class="text-muted small mt-1">Dipakai untuk mendeteksi stok menipis. Stok aktual diubah lewat menu Stok.</div>
        </div>
        <div class="col-md-4">
            <label class="sc-label">Satuan Jual <span class="text-danger">*</span></label>
            <input class="form-control sc-input" name="satuan" value="{{ old('satuan', $produk->satuan ?? 'Pack') }}" placeholder="Pack, Pcs, dll" required>
        </div>
        <div class="col-md-4">
            <label class="sc-label">Isi per Satuan</label>
            <div class="input-group">
                <input class="form-control sc-input border-end-0" style="border-top-right-radius:0;border-bottom-right-radius:0;" type="number" min="0" name="isi_per_satuan" value="{{ old('isi_per_satuan', $produk->isi_per_satuan) }}" placeholder="0">
                <span class="input-group-text sc-group-text">Pcs</span>
            </div>
        </div>
        <div class="col-md-4">
            <label class="sc-label">Berat</label>
            <div class="input-group">
                <input class="form-control sc-input border-end-0" style="border-top-right-radius:0;border-bottom-right-radius:0;" type="number" step="0.01" min="0" name="berat" value="{{ old('berat', $produk->berat) }}" placeholder="0">
                <span class="input-group-text sc-group-text">Gr</span>
            </div>
        </div>
    </div>
</div>

<div class="sc-box">
    <div class="sc-header">Penyajian & Media Foto</div>
    <div class="sc-body row g-4">
        <div class="col-md-6">
            <label class="sc-label">Masa Simpan</label>
            <div class="input-group">
                <input class="form-control sc-input border-end-0" style="border-top-right-radius:0;border-bottom-right-radius:0;" type="number" min="0" name="masa_simpan" value="{{ old('masa_simpan', $produk->masa_simpan) }}" placeholder="0">
                <span class="input-group-text sc-group-text">Hari</span>
            </div>
        </div>
        <div class="col-md-6">
            <label class="sc-label">Saran Penyimpanan</label>
            <input class="form-control sc-input" name="saran_penyimpanan" value="{{ old('saran_penyimpanan', $produk->saran_penyimpanan) }}" placeholder="Misal: Suhu chiller 4°C">
        </div>
        <div class="col-12">
            <label class="sc-label">Saran Penyajian</label>
            <input class="form-control sc-input" name="saran_penyajian" value="{{ old('saran_penyajian', $produk->saran_penyajian) }}" placeholder="Misal: Enak digoreng dengan api sedang">
        </div>
        <div class="col-12 mt-2">
            <label class="sc-label mb-2">Foto Utama Produk</label>
            <div class="d-flex flex-column flex-sm-row gap-3">
                @if($produk->exists && $produk->gambarUtama)
                    <div class="border rounded-3 p-1 bg-white shadow-sm flex-shrink-0" style="width:120px;height:120px;">
                        <img src="{{ asset('storage/'.$produk->gambarUtama->url_gambar) }}" style="width:100%;height:100%;object-fit:cover;border-radius:0.35rem;" alt="Foto Produk">
                    </div>
                @endif
                <div class="flex-grow-1 d-flex flex-column justify-content-center align-items-center border border-2 rounded-3 bg-light p-4">
                    <i class="bi bi-image text-muted fs-3 mb-2"></i>
                    <div class="fw-semibold text-dark" style="font-size:0.9rem;">Pilih Foto Produk</div>
                    <div class="text-muted mb-2" style="font-size:0.75rem;">Format JPG/PNG. Maksimal 2MB.</div>
                    <input class="form-control" type="file" name="foto" accept="image/*">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="sc-box mb-0 border-0 shadow-none bg-transparent">
    <div class="d-flex justify-content-between align-items-center p-3 border rounded-3 bg-white shadow-sm">
        <div>
            <strong class="d-block text-dark" style="font-size:0.9rem;">Tampilkan Produk (Aktif)</strong>
            <span class="text-muted" style="font-size:0.8rem;">Matikan jika produk sedang kosong atau ingin disembunyikan.</span>
        </div>
        <select class="form-select sc-input bg-light border-light fw-bold" name="aktif" style="width:150px;cursor:pointer;">
            <option value="1" @selected(old('aktif', $produk->aktif ?? true))>Ya, Tampil</option>
            <option value="0" @selected(!old('aktif', $produk->aktif ?? true))>Sembunyikan</option>
        </select>
    </div>
</div>
