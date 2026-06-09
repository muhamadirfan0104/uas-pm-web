@extends('layouts.admin')

@section('title', 'Pengguna Admin - SiTahu')
@section('page_title', 'Pengguna Admin')
@section('page_subtitle', 'Kelola akun admin yang dapat mengakses dashboard.')

@push('styles')
<style>
    .user-head { display:flex; align-items:center; justify-content:space-between; gap:16px; padding:22px; border-radius:24px; border:1px solid #f1d49c; background:linear-gradient(135deg,#fff,#fff8ea); box-shadow:var(--shadow-soft); margin-bottom:16px; }
    .user-head h1 { margin:0; font-size:1.48rem; font-weight:950; letter-spacing:-.055em; }
    .user-head p { margin:7px 0 0; color:var(--muted); font-size:.86rem; font-weight:650; }
    .user-filter { padding:14px; border-radius:22px; border:1px solid var(--border); background:#fff; box-shadow:var(--shadow-soft); margin-bottom:16px; }
    .user-filter form { display:grid; grid-template-columns: minmax(260px,1fr) 170px 170px auto; gap:10px; align-items:end; }
    .user-filter label { margin-bottom:6px; color:var(--muted); font-size:.69rem; font-weight:950; text-transform:uppercase; letter-spacing:.06em; }
    .admin-avatar { width:44px; height:44px; border-radius:16px; display:grid; place-items:center; background:linear-gradient(135deg,var(--brand),#ad7a24); color:#fff; font-weight:950; flex-shrink:0; }
    .role-card { border-radius:22px; border:1px solid var(--border); background:#fff; box-shadow:var(--shadow-soft); overflow:hidden; }
    .role-card-head { padding:16px 18px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; gap:12px; }
    .role-card-head h2 { margin:0; font-size:1rem; font-weight:950; letter-spacing:-.035em; }
    .modal-clean .modal-content { border:0; border-radius:24px; box-shadow:var(--shadow); overflow:hidden; }
    .modal-clean .modal-header, .modal-clean .modal-footer { padding:18px 20px; background:#fff; }
    .modal-clean .modal-body { padding:20px; background:#fbfcfd; }
    .form-grid-2 { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:14px; }
    .setting-field label { margin-bottom:7px; color:var(--text); font-size:.8rem; font-weight:950; }
    .access-toggle { display:flex; align-items:center; justify-content:space-between; gap:14px; padding:14px; border:1px solid var(--border); border-radius:18px; background:#fff; }
    .access-toggle span { display:block; margin-top:3px; color:var(--muted); font-size:.75rem; font-weight:700; }
    .detail-grid { display:grid; gap:10px; }
    .detail-row { display:flex; justify-content:space-between; gap:14px; padding:12px 14px; border:1px solid var(--border); border-radius:15px; background:#fff; }
    .detail-row span { color:var(--muted); font-size:.74rem; font-weight:950; text-transform:uppercase; letter-spacing:.05em; }
    .detail-row strong { text-align:right; }
    @media(max-width:1100px){ .user-filter form{ grid-template-columns:1fr 1fr; } }
    @media(max-width:700px){ .user-head{align-items:flex-start; flex-direction:column;} .user-filter form,.form-grid-2{grid-template-columns:1fr;} .access-toggle{align-items:flex-start; flex-direction:column;} }
</style>
@endpush

@section('content')
<div class="user-head">
    <div>
        <span class="chip c-yellow mb-2">Akses dashboard</span>
        <h1>Pengguna admin</h1>
        <p>Role sistem disederhanakan menjadi Admin dan Pembeli. Halaman ini hanya untuk akun admin.</p>
    </div>
    <button class="btn btn-brand" type="button" data-bs-toggle="modal" data-bs-target="#modalPenggunaCreate"><i class="bi bi-plus-lg me-1"></i>Tambah Admin</button>
</div>

<div class="grid g4 mb-3">
    <div class="stat page-card"><div><div class="stat-label">Total admin</div><div class="stat-value">{{ number_format($stats['total']) }}</div><span class="stat-note text-muted">Akses dashboard</span></div><span class="stat-icon"><i class="bi bi-person-badge-fill"></i></span></div>
    <div class="stat page-card"><div><div class="stat-label">Admin aktif</div><div class="stat-value">{{ number_format($stats['aktif']) }}</div><span class="stat-note text-muted">Bisa login</span></div><span class="stat-icon"><i class="bi bi-shield-check"></i></span></div>
    <div class="stat page-card"><div><div class="stat-label">Nonaktif</div><div class="stat-value">{{ number_format($stats['nonaktif']) }}</div><span class="stat-note text-muted">Akses dikunci</span></div><span class="stat-icon"><i class="bi bi-lock"></i></span></div>
    <div class="stat page-card"><div><div class="stat-label">Pembeli</div><div class="stat-value">{{ number_format($stats['pembeli']) }}</div><span class="stat-note text-muted">Dikelola di menu Pembeli</span></div><span class="stat-icon"><i class="bi bi-people"></i></span></div>
</div>

<div class="user-filter">
    <form method="GET" action="{{ route('admin.pengguna-admin.index') }}">
        <div><label>Cari admin</label><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Nama, email, nomor HP..."></div>
        <div><label>Status</label><select class="form-select" name="status"><option value="">Semua</option><option value="aktif" @selected(request('status')==='aktif')>Aktif</option><option value="nonaktif" @selected(request('status')==='nonaktif')>Nonaktif</option></select></div>
        <div><label>Urutkan</label><select class="form-select" name="sort"><option value="terbaru" @selected(request('sort','terbaru')==='terbaru')>Terbaru</option><option value="terlama" @selected(request('sort')==='terlama')>Terlama</option><option value="nama" @selected(request('sort')==='nama')>Nama A-Z</option><option value="status" @selected(request('sort')==='status')>Status</option></select></div>
        <div><a href="{{ route('admin.pengguna-admin.index') }}" class="btn btn-light border w-100"><i class="bi bi-arrow-clockwise"></i></a></div>
    </form>
</div>

<section class="role-card">
    <div class="role-card-head">
        <div><h2>Daftar admin</h2><div class="text-muted small fw-semibold">Akun admin yang tersimpan di sistem.</div></div>
        <span class="chip c-gray">{{ number_format($users->total()) }} data</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Admin</th><th>Kontak</th><th>Role</th><th>Status</th><th>Terdaftar</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
            @forelse($users as $user)
                <tr>
                    <td><div class="d-flex align-items-center gap-3"><div class="admin-avatar">{{ strtoupper(substr($user->name,0,2)) }}</div><div class="min-w-0"><strong class="d-block text-truncate">{{ $user->name }}</strong><span class="sub">ID #{{ str_pad($user->id,4,'0',STR_PAD_LEFT) }}</span></div></div></td>
                    <td>{{ $user->email }}<span class="sub">{{ $user->telepon ?: '-' }}</span></td>
                    <td><span class="chip c-yellow">ADMIN</span></td>
                    <td><span class="chip {{ $user->aktif ? 'c-green' : 'c-gray' }}">{{ $user->aktif ? 'Aktif' : 'Nonaktif' }}</span></td>
                    <td>{{ optional($user->created_at)->format('d M Y') }}<span class="sub">{{ optional($user->created_at)->format('H:i') }}</span></td>
                    <td class="text-end">
                        <div class="actions justify-content-end">
                            <button class="btn-action" type="button" data-bs-toggle="modal" data-bs-target="#modalPenggunaDetail{{ $user->id }}"><i class="bi bi-eye"></i></button>
                            <button class="btn-action" type="button" data-bs-toggle="modal" data-bs-target="#modalPenggunaEdit{{ $user->id }}"><i class="bi bi-pencil"></i></button>
                            <form class="inline-form" method="POST" action="{{ route('admin.pengguna-admin.toggle', $user) }}" data-confirm-title="Ubah Status Admin" data-confirm-message="Yakin ingin mengubah status {{ $user->name }}?" data-confirm-button="Ubah Status">@csrf @method('PATCH')<button class="btn-action" type="submit"><i class="bi {{ $user->aktif ? 'bi-person-dash' : 'bi-person-check' }}"></i></button></form>
                            <form class="inline-form" method="POST" action="{{ route('admin.pengguna-admin.destroy', $user) }}" data-confirm-title="Hapus Admin" data-confirm-message="Yakin ingin menghapus admin {{ $user->name }}?" data-confirm-button="Hapus">@csrf @method('DELETE')<button class="btn-action text-danger" type="submit"><i class="bi bi-trash"></i></button></form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center py-5 text-muted fw-bold">Belum ada akun admin.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())<div class="p-3 border-top">{{ $users->links() }}</div>@endif
</section>

<div class="modal fade modal-clean" id="modalPenggunaCreate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form method="POST" action="{{ route('admin.pengguna-admin.store') }}" class="modal-content">
            @csrf
            <div class="modal-header"><div><h5 class="modal-title fw-black">Tambah admin</h5><div class="text-muted small fw-semibold">Buat akun baru untuk mengakses dashboard.</div></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">@include('admin.pengguna-admin._form', ['user' => new \App\Models\User(), 'mode' => 'create'])</div>
            <div class="modal-footer"><button class="btn btn-light border" type="button" data-bs-dismiss="modal">Batal</button><button class="btn btn-brand" type="submit">Simpan</button></div>
        </form>
    </div>
</div>

@foreach($users as $user)
<div class="modal fade modal-clean" id="modalPenggunaDetail{{ $user->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><div><h5 class="modal-title fw-black">Detail admin</h5><div class="text-muted small fw-semibold">{{ $user->name }}</div></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="text-center mb-3"><div class="admin-avatar mx-auto mb-3" style="width:72px;height:72px;font-size:1.35rem;">{{ strtoupper(substr($user->name,0,2)) }}</div><h5 class="fw-black mb-1">{{ $user->name }}</h5><div class="text-muted small fw-semibold">{{ $user->email }}</div></div>
                <div class="detail-grid">
                    <div class="detail-row"><span>Telepon</span><strong>{{ $user->telepon ?: '-' }}</strong></div>
                    <div class="detail-row"><span>Role</span><strong>ADMIN</strong></div>
                    <div class="detail-row"><span>Status</span><strong class="{{ $user->aktif ? 'text-success' : 'text-danger' }}">{{ $user->aktif ? 'Aktif' : 'Nonaktif' }}</strong></div>
                    <div class="detail-row"><span>Terdaftar</span><strong>{{ optional($user->created_at)->format('d M Y H:i') }}</strong></div>
                </div>
            </div>
            <div class="modal-footer"><button class="btn btn-light border" type="button" data-bs-dismiss="modal">Tutup</button><button class="btn btn-brand" type="button" data-bs-target="#modalPenggunaEdit{{ $user->id }}" data-bs-toggle="modal">Edit</button></div>
        </div>
    </div>
</div>

<div class="modal fade modal-clean" id="modalPenggunaEdit{{ $user->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form method="POST" action="{{ route('admin.pengguna-admin.update', $user) }}" class="modal-content">
            @csrf @method('PUT')
            <div class="modal-header"><div><h5 class="modal-title fw-black">Edit admin</h5><div class="text-muted small fw-semibold">{{ $user->name }}</div></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">@include('admin.pengguna-admin._form', ['user' => $user, 'mode' => 'edit'])</div>
            <div class="modal-footer"><button class="btn btn-light border" type="button" data-bs-dismiss="modal">Batal</button><button class="btn btn-brand" type="submit">Perbarui</button></div>
        </form>
    </div>
</div>
@endforeach
@endsection
