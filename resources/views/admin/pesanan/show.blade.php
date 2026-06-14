@extends('layouts.admin')

@section('title', 'Detail Pesanan - SiTahu')
@section('page_title', 'Detail Pesanan')

@section('content')
<style>
    .detail-grid{display:grid;grid-template-columns:minmax(0,1.3fr) 380px;gap:18px;align-items:start}.panel{border:1px solid var(--border);border-radius:24px;background:#fff;box-shadow:var(--shadow-soft);overflow:hidden}.panel-head{padding:18px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:flex-start;gap:14px}.panel-title{margin:0;font-size:1rem;font-weight:950;letter-spacing:-.03em}.panel-sub{margin:5px 0 0;color:var(--muted);font-size:.82rem;font-weight:700}.panel-body{padding:18px}.invoice-hero{border:1px solid #f0d6a6;border-radius:24px;background:linear-gradient(135deg,#fff,#fff8ea);box-shadow:var(--shadow-soft);padding:22px;margin-bottom:18px;display:flex;justify-content:space-between;align-items:flex-start;gap:16px}.invoice-title{font-size:1.45rem;font-weight:950;letter-spacing:-.05em;margin:0}.meta{font-size:.8rem;color:var(--muted);font-weight:750}.btn-brand{background:var(--brand);border-color:var(--brand);color:#fff;font-weight:900;border-radius:14px}.btn-brand:hover{background:var(--brand-dark);border-color:var(--brand-dark);color:#fff}.btn-soft{border:1px solid #f1d49c;background:#fff8ea;color:var(--brand-dark);font-weight:900;border-radius:14px}.timeline{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;margin-bottom:18px}.step{padding:14px;border:1px solid var(--border);border-radius:18px;background:#fbfcfd}.step.done{border-color:#bbf7d0;background:#f0fdf4}.step.active{border-color:#f0d6a6;background:#fff8ea}.step-icon{width:34px;height:34px;border-radius:13px;display:grid;place-items:center;background:#fff;border:1px solid var(--border);margin-bottom:10px}.step.done .step-icon{background:#dcfce7;color:#15803d}.step.active .step-icon{background:#fff3d8;color:var(--brand-dark)}.info-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.info-box{border:1px solid var(--border);border-radius:18px;padding:14px;background:#fbfcfd}.label{font-size:.7rem;font-weight:950;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:7px}.value{font-weight:900;color:var(--text)}.product-line{display:grid;grid-template-columns:64px minmax(0,1fr) auto;gap:14px;align-items:center;padding:14px;border:1px solid var(--border);border-radius:18px;background:#fff}.product-img{width:64px;height:64px;border-radius:16px;object-fit:cover;border:1px solid var(--border);background:#fff}.product-fallback{width:64px;height:64px;border-radius:16px;display:grid;place-items:center;background:var(--brand-soft);color:var(--brand-dark);border:1px solid #f1d49c}.summary-row{display:flex;justify-content:space-between;gap:12px;padding:10px 0;border-bottom:1px dashed var(--border);font-weight:800}.summary-row:last-child{border:0}.total-row{font-size:1.25rem;font-weight:950;color:var(--brand-dark)}.proof-thumb{width:100%;max-height:240px;object-fit:cover;border:1px solid var(--border);border-radius:18px;background:#fbfcfd}.sticky-side{position:sticky;top:92px}.action-stack{display:grid;gap:10px}.method-pill{display:inline-flex;align-items:center;gap:7px;padding:7px 10px;border:1px solid var(--border);border-radius:999px;background:#fff;font-size:.76rem;font-weight:950}.address-box{border:1px solid var(--border);border-radius:18px;padding:14px;background:#fbfcfd}.order-note{padding:12px 14px;border-radius:16px;background:#fff8ea;border:1px solid #f1d49c;color:var(--brand-dark);font-weight:800;font-size:.84rem}@media(max-width:1100px){.detail-grid{grid-template-columns:1fr}.sticky-side{position:static}.timeline{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:640px){.invoice-hero{flex-direction:column}.info-grid{grid-template-columns:1fr}.product-line{grid-template-columns:52px 1fr}.product-line .text-end{grid-column:1/-1;text-align:left!important}.timeline{grid-template-columns:1fr}}
</style>

@php
    $payment = $pesanan->pembayaran;
    $ship = $pesanan->pengiriman;
    $alamat = $pesanan->alamatPengiriman;
    $flowKeys = \App\Support\OrderFlow::steps($pesanan);
    $steps = collect($flowKeys)->map(fn($key) => [
        'key' => $key,
        'label' => $statusLabel($key),
        'icon' => match($key) {
            'menunggu_pembayaran' => 'bi-receipt',
            'menunggu_verifikasi' => 'bi-shield-check',
            'diproses' => 'bi-shop',
            'diproses' => 'bi-gear',
            'disiapkan' => 'bi-box-seam',
            'siap_diambil' => 'bi-shop',
            'dalam_pengantaran' => 'bi-truck',
            'selesai' => 'bi-check2-circle',
            default => 'bi-circle',
        },
    ])->all();
    $orderStatusOrder = array_flip($flowKeys);
    $currentStep = isset($orderStatusOrder[$pesanan->status]) ? $orderStatusOrder[$pesanan->status] + 1 : 0;
    $next = \App\Support\OrderFlow::nextOrderStatus($pesanan);
    $actionLabel = function($status) use ($statusLabel, $payment) {
        return match($status) {
            'diproses' => 'Proses pesanan',
            'disiapkan' => 'Siapkan pesanan',
            'siap_diambil' => 'Tandai siap diambil',
            'dalam_pengantaran' => 'Mulai pengantaran',
            'selesai' => ($payment?->metode_pembayaran === 'cod') ? 'Selesaikan & bayar COD' : 'Selesaikan pesanan',
            default => $statusLabel($status),
        };
    };
    $methodLabel = fn($m) => match($m){'ambil_toko'=>'Ambil di toko','kurir_toko'=>'Kurir toko',default=>$statusLabel($m)};
    $paymentMethodLabel = fn($m) => match($m){'transfer_bank'=>'Transfer Bank','cod'=>'COD',default=>strtoupper((string)$m)};
    $proofUrl = $payment?->bukti_transfer ? asset('storage/'.$payment->bukti_transfer) : null;
    $isImageProof = $payment?->bukti_transfer && \Illuminate\Support\Str::endsWith(strtolower($payment->bukti_transfer), ['.jpg','.jpeg','.png','.webp']);
@endphp

<div class="invoice-hero">
    <div>
        <div class="d-flex gap-2 flex-wrap mb-2"><span class="method-pill"><i class="bi bi-receipt"></i> Invoice</span><span class="chip {{ $statusClass($pesanan->status) }}">{{ $statusLabel($pesanan->status) }}</span><span class="chip {{ $statusClass($pesanan->status_pembayaran) }}">Bayar: {{ $statusLabel($pesanan->status_pembayaran) }}</span></div>
        <h1 class="invoice-title">{{ $pesanan->nomor_invoice }}</h1>
        <div class="meta mt-2">Dibuat {{ optional($pesanan->tanggal_pesanan)->format('d M Y H:i') }}</div>
    </div>
    <div class="d-flex gap-2 flex-wrap justify-content-end">
        <a href="{{ route('admin.pesanan.index') }}" class="btn btn-light border fw-bold rounded-4"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
        <a href="{{ route('admin.pesanan.invoice', $pesanan) }}" target="_blank" class="btn btn-soft"><i class="bi bi-printer me-1"></i> Cetak / PDF Invoice</a>
    </div>
</div>

@if($pesanan->status === 'dibatalkan')
    <div class="alert alert-danger rounded-4 fw-bold">Pesanan ini sudah dibatalkan. Stok produk dikembalikan saat pembatalan dilakukan admin.</div>
@endif

<div class="timeline">
    @foreach($steps as $index => $step)
        @php $pos = $index + 1; $cls = $currentStep > $pos ? 'done' : ($currentStep === $pos ? 'active' : ''); @endphp
        <div class="step {{ $cls }}"><div class="step-icon"><i class="bi {{ $step['icon'] }}"></i></div><div class="fw-black">{{ $step['label'] }}</div><div class="meta mt-1">{{ $pos === 1 ? optional($pesanan->tanggal_pesanan)->format('d M Y') : ($currentStep >= $pos ? 'Sudah berjalan' : 'Menunggu') }}</div></div>
    @endforeach
</div>

<div class="detail-grid">
    <div class="d-grid gap-3">
        <div class="panel">
            <div class="panel-head"><div><h2 class="panel-title">Produk dipesan</h2><p class="panel-sub">{{ $pesanan->item->sum('jumlah') }} item dari {{ $pesanan->item->count() }} produk.</p></div></div>
            <div class="panel-body d-grid gap-3">
                @foreach($pesanan->item as $item)
                    @php $p = $item->produk; $img = $p?->gambarUtama?->url_gambar ? asset('storage/'.$p->gambarUtama->url_gambar) : null; @endphp
                    <div class="product-line">
                        @if($img)<img src="{{ $img }}" class="product-img" alt="{{ $p?->nama }}">@else<div class="product-fallback"><i class="bi bi-box-seam"></i></div>@endif
                        <div class="min-w-0"><div class="fw-black text-truncate">{{ $p?->nama ?? 'Produk dihapus' }}</div><div class="meta">{{ $item->jumlah }} x {{ $rupiah($item->harga_satuan) }}</div></div>
                        <div class="text-end fw-black">{{ $rupiah($item->subtotal) }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="panel">
            <div class="panel-head"><div><h2 class="panel-title">Penerima dan Pengambilan</h2></div></div>
            <div class="panel-body">
                <div class="info-grid">
                    <div class="info-box"><div class="label">Pembeli akun</div><div class="value">{{ $pesanan->user?->name ?? '-' }}</div><div class="meta mt-1">{{ $pesanan->user?->telepon ?: $pesanan->user?->email }}</div></div>
                    <div class="info-box"><div class="label">Metode</div><div class="value">{{ $methodLabel($pesanan->metode_pengambilan) }}</div><div class="meta mt-1">{{ $ship?->jarak_km ? $ship->jarak_km.' km' : 'Jarak belum dihitung' }}</div></div>
                </div>
                <div class="address-box mt-3"><div class="label">Alamat penerima</div><div class="value">{{ $alamat?->nama_penerima ?? $pesanan->user?->name ?? '-' }}</div><div class="meta mt-1">{{ $alamat?->telepon ?? $pesanan->user?->telepon }}</div><div class="mt-2 fw-bold text-muted">{{ $alamat?->alamat_lengkap ?? 'Alamat tidak tersedia' }}</div></div>
            </div>
        </div>
    </div>

    <div class="sticky-side d-grid gap-3">
        <div class="panel">
            <div class="panel-head"><div><h2 class="panel-title">Tindakan admin</h2><p class="panel-sub">Lanjutkan pesanan sesuai tahapnya.</p></div></div>
            <div class="panel-body action-stack">
                @if($payment?->metode_pembayaran === 'transfer_bank' && in_array($payment?->status, ['menunggu_pembayaran','menunggu_verifikasi'], true))
                    <a class="btn btn-soft" href="{{ route('admin.pembayaran.index', ['q'=>$pesanan->nomor_invoice]) }}"><i class="bi bi-shield-check me-1"></i> Pembayaran Transfer</a>
                @endif
                @if($next)
                    <form method="POST" action="{{ route('admin.pesanan.status', $pesanan) }}" data-confirm-title="Ubah Status Pesanan" data-confirm-message="Lanjutkan invoice {{ $pesanan->nomor_invoice }} ke tahap {{ $statusLabel($next) }}?" data-confirm-button="Simpan">
                        @csrf @method('PATCH')<input type="hidden" name="status" value="{{ $next }}"><button class="btn btn-brand w-100"><i class="bi bi-arrow-right-circle me-1"></i> {{ $actionLabel($next) }}</button>
                    </form>
                @endif
                @if(! in_array($pesanan->status, ['selesai','dibatalkan'], true))
                    <form method="POST" action="{{ route('admin.pesanan.status', $pesanan) }}" data-confirm-title="Batalkan Pesanan" data-confirm-message="Batalkan invoice {{ $pesanan->nomor_invoice }}? Stok produk akan dikembalikan." data-confirm-button="Batalkan">
                        @csrf @method('PATCH')<input type="hidden" name="status" value="dibatalkan"><button class="btn btn-outline-danger fw-bold rounded-4 w-100"><i class="bi bi-x-circle me-1"></i> Batalkan pesanan</button>
                    </form>
                @endif
                @if(!$next && $pesanan->status !== 'dibatalkan')<div class="order-note"><i class="bi bi-info-circle me-1"></i> </div>@endif
            </div>
        </div>

        <div class="panel">
            <div class="panel-head"><div><h2 class="panel-title">Pembayaran</h2><p class="panel-sub">{{ $paymentMethodLabel($payment?->metode_pembayaran) }}</p></div><span class="chip {{ $statusClass($payment?->status) }}">{{ $statusLabel($payment?->status) }}</span></div>
            <div class="panel-body">
                <div class="summary-row"><span>Subtotal produk</span><strong>{{ $rupiah($pesanan->subtotal_produk) }}</strong></div>
                <div class="summary-row"><span>Biaya pengiriman</span><strong>{{ $rupiah($pesanan->biaya_pengiriman) }}</strong></div>
                <div class="summary-row total-row"><span>Total bayar</span><span>{{ $rupiah($pesanan->total_bayar) }}</span></div>
                <div class="mt-3 meta">Referensi: <strong>{{ $payment?->referensi_pembayaran ?: '-' }}</strong></div>
                @if($proofUrl)
                    <div class="mt-3"><div class="label">Bukti transfer</div>@if($isImageProof)<a href="{{ $proofUrl }}" target="_blank"><img src="{{ $proofUrl }}" class="proof-thumb" alt="Bukti transfer"></a>@else<a href="{{ $proofUrl }}" target="_blank" class="btn btn-light border fw-bold rounded-4">Lihat file bukti</a>@endif</div>
                @endif
                @if($payment?->catatan_admin)<div class="order-note mt-3">Catatan: {{ $payment->catatan_admin }}</div>@endif
            </div>
        </div>
    </div>
</div>
@endsection
