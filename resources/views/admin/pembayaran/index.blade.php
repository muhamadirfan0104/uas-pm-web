@extends('layouts.admin')

@section('title', 'Pembayaran - SiTahu')
@section('page_title', 'Pembayaran')

@section('content')
@include('admin.partials.ops-page-style')

@php
    $methodLabel = fn($method) => match($method){'transfer_bank'=>'Transfer Bank','cod'=>'COD',default=>strtoupper((string)$method)};
    $tab = request('tab', 'semua');
    $hasActiveFilter = request()->filled('q') || request()->filled('status') || request()->filled('metode') || request()->filled('bukti') || request()->filled('tanggal_mulai') || request()->filled('tanggal_selesai');
@endphp

<div class="ops-page-head">
    <div>
        <h1 class="ops-title">Pembayaran</h1>
        <p class="ops-subtitle">Fokus untuk memeriksa bukti transfer dan status COD. Detail pembayaran dibuka lewat pop up agar halaman tetap rapi.</p>
    </div>
</div>

<div class="ops-tabs">
    <a class="ops-tab {{ $tab==='semua' ? 'active' : '' }}" href="{{ route('admin.pembayaran.index') }}">Semua</a>
    <a class="ops-tab {{ $tab==='perlu_dicek' ? 'active' : '' }}" href="{{ route('admin.pembayaran.index', ['tab'=>'perlu_dicek']) }}">Perlu dicek <b>{{ $stats['perlu_dicek'] ?? 0 }}</b></a>
    <a class="ops-tab {{ $tab==='belum_upload' ? 'active' : '' }}" href="{{ route('admin.pembayaran.index', ['tab'=>'belum_upload']) }}">Belum upload <b>{{ $stats['belum_upload'] ?? 0 }}</b></a>
    <a class="ops-tab {{ $tab==='dibayar' ? 'active' : '' }}" href="{{ route('admin.pembayaran.index', ['tab'=>'dibayar']) }}">Dibayar <b>{{ $stats['dibayar'] ?? 0 }}</b></a>
    <a class="ops-tab {{ $tab==='ditolak' ? 'active' : '' }}" href="{{ route('admin.pembayaran.index', ['tab'=>'ditolak']) }}">Ditolak <b>{{ $stats['ditolak'] ?? 0 }}</b></a>
    <a class="ops-tab {{ $tab==='cod' ? 'active' : '' }}" href="{{ route('admin.pembayaran.index', ['tab'=>'cod']) }}">COD <b>{{ $stats['cod'] ?? 0 }}</b></a>
</div>

<div class="ops-filter-card">
    <form id="page-filter" class="js-instant-filter" method="GET">
        @if($tab !== 'semua')<input type="hidden" name="tab" value="{{ $tab }}">@endif
        <div class="ops-filter-grid">
            <div class="ops-field"><label class="ops-label">Cari pembayaran</label><div class="ops-search"><i class="bi bi-search text-muted"></i><input name="q" value="{{ request('q') }}" placeholder="Invoice, pembeli, nomor HP, atau referensi"></div></div>
            <div class="ops-field"><label class="ops-label">Status</label><select class="ops-control" name="status"><option value="">Semua</option>@foreach(['menunggu_pembayaran','dibayar','ditolak','gagal','kedaluwarsa','dibatalkan'] as $s)<option value="{{ $s }}" @selected(request('status')===$s)>{{ $statusLabel($s) }}</option>@endforeach</select></div>
            <div class="ops-field"><label class="ops-label">Metode</label><select class="ops-control" name="metode"><option value="">Semua</option><option value="transfer_bank" @selected(request('metode')==='transfer_bank')>Transfer</option><option value="cod" @selected(request('metode')==='cod')>COD</option></select></div>
            <div class="ops-field"><label class="ops-label">Bukti</label><select class="ops-control" name="bukti"><option value="">Semua</option><option value="ada" @selected(request('bukti')==='ada')>Ada bukti</option><option value="belum" @selected(request('bukti')==='belum')>Belum upload</option></select></div>
            <div class="ops-field"><label class="ops-label">Dari tanggal</label><input type="date" class="ops-control" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}"></div>
            <div class="ops-field"><label class="ops-label">Sampai</label><input type="date" class="ops-control" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}"></div>
            <div class="ops-filter-actions"><a href="{{ route('admin.pembayaran.index') }}" class="ops-btn-reset"><i class="bi bi-x-circle"></i> Reset</a></div>
        </div>
        @if($hasActiveFilter || $tab !== 'semua')<div class="ops-filter-note"><i class="bi bi-funnel text-brand"></i> Filter sedang aktif. <a href="{{ route('admin.pembayaran.index') }}" class="text-brand fw-black text-decoration-none">Bersihkan</a></div>@endif
    </form>
</div>

@if($pembayaran->count())
    <div class="ops-table-card table-wrap">
        <table class="table align-middle">
            <thead><tr><th>Invoice</th><th>Pembeli</th><th>Metode</th><th>Bukti</th><th>Status</th><th>Total</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
            @foreach($pembayaran as $pay)
                @php
                    $order = $pay->pesanan;
                    $buyer = $order?->user;
                    $initial = strtoupper(substr($buyer?->name ?? 'PB',0,2));
                    $proofUrl = $pay->bukti_transfer ? asset('storage/'.$pay->bukti_transfer) : null;
                    $isPdfProof = $pay->bukti_transfer && str_ends_with(strtolower($pay->bukti_transfer), '.pdf');
                    $firstItem = $order?->item?->first();
                @endphp
                <tr>
                    <td><button type="button" class="ops-link action-modal-btn text-start" data-bs-toggle="modal" data-bs-target="#paymentDetail{{ $pay->id }}">{{ $order?->nomor_invoice ?? '-' }}</button><span class="ops-muted">{{ optional($pay->created_at)->format('d M Y H:i') }}</span></td>
                    <td><div class="d-flex align-items-center gap-2 min-w-0"><span class="ops-avatar">{{ $initial }}</span><div class="min-w-0"><div class="fw-black text-truncate">{{ $buyer?->name ?? 'Pembeli' }}</div><span class="ops-muted text-truncate">{{ $buyer?->telepon ?: $buyer?->email }}</span></div></div></td>
                    <td><span class="ops-pill"><i class="bi {{ $pay->metode_pembayaran === 'cod' ? 'bi-cash-coin text-success' : 'bi-bank text-primary' }}"></i>{{ $methodLabel($pay->metode_pembayaran) }}</span><span class="ops-muted">{{ $pay->referensi_pembayaran ?: 'Tanpa referensi' }}</span></td>
                    <td>@if($proofUrl)@if($isPdfProof)<a href="{{ $proofUrl }}" target="_blank" class="small-btn"><i class="bi bi-file-earmark-pdf"></i> PDF</a>@else<img src="{{ $proofUrl }}" class="proof-thumb" alt="Bukti transfer">@endif@else<span class="ops-pill text-muted"><i class="bi bi-dash-circle"></i> Belum ada</span>@endif</td>
                    <td><span class="chip {{ $statusClass($pay->status) }}">{{ $statusLabel($pay->status) }}</span></td>
                    <td class="fw-black">{{ $rupiah($pay->jumlah) }}</td>
                    <td><div class="ops-actions">
                        @if($pay->metode_pembayaran === 'transfer_bank' && $pay->status === 'menunggu_pembayaran' && filled($pay->bukti_transfer))
                            <button type="button" class="small-btn text-success" data-bs-toggle="modal" data-bs-target="#acceptPayment{{ $pay->id }}"><i class="bi bi-check2-circle"></i> Terima</button>
                            <button type="button" class="small-btn text-danger" data-bs-toggle="modal" data-bs-target="#rejectPayment{{ $pay->id }}"><i class="bi bi-x-circle"></i> Tolak</button>
                        @endif
                        <button type="button" class="small-btn" data-bs-toggle="modal" data-bs-target="#paymentDetail{{ $pay->id }}"><i class="bi bi-eye"></i> Detail</button>
                    </div></td>
                </tr>

                <div class="modal fade" id="paymentDetail{{ $pay->id }}" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable"><div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden"><div class="modal-header bg-white border-bottom p-4"><div><h5 class="modal-title fw-black">Detail pembayaran</h5><div class="ops-muted">{{ $order?->nomor_invoice ?? '-' }} · {{ $methodLabel($pay->metode_pembayaran) }}</div></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body modal-body-soft p-4"><div class="row g-3"><div class="col-lg-7"><div class="detail-modal-card mb-3"><span class="detail-label">Pembeli</span><div class="detail-value">{{ $buyer?->name ?? '-' }}</div><div class="ops-muted">{{ $buyer?->telepon ?: '-' }} · {{ $buyer?->email ?: '-' }}</div></div><div class="detail-modal-card mb-3"><span class="detail-label">Ringkasan pesanan</span><div class="detail-value">{{ $firstItem?->produk?->nama ?? 'Produk' }}</div><div class="ops-muted">{{ $order?->item?->sum('jumlah') ?? 0 }} item dari {{ $order?->item?->count() ?? 0 }} produk</div></div>@if($proofUrl)<div class="detail-modal-card"><span class="detail-label">Bukti transfer</span>@if($isPdfProof)<a href="{{ $proofUrl }}" target="_blank" class="btn btn-soft-brand btn-sm">Buka PDF</a>@else<img src="{{ $proofUrl }}" class="modal-proof" alt="Bukti transfer">@endif</div>@endif</div><div class="col-lg-5"><div class="detail-modal-card mb-3"><span class="detail-label">Status</span><div class="d-flex flex-wrap gap-2"><span class="chip {{ $statusClass($pay->status) }}">{{ $statusLabel($pay->status) }}</span><span class="ops-pill">{{ $methodLabel($pay->metode_pembayaran) }}</span></div></div><div class="detail-modal-card"><span class="detail-label">Nominal</span><div class="summary-row"><span>Total dibayar</span><strong>{{ $rupiah($pay->jumlah) }}</strong></div><div class="summary-row"><span>Referensi</span><strong>{{ $pay->referensi_pembayaran ?: '-' }}</strong></div><div class="summary-row"><span>Diverifikasi</span><strong>{{ optional($pay->diverifikasi_pada)->format('d M Y H:i') ?: '-' }}</strong></div>@if($pay->catatan_admin)<div class="ops-muted mt-2">Catatan admin: {{ $pay->catatan_admin }}</div>@endif</div></div></div></div></div></div></div>
                @if($pay->metode_pembayaran === 'transfer_bank' && $pay->status === 'menunggu_pembayaran' && filled($pay->bukti_transfer))
                    <div class="modal fade" id="acceptPayment{{ $pay->id }}" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><form method="POST" action="{{ route('admin.pembayaran.terima', $pay) }}" class="modal-content border-0 shadow-lg rounded-4">@csrf @method('PATCH')<div class="modal-header"><h5 class="modal-title fw-black">Terima pembayaran?</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><p class="fw-bold mb-3">Pesanan akan masuk ke tahap diproses.</p><label class="form-label-modern">Catatan admin</label><textarea name="catatan_admin" rows="3" class="form-control form-control-modern" placeholder="Opsional"></textarea></div><div class="modal-footer"><button type="button" class="btn btn-light border fw-bold rounded-4" data-bs-dismiss="modal">Batal</button><button class="btn btn-brand">Terima</button></div></form></div></div>
                    <div class="modal fade" id="rejectPayment{{ $pay->id }}" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><form method="POST" action="{{ route('admin.pembayaran.tolak', $pay) }}" class="modal-content border-0 shadow-lg rounded-4">@csrf @method('PATCH')<div class="modal-header"><h5 class="modal-title fw-black">Tolak bukti transfer?</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><label class="form-label-modern">Alasan penolakan</label><textarea name="catatan_admin" rows="4" class="form-control form-control-modern" required placeholder="Contoh: nominal tidak sesuai atau gambar kurang jelas"></textarea></div><div class="modal-footer"><button type="button" class="btn btn-light border fw-bold rounded-4" data-bs-dismiss="modal">Batal</button><button class="btn btn-danger fw-bold rounded-4">Tolak</button></div></form></div></div>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="ops-empty"><i class="bi bi-credit-card fs-2 text-muted"></i><strong class="d-block mt-2">Belum ada pembayaran</strong><span class="text-muted fw-bold small">Data pembayaran muncul setelah pesanan dibuat.</span></div>
@endif

<div class="ops-footer"><div class="text-muted small fw-bold">Menampilkan {{ $pembayaran->count() }} dari {{ $pembayaran->total() }} pembayaran.</div><div>{{ $pembayaran->links() }}</div></div>
@endsection
