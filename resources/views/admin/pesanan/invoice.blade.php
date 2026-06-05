<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $pesanan->nomor_invoice }} - SiTahu</title>

    <style>
        :root {
            --brand: #dfba68;
            --brand-dark: #8a6321;
            --text: #111827;
            --muted: #6b7280;
            --line: #e5e7eb;
            --soft: #fff8e8;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 24px;
            background: #f6f7fb;
            color: var(--text);
            font-family: Arial, Helvetica, sans-serif;
        }

        .invoice-page {
            max-width: 920px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 18px;
            border: 1px solid var(--line);
            overflow: hidden;
            box-shadow: 0 18px 40px rgba(17, 24, 39, 0.08);
        }

        .invoice-header {
            padding: 28px;
            display: flex;
            justify-content: space-between;
            gap: 24px;
            align-items: flex-start;
            background:
                radial-gradient(circle at top right, rgba(223, 186, 104, 0.28), transparent 34%),
                linear-gradient(135deg, #ffffff 0%, #fff8e8 100%);
            border-bottom: 1px solid var(--line);
        }

        .brand {
            display: flex;
            gap: 14px;
            align-items: center;
        }

        .brand-logo {
            width: 54px;
            height: 54px;
            border-radius: 18px;
            background: linear-gradient(135deg, var(--brand), #c89335);
            color: #ffffff;
            display: grid;
            place-items: center;
            font-size: 20px;
            font-weight: 900;
            letter-spacing: -0.05em;
        }

        .brand h1 {
            margin: 0;
            font-size: 25px;
            letter-spacing: -0.06em;
        }

        .brand p {
            margin: 5px 0 0;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.5;
        }

        .invoice-title {
            text-align: right;
        }

        .invoice-title h2 {
            margin: 0;
            font-size: 28px;
            letter-spacing: -0.06em;
        }

        .invoice-title p {
            margin: 6px 0 0;
            color: var(--brand-dark);
            font-size: 14px;
            font-weight: 800;
        }

        .invoice-body {
            padding: 28px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            margin-bottom: 24px;
        }

        .info-box {
            padding: 18px;
            border: 1px solid var(--line);
            border-radius: 16px;
            background: #ffffff;
        }

        .info-box h3 {
            margin: 0 0 12px;
            font-size: 14px;
            color: var(--brand-dark);
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 8px 0;
            border-bottom: 1px dashed var(--line);
            font-size: 13px;
        }

        .info-row:last-child {
            border-bottom: 0;
        }

        .info-row span:first-child {
            color: var(--muted);
        }

        .info-row strong {
            text-align: right;
        }

        .status-pill {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            background: var(--soft);
            color: var(--brand-dark);
            font-size: 12px;
            font-weight: 800;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 14px;
        }

        thead {
            background: #f9fafb;
        }

        th {
            padding: 13px 12px;
            color: var(--muted);
            font-size: 12px;
            text-align: left;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            border-bottom: 1px solid var(--line);
        }

        td {
            padding: 13px 12px;
            border-bottom: 1px solid var(--line);
            font-size: 13px;
            vertical-align: top;
        }

        tbody tr:last-child td {
            border-bottom: 0;
        }

        .text-end {
            text-align: right;
        }

        .product-name {
            font-weight: 800;
            color: var(--text);
        }

        .product-note {
            margin-top: 4px;
            color: var(--muted);
            font-size: 12px;
        }

        .summary {
            width: min(100%, 360px);
            margin-left: auto;
            margin-top: 22px;
            border: 1px solid var(--line);
            border-radius: 16px;
            overflow: hidden;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 14px;
            border-bottom: 1px dashed var(--line);
            font-size: 13px;
        }

        .summary-row:last-child {
            border-bottom: 0;
        }

        .summary-total {
            background: var(--soft);
            color: var(--brand-dark);
            font-weight: 900;
            font-size: 15px;
        }

        .footer-note {
            margin-top: 26px;
            padding: 16px;
            border-radius: 16px;
            background: #f9fafb;
            color: var(--muted);
            font-size: 12.5px;
            line-height: 1.6;
        }

        .print-actions {
            max-width: 920px;
            margin: 0 auto 14px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn {
            min-height: 40px;
            padding: 10px 15px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: #ffffff;
            color: var(--text);
            font-weight: 800;
            cursor: pointer;
            text-decoration: none;
            font-size: 13px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--brand), #c89335);
            border-color: transparent;
            color: #ffffff;
        }

        @media print {
            body {
                padding: 0;
                background: #ffffff;
            }

            .print-actions {
                display: none;
            }

            .invoice-page {
                max-width: none;
                margin: 0;
                border-radius: 0;
                border: 0;
                box-shadow: none;
            }
        }

        @media (max-width: 720px) {
            body {
                padding: 14px;
            }

            .invoice-header,
            .info-grid {
                grid-template-columns: 1fr;
                display: grid;
            }

            .invoice-title {
                text-align: left;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
@php
    $alamatTujuan = $pesanan->alamatPengiriman?->alamat_lengkap
        ?? $pesanan->pengiriman?->alamat_tujuan
        ?? 'Ambil di toko';

    $namaPembeli = $pesanan->user?->name ?? 'Pembeli';

    $formatStatus = function ($value) {
        return ucwords(str_replace('_', ' ', (string) $value));
    };
@endphp

<div class="print-actions">
    <a href="{{ route('admin.pesanan.show', $pesanan) }}" class="btn">
        Kembali
    </a>

    <button onclick="window.print()" class="btn btn-primary">
        Cetak Invoice
    </button>
</div>

<section class="invoice-page">
    <header class="invoice-header">
        <div class="brand">
            <div class="brand-logo">ST</div>

            <div>
                <h1>SiTahu</h1>
                <p>
                    Aplikasi penjualan produk tahu berbasis web admin dan pembeli.
                    Invoice ini dicetak dari dashboard admin.
                </p>
            </div>
        </div>

        <div class="invoice-title">
            <h2>INVOICE</h2>
            <p>{{ $pesanan->nomor_invoice }}</p>
        </div>
    </header>

    <main class="invoice-body">
        <div class="info-grid">
            <div class="info-box">
                <h3>Informasi Pesanan</h3>

                <div class="info-row">
                    <span>No. Invoice</span>
                    <strong>{{ $pesanan->nomor_invoice }}</strong>
                </div>

                <div class="info-row">
                    <span>Tanggal</span>
                    <strong>{{ optional($pesanan->tanggal_pesanan)->format('d/m/Y H:i') ?? '-' }}</strong>
                </div>

                <div class="info-row">
                    <span>Status Pesanan</span>
                    <strong>
                        <span class="status-pill">
                            {{ $formatStatus($pesanan->status) }}
                        </span>
                    </strong>
                </div>

                <div class="info-row">
                    <span>Status Pembayaran</span>
                    <strong>
                        <span class="status-pill">
                            {{ $formatStatus($pesanan->status_pembayaran) }}
                        </span>
                    </strong>
                </div>

                <div class="info-row">
                    <span>Metode Bayar</span>
                    <strong>{{ strtoupper($pesanan->pembayaran?->metode_pembayaran ?? '-') }}</strong>
                </div>
            </div>

            <div class="info-box">
                <h3>Informasi Pembeli</h3>

                <div class="info-row">
                    <span>Nama</span>
                    <strong>{{ $namaPembeli }}</strong>
                </div>

                <div class="info-row">
                    <span>Email</span>
                    <strong>{{ $pesanan->user?->email ?? '-' }}</strong>
                </div>

                <div class="info-row">
                    <span>Telepon</span>
                    <strong>{{ $pesanan->user?->telepon ?? '-' }}</strong>
                </div>

                <div class="info-row">
                    <span>Penerimaan</span>
                    <strong>{{ $formatStatus($pesanan->metode_pengambilan) }}</strong>
                </div>

                <div class="info-row">
                    <span>Alamat</span>
                    <strong>{{ $alamatTujuan }}</strong>
                </div>
            </div>
        </div>

        <h3 style="margin: 0 0 10px; font-size: 15px;">
            Detail Produk
        </h3>

        <table>
            <thead>
            <tr>
                <th>Produk</th>
                <th class="text-end">Qty</th>
                <th class="text-end">Harga</th>
                <th class="text-end">Subtotal</th>
            </tr>
            </thead>

            <tbody>
            @forelse($pesanan->item as $item)
                <tr>
                    <td>
                        <div class="product-name">
                            {{ $item->produk?->nama ?? '-' }}
                        </div>
                        <div class="product-note">
                            Produk ID: {{ $item->produk_id }}
                        </div>
                    </td>

                    <td class="text-end">
                        {{ $item->jumlah }}
                    </td>

                    <td class="text-end">
                        {{ $rupiah($item->harga_satuan ?? 0) }}
                    </td>

                    <td class="text-end">
                        <strong>{{ $rupiah($item->subtotal ?? 0) }}</strong>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align:center;color:var(--muted);">
                        Tidak ada item pesanan.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="summary">
            <div class="summary-row">
                <span>Subtotal Produk</span>
                <strong>{{ $rupiah($pesanan->subtotal_produk ?? 0) }}</strong>
            </div>

            <div class="summary-row">
                <span>Biaya Pengiriman</span>
                <strong>{{ $rupiah($pesanan->biaya_pengiriman ?? 0) }}</strong>
            </div>

            <div class="summary-row summary-total">
                <span>Total Bayar</span>
                <strong>{{ $rupiah($pesanan->total_bayar ?? 0) }}</strong>
            </div>
        </div>

        <div class="footer-note">
            Invoice ini digunakan sebagai bukti ringkasan pesanan. Status pembayaran dan pengiriman mengikuti data terakhir
            yang diperbarui oleh admin pada sistem SiTahu.
        </div>
    </main>
</section>
</body>
</html>