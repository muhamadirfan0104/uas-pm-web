@extends('layouts.admin')

@section('title', 'Pembeli - SiTahu')
@section('page_title', 'Pembeli')

@section('content')
@php
    $sortOptions = [
        'terbaru' => 'Terbaru daftar',
        'nama' => 'Nama A-Z',
        'belanja_terbesar' => 'Belanja terbesar',
        'pesanan_terbanyak' => 'Pesanan terbanyak',
        'terakhir_belanja' => 'Terakhir belanja',
    ];
@endphp

<style>
    .buyer-toolbar {
        display: grid;
        grid-template-columns: minmax(280px, 1fr) 220px auto auto;
        gap: 10px;
        align-items: end;
    }
    .buyer-filter-label {
        display: block;
        margin-bottom: 6px;
        color: var(--muted);
        font-size: .72rem;
        font-weight: 950;
        text-transform: uppercase;
        letter-spacing: .055em;
    }
    .buyer-search {
        min-height: 42px;
        padding: 0 13px;
        border-radius: 14px;
        border: 1px solid var(--border);
        background: #fff;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .buyer-search input {
        width: 100%;
        border: 0;
        outline: 0;
        background: transparent;
        font-size: .88rem;
        font-weight: 750;
    }
    .buyer-avatar-soft {
        width: 42px;
        height: 42px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--brand), #ad7a24);
        color: #fff;
        font-size: .8rem;
        font-weight: 950;
        letter-spacing: -.03em;
        box-shadow: 0 10px 20px rgba(200,147,53,.18);
        flex-shrink: 0;
    }
    .buyer-mini-stat {
        border: 1px solid var(--border);
        border-radius: 18px;
        background: #fff;
        padding: 14px 15px;
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: center;
        min-height: 92px;
        box-shadow: var(--shadow-soft);
    }
    .buyer-mini-stat .label {
        color: var(--muted);
        font-size: .72rem;
        font-weight: 950;
        text-transform: uppercase;
        letter-spacing: .05em;
    }
    .buyer-mini-stat .value {
        margin-top: 7px;
        color: var(--text);
        font-size: 1.35rem;
        line-height: 1;
        font-weight: 950;
        letter-spacing: -.05em;
    }
    .buyer-mini-stat .icon {
        width: 42px;
        height: 42px;
        border-radius: 15px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--brand-soft);
        color: var(--brand-dark);
        flex-shrink: 0;
    }
    .buyer-tabs {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    .buyer-tab {
        border: 1px solid var(--border);
        background: #fff;
        color: #475467;
        padding: 8px 12px;
        border-radius: 999px;
        font-size: .78rem;
        font-weight: 900;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 7px;
    }
    .buyer-tab.active, .buyer-tab:hover {
        color: var(--brand-dark);
        border-color: #f1d49c;
        background: var(--brand-soft);
    }
    .buyer-table td { vertical-align: middle; }
    .buyer-name {
        color: var(--text);
        font-size: .92rem;
        font-weight: 950;
        letter-spacing: -.02em;
    }
    .buyer-meta {
        margin-top: 4px;
        color: var(--muted);
        font-size: .76rem;
        font-weight: 700;
        line-height: 1.45;
    }
    .buyer-number {
        color: var(--text);
        font-size: .9rem;
        font-weight: 950;
    }
    .buyer-empty {
        padding: 46px 16px;
        text-align: center;
        color: var(--muted);
        font-size: .86rem;
        font-weight: 750;
    }
    @media (max-width: 1200px) {
        .buyer-toolbar { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 680px) {
        .buyer-toolbar { grid-template-columns: 1fr; }
        .buyer-table th:nth-child(3), .buyer-table td:nth-child(3),
        .buyer-table th:nth-child(4), .buyer-table td:nth-child(4) { display:none; }
    }
</style>

<section class="hero">
    <div>
        <h1>Pembeli</h1>
        <p>Kelola akun pelanggan, lihat aktivitas belanja, dan pantau pelanggan yang sedang punya pesanan aktif.</p>
    </div>
    <a href="{{ route('admin.semua-pesanan.index') }}" class="btn btn-light border fw-bold px-3">
        <i class="bi bi-journal-text me-1 text-muted"></i>
        Semua Pesanan
    </a>
</section>

<div class="grid g4 mb-4">
    <div class="buyer-mini-stat">
        <div>
            <div class="label">Total Pembeli</div>
            <div class="value">{{ $stats['total'] ?? 0 }}</div>
        </div>
        <div class="icon"><i class="bi bi-people-fill"></i></div>
    </div>
    <div class="buyer-mini-stat">
        <div>
            <div class="label">Akun Aktif</div>
            <div class="value">{{ $stats['aktif'] ?? 0 }}</div>
        </div>
        <div class="icon"><i class="bi bi-person-check-fill"></i></div>
    </div>
    <div class="buyer-mini-stat">
        <div>
            <div class="label">Pesanan Aktif</div>
            <div class="value">{{ $stats['pesanan_aktif'] ?? 0 }}</div>
        </div>
        <div class="icon"><i class="bi bi-hourglass-split"></i></div>
    </div>
    <div class="buyer-mini-stat">
        <div>
            <div class="label">Total Belanja</div>
            <div class="value" style="font-size:1rem;">{{ $rupiah($stats['total_belanja'] ?? 0) }}</div>
        </div>
        <div class="icon"><i class="bi bi-cash-stack"></i></div>
    </div>
</div>

<div class="page-card overflow-hidden">
    <div class="p-3 p-lg-4 border-bottom bg-white">
        <div class="buyer-tabs mb-3">
            <a class="buyer-tab {{ $status === 'semua' ? 'active' : '' }}" href="{{ route('admin.pembeli.index', array_merge(request()->except(['page', 'aktivitas']), ['status' => 'semua'])) }}">Semua</a>
            <a class="buyer-tab {{ $status === 'aktif' ? 'active' : '' }}" href="{{ route('admin.pembeli.index', array_merge(request()->except(['page', 'aktivitas']), ['status' => 'aktif'])) }}">Aktif</a>
            <a class="buyer-tab {{ $status === 'nonaktif' ? 'active' : '' }}" href="{{ route('admin.pembeli.index', array_merge(request()->except(['page', 'aktivitas']), ['status' => 'nonaktif'])) }}">Nonaktif</a>
        </div>

        <form id="page-filter" method="GET" class="buyer-toolbar">
            <input type="hidden" name="status" value="{{ $status }}">
            <div>
                <label class="buyer-filter-label">Cari pembeli</label>
                <div class="buyer-search">
                    <i class="bi bi-search text-muted"></i>
                    <input name="q" value="{{ $search }}" placeholder="Nama, email, atau nomor HP">
                </div>
            </div>
            <div>
                <label class="buyer-filter-label">Urutkan</label>
                <select name="sort" class="form-select">
                    @foreach($sortOptions as $value => $label)
                        <option value="{{ $value }}" @selected($sort === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="d-grid">
                <label class="buyer-filter-label opacity-0">Terapkan</label>
                <button class="btn btn-brand" type="submit"><i class="bi bi-funnel me-1"></i> Terapkan</button>
            </div>
            <div class="d-grid">
                <label class="buyer-filter-label opacity-0">Reset</label>
                <a class="btn btn-light border" href="{{ route('admin.pembeli.index') }}">Reset</a>
            </div>
        </form>
    </div>

    <div class="table-wrap">
        <table class="buyer-table mb-0">
            <thead>
                <tr>
                    <th>Pembeli</th>
                    <th>Status</th>
                    <th class="text-center">Aktivitas</th>
                    <th>Nilai Belanja</th>
                    <th>Terakhir Belanja</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($pembeli as $user)
                @php
                    $lastOrder = $user->terakhir_belanja ? \Illuminate\Support\Carbon::parse($user->terakhir_belanja) : null;
                @endphp
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3 min-w-0">
                            <div class="buyer-avatar-soft">{{ strtoupper(substr($user->name ?? 'PB', 0, 2)) }}</div>
                            <div class="min-w-0">
                                <div class="buyer-name text-truncate">{{ $user->name }}</div>
                                <div class="buyer-meta text-truncate">
                                    {{ $user->email }}
                                    @if($user->telepon)
                                        · {{ $user->telepon }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="chip {{ $user->aktif ? 'c-green' : 'c-red' }}">{{ $user->aktif ? 'Aktif' : 'Nonaktif' }}</span>
                    </td>
                    <td class="text-center">
                        <div class="buyer-number">{{ $user->pesanan_count ?? 0 }} pesanan</div>
                        <span class="sub">{{ $user->ulasan_count ?? 0 }} ulasan · {{ $user->alamat_count ?? 0 }} alamat</span>
                    </td>
                    <td>
                        <div class="buyer-number">{{ $rupiah($user->total_belanja ?? 0) }}</div>
                        <span class="sub">Pembayaran berhasil</span>
                    </td>
                    <td>
                        @if($lastOrder)
                            <div class="buyer-number">{{ $lastOrder->format('d/m/Y') }}</div>
                            <span class="sub">{{ $lastOrder->format('H:i') }}</span>
                        @else
                            <span class="text-muted small fw-bold">Belum belanja</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="actions justify-content-end">
                            <button class="btn-action" type="button" data-bs-toggle="modal" data-bs-target="#buyerQuickModal{{ $user->id }}" title="Ringkasan">
                                <i class="bi bi-eye"></i>
                            </button>
                            <a class="btn-action" href="{{ route('admin.pembeli.show', $user) }}" title="Profil lengkap">
                                <i class="bi bi-person-lines-fill"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.pembeli.toggle', $user) }}" class="inline-form" data-confirm-title="Ubah Status Pembeli" data-confirm-message="Yakin ingin mengubah status akun {{ $user->name }}?" data-confirm-button="Ubah Status">
                                @csrf @method('PATCH')
                                <button class="btn-action" type="submit" title="{{ $user->aktif ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <i class="bi {{ $user->aktif ? 'bi-lock text-warning' : 'bi-unlock text-success' }}"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <div class="buyer-empty">
                            <i class="bi bi-people fs-1 d-block mb-2"></i>
                            <strong class="d-block text-dark mb-1">Data pembeli tidak ditemukan.</strong>
                            Ubah kata kunci atau filter untuk melihat data lain.
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($pembeli->hasPages())
        <div class="p-3 border-top bg-white">{{ $pembeli->links() }}</div>
    @endif
</div>

@foreach($pembeli as $user)
    @php
        $lastOrder = $user->terakhir_belanja ? \Illuminate\Support\Carbon::parse($user->terakhir_belanja) : null;
    @endphp
    <div class="modal fade" id="buyerQuickModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
                <div class="modal-body p-4">
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <div class="buyer-avatar-soft" style="width:56px;height:56px;border-radius:20px;">{{ strtoupper(substr($user->name ?? 'PB', 0, 2)) }}</div>
                        <div class="min-w-0 flex-grow-1">
                            <h5 class="fw-black text-dark mb-1">{{ $user->name }}</h5>
                            <div class="text-muted small fw-bold text-break">{{ $user->email }}</div>
                            <div class="mt-2"><span class="chip {{ $user->aktif ? 'c-green' : 'c-red' }}">{{ $user->aktif ? 'Aktif' : 'Nonaktif' }}</span></div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6"><div class="p-3 rounded-4 bg-light"><div class="small text-muted fw-bold">Pesanan</div><div class="fw-black">{{ $user->pesanan_count ?? 0 }}</div></div></div>
                        <div class="col-6"><div class="p-3 rounded-4 bg-light"><div class="small text-muted fw-bold">Ulasan</div><div class="fw-black">{{ $user->ulasan_count ?? 0 }}</div></div></div>
                        <div class="col-12"><div class="p-3 rounded-4 bg-light"><div class="small text-muted fw-bold">Total belanja</div><div class="fw-black">{{ $rupiah($user->total_belanja ?? 0) }}</div></div></div>
                    </div>
                    <div class="small text-muted fw-bold mb-1">Kontak</div>
                    <div class="fw-bold text-dark mb-3">{{ $user->telepon ?: 'Nomor HP belum diisi' }}</div>
                    <div class="small text-muted fw-bold mb-1">Terakhir belanja</div>
                    <div class="fw-bold text-dark">{{ $lastOrder ? $lastOrder->format('d/m/Y H:i') : 'Belum ada pesanan' }}</div>
                </div>
                <div class="modal-footer border-0 bg-light p-3">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Tutup</button>
                    <a href="{{ route('admin.pembeli.show', $user) }}" class="btn btn-brand">Buka Profil Lengkap</a>
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection
