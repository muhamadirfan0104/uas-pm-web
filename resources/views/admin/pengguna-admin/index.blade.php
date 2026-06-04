@extends('layouts.admin')
@section('title', 'Kelola Pengguna Admin - SiTahu')

@section('content')
<style>
    .sc-box { border: 1px solid #e5e7eb; border-radius: 0.75rem; background: #fff; margin-bottom: 1.5rem; overflow: hidden; box-shadow: 0 1px 2px rgba(0,0,0,0.02); }
    .metric-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1.5rem; position: relative; overflow: hidden; transition: all 0.2s; }
    .metric-card:hover { border-color: var(--brand-color, #dfba68); box-shadow: 0 4px 12px rgba(223, 186, 104, 0.15); transform: translateY(-2px); }
    .metric-label { font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; margin-bottom: 0.5rem; }
    .metric-value { font-size: 1.85rem; font-weight: 800; letter-spacing: -0.03em; color: #111827; line-height: 1.1; }
    .metric-icon { position: absolute; right: -10px; bottom: -15px; font-size: 5rem; opacity: 0.04; color: #111827; }
    .search-bar-modern { background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 0.75rem; transition: all 0.2s; }
    .search-bar-modern:focus-within { background-color: #ffffff; border-color: var(--brand-color, #dfba68); box-shadow: 0 0 0 3px rgba(223, 186, 104, 0.15); }
    .search-bar-modern input { background: transparent; border: none; box-shadow: none; outline: none; width: 100%; }
    .table-enterprise th { border-bottom: 2px solid #e5e7eb; color: #6b7280; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 1rem 1.5rem; font-weight: 600; background: #fafafa; }
    .table-enterprise td { vertical-align: middle; padding: 1rem 1.5rem; border-bottom: 1px solid #f3f4f6; color: #111827; }
    .table-enterprise tbody tr:hover { background-color: #f9fafb; }
    .avatar-admin { width: 42px; height: 42px; border-radius: 999px; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 800; background: var(--brand-color, #dfba68); }
    .form-label-modern { font-size: 0.8rem; font-weight: 700; color: #374151; margin-bottom: 0.4rem; }
    .form-control-modern, .form-select-modern { background-color: #f9fafb; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.6rem 0.75rem; font-size: 0.9rem; transition: all 0.2s; box-shadow: none; }
    .form-control-modern:focus, .form-select-modern:focus { background-color: #ffffff; border-color: var(--brand-color, #dfba68); box-shadow: 0 0 0 3px rgba(223, 186, 104, 0.15); outline: none; }
</style>

<div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3 mb-4">
    <div>
        <h1 class="h4 fw-bold text-dark mb-1">Kelola Pengguna Admin</h1>
        <p class="text-muted small mb-0">Tambah, edit, aktifkan, dan nonaktifkan pengguna admin/kasir web SiTahu.</p>
    </div>
    <button class="btn shadow-sm fw-bold px-4 text-white d-flex align-items-center gap-2" type="button" data-bs-toggle="modal" data-bs-target="#modalPenggunaCreate" style="background: var(--brand-color, #dfba68);">
        <i class="bi bi-person-plus"></i> Tambah Pengguna
    </button>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="metric-card h-100">
            <div class="metric-label">Total Pengguna</div>
            <div class="metric-value">{{ $stats['total'] }}</div>
            <i class="bi bi-people metric-icon"></i>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="metric-card h-100" style="border-left: 4px solid #dfba68;">
            <div class="metric-label">Admin</div>
            <div class="metric-value">{{ $stats['admin'] }}</div>
            <i class="bi bi-shield-lock metric-icon"></i>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="metric-card h-100" style="border-left: 4px solid #0ea5e9;">
            <div class="metric-label">Kasir</div>
            <div class="metric-value">{{ $stats['kasir'] }}</div>
            <i class="bi bi-person-badge metric-icon"></i>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="metric-card h-100" style="border-left: 4px solid #10b981;">
            <div class="metric-label">Aktif</div>
            <div class="metric-value">{{ $stats['aktif'] }}</div>
            <i class="bi bi-check-circle metric-icon"></i>
        </div>
    </div>
</div>

<div class="sc-box mb-4">
    <div class="bg-white border-bottom p-3 p-md-4">
        <form id="page-filter" class="js-instant-filter" method="GET">
            <div class="row g-3 align-items-center">
                <div class="col-12 col-lg">
                    <div class="search-bar-modern d-flex align-items-center px-3 py-2">
                        <i class="bi bi-search text-muted me-2"></i>
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama, email, atau telepon...">
                    </div>
                </div>
                <div class="col-12 col-md-4 col-lg-3">
                    <select class="form-select bg-light border-light fw-medium" name="role" style="border-radius:0.75rem; min-height:42px;">
                        <option value="">Semua Role</option>
                        <option value="admin" @selected(request('role')==='admin')>Admin</option>
                        <option value="kasir" @selected(request('role')==='kasir')>Kasir</option>
                    </select>
                </div>
                <div class="col-12 col-md-4 col-lg-3">
                    <select class="form-select bg-light border-light fw-medium" name="status" style="border-radius:0.75rem; min-height:42px;">
                        <option value="">Semua Status</option>
                        <option value="aktif" @selected(request('status')==='aktif')>Aktif</option>
                        <option value="nonaktif" @selected(request('status')==='nonaktif')>Nonaktif</option>
                    </select>
                </div>
            </div>
            <div class="text-muted small mt-2"><i class="bi bi-lightning-charge me-1"></i>Pencarian dan filter otomatis berjalan tanpa tombol.</div>
        </form>
    </div>

    <div class="table-responsive bg-white">
        <table class="table table-enterprise table-borderless mb-0" style="min-width:900px;">
            <thead>
                <tr>
                    <th>Pengguna</th>
                    <th>Kontak</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Terdaftar</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($users as $user)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-admin">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                            <div>
                                <strong class="d-block text-dark mb-1">{{ $user->name }}</strong>
                                <span class="text-muted small">ID: #{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="fw-medium text-dark">{{ $user->email }}</div>
                        <div class="small text-muted">{{ $user->telepon ?: '-' }}</div>
                    </td>
                    <td>
                        <span class="badge rounded-pill {{ $user->role === 'admin' ? 'bg-warning-subtle text-warning-emphasis' : 'bg-info-subtle text-info-emphasis' }}">
                            {{ strtoupper($user->role) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge rounded-pill {{ $user->aktif ? 'bg-success-subtle text-success-emphasis' : 'bg-secondary-subtle text-secondary-emphasis' }}">
                            {{ $user->aktif ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td>{{ optional($user->created_at)->format('d M Y') }}</td>
                    <td class="text-end">
                        <div class="d-flex justify-content-end gap-1 flex-wrap">
                            <button class="btn btn-sm btn-light border" type="button" data-bs-toggle="modal" data-bs-target="#modalPenggunaDetail{{ $user->id }}" title="Detail">
                                <i class="bi bi-info-circle"></i>
                            </button>
                            <button class="btn btn-sm btn-light border" type="button" data-bs-toggle="modal" data-bs-target="#modalPenggunaEdit{{ $user->id }}" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form class="d-inline" method="POST" action="{{ route('admin.pengguna-admin.toggle', $user) }}" data-confirm-title="Ubah Status Pengguna" data-confirm-message="Yakin ingin mengubah status pengguna {{ $user->name }}?" data-confirm-button="Ubah Status">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-light border" type="submit" title="{{ $user->aktif ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <i class="bi {{ $user->aktif ? 'bi-person-dash' : 'bi-person-check' }}"></i>
                                </button>
                            </form>
                            <form class="d-inline" method="POST" action="{{ route('admin.pengguna-admin.destroy', $user) }}" data-confirm-title="Hapus Pengguna" data-confirm-message="Yakin ingin menghapus pengguna {{ $user->name }}? Data yang dihapus tidak dapat dikembalikan." data-confirm-button="Hapus">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-light border text-danger" type="submit" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <i class="bi bi-people fs-1 text-muted d-block mb-2"></i>
                        <strong class="text-dark">Belum ada pengguna admin.</strong>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="bg-light border-top p-3">{{ $users->links() }}</div>
    @endif
</div>

<div class="modal fade" id="modalPenggunaCreate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form method="POST" action="{{ route('admin.pengguna-admin.store') }}" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            <div class="modal-header p-4 bg-white">
                <div>
                    <h5 class="modal-title fw-bold">Tambah Pengguna Admin</h5>
                    <div class="text-muted small">Buat akun admin atau kasir untuk akses web SiTahu.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-4" style="background-color:#f9fafb;">
                @include('admin.pengguna-admin._form', ['user' => new \App\Models\User(), 'mode' => 'create'])
            </div>
            <div class="modal-footer p-4 bg-white">
                <button class="btn btn-light border" type="button" data-bs-dismiss="modal">Batal</button>
                <button class="btn fw-bold text-white" type="submit" style="background: var(--brand-color, #dfba68);">Simpan Pengguna</button>
            </div>
        </form>
    </div>
</div>

@foreach($users as $user)
<div class="modal fade" id="modalPenggunaDetail{{ $user->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header p-4 bg-white">
                <div>
                    <h5 class="modal-title fw-bold">Detail Pengguna</h5>
                    <div class="text-muted small">{{ $user->name }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="avatar-admin mx-auto mb-3" style="width:78px;height:78px;font-size:1.5rem;">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                    <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                    <div class="text-muted small">{{ $user->email }}</div>
                </div>
                <div class="border rounded-4 overflow-hidden">
                    <div class="d-flex justify-content-between p-3 border-bottom">
                        <span class="text-muted small fw-bold text-uppercase">Telepon</span>
                        <strong>{{ $user->telepon ?: '-' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between p-3 border-bottom">
                        <span class="text-muted small fw-bold text-uppercase">Role</span>
                        <strong>{{ strtoupper($user->role) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between p-3 border-bottom">
                        <span class="text-muted small fw-bold text-uppercase">Status</span>
                        <strong class="{{ $user->aktif ? 'text-success' : 'text-danger' }}">{{ $user->aktif ? 'Aktif' : 'Nonaktif' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between p-3">
                        <span class="text-muted small fw-bold text-uppercase">Terdaftar</span>
                        <strong>{{ optional($user->created_at)->format('d M Y H:i') }}</strong>
                    </div>
                </div>
            </div>
            <div class="modal-footer p-4 bg-white">
                <button class="btn btn-light border" type="button" data-bs-dismiss="modal">Tutup</button>
                <button class="btn text-white" type="button" data-bs-target="#modalPenggunaEdit{{ $user->id }}" data-bs-toggle="modal" style="background: var(--brand-color, #dfba68);">Edit Pengguna</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalPenggunaEdit{{ $user->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form method="POST" action="{{ route('admin.pengguna-admin.update', $user) }}" class="modal-content border-0 shadow-lg rounded-4">
            @csrf @method('PUT')
            <div class="modal-header p-4 bg-white">
                <div>
                    <h5 class="modal-title fw-bold">Edit Pengguna</h5>
                    <div class="text-muted small">{{ $user->name }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-4" style="background-color:#f9fafb;">
                @include('admin.pengguna-admin._form', ['user' => $user, 'mode' => 'edit'])
            </div>
            <div class="modal-footer p-4 bg-white">
                <button class="btn btn-light border" type="button" data-bs-dismiss="modal">Batal</button>
                <button class="btn fw-bold text-white" type="submit" style="background: var(--brand-color, #dfba68);">Perbarui Pengguna</button>
            </div>
        </form>
    </div>
</div>
@endforeach
@endsection
