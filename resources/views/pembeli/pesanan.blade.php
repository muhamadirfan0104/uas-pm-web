@extends('layouts.pembeli')

@section('title', 'Pesanan Saya - SiTahu')

@push('styles')
<style>
    .order-hero {
        padding: 30px;
        margin-bottom: 22px;
        background:
            radial-gradient(circle at top right, rgba(223, 186, 104, 0.25), transparent 32%),
            linear-gradient(135deg, #ffffff 0%, #fff8e8 100%);
    }

    .order-hero h1 {
        margin: 12px 0 0;
        color: var(--heading);
        font-size: clamp(30px, 4.5vw, 48px);
        line-height: 1;
        letter-spacing: -0.075em;
    }

    .order-hero h1 span {
        color: var(--brand-text);
    }

    .order-hero p {
        margin: 12px 0 0;
        max-width: 720px;
        color: var(--muted);
        line-height: 1.7;
        font-size: 15px;
    }

    .search-card {
        padding: 18px;
        margin-bottom: 20px;
    }

    .search-form {
        display: grid;
        grid-template-columns: 1fr auto;
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

    .order-list {
        display: grid;
        gap: 14px;
    }

    .order-card {
        padding: 18px;
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 16px;
        align-items: center;
        transition: 0.18s ease;
    }

    .order-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 28px rgba(17, 24, 39, 0.09);
    }

    .order-title {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 10px;
    }

    .order-title h2 {
        margin: 0;
        color: var(--heading);
        font-size: 18px;
        letter-spacing: -0.04em;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 850;
        border: 1px solid var(--line);
        background: #f9fafb;
        color: var(--muted);
    }

    .status-wait {
        background: #fff8e8;
        color: var(--brand-text);
        border-color: rgba(223, 186, 104, 0.45);
    }

    .status-success {
        background: #ecfdf5;
        color: #15803d;
        border-color: #bbf7d0;
    }

    .status-danger {
        background: #fef2f2;
        color: #b91c1c;
        border-color: #fecaca;
    }

    .order-info {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        color: var(--muted);
        font-size: 13px;
        line-height: 1.5;
    }

    .order-info strong {
        color: var(--heading);
    }

    .order-total {
        text-align: right;
    }

    .order-total span {
        display: block;
        color: var(--muted);
        font-size: 12px;
        margin-bottom: 4px;
    }

    .order-total strong {
        display: block;
        color: var(--brand-text);
        font-size: 20px;
        letter-spacing: -0.04em;
        margin-bottom: 10px;
    }

    .empty-card {
        padding: 40px 22px;
        text-align: center;
    }

    .empty-icon {
        width: 66px;
        height: 66px;
        margin: 0 auto 16px;
        display: grid;
        place-items: center;
        border-radius: 20px;
        background: var(--brand-soft);
        color: var(--brand-text);
        font-size: 30px;
    }

    .empty-card h2 {
        margin: 0;
        color: var(--heading);
        font-size: 24px;
        letter-spacing: -0.05em;
    }

    .empty-card p {
        margin: 9px auto 0;
        max-width: 500px;
        color: var(--muted);
        line-height: 1.7;
        font-size: 14px;
    }

    .tips-card {
        margin-top: 20px;
        padding: 18px;
        background: #ffffff;
    }

    .tips-card h2 {
        margin: 0 0 10px;
        color: var(--heading);
        font-size: 20px;
        letter-spacing: -0.04em;
    }

    .tips-list {
        margin: 0;
        padding-left: 18px;
        color: var(--muted);
        font-size: 14px;
        line-height: 1.8;
    }

    @media (max-width: 720px) {
        .order-hero,
        .search-card,
        .order-card {
            padding: 20px;
        }

        .search-form,
        .order-card {
            grid-template-columns: 1fr;
        }

        .order-total {
            text-align: left;
        }
    }
</style>
@endpush

@section('content')
<section class="page-card order-hero">
    <div class="badge">Pesanan Saya</div>

    <h1>
        Cek status pesananmu <span>dengan mudah</span>
    </h1>

    <p>
        Masukkan nomor invoice, email, atau nomor WhatsApp yang kamu pakai saat checkout.
        Setelah itu, kamu bisa melihat detail produk, pembayaran, dan cara menerima pesanan.
    </p>
</section>

<section class="page-card search-card">
    <form action="{{ route('pembeli-web.pesanan.index') }}" method="GET" class="search-form">
        <div class="form-group">
            <label for="keyword">Cari pesanan</label>
            <input
                type="text"
                id="keyword"
                name="keyword"
                class="form-control"
                value="{{ $keyword }}"
                placeholder="Contoh: INV-202606..., email, atau nomor WhatsApp"
            >
        </div>

        <button type="submit" class="btn btn-primary">
            Cek Pesanan
        </button>
    </form>
</section>

@if($keyword !== '')
    @if($pesananList->count())
        <section class="order-list">
            @foreach($pesananList as $pesanan)
                @php
                    $status = $pesanan->status;
                    $statusPembayaran = $pesanan->status_pembayaran;

                    $statusClass = 'status-wait';

                    if (in_array($status, ['selesai', 'dibayar'])) {
                        $statusClass = 'status-success';
                    }

                    if (in_array($status, ['dibatalkan']) || in_array($statusPembayaran, ['gagal', 'kedaluwarsa', 'dibatalkan'])) {
                        $statusClass = 'status-danger';
                    }
                @endphp

                <article class="page-card order-card">
                    <div>
                        <div class="order-title">
                            <h2>{{ $pesanan->nomor_invoice }}</h2>
                            <span class="status-pill {{ $statusClass }}">
                                {{ ucwords(str_replace('_', ' ', $pesanan->status)) }}
                            </span>
                        </div>

                        <div class="order-info">
                            <span>
                                Tanggal:
                                <strong>{{ optional($pesanan->tanggal_pesanan)->format('d M Y H:i') }}</strong>
                            </span>

                            <span>
                                Produk:
                                <strong>{{ $pesanan->item->sum('jumlah') }} item</strong>
                            </span>

                            <span>
                                Pembayaran:
                                <strong>{{ ucwords(str_replace('_', ' ', $pesanan->status_pembayaran)) }}</strong>
                            </span>

                            <span>
                                Penerimaan:
                                <strong>{{ $pesanan->metode_pengambilan === 'kurir_toko' ? 'Kurir toko' : 'Ambil di toko' }}</strong>
                            </span>
                        </div>
                    </div>

                    <div class="order-total">
                        <span>Total bayar</span>
                        <strong>Rp {{ number_format($pesanan->total_bayar, 0, ',', '.') }}</strong>

                        <a href="{{ route('pembeli-web.pesanan.show', $pesanan->nomor_invoice) }}" class="btn btn-outline">
                            Detail Pesanan
                        </a>
                    </div>
                </article>
            @endforeach
        </section>
    @else
        <section class="page-card empty-card">
            <div class="empty-icon">🔎</div>
            <h2>Pesanan tidak ditemukan</h2>
            <p>
                Coba cek lagi nomor invoice, email, atau nomor WhatsApp yang kamu masukkan.
                Pastikan sama seperti data saat checkout.
            </p>
        </section>
    @endif
@else
    <section class="page-card empty-card">
        <div class="empty-icon">🧾</div>
        <h2>Masukkan data pesanan dulu</h2>
        <p>
            Kamu bisa mencari pesanan memakai nomor invoice, email, atau nomor WhatsApp.
            Nomor invoice biasanya muncul setelah checkout berhasil.
        </p>
    </section>
@endif

<section class="page-card tips-card">
    <h2>Tips cek pesanan</h2>

    <ul class="tips-list">
        <li>Gunakan nomor invoice kalau ingin hasil paling tepat.</li>
        <li>Gunakan email atau nomor WhatsApp kalau lupa nomor invoice.</li>
        <li>Status pesanan akan berubah sesuai proses dari toko.</li>
    </ul>
</section>
@endsection
