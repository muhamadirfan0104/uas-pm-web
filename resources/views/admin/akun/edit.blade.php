@extends('layouts.admin')
@section('title','Pengaturan Akun - SiTahu')

@section('content')
<form method="POST" action="{{ route('admin.akun.update') }}">
    @csrf @method('PATCH')

    <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h4 fw-bold text-dark mb-1">Pengaturan Akun</h1>
            <p class="text-muted small mb-0">Kelola profil akun admin/kasir yang sedang login.</p>
        </div>
        <button class="btn shadow-sm fw-bold px-4 text-white d-flex align-items-center gap-2" type="submit" style="background: var(--brand-color, #dfba68);">
            <i class="bi bi-save"></i> Simpan Akun
        </button>
    </div>

    <div class="row g-4">
        <div class="col-12 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-bold mb-3" style="width:86px;height:86px;font-size:1.8rem;background:var(--brand-color,#dfba68);">
                        {{ strtoupper(substr($user->name,0,2)) }}
                    </div>
                    <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                    <div class="text-muted small mb-3">{{ $user->email }}</div>
                    <span class="badge rounded-pill bg-light text-secondary border">{{ strtoupper($user->role) }}</span>

                    <hr>
                    <div class="text-start small text-muted">
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span>Status</span>
                            <strong class="{{ $user->aktif ? 'text-success' : 'text-danger' }}">{{ $user->aktif ? 'Aktif' : 'Nonaktif' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span>Telepon</span>
                            <strong>{{ $user->telepon ?: '-' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-2">
                            <span>Bergabung</span>
                            <strong>{{ $user->created_at?->format('d M Y') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white fw-bold">
                    <i class="bi bi-person-gear me-2 text-muted"></i> Detail Profil Akun
                </div>
                <div class="card-body row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nama</label>
                        <input class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email</label>
                        <input class="form-control" type="email" name="email" value="{{ old('email', $user->email) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Telepon</label>
                        <input class="form-control" name="telepon" value="{{ old('telepon', $user->telepon) }}" placeholder="081234567890">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Role</label>
                        <input class="form-control" value="{{ strtoupper($user->role) }}" disabled>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mt-4">
                <div class="card-header bg-white fw-bold">
                    <i class="bi bi-shield-lock me-2 text-muted"></i> Ubah Password
                </div>
                <div class="card-body row g-4">
                    <div class="col-12">
                        <div class="rounded-3 bg-warning-subtle text-warning-emphasis p-3 small">Kosongkan bagian password jika tidak ingin mengubah password.</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Password Lama</label>
                        <input class="form-control" type="password" name="password_lama">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Password Baru</label>
                        <input class="form-control" type="password" name="password">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Konfirmasi Password</label>
                        <input class="form-control" type="password" name="password_confirmation">
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
