@extends('layouts.admin')

@section('title', 'Semua Pesanan - SiTahu')
@section('page_title', 'Semua Pesanan')
@section('page_subtitle', 'Arsip semua invoice dari pesanan aktif, selesai, sampai dibatalkan.')

@section('content')
@include('admin.partials.ops-page-style')

@php
    $pickupLabel = fn($method) => match($method){'ambil_toko'=>'Ambil toko','kurir_toko'=>'Kurir toko',default=>$statusLabel($method)};
    $paymentMethodLabel = fn($method) => match($method){'transfer_bank'=>'Transfer Bank','cod'=>'COD',default=>strtoupper((string)$method)};
    $tab = request('tab', 'semua');
    $hasActiveFilter = request()->filled('q') || request()->filled('status') || request()->filled('status_pembayaran') || request()->filled('metode_pengambilan') || request()->filled('metode_pembayaran') || request()->filled('tanggal_mulai') || request()->filled('tanggal_selesai');
@endphp

<div class="ops-page-head compact">
    <div>
        <h1 class="ops-title">Semua Pesanan</h1>
        <p class="ops-subtitle">Menu ini menjadi arsip utama seluruh invoice. Gunakan halaman ini untuk mencari pesanan lama, melihat detail, dan mencetak invoice.</p>
    </div>
</div>

<div class="ops-tabs">
    <a class="ops-tab {{ $tab==='semua' && !request()->filled('status') ? 'active' : '' }}" href="{{ route('admin.semua-pesanan.index') }}">Semua <b>{{ $stats['semua'] ?? 0 }}</b></a>
    <a class="ops-tab {{ $tab==='aktif' ? 'active' : '' }}" href="{{ route('admin.semua-pesanan.index', ['tab'=>'aktif']) }}">Aktif <b>{{ $stats['aktif'] ?? 0 }}</b></a>
    <a class="ops-tab {{ $tab==='selesai' || request('status')==='selesai' ? 'active' : '' }}" href="{{ route('admin.semua-pesanan.index', ['tab'=>'selesai']) }}">Selesai <b>{{ $stats['selesai'] ?? 0 }}</b></a>
    <a class="ops-tab {{ $tab==='dibatalkan' || request('status')==='dibatalkan' ? 'active' : '' }}" href="{{ route('admin.semua-pesanan.index', ['tab'=>'dibatalkan']) }}">Batal <b>{{ $stats['dibatalkan'] ?? 0 }}</b></a>
</div>

<div class="ops-filter-card compact">
    <form id="page-filter" class="js-instant-filter" method="GET">
        @if($tab !== 'semua')<input type="hidden" name="tab" value="{{ $tab }}">@endif
        <div class="ops-filter-grid orders">
            <div class="ops-field"><label class="ops-label">Cari</label><div class="ops-search"><i class="bi bi-search text-muted"></i><input name="q" value="{{ request('q') }}" placeholder="Invoice, pembeli, HP, atau produk"></div></div>
            <div class="ops-field"><label class="ops-label">Status pesanan</label><select class="ops-control" name="status"><option value="">Semua</option>@foreach(['menunggu_pembayaran','menunggu_verifikasi','menunggu_konfirmasi','diproses','disiapkan','siap_diambil','dalam_pengantaran','selesai','dibatalkan'] as $s)<option value="{{ $s }}" @selected(request('status')===$s)>{{ $statusLabel($s) }}</option>@endforeach</select></div>
            <div class="ops-field"><label class="ops-label">Status bayar</label><select class="ops-control" name="status_pembayaran"><option value="">Semua</option>@foreach(['menunggu_pembayaran','menunggu_verifikasi','dibayar','ditolak','dibatalkan'] as $s)<option value="{{ $s }}" @selected(request('status_pembayaran')===$s)>{{ $statusLabel($s) }}</option>@endforeach</select></div>
            <div class="ops-field"><label class="ops-label">Metode bayar</label><select class="ops-control" name="metode_pembayaran"><option value="">Semua</option><option value="transfer_bank" @selected(request('metode_pembayaran')==='transfer_bank')>Transfer</option><option value="cod" @selected(request('metode_pembayaran')==='cod')>COD</option></select></div>
            <div class="ops-field"><label class="ops-label">Metode ambil</label><select class="ops-control" name="metode_pengambilan"><option value="">Semua</option><option value="ambil_toko" @selected(request('metode_pengambilan')==='ambil_toko')>Ambil toko</option><option value="kurir_toko" @selected(request('metode_pengambilan')==='kurir_toko')>Kurir toko</option></select></div>
            <div class="ops-field"><label class="ops-label">Dari</label><input type="date" class="ops-control" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}"></div>
            <div class="ops-field"><label class="ops-label">Sampai</label><input type="date" class="ops-control" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}"></div>
            <div class="ops-filter-actions"><a href="{{ route('admin.semua-pesanan.index') }}" class="ops-btn-reset"><i class="bi bi-x-circle"></i> Reset</a></div>
        </div>
        @if($hasActiveFilter || $tab !== 'semua')<div class="ops-filter-note"><i class="bi bi-funnel text-brand"></i> Filter aktif. <a href="{{ route('admin.semua-pesanan.index') }}" class="text-brand fw-black text-decoration-none">Bersihkan</a></div>@endif
    </form>
</div>

@if($pesanan->count())
    <div class="ops-table-card table-wrap">
        <table class="table align-middle ops-table-compact">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Pembeli</th>
                    <th>Produk</th>
                    <th>Metode</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @foreach($pesanan as $order)
                @php
                    $buyer = $order->user;
                    $pay = $order->pembayaran;
                    $ship = $order->pengiriman;
                    $alamat = $order->alamatPengiriman;
                    $initial = strtoupper(substr($buyer?->name ?? 'PB',0,2));
                    $firstItem = $order->item->first();
                    $firstProduct = $firstItem?->produk;
                    $img = $firstProduct?->gambarUtama?->url_gambar ? asset('storage/'.$firstProduct->gambarUtama->url_gambar) : null;
                    $moreCount = max(0, $order->item->count() - 1);
                    $proofUrl = $pay?->bukti_transfer ? asset('storage/'.$pay->bukti_transfer) : null;
                    $isPdfProof = $pay?->bukti_transfer && str_ends_with(strtolower($pay->bukti_transfer), '.pdf');
                @endphp
                <tr>
                    <td><button type="button" class="ops-link action-modal-btn text-start" data-bs-toggle="modal" data-bs-target="#allOrderDetail{{ $order->id }}">{{ $order->nomor_invoice }}</button><span class="ops-muted">{{ optional($order->tanggal_pesanan)->format('d M Y H:i') }}</span></td>
                    <td><div class="d-flex align-items-center gap-2 min-w-0"><span class="ops-avatar">{{ $initial }}</span><div class="min-w-0"><div class="fw-black text-truncate">{{ $buyer?->name ?? 'Pembeli' }}</div><span class="ops-muted text-truncate">{{ $buyer?->telepon ?: $buyer?->email }}</span></div></div></td>
                    <td><div class="d-flex align-items-center gap-2 min-w-0">@if($img)<img src="{{ $img }}" class="ops-cover" alt="{{ $firstProduct?->nama }}">@else<span class="ops-cover-fallback"><i class="bi bi-box-seam"></i></span>@endif<div class="min-w-0"><div class="fw-black text-truncate">{{ $firstProduct?->nama ?? 'Produk' }}</div><span class="ops-muted">{{ $order->item->sum('jumlah') }} item{{ $moreCount ? ' · +'.$moreCount.' produk' : '' }}</span></div></div></td>
                    <td><div class="d-flex flex-column gap-1 align-items-start"><span class="ops-pill"><i class="bi {{ $pay?->metode_pembayaran === 'cod' ? 'bi-cash-coin text-success' : 'bi-bank text-primary' }}"></i>{{ $paymentMethodLabel($pay?->metode_pembayaran) }}</span><span class="ops-pill"><i class="bi {{ $order->metode_pengambilan === 'kurir_toko' ? 'bi-truck text-primary' : 'bi-shop text-warning' }}"></i>{{ $pickupLabel($order->metode_pengambilan) }}</span></div></td>
                    <td><div class="d-flex flex-column gap-1 align-items-start"><span class="chip {{ $statusClass($order->status) }}">{{ $statusLabel($order->status) }}</span><span class="chip {{ $statusClass($order->status_pembayaran) }}">Bayar: {{ $statusLabel($order->status_pembayaran) }}</span></div></td>
                    <td class="fw-black">{{ $rupiah($order->total_bayar) }}</td>
                    <td><div class="ops-actions"><button type="button" class="small-btn" data-bs-toggle="modal" data-bs-target="#allOrderDetail{{ $order->id }}"><i class="bi bi-eye"></i> Detail</button><a href="{{ route('admin.pesanan.invoice', $order) }}" target="_blank" class="small-btn"><i class="bi bi-printer"></i> Cetak Invoice</a></div></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="ops-empty"><i class="bi bi-journal-text fs-2 text-muted"></i><strong class="d-block mt-2">Belum ada pesanan</strong><span class="text-muted fw-bold small">Semua invoice akan muncul di halaman ini setelah pembeli checkout.</span></div>
@endif

@foreach($pesanan as $order)
    @php
        $buyer = $order->user;
        $pay = $order->pembayaran;
        $ship = $order->pengiriman;
        $alamat = $order->alamatPengiriman;
        $proofUrl = $pay?->bukti_transfer ? asset('storage/'.$pay->bukti_transfer) : null;
        $isPdfProof = $pay?->bukti_transfer && str_ends_with(strtolower($pay->bukti_transfer), '.pdf');
    @endphp
    <div class="modal fade" id="allOrderDetail{{ $order->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content ops-modal">
                <div class="modal-header"><div><h5 class="modal-title fw-black">Detail pesanan</h5><div class="ops-muted">{{ $order->nomor_invoice }} · {{ optional($order->tanggal_pesanan)->format('d M Y H:i') }}</div></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body modal-body-soft">
                    <div class="row g-3">
                        <div class="col-lg-8">
                            <div class="detail-modal-card mb-3"><span class="detail-label">Pembeli</span><div class="detail-value">{{ $buyer?->name ?? '-' }}</div><div class="ops-muted">{{ $buyer?->telepon ?: '-' }} · {{ $buyer?->email ?: '-' }}</div></div>
                            <div class="detail-modal-card mb-3"><span class="detail-label">Produk dipesan</span><div class="detail-list">@foreach($order->item as $item)<div class="detail-product"><div><div class="fw-black">{{ $item->produk?->nama ?? 'Produk' }}</div><div class="ops-muted">{{ $item->jumlah }} × {{ $rupiah($item->harga_satuan) }}</div></div><strong>{{ $rupiah($item->subtotal) }}</strong></div>@endforeach</div></div>
                            <div class="detail-modal-card"><span class="detail-label">Alamat / pengambilan</span><div class="detail-value">{{ $pickupLabel($order->metode_pengambilan) }}</div><div class="ops-muted">{{ $order->metode_pengambilan === 'kurir_toko' ? ($alamat?->alamat_lengkap ?? $ship?->alamat_tujuan ?? 'Alamat belum tersedia') : ($ship?->alamat_toko ?? 'Ambil di toko') }}</div></div>
                        </div>
                        <div class="col-lg-4">
                            <div class="detail-modal-card mb-3"><span class="detail-label">Status</span><div class="d-flex flex-wrap gap-2"><span class="chip {{ $statusClass($order->status) }}">{{ $statusLabel($order->status) }}</span><span class="chip {{ $statusClass($order->status_pembayaran) }}">{{ $statusLabel($order->status_pembayaran) }}</span></div></div>
                            <div class="detail-modal-card mb-3"><span class="detail-label">Pembayaran</span><div class="summary-row"><span>Metode</span><strong>{{ $paymentMethodLabel($pay?->metode_pembayaran) }}</strong></div><div class="summary-row"><span>Subtotal</span><strong>{{ $rupiah($order->subtotal_produk) }}</strong></div><div class="summary-row"><span>Ongkir</span><strong>{{ $rupiah($order->biaya_pengiriman) }}</strong></div><div class="summary-row"><span>Total</span><strong>{{ $rupiah($order->total_bayar) }}</strong></div>@if($pay?->catatan_admin)<div class="ops-muted mt-2">Catatan admin: {{ $pay->catatan_admin }}</div>@endif</div>
                            @if($proofUrl)<div class="detail-modal-card mb-3"><span class="detail-label">Bukti transfer</span>@if($isPdfProof)<a href="{{ $proofUrl }}" target="_blank" class="btn btn-soft-brand btn-sm">Buka PDF</a>@else<button type="button" class="proof-button mt-1" data-bs-toggle="modal" data-bs-target="#allOrderProof{{ $order->id }}"><img src="{{ $proofUrl }}" class="proof-thumb" alt="Bukti transfer"><span>Lihat besar</span></button>@endif</div>@endif
                            <a href="{{ route('admin.pesanan.invoice', $order) }}" target="_blank" class="btn btn-brand w-100"><i class="bi bi-printer me-1"></i> Cetak invoice</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if($proofUrl && ! $isPdfProof)
        <div class="modal fade" id="allOrderProof{{ $order->id }}" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-xl modal-dialog-centered"><div class="modal-content proof-modal-content"><div class="modal-header"><div><h5 class="modal-title fw-black">Bukti transfer</h5><div class="ops-muted">{{ $order->nomor_invoice }}</div></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body text-center proof-modal-body"><img src="{{ $proofUrl }}" class="proof-large" alt="Bukti transfer"></div></div></div></div>
    @endif
@endforeach

<div class="ops-footer"><div class="text-muted small fw-bold">Menampilkan {{ $pesanan->count() }} dari {{ $pesanan->total() }} pesanan.</div><div>{{ $pesanan->links() }}</div></div>
@endsection
