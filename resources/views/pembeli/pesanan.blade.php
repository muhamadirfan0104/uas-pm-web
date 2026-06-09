@extends('layouts.pembeli')

@section('title', 'Pesanan Saya - SiTahu')

@push('styles')
<style>
    .orders-head {
        border-radius: 30px;
        border: 1px solid rgba(234,236,240,.92);
        background: linear-gradient(135deg, #fff 0%, #fffaf0 100%);
        box-shadow: var(--shadow-sm);
        padding: 24px;
    }
    .orders-title { font-weight: 950; letter-spacing: -.045em; line-height: 1.08; }
    .orders-filter {
        border: 1px solid var(--line);
        background: #fff;
        border-radius: 24px;
        box-shadow: var(--shadow-xs);
        padding: 14px;
    }
    .order-search { position: relative; }
    .order-search .form-control {
        min-height: 46px;
        border-radius: 999px;
        border-color: var(--line);
        padding-left: 42px;
        font-weight: 750;
        background: #f9fafb;
    }
    .order-search .form-control:focus { background: #fff; border-color: rgba(200,147,53,.55); box-shadow: 0 0 0 .25rem rgba(200,147,53,.12); }
    .order-search i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--muted-2); }
    .order-tabs { display: flex; gap: 8px; overflow-x: auto; scrollbar-width: none; padding-bottom: 2px; }
    .order-tabs::-webkit-scrollbar { display: none; }
    .order-tab {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
        padding: 10px 14px;
        border-radius: 999px;
        border: 1px solid var(--line);
        background: #fff;
        color: var(--muted);
        text-decoration: none;
        font-size: 13px;
        font-weight: 900;
        transition: .2s ease;
    }
    .order-tab:hover,
    .order-tab.active { color: var(--brand-dark); background: var(--brand-soft); border-color: rgba(200,147,53,.35); }
    .order-tab .count { min-width: 22px; height: 22px; padding: 0 7px; display: inline-grid; place-items: center; border-radius: 999px; background: #f2f4f7; color: var(--ink); font-size: 11px; }
    .order-tab.active .count { background: #fff; color: var(--brand-dark); }

    .order-card {
        border-radius: 26px;
        border: 1px solid var(--line);
        background: #fff;
        box-shadow: var(--shadow-xs);
        overflow: hidden;
        transition: .22s ease;
    }
    .order-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-sm); border-color: rgba(200,147,53,.22); }
    .order-card-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 14px;
        padding: 18px 20px;
        border-bottom: 1px solid var(--line);
        background: linear-gradient(180deg, #fff, #fffdf8);
    }
    .invoice-code { font-size: 14px; font-weight: 950; letter-spacing: -.01em; color: var(--ink); }
    .order-date { color: var(--muted); font-size: 12px; font-weight: 750; }
    .order-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 7px 11px;
        font-size: 12px;
        font-weight: 950;
        white-space: nowrap;
    }
    .order-status.waiting { background: #fff7ed; color: #c2410c; }
    .order-status.process { background: #eff6ff; color: #1d4ed8; }
    .order-status.ready { background: #ecfeff; color: #0e7490; }
    .order-status.done { background: #dcfce7; color: #15803d; }
    .order-status.cancel { background: #fee2e2; color: #b91c1c; }
    .order-body { padding: 18px 20px; }
    .store-line { display: flex; align-items: center; gap: 8px; margin-bottom: 14px; color: var(--ink); font-weight: 900; }
    .store-line i { color: var(--brand-color); }
    .order-item-row {
        display: grid;
        grid-template-columns: 68px minmax(0,1fr) auto;
        align-items: center;
        gap: 14px;
        padding: 12px 0;
    }
    .order-item-row + .order-item-row { border-top: 1px solid var(--line); }
    .order-img {
        width: 68px;
        height: 68px;
        border-radius: 18px;
        overflow: hidden;
        background: var(--brand-soft);
        border: 1px solid rgba(200,147,53,.18);
        display: grid;
        place-items: center;
        color: var(--brand-dark);
        flex: 0 0 auto;
    }
    .order-img img { width: 100%; height: 100%; object-fit: cover; }
    .order-product-name { font-weight: 900; color: var(--ink); line-height: 1.35; text-decoration: none; }
    .order-product-name:hover { color: var(--brand-dark); }
    .order-meta { color: var(--muted); font-size: 12px; font-weight: 750; }
    .order-payline {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        align-items: center;
        padding: 15px 20px;
        border-top: 1px solid var(--line);
        background: #fcfcfd;
    }
    .payment-note { color: var(--muted); font-size: 13px; font-weight: 750; }
    .payment-note strong { color: var(--ink); }
    .order-actions { display: flex; flex-wrap: wrap; gap: 8px; justify-content: flex-end; }
    .empty-orders {
        border-radius: 30px;
        background: #fff;
        border: 1px solid var(--line);
        box-shadow: var(--shadow-sm);
        text-align: center;
        padding: 52px 24px;
    }
    .empty-icon { width: 76px; height: 76px; border-radius: 26px; background: var(--brand-soft); color: var(--brand-dark); display: grid; place-items: center; font-size: 34px; margin: 0 auto 18px; }
    @media (max-width: 767.98px) {
        .orders-head { padding: 20px; border-radius: 24px; }
        .order-card-head, .order-payline { flex-direction: column; align-items: stretch; }
        .order-actions { justify-content: stretch; }
        .order-actions .btn, .order-actions form { width: 100%; }
        .order-actions form .btn { width: 100%; }
        .order-item-row { grid-template-columns: 58px minmax(0,1fr); }
        .order-item-row .text-end { grid-column: 2; text-align: left !important; }
        .order-img { width: 58px; height: 58px; border-radius: 16px; }
    }
</style>
@endpush

@section('content')
@php
    $statusTabs = [
        '' => ['label' => 'Semua', 'count' => $jumlahStatus['semua'] ?? 0],
        'menunggu_pembayaran' => ['label' => 'Belum Bayar', 'count' => $jumlahStatus['menunggu_pembayaran'] ?? 0],
        'diproses' => ['label' => 'Diproses', 'count' => $jumlahStatus['diproses'] ?? 0],
        'siap_diterima' => ['label' => 'Siap Diterima', 'count' => $jumlahStatus['siap_diterima'] ?? 0],
        'selesai' => ['label' => 'Selesai', 'count' => $jumlahStatus['selesai'] ?? 0],
        'dibatalkan' => ['label' => 'Dibatalkan', 'count' => $jumlahStatus['dibatalkan'] ?? 0],
    ];
@endphp
<div class="container py-4 py-lg-5">
    <section class="orders-head mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3">
            <div>
                <span class="eyebrow mb-3"><i class="bi bi-receipt-cutoff"></i> Pesanan saya</span>
                <h1 class="orders-title h2 mb-2">Riwayat pesanan</h1>
                <p class="section-subtitle mb-0">Cek pembayaran, proses toko, pengambilan, pengiriman, dan ulasan dalam satu halaman.</p>
            </div>
            <a href="{{ route('pembeli-web.produk') }}" class="btn btn-brand px-4 py-3"><i class="bi bi-bag-plus me-2"></i> Belanja Lagi</a>
        </div>
    </section>

    <section class="orders-filter mb-4">
        <form action="{{ route('pembeli-web.pesanan.index') }}" method="GET" class="row g-3 align-items-center mb-3">
            @if($status !== '')
                <input type="hidden" name="status" value="{{ $status }}">
            @endif
            <div class="col-lg-9">
                <div class="order-search">
                    <i class="bi bi-search"></i>
                    <input type="search" name="q" value="{{ $q ?? '' }}" class="form-control" placeholder="Cari invoice atau nama produk...">
                </div>
            </div>
            <div class="col-lg-3 d-grid">
                <button class="btn btn-brand py-3" type="submit"><i class="bi bi-search me-1"></i> Cari Pesanan</button>
            </div>
        </form>
        <div class="order-tabs">
            @foreach($statusTabs as $value => $tab)
                <a href="{{ route('pembeli-web.pesanan.index', array_filter(['status' => $value !== '' ? $value : null, 'q' => ($q ?? '') !== '' ? $q : null])) }}" class="order-tab {{ (string) $status === (string) $value ? 'active' : '' }}">
                    {{ $tab['label'] }} <span class="count">{{ $tab['count'] }}</span>
                </a>
            @endforeach
            @if(($q ?? '') !== '')
                <a href="{{ route('pembeli-web.pesanan.index', array_filter(['status' => $status ?: null])) }}" class="order-tab"><i class="bi bi-x-circle"></i> Reset Pencarian</a>
            @endif
        </div>
    </section>

    @if($pesananList->count())
        <div class="d-grid gap-3">
            @foreach($pesananList as $pesanan)
                @php
                    $payment = $pesanan->pembayaran;
                    $items = $pesanan->item;
                    $firstItem = $items->first();
                    $previewItems = $items->take(2);
                    $moreItems = max(0, $items->count() - $previewItems->count());
                    $statusTone = match($pesanan->status) {
                        'selesai' => 'done',
                        'dibatalkan' => 'cancel',
                        'siap_diambil', 'dalam_pengantaran' => 'ready',
                        'dibayar', 'diproses' => 'process',
                        default => 'waiting',
                    };
                    $statusIcon = match($pesanan->status) {
                        'selesai' => 'bi-check2-circle',
                        'dibatalkan' => 'bi-x-circle',
                        'siap_diambil', 'dalam_pengantaran' => 'bi-truck',
                        'dibayar', 'diproses' => 'bi-gear',
                        default => 'bi-wallet2',
                    };
                    $statusText = match($pesanan->status) {
                        'menunggu_pembayaran' => 'Belum Bayar',
                        'dibayar' => 'Pembayaran Diterima',
                        'diproses' => 'Diproses Toko',
                        'siap_diambil' => 'Siap Diambil',
                        'dalam_pengantaran' => 'Dalam Pengantaran',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                        default => ucwords(str_replace('_', ' ', $pesanan->status)),
                    };
                    $paymentText = match($payment?->status ?: $pesanan->status_pembayaran) {
                        'dibayar' => 'Pembayaran diterima',
                        'ditolak' => 'Bukti transfer ditolak',
                        'dibatalkan' => 'Pembayaran dibatalkan',
                        'gagal' => 'Pembayaran gagal',
                        default => 'Menunggu pembayaran',
                    };
                    $needsTransferProof = $payment?->metode_pembayaran === 'transfer_bank' && in_array($payment?->status, ['menunggu_pembayaran', 'ditolak'], true);
                @endphp
                <article class="order-card">
                    <div class="order-card-head">
                        <div class="min-w-0">
                            <div class="invoice-code line-clamp-1">{{ $pesanan->nomor_invoice }}</div>
                            <div class="order-date mt-1"><i class="bi bi-calendar2-week me-1"></i>{{ optional($pesanan->tanggal_pesanan)->format('d M Y H:i') }}</div>
                        </div>
                        <span class="order-status {{ $statusTone }}"><i class="bi {{ $statusIcon }}"></i>{{ $statusText }}</span>
                    </div>

                    <div class="order-body">
                        <div class="store-line"><i class="bi bi-shop-window"></i> SiTahu Premium</div>
                        @foreach($previewItems as $item)
                            @php
                                $produk = $item->produk;
                                $image = $produk?->gambarUtama?->url_gambar;
                            @endphp
                            <div class="order-item-row">
                                <a href="{{ $produk ? route('pembeli-web.produk.detail', $produk) : '#' }}" class="order-img text-decoration-none">
                                    @if($image)
                                        <img src="{{ asset('storage/' . $image) }}" alt="{{ $produk?->nama }}">
                                    @else
                                        <i class="bi bi-box-seam fs-4"></i>
                                    @endif
                                </a>
                                <div class="min-w-0">
                                    <a href="{{ $produk ? route('pembeli-web.produk.detail', $produk) : '#' }}" class="order-product-name line-clamp-1">{{ $produk?->nama ?: 'Produk SiTahu' }}</a>
                                    <div class="order-meta mt-1">{{ $item->jumlah }} item × {{ $rupiah($item->harga_satuan) }} · {{ $pesanan->metode_pengambilan === 'kurir_toko' ? 'Kurir toko' : 'Ambil di toko' }}</div>
                                </div>
                                <div class="text-end fw-black">{{ $rupiah($item->subtotal) }}</div>
                            </div>
                        @endforeach
                        @if($moreItems > 0)
                            <a href="{{ route('pembeli-web.pesanan.show', $pesanan->nomor_invoice) }}" class="small fw-bold text-brand text-decoration-none d-inline-flex align-items-center gap-1 mt-2">
                                Lihat {{ $moreItems }} produk lainnya <i class="bi bi-chevron-right"></i>
                            </a>
                        @endif
                    </div>

                    <div class="order-payline">
                        <div>
                            <div class="payment-note"><strong>{{ $payment?->metode_pembayaran === 'cod' ? 'COD' : 'Transfer Bank' }}</strong> · {{ $paymentText }}</div>
                            @if($payment?->catatan_admin)
                                <div class="small text-danger fw-bold mt-1"><i class="bi bi-info-circle me-1"></i>{{ $payment->catatan_admin }}</div>
                            @endif
                        </div>
                        <div class="text-lg-end">
                            <div class="small text-muted fw-bold">Total pembayaran</div>
                            <div class="price-text h4 mb-0">{{ $rupiah($pesanan->total_bayar) }}</div>
                        </div>
                    </div>

                    <div class="order-payline bg-white">
                        <div class="small text-muted fw-semibold">{{ $items->count() }} jenis produk · {{ $items->sum('jumlah') }} item</div>
                        <div class="order-actions">
                            @if($needsTransferProof)
                                <a href="{{ route('pembeli-web.pesanan.show', $pesanan->nomor_invoice) }}#upload-bukti" class="btn btn-brand btn-sm px-3"><i class="bi bi-upload me-1"></i> {{ $payment?->status === 'ditolak' ? 'Upload Ulang' : 'Upload Bukti' }}</a>
                            @endif
                            @if($pesanan->status === 'menunggu_pembayaran')
                                <form action="{{ route('pembeli-web.pesanan.cancel', $pesanan->nomor_invoice) }}" method="POST" onsubmit="return confirm('Batalkan pesanan ini?')">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-plain btn-sm px-3 text-danger" type="submit">Batalkan</button>
                                </form>
                            @endif
                            @if(in_array($pesanan->status, ['siap_diambil', 'dalam_pengantaran'], true))
                                <form action="{{ route('pembeli-web.pesanan.confirm-received', $pesanan->nomor_invoice) }}" method="POST" onsubmit="return confirm('Konfirmasi pesanan sudah diterima?')">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-brand btn-sm px-3" type="submit">Pesanan Diterima</button>
                                </form>
                            @endif
                            <a href="{{ route('pembeli-web.pesanan.show', $pesanan->nomor_invoice) }}" class="btn btn-soft-brand btn-sm px-3">Detail Pesanan</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
        <div class="mt-4 d-flex justify-content-center">{{ $pesananList->links() }}</div>
    @else
        <div class="empty-orders">
            <div class="empty-icon"><i class="bi bi-receipt"></i></div>
            <h2 class="h3 fw-black mb-2">Pesanan belum ditemukan</h2>
            <p class="text-muted fw-semibold mb-4">{{ ($q ?? '') !== '' ? 'Coba gunakan kata kunci lain atau reset pencarian.' : 'Pesanan dari keranjang akan tampil di sini setelah checkout.' }}</p>
            <div class="d-flex flex-column flex-sm-row justify-content-center gap-2">
                @if(($q ?? '') !== '')
                    <a href="{{ route('pembeli-web.pesanan.index', array_filter(['status' => $status ?: null])) }}" class="btn btn-plain px-4 py-3">Reset Pencarian</a>
                @endif
                <a href="{{ route('pembeli-web.produk') }}" class="btn btn-brand px-4 py-3">Mulai Belanja</a>
            </div>
        </div>
    @endif
</div>
@endsection
