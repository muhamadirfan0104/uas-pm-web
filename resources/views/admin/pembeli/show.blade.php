@extends('layouts.admin')
@section('title', 'Detail Pembeli - SiTahu')

@section('content')
<style>
    /* Styling Standar CRM */
    .sc-box { border: 1px solid #e5e7eb; border-radius: 0.75rem; background: #fff; margin-bottom: 1.5rem; overflow: hidden; box-shadow: 0 1px 2px rgba(0,0,0,0.02); }
    .sc-header { padding: 1.25rem 1.5rem; font-weight: 700; font-size: 1rem; border-bottom: 1px solid #f3f4f6; color: #111827; }
    
    .profile-hero { background: linear-gradient(135deg, var(--brand-active-bg, #fbf1d4), #f9fafb); padding: 2rem 1.5rem; text-align: center; border-bottom: 1px solid #e5e7eb; }
    .profile-avatar { width: 80px; height: 80px; border-radius: 50%; font-size: 2rem; font-weight: 800; color: #fff; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; background: var(--brand-color, #dfba68); box-shadow: 0 4px 10px rgba(223, 186, 104, 0.3); border: 3px solid #fff; }
    
    .info-list { display: flex; flex-direction: column; }
    .info-item { padding: 1rem 1.5rem; border-bottom: 1px solid #f3f4f6; display: flex; flex-direction: column; gap: 0.25rem; }
    .info-item:last-child { border-bottom: none; }
    
    .address-card { border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 1rem; margin-bottom: 1rem; background: #f9fafb; transition: border-color 0.2s; }
    .address-card:hover { border-color: var(--brand-color, #dfba68); background: #fff; }
    .address-card:last-child { margin-bottom: 0; }
    
    .map-placeholder { height: 200px; background-color: #e5e7eb; background-image: radial-gradient(#d1d5db 1px, transparent 0); background-size: 20px 20px; display: flex; align-items: center; justify-content: center; position: relative; }
    .map-pin { font-size: 2.5rem; color: #ef4444; filter: drop-shadow(0 4px 4px rgba(0,0,0,0.2)); }
    
    .table-enterprise th { border-bottom: 2px solid #e5e7eb; color: #6b7280; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 1rem 1.5rem; font-weight: 600; background: #fafafa; }
    .table-enterprise td { vertical-align: middle; padding: 1rem 1.5rem; border-bottom: 1px solid #f3f4f6; color: #111827; }
</style>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('admin.pembeli.index') }}" class="btn btn-light border rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 class="h4 fw-bold text-dark mb-1">Profil Pelanggan</h1>
            <p class="text-muted small mb-0">Detail informasi dan riwayat transaksi pembeli.</p>
        </div>
    </div>
</div>

<div class="row g-4">
    
    <div class="col-12 col-xl-4">
        
        <div class="sc-box">
            <div class="profile-hero">
                <div class="profile-avatar">
                    {{ strtoupper(substr($pembeli->name, 0, 2)) }}
                </div>
                <h2 class="h5 fw-bold text-dark mb-1">{{ $pembeli->name }}</h2>
                <div class="badge rounded-pill {{ $pembeli->aktif ? 'bg-success-subtle text-success-emphasis' : 'bg-danger-subtle text-danger-emphasis' }} mb-2">
                    {{ $pembeli->aktif ? 'Akun Aktif' : 'Akun Nonaktif' }}
                </div>
                <div class="text-muted small">Bergabung sejak {{ $pembeli->created_at->format('d M Y') }}</div>
            </div>
            <div class="info-list">
                <div class="info-item">
                    <span class="text-muted small fw-bold text-uppercase">Email</span>
                    <span class="text-dark fw-medium">{{ $pembeli->email }}</span>
                </div>
                <div class="info-item">
                    <span class="text-muted small fw-bold text-uppercase">Telepon / WhatsApp</span>
                    <span class="text-dark fw-medium">{{ $pembeli->telepon ?? 'Belum ditambahkan' }}</span>
                </div>
            </div>
        </div>

        <div class="sc-box">
            <div class="sc-header">
                <i class="bi bi-geo-alt text-danger me-2"></i> Peta Lokasi (Utama)
            </div>
            <div class="map-placeholder">
                <i class="bi bi-geo-alt-fill map-pin"></i>
                <div class="position-absolute bottom-0 start-0 w-100 p-2 bg-white bg-opacity-75 text-center small fw-medium">
                    Koordinat GPS Mobile
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-8">
        
        <div class="sc-box">
            <div class="sc-header">
                Buku Alamat Tersimpan
            </div>
            <div class="p-4">
                @forelse($pembeli->alamat as $alamat)
                    <div class="address-card d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <strong class="text-dark">{{ $alamat->nama_penerima }}</strong>
                                @if($alamat->utama)
                                    <span class="badge bg-success-subtle text-success-emphasis rounded-pill" style="font-size: 0.65rem;">Alamat Utama</span>
                                @endif
                            </div>
                            <p class="text-muted small mb-0 lh-lg" style="max-width: 500px;">
                                {{ $alamat->alamat_lengkap }}
                            </p>
                        </div>
                        <i class="bi bi-house-door text-muted fs-4 opacity-50"></i>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <i class="bi bi-journal-x fs-1 text-muted mb-2 d-block"></i>
                        <strong class="text-dark">Belum ada alamat tersimpan</strong>
                        <div class="text-muted small mt-1">Alamat akan otomatis muncul setelah pembeli menambahkannya via aplikasi mobile.</div>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="sc-box mb-0">
            <div class="sc-header d-flex justify-content-between align-items-center">
                <span>10 Riwayat Pesanan Terbaru</span>
                <span class="badge bg-light text-secondary border fw-normal">{{ $pembeli->pesanan->count() }} Total Transaksi</span>
            </div>
            <div class="table-responsive bg-white">
                <table class="table table-enterprise table-borderless mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">No. Invoice</th>
                            <th>Tanggal</th>
                            <th>Status Transaksi</th>
                            <th class="text-end pe-4">Total Bayar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pembeli->pesanan as $order)
                        <tr>
                            <td class="ps-4">
                                <strong class="text-dark">{{ $order->nomor_invoice }}</strong>
                            </td>
                            <td class="text-muted small">
                                {{ optional($order->tanggal_pesanan)->format('d M Y, H:i') }}
                            </td>
                            <td>
                                <span class="badge rounded-pill fw-medium px-2 py-1 {{ str_replace('text-bg-', 'bg-', $statusClass($order->status)) }}-subtle text-{{ str_replace('text-bg-', '', $statusClass($order->status)) }}-emphasis" style="font-size: 0.75rem;">
                                    {{ $statusLabel($order->status) }}
                                </span>
                            </td>
                            <td class="text-end pe-4 fw-bold text-dark">
                                {{ $rupiah($order->total_bayar) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <i class="bi bi-cart-x fs-2 text-muted mb-2 d-block"></i>
                                <strong class="text-dark">Belum pernah berbelanja</strong>
                                <div class="text-muted small mt-1">Riwayat pesanan akan dicatat saat pembeli melakukan checkout.</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection