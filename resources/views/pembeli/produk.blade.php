@extends('layouts.pembeli')

@section('title', 'Katalog Produk - SiTahu')

@push('styles')
<style>
    .catalog-tools {
        border-radius: 28px;
        background: #fff;
        border: 1px solid var(--line);
        box-shadow: var(--shadow-sm);
    }
    .filter-label {
        color: var(--muted);
        font-size: 12px;
        font-weight: 900;
        letter-spacing: .02em;
        text-transform: uppercase;
        margin-bottom: 8px;
    }
    .filter-control {
        min-height: 48px;
        border-radius: 16px;
        border-color: var(--line);
        background-color: #f9fafb;
        font-weight: 750;
        font-size: 14px;
        box-shadow: none !important;
    }
    .filter-control:focus {
        background: #fff;
        border-color: rgba(200,147,53,.55);
        box-shadow: 0 0 0 .25rem rgba(200,147,53,.12) !important;
    }
    .quick-chip {
        border: 1px solid var(--line);
        background: #fff;
        border-radius: 999px;
        padding: 9px 13px;
        text-decoration: none;
        color: var(--muted);
        font-weight: 850;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .quick-chip:hover,
    .quick-chip.active {
        background: var(--brand-soft);
        border-color: rgba(200,147,53,.28);
        color: var(--brand-dark);
    }
    .result-chip {
        border: 1px solid rgba(200,147,53,.30);
        background: var(--brand-soft);
        color: var(--brand-dark);
        border-radius: 999px;
        padding: 9px 13px;
        font-size: 13px;
        font-weight: 850;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .empty-catalog {
        border: 1px dashed rgba(200,147,53,.36);
        border-radius: 28px;
        background: #fff;
        padding: 4rem 1.25rem;
    }
</style>
@endpush

@section('content')
<div class="container py-4 py-lg-5">
    <section class="catalog-tools p-3 p-lg-4 mb-4">
        <form id="catalogFilterForm" action="{{ route('pembeli-web.produk') }}" method="GET">
            @if($search !== '')
                <input type="hidden" name="search" value="{{ $search }}">
            @endif

            <div class="row g-3 align-items-end">
                <div class="col-md-6 col-lg-4">
                    <label for="sort" class="filter-label">Urutkan</label>
                    <select id="sort" name="sort" class="form-select filter-control js-auto-submit" aria-label="Urutkan produk">
                        <option value="terbaru" {{ $sort === 'terbaru' ? 'selected' : '' }}>Paling Baru</option>
                        <option value="termurah" {{ $sort === 'termurah' ? 'selected' : '' }}>Harga Terendah</option>
                        <option value="termahal" {{ $sort === 'termahal' ? 'selected' : '' }}>Harga Tertinggi</option>
                        <option value="rating" {{ $sort === 'rating' ? 'selected' : '' }}>Rating Tertinggi</option>
                        <option value="stok_banyak" {{ $sort === 'stok_banyak' ? 'selected' : '' }}>Stok Terbanyak</option>
                        <option value="nama" {{ $sort === 'nama' ? 'selected' : '' }}>Nama A-Z</option>
                    </select>
                </div>

                <div class="col-md-6 col-lg-4">
                    <label for="stok" class="filter-label">Ketersediaan</label>
                    <select id="stok" name="stok" class="form-select filter-control js-auto-submit" aria-label="Filter stok produk">
                        <option value="semua" {{ $stok === 'semua' ? 'selected' : '' }}>Semua Stok</option>
                        <option value="tersedia" {{ $stok === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                        <option value="habis" {{ $stok === 'habis' ? 'selected' : '' }}>Habis</option>
                    </select>
                </div>

                <div class="col-lg-4 d-flex justify-content-lg-end gap-2">
                    @if($search || $stok !== 'semua' || $sort !== 'terbaru')
                        <a href="{{ route('pembeli-web.produk') }}" class="btn btn-soft-brand px-4 py-3 w-100 w-lg-auto">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>

        <div class="d-flex flex-wrap gap-2 mt-3">
            @if($search !== '')
                <span class="result-chip">
                    <i class="bi bi-search"></i>
                    Hasil: {{ $search }}
                </span>
            @endif

            <a class="quick-chip {{ $search === '' && $stok === 'semua' ? 'active' : '' }}" href="{{ route('pembeli-web.produk', ['sort' => $sort]) }}">Semua Produk</a>
            <a class="quick-chip {{ $stok === 'tersedia' ? 'active' : '' }}" href="{{ route('pembeli-web.produk', array_filter(['search' => $search ?: null, 'stok' => 'tersedia', 'sort' => $sort])) }}">Tersedia</a>
            <a class="quick-chip {{ $search === 'putih' ? 'active' : '' }}" href="{{ route('pembeli-web.produk', array_filter(['search' => 'putih', 'stok' => $stok !== 'semua' ? $stok : null, 'sort' => $sort])) }}">Tahu Putih</a>
            <a class="quick-chip {{ $search === 'kuning' ? 'active' : '' }}" href="{{ route('pembeli-web.produk', array_filter(['search' => 'kuning', 'stok' => $stok !== 'semua' ? $stok : null, 'sort' => $sort])) }}">Tahu Kuning</a>
            <a class="quick-chip {{ $search === 'paket' ? 'active' : '' }}" href="{{ route('pembeli-web.produk', array_filter(['search' => 'paket', 'stok' => $stok !== 'semua' ? $stok : null, 'sort' => $sort])) }}">Paket</a>
        </div>
    </section>

    @if($produkList->count())
        <div class="row g-3 g-lg-4">
            @foreach($produkList as $produk)
                <div class="col-sm-6 col-lg-4 col-xl-3">@include('pembeli.partials.product-card', ['produk' => $produk])</div>
            @endforeach
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $produkList->links() }}
        </div>
    @else
        <div class="empty-catalog text-center">
            <div class="stat-icon mx-auto mb-3"><i class="bi bi-search"></i></div>
            <h2 class="h4 fw-bold">Produk tidak ditemukan.</h2>
            <p class="text-muted mb-4">Produk tidak ditemukan.</p>
            <a href="{{ route('pembeli-web.produk') }}" class="btn btn-brand px-4 py-3">Reset Filter</a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('catalogFilterForm');
        document.querySelectorAll('.js-auto-submit').forEach(el => {
            el.addEventListener('change', () => form.submit());
        });
    });
</script>
@endpush
