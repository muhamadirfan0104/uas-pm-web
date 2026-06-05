@extends('layouts.pembeli')

@section('title', 'Alamat Saya - SiTahu')

@push('styles')
<style>
    .address-page {
        display: grid;
        gap: 18px;
    }

    .address-hero {
        padding: 24px;
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 18px;
        align-items: center;
        background:
            radial-gradient(circle at top right, rgba(223, 186, 104, 0.24), transparent 34%),
            linear-gradient(135deg, #ffffff 0%, #fff8e8 100%);
    }

    .address-hero h1 {
        margin: 10px 0 0;
        color: var(--heading);
        font-size: clamp(30px, 4vw, 44px);
        line-height: 1.04;
        letter-spacing: -0.07em;
    }

    .address-hero h1 span {
        color: var(--brand-dark);
    }

    .address-hero p {
        margin: 10px 0 0;
        max-width: 720px;
        color: var(--muted);
        font-size: 14px;
        line-height: 1.7;
    }

    .alert {
        padding: 13px 15px;
        border-radius: 16px;
        font-size: 14px;
        font-weight: 800;
        border: 1px solid transparent;
    }

    .alert-success {
        background: #ecfdf5;
        color: #15803d;
        border-color: #bbf7d0;
    }

    .address-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .address-card {
        padding: 20px;
        display: grid;
        gap: 14px;
        transition: 0.16s ease;
    }

    .address-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 28px rgba(17, 24, 39, 0.08);
    }

    .address-card-head {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: flex-start;
    }

    .address-card h2 {
        margin: 0;
        color: var(--heading);
        font-size: 19px;
        line-height: 1.25;
        letter-spacing: -0.045em;
    }

    .address-card p {
        margin: 6px 0 0;
        color: var(--muted);
        font-size: 13.5px;
        line-height: 1.65;
    }

    .address-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 999px;
        background: var(--brand-soft);
        color: var(--brand-dark);
        border: 1px solid rgba(223, 186, 104, 0.42);
        font-size: 12px;
        font-weight: 900;
        white-space: nowrap;
    }

    .address-detail {
        padding: 14px;
        border-radius: 16px;
        background: #f9fafb;
        border: 1px solid var(--line);
        display: grid;
        gap: 8px;
    }

    .address-detail-row {
        display: grid;
        grid-template-columns: 105px minmax(0, 1fr);
        gap: 10px;
        font-size: 13px;
        line-height: 1.5;
    }

    .address-detail-row span {
        color: var(--muted);
        font-weight: 900;
    }

    .address-detail-row strong {
        color: var(--heading);
        font-weight: 850;
        overflow-wrap: anywhere;
    }

    .address-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .address-actions form {
        margin: 0;
    }

    .btn-danger-soft {
        background: #fef2f2;
        color: #b91c1c;
        border-color: #fecaca;
    }

    .empty-address {
        padding: 42px 24px;
        text-align: center;
    }

    .empty-icon {
        width: 66px;
        height: 66px;
        margin: 0 auto 15px;
        border-radius: 22px;
        display: grid;
        place-items: center;
        background: var(--brand-soft);
        color: var(--brand-dark);
        font-size: 30px;
    }

    .empty-address h2 {
        margin: 0;
        color: var(--heading);
        font-size: 26px;
        letter-spacing: -0.055em;
    }

    .empty-address p {
        margin: 9px auto 0;
        max-width: 520px;
        color: var(--muted);
        font-size: 14px;
        line-height: 1.7;
    }

    .empty-actions {
        margin-top: 18px;
        display: flex;
        justify-content: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    @media (max-width: 880px) {
        .address-hero,
        .address-grid {
            grid-template-columns: 1fr;
        }

        .address-hero > a {
            width: fit-content;
        }
    }

    @media (max-width: 560px) {
        .address-hero,
        .address-card {
            padding: 18px;
        }

        .address-detail-row {
            grid-template-columns: 1fr;
            gap: 3px;
        }

        .address-actions .btn,
        .address-actions form,
        .address-actions button {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="address-page">
    <section class="page-card address-hero">
        <div>
            <div class="badge">Alamat Saya</div>

            <h1>
                Kelola alamat <span>pengirimanmu</span>
            </h1>

            <p>
                Simpan alamat pembeli supaya proses checkout kurir toko lebih cepat.
                Pilih satu alamat sebagai alamat utama.
            </p>
        </div>

        <a href="{{ route('pembeli-web.alamat.create') }}" class="btn btn-primary">
            + Tambah Alamat
        </a>
    </section>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($alamat->count())
        <section class="address-grid">
            @foreach($alamat as $item)
                <article class="page-card address-card">
                    <div class="address-card-head">
                        <div>
                            <h2>{{ $item->nama_penerima }}</h2>
                            <p>{{ $item->telepon }}</p>
                        </div>

                        @if($item->utama)
                            <span class="address-badge">
                                ★ Utama
                            </span>
                        @endif
                    </div>

                    <div class="address-detail">
                        <div class="address-detail-row">
                            <span>Alamat</span>
                            <strong>{{ $item->alamat_lengkap }}</strong>
                        </div>

                        <div class="address-detail-row">
                            <span>Latitude</span>
                            <strong>{{ $item->latitude ?: '-' }}</strong>
                        </div>

                        <div class="address-detail-row">
                            <span>Longitude</span>
                            <strong>{{ $item->longitude ?: '-' }}</strong>
                        </div>
                    </div>

                    <div class="address-actions">
                        @if($item->latitude && $item->longitude)
                            <a
                                href="https://www.google.com/maps?q={{ $item->latitude }},{{ $item->longitude }}"
                                target="_blank"
                                class="btn btn-outline"
                            >
                                Buka Maps
                            </a>
                        @endif

                        <a href="{{ route('pembeli-web.alamat.edit', $item) }}" class="btn btn-outline">
                            Edit
                        </a>

                        @if(! $item->utama)
                            <form action="{{ route('pembeli-web.alamat.utama', $item) }}" method="POST">
                                @csrf
                                @method('PATCH')

                                <button type="submit" class="btn btn-outline">
                                    Jadikan Utama
                                </button>
                            </form>
                        @endif

                        <form
                            action="{{ route('pembeli-web.alamat.destroy', $item) }}"
                            method="POST"
                            onsubmit="return confirm('Hapus alamat ini?')"
                        >
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="btn btn-danger-soft">
                                Hapus
                            </button>
                        </form>
                    </div>
                </article>
            @endforeach
        </section>
    @else
        <section class="page-card empty-address">
            <div class="empty-icon">📍</div>

            <h2>Belum ada alamat tersimpan</h2>

            <p>
                Tambahkan alamat supaya saat checkout metode kurir toko,
                data penerima dan alamat bisa langsung digunakan.
            </p>

            <div class="empty-actions">
                <a href="{{ route('pembeli-web.alamat.create') }}" class="btn btn-primary">
                    Tambah Alamat Pertama
                </a>

                <a href="{{ route('pembeli-web.profil') }}" class="btn btn-outline">
                    Kembali ke Profil
                </a>
            </div>
        </section>
    @endif
</div>
@endsection