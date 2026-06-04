@extends('layouts.pembeli')

@section('title', 'Produk Tahu - SiTahu')

@push('styles')
<style>
    .produk-hero {
        padding: 30px;
        margin-bottom: 22px;
        display: grid;
        grid-template-columns: 1.15fr 0.85fr;
        gap: 22px;
        align-items: center;
        background:
            radial-gradient(circle at top right, rgba(223, 186, 104, 0.25), transparent 32%),
            linear-gradient(135deg, #ffffff 0%, #fff8e8 100%);
    }

    .produk-hero h1 {
        margin: 14px 0 0;
        color: var(--heading);
        font-size: clamp(32px, 4.8vw, 52px);
        line-height: 1;
        letter-spacing: -0.075em;
    }

    .produk-hero h1 span {
        color: var(--brand-text);
    }

    .produk-hero p {
        margin: 14px 0 0;
        max-width: 660px;
        color: var(--muted);
        line-height: 1.75;
        font-size: 15px;
    }

    .hero-note {
        display: grid;
        gap: 12px;
    }

    .note-card {
        padding: 15px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.78);
        border: 1px solid var(--line);
    }

    .note-card strong {
        display: block;
        color: var(--heading);
        font-size: 14px;
        margin-bottom: 4px;
    }

    .note-card span {
        display: block;
        color: var(--muted);
        font-size: 13px;
        line-height: 1.5;
    }

    .filter-card {
        padding: 18px;
        margin-bottom: 22px;
    }

    .filter-form {
        display: grid;
        grid-template-columns: 1.5fr 0.8fr 0.8fr auto;
        gap: 12px;
        align-items: end;
    }

    .form-group label {
        display: block;
        margin-bottom: 7px;
        color: var(--heading);
        font-size: 13px;
        font-weight: 800;
    }

    .form-control {
        width: 100%;
        min-height: 44px;
        border: 1px solid var(--line);
        border-radius: 12px;
        background: #ffffff;
        color: var(--text);
        padding: 10px 13px;
        outline: none;
        transition: 0.16s ease;
    }

    .form-control:focus {
        border-color: rgba(223, 186, 104, 0.95);
        box-shadow: 0 0 0 4px rgba(223, 186, 104, 0.16);
    }

    .filter-actions {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .result-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin: 0 0 16px;
        color: var(--muted);
        font-size: 14px;
    }

    .result-row strong {
        color: var(--heading);
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }

    .product-card {
        overflow: hidden;
        transition: 0.18s ease;
        background: #ffffff;
    }

    .product-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 28px rgba(17, 24, 39, 0.10);
    }

    .product-img {
        position: relative;
        height: 180px;
        background:
            radial-gradient(circle at top right, rgba(223, 186, 104, 0.22), transparent 34%),
            #f9fafb;
        border-bottom: 1px solid var(--line);
        display: grid;
        place-items: center;
        color: var(--brand-text);
        font-weight: 900;
        font-size: 13px;
        letter-spacing: 0.06em;
        overflow: hidden;
    }

    .product-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .stock-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        padding: 6px 9px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 850;
        border: 1px solid rgba(255, 255, 255, 0.75);
        backdrop-filter: blur(10px);
    }

    .stock-ready {
        background: rgba(236, 253, 245, 0.92);
        color: #15803d;
    }

    .stock-empty {
        background: rgba(254, 242, 242, 0.92);
        color: #b91c1c;
    }

    .product-body {
        padding: 16px;
    }

    .product-name {
        margin: 0;
        color: var(--heading);
        font-size: 15px;
        font-weight: 850;
        line-height: 1.35;
    }

    .product-desc {
        margin: 7px 0 0;
        color: var(--muted);
        font-size: 12px;
        line-height: 1.55;
        min-height: 38px;
    }

    .product-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
        margin-top: 12px;
    }

    .meta-pill {
        padding: 6px 9px;
        border-radius: 999px;
        background: #f9fafb;
        border: 1px solid var(--line);
        color: var(--muted);
        font-size: 12px;
        font-weight: 700;
    }

    .product-price {
        margin-top: 13px;
        color: var(--brand-text);
        font-size: 18px;
        font-weight: 900;
    }

    .product-action {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        margin-top: 13px;
    }

    .product-action .btn {
        min-height: 38px;
        padding: 9px 10px;
        font-size: 13px;
        border-radius: 11px;
    }

    .product-action form {
        margin: 0;
    }

    .product-action form .btn {
        width: 100%;
    }

    .btn-disabled {
        background: #f3f4f6;
        color: #9ca3af;
        border-color: #e5e7eb;
        cursor: not-allowed;
        box-shadow: none;
    }

    .empty-card {
        padding: 34px 22px;
        text-align: center;
    }

    .empty-icon {
        width: 58px;
        height: 58px;
        margin: 0 auto 14px;
        display: grid;
        place-items: center;
        border-radius: 18px;
        background: var(--brand-soft);
        color: var(--brand-text);
        font-size: 25px;
    }

    .empty-card h2 {
        margin: 0;
        color: var(--heading);
        font-size: 22px;
        letter-spacing: -0.04em;
    }

    .empty-card p {
        margin: 8px auto 0;
        max-width: 460px;
        color: var(--muted);
        line-height: 1.65;
        font-size: 14px;
    }

    .pagination-wrap {
        margin-top: 22px;
    }

    .pagination-wrap nav {
        display: flex;
        justify-content: center;
    }

    @media (max-width: 980px) {
        .produk-hero {
            grid-template-columns: 1fr;
        }

        .filter-form {
            grid-template-columns: 1fr 1fr;
        }

        .product-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 620px) {
        .produk-hero,
        .filter-card {
            padding: 20px;
        }

        .filter-form,
        .product-grid {
            grid-template-columns: 1fr;
        }

        .filter-actions {
            align-items: stretch;
            flex-direction: column;
        }

        .filter-actions .btn {
            width: 100%;
        }

        .result-row {
            align-items: flex-start;
            flex-direction: column;
        }

        .product-action {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<section class="page-card produk-hero">
    <div>
        <div class="badge">Etalase Produk SiTahu</div>

        <h1>
            Pilih tahu favoritmu, <span>langsung dari toko</span>
        </h1>

        <p>
            Temukan berbagai pilihan tahu segar untuk lauk keluarga, camilan sore,
            atau stok praktis di rumah. Tinggal pilih produk yang kamu suka, lalu lanjutkan pesanan dengan mudah.
        </p>
    </div>

    <div class="hero-note">
        <div class="note-card">
            <strong>Fresh untuk harian</strong>
            <span>Produk tahu cocok untuk menu makan sederhana sampai camilan hangat.</span>
        </div>

        <div class="note-card">
            <strong>Informasi jelas</strong>
            <span>Kamu bisa cek harga, stok, satuan, dan deskripsi produk sebelum pesan.</span>
        </div>
    </div>
</section>

<section class="page-card filter-card">
    <form action="{{ route('pembeli-web.produk') }}" method="GET" class="filter-form">
        <div class="form-group">
            <label for="search">Cari produk</label>
            <input
                type="text"
                id="search"
                name="search"
                class="form-control"
                value="{{ $search }}"
                placeholder="Contoh: tahu putih, tahu bakso, tahu kuning..."
            >
        </div>

        <div class="form-group">
            <label for="sort">Urutkan</label>
            <select id="sort" name="sort" class="form-control">
                <option value="terbaru" {{ $sort === 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                <option value="termurah" {{ $sort === 'termurah' ? 'selected' : '' }}>Harga termurah</option>
                <option value="termahal" {{ $sort === 'termahal' ? 'selected' : '' }}>Harga termahal</option>
                <option value="stok_banyak" {{ $sort === 'stok_banyak' ? 'selected' : '' }}>Stok terbanyak</option>
                <option value="nama" {{ $sort === 'nama' ? 'selected' : '' }}>Nama A-Z</option>
            </select>
        </div>

        <div class="form-group">
            <label for="stok">Ketersediaan</label>
            <select id="stok" name="stok" class="form-control">
                <option value="semua" {{ $stok === 'semua' ? 'selected' : '' }}>Semua produk</option>
                <option value="tersedia" {{ $stok === 'tersedia' ? 'selected' : '' }}>Stok tersedia</option>
                <option value="habis" {{ $stok === 'habis' ? 'selected' : '' }}>Stok habis</option>
            </select>
        </div>

        <div class="filter-actions">
            <button type="submit" class="btn btn-primary">
                Terapkan
            </button>

            <a href="{{ route('pembeli-web.produk') }}" class="btn btn-outline">
                Reset
            </a>
        </div>
    </form>
</section>

<div class="result-row">
    <div>
        Menampilkan <strong>{{ $produkList->count() }}</strong> dari <strong>{{ $produkList->total() }}</strong> produk.
        @if($search)
            Hasil pencarian untuk <strong>"{{ $search }}"</strong>.
        @endif
    </div>

    <a href="{{ route('pembeli-web.home') }}" class="btn btn-outline">
        Kembali ke Beranda
    </a>
</div>

@if($produkList->count())
    <section class="product-grid">
        @foreach($produkList as $produk)
            @php
                $gambar = $produk->gambarUtama?->url_gambar;
                $deskripsi = $produk->deskripsi
                    ? \Illuminate\Support\Str::limit(strip_tags($produk->deskripsi), 82)
                    : 'Produk tahu segar yang cocok untuk kebutuhan harian keluarga.';

                $stokTersedia = (int) $produk->stok > 0;
            @endphp

            <article class="page-card product-card">
                <div class="product-img">
                    @if($gambar)
                        <img src="{{ asset('storage/' . $gambar) }}" alt="{{ $produk->nama }}">
                    @else
                        PRODUK TAHU
                    @endif

                    @if($stokTersedia)
                        <span class="stock-badge stock-ready">Tersedia</span>
                    @else
                        <span class="stock-badge stock-empty">Stok habis</span>
                    @endif
                </div>

                <div class="product-body">
                    <h2 class="product-name">{{ $produk->nama }}</h2>

                    <p class="product-desc">
                        {{ $deskripsi }}
                    </p>

                    <div class="product-meta">
                        <span class="meta-pill">{{ $produk->satuan ?: 'Satuan' }}</span>

                        @if($produk->isi_per_satuan)
                            <span class="meta-pill">Isi {{ $produk->isi_per_satuan }}</span>
                        @endif

                        @if($produk->berat)
                            <span class="meta-pill">{{ number_format($produk->berat, 0, ',', '.') }} gr</span>
                        @endif

                        <span class="meta-pill">Stok {{ $produk->stok }}</span>
                    </div>

                    <div class="product-price">
                        Rp {{ number_format($produk->harga, 0, ',', '.') }}
                    </div>

                    <div class="product-action">
                        <a href="{{ route('pembeli-web.produk.detail', $produk) }}" class="btn btn-outline">
                            Detail
                        </a>

                        @if($stokTersedia)
                            <form action="{{ route('pembeli-web.keranjang.store', $produk) }}" method="POST">
                                @csrf
                                <input type="hidden" name="jumlah" value="1">
                                <button type="submit" class="btn btn-primary" style="width: 100%;">
                                    Keranjang
                                </button>
                            </form>
                        @else
                            <button type="button" class="btn btn-disabled" disabled>
                                Habis
                            </button>
                        @endif
                    </div>
                </div>
            </article>
        @endforeach
    </section>

    <div class="pagination-wrap">
        {{ $produkList->links() }}
    </div>
@else
    <section class="page-card empty-card">
        <div class="empty-icon">🔎</div>
        <h2>Produk belum ditemukan</h2>
        <p>
            Coba pakai kata kunci lain atau reset pencarian untuk melihat pilihan produk tahu yang tersedia.
        </p>

        <div style="margin-top: 16px;">
            <a href="{{ route('pembeli-web.produk') }}" class="btn btn-primary">
                Lihat Semua Produk
            </a>
        </div>
    </section>
@endif
@endsection
