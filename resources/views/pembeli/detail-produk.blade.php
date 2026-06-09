@extends('layouts.pembeli')

@section('title', $produk->nama . ' - Detail Produk')

@push('styles')
<style>
    .product-detail-page { padding-top: 1.25rem; }
    .detail-container { max-width: 1180px; margin-inline: auto; }
    .breadcrumb-modern { margin-bottom: 16px; font-size: 13px; }

    .detail-compact-card {
        border: 1px solid rgba(234,236,240,.95);
        border-radius: 28px;
        background:
            radial-gradient(circle at 95% 0%, rgba(200,147,53,.12), transparent 22rem),
            #fff;
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }
    .detail-gallery-wrap { padding: 18px; }
    .detail-gallery-main {
        position: relative;
        aspect-ratio: 1 / 1;
        border-radius: 22px;
        overflow: hidden;
        border: 1px solid var(--line);
        background:
            radial-gradient(circle at 80% 10%, rgba(200,147,53,.13), transparent 32%),
            #f9fafb;
    }
    .detail-gallery-main img { width: 100%; height: 100%; object-fit: cover; transition: .28s ease; }
    .detail-gallery-main:hover img { transform: scale(1.02); }
    .detail-placeholder {
        position: absolute;
        inset: 0;
        display: grid;
        place-items: center;
        text-align: center;
        color: var(--brand-dark);
        padding: 26px;
        font-weight: 900;
    }
    .detail-status-badge {
        position: absolute;
        top: 14px;
        left: 14px;
        z-index: 2;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 11px;
        border-radius: 999px;
        background: rgba(255,255,255,.94);
        border: 1px solid rgba(234,236,240,.9);
        box-shadow: var(--shadow-xs);
        font-size: 11px;
        font-weight: 900;
        color: var(--success);
    }
    .detail-status-badge.empty { color: #b91c1c; background: rgba(254,242,242,.95); }
    .detail-thumbs { display: grid; grid-template-columns: repeat(5, 1fr); gap: 9px; margin-top: 12px; }
    .detail-thumb {
        border: 2px solid transparent;
        padding: 0;
        border-radius: 14px;
        overflow: hidden;
        background: #fff;
        aspect-ratio: 1 / 1;
        cursor: pointer;
        transition: .2s ease;
    }
    .detail-thumb:hover, .detail-thumb.active { border-color: var(--brand-color); box-shadow: 0 10px 22px rgba(200,147,53,.14); }
    .detail-thumb img { width: 100%; height: 100%; object-fit: cover; }

    .detail-info-wrap { padding: 22px 24px 22px 6px; }
    .product-title-compact {
        font-size: clamp(1.75rem, 2.35vw, 2.45rem);
        font-weight: 950;
        letter-spacing: -.052em;
        line-height: 1.08;
        margin-bottom: 10px;
    }
    .product-desc-compact { color: var(--muted); line-height: 1.65; font-weight: 650; max-width: 720px; font-size: 14px; }
    .detail-meta-row { display: flex; flex-wrap: wrap; gap: 8px; margin: 18px 0; }
    .detail-stat-pill {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 8px 11px;
        border: 1px solid var(--line);
        border-radius: 999px;
        background: #fff;
        color: var(--ink);
        font-size: 12px;
        font-weight: 850;
        box-shadow: var(--shadow-xs);
        white-space: nowrap;
    }
    .detail-stat-pill i { color: var(--brand-dark); }

    .buy-panel {
        border: 1px solid rgba(200,147,53,.22);
        border-radius: 22px;
        background: linear-gradient(135deg, #fff, #fffaf0);
        padding: 18px;
    }
    .purchase-price { color: var(--brand-dark); font-size: clamp(1.75rem, 2.6vw, 2.45rem); font-weight: 950; letter-spacing: -.045em; line-height: 1; }
    .buy-action-grid { display: grid; grid-template-columns: 1fr 1.15fr; gap: 10px; }
    .qty-control {
        display: grid;
        grid-template-columns: 42px minmax(56px, 1fr) 42px;
        border: 1px solid var(--line);
        border-radius: 15px;
        overflow: hidden;
        background: #fff;
    }
    .qty-control button { border: 0; background: #fff; min-height: 44px; font-weight: 900; color: var(--ink); }
    .qty-control button:hover { background: var(--brand-soft); color: var(--brand-dark); }
    .qty-control input { border: 0; border-left: 1px solid var(--line); border-right: 1px solid var(--line); text-align: center; font-weight: 900; min-height: 44px; }
    .quick-info-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 10px; margin-top: 14px; }
    .quick-info-card { border: 1px solid var(--line); border-radius: 18px; background: rgba(255,255,255,.9); padding: 13px; min-height: 86px; }
    .quick-info-card .label { color: var(--muted); font-size: 10px; text-transform: uppercase; letter-spacing: .04em; font-weight: 900; }
    .quick-info-card .value { color: var(--ink); font-weight: 950; font-size: 13px; margin-top: 4px; }
    .quick-info-card i { color: var(--brand-dark); font-size: 17px; margin-bottom: 8px; }

    .detail-info-section { margin-top: 22px; }
    .simple-info-card { border: 1px solid var(--line); border-radius: 24px; background: #fff; box-shadow: var(--shadow-xs); padding: 22px; height: 100%; }
    .simple-info-title { font-weight: 950; letter-spacing: -.035em; margin-bottom: 14px; }
    .simple-list { display: grid; gap: 10px; }
    .simple-list-item { display: flex; gap: 11px; align-items: flex-start; padding: 12px; border-radius: 16px; background: #f9fafb; border: 1px solid var(--line); }
    .simple-list-item .icon { width: 34px; height: 34px; border-radius: 12px; display: grid; place-items: center; flex: 0 0 auto; color: var(--brand-dark); background: var(--brand-soft); border: 1px solid rgba(200,147,53,.18); }
    .simple-list-item .title { font-weight: 900; font-size: 13px; margin-bottom: 2px; }
    .simple-list-item .text { color: var(--muted); font-weight: 600; line-height: 1.55; font-size: 13px; }

    .review-section-head { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: end; gap: 14px; }
    .review-filter { border: 1px solid var(--line); background: #fff; border-radius: 999px; padding: 8px 12px; color: var(--muted); font-size: 12px; font-weight: 850; transition: .18s ease; }
    .review-filter:hover, .review-filter.active { color: var(--brand-dark); background: var(--brand-soft); border-color: rgba(200,147,53,.35); }
    .review-summary-card { border: 1px solid var(--line); border-radius: 24px; background: #fff; box-shadow: var(--shadow-xs); padding: 22px; }
    .review-average { font-size: 2.2rem; font-weight: 950; letter-spacing: -.05em; line-height: 1; }
    .rating-bar { height: 7px; border-radius: 999px; background: #f2f4f7; overflow: hidden; }
    .rating-bar span { display: block; height: 100%; border-radius: inherit; background: linear-gradient(90deg, var(--brand-color), var(--warning)); }

    .review-card-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
    .review-line {
        border: 1px solid var(--line);
        border-radius: 22px;
        padding: 18px;
        background: #fff;
        box-shadow: var(--shadow-xs);
        transition: .18s ease;
        height: 100%;
    }
    .review-line:hover { border-color: rgba(200,147,53,.3); box-shadow: var(--shadow-sm); transform: translateY(-1px); }
    .review-avatar { width: 42px; height: 42px; border-radius: 14px; display: grid; place-items: center; color: var(--brand-dark); background: var(--brand-soft); border: 1px solid rgba(200,147,53,.18); flex: 0 0 auto; }
    .review-comment { color: var(--ink); font-weight: 650; line-height: 1.65; font-size: 14px; }
    .review-media-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(96px, 1fr)); gap: 9px; margin-top: 14px; }
    .review-media-item { position: relative; display: block; aspect-ratio: 1 / 1; border-radius: 15px; overflow: hidden; background: #f3f4f6; border: 1px solid var(--line); }
    .review-media-item img, .review-media-item video { width: 100%; height: 100%; object-fit: cover; }
    .review-media-badge { position: absolute; left: 7px; bottom: 7px; display: inline-flex; align-items: center; gap: 5px; border-radius: 999px; padding: 5px 8px; background: rgba(17,24,39,.72); color: #fff; font-size: 10px; font-weight: 850; backdrop-filter: blur(8px); }

    @media (max-width: 1199.98px) {
        .detail-info-wrap { padding: 4px 20px 22px; }
        .quick-info-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 991.98px) {
        .review-card-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 767.98px) {
        .product-detail-page { padding-top: 1rem; }
        .detail-compact-card { border-radius: 22px; }
        .detail-gallery-wrap { padding: 14px; }
        .detail-gallery-main { border-radius: 20px; }
        .detail-info-wrap { padding: 4px 14px 18px; }
        .product-title-compact { font-size: 1.85rem; }
        .buy-panel { padding: 15px; }
        .buy-action-grid { grid-template-columns: 1fr; }
        .quick-info-grid { grid-template-columns: 1fr 1fr; }
        .review-section-head { align-items: flex-start; }
    }
</style>
@endpush

@section('content')
@php
    $gambar = $produk->gambar;
    $gambarUtama = $produk->gambarUtama?->url_gambar ?: $gambar->first()?->url_gambar;
    $stok = (int) $produk->stok;
    $ratingBulatan = (int) round((float) $rataRating);
    $jumlahUlasan = (int) $jumlahUlasan;
    $totalTerjual = (int) ($totalTerjual ?? 0);
    $beratLabel = $produk->berat ? rtrim(rtrim(number_format((float) $produk->berat, 2, ',', '.'), '0'), ',') . ' kg' : '-';
    $deskripsiProduk = $produk->deskripsi ?: 'Produk tahu segar yang cocok untuk kebutuhan harian dan pesanan keluarga.';
    $saranSimpan = $produk->saran_penyimpanan ?: 'Simpan di tempat bersih dan sejuk. Untuk kualitas terbaik, segera olah setelah pembelian.';
    $saranSaji = $produk->saran_penyajian ?: 'Cocok untuk digoreng, ditumis, dicampur sayur, atau dijadikan lauk keluarga.';
    $isiLabel = $produk->isi_per_satuan ? $produk->isi_per_satuan . ' pcs' : '-';
    $masaSimpanLabel = $produk->masa_simpan ? $produk->masa_simpan . ' hari' : '-';
    $waProduk = preg_replace('/[^0-9]/', '', $pengaturan->telepon ?? '');
    if ($waProduk && \Illuminate\Support\Str::startsWith($waProduk, '0')) {
        $waProduk = '62' . \Illuminate\Support\Str::after($waProduk, '0');
    }
@endphp

<div class="container-fluid product-detail-page pb-5 px-3 px-xl-4">
    <div class="detail-container">
        <div class="breadcrumb-modern">
            <a href="{{ route('pembeli-web.produk') }}">Produk</a>
            <i class="bi bi-chevron-right small"></i>
            <span>{{ $produk->nama }}</span>
        </div>

        <section class="detail-compact-card mb-4">
            <div class="row g-0 align-items-stretch">
                <div class="col-lg-5">
                    <div class="detail-gallery-wrap h-100">
                        <div class="detail-gallery-main" id="mainProductImage">
                            <span class="detail-status-badge {{ $stok <= 0 ? 'empty' : '' }}">
                                <i class="bi {{ $stok > 0 ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                                {{ $stok > 0 ? 'Tersedia' : 'Stok Habis' }}
                            </span>
                            @if($gambarUtama)
                                <img src="{{ asset('storage/' . $gambarUtama) }}" alt="{{ $produk->nama }}">
                            @else
                                <div class="detail-placeholder">
                                    <div>
                                        <i class="bi bi-box-seam display-5 d-block mb-3"></i>
                                        <div>{{ $produk->nama }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if($gambar->count() > 1)
                            <div class="detail-thumbs">
                                @foreach($gambar as $index => $img)
                                    <button type="button" class="detail-thumb js-thumb {{ $index === 0 ? 'active' : '' }}" data-src="{{ asset('storage/' . $img->url_gambar) }}" aria-label="Lihat gambar {{ $index + 1 }}">
                                        <img src="{{ asset('storage/' . $img->url_gambar) }}" alt="{{ $produk->nama }} {{ $index + 1 }}">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="detail-info-wrap h-100 d-flex flex-column justify-content-center">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                            <span class="eyebrow"><i class="bi bi-patch-check-fill"></i> Produk pilihan</span>
                            <span class="meta-chip"><i class="bi bi-basket2"></i>{{ $produk->satuan ?: 'produk' }}</span>
                        </div>

                        <h1 class="product-title-compact">{{ $produk->nama }}</h1>
                        <p class="product-desc-compact mb-0">{{ $deskripsiProduk }}</p>

                        <div class="detail-meta-row">
                            <span class="detail-stat-pill"><i class="bi bi-star-fill"></i> {{ $rataRating ?: '-' }} rating</span>
                            <span class="detail-stat-pill"><i class="bi bi-chat-dots"></i> {{ $jumlahUlasan }} ulasan</span>
                            <span class="detail-stat-pill"><i class="bi bi-bag-check"></i> {{ $totalTerjual }} terjual</span>
                            <span class="detail-stat-pill"><i class="bi bi-box-seam"></i> Stok {{ $stok }} {{ $produk->satuan }}</span>
                        </div>

                        <div class="buy-panel">
                            <div class="row g-3 align-items-center">
                                <div class="col-lg-4">
                                    <div class="small text-muted fw-bold text-uppercase mb-1">Harga</div>
                                    <div class="purchase-price">{{ $rupiah($produk->harga) }}</div>
                                </div>
                                <div class="col-lg-8">
                                    @if($stok > 0)
                                        <div class="row g-2 align-items-end">
                                            <div class="col-sm-4">
                                                <label class="form-label fw-black small mb-2">Jumlah</label>
                                                <div class="qty-control">
                                                    <button type="button" class="js-qty-minus" aria-label="Kurangi jumlah"><i class="bi bi-dash-lg"></i></button>
                                                    <input type="number" class="js-qty-input" min="1" max="{{ max(1, $stok) }}" value="1" aria-label="Jumlah pesanan">
                                                    <button type="button" class="js-qty-plus" aria-label="Tambah jumlah"><i class="bi bi-plus-lg"></i></button>
                                                </div>
                                            </div>
                                            <div class="col-sm-8">
                                                <div class="buy-action-grid">
                                                    <form action="{{ route('pembeli-web.keranjang.store', $produk) }}" method="POST" class="js-add-cart-form m-0">
                                                        @csrf
                                                        <input type="hidden" name="jumlah" value="1" class="js-cart-qty-hidden">
                                                        <button class="btn btn-soft-brand w-100 py-3" type="submit"><i class="bi bi-bag-plus me-2"></i> Keranjang</button>
                                                    </form>
                                                    <form action="{{ route('pembeli-web.checkout.buy-now', $produk) }}" method="POST" class="js-buy-now-form m-0">
                                                        @csrf
                                                        <input type="hidden" name="jumlah" value="1" class="js-buy-qty-hidden">
                                                        <button class="btn btn-brand w-100 py-3" type="submit"><i class="bi bi-lightning-charge-fill me-2"></i> Beli Sekarang</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-warning alert-shop mb-0"><i class="bi bi-exclamation-circle me-2"></i> Produk sedang habis.</div>
                                    @endif
                                </div>
                            </div>
                            @if($waProduk)
                                <div class="mt-3">
                                    <a href="https://wa.me/{{ $waProduk }}?text={{ urlencode('Halo, saya ingin menanyakan produk ' . $produk->nama) }}" target="_blank" rel="noopener" class="btn btn-plain px-4"><i class="bi bi-whatsapp me-2 text-success"></i> Tanya Toko</a>
                                </div>
                            @endif
                        </div>

                        <div class="quick-info-grid">
                            <div class="quick-info-card"><i class="bi bi-box2-heart d-block"></i><div class="label">Isi</div><div class="value">{{ $isiLabel }}</div></div>
                            <div class="quick-info-card"><i class="bi bi-clock-history d-block"></i><div class="label">Masa simpan</div><div class="value">{{ $masaSimpanLabel }}</div></div>
                            <div class="quick-info-card"><i class="bi bi-speedometer2 d-block"></i><div class="label">Berat</div><div class="value">{{ $beratLabel }}</div></div>
                            <div class="quick-info-card"><i class="bi bi-tag d-block"></i><div class="label">Satuan</div><div class="value">{{ $produk->satuan ?: '-' }}</div></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="detail-info-section row g-3 mb-5">
            <div class="col-lg-7">
                <div class="simple-info-card">
                    <h2 class="h4 simple-info-title">Informasi produk</h2>
                    <div class="simple-list">
                        <div class="simple-list-item">
                            <div class="icon"><i class="bi bi-card-text"></i></div>
                            <div><div class="title">Deskripsi</div><div class="text">{{ $deskripsiProduk }}</div></div>
                        </div>
                        <div class="simple-list-item">
                            <div class="icon"><i class="bi bi-snow"></i></div>
                            <div><div class="title">Penyimpanan</div><div class="text">{{ $saranSimpan }}</div></div>
                        </div>
                        <div class="simple-list-item">
                            <div class="icon"><i class="bi bi-cup-hot"></i></div>
                            <div><div class="title">Penyajian</div><div class="text">{{ $saranSaji }}</div></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="simple-info-card">
                    <h2 class="h4 simple-info-title">Layanan pemesanan</h2>
                    <div class="simple-list">
                        <div class="simple-list-item"><div class="icon"><i class="bi bi-shop"></i></div><div><div class="title">Ambil di toko</div><div class="text">Pesanan bisa diambil setelah diproses.</div></div></div>
                        <div class="simple-list-item"><div class="icon"><i class="bi bi-geo-alt"></i></div><div><div class="title">Kurir toko</div><div class="text">Area layanan: {{ $pengaturan->area_pengiriman ?: 'sekitar toko' }}.</div></div></div>
                        <div class="simple-list-item"><div class="icon"><i class="bi bi-wallet2"></i></div><div><div class="title">Pembayaran</div><div class="text">{{ $pengaturan->info_pembayaran ?: 'Pembayaran mengikuti pilihan checkout.' }}</div></div></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mb-5" id="ulasan-produk">
            <div class="review-section-head mb-3">
                <div>
                    <span class="eyebrow mb-2"><i class="bi bi-chat-heart-fill"></i> Ulasan produk</span>
                    <h2 class="section-heading h2 mb-1">Penilaian pembeli</h2>
                    <p class="section-subtitle mb-0">Ulasan dari pembeli yang sudah menyelesaikan pesanan.</p>
                </div>
                @if($jumlahUlasan > 0)
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('pembeli-web.produk.detail', $produk) }}#ulasan-produk" class="review-filter {{ $filterUlasan === 'semua' ? 'active' : '' }} text-decoration-none">Semua {{ $jumlahUlasan }}</a>
                        <a href="{{ route('pembeli-web.produk.detail', ['produk' => $produk, 'filter_ulasan' => 'foto']) }}#ulasan-produk" class="review-filter {{ $filterUlasan === 'foto' ? 'active' : '' }} text-decoration-none">Foto {{ $jumlahUlasanFoto ?? 0 }}</a>
                        <a href="{{ route('pembeli-web.produk.detail', ['produk' => $produk, 'filter_ulasan' => 'video']) }}#ulasan-produk" class="review-filter {{ $filterUlasan === 'video' ? 'active' : '' }} text-decoration-none">Video {{ $jumlahUlasanVideo ?? 0 }}</a>
                        <a href="{{ route('pembeli-web.produk.detail', ['produk' => $produk, 'filter_ulasan' => 'bintang5']) }}#ulasan-produk" class="review-filter {{ $filterUlasan === 'bintang5' ? 'active' : '' }} text-decoration-none">Bintang 5 {{ $jumlahUlasanBintang5 ?? 0 }}</a>
                    </div>
                @endif
            </div>

            @if($jumlahUlasan > 0)
                <div class="review-summary-card mb-3">
                    <div class="row g-3 align-items-center">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="review-average">{{ $rataRating ?: '-' }}</div>
                                <div>
                                    <div class="rating-stars mb-1">
                                        @for($i=1;$i<=5;$i++)
                                            <i class="bi {{ $i <= $ratingBulatan ? 'bi-star-fill' : 'bi-star' }}"></i>
                                        @endfor
                                    </div>
                                    <div class="small text-muted fw-semibold">{{ $jumlahUlasan }} ulasan</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <div class="d-grid gap-2">
                                @foreach(($ratingDistribusi ?? []) as $bintang => $totalRating)
                                    @php $persenRating = $jumlahUlasan > 0 ? round(($totalRating / $jumlahUlasan) * 100) : 0; @endphp
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="small fw-black" style="width: 46px;">{{ $bintang }} <i class="bi bi-star-fill text-warning"></i></div>
                                        <div class="rating-bar flex-grow-1"><span style="width: {{ $persenRating }}%"></span></div>
                                        <div class="small text-muted fw-bold text-end" style="width: 34px;">{{ $totalRating }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                @if($ulasan->count())
                    <div class="review-card-grid">
                        @foreach($ulasan as $item)
                            @include('pembeli.partials.review-line', ['item' => $item, 'produk' => $produk])
                        @endforeach
                    </div>

                    <div class="mt-4 d-flex justify-content-center">
                        {{ $ulasan->links() }}
                    </div>
                @else
                    <div class="simple-info-card text-center">
                        <i class="bi bi-funnel fs-1 text-brand"></i>
                        <h3 class="h5 fw-black mt-2">Belum ada ulasan pada filter ini.</h3>
                        <p class="text-muted mb-0">Pilih filter semua ulasan untuk melihat penilaian pembeli lainnya.</p>
                    </div>
                @endif
            @else
                <div class="simple-info-card text-center">
                    <i class="bi bi-chat-square-text fs-1 text-brand"></i>
                    <h3 class="h5 fw-black mt-3">Belum ada ulasan untuk produk ini.</h3>
                    <p class="text-muted mb-0">Ulasan akan muncul setelah pesanan selesai dan pembeli memberi penilaian.</p>
                </div>
            @endif
        </section>

        @if($produkLain->count())
            <section>
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
                    <div>
                        <span class="eyebrow mb-2"><i class="bi bi-basket"></i> Produk lain</span>
                        <h2 class="section-heading h2 mb-0">Rekomendasi untuk Anda</h2>
                    </div>
                    <a href="{{ route('pembeli-web.produk') }}" class="btn btn-soft-brand px-4 align-self-start align-self-md-auto">Semua Produk</a>
                </div>
                <div class="row g-3 g-lg-4">
                    @foreach($produkLain as $item)
                        <div class="col-sm-6 col-lg-3">@include('pembeli.partials.product-card', ['produk' => $item, 'compact' => true])</div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.js-thumb').forEach(function (thumb) {
        thumb.addEventListener('click', function () {
            const main = document.querySelector('#mainProductImage img');
            if (main) main.src = this.dataset.src;
            document.querySelectorAll('.js-thumb').forEach(el => el.classList.remove('active'));
            this.classList.add('active');
        });
    });

    document.querySelectorAll('.buy-panel').forEach(function (card) {
        const input = card.querySelector('.js-qty-input');
        const minus = card.querySelector('.js-qty-minus');
        const plus = card.querySelector('.js-qty-plus');
        const hiddenInputs = card.querySelectorAll('.js-cart-qty-hidden, .js-buy-qty-hidden');
        if (!input || !minus || !plus) return;

        const clamp = function (value) {
            const min = parseInt(input.min || '1', 10);
            const max = parseInt(input.max || '9999', 10);
            return Math.max(min, Math.min(max, value));
        };

        const syncQty = function () {
            input.value = clamp(parseInt(input.value || '1', 10) || 1);
            hiddenInputs.forEach(function (hidden) { hidden.value = input.value; });
        };

        minus.addEventListener('click', function () {
            input.value = clamp((parseInt(input.value || '1', 10) || 1) - 1);
            syncQty();
        });
        plus.addEventListener('click', function () {
            input.value = clamp((parseInt(input.value || '1', 10) || 1) + 1);
            syncQty();
        });
        input.addEventListener('input', syncQty);
        input.addEventListener('change', syncQty);
        card.querySelectorAll('form').forEach(function (form) {
            form.addEventListener('submit', syncQty);
        });
        syncQty();
    });
</script>
@endpush
