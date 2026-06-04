<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label-modern">Nama Lengkap <span class="text-danger">*</span></label>
        <input class="form-control form-control-modern" name="name" value="{{ old('name', $user->name) }}" placeholder="Nama pengguna" required>
    </div>

    <div class="col-md-6">
        <label class="form-label-modern">Email <span class="text-danger">*</span></label>
        <input class="form-control form-control-modern" type="email" name="email" value="{{ old('email', $user->email) }}" placeholder="admin@sitahu.com" required>
    </div>

    <div class="col-md-6">
        <label class="form-label-modern">Telepon</label>
        <input class="form-control form-control-modern" name="telepon" value="{{ old('telepon', $user->telepon) }}" placeholder="081234567890">
    </div>

    <div class="col-md-6">
        <label class="form-label-modern">Role <span class="text-danger">*</span></label>
        <select class="form-select form-select-modern" name="role" required>
            <option value="admin" @selected(old('role', $user->role ?: 'admin') === 'admin')>Admin</option>
            <option value="kasir" @selected(old('role', $user->role) === 'kasir')>Kasir</option>
        </select>
        <div class="text-muted small mt-1">Admin bisa masuk dashboard penuh. Kasir diarahkan ke halaman kasir.</div>
    </div>

    <div class="col-md-6">
        <label class="form-label-modern">Password {{ $mode === 'create' ? '*' : 'Baru' }}</label>
        <input class="form-control form-control-modern" type="password" name="password" {{ $mode === 'create' ? 'required' : '' }} placeholder="{{ $mode === 'create' ? 'Minimal 6 karakter' : 'Kosongkan jika tidak diubah' }}">
    </div>

    <div class="col-md-6">
        <label class="form-label-modern">Konfirmasi Password {{ $mode === 'create' ? '*' : '' }}</label>
        <input class="form-control form-control-modern" type="password" name="password_confirmation" {{ $mode === 'create' ? 'required' : '' }} placeholder="Ulangi password">
    </div>

    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center p-3 border rounded-3 bg-white">
            <div>
                <strong class="d-block text-dark">Status Aktif</strong>
                <span class="text-muted small">Nonaktifkan jika akun sementara tidak boleh login.</span>
            </div>
            <select class="form-select form-select-modern w-auto" name="aktif">
                <option value="1" @selected(old('aktif', $user->aktif ?? true))>Aktif</option>
                <option value="0" @selected(!old('aktif', $user->aktif ?? true))>Nonaktif</option>
            </select>
        </div>
    </div>
</div>
