@extends('layouts.pembeli')

@section('title', 'Detail Pesanan - ' . $pesanan->nomor_invoice)

@push('styles')
<style>
    .detail-head {
        border-radius: 30px;
        border: 1px solid var(--line);
        background: radial-gradient(circle at 90% 18%, rgba(200,147,53,.12), transparent 18rem), #fff;
        box-shadow: var(--shadow-sm);
        padding: 24px;
    }
    .invoice-title { font-weight: 950; letter-spacing: -.035em; }
    .status-badge-xl {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        border-radius: 999px;
        padding: 9px 13px;
        font-size: 13px;
        font-weight: 950;
        white-space: nowrap;
    }
    .status-badge-xl.waiting { background: #fff7ed; color: #c2410c; }
    .status-badge-xl.process { background: #eff6ff; color: #1d4ed8; }
    .status-badge-xl.ready { background: #ecfeff; color: #0e7490; }
    .status-badge-xl.done { background: #dcfce7; color: #15803d; }
    .status-badge-xl.cancel { background: #fee2e2; color: #b91c1c; }

    .detail-card {
        border-radius: 26px;
        border: 1px solid var(--line);
        background: #fff;
        box-shadow: var(--shadow-xs);
        overflow: hidden;
    }
    .detail-card-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 18px 20px;
        border-bottom: 1px solid var(--line);
        background: linear-gradient(180deg, #fff, #fffdf8);
    }
    .detail-card-title { font-size: 1.05rem; font-weight: 950; margin: 0; letter-spacing: -.02em; }
    .detail-card-body { padding: 20px; }

    .progress-steps {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }
    .progress-step {
        position: relative;
        border: 1px solid var(--line);
        border-radius: 20px;
        padding: 16px;
        background: #fff;
        min-height: 112px;
    }
    .progress-step.done,
    .progress-step.active { border-color: rgba(200,147,53,.34); background: var(--brand-soft); }
    .progress-step.cancelled { border-color: rgba(239,68,68,.25); background: #fff5f5; }
    .step-icon {
        width: 38px;
        height: 38px;
        border-radius: 14px;
        display: grid;
        place-items: center;
        background: #f2f4f7;
        color: var(--muted);
        margin-bottom: 10px;
    }
    .progress-step.done .step-icon,
    .progress-step.active .step-icon { background: #fff; color: var(--brand-dark); border: 1px solid rgba(200,147,53,.22); }
    .progress-step.cancelled .step-icon { background: #fee2e2; color: #b91c1c; }
    .step-title { font-size: 13px; font-weight: 950; color: var(--ink); margin-bottom: 4px; }
    .step-desc { font-size: 12px; line-height: 1.45; color: var(--muted); font-weight: 650; }

    .product-line {
        display: grid;
        grid-template-columns: 74px minmax(0,1fr) auto;
        gap: 14px;
        align-items: center;
        padding: 14px 0;
    }
    .product-line + .product-line { border-top: 1px solid var(--line); }
    .order-product-img {
        width: 74px;
        height: 74px;
        border-radius: 18px;
        overflow: hidden;
        background: var(--brand-soft);
        border: 1px solid rgba(200,147,53,.18);
        display: grid;
        place-items: center;
        color: var(--brand-dark);
        text-decoration: none;
        flex: 0 0 auto;
    }
    .order-product-img img { width: 100%; height: 100%; object-fit: cover; }
    .product-link { color: var(--ink); text-decoration: none; font-weight: 950; line-height: 1.35; }
    .product-link:hover { color: var(--brand-dark); }
    .address-box, .payment-box {
        border: 1px solid var(--line);
        border-radius: 20px;
        background: #fff;
        padding: 16px;
    }
    .address-icon, .pay-icon {
        width: 42px;
        height: 42px;
        border-radius: 15px;
        display: grid;
        place-items: center;
        background: var(--brand-soft);
        color: var(--brand-dark);
        border: 1px solid rgba(200,147,53,.18);
        flex: 0 0 auto;
    }
    .summary-sticky { position: sticky; top: 112px; }
    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        padding: 12px 0;
        border-bottom: 1px solid var(--line);
        font-size: 14px;
    }
    .summary-row span { color: var(--muted); font-weight: 750; }
    .summary-row strong { color: var(--ink); font-weight: 950; text-align: right; }
    .bank-mini-card {
        display: grid;
        grid-template-columns: 42px minmax(0,1fr) auto;
        gap: 12px;
        align-items: center;
        padding: 13px;
        border-radius: 18px;
        border: 1px solid var(--line);
        background: #fff;
    }
    .bank-mini-icon { width: 42px; height: 42px; border-radius: 15px; display: grid; place-items: center; background: var(--brand-soft); color: var(--brand-dark); border: 1px solid rgba(200,147,53,.18); }
    .bank-mini-label { color: var(--muted); font-size: 11px; font-weight: 950; text-transform: uppercase; letter-spacing: .04em; }
    .bank-mini-number { font-weight: 950; color: var(--ink); font-size: 1.06rem; line-height: 1.12; }
    .proof-preview { width: 100%; max-height: 220px; object-fit: contain; border-radius: 18px; background: #fff; border: 1px solid var(--line); }
    .proof-upload-box { padding: 15px; border-radius: 18px; background: #fffdf8; border: 1px dashed rgba(200,147,53,.42); }
    .form-label-mini { display: block; text-transform: uppercase; letter-spacing: .035em; color: var(--muted); font-size: 12px; font-weight: 950; margin-bottom: 8px; }
    .checkout-field { min-height: 48px; border-radius: 16px; border-color: var(--line); font-weight: 750; }
    .order-alert { border-radius: 18px; border: 1px solid var(--line); background: #f9fafb; padding: 14px; color: var(--muted); font-size: 13px; font-weight: 700; line-height: 1.55; }
    @media (max-width: 991.98px) { .summary-sticky { position: static; } .progress-steps { grid-template-columns: repeat(2, minmax(0,1fr)); } }
    @media (max-width: 575.98px) {
        .detail-head { padding: 20px; border-radius: 24px; }
        .detail-card-head { align-items: flex-start; flex-direction: column; }
        .progress-steps { grid-template-columns: 1fr; }
        .product-line { grid-template-columns: 62px minmax(0,1fr); }
        .product-line .text-end { grid-column: 2; text-align: left !important; }
        .order-product-img { width: 62px; height: 62px; border-radius: 16px; }
        .bank-mini-card { grid-template-columns: 42px minmax(0,1fr); }
        .bank-mini-card .btn { grid-column: 1 / -1; }
    }
</style>
@endpush

@section('content')
@php
    $payment = $pesanan->pembayaran;
    $pengiriman = $pesanan->pengiriman;
    $alamat = $pesanan->alamatPengiriman;
    $statusTone = match($pesanan->status) {
        'selesai' => 'done',
        'dibatalkan' => 'cancel',
        'siap_diambil', 'dalam_pengantaran' => 'ready',
        'diproses', 'disiapkan' => 'process',
        default => 'waiting',
    };
    $statusIcon = match($pesanan->status) {
        'selesai' => 'bi-check2-circle',
        'dibatalkan' => 'bi-x-circle',
        'siap_diambil', 'dalam_pengantaran' => 'bi-truck',
        'diproses', 'disiapkan' => 'bi-gear',
        default => 'bi-wallet2',
    };
    $statusText = match($pesanan->status) {
        'menunggu_pembayaran' => 'Belum Bayar',
        'menunggu_verifikasi' => 'Menunggu Verifikasi',
        'dibayar' => 'Pembayaran Diterima',
        'diproses' => 'Diproses Toko',
        'disiapkan' => 'Disiapkan',
        'siap_diambil' => 'Siap Diambil',
        'dalam_pengantaran' => 'Dalam Pengantaran',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
        default => ucwords(str_replace('_', ' ', $pesanan->status)),
    };
    $paymentText = match($payment?->status ?: $pesanan->status_pembayaran) {
        'dibayar' => 'Dibayar',
        'ditolak' => 'Ditolak',
        'dibatalkan' => 'Dibatalkan',
        'gagal' => 'Gagal',
        'menunggu_verifikasi' => 'Menunggu Verifikasi',
        default => 'Menunggu',
    };
    $rekeningList = \App\Models\RekeningToko::daftarAktif();
    if ($rekeningList->isEmpty()) {
        $rekeningList = collect([(object) [
            'nama_bank' => trim((string) ($pengaturan->bank_nama ?? '')) ?: 'Bank belum diatur',
            'nomor_rekening' => trim((string) ($pengaturan->bank_nomor_rekening ?? '')) ?: 'Nomor rekening belum diatur',
            'atas_nama' => trim((string) ($pengaturan->bank_atas_nama ?? '')) ?: ($pengaturan->nama ?: 'SiTahu Premium'),
        ]]);
    }
    $rekeningUtama = $rekeningList->first();
    $bankNama = $rekeningUtama->nama_bank;
    $bankNomor = $rekeningUtama->nomor_rekening;
    $bankAtasNama = $rekeningUtama->atas_nama;
    $catatanPembayaran = trim((string) ($pengaturan->info_pembayaran ?? '')) ?: 'Transfer sesuai total pembayaran.';
    $canUploadProof = $payment?->metode_pembayaran === 'transfer_bank' && in_array($payment?->status, ['menunggu_pembayaran', 'menunggu_verifikasi', 'ditolak'], true);
    $hasProof = filled($payment?->bukti_transfer);
    $currentStep = match($pesanan->status) {
        'menunggu_pembayaran' => 1,
        'menunggu_verifikasi' => 1,
        'diproses', 'disiapkan' => 2,
        'siap_diambil', 'dalam_pengantaran' => 3,
        'selesai' => 4,
        default => 0,
    };
    $steps = [
        1 => ['title' => 'Pesanan dibuat', 'desc' => 'Invoice berhasil dibuat.', 'icon' => 'bi-receipt-cutoff'],
        2 => ['title' => 'Diproses toko', 'desc' => 'Toko menerima pembayaran/COD dan menyiapkan pesanan.', 'icon' => 'bi-gear'],
        3 => ['title' => $pesanan->metode_pengambilan === 'kurir_toko' ? 'Dikirim' : 'Siap diambil', 'desc' => $pesanan->metode_pengambilan === 'kurir_toko' ? 'Pesanan berada dalam pengantaran.' : 'Pesanan dapat diambil di toko.', 'icon' => 'bi-truck'],
        4 => ['title' => 'Selesai', 'desc' => 'Pesanan sudah diterima pembeli.', 'icon' => 'bi-check2-circle'],
    ];
    $storeMapsUrl = ($pengaturan->latitude_toko && $pengaturan->longitude_toko)
        ? 'https://www.google.com/maps?q=' . $pengaturan->latitude_toko . ',' . $pengaturan->longitude_toko
        : null;
@endphp

<div class="container py-4 py-lg-5">
    <div class="breadcrumb-modern"><a href="{{ route('pembeli-web.pesanan.index') }}">Pesanan</a><i class="bi bi-chevron-right small"></i><span>{{ $pesanan->nomor_invoice }}</span></div>

    <section class="detail-head mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start gap-3">
            <div class="min-w-0">
                <span class="eyebrow mb-3"><i class="bi bi-receipt"></i> Detail pesanan</span>
                <h1 class="invoice-title h3 mb-2">{{ $pesanan->nomor_invoice }}</h1>
                <p class="section-subtitle mb-0">{{ optional($pesanan->tanggal_pesanan)->format('d M Y H:i') }}</p>
            </div>
            <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                <span class="status-badge-xl {{ $statusTone }}"><i class="bi {{ $statusIcon }}"></i>{{ $statusText }}</span>
                @if($canUploadProof)
                    <button type="button" class="btn btn-brand px-4" data-bs-toggle="modal" data-bs-target="#uploadProofModal"><i class="bi bi-upload me-1"></i> {{ $payment?->status === 'ditolak' ? 'Upload Ulang' : 'Upload Bukti' }}</button>
                @endif
                <a href="{{ route('pembeli-web.pesanan.invoice', $pesanan->nomor_invoice) }}" target="_blank" rel="noopener" class="btn btn-soft-brand px-4">
                    <i class="bi bi-printer me-1"></i> Cetak Struk
                </a>
                <a href="{{ route('pembeli-web.pesanan.index') }}" class="btn btn-plain px-4"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
            </div>
        </div>
    </section>

    @if($pesanan->status === 'dibatalkan')
        <div class="alert alert-danger alert-shop mb-4"><i class="bi bi-x-circle me-2"></i> Pesanan ini sudah dibatalkan. Produk yang belum diproses dikembalikan ke stok toko.</div>
    @elseif($payment?->status === 'ditolak')
        <div class="alert alert-warning alert-shop mb-4"><i class="bi bi-exclamation-triangle me-2"></i> Bukti transfer ditolak. {{ $payment->catatan_admin ?: 'Upload ulang bukti transfer.' }}</div>
    @elseif($payment?->metode_pembayaran === 'transfer_bank' && ! $hasProof)
        <div class="alert alert-warning alert-shop mb-4"><i class="bi bi-bank me-2"></i> Menunggu bukti pembayaran.</div>
    @endif

    <div class="row g-4 align-items-start">
        <div class="col-lg-8">
            <div class="d-grid gap-4">
                <section class="detail-card">
                    <div class="detail-card-head">
                        <div>
                            <h2 class="detail-card-title">Status pesanan</h2>
                            <div class="small text-muted fw-semibold mt-1">Ikuti proses pesanan sampai selesai.</div>
                        </div>
                    </div>
                    <div class="detail-card-body">
                        @if($pesanan->status === 'dibatalkan')
                            <div class="progress-step cancelled">
                                <div class="step-icon"><i class="bi bi-x-circle"></i></div>
                                <div class="step-title">Pesanan dibatalkan</div>
                                <div class="step-desc">Pesanan tidak dilanjutkan dan tidak perlu dibayar.</div>
                            </div>
                        @else
                            <div class="progress-steps">
                                @foreach($steps as $number => $step)
                                    @php $stepState = $currentStep > $number ? 'done' : ($currentStep === $number ? 'active' : ''); @endphp
                                    <div class="progress-step {{ $stepState }}">
                                        <div class="step-icon"><i class="bi {{ $step['icon'] }}"></i></div>
                                        <div class="step-title">{{ $step['title'] }}</div>
                                        <div class="step-desc">{{ $step['desc'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </section>

                <section class="detail-card">
                    <div class="detail-card-head">
                        <div>
                            <h2 class="detail-card-title">Produk dipesan</h2>
                            <div class="small text-muted fw-semibold mt-1">{{ $pesanan->item->count() }} jenis produk · {{ $pesanan->item->sum('jumlah') }} item.</div>
                        </div>
                    </div>
                    <div class="detail-card-body pt-2">
                        @foreach($pesanan->item as $item)
                            @php $produk = $item->produk; $image = $produk?->gambarUtama?->url_gambar; @endphp
                            <div class="product-line">
                                <a href="{{ $produk ? route('pembeli-web.produk.detail', $produk) : '#' }}" class="order-product-img">
                                    @if($image)
                                        <img src="{{ asset('storage/' . $image) }}" alt="{{ $produk?->nama }}">
                                    @else
                                        <i class="bi bi-box-seam fs-4"></i>
                                    @endif
                                </a>
                                <div class="min-w-0">
                                    <a href="{{ $produk ? route('pembeli-web.produk.detail', $produk) : '#' }}" class="product-link line-clamp-1">{{ $produk?->nama ?: 'Produk SiTahu' }}</a>
                                    <div class="small text-muted fw-semibold mt-1">{{ $item->jumlah }} item × {{ $rupiah($item->harga_satuan) }}</div>
                                    @if($pesanan->status === 'selesai' && $produk)
                                        @php $sudahUlas = $pesanan->ulasan->where('produk_id', $produk->id)->isNotEmpty(); @endphp
                                        @if(! $sudahUlas)
                                            <a href="{{ route('pembeli-web.ulasan.create', [$pesanan->nomor_invoice, $produk]) }}" class="btn btn-soft-brand btn-sm mt-2">Beri Ulasan</a>
                                        @else
                                            <span class="badge rounded-pill text-bg-light mt-2">Sudah diulas</span>
                                        @endif
                                    @endif
                                </div>
                                <div class="text-end fw-black">{{ $rupiah($item->subtotal) }}</div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="detail-card">
                    <div class="detail-card-head">
                        <div>
                            <h2 class="detail-card-title">Alamat dan pengambilan</h2>
                            <div class="small text-muted fw-semibold mt-1">Informasi penerima dan metode pemenuhan pesanan.</div>
                        </div>
                    </div>
                    <div class="detail-card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="address-box h-100 d-flex gap-3 align-items-start">
                                    <div class="address-icon"><i class="bi bi-person-lines-fill"></i></div>
                                    <div>
                                        <div class="fw-black mb-1">Penerima</div>
                                        @if($alamat)
                                            <div class="text-muted fw-semibold small lh-lg">
                                                <strong class="text-dark">{{ $alamat->nama_penerima }}</strong><br>
                                                {{ $alamat->telepon }}<br>
                                                @if($alamat->email_penerima){{ $alamat->email_penerima }}<br>@endif
                                                {{ $alamat->alamat_lengkap }}
                                            </div>
                                        @else
                                            <div class="text-muted fw-semibold small">Alamat penerima belum tersedia.</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="address-box h-100 d-flex gap-3 align-items-start">
                                    <div class="address-icon"><i class="bi {{ $pesanan->metode_pengambilan === 'kurir_toko' ? 'bi-truck' : 'bi-shop' }}"></i></div>
                                    <div>
                                        <div class="fw-black mb-1">{{ $pesanan->metode_pengambilan === 'kurir_toko' ? 'Kurir toko' : 'Ambil di toko' }}</div>
                                        <div class="text-muted fw-semibold small lh-lg">
                                            @if($pesanan->metode_pengambilan === 'kurir_toko')
                                                Pesanan dikirim ke alamat penerima dalam area layanan toko.<br>
                                                Ongkir: <strong class="text-dark">{{ $rupiah($pesanan->biaya_pengiriman) }}</strong>
                                            @else
                                                Pesanan diambil langsung setelah toko menyiapkan produk.<br>
                                                Lokasi toko: <strong class="text-dark">{{ $pengiriman?->alamat_toko ?: ($pengaturan->alamat ?: 'Belum diatur') }}</strong>
                                                @if($storeMapsUrl)
                                                    <br><a href="{{ $storeMapsUrl }}" target="_blank" rel="noopener" class="btn btn-soft-brand btn-sm px-3 mt-2"><i class="bi bi-map me-1"></i>Buka di Google Maps</a>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <div class="col-lg-4">
            <aside class="detail-card summary-sticky">
                <div class="detail-card-head">
                    <div>
                        <h2 class="detail-card-title">Pembayaran</h2>
                        <div class="small text-muted fw-semibold mt-1">Ringkasan invoice dan status bayar.</div>
                    </div>
                </div>
                <div class="detail-card-body">
                    <div class="payment-box mb-3">
                        <div class="d-flex gap-3 align-items-start">
                            <div class="pay-icon"><i class="bi {{ $payment?->metode_pembayaran === 'cod' ? 'bi-cash-coin' : 'bi-bank2' }}"></i></div>
                            <div>
                                <div class="fw-black">{{ $payment?->metode_pembayaran === 'cod' ? 'COD' : 'Transfer Bank' }}</div>
                                <div class="small text-muted fw-semibold">Status: {{ $paymentText }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="summary-row"><span>Subtotal produk</span><strong>{{ $rupiah($pesanan->subtotal_produk) }}</strong></div>
                    <div class="summary-row"><span>Pengiriman</span><strong>{{ $rupiah($pesanan->biaya_pengiriman) }}</strong></div>
                    <div class="summary-row"><span>Referensi</span><strong>{{ $payment?->referensi_pembayaran ?: '-' }}</strong></div>
                    <div class="d-flex justify-content-between align-items-end mt-3 mb-3">
                        <span class="fw-black">Total bayar</span>
                        <span class="price-text h3 mb-0">{{ $rupiah($pesanan->total_bayar) }}</span>
                    </div>

                    @if($payment?->metode_pembayaran === 'transfer_bank')
                        <div class="proof-upload-box mb-3" id="upload-bukti">
                            <div class="fw-black mb-2"><i class="bi bi-bank text-brand me-1"></i> Rekening toko</div>
                            <div class="d-grid gap-2 mb-3">
                                @foreach($rekeningList as $rekening)
                                    <div class="bank-mini-card">
                                        <div class="bank-mini-icon"><i class="bi bi-credit-card-2-front"></i></div>
                                        <div class="min-w-0">
                                            <div class="bank-mini-label">{{ $rekening->nama_bank }}</div>
                                            <div class="bank-mini-number js-bank-number">{{ $rekening->nomor_rekening }}</div>
                                            <div class="small text-muted fw-semibold">Atas nama {{ $rekening->atas_nama }}</div>
                                        </div>
                                        <button type="button" class="btn btn-soft-brand btn-sm js-copy-bank" data-copy="{{ $rekening->nomor_rekening }}"><i class="bi bi-copy me-1"></i> Salin</button>
                                    </div>
                                @endforeach
                            </div>

                            @if($payment?->catatan_admin)
                                <div class="alert alert-warning py-2 px-3 small fw-semibold mb-3">Catatan: {{ $payment->catatan_admin }}</div>
                            @endif

                            @if($hasProof)
                                @php $proofUrl = asset('storage/' . $payment->bukti_transfer); @endphp
                                <div class="small text-muted fw-bold mb-2">Bukti transfer</div>
                                @if(\Illuminate\Support\Str::endsWith(strtolower($payment->bukti_transfer), ['.jpg', '.jpeg', '.png', '.webp']))
                                    <a href="{{ $proofUrl }}" target="_blank"><img src="{{ $proofUrl }}" alt="Bukti transfer" class="proof-preview mb-3"></a>
                                @else
                                    <a href="{{ $proofUrl }}" target="_blank" class="btn btn-soft-brand w-100 mb-3"><i class="bi bi-file-earmark-pdf me-1"></i> Lihat Bukti Transfer</a>
                                @endif
                            @else
                                <div class="order-alert mb-3">Belum ada bukti transfer. Upload bukti setelah melakukan pembayaran.</div>
                            @endif

                            @if($canUploadProof)
                                <button type="button" class="btn btn-brand w-100" data-bs-toggle="modal" data-bs-target="#uploadProofModal"><i class="bi bi-upload me-1"></i> {{ $hasProof ? 'Upload Ulang Bukti' : 'Upload Bukti Transfer' }}</button>
                            @endif
                        </div>
                    @elseif($payment?->metode_pembayaran === 'cod')
                        <div class="order-alert mb-3"><i class="bi bi-cash-coin text-brand me-1"></i> Pembayaran COD dilakukan saat pesanan diambil atau diterima.</div>
                    @endif

                    <div class="d-grid gap-2">
                        @if($pesanan->status === 'menunggu_pembayaran')
                            <form action="{{ route('pembeli-web.pesanan.cancel', $pesanan->nomor_invoice) }}" method="POST" data-confirm-title="Batalkan Pesanan" data-confirm-message="Yakin ingin membatalkan pesanan {{ $pesanan->nomor_invoice }}?" data-confirm-button="Batalkan">
                                @csrf @method('PATCH')
                                <button class="btn btn-plain text-danger w-100 py-3" type="submit">Batalkan Pesanan</button>
                            </form>
                        @endif
                        @if(in_array($pesanan->status, ['siap_diambil', 'dalam_pengantaran'], true))
                            <form action="{{ route('pembeli-web.pesanan.confirm-received', $pesanan->nomor_invoice) }}" method="POST" data-confirm-title="Konfirmasi Pesanan Diterima" data-confirm-message="Konfirmasi bahwa pesanan {{ $pesanan->nomor_invoice }} telah diterima." data-confirm-button="Ya, Pesanan Diterima">
                                @csrf @method('PATCH')
                                <button class="btn btn-brand w-100 py-3" type="submit">Pesanan Sudah Diterima</button>
                            </form>
                        @endif
                        <a href="{{ route('pembeli-web.produk') }}" class="btn btn-soft-brand w-100 py-3">Belanja Lagi</a>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>

@if($canUploadProof)
    <div class="modal fade" id="uploadProofModal" tabindex="-1" aria-labelledby="uploadProofModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 rounded-5 shadow-lg overflow-hidden">
                <div class="modal-header px-4 py-3 border-0" style="background: linear-gradient(180deg, #fff8ea, #fff);">
                    <div>
                        <h5 class="modal-title fw-black" id="uploadProofModalLabel"><i class="bi bi-bank2 text-brand me-2"></i> Upload bukti transfer</h5>
                        <div class="small text-muted fw-semibold mt-1">Transfer sesuai total pembayaran.</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <form action="{{ route('pembeli-web.pesanan.bukti-transfer', $pesanan->nomor_invoice) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="d-grid gap-2 mb-3">
                            @foreach($rekeningList as $rekening)
                                <div class="bank-mini-card">
                                    <div class="bank-mini-icon"><i class="bi bi-credit-card-2-front"></i></div>
                                    <div class="min-w-0">
                                        <div class="bank-mini-label">{{ $rekening->nama_bank }}</div>
                                        <div class="bank-mini-number js-modal-bank-number">{{ $rekening->nomor_rekening }}</div>
                                        <div class="small text-muted fw-semibold">Atas nama {{ $rekening->atas_nama }}</div>
                                    </div>
                                    <button type="button" class="btn btn-soft-brand btn-sm js-copy-bank" data-copy="{{ $rekening->nomor_rekening }}"><i class="bi bi-copy me-1"></i> Salin</button>
                                </div>
                            @endforeach
                        </div>
                        <div class="d-flex justify-content-between align-items-end gap-3 rounded-4 bg-white border p-3 mb-3">
                            <span class="text-muted fw-bold">Total transfer</span>
                            <span class="price-text h4 mb-0">{{ $rupiah($pesanan->total_bayar) }}</span>
                        </div>
                        <div class="proof-upload-box">
                            <label for="bukti_transfer" class="form-label-mini">File bukti transfer</label>
                            <input type="file" name="bukti_transfer" id="bukti_transfer" class="form-control checkout-field @error('bukti_transfer') is-invalid @enderror" accept="image/*,.pdf" required>
                            @error('bukti_transfer')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            <div class="small text-muted fw-semibold mt-2">Format JPG, PNG, WEBP, atau PDF. Maksimal 4 MB.</div>
                        </div>
                        <div class="order-alert mt-3"><i class="bi bi-info-circle text-brand me-1"></i> {{ $catatanPembayaran }}</div>
                    </div>
                    <div class="modal-footer px-4 py-3 border-0 bg-light-subtle">
                        <button type="button" class="btn btn-plain" data-bs-dismiss="modal">Nanti Saja</button>
                        <button type="submit" class="btn btn-brand px-4"><i class="bi bi-upload me-2"></i> Kirim Bukti</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalEl = document.getElementById('uploadProofModal');
        const shouldOpenModal = @json($errors->has('bukti_transfer')) || window.location.hash === '#upload-bukti';
        if (shouldOpenModal && modalEl && window.bootstrap) {
            new bootstrap.Modal(modalEl).show();
        }

        document.querySelectorAll('.js-copy-bank').forEach(function (button) {
            button.addEventListener('click', async function () {
                const value = button.dataset.copy || '';
                if (!value || value.toLowerCase().includes('belum diatur')) return;

                try {
                    await navigator.clipboard.writeText(value);
                } catch (e) {
                    const input = document.createElement('input');
                    input.value = value;
                    document.body.appendChild(input);
                    input.select();
                    document.execCommand('copy');
                    input.remove();
                }

                const oldHtml = button.innerHTML;
                button.innerHTML = '<i class="bi bi-check2 me-1"></i> Tersalin';
                setTimeout(() => button.innerHTML = oldHtml, 1400);
            });
        });
    });
</script>
@endpush
