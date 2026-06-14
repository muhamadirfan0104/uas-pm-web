@extends('layouts.admin')

@section('title', 'Laporan - SiTahu')
@section('page_title', 'Laporan')
@section('content')
@php
    $statusLabel = fn ($value) => ucwords(str_replace('_', ' ', (string) $value));
    $money = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
    $periodeText = $tanggalMulai->format('d M Y') . ' - ' . $tanggalSelesai->format('d M Y');
    $maxPendapatan = max((float) $laporanHarian->max('pendapatan'), 1);
    $jenisMeta = [
        'penjualan' => ['label' => 'Penjualan', 'icon' => 'bi-graph-up-arrow', 'desc' => 'Laporan transaksi.'],
        'pembeli' => ['label' => 'Akun Pembeli', 'icon' => 'bi-people', 'desc' => 'Data pelanggan.'],
        'produk' => ['label' => 'Produk', 'icon' => 'bi-basket2', 'desc' => 'Data produk.'],
        'pembayaran' => ['label' => 'Pembayaran', 'icon' => 'bi-credit-card', 'desc' => 'Data pembayaran.'],
        'stok' => ['label' => 'Stok', 'icon' => 'bi-box-seam', 'desc' => 'Mutasi stok.'],
    ];
    $statusClass = [
        'menunggu_pembayaran' => 'c-yellow', 'menunggu_verifikasi' => 'c-yellow',
        'diproses' => 'c-blue', 'disiapkan' => 'c-purple', 'siap_diambil' => 'c-green', 'dalam_pengantaran' => 'c-blue',
        'selesai' => 'c-green', 'dibatalkan' => 'c-red', 'dibayar' => 'c-green', 'ditolak' => 'c-red', 'gagal' => 'c-red',
    ];
@endphp

@push('styles')
<style>
    .report-hero { display:grid; grid-template-columns:minmax(0,1fr) 420px; gap:16px; margin-bottom:16px; }
    .report-panel { border:1px solid var(--border); background:#fff; border-radius:24px; box-shadow:var(--shadow-soft); }
    .report-intro { padding:22px; border-color:#f1d49c; background:radial-gradient(circle at 90% 0%,rgba(200,147,53,.16),transparent 18rem),linear-gradient(135deg,#fff,#fff8ea); }
    .report-intro h1 { margin:0; font-size:1.45rem; font-weight:950; letter-spacing:-.05em; }
    .report-intro p { margin:8px 0 0; max-width:720px; color:var(--muted); font-size:.85rem; font-weight:650; line-height:1.55; }
    .report-tabs { display:flex; flex-wrap:wrap; gap:8px; margin-top:18px; }
    .report-tab { display:inline-flex; align-items:center; gap:8px; padding:9px 12px; border:1px solid var(--border); border-radius:999px; background:#fff; color:var(--text); text-decoration:none; font-size:.78rem; font-weight:950; }
    .report-tab.active { background:var(--brand); color:#fff; border-color:var(--brand); }
    .report-filter { padding:16px; }
    .report-filter-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px; }
    .report-filter label { margin-bottom:6px; color:var(--muted); font-size:.68rem; font-weight:950; text-transform:uppercase; letter-spacing:.06em; }
    .metric { min-height:104px; padding:16px; border:1px solid var(--border); background:#fff; border-radius:20px; box-shadow:var(--shadow-soft); display:flex; justify-content:space-between; gap:12px; }
    .metric .label { color:var(--muted); font-size:.7rem; font-weight:950; letter-spacing:.05em; text-transform:uppercase; }
    .metric .value { margin-top:7px; color:var(--text); font-size:1.3rem; font-weight:950; letter-spacing:-.04em; line-height:1.1; }
    .metric .value.money { font-size:1.02rem; line-height:1.28; }
    .metric .note { margin-top:5px; color:var(--muted); font-size:.73rem; font-weight:750; }
    .metric .icon { width:42px; height:42px; display:grid; place-items:center; border-radius:15px; background:var(--brand-soft); color:var(--brand-dark); flex-shrink:0; }
    .report-card { border:1px solid var(--border); background:#fff; border-radius:22px; box-shadow:var(--shadow-soft); overflow:hidden; }
    .report-card-head { padding:15px 18px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; gap:12px; }
    .report-card-head h2 { margin:0; font-size:.98rem; font-weight:950; letter-spacing:-.035em; }
    .report-card-head p { margin:4px 0 0; color:var(--muted); font-size:.76rem; font-weight:650; }
    .report-line-box {
        position: relative;
        min-height: 262px;
        overflow-x: auto;
        padding: 12px;
        background:
            linear-gradient(180deg, rgba(255,248,234,.7), rgba(255,255,255,.92)),
            repeating-linear-gradient(to bottom, transparent 0, transparent 49px, rgba(148,163,184,.18) 50px);
    }
    .report-line-svg { min-width:720px; width:100%; height:250px; display:block; }
    .report-line-grid { stroke:rgba(148,163,184,.20); stroke-width:1; }
    .report-line-axis { stroke:rgba(102,112,133,.25); stroke-width:1; }
    .report-line-area { fill:rgba(200,147,53,.13); }
    .report-line-path { fill:none; stroke:var(--brand); stroke-width:4; stroke-linecap:round; stroke-linejoin:round; filter:drop-shadow(0 10px 14px rgba(200,147,53,.18)); }
    .report-line-point { fill:#fff; stroke:var(--brand); stroke-width:4; cursor:pointer; transition:.15s ease; }
    .report-line-point-wrap:hover .report-line-point { r:7; fill:var(--brand); }
    .report-line-value { fill:#7a5618; font-size:10px; font-weight:900; text-anchor:middle; pointer-events:none; }
    .report-line-label { fill:#98a2b3; font-size:10px; font-weight:850; text-anchor:middle; }
    .report-line-tooltip {
        position:fixed;
        z-index:2000;
        padding:8px 10px;
        border-radius:12px;
        background:#111827;
        color:#fff;
        font-size:.72rem;
        line-height:1.35;
        font-weight:850;
        box-shadow:0 14px 35px rgba(17,24,39,.24);
        opacity:0;
        pointer-events:none;
        transform:translate(-50%,-115%);
        white-space:nowrap;
        transition:opacity .12s ease;
    }
    .report-line-tooltip.show { opacity:1; }
    .mini-list { display:grid; gap:10px; padding:15px 18px 18px; }
    .mini-row { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:12px; border:1px solid #eef0f3; border-radius:16px; background:#fff; }
    .mini-row .title { font-weight:950; color:var(--text); font-size:.86rem; }
    .mini-row .subtext { margin-top:3px; color:var(--muted); font-size:.74rem; font-weight:700; }
    .rank-cover { width:42px; height:42px; border-radius:14px; border:1px solid var(--border); object-fit:cover; background:var(--brand-soft); }
    .table-sub { display:block; margin-top:3px; color:var(--muted); font-size:.74rem; font-weight:700; }
    .empty-soft { padding:28px; text-align:center; color:var(--muted); font-weight:800; }
    .two-col { display:grid; grid-template-columns:minmax(0,1.35fr) minmax(330px,.65fr); gap:16px; }
    @media(max-width:1100px){ .report-hero,.two-col{grid-template-columns:1fr;} }
    @media(max-width:760px){ .report-filter-grid{grid-template-columns:1fr;} }
</style>
@endpush

<div class="report-hero">
    <section class="report-panel report-intro">
        <span class="chip c-yellow mb-2"><i class="bi {{ $jenisMeta[$jenis]['icon'] }} me-1"></i>{{ $jenisMeta[$jenis]['label'] }}</span>
        <h1>Laporan {{ strtolower($jenisMeta[$jenis]['label']) }}</h1>
        <p>{{ $jenisMeta[$jenis]['desc'] }} Periode aktif: <strong>{{ $periodeText }}</strong>.</p>
        <div class="report-tabs">
            @foreach($jenisMeta as $key => $meta)
                <a class="report-tab {{ $jenis === $key ? 'active' : '' }}" href="{{ route('admin.laporan.index', array_merge(request()->except('page'), ['jenis' => $key])) }}"><i class="bi {{ $meta['icon'] }}"></i>{{ $meta['label'] }}</a>
            @endforeach
        </div>
    </section>

    <form class="report-panel report-filter" method="GET" action="{{ route('admin.laporan.index') }}">
        <input type="hidden" name="jenis" value="{{ $jenis }}">
        <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
            <div><div class="fw-black text-dark">Filter laporan</div><div class="text-muted small fw-semibold">Periode, pencarian, dan status.</div></div>
            <a href="{{ route('admin.laporan.index', ['jenis' => $jenis]) }}" class="small-btn"><i class="bi bi-arrow-clockwise"></i> Reset</a>
        </div>
        <div class="report-filter-grid">
            <div><label>Tanggal mulai</label><input type="date" name="tanggal_mulai" class="form-control" value="{{ request('tanggal_mulai', $tanggalMulai->format('Y-m-d')) }}"></div>
            <div><label>Tanggal selesai</label><input type="date" name="tanggal_selesai" class="form-control" value="{{ request('tanggal_selesai', $tanggalSelesai->format('Y-m-d')) }}"></div>
            <div style="grid-column:1 / -1;"><label>Pencarian</label><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Invoice, pembeli, produk, referensi..."></div>
            <div><label>Status pesanan</label><select name="status" class="form-select"><option value="">Semua</option>@foreach(['menunggu_pembayaran','menunggu_verifikasi','diproses','disiapkan','siap_diambil','dalam_pengantaran','selesai','dibatalkan'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ $statusLabel($status) }}</option>@endforeach</select></div>
            <div><label>Metode bayar</label><select name="metode_pembayaran" class="form-select"><option value="">Semua</option><option value="transfer_bank" @selected(request('metode_pembayaran') === 'transfer_bank')>Transfer Bank</option><option value="cod" @selected(request('metode_pembayaran') === 'cod')>COD</option></select></div>
        </div>
        <div class="d-flex flex-wrap gap-2 mt-3">
            <button class="btn btn-brand flex-fill" type="submit"><i class="bi bi-funnel me-1"></i>Terapkan</button>
            <a class="btn btn-light border flex-fill" href="{{ route('admin.laporan.export.excel', request()->query()) }}"><i class="bi bi-file-earmark-spreadsheet me-1"></i>Excel</a>
            <a class="btn btn-light border flex-fill" href="{{ route('admin.laporan.export.pdf', request()->query()) }}" target="_blank" rel="noopener"><i class="bi bi-file-earmark-pdf me-1"></i>PDF</a>
        </div>
    </form>
</div>

<div class="grid g4 mb-3">
    <div class="metric"><div><div class="label">Pendapatan</div><div class="value money">{{ $money($stats['pendapatan']) }}</div><div class="note">Pembayaran berhasil</div></div><span class="icon"><i class="bi bi-cash-stack"></i></span></div>
    <div class="metric"><div><div class="label">Pesanan</div><div class="value">{{ number_format($stats['pesanan_total']) }}</div><div class="note">{{ number_format($stats['pesanan_selesai']) }} selesai</div></div><span class="icon"><i class="bi bi-receipt-cutoff"></i></span></div>
    <div class="metric"><div><div class="label">Produk terjual</div><div class="value">{{ number_format($stats['produk_terjual']) }}</div><div class="note">Semua invoice non-batal</div></div><span class="icon"><i class="bi bi-basket2-fill"></i></span></div>
    <div class="metric"><div><div class="label">Pembeli aktif</div><div class="value">{{ number_format($pembeliStats['aktif']) }}</div><div class="note">Bertransaksi di periode ini</div></div><span class="icon"><i class="bi bi-people-fill"></i></span></div>
</div>

<div class="two-col mb-3">
    <section class="report-card">
        <div class="report-card-head"><div><h2>Tren Pendapatan</h2><p>{{ $periodeText }}</p></div><span class="chip c-yellow">{{ $periodeText }}</span></div>
        @php
            $reportRows = $laporanHarian->values();
            $reportCount = max($reportRows->count(), 1);
            $reportSvgWidth = max(720, $reportCount * 52);
            $reportSvgHeight = 250;
            $reportPadLeft = 42;
            $reportPadRight = 28;
            $reportPadTop = 34;
            $reportPadBottom = 40;
            $reportPlotWidth = max(1, $reportSvgWidth - $reportPadLeft - $reportPadRight);
            $reportPlotHeight = max(1, $reportSvgHeight - $reportPadTop - $reportPadBottom);
            $reportPoints = $reportRows->map(function ($hari, $index) use ($reportCount, $reportPlotWidth, $reportPlotHeight, $reportPadLeft, $reportPadTop, $maxPendapatan, $money) {
                $total = (float) ($hari['pendapatan'] ?? 0);
                $x = $reportPadLeft + ($reportCount > 1 ? (($reportPlotWidth / ($reportCount - 1)) * $index) : ($reportPlotWidth / 2));
                $y = $reportPadTop + ($reportPlotHeight - (($total / max((float) $maxPendapatan, 1)) * $reportPlotHeight));
                return [
                    'x' => round($x, 2),
                    'y' => round($y, 2),
                    'label' => $hari['tanggal'],
                    'full' => $hari['tanggal_lengkap'] ?? $hari['tanggal'],
                    'value' => $total,
                    'money' => $money($total),
                ];
            });
            $reportLinePath = $reportPoints->map(fn ($point, $index) => ($index === 0 ? 'M' : 'L') . $point['x'] . ' ' . $point['y'])->implode(' ');
            $reportAreaPath = $reportPoints->isNotEmpty()
                ? $reportLinePath . ' L ' . $reportPoints->last()['x'] . ' ' . ($reportSvgHeight - $reportPadBottom) . ' L ' . $reportPoints->first()['x'] . ' ' . ($reportSvgHeight - $reportPadBottom) . ' Z'
                : '';
            $reportLabelEvery = max(1, (int) ceil($reportCount / 10));
            $reportValueEvery = max(1, (int) ceil($reportCount / 8));
        @endphp
        <div class="report-line-box js-report-line-chart">
            <svg class="report-line-svg" viewBox="0 0 {{ $reportSvgWidth }} {{ $reportSvgHeight }}" role="img" aria-label="Grafik garis pendapatan harian">
                @for($i = 0; $i <= 4; $i++)
                    @php $gridY = $reportPadTop + (($reportPlotHeight / 4) * $i); @endphp
                    <line class="report-line-grid" x1="{{ $reportPadLeft }}" y1="{{ $gridY }}" x2="{{ $reportSvgWidth - $reportPadRight }}" y2="{{ $gridY }}"></line>
                @endfor
                <line class="report-line-axis" x1="{{ $reportPadLeft }}" y1="{{ $reportSvgHeight - $reportPadBottom }}" x2="{{ $reportSvgWidth - $reportPadRight }}" y2="{{ $reportSvgHeight - $reportPadBottom }}"></line>
                @if($reportAreaPath)
                    <path class="report-line-area" d="{{ $reportAreaPath }}"></path>
                    <path class="report-line-path" d="{{ $reportLinePath }}"></path>
                @endif
                @foreach($reportPoints as $index => $point)
                    <g class="report-line-point-wrap" data-label="{{ $point['full'] }}" data-value="{{ $point['money'] }}">
                        @if($point['value'] > 0 && $index % $reportValueEvery === 0)
                            <text class="report-line-value" x="{{ $point['x'] }}" y="{{ max(12, $point['y'] - 13) }}">{{ $point['money'] }}</text>
                        @endif
                        <circle class="report-line-point" cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="5"></circle>
                        @if($index % $reportLabelEvery === 0 || $index === $reportCount - 1)
                            <text class="report-line-label" x="{{ $point['x'] }}" y="{{ $reportSvgHeight - 15 }}">{{ $point['label'] }}</text>
                        @endif
                    </g>
                @endforeach
            </svg>
        </div>
    </section>
    <section class="report-card">
        <div class="report-card-head"><div><h2>Ringkasan</h2></div></div>
        <div class="mini-list">
            <div class="mini-row"><div><div class="title">Rata-rata transaksi</div><div class="subtext">Dari pembayaran berhasil</div></div><strong>{{ $money($stats['rata_transaksi']) }}</strong></div>
            <div class="mini-row"><div><div class="title">Pembeli baru</div><div class="subtext">Akun dibuat pada periode</div></div><span class="chip c-blue">{{ number_format($pembeliStats['baru']) }}</span></div>
            <div class="mini-row"><div><div class="title">Rating rata-rata</div><div class="subtext">{{ number_format($ulasanStats['total']) }} ulasan</div></div><span class="chip c-yellow">{{ number_format($ulasanStats['rata'], 1) }} ★</span></div>
            <div class="mini-row"><div><div class="title">Stok butuh tindakan</div><div class="subtext">Menipis + habis</div></div><span class="chip c-red">{{ number_format($produkStats['stok_menipis'] + $produkStats['stok_habis']) }}</span></div>
        </div>
    </section>
</div>

@if($jenis === 'penjualan')
<section class="report-card">
    <div class="report-card-head"><div><h2>Laporan Penjualan</h2></div><span class="chip c-gray">{{ number_format($dataTable->total()) }} data</span></div>
    <div class="table-wrap"><table><thead><tr><th>Invoice</th><th>Pembeli</th><th>Metode</th><th>Status</th><th>Total</th><th>Tanggal</th></tr></thead><tbody>
    @forelse($dataTable as $order)
        <tr><td><strong>{{ $order->nomor_invoice }}</strong><span class="table-sub">{{ $order->item->sum('jumlah') }} item</span></td><td>{{ $order->user?->name ?? '-' }}<span class="table-sub">{{ $order->user?->telepon ?? $order->user?->email ?? '-' }}</span></td><td>{{ $order->pembayaran?->metode_pembayaran === 'cod' ? 'COD' : 'Transfer Bank' }}<span class="table-sub">{{ $order->metode_pengambilan === 'kurir_toko' ? 'Kurir toko' : 'Ambil toko' }}</span></td><td><span class="chip {{ $statusClass[$order->status] ?? 'c-gray' }}">{{ $statusLabel($order->status) }}</span></td><td><strong>{{ $money($order->total_bayar) }}</strong><span class="table-sub">Bayar: {{ $statusLabel($order->pembayaran?->status ?? $order->status_pembayaran) }}</span></td><td>{{ optional($order->tanggal_pesanan)->format('d M Y') }}<span class="table-sub">{{ optional($order->tanggal_pesanan)->format('H:i') }}</span></td></tr>
    @empty<tr><td colspan="6" class="text-center py-5 text-muted fw-bold">Belum ada invoice pada filter ini.</td></tr>@endforelse
    </tbody></table></div>
    @if($dataTable->hasPages())<div class="p-3 border-top">{{ $dataTable->links() }}</div>@endif
</section>
@endif

@if($jenis === 'pembeli')
<section class="report-card">
    <div class="report-card-head"><div><h2>Laporan Akun Pembeli</h2></div><span class="chip c-gray">{{ number_format($dataTable->total()) }} akun</span></div>
    <div class="table-wrap"><table><thead><tr><th>Pembeli</th><th>Kontak</th><th>Pesanan periode</th><th>Total belanja</th><th>Status</th><th>Daftar</th></tr></thead><tbody>
    @forelse($dataTable as $buyer)
        <tr><td><strong>{{ $buyer->name }}</strong><span class="table-sub">ID #{{ str_pad($buyer->id,4,'0',STR_PAD_LEFT) }}</span></td><td>{{ $buyer->email }}<span class="table-sub">{{ $buyer->telepon ?: '-' }}</span></td><td>{{ number_format($buyer->total_pesanan_periode) }}</td><td><strong>{{ $money($buyer->total_belanja_periode ?? 0) }}</strong></td><td><span class="chip {{ $buyer->aktif ? 'c-green' : 'c-gray' }}">{{ $buyer->aktif ? 'Aktif' : 'Nonaktif' }}</span></td><td>{{ optional($buyer->created_at)->format('d M Y') }}</td></tr>
    @empty<tr><td colspan="6" class="text-center py-5 text-muted fw-bold">Belum ada akun pembeli.</td></tr>@endforelse
    </tbody></table></div>
    @if($dataTable->hasPages())<div class="p-3 border-top">{{ $dataTable->links() }}</div>@endif
</section>
@endif

@if($jenis === 'produk')
<div class="two-col">
    <section class="report-card">
        <div class="report-card-head"><div><h2>Laporan produk</h2><p>Performa penjualan dan kondisi stok per produk.</p></div><span class="chip c-gray">{{ number_format($dataTable->total()) }} produk</span></div>
        <div class="table-wrap"><table><thead><tr><th>Produk</th><th>Harga</th><th>Terjual</th><th>Pendapatan</th><th>Stok</th><th>Status</th></tr></thead><tbody>
        @forelse($dataTable as $product)
            <tr><td><div class="d-flex align-items-center gap-2 min-w-0">@if($product->gambarUtama?->url_gambar)<img class="rank-cover" src="{{ asset('storage/' . $product->gambarUtama->url_gambar) }}" alt="{{ $product->nama }}">@endif<div><strong>{{ $product->nama }}</strong><span class="table-sub">{{ $product->satuan }}</span></div></div></td><td>{{ $money($product->harga) }}</td><td>{{ number_format($product->total_terjual ?? 0) }}</td><td><strong>{{ $money($product->total_pendapatan_produk ?? 0) }}</strong></td><td><span class="chip {{ $product->stok <= 0 ? 'c-red' : (($product->stok <= $product->min_stok) ? 'c-yellow' : 'c-green') }}">{{ number_format($product->stok) }}</span></td><td>{{ $product->aktif ? 'Tampil' : 'Disembunyikan' }}</td></tr>
        @empty<tr><td colspan="6" class="text-center py-5 text-muted fw-bold">Belum ada produk.</td></tr>@endforelse
        </tbody></table></div>@if($dataTable->hasPages())<div class="p-3 border-top">{{ $dataTable->links() }}</div>@endif
    </section>
    <section class="report-card"><div class="report-card-head"><div><h2>Produk terlaris</h2><p>Top 10 produk periode ini.</p></div></div><div class="mini-list">@forelse($produkTerlaris as $i => $produk)<div class="mini-row"><div><div class="title">#{{ $i+1 }} {{ $produk->nama }}</div><div class="subtext">{{ number_format($produk->total_terjual) }} terjual</div></div><strong>{{ $money($produk->total_pendapatan_produk ?? 0) }}</strong></div>@empty<div class="empty-soft">Belum ada penjualan produk.</div>@endforelse</div></section>
</div>
@endif

@if($jenis === 'pembayaran')
<section class="report-card">
    <div class="report-card-head"><div><h2>Laporan pembayaran</h2><p>Transfer bank, COD, verifikasi, dan pembayaran berhasil.</p></div><span class="chip c-gray">{{ number_format($dataTable->total()) }} data</span></div>
    <div class="grid g4 p-3 border-bottom"><div class="metric"><div><div class="label">Belum upload</div><div class="value">{{ number_format($pembayaranStats['menunggu_upload']) }}</div></div><span class="icon"><i class="bi bi-hourglass-split"></i></span></div><div class="metric"><div><div class="label">Verifikasi</div><div class="value">{{ number_format($pembayaranStats['menunggu_verifikasi']) }}</div></div><span class="icon"><i class="bi bi-search"></i></span></div><div class="metric"><div><div class="label">Ditolak</div><div class="value">{{ number_format($pembayaranStats['ditolak']) }}</div></div><span class="icon"><i class="bi bi-x-circle"></i></span></div><div class="metric"><div><div class="label">Dibayar</div><div class="value">{{ number_format($pembayaranStats['dibayar']) }}</div></div><span class="icon"><i class="bi bi-check-circle"></i></span></div></div>
    <div class="table-wrap"><table><thead><tr><th>Invoice</th><th>Pembeli</th><th>Metode</th><th>Referensi</th><th>Status</th><th>Jumlah</th></tr></thead><tbody>@forelse($dataTable as $payment)<tr><td><strong>{{ $payment->pesanan?->nomor_invoice }}</strong><span class="table-sub">{{ optional($payment->created_at)->format('d M Y H:i') }}</span></td><td>{{ $payment->pesanan?->user?->name ?? '-' }}</td><td>{{ $payment->metode_pembayaran === 'cod' ? 'COD' : 'Transfer Bank' }}</td><td>{{ $payment->referensi_pembayaran ?: '-' }}</td><td><span class="chip {{ $statusClass[$payment->status] ?? 'c-gray' }}">{{ $statusLabel($payment->status) }}</span></td><td><strong>{{ $money($payment->jumlah) }}</strong></td></tr>@empty<tr><td colspan="6" class="text-center py-5 text-muted fw-bold">Belum ada pembayaran.</td></tr>@endforelse</tbody></table></div>@if($dataTable->hasPages())<div class="p-3 border-top">{{ $dataTable->links() }}</div>@endif
</section>
@endif

@if($jenis === 'stok')
<div class="two-col">
    <section class="report-card">
        <div class="report-card-head">
            <div>
                <h2>Laporan mutasi stok</h2>
                <p>Format mutasi: tambah, kurang, dan sisa stok setelah transaksi.</p>
            </div>
            <span class="chip c-gray">{{ number_format($dataTable->total()) }} mutasi</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Produk</th>
                        <th>Uraian Mutasi</th>
                        <th>Tambah</th>
                        <th>Kurang</th>
                        <th>Sisa</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dataTable as $history)
                        <tr>
                            <td>{{ optional($history->created_at)->format('d M Y H:i') }}</td>
                            <td><strong>{{ $history->produk?->nama ?? '-' }}</strong></td>
                            <td>{{ $history->catatan ?: $statusLabel($history->tipe) }}</td>
                            <td><span class="chip c-green">{{ ((int) ($history->masuk ?? 0)) > 0 ? number_format($history->masuk) : '-' }}</span></td>
                            <td><span class="chip c-red">{{ ((int) ($history->keluar ?? 0)) > 0 ? number_format($history->keluar) : '-' }}</span></td>
                            <td><strong>{{ number_format((int) ($history->sisa_stok ?? 0)) }}</strong></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-5 text-muted fw-bold">Belum ada mutasi stok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($dataTable->hasPages())<div class="p-3 border-top">{{ $dataTable->links() }}</div>@endif
    </section>
    <section class="report-card"><div class="report-card-head"><div><h2>Stok Perhatian</h2></div></div><div class="mini-list">@forelse($produkStokPerhatian as $produk)<div class="mini-row"><div><div class="title">{{ $produk->nama }}</div><div class="subtext">Minimal stok {{ number_format($produk->min_stok) }}</div></div><span class="chip {{ $produk->stok <= 0 ? 'c-red' : 'c-yellow' }}">Stok {{ number_format($produk->stok) }}</span></div>@empty<div class="empty-soft">Stok aman.</div>@endforelse</div></section>
</div>
@endif
@endsection


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tooltip = document.createElement('div');
        tooltip.className = 'report-line-tooltip';
        document.body.appendChild(tooltip);

        document.querySelectorAll('.report-line-point-wrap').forEach((point) => {
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
