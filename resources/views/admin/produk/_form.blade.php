@php
    $aktifValue = (string) old('aktif', $produk->exists ? (int) $produk->aktif : 1);
@endphp

<style>
    .product-form-card { border: 1px solid var(--border); border-radius: 20px; background: #fff; overflow: hidden; }
    .product-form-head { padding: 16px 18px; border-bottom: 1px solid var(--border); background: linear-gradient(135deg,#fff,#fff8ea); }
    .product-form-head strong { display: block; color: var(--text); font-size: .95rem; font-weight: 950; }
    .product-form-head span { color: var(--muted); font-size: .76rem; font-weight: 700; }
    .product-form-body { padding: 18px; }
    .form-label-mini { margin-bottom: 7px; color: #475467; font-size: .74rem; font-weight: 950; letter-spacing: .04em; text-transform: uppercase; }
    .upload-product-box { min-height: 190px; border: 1px dashed #e6c987; border-radius: 20px; background: linear-gradient(135deg,#fff8ea,#fff); display: flex; align-items: center; justify-content: center; padding: 18px; text-align: center; }
    .current-product-photo { width: 132px; height: 132px; border-radius: 22px; border: 1px solid var(--border); object-fit: cover; background: #f8fafc; }
</style>

<div class="row g-3">
    <div class="col-12 col-xl-7">
        <div class="product-form-card h-100">
            <div class="product-form-head">
                <strong>Informasi produk</strong>
                <span>Nama, deskripsi, dan status tampil akan langsung terbaca di katalog pembeli. Pengaturan stok dilakukan dari menu Stok.</span>
            </div>
            <div class="product-form-body row g-3">
                <div class="col-12">
                    <label class="form-label-mini">Nama produk</label>
                    <input class="form-control" name="nama" value="{{ old('nama', $produk->nama) }}" placeholder="Nama produk" required>
                </div>
                <div class="col-12">
                    <label class="form-label-mini">Deskripsi produk</label>
                    <textarea class="form-control" rows="5" name="deskripsi" placeholder="Jelaskan rasa, isi, kegunaan, dan keunggulan produk secara singkat.">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label-mini">Status katalog</label>
                    <select class="form-select" name="aktif">
                        <option value="1" @selected($aktifValue === '1')>Tampilkan ke pembeli</option>
                        <option value="0" @selected($aktifValue === '0')>Sembunyikan dulu</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label-mini">Masa simpan</label>
                    <div class="input-group">
                        <input class="form-control" type="number" min="0" name="masa_simpan" value="{{ old('masa_simpan', $produk->masa_simpan) }}" placeholder="0">
                        <span class="input-group-text bg-white">hari</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-5">
        <div class="product-form-card h-100">
            <div class="product-form-head">
                <strong>Foto utama</strong>
                <span></span>
            </div>
            <div class="product-form-body">
                @if($produk->exists && $produk->gambarUtama)
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img class="current-product-photo" src="{{ asset('storage/'.$produk->gambarUtama->url_gambar) }}" alt="{{ $produk->nama }}">
                        <div>
                            <div class="fw-bold small">Foto saat ini</div>
                            <div class="text-muted small">Upload foto baru untuk mengganti foto ini.</div>
                        </div>
                    </div>
                @endif
                <div class="upload-product-box">
                    <div class="w-100">
                        <i class="bi bi-cloud-arrow-up fs-1" style="color: var(--brand-dark)"></i>
                        <div class="fw-bold mt-2">Upload foto produk</div>
                        <div class="text-muted small mb-3">JPG, PNG, atau WEBP. Maksimal 2 MB.</div>
                        <input class="form-control" type="file" name="foto" accept="image/*">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="product-form-card">
            <div class="product-form-head">
                <strong>Harga dan satuan jual</strong>
                <span></span>
            </div>
            <div class="product-form-body row g-3">
                <div class="col-md-6">
                    <label class="form-label-mini">Harga</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">Rp</span>
                        <input class="form-control" type="number" min="0" name="harga" value="{{ old('harga', $produk->harga) }}" placeholder="0" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label-mini">Satuan jual</label>
                    <input class="form-control" name="satuan" value="{{ old('satuan', $produk->satuan ?? 'pack') }}" placeholder="pack, pcs, box" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label-mini">Isi per satuan</label>
                    <div class="input-group">
                        <input class="form-control" type="number" min="0" name="isi_per_satuan" value="{{ old('isi_per_satuan', $produk->isi_per_satuan) }}" placeholder="0">
                        <span class="input-group-text bg-white">pcs</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label-mini">Berat</label>
                    <div class="input-group">
                        <input class="form-control" type="number" step="0.01" min="0" name="berat" value="{{ old('berat', $produk->berat) }}" placeholder="0">
                        <span class="input-group-text bg-white">kg</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="product-form-card">
            <div class="product-form-head">
                <strong>Penyimpanan dan penyajian</strong>
                <span></span>
            </div>
            <div class="product-form-body row g-3">
                <div class="col-md-6">
                    <label class="form-label-mini">Saran penyimpanan</label>
                    <input class="form-control" name="saran_penyimpanan" value="{{ old('saran_penyimpanan', $produk->saran_penyimpanan) }}" placeholder="Saran penyimpanan">
                </div>
                <div class="col-md-6">
                    <label class="form-label-mini">Saran penyajian</label>
                    <input class="form-control" name="saran_penyajian" value="{{ old('saran_penyajian', $produk->saran_penyajian) }}" placeholder="Saran penyajian">
                </div>
            </div>
        </div>
    </div>
</div>
