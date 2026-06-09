<div class="form-grid-2">
    <div class="setting-field">
        <label>Nama admin <span class="text-danger">*</span></label>
        <input class="form-control" name="name" value="{{ old('name', $user->name) }}" placeholder="Nama lengkap" required>
    </div>
    <div class="setting-field">
        <label>Email login <span class="text-danger">*</span></label>
        <input class="form-control" type="email" name="email" value="{{ old('email', $user->email) }}" placeholder="admin@sitahu.com" required>
    </div>
    <div class="setting-field">
        <label>Nomor HP</label>
        <input class="form-control" name="telepon" value="{{ old('telepon', $user->telepon) }}" placeholder="081234567890">
    </div>
    <div class="setting-field">
        <label>Hak akses</label>
        <input class="form-control" value="Admin — akses penuh" readonly>
        <div class="text-muted small fw-semibold mt-2">Sistem hanya memakai role Admin dan Pembeli.</div>
    </div>
    <div class="setting-field">
        <label>Password {{ $mode === 'create' ? '*' : 'baru' }}</label>
        <input class="form-control" type="password" name="password" {{ $mode === 'create' ? 'required' : '' }} placeholder="{{ $mode === 'create' ? 'Minimal 6 karakter' : 'Kosongkan jika tidak diubah' }}">
    </div>
    <div class="setting-field">
        <label>Konfirmasi password {{ $mode === 'create' ? '*' : '' }}</label>
        <input class="form-control" type="password" name="password_confirmation" {{ $mode === 'create' ? 'required' : '' }} placeholder="Ulangi password">
    </div>
</div>
<div class="access-toggle mt-3">
    <div>
        <strong>Status akun</strong>
        <span>Nonaktifkan jika akun sementara tidak boleh masuk dashboard admin.</span>
    </div>
    <select class="form-select" name="aktif" style="max-width:160px;">
        <option value="1" @selected(old('aktif', $user->aktif ?? true))>Aktif</option>
        <option value="0" @selected(! old('aktif', $user->aktif ?? true))>Nonaktif</option>
    </select>
</div>
