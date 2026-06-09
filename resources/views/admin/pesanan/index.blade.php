@extends('layouts.admin')

@section('title', 'Pesanan - SiTahu')
@section('page_title', 'Pesanan')

@section('content')
@include('admin.partials.ops-page-style')

@php
    $methodLabel = fn($method) => match($method){'ambil_toko'=>'Ambil toko','kurir_toko'=>'Kurir toko',default=>$statusLabel($method)};
    $paymentMethodLabel = fn($method) => match($method){'transfer_bank'=>'Transfer Bank','cod'=>'COD',default=>strtoupper((string)$method)};
    $nextStatus = fn($order) => \App\Support\OrderFlow::nextOrderStatus($order);
    $actionLabel = function($status, $order = null) use ($statusLabel) {
        return match($status) {
            'diproses' => 'Proses pesanan',
            'siap_diambil' => 'Siap diambil',
            'dalam_pengantaran' => 'Kirim pesanan',
            'selesai' => (($order?->pembayaran?->metode_pembayaran ?? null) === 'cod') ? 'Selesaikan & bayar COD' : 'Selesaikan',
            default => $statusLabel($status),
        };
    };
    $flowText = fn($order) => $order?->metode_pengambilan === 'kurir_toko'
        ? 'Belum bayar → Diproses → Dalam pengantaran → Selesai'
        : 'Belum bayar → Diproses → Siap diambil → Selesai';
    $tab = request('tab', 'semua');
    $hasActiveFilter = request()->filled('q') || request()->filled('status') || request()->filled('status_pembayaran') || request()->filled('metode_pengambilan') || request()->filled('metode_pembayaran') || request()->filled('tanggal_mulai') || request()->filled('tanggal_selesai');
@endphp

<div class="ops-page-head">
    <div>
        <h1 class="ops-title">Pesanan</h1>
        <p class="ops-subtitle">Kelola invoice pembeli dari satu halaman. Tab di bawah hanya menyaring data pesanan, tidak memindahkan admin ke menu lain.</p>
    </div>
</div>

<div class="ops-tabs">
    <a class="ops-tab {{ $tab==='semua' && !request()->filled('status') ? 'active' : '' }}" href="{{ route('admin.pesanan.index') }}">Semua <b>{{ $pesanan->total() }}</b></a>
    <a class="ops-tab {{ $tab==='verifikasi' ? 'active' : '' }}" href="{{ route('admin.pesanan.index', ['tab'=>'verifikasi']) }}">Verifikasi <b>{{ $stats['perlu_verifikasi'] ?? 0 }}</b></a>
    <a class="ops-tab {{ $tab==='belum_bayar' || request('status')==='menunggu_pembayaran' ? 'active' : '' }}" href="{{ route('admin.pesanan.index', ['tab'=>'belum_bayar']) }}">Belum bayar <b>{{ $stats['baru'] ?? 0 }}</b></a>
    <a class="ops-tab {{ $tab==='diproses' || request('status')==='diproses' ? 'active' : '' }}" href="{{ route('admin.pesanan.index', ['tab'=>'diproses']) }}">Diproses <b>{{ $stats['diproses'] ?? 0 }}</b></a>
    <a class="ops-tab {{ $tab==='ambil_kirim' || in_array(request('status'), ['siap_diambil','dalam_pengantaran'], true) ? 'active' : '' }}" href="{{ route('admin.pesanan.index', ['tab'=>'ambil_kirim']) }}">Ambil/Kirim <b>{{ $stats['ambil_kirim'] ?? 0 }}</b></a>
    <a class="ops-tab {{ $tab==='selesai' || request('status')==='selesai' ? 'active' : '' }}" href="{{ route('admin.pesanan.index', ['tab'=>'selesai']) }}">Selesai <b>{{ $stats['selesai'] ?? 0 }}</b></a>
</div>

<div class="ops-filter-card">
    <form id="page-filter" class="js-instant-filter" method="GET">
        @if($tab !== 'semua')<input type="hidden" name="tab" value="{{ $tab }}">@endif
        <div class="ops-filter-grid orders">
            <div class="ops-field">
                <label class="ops-label">Cari pesanan</label>
                <div class="ops-search"><i class="bi bi-search text-muted"></i><input name="q" value="{{ request('q') }}" placeholder="Invoice, pembeli, nomor HP, atau produk"></div>
            </div>
            <div class="ops-field">
                <label class="ops-label">Status pesanan</label>
                <select class="ops-control" name="status">
                    <option value="">Semua</option>
                    @foreach(['menunggu_pembayaran','diproses','siap_diambil','dalam_pengantaran','selesai','dibatalkan'] as $s)
                        <option value="{{ $s }}" @selected(request('status')===$s)>{{ $statusLabel($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="ops-field">
                <label class="ops-label">Status bayar</label>
                <select class="ops-control" name="status_pembayaran">
                    <option value="">Semua</option>
                    @foreach(['menunggu_pembayaran','dibayar','ditolak','dibatalkan'] as $s)
                        <option value="{{ $s }}" @selected(request('status_pembayaran')===$s)>{{ $statusLabel($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="ops-field">
                <label class="ops-label">Pengambilan</label>
                <select class="ops-control" name="metode_pengambilan">
                    <option value="">Semua</option>
                    <option value="ambil_toko" @selected(request('metode_pengambilan')==='ambil_toko')>Ambil toko</option>
                    <option value="kurir_toko" @selected(request('metode_pengambilan')==='kurir_toko')>Kurir toko</option>
                </select>
            </div>
            <div class="ops-field">
                <label class="ops-label">Pembayaran</label>
                <select class="ops-control" name="metode_pembayaran">
                    <option value="">Semua</option>
                    <option value="transfer_bank" @selected(request('metode_pembayaran')==='transfer_bank')>Transfer</option>
                    <option value="cod" @selected(request('metode_pembayaran')==='cod')>COD</option>
                </select>
            </div>
            <div class="ops-field">
                <label class="ops-label">Dari tanggal</label>
                <input type="date" class="ops-control" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}">
            </div>
            <div class="ops-field">
                <label class="ops-label">Sampai</label>
                <input type="date" class="ops-control" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}">
            </div>
            <div class="ops-filter-actions"><a href="{{ route('admin.pesanan.index') }}" class="ops-btn-reset"><i class="bi bi-x-circle"></i> Reset</a></div>
        </div>
        @if($hasActiveFilter || $tab !== 'semua')
            <div class="ops-filter-note"><i class="bi bi-funnel text-brand"></i> Filter sedang aktif. <a href="{{ route('admin.pesanan.index') }}" class="text-brand fw-black text-decoration-none">Bersihkan</a></div>
        @endif
    </form>
</div>

@if($pesanan->count())
    <div class="ops-table-card table-wrap">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Invoice</th><th>Pembeli</th><th>Produk</th><th>Metode</th><th>Status</th><th>Total</th><th class="text-end">Aksi</th>
                </tr>
            </thead>
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
                    $proofUrl = $pay?->bukti_transfer ? asset('storage/'.$pay->bukti_transfer) : null;
                    $isPdfProof = $pay?->bukti_transfer && str_ends_with(strtolower($pay->bukti_transfer), '.pdf');
                @endphp
                <tr>
                    <td><button type="button" class="ops-link action-modal-btn text-start" data-bs-toggle="modal" data-bs-target="#orderDetail{{ $order->id }}">{{ $order->nomor_invoice }}</button><span class="ops-muted">{{ optional($order->tanggal_pesanan)->format('d M Y H:i') }}</span></td>
                    <td><div class="d-flex align-items-center gap-2 min-w-0"><span class="ops-avatar">{{ $initial }}</span><div class="min-w-0"><div class="fw-black text-truncate">{{ $buyer?->name ?? 'Pembeli' }}</div><span class="ops-muted text-truncate">{{ $buyer?->telepon ?: $buyer?->email }}</span></div></div></td>
                    <td><div class="d-flex align-items-center gap-2 min-w-0">@if($img)<img src="{{ $img }}" class="ops-cover" alt="{{ $firstProduct?->nama }}">@else<span class="ops-cover-fallback"><i class="bi bi-box-seam"></i></span>@endif<div class="min-w-0"><div class="fw-black text-truncate">{{ $firstProduct?->nama ?? 'Produk' }}</div><span class="ops-muted">{{ $order->item->sum('jumlah') }} item{{ $moreCount ? ' · +'.$moreCount.' produk' : '' }}</span></div></div></td>
                    <td><div class="d-flex flex-column gap-1 align-items-start"><span class="ops-pill"><i class="bi {{ $order->metode_pengambilan === 'kurir_toko' ? 'bi-truck' : 'bi-shop' }} text-brand"></i>{{ $methodLabel($order->metode_pengambilan) }}</span><span class="ops-pill"><i class="bi {{ $pay?->metode_pembayaran === 'cod' ? 'bi-cash-coin text-success' : 'bi-bank text-primary' }}"></i>{{ $paymentMethodLabel($pay?->metode_pembayaran) }}</span></div></td>
                    <td><div class="d-flex flex-column gap-1 align-items-start"><span class="chip {{ $statusClass($order->status) }}">{{ $statusLabel($order->status) }}</span><span class="chip {{ $statusClass($order->status_pembayaran) }}">Bayar: {{ $statusLabel($order->status_pembayaran) }}</span></div></td>
                    <td class="fw-black">{{ $rupiah($order->total_bayar) }}</td>
                    <td><div class="ops-actions">
                        @if($pay && $pay->metode_pembayaran === 'transfer_bank' && $pay->status === 'menunggu_pembayaran' && filled($pay->bukti_transfer))
                            <button type="button" class="small-btn text-success" data-bs-toggle="modal" data-bs-target="#acceptPayment{{ $pay->id }}"><i class="bi bi-check2-circle"></i> Terima</button>
                            <button type="button" class="small-btn text-danger" data-bs-toggle="modal" data-bs-target="#rejectPayment{{ $pay->id }}"><i class="bi bi-x-circle"></i> Tolak</button>
                        @elseif($next)
                            <form method="POST" action="{{ route('admin.pesanan.status', $order) }}" data-confirm-title="Lanjutkan alur pesanan" data-confirm-message="{{ $flowText($order) }}. Lanjutkan {{ $order->nomor_invoice }} ke tahap {{ $statusLabel($next) }}?" data-confirm-button="Lanjutkan">@csrf @method('PATCH')<input type="hidden" name="status" value="{{ $next }}"><button class="small-btn text-brand" type="submit"><i class="bi bi-arrow-right-circle"></i> {{ $actionLabel($next, $order) }}</button></form>
                        @endif
                        <button type="button" class="small-btn" data-bs-toggle="modal" data-bs-target="#orderDetail{{ $order->id }}"><i class="bi bi-eye"></i> Detail</button>
                        <a href="{{ route('admin.pesanan.invoice', $order) }}" target="_blank" class="small-btn"><i class="bi bi-printer"></i></a>
                        @if(!in_array($order->status, ['selesai','dibatalkan'], true))
                            <form method="POST" action="{{ route('admin.pesanan.status', $order) }}" data-confirm-title="Batalkan Pesanan" data-confirm-message="Batalkan {{ $order->nomor_invoice }}? Stok produk akan dikembalikan." data-confirm-button="Batalkan">@csrf @method('PATCH')<input type="hidden" name="status" value="dibatalkan"><button class="small-btn text-danger" type="submit"><i class="bi bi-x-circle"></i></button></form>
                        @endif
                    </div></td>
                </tr>

                <div class="modal fade" id="orderDetail{{ $order->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                            <div class="modal-header bg-white border-bottom p-4"><div><h5 class="modal-title fw-black">Detail pesanan</h5><div class="ops-muted">{{ $order->nomor_invoice }} · {{ optional($order->tanggal_pesanan)->format('d M Y H:i') }}</div></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                            <div class="modal-body modal-body-soft p-4">
                                <div class="row g-3">
                                    <div class="col-lg-8">
                                        <div class="detail-modal-card mb-3"><span class="detail-label">Pembeli</span><div class="detail-value">{{ $buyer?->name ?? '-' }}</div><div class="ops-muted">{{ $buyer?->telepon ?: '-' }} · {{ $buyer?->email ?: '-' }}</div></div>
                                        <div class="detail-modal-card mb-3"><span class="detail-label">Produk dipesan</span><div class="detail-list">@foreach($order->item as $item)<div class="detail-product"><div><div class="fw-black">{{ $item->produk?->nama ?? 'Produk' }}</div><div class="ops-muted">{{ $item->jumlah }} × {{ $rupiah($item->harga_satuan) }}</div></div><strong>{{ $rupiah($item->subtotal) }}</strong></div>@endforeach</div></div>
                                        <div class="detail-modal-card"><span class="detail-label">Alamat dan pengambilan</span><div class="detail-value">{{ $methodLabel($order->metode_pengambilan) }}</div><div class="ops-muted">{{ $order->metode_pengambilan === 'kurir_toko' ? ($alamat?->alamat_lengkap ?? $ship?->alamat_tujuan ?? 'Alamat belum tersedia') : ($ship?->alamat_toko ?? 'Alamat toko') }}</div></div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="detail-modal-card mb-3"><span class="detail-label">Status</span><div class="d-flex flex-wrap gap-2"><span class="chip {{ $statusClass($order->status) }}">{{ $statusLabel($order->status) }}</span><span class="chip {{ $statusClass($order->status_pembayaran) }}">{{ $statusLabel($order->status_pembayaran) }}</span></div><div class="flow-mini mt-3"><i class="bi bi-diagram-3 text-brand"></i><span>{{ $flowText($order) }}</span></div>
                                                @php
                                                    $steps = \App\Support\OrderFlow::steps($order);
                                                    $currentIndex = array_search($order->status, $steps, true);
                                                    $nextInModal = \App\Support\OrderFlow::nextOrderStatus($order);
                                                    $needsTransferReview = $pay && $pay->metode_pembayaran === 'transfer_bank' && $pay->status === 'menunggu_pembayaran' && filled($pay->bukti_transfer);
                                                @endphp
                                                <div class="flow-steps">
                                                    @foreach($steps as $idx => $step)
                                                        @php
                                                            $done = $currentIndex !== false && $idx < $currentIndex;
                                                            $current = $order->status === $step;
                                                            $isNextStep = $nextInModal === $step;
                                                        @endphp
                                                        @if($isNextStep && ! $needsTransferReview)
                                                            <form method="POST" action="{{ route('admin.pesanan.status', $order) }}" class="flow-step-form" data-confirm-title="Lanjutkan alur pesanan" data-confirm-message="{{ $flowText($order) }}. Lanjutkan {{ $order->nomor_invoice }} ke tahap {{ $statusLabel($step) }}?" data-confirm-button="Lanjutkan">
                                                                @csrf @method('PATCH')
                                                                <input type="hidden" name="status" value="{{ $step }}">
                                                                <button class="flow-step action" type="submit"><i class="bi bi-arrow-right-circle"></i>{{ $actionLabel($step, $order) }}</button>
                                                            </form>
                                                        @else
                                                            <span class="flow-step {{ $done ? 'done' : ($current ? 'current' : 'locked') }}">
                                                                <i class="bi {{ $done ? 'bi-check2-circle' : ($current ? 'bi-record-circle' : 'bi-lock') }}"></i>{{ $statusLabel($step) }}
                                                            </span>
                                                        @endif
                                                    @endforeach
                                                </div>
                                                @if($needsTransferReview)
                                                    <div class="flow-help">Pembayaran transfer harus diterima atau ditolak dulu sebelum pesanan diproses.</div>
                                                    <div class="d-flex flex-wrap gap-2 mt-2">
                                                        <button type="button" class="small-btn text-success" data-bs-toggle="modal" data-bs-target="#acceptPayment{{ $pay->id }}"><i class="bi bi-check2-circle"></i> Terima transfer</button>
                                                        <button type="button" class="small-btn text-danger" data-bs-toggle="modal" data-bs-target="#rejectPayment{{ $pay->id }}"><i class="bi bi-x-circle"></i> Tolak transfer</button>
                                                    </div>
                                                @elseif(! $nextInModal && $order->status === 'menunggu_pembayaran')
                                                    <div class="flow-help">Pesanan transfer bank belum bisa diproses sampai pembeli mengunggah bukti dan admin menerima pembayaran.</div>
                                                @endif
                                            </div>
                                        <div class="detail-modal-card mb-3"><span class="detail-label">Pembayaran</span><div class="detail-value">{{ $paymentMethodLabel($pay?->metode_pembayaran) }}</div><div class="summary-row"><span>Subtotal</span><strong>{{ $rupiah($order->subtotal_produk) }}</strong></div><div class="summary-row"><span>Ongkir</span><strong>{{ $rupiah($order->biaya_pengiriman) }}</strong></div><div class="summary-row"><span>Total</span><strong>{{ $rupiah($order->total_bayar) }}</strong></div>@if($pay?->catatan_admin)<div class="ops-muted mt-2">Catatan: {{ $pay->catatan_admin }}</div>@endif</div>
                                        @if($proofUrl)<div class="detail-modal-card"><span class="detail-label">Bukti transfer</span>@if($isPdfProof)<a href="{{ $proofUrl }}" target="_blank" class="btn btn-soft-brand btn-sm">Buka PDF</a>@else<img src="{{ $proofUrl }}" class="modal-proof" alt="Bukti transfer">@endif</div>@endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if($pay && $pay->metode_pembayaran === 'transfer_bank' && $pay->status === 'menunggu_pembayaran' && filled($pay->bukti_transfer))
                    <div class="modal fade" id="acceptPayment{{ $pay->id }}" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><form method="POST" action="{{ route('admin.pembayaran.terima', $pay) }}" class="modal-content border-0 shadow-lg rounded-4">@csrf @method('PATCH')<div class="modal-header"><h5 class="modal-title fw-black">Terima pembayaran?</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><p class="fw-bold mb-3">Invoice {{ $order->nomor_invoice }} akan masuk ke status diproses.</p><label class="form-label-modern">Catatan admin</label><textarea name="catatan_admin" rows="3" class="form-control form-control-modern" placeholder="Opsional"></textarea></div><div class="modal-footer"><button type="button" class="btn btn-light border fw-bold rounded-4" data-bs-dismiss="modal">Batal</button><button class="btn btn-brand">Terima</button></div></form></div></div>
                    <div class="modal fade" id="rejectPayment{{ $pay->id }}" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><form method="POST" action="{{ route('admin.pembayaran.tolak', $pay) }}" class="modal-content border-0 shadow-lg rounded-4">@csrf @method('PATCH')<div class="modal-header"><h5 class="modal-title fw-black">Tolak bukti transfer?</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><label class="form-label-modern">Alasan penolakan</label><textarea name="catatan_admin" rows="4" class="form-control form-control-modern" required placeholder="Contoh: nominal tidak sesuai atau gambar kurang jelas"></textarea></div><div class="modal-footer"><button type="button" class="btn btn-light border fw-bold rounded-4" data-bs-dismiss="modal">Batal</button><button class="btn btn-danger fw-bold rounded-4">Tolak</button></div></form></div></div>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="ops-empty"><i class="bi bi-receipt fs-2 text-muted"></i><strong class="d-block mt-2">Belum ada pesanan</strong><span class="text-muted fw-bold small">Pesanan pembeli akan muncul di sini setelah checkout dibuat.</span></div>
@endif

<div class="ops-footer"><div class="text-muted small fw-bold">Menampilkan {{ $pesanan->count() }} dari {{ $pesanan->total() }} pesanan.</div><div>{{ $pesanan->links() }}</div></div>
@endsection
