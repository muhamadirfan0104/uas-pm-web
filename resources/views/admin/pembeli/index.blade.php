@extends('layouts.admin')
@section('title', 'Data Pembeli - SiTahu')

@section('content')
<style>
    /* Styling Standar E-Commerce */
    .sc-box { border: 1px solid #e5e7eb; border-radius: 0.75rem; background: #fff; margin-bottom: 1.5rem; overflow: hidden; box-shadow: 0 1px 2px rgba(0,0,0,0.02); }
    
    /* Search Bar Modern */
    .search-bar-modern { background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 0.75rem; transition: all 0.2s; }
    .search-bar-modern:focus-within { background-color: #ffffff; border-color: var(--brand-color, #dfba68); box-shadow: 0 0 0 4px rgba(223, 186, 104, 0.15); }
    .search-bar-modern input { background: transparent; border: none; box-shadow: none; outline: none; width: 100%; }
    
    /* Tabel Enterprise */
    .table-enterprise th { border-bottom: 2px solid #e5e7eb; color: #6b7280; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 1rem 1.25rem; font-weight: 600; background: #fafafa; }
    .table-enterprise td { vertical-align: middle; padding: 1rem 1.25rem; border-bottom: 1px solid #f3f4f6; color: #111827; }
    .table-enterprise tbody tr:hover { background-color: #f9fafb; }
    
    /* Avatar User */
    .avatar-user { width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #fff; flex-shrink: 0; font-size: 0.9rem; }
    
    /* Tombol Aksi */
    .btn-action { width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; border-radius: 0.5rem; transition: all 0.2s; border: 1px solid #e5e7eb; background: #fff; color: #4b5563; }
    .btn-action:hover { background-color: #f3f4f6; color: #111827; }
</style>

<div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3 mb-4">
    <div>
        <h1 class="h4 fw-bold text-dark mb-1">Data Pembeli</h1>
        <p class="text-muted small mb-0">Manajemen basis pelanggan yang terdaftar dari aplikasi mobile.</p>
    </div>
</div>

<div class="sc-box">
    <div class="bg-white border-bottom p-3 p-md-4">
        <form id="page-filter" method="GET" class="row g-2 align-items-center">
            <div class="col-12 col-md-6 col-lg-5">
                <div class="search-bar-modern d-flex align-items-center px-3 py-2">
                    <i class="bi bi-search text-muted me-2"></i>
                    <input name="q" value="{{ request('q') }}" placeholder="Cari nama, email, atau no telepon...">
                </div>
            </div>
            <div class="col-12 col-md-auto">
                <button class="btn btn-dark fw-medium px-4" type="submit" style="border-radius: 0.75rem; min-height: 42px;">Cari Pembeli</button>
            </div>
        </form>
    </div>

    <div class="table-responsive bg-white">
        <table class="table table-enterprise table-borderless mb-0">
            <thead>
                <tr>
                    <th class="ps-4">Profil Pembeli</th>
                    <th>Kontak</th>
                    <th>Performa Belanja</th>
                    <th>Status</th>
                    <th>Terdaftar</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($pembeli as $user)
                @php 
                    $colors = ['#f59e0b', '#3b82f6', '#10b981', '#ef4444', '#8b5cf6']; 
                    $bgColor = $colors[$user->id % count($colors)];
                @endphp
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-user shadow-sm" style="background-color: {{ $bgColor }};">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <div>
                                <strong class="d-block text-dark mb-1" style="font-size: 0.95rem;">{{ $user->name }}</strong>
                                <span class="text-muted small">{{ $user->email }}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="text-dark fw-medium">{{ $user->telepon ?? 'Belum diset' }}</div>
                    </td>
                    <td>
                        <div class="d-flex flex-column gap-1">
                            <span class="text-dark fw-bold">{{ $rupiah($user->total_belanja ?? 0) }}</span>
                            <span class="text-muted small">{{ $user->pesanan_count }}x Transaksi</span>
                        </div>
                    </td>
                    <td>
                        <span class="badge rounded-pill px-2 py-1 fw-medium {{ $user->aktif ? 'bg-success-subtle text-success-emphasis' : 'bg-danger-subtle text-danger-emphasis' }}">
                            {{ $user->aktif ? 'Akun Aktif' : 'Diblokir' }}
                        </span>
                    </td>
                    <td>
                        <div class="text-muted small">{{ $user->created_at->format('d M Y') }}</div>
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-1">
                            <a class="btn-action text-decoration-none" href="{{ route('admin.pembeli.show', $user) }}" title="Lihat Profil">
                                <i class="bi bi-person-lines-fill"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.pembeli.toggle', $user) }}" class="d-inline" data-confirm-title="Ubah Status Pembeli" data-confirm-message="Yakin ingin mengubah status akun pembeli ini?" data-confirm-button="Ubah Status">
                                @csrf @method('PATCH')
                                <button class="btn-action" type="submit" title="{{ $user->aktif ? 'Blokir / Nonaktifkan' : 'Aktifkan Akun' }}">
                                    <i class="bi {{ $user->aktif ? 'bi-lock text-warning' : 'bi-unlock text-success' }}"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <i class="bi bi-people fs-1 text-muted mb-3 d-block"></i>
                        <strong class="text-dark d-block mb-1">Belum ada pembeli terdaftar.</strong>
                        <span class="text-muted small">Data pembeli akan muncul otomatis saat ada user registrasi.</span>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($pembeli->hasPages())
        <div class="bg-light border-top p-3">{{ $pembeli->links() }}</div>
    @endif
</div>
@endsection