@csrf
<div class="row g-3">
    <div class="col-12">
        <label class="form-label fw-semibold">Judul</label>
        <input class="form-control" name="judul" value="{{ old('judul',$banner->judul) }}" required>
    </div>

    <div class="col-12">
        <label class="form-label fw-semibold">Deskripsi</label>
        <textarea class="form-control" rows="3" name="deskripsi">{{ old('deskripsi',$banner->deskripsi) }}</textarea>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Urutan</label>
        <input class="form-control" type="number" min="0" name="urutan" value="{{ old('urutan',$banner->urutan ?? 0) }}">
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Status</label>
        <select class="form-select" name="aktif">
            <option value="1" @selected(old('aktif',$banner->aktif ?? true))>Aktif</option>
            <option value="0" @selected(!old('aktif',$banner->aktif ?? true))>Nonaktif</option>
        </select>
    </div>

    <div class="col-12">
        <label class="form-label fw-semibold">Gambar Banner</label>
        <div class="upload-box">
            <div>
                <strong>Upload Gambar Banner</strong>
                <div class="small text-muted mb-3">JPG/PNG maksimal 2MB</div>
                <input class="form-control" type="file" name="gambar" accept="image/*" {{ $banner->exists ? '' : 'required' }}>
            </div>
        </div>
        @if($banner->exists && $banner->url_gambar)
            <img src="{{ asset('storage/'.$banner->url_gambar) }}" class="mt-3 rounded-4 border" style="width:220px" alt="{{ $banner->judul }}">
        @endif
    </div>
</div>
