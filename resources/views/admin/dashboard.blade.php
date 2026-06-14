@extends('layouts.admin')

@section('title', 'Dashboard Admin - SiTahu')
@section('page_title', 'Dashboard Admin')

@php
    $stokKritis = (int) (($stats['stok_menipis'] ?? 0) + ($stats['stok_habis'] ?? 0));
    $siapDiproses = (int) ($stats['dibayar'] ?? 0);
    $aktifDiproses = (int) (($stats['diproses'] ?? 0) + ($stats['siap_diambil'] ?? 0) + ($stats['dalam_pengantaran'] ?? 0));
    $selisihPesanan = (int) ($stats['selisih_pesanan_hari_ini'] ?? 0);
    $selisihPenjualan = (float) ($stats['selisih_penjualan_hari_ini'] ?? 0);
    $growth = $stats['pertumbuhan_periode'] ?? null;

    $statusClass = [
        'menunggu_pembayaran' => 'bg-warning-subtle text-warning-emphasis',
        'dibayar' => 'bg-primary-subtle text-primary-emphasis',
        'diproses' => 'bg-info-subtle text-info-emphasis',
        'siap_diambil' => 'bg-success-subtle text-success-emphasis',
        'dalam_pengantaran' => 'bg-primary-subtle text-primary-emphasis',
        'selesai' => 'bg-success-subtle text-success-emphasis',
        'dibatalkan' => 'bg-danger-subtle text-danger-emphasis',
        'ditolak' => 'bg-danger-subtle text-danger-emphasis',
    ];

    $statusLabel = [
        'menunggu_pembayaran' => 'Belum Bayar',
                'diproses' => 'Diproses',
        'siap_diambil' => 'Siap Diambil',
        'dalam_pengantaran' => 'Dikirim',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
        'ditolak' => 'Ditolak',
    ];
@endphp

@push('styles')
<style>
    .admin-command-dashboard {
        --dash-ink: #182230;
        --dash-muted: #667085;
        --dash-line: #eaecf0;
        --dash-brand: #c89335;
        --dash-brand-hover: #ad7a24;
        --dash-brand-dark: #7a5618;
        --dash-brand-soft: #fff8ea;
        --dash-brand-pale: #fdf4df;
        --dash-surface: #ffffff;
        display: grid;
        gap: 18px;
    }

    .dash-shell-card {
        background: var(--dash-surface);
        border: 1px solid var(--dash-line);
        border-radius: 28px;
        box-shadow: 0 8px 22px rgba(16,24,40,.05);
        overflow: hidden;
    }

    .dash-top-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 380px;
        gap: 18px;
        align-items: stretch;
    }

    .dash-headline {
        position: relative;
        overflow: hidden;
        min-height: 270px;
        padding: clamp(22px, 3vw, 34px);
        border-radius: 34px;
        border: 1px solid rgba(200,147,53,.18);
        background:
            radial-gradient(circle at 92% 8%, rgba(200,147,53,.17), transparent 27rem),
            radial-gradient(circle at 0% 100%, rgba(255,248,234,.9), transparent 21rem),
            linear-gradient(135deg, #fff 0%, #fffaf0 52%, #f8fafc 100%);
        box-shadow: var(--shadow-soft);
    }

    .dash-headline::after {
        content: '';
        position: absolute;
        right: -105px;
        bottom: -135px;
        width: 300px;
        height: 300px;
        border-radius: 999px;
        background: rgba(200,147,53,.12);
    }

    .dash-headline > * { position: relative; z-index: 1; }

    .dash-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 999px;
        background: var(--dash-brand-soft);
        color: var(--dash-brand-dark);
        border: 1px solid rgba(200,147,53,.20);
        font-size: 12px;
        font-weight: 900;
        letter-spacing: .02em;
    }

    .dash-title {
        max-width: 760px;
        margin: 15px 0 10px;
        color: var(--dash-ink);
        font-size: clamp(1.65rem, 3vw, 2.55rem);
        line-height: 1.05;
        letter-spacing: -.07em;
        font-weight: 900;
    }

    .dash-desc {
        max-width: 760px;
        margin: 0;
        color: var(--dash-muted);
        font-size: .94rem;
        line-height: 1.72;
        font-weight: 650;
    }

    .dash-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 22px;
    }

    .dash-btn {
        min-height: 43px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0 15px;
        border-radius: 999px;
        text-decoration: none;
        font-size: .84rem;
        font-weight: 900;
        transition: .16s ease;
    }

    .dash-btn:hover { transform: translateY(-1px); }
    .dash-btn.primary { background: linear-gradient(135deg, var(--dash-brand), var(--dash-brand-hover)); color: #fff; box-shadow: 0 18px 40px rgba(200,147,53,.22); }
    .dash-btn.light { background: #fff; color: var(--dash-ink); border: 1px solid var(--dash-line); }
    .dash-btn.soft { background: var(--dash-brand-soft); color: var(--dash-brand-dark); border: 1px solid rgba(200,147,53,.20); }

    .period-panel {
        padding: 20px;
        border-radius: 28px;
        border: 1px solid var(--dash-line);
        background: rgba(255,255,255,.92);
        box-shadow: 0 8px 22px rgba(16,24,40,.05);
    }

    .period-title {
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--dash-ink);
        font-size: 1rem;
        font-weight: 900;
        letter-spacing: -.04em;
        margin-bottom: 16px;
    }

    .period-panel label {
        color: var(--dash-muted);
        font-size: .7rem;
        font-weight: 900;
        letter-spacing: .06em;
        text-transform: uppercase;
        margin-bottom: 7px;
    }

    .period-panel .form-select {
        min-height: 44px;
        border-radius: 16px;
        border-color: var(--dash-line);
        box-shadow: none;
        font-weight: 800;
    }

    .period-mini-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
        margin-top: 16px;
    }

    .period-mini {
        padding: 14px;
        border-radius: 19px;
        background: var(--dash-brand-soft);
        border: 1px solid rgba(200,147,53,.16);
    }

    .period-mini span {
        display: block;
        color: var(--dash-brand-dark);
        font-size: .68rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .period-mini strong {
        display: block;
        margin-top: 7px;
        color: var(--dash-ink);
        font-size: .96rem;
        font-weight: 900;
        letter-spacing: -.04em;
    }

    .dash-section-title {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 14px;
        margin: 2px 0 -2px;
    }

    .dash-section-title h2 {
        margin: 0;
        color: var(--dash-ink);
        font-size: 1.15rem;
        font-weight: 900;
        letter-spacing: -.045em;
    }

    .dash-section-title p {
        margin: 4px 0 0;
        color: var(--dash-muted);
        font-size: .8rem;
        font-weight: 650;
    }

    .work-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .work-card {
        position: relative;
        min-height: 154px;
        padding: 18px;
        border-radius: 26px;
        border: 1px solid var(--dash-line);
        background: #fff;
        box-shadow: 0 8px 22px rgba(16,24,40,.05);
        color: inherit;
        text-decoration: none;
        overflow: hidden;
        transition: .16s ease;
    }

    .work-card:hover { transform: translateY(-2px); border-color: rgba(200,147,53,.34); box-shadow: 0 18px 45px rgba(16,24,40,.10); }
    .work-card::after { content: ''; position: absolute; right: -34px; top: -42px; width: 116px; height: 116px; border-radius: 999px; background: rgba(200,147,53,.10); }

    .work-top { position: relative; z-index: 1; display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; }
    .work-label { color: var(--dash-muted); font-size: .72rem; font-weight: 900; letter-spacing: .055em; text-transform: uppercase; }
    .work-icon { width: 43px; height: 43px; display: grid; place-items: center; flex: 0 0 auto; border-radius: 16px; background: var(--dash-brand-soft); color: var(--dash-brand-dark); }
    .work-value { position: relative; z-index: 1; margin-top: 18px; color: var(--dash-ink); font-size: 1.9rem; line-height: .95; font-weight: 900; letter-spacing: -.06em; }
    .work-value.money { font-size: 1.2rem; line-height: 1.22; }
    .work-note { position: relative; z-index: 1; display: flex; gap: 7px; margin-top: 13px; color: var(--dash-muted); font-size: .74rem; line-height: 1.45; font-weight: 750; }

    .main-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.45fr) minmax(330px, .9fr);
        gap: 18px;
        align-items: start;
    }

    .stack { display: grid; gap: 18px; }

    .dash-card-head {
        padding: 19px 20px 0;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
    }

    .dash-card-title {
        margin: 0;
        color: var(--dash-ink);
        font-size: 1.02rem;
        line-height: 1.2;
        font-weight: 900;
        letter-spacing: -.04em;
    }

    .dash-card-subtitle {
        margin: 7px 0 0;
        color: var(--dash-muted);
        font-size: .8rem;
        line-height: 1.55;
        font-weight: 650;
    }

    .small-link {
        min-height: 35px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 0 12px;
        border-radius: 999px;
        background: #fff;
        border: 1px solid var(--dash-line);
        color: var(--dash-ink);
        text-decoration: none;
        font-size: .74rem;
        font-weight: 900;
        white-space: nowrap;
        transition: .16s ease;
    }

    .small-link:hover { background: var(--dash-brand-soft); color: var(--dash-brand-dark); border-color: rgba(200,147,53,.28); }

    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        padding: 20px;
    }

    .kpi-card {
        padding: 16px;
        border-radius: 22px;
        border: 1px solid var(--dash-line);
        background: linear-gradient(135deg, #fff, #fffaf0);
    }

    .kpi-label { color: var(--dash-muted); font-size: .7rem; font-weight: 900; letter-spacing: .06em; text-transform: uppercase; }
    .kpi-value { margin-top: 8px; color: var(--dash-ink); font-size: 1.2rem; line-height: 1.15; font-weight: 900; letter-spacing: -.045em; }
    .kpi-note { margin-top: 8px; color: var(--dash-muted); font-size: .72rem; font-weight: 750; line-height: 1.45; }

    .quick-action-grid {
        display: grid;
        gap: 10px;
        padding: 18px;
    }

    .quick-action {
        display: grid;
        grid-template-columns: 44px minmax(0, 1fr) auto;
        gap: 12px;
        align-items: center;
        padding: 13px;
        border-radius: 20px;
        border: 1px solid var(--dash-line);
        background: #fff;
        color: inherit;
        text-decoration: none;
        transition: .16s ease;
    }

    .quick-action:hover { transform: translateY(-1px); background: #fffaf0; border-color: rgba(200,147,53,.26); }
    .quick-icon { width: 44px; height: 44px; border-radius: 16px; display: grid; place-items: center; background: var(--dash-brand-soft); color: var(--dash-brand-dark); }
    .quick-title { color: var(--dash-ink); font-size: .86rem; font-weight: 900; letter-spacing: -.025em; }
    .quick-desc { margin-top: 4px; color: var(--dash-muted); font-size: .72rem; line-height: 1.45; font-weight: 650; }
    .quick-count { min-width: 36px; height: 36px; padding: 0 10px; border-radius: 999px; display: grid; place-items: center; background: var(--dash-brand-soft); color: var(--dash-brand-dark); font-weight: 900; }

    .chart-area { padding: 18px 18px 20px; }
    .line-chart-box {
        position: relative;
        min-height: 292px;
        overflow-x: auto;
        border-radius: 20px;
        border: 1px solid #f2f4f7;
        background:
            linear-gradient(180deg, rgba(255,248,234,.72), rgba(255,255,255,.92)),
            repeating-linear-gradient(to bottom, transparent 0, transparent 51px, rgba(148,163,184,.18) 52px);
        padding: 12px;
    }
    .line-chart-svg { min-width: 760px; width: 100%; height: 270px; display: block; }
    .line-chart-axis { stroke: rgba(102,112,133,.26); stroke-width: 1; }
    .line-chart-grid { stroke: rgba(148,163,184,.18); stroke-width: 1; }
    .line-chart-area { fill: rgba(200,147,53,.13); }
    .line-chart-path { fill: none; stroke: var(--dash-brand); stroke-width: 4; stroke-linecap: round; stroke-linejoin: round; filter: drop-shadow(0 10px 14px rgba(200,147,53,.18)); }
    .line-chart-point { fill: #fff; stroke: var(--dash-brand); stroke-width: 4; cursor: pointer; transition: .15s ease; }
    .line-chart-point-wrap:hover .line-chart-point { r: 7; fill: var(--dash-brand); }
    .line-chart-value { fill: var(--dash-brand-dark); font-size: 10px; font-weight: 900; text-anchor: middle; opacity: .92; pointer-events: none; }
    .line-chart-label { fill: #98a2b3; font-size: 10px; font-weight: 800; text-anchor: middle; }
    .line-chart-tooltip {
        position: fixed;
        z-index: 2000;
        padding: 8px 10px;
        border-radius: 12px;
        background: #111827;
        color: #fff;
        font-size: .72rem;
        line-height: 1.35;
        font-weight: 800;
        box-shadow: 0 14px 35px rgba(17,24,39,.24);
        opacity: 0;
        pointer-events: none;
        transform: translate(-50%, -115%);
        transition: opacity .12s ease;
        white-space: nowrap;
    }
    .line-chart-tooltip.show { opacity: 1; }

    .list-wrap { padding: 10px 0; }
    .list-row {
        display: flex;
        align-items: center;
        gap: 13px;
        padding: 14px 20px;
        border-top: 1px solid #f2f4f7;
        text-decoration: none;
        color: inherit;
        transition: .16s ease;
    }
    .list-row:first-child { border-top: 0; }
    .list-row:hover { background: #fffaf0; }
    .row-thumb, .row-icon, .stock-count { width: 44px; height: 44px; flex: 0 0 auto; border-radius: 16px; display: grid; place-items: center; overflow: hidden; font-weight: 900; }
    .row-thumb { background: #f9fafb; border: 1px solid var(--dash-line); color: var(--dash-brand-dark); }
    .row-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .row-icon { background: var(--dash-brand-soft); color: var(--dash-brand-dark); }
    .stock-count { background: #fef2f2; color: #b91c1c; border: 1px solid #fee2e2; }
    .row-main { min-width: 0; flex: 1; }
    .row-title { color: var(--dash-ink); font-size: .88rem; font-weight: 900; letter-spacing: -.025em; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .row-sub { margin-top: 5px; color: var(--dash-muted); font-size: .73rem; line-height: 1.45; font-weight: 650; }
    .row-price { color: var(--dash-brand-dark); font-size: .86rem; font-weight: 900; white-space: nowrap; text-align: right; }

    .badge-soft { display: inline-flex; align-items: center; justify-content: center; padding: 6px 9px; border-radius: 999px; font-size: .68rem; font-weight: 900; white-space: nowrap; }

    .ops-grid { display: grid; gap: 12px; padding: 20px; }
    .ops-row { display: grid; grid-template-columns: 40px minmax(0, 1fr) auto; gap: 12px; align-items: center; padding: 12px; border-radius: 18px; border: 1px solid var(--dash-line); background: #fff; }
    .ops-icon { width: 40px; height: 40px; border-radius: 15px; display: grid; place-items: center; background: var(--dash-brand-soft); color: var(--dash-brand-dark); }
    .ops-label { color: var(--dash-ink); font-size: .82rem; font-weight: 900; }
    .ops-progress { height: 7px; margin-top: 8px; overflow: hidden; border-radius: 999px; background: #f2f4f7; }
    .ops-progress span { display: block; height: 100%; min-width: 3%; border-radius: inherit; background: linear-gradient(90deg, var(--dash-brand), #f4d894); }

    .two-column { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }

    .empty-state { padding: 38px 20px; text-align: center; color: var(--dash-muted); font-size: .86rem; font-weight: 700; }
    .empty-state i { display: block; margin-bottom: 8px; color: #cbd5e1; font-size: 1.7rem; }


    /* Dashboard admin dibuat lebih sederhana: satu header ringkas, filter periode menyatu, dan kartu prioritas lebih compact. */
    .dash-simple-top {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(300px, 420px);
        gap: 18px;
        align-items: center;
        padding: 22px;
        border-radius: 28px;
        background:
            radial-gradient(circle at 100% 0%, rgba(200,147,53,.12), transparent 26rem),
            linear-gradient(135deg, #fff 0%, #fffaf0 48%, #fff 100%);
        border: 1px solid rgba(200,147,53,.16);
    }

    .dash-simple-top h1 {
        margin: 10px 0 6px;
        color: var(--dash-ink);
        font-size: clamp(1.35rem, 2.3vw, 2rem);
        line-height: 1.12;
        font-weight: 900;
        letter-spacing: -.055em;
    }

    .dash-simple-top p {
        max-width: 620px;
        margin: 0;
        color: var(--dash-muted);
        font-size: .88rem;
        line-height: 1.6;
        font-weight: 650;
    }

    .dash-simple-filter {
        display: grid;
        gap: 12px;
        padding: 16px;
        border-radius: 24px;
        background: rgba(255,255,255,.82);
        border: 1px solid var(--dash-line);
    }

    .dash-simple-filter .filter-row {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .dash-simple-filter label {
        color: var(--dash-muted);
        font-size: .68rem;
        font-weight: 900;
        letter-spacing: .06em;
        text-transform: uppercase;
        margin-bottom: 6px;
    }

    .dash-simple-filter .form-select {
        min-height: 42px;
        border-radius: 16px;
        border-color: var(--dash-line);
        box-shadow: none;
        font-weight: 850;
    }

    .simple-summary {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .simple-summary-item {
        padding: 12px;
        border-radius: 18px;
        background: var(--dash-brand-soft);
        border: 1px solid rgba(200,147,53,.16);
    }

    .simple-summary-item span {
        display: block;
        color: var(--dash-brand-dark);
        font-size: .67rem;
        font-weight: 900;
        letter-spacing: .055em;
        text-transform: uppercase;
    }

    .simple-summary-item strong {
        display: block;
        margin-top: 5px;
        color: var(--dash-ink);
        font-size: .93rem;
        font-weight: 900;
        letter-spacing: -.035em;
    }

    .dash-section-title.compact {
        margin: 0;
    }

    .dash-section-title.compact p {
        display: none;
    }

    .work-card {
        min-height: 122px;
        padding: 16px;
        border-radius: 22px;
    }

    .work-icon {
        width: 38px;
        height: 38px;
        border-radius: 14px;
    }

    .work-value {
        margin-top: 13px;
        font-size: 1.55rem;
    }

    .work-note {
        margin-top: 9px;
        font-size: .71rem;
    }


    @media (max-width: 1200px) {
        .dash-top-grid, .main-grid, .dash-simple-top { grid-template-columns: 1fr; }
        .work-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 780px) {
        .dash-headline { min-height: auto; padding: 20px; border-radius: 28px; }
        .period-mini-grid, .work-grid, .kpi-grid, .two-column, .dash-simple-filter .filter-row, .simple-summary { grid-template-columns: 1fr; }
        .dash-actions .dash-btn { width: 100%; }
        .dash-section-title, .dash-card-head { flex-direction: column; align-items: flex-start; }
        .row-price { display: none; }
    }
</style>
@endpush

@section('content')
<div class="admin-command-dashboard">
    <section class="dash-simple-top dash-shell-card">
        <div>
            <span class="dash-eyebrow">
                <i class="bi bi-grid-1x2-fill"></i>
                Dashboard Admin · {{ $periodeLabel }}
            </span>
            <h1>Ringkasan operasional toko</h1>
            <p>Ringkasan operasional toko.</p>
            <div class="dash-actions">
                <a href="{{ route('admin.pembayaran.index') }}" class="dash-btn primary">
                    <i class="bi bi-shield-check"></i> Pembayaran
                </a>
                <a href="{{ route('admin.pesanan.index') }}" class="dash-btn light">
                    <i class="bi bi-receipt"></i> Pesanan
                </a>
                <a href="{{ route('admin.produk.create') }}" class="dash-btn soft">
                    <i class="bi bi-plus-lg"></i> Produk
                </a>
            </div>
        </div>

        <form class="dash-simple-filter js-instant-filter" method="GET">
            <div class="filter-row">
                <div>
                    <label for="filterBulan">Bulan</label>
                    <select class="form-select" id="filterBulan" name="bulan">
                        @foreach($daftarBulan as $nomorBulan => $namaBulan)
                            <option value="{{ $nomorBulan }}" @selected($filterBulan == $nomorBulan)>{{ $namaBulan }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="filterTahun">Tahun</label>
                    <select class="form-select" id="filterTahun" name="tahun">
                        @foreach($daftarTahun as $tahun)
                            <option value="{{ $tahun }}" @selected($filterTahun == $tahun)>{{ $tahun }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="simple-summary">
                <div class="simple-summary-item">
                    <span>Penjualan</span>
                    <strong>{{ $rupiah($stats['penjualan_periode'] ?? 0) }}</strong>
                </div>
                <div class="simple-summary-item">
                    <span>Rata-rata</span>
                    <strong>{{ $rupiah($stats['nilai_rata_rata_pesanan'] ?? 0) }}</strong>
                </div>
            </div>
        </form>
    </section>

    <div class="dash-section-title compact">
        <div>
            <h2>Prioritas hari ini</h2>
            <p>Pekerjaan utama admin.</p>
        </div>
    </div>

    <section class="work-grid">
        <a href="{{ route('admin.pembayaran.index') }}" class="work-card">
            <div class="work-top">
                <div class="work-label">Verifikasi Transfer</div>
                <div class="work-icon"><i class="bi bi-credit-card-2-front-fill"></i></div>
            </div>
            <div class="work-value">{{ $stats['transfer_perlu_verifikasi'] ?? 0 }}</div>
            <div class="work-note text-warning"><i class="bi bi-exclamation-circle"></i> Bukti transfer menunggu verifikasi.</div>
        </a>

        <a href="{{ route('admin.pesanan.index') }}" class="work-card">
            <div class="work-top">
                <div class="work-label">Perlu Disiapkan</div>
                <div class="work-icon"><i class="bi bi-box-seam-fill"></i></div>
            </div>
            <div class="work-value">{{ $siapDiproses }}</div>
            <div class="work-note"><i class="bi bi-arrow-right-circle"></i> Pesanan sudah masuk proses.</div>
        </a>

        <a href="{{ route('admin.pesanan.index') }}" class="work-card">
            <div class="work-top">
                <div class="work-label">Dalam Proses</div>
                <div class="work-icon"><i class="bi bi-truck"></i></div>
            </div>
            <div class="work-value">{{ $aktifDiproses }}</div>
            <div class="work-note"><i class="bi bi-clock-history"></i> Pesanan sedang berjalan.</div>
        </a>

        <a href="{{ route('admin.stok.index') }}" class="work-card">
            <div class="work-top">
                <div class="work-label">Stok Kritis</div>
                <div class="work-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
            </div>
            <div class="work-value">{{ $stokKritis }}</div>
            <div class="work-note text-danger"><i class="bi bi-box"></i> {{ $stats['stok_habis'] ?? 0 }} habis · {{ $stats['stok_menipis'] ?? 0 }} menipis.</div>
        </a>
    </section>

    <div class="main-grid">
        <div class="stack">
            <section class="dash-shell-card">
                <div class="dash-card-head">
                    <div>
                        <h2 class="dash-card-title">Penjualan Harian</h2>
                        <p class="dash-card-subtitle">{{ $periodeLabel }}</p>
                    </div>
                    <a href="{{ route('admin.laporan.index') }}" class="small-link"><i class="bi bi-bar-chart"></i> Laporan</a>
                </div>
                @php
                    $chartRows = $penjualanHarian->values();
                    $chartCount = max($chartRows->count(), 1);
                    $svgWidth = max(760, $chartCount * 54);
                    $svgHeight = 270;
                    $padLeft = 42;
                    $padRight = 28;
                    $padTop = 34;
                    $padBottom = 42;
                    $plotWidth = max(1, $svgWidth - $padLeft - $padRight);
                    $plotHeight = max(1, $svgHeight - $padTop - $padBottom);
                    $dashboardPoints = $chartRows->map(function ($item, $index) use ($chartCount, $plotWidth, $plotHeight, $padLeft, $padTop, $maxPenjualan, $rupiah) {
                        $total = (float) ($item['total'] ?? 0);
                        $x = $padLeft + ($chartCount > 1 ? (($plotWidth / ($chartCount - 1)) * $index) : ($plotWidth / 2));
                        $y = $padTop + ($plotHeight - (($total / max((float) $maxPenjualan, 1)) * $plotHeight));
                        return [
                            'x' => round($x, 2),
                            'y' => round($y, 2),
                            'label' => $item['label'],
                            'value' => $total,
                            'money' => $rupiah($total),
                        ];
                    });
                    $linePath = $dashboardPoints->map(fn ($point, $index) => ($index === 0 ? 'M' : 'L') . $point['x'] . ' ' . $point['y'])->implode(' ');
                    $areaPath = $dashboardPoints->isNotEmpty()
                        ? $linePath . ' L ' . $dashboardPoints->last()['x'] . ' ' . ($svgHeight - $padBottom) . ' L ' . $dashboardPoints->first()['x'] . ' ' . ($svgHeight - $padBottom) . ' Z'
                        : '';
                    $labelEvery = max(1, (int) ceil($chartCount / 12));
                    $valueEvery = max(1, (int) ceil($chartCount / 10));
                @endphp
                <div class="chart-area">
                    <div class="line-chart-box js-line-chart">
                        <svg class="line-chart-svg" viewBox="0 0 {{ $svgWidth }} {{ $svgHeight }}" role="img" aria-label="Grafik garis penjualan harian">
                            @for($i = 0; $i <= 4; $i++)
                                @php $gridY = $padTop + (($plotHeight / 4) * $i); @endphp
                                <line class="line-chart-grid" x1="{{ $padLeft }}" y1="{{ $gridY }}" x2="{{ $svgWidth - $padRight }}" y2="{{ $gridY }}"></line>
                            @endfor
                            <line class="line-chart-axis" x1="{{ $padLeft }}" y1="{{ $svgHeight - $padBottom }}" x2="{{ $svgWidth - $padRight }}" y2="{{ $svgHeight - $padBottom }}"></line>
                            @if($areaPath)
                                <path class="line-chart-area" d="{{ $areaPath }}"></path>
                                <path class="line-chart-path" d="{{ $linePath }}"></path>
                            @endif
                            @foreach($dashboardPoints as $index => $point)
                                <g class="line-chart-point-wrap" data-label="{{ $point['label'] }}" data-value="{{ $point['money'] }}">
                                    @if($point['value'] > 0 && $index % $valueEvery === 0)
                                        <text class="line-chart-value" x="{{ $point['x'] }}" y="{{ max(12, $point['y'] - 13) }}">{{ $point['money'] }}</text>
                                    @endif
                                    <circle class="line-chart-point" cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="5"></circle>
                                    @if($index % $labelEvery === 0 || $index === $chartCount - 1)
                                        <text class="line-chart-label" x="{{ $point['x'] }}" y="{{ $svgHeight - 17 }}">{{ $point['label'] }}</text>
                                    @endif
                                </g>
                            @endforeach
                        </svg>
                    </div>
                </div>
            </section>

            <section class="dash-shell-card">
                <div class="dash-card-head">
                    <div>
                        <h2 class="dash-card-title">Pesanan Terbaru</h2>
                        <p class="dash-card-subtitle">Daftar terbaru.</p>
                    </div>
                    <a href="{{ route('admin.semua-pesanan.index') }}" class="small-link"><i class="bi bi-arrow-right"></i> Semua Pesanan</a>
                </div>
                <div class="list-wrap">
                    @forelse($pesananTerbaru as $order)
                        @php
                            $produkPreview = $order->item->first()?->produk;
                            $image = $produkPreview?->gambarUtama?->url_gambar;
                        @endphp
                        <a href="{{ route('admin.pesanan.show', $order) }}" class="list-row">
                            <div class="row-thumb">
                                @if($image)
                                    <img src="{{ asset('storage/' . $image) }}" alt="{{ $produkPreview?->nama }}">
                                @else
                                    <i class="bi bi-receipt"></i>
                                @endif
                            </div>
                            <div class="row-main">
                                <div class="row-title">{{ $order->nomor_invoice }}</div>
                                <div class="row-sub">
                                    {{ $order->user?->name ?? 'Pembeli' }} · {{ $order->tanggal_pesanan?->format('d/m/Y H:i') }}
                                    @if($produkPreview) · {{ $produkPreview->nama }} @endif
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="row-price mb-1">{{ $rupiah($order->total_bayar ?? 0) }}</div>
                                <span class="badge-soft {{ $statusClass[$order->status] ?? 'bg-secondary-subtle text-secondary-emphasis' }}">
                                    {{ $statusLabel[$order->status] ?? ucwords(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </div>
                        </a>
                    @empty
                        <div class="empty-state"><i class="bi bi-inbox"></i>Belum ada pesanan terbaru.</div>
                    @endforelse
                </div>
            </section>
        </div>

        <aside class="stack">
            <section class="dash-shell-card">
                <div class="dash-card-head">
                    <div>
                        <h2 class="dash-card-title">Ringkasan Bisnis</h2>
                        <p class="dash-card-subtitle">Indikator toko.</p>
                    </div>
                </div>
                <div class="kpi-grid">
                    <div class="kpi-card">
                        <div class="kpi-label">Penjualan Hari Ini</div>
                        <div class="kpi-value">{{ $rupiah($stats['penjualan_hari_ini'] ?? 0) }}</div>
                        <div class="kpi-note {{ $selisihPenjualan >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $selisihPenjualan >= 0 ? '+' : '-' }}{{ $rupiah(abs($selisihPenjualan)) }} dari kemarin
                        </div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-label">Pesanan Hari Ini</div>
                        <div class="kpi-value">{{ $stats['pesanan_hari_ini'] ?? 0 }}</div>
                        <div class="kpi-note {{ $selisihPesanan >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $selisihPesanan >= 0 ? '+' : '' }}{{ $selisihPesanan }} dari kemarin
                        </div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-label">Produk Aktif</div>
                        <div class="kpi-value">{{ $stats['produk_aktif'] ?? 0 }}</div>
                        <div class="kpi-note">{{ $stats['produk_nonaktif'] ?? 0 }} produk nonaktif.</div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-label">Rating Toko</div>
                        <div class="kpi-value">{{ number_format($stats['rating_rata_rata'] ?? 0, 1, ',', '.') }} / 5</div>
                        <div class="kpi-note">{{ $stats['total_ulasan'] ?? 0 }} ulasan pembeli.</div>
                    </div>
                </div>
            </section>

            <section class="dash-shell-card">
                <div class="dash-card-head">
                    <div>
                        <h2 class="dash-card-title">Tindakan Cepat</h2>
                        <p class="dash-card-subtitle">Akses cepat operasional.</p>
                    </div>
                </div>
                <div class="quick-action-grid">
                    @foreach($prioritas as $item)
                        <a href="{{ $item['route'] }}" class="quick-action">
                            <div class="quick-icon"><i class="bi {{ $item['icon'] }}"></i></div>
                            <div>
                                <div class="quick-title">{{ $item['label'] }}</div>
                                <div class="quick-desc">{{ $item['desc'] }}</div>
                            </div>
                            <div class="quick-count">{{ $item['value'] }}</div>
                        </a>
                    @endforeach
                    <a href="{{ route('admin.pengaturan.edit') }}" class="quick-action">
                        <div class="quick-icon"><i class="bi bi-gear"></i></div>
                        <div>
                            <div class="quick-title">Pengaturan Toko</div>
                            <div class="quick-desc">Atur rekening, pengiriman, dan informasi toko.</div>
                        </div>
                        <div class="quick-count"><i class="bi bi-arrow-right"></i></div>
                    </a>
                </div>
            </section>
        </aside>
    </div>

    <div class="two-column">
        <section class="dash-shell-card">
            <div class="dash-card-head">
                <div>
                    <h2 class="dash-card-title">Status Pesanan</h2>
                    <p class="dash-card-subtitle">Status operasional pesanan.</p>
                </div>
                <a href="{{ route('admin.pesanan.index') }}" class="small-link"><i class="bi bi-receipt"></i> Pesanan</a>
            </div>
            <div class="ops-grid">
                @foreach($statusOperasional as $item)
                    @php $width = max(4, round(($item['value'] / $maxStatus) * 100)); @endphp
                    <div class="ops-row">
                        <div class="ops-icon"><i class="bi {{ $item['icon'] }}"></i></div>
                        <div>
                            <div class="ops-label">{{ $item['label'] }}</div>
                            <div class="ops-progress"><span style="width: {{ $width }}%;"></span></div>
                        </div>
                        <strong>{{ $item['value'] }}</strong>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="dash-shell-card">
            <div class="dash-card-head">
                <div>
                    <h2 class="dash-card-title">Pembayaran Perlu Dicek</h2>
                    <p class="dash-card-subtitle">Bukti transfer yang perlu diverifikasi.</p>
                </div>
                <a href="{{ route('admin.pembayaran.index') }}" class="small-link"><i class="bi bi-arrow-right"></i> Buka</a>
            </div>
            <div class="list-wrap">
                @forelse($pembayaranPerluDicek as $pembayaran)
                    <a href="{{ route('admin.pembayaran.index') }}" class="list-row">
                        <div class="row-icon"><i class="bi bi-bank"></i></div>
                        <div class="row-main">
                            <div class="row-title">{{ $pembayaran->pesanan?->nomor_invoice ?? 'Invoice' }}</div>
                            <div class="row-sub">{{ $pembayaran->pesanan?->user?->name ?? 'Pembeli' }} · {{ $pembayaran->created_at?->format('d/m/Y H:i') }}</div>
                        </div>
                        <div class="row-price">{{ $rupiah($pembayaran->jumlah ?? 0) }}</div>
                    </a>
                @empty
                    <div class="empty-state"><i class="bi bi-check2-circle"></i>Tidak ada bukti transfer yang menunggu verifikasi.</div>
                @endforelse
            </div>
        </section>
    </div>

    <div class="two-column">
        <section class="dash-shell-card">
            <div class="dash-card-head">
                <div>
                    <h2 class="dash-card-title">Produk Terlaris</h2>
                    <p class="dash-card-subtitle">Produk dengan penjualan tertinggi.</p>
                </div>
                <a href="{{ route('admin.produk.index') }}" class="small-link"><i class="bi bi-box-seam"></i> Produk</a>
            </div>
            <div class="list-wrap">
                @forelse($produkTerlaris as $index => $produk)
                    @php $image = $produk->gambarUtama?->url_gambar; @endphp
                    <a href="{{ route('admin.produk.edit', $produk) }}" class="list-row">
                        <div class="row-thumb">
                            @if($image)
                                <img src="{{ asset('storage/' . $image) }}" alt="{{ $produk->nama }}">
                            @else
                                <span>{{ $index + 1 }}</span>
                            @endif
                        </div>
                        <div class="row-main">
                            <div class="row-title">{{ $produk->nama }}</div>
                            <div class="row-sub">Terjual {{ (int) ($produk->total_terjual ?? 0) }} item · Stok {{ $produk->stok }} {{ $produk->satuan ?? 'pcs' }}</div>
                        </div>
                        <div class="row-price">{{ $rupiah($produk->harga ?? 0) }}</div>
                    </a>
                @empty
                    <div class="empty-state"><i class="bi bi-graph-up"></i>Belum ada produk terjual pada periode ini.</div>
                @endforelse
            </div>
        </section>

        <section class="dash-shell-card">
            <div class="dash-card-head">
                <div>
                    <h2 class="dash-card-title">Stok Perlu Tindakan</h2>
                    <p class="dash-card-subtitle">Stok membutuhkan perhatian.</p>
                </div>
                <a href="{{ route('admin.stok.index') }}" class="small-link"><i class="bi bi-box-seam"></i> Stok</a>
            </div>
            <div class="list-wrap">
                @forelse($stokPerhatian as $produk)
                    <a href="{{ route('admin.stok.index') }}" class="list-row">
                        <div class="stock-count">{{ $produk->stok }}</div>
                        <div class="row-main">
                            <div class="row-title">{{ $produk->nama }}</div>
                            <div class="row-sub">Minimum {{ $produk->min_stok ?? 0 }} · {{ $produk->satuan ?? 'satuan' }}</div>
                        </div>
                        <div class="text-end">
                            @if($produk->stok <= 0)
                                <span class="badge-soft bg-danger-subtle text-danger-emphasis">Habis</span>
                            @else
                                <span class="badge-soft bg-warning-subtle text-warning-emphasis">Menipis</span>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="empty-state"><i class="bi bi-check2-circle"></i>Stok masih aman.</div>
                @endforelse
            </div>
        </section>
    </div>

    <div class="two-column">
        <section class="dash-shell-card">
            <div class="dash-card-head">
                <div>
                    <h2 class="dash-card-title">Metode Pembayaran</h2>
                    <p class="dash-card-subtitle">Komposisi transaksi transfer bank dan COD.</p>
                </div>
            </div>
            <div class="ops-grid">
                @foreach($metodePembayaran as $metode)
                    @php $persen = round(($metode['value'] / $totalMetodePembayaran) * 100); @endphp
                    <div class="ops-row">
                        <div class="ops-icon"><i class="bi {{ $metode['icon'] }}"></i></div>
                        <div>
                            <div class="ops-label">{{ $metode['label'] }}</div>
                            <div class="ops-progress"><span style="width: {{ max(4, $persen) }}%;"></span></div>
                        </div>
                        <strong>{{ $persen }}%</strong>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="dash-shell-card">
            <div class="dash-card-head">
                <div>
                    <h2 class="dash-card-title">Ulasan Terbaru</h2>
                    <p class="dash-card-subtitle">Masukan terbaru dari pembeli.</p>
                </div>
                <a href="{{ route('admin.ulasan.index') }}" class="small-link"><i class="bi bi-arrow-right"></i> Semua</a>
            </div>
            <div class="list-wrap">
                @forelse($ulasanTerbaru as $ulasan)
                    @php $image = $ulasan->produk?->gambarUtama?->url_gambar; @endphp
                    <a href="{{ route('admin.ulasan.index') }}" class="list-row">
                        <div class="row-thumb">
                            @if($image)
                                <img src="{{ asset('storage/' . $image) }}" alt="{{ $ulasan->produk?->nama }}">
                            @else
                                <i class="bi bi-star"></i>
                            @endif
                        </div>
                        <div class="row-main">
                            <div class="row-title">{{ $ulasan->produk?->nama ?? 'Produk' }}</div>
                            <div class="row-sub">
                                {{ $ulasan->user?->name ?? 'Pembeli' }} ·
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi {{ $i <= (int) $ulasan->rating ? 'bi-star-fill text-warning' : 'bi-star text-muted' }}"></i>
                                @endfor
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="empty-state"><i class="bi bi-chat-square-heart"></i>Belum ada ulasan terbaru.</div>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tooltip = document.createElement('div');
        tooltip.className = 'line-chart-tooltip';
        document.body.appendChild(tooltip);

        document.querySelectorAll('.line-chart-point-wrap').forEach((point) => {
            point.addEventListener('mousemove', (event) => {
                tooltip.innerHTML = `<strong>${point.dataset.value || 'Rp 0'}</strong><br><span>${point.dataset.label || ''}</span>`;
                tooltip.style.left = event.clientX + 'px';
                tooltip.style.top = event.clientY + 'px';
                tooltip.classList.add('show');
            });
            point.addEventListener('mouseleave', () => tooltip.classList.remove('show'));
        });
    });
</script>
@endpush
