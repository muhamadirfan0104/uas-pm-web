@extends('layouts.admin')

@section('title', 'Pesanan - SiTahu')
@section('page_title', 'Pesanan')

@section('content')
@include('admin.partials.ops-page-style')

@php
    $pickupLabel = fn($method) => match($method){'ambil_toko'=>'Ambil toko','kurir_toko'=>'Kurir toko',default=>$statusLabel($method)};
    $paymentMethodLabel = fn($method) => match($method){'transfer_bank'=>'Transfer Bank','cod'=>'COD',default=>strtoupper((string)$method)};
    $nextStatus = fn($order) => \App\Support\OrderFlow::nextOrderStatus($order);
    $actionLabel = function($status, $order = null) {
        return match($status) {
            'diproses' => 'Proses',
            'disiapkan' => 'Siapkan',
            'siap_diambil' => 'Siap diambil',
            'dalam_pengantaran' => 'Kirim',
            'selesai' => (($order?->pembayaran?->metode_pembayaran ?? null) === 'cod') ? 'Selesai & bayar COD' : 'Selesaikan',
            default => 'Lanjut',
        };
    };
    $flowText = fn($order) => $order?->metode_pengambilan === 'kurir_toko'
        ? 'Bayar/COD → Konfirmasi → Diproses → Disiapkan → Dalam pengantaran → Selesai'
        : 'Bayar/COD → Konfirmasi → Diproses → Disiapkan → Siap diambil → Selesai';
    $tab = request('tab', 'semua');
    $hasActiveFilter = request()->filled('q') || request()->filled('status') || request()->filled('metode_pengambilan') || request()->filled('metode_pembayaran') || request()->filled('tanggal_mulai') || request()->filled('tanggal_selesai');
@endphp

<div class="ops-page-head compact">
    <div>
        <h1 class="ops-title">Pesanan</h1>
        <p class="ops-subtitle">Menu ini hanya menampilkan pesanan aktif yang harus dikerjakan toko: konfirmasi pesanan dan proses persiapan. Pesanan selesai, batal, belum bayar, dan tahap ambil/kirim dipindah ke menu masing-masing.</p>
    </div>
</div>

<div class="ops-tabs">
    <a class="ops-tab {{ $tab==='semua' && !request()->filled('status') ? 'active' : '' }}" href="{{ route('admin.pesanan.index') }}">Semua aktif <b>{{ $stats['aktif'] ?? 0 }}</b></a>
    <a class="ops-tab {{ $tab==='konfirmasi' || request('status')==='menunggu_konfirmasi' ? 'active' : '' }}" href="{{ route('admin.pesanan.index', ['tab'=>'konfirmasi']) }}">Konfirmasi <b>{{ $stats['konfirmasi'] ?? 0 }}</b></a>
    <a class="ops-tab {{ $tab==='diproses' || request('status')==='diproses' ? 'active' : '' }}" href="{{ route('admin.pesanan.index', ['tab'=>'diproses']) }}">Diproses <b>{{ $stats['diproses'] ?? 0 }}</b></a>
    <a class="ops-tab" href="{{ route('admin.pengiriman.index') }}">Lihat ambil/kirim <i class="bi bi-arrow-right"></i></a>
    <a class="ops-tab" href="{{ route('admin.semua-pesanan.index') }}">Semua Pesanan <i class="bi bi-journal-text"></i></a>
</div>

<div class="ops-filter-card compact">
    <form id="page-filter" class="js-instant-filter" method="GET">
        @if($tab !== 'semua')<input type="hidden" name="tab" value="{{ $tab }}">@endif
        <div class="ops-filter-grid order-only">
            <div class="ops-field"><label class="ops-label">Cari</label><div class="ops-search"><i class="bi bi-search text-muted"></i><input name="q" value="{{ request('q') }}" placeholder="Invoice, pembeli, HP, atau produk"></div></div>
            <div class="ops-field"><label class="ops-label">Status</label><select class="ops-control" name="status"><option value="">Semua aktif</option>@foreach(['menunggu_konfirmasi','diproses'] as $s)<option value="{{ $s }}" @selected(request('status')===$s)>{{ $statusLabel($s) }}</option>@endforeach</select></div>
            <div class="ops-field"><label class="ops-label">Metode ambil</label><select class="ops-control" name="metode_pengambilan"><option value="">Semua</option><option value="ambil_toko" @selected(request('metode_pengambilan')==='ambil_toko')>Ambil toko</option><option value="kurir_toko" @selected(request('metode_pengambilan')==='kurir_toko')>Kurir toko</option></select></div>
            <div class="ops-field"><label class="ops-label">Bayar</label><select class="ops-control" name="metode_pembayaran"><option value="">Semua</option><option value="transfer_bank" @selected(request('metode_pembayaran')==='transfer_bank')>Transfer</option><option value="cod" @selected(request('metode_pembayaran')==='cod')>COD</option></select></div>
            <div class="ops-field"><label class="ops-label">Dari</label><input type="date" class="ops-control" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}"></div>
            <div class="ops-field"><label class="ops-label">Sampai</label><input type="date" class="ops-control" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}"></div>
            <div class="ops-filter-actions"><a href="{{ route('admin.pesanan.index') }}" class="ops-btn-reset"><i class="bi bi-x-circle"></i> Reset</a></div>
        </div>
        @if($hasActiveFilter || $tab !== 'semua')<div class="ops-filter-note"><i class="bi bi-funnel text-brand"></i> Filter aktif. <a href="{{ route('admin.pesanan.index') }}" class="text-brand fw-black text-decoration-none">Bersihkan</a></div>@endif
    </form>
</div>

@if($pesanan->count())
    <div class="ops-table-card table-wrap">
        <table class="table align-middle ops-table-compact">
            <thead><tr><th>Invoice</th><th>Pembeli</th><th>Produk</th><th>Pengambilan</th><th>Status</th><th>Total</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
            @foreach($pesanan as $order)
                @php
                    $buyer = $order->user;
                    $initial = strtoupper(substr($buyer?->name ?? 'PB',0,2));
                    $firstItem = $order->item->first();
                    $firstProduct = $firstItem?->produk;
                    $img = $firstProduct?->gambarUtama?->url_gambar ? asset('storage/'.$firstProduct->gambarUtama->url_gambar) : null;
                    $moreCount = max(0, $order->item->count() - 1);
                    $next = $nextStatus($order);
                    $pay = $order->pembayaran;
                    $ship = $order->pengiriman;
                    $alamat = $order->alamatPengiriman;
                    $needsTransferReview = $pay && $pay->metode_pembayaran === 'transfer_bank' && in_array($pay->status, ['menunggu_pembayaran','menunggu_verifikasi'], true) && filled($pay->bukti_transfer);
                @endphp
                <tr>
                    <td><button type="button" class="ops-link action-modal-btn text-start" data-bs-toggle="modal" data-bs-target="#orderDetail{{ $order->id }}">{{ $order->nomor_invoice }}</button><span class="ops-muted">{{ optional($order->tanggal_pesanan)->format('d M Y H:i') }}</span></td>
                    <td><div class="d-flex align-items-center gap-2 min-w-0"><span class="ops-avatar">{{ $initial }}</span><div class="min-w-0"><div class="fw-black text-truncate">{{ $buyer?->name ?? 'Pembeli' }}</div><span class="ops-muted text-truncate">{{ $buyer?->telepon ?: $buyer?->email }}</span></div></div></td>
                    <td><div class="d-flex align-items-center gap-2 min-w-0">@if($img)<img src="{{ $img }}" class="ops-cover" alt="{{ $firstProduct?->nama }}">@else<span class="ops-cover-fallback"><i class="bi bi-box-seam"></i></span>@endif<div class="min-w-0"><div class="fw-black text-truncate">{{ $firstProduct?->nama ?? 'Produk' }}</div><span class="ops-muted">{{ $order->item->sum('jumlah') }} item{{ $moreCount ? ' · +'.$moreCount.' produk' : '' }}</span></div></div></td>
                    <td><span class="ops-pill"><i class="bi {{ $order->metode_pengambilan === 'kurir_toko' ? 'bi-truck text-primary' : 'bi-shop text-warning' }}"></i>{{ $pickupLabel($order->metode_pengambilan) }}</span></td>
                    <td><div class="d-flex flex-column gap-1 align-items-start"><span class="chip {{ $statusClass($order->status) }}">{{ $statusLabel($order->status) }}</span>@if($needsTransferReview)<span class="chip c-yellow">Cek di pembayaran</span>@endif</div></td>
                    <td class="fw-black">{{ $rupiah($order->total_bayar) }}</td>
                    <td><div class="ops-actions">
                        @if($next && ! $needsTransferReview)
                            <form method="POST" action="{{ route('admin.pesanan.status', $order) }}" data-confirm-title="Lanjutkan status" data-confirm-message="{{ $flowText($order) }}. Lanjutkan {{ $order->nomor_invoice }} ke tahap {{ $statusLabel($next) }}?" data-confirm-button="Lanjutkan">@csrf @method('PATCH')<input type="hidden" name="status" value="{{ $next }}"><button class="small-btn text-brand" type="submit"><i class="bi bi-arrow-right-circle"></i> {{ $actionLabel($next, $order) }}</button></form>
                        @elseif($needsTransferReview)
                            <span class="small-btn text-muted"><i class="bi bi-shield-lock"></i> Cek bayar</span>
                        @endif
                        <button type="button" class="small-btn" data-bs-toggle="modal" data-bs-target="#orderDetail{{ $order->id }}"><i class="bi bi-eye"></i> Detail</button>
                        <a href="{{ route('admin.pesanan.invoice', $order) }}" target="_blank" class="small-btn"><i class="bi bi-printer"></i></a>
                        @if(!in_array($order->status, ['selesai','dibatalkan'], true))
                            <form method="POST" action="{{ route('admin.pesanan.status', $order) }}" data-confirm-title="Batalkan pesanan" data-confirm-message="Batalkan {{ $order->nomor_invoice }}? Stok produk akan dikembalikan." data-confirm-button="Batalkan">@csrf @method('PATCH')<input type="hidden" name="status" value="dibatalkan"><button class="small-btn text-danger" type="submit"><i class="bi bi-x-circle"></i></button></form>
                        @endif
                    </div></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="ops-empty"><i class="bi bi-receipt fs-2 text-muted"></i><strong class="d-block mt-2">Tidak ada pesanan aktif</strong><span class="text-muted fw-bold small">Pesanan selesai/batal ada di menu Semua Pesanan. Tahap ambil/kirim ada di menu Pengambilan & Kirim.</span></div>
@endif

@foreach($pesanan as $order)
    @php
        $buyer = $order->user;
        $pay = $order->pembayaran;
        $ship = $order->pengiriman;
        $alamat = $order->alamatPengiriman;
        $steps = \App\Support\OrderFlow::steps($order);
        $currentIndex = array_search($order->status, $steps, true);
        $nextInModal = \App\Support\OrderFlow::nextOrderStatus($order);
        $proofUrl = $pay?->bukti_transfer ? asset('storage/'.$pay->bukti_transfer) : null;
        $isPdfProof = $pay?->bukti_transfer && str_ends_with(strtolower($pay->bukti_transfer), '.pdf');
    @endphp
    <div class="modal fade" id="orderDetail{{ $order->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content ops-modal">
                <div class="modal-header"><div><h5 class="modal-title fw-black">Detail pesanan</h5><div class="ops-muted">{{ $order->nomor_invoice }} · {{ optional($order->tanggal_pesanan)->format('d M Y H:i') }}</div></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body modal-body-soft">
                    <div class="row g-3">
                        <div class="col-lg-8">
                            <div class="detail-modal-card mb-3"><span class="detail-label">Pembeli</span><div class="detail-value">{{ $buyer?->name ?? '-' }}</div><div class="ops-muted">{{ $buyer?->telepon ?: '-' }} · {{ $buyer?->email ?: '-' }}</div></div>
                            <div class="detail-modal-card mb-3"><span class="detail-label">Produk dipesan</span><div class="detail-list">@foreach($order->item as $item)<div class="detail-product"><div><div class="fw-black">{{ $item->produk?->nama ?? 'Produk' }}</div><div class="ops-muted">{{ $item->jumlah }} × {{ $rupiah($item->harga_satuan) }}</div></div><strong>{{ $rupiah($item->subtotal) }}</strong></div>@endforeach</div></div>
                            <div class="detail-modal-card"><span class="detail-label">Pengambilan</span><div class="detail-value">{{ $pickupLabel($order->metode_pengambilan) }}</div><div class="ops-muted">{{ $order->metode_pengambilan === 'kurir_toko' ? ($alamat?->alamat_lengkap ?? $ship?->alamat_tujuan ?? 'Alamat belum tersedia') : ($ship?->alamat_toko ?? 'Alamat toko') }}</div></div>
                        </div>
                        <div class="col-lg-4">
                            <div class="detail-modal-card mb-3">
                                <span class="detail-label">Alur pesanan</span>
                                <div class="d-flex flex-wrap gap-2 mb-2"><span class="chip {{ $statusClass($order->status) }}">{{ $statusLabel($order->status) }}</span><span class="ops-pill">{{ $paymentMethodLabel($pay?->metode_pembayaran) }}</span></div>
                                <div class="flow-steps">
                                    @foreach($steps as $idx => $step)
                                        @php $done = $currentIndex !== false && $idx < $currentIndex; $current = $order->status === $step; $isNextStep = $nextInModal === $step; @endphp
                                        @if($isNextStep)
                                            <form method="POST" action="{{ route('admin.pesanan.status', $order) }}" class="flow-step-form" data-confirm-title="Lanjutkan status" data-confirm-message="{{ $flowText($order) }}. Lanjutkan {{ $order->nomor_invoice }} ke tahap {{ $statusLabel($step) }}?" data-confirm-button="Lanjutkan">@csrf @method('PATCH')<input type="hidden" name="status" value="{{ $step }}"><button class="flow-step action" type="submit"><i class="bi bi-arrow-right-circle"></i>{{ $actionLabel($step, $order) }}</button></form>
                                        @else
                                            <span class="flow-step {{ $done ? 'done' : ($current ? 'current' : 'locked') }}"><i class="bi {{ $done ? 'bi-check2-circle' : ($current ? 'bi-record-circle' : 'bi-lock') }}"></i>{{ $statusLabel($step) }}</span>
                                        @endif
                                    @endforeach
                                </div>
                                @if($pay?->metode_pembayaran === 'transfer_bank' && in_array($pay->status, ['menunggu_pembayaran','menunggu_verifikasi'], true))
                                    <div class="flow-help">Transfer bank harus diterima di menu Pembayaran. Setelah sah, pesanan masuk Konfirmasi dan bisa diproses di sini.</div>
                                @endif
                            </div>
                            <div class="detail-modal-card mb-3"><span class="detail-label">Pembayaran</span><div class="summary-row"><span>Metode</span><strong>{{ $paymentMethodLabel($pay?->metode_pembayaran) }}</strong></div><div class="summary-row"><span>Status</span><strong>{{ $statusLabel($order->status_pembayaran) }}</strong></div><div class="summary-row"><span>Subtotal</span><strong>{{ $rupiah($order->subtotal_produk) }}</strong></div><div class="summary-row"><span>Ongkir</span><strong>{{ $rupiah($order->biaya_pengiriman) }}</strong></div><div class="summary-row"><span>Total</span><strong>{{ $rupiah($order->total_bayar) }}</strong></div></div>
                            @if($proofUrl)<div class="detail-modal-card"><span class="detail-label">Bukti transfer</span>@if($isPdfProof)<a href="{{ $proofUrl }}" target="_blank" class="btn btn-soft-brand btn-sm">Buka PDF</a>@else<button type="button" class="proof-button mt-1" data-bs-toggle="modal" data-bs-target="#orderProof{{ $order->id }}"><img src="{{ $proofUrl }}" class="proof-thumb" alt="Bukti transfer"><span>Lihat besar</span></button>@endif</div>@endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if($proofUrl && ! $isPdfProof)
        <div class="modal fade" id="orderProof{{ $order->id }}" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-xl modal-dialog-centered"><div class="modal-content proof-modal-content"><div class="modal-header"><div><h5 class="modal-title fw-black">Bukti transfer</h5><div class="ops-muted">{{ $order->nomor_invoice }}</div></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body text-center proof-modal-body"><img src="{{ $proofUrl }}" class="proof-large" alt="Bukti transfer"></div></div></div></div>
    @endif
@endforeach

<div class="ops-footer"><div class="text-muted small fw-bold">Menampilkan {{ $pesanan->count() }} dari {{ $pesanan->total() }} pesanan aktif.</div><div>{{ $pesanan->links() }}</div></div>
@endsection
