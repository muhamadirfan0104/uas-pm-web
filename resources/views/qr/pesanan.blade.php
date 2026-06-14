<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Validasi QR Pesanan - Si Tahu</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: #FFF8E8;
            color: #111827;
            font-family: Arial, sans-serif;
        }

        .page {
            width: 100%;
            max-width: 720px;
            margin: 0 auto;
            padding: 24px 16px;
        }

        .card {
            background: #FFFFFF;
            border: 1px solid #E5E7EB;
            border-radius: 24px;
            padding: 22px;
            box-shadow: 0 14px 35px rgba(92, 59, 24, 0.10);
        }

        .badge {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .badge-valid {
            background: #E3F4E8;
            color: #285234;
        }

        .badge-invalid {
            background: #FDE2E2;
            color: #B42318;
        }

        h1 {
            margin: 0 0 8px;
            font-size: 26px;
            color: #5C3B18;
        }

        p {
            margin: 0;
            line-height: 1.6;
            color: #6B7280;
        }

        .info {
            margin-top: 18px;
            border-top: 1px solid #E5E7EB;
            padding-top: 16px;
        }

        .row {
            display: flex;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px dashed #E5E7EB;
        }

        .label {
            width: 42%;
            color: #6B7280;
            font-size: 14px;
        }

        .value {
            width: 58%;
            color: #111827;
            font-weight: 700;
            font-size: 14px;
            word-break: break-word;
        }

        .items {
            margin-top: 18px;
        }

        .item {
            padding: 12px;
            border-radius: 16px;
            background: #FFF8E8;
            margin-top: 10px;
        }

        .footer {
            margin-top: 18px;
            padding: 14px;
            border-radius: 18px;
            background: #FBF1D4;
            color: #5C3B18;
            font-weight: 700;
            line-height: 1.5;
        }

        .empty {
            text-align: center;
            padding: 30px 10px;
        }
    </style>
</head>
<body>
<div class="page">
    <div class="card">
        @if($pesanan)
            <span class="badge {{ $valid ? 'badge-valid' : 'badge-invalid' }}">
                {{ $valid ? 'QR VALID' : 'QR BELUM VALID' }}
            </span>

            <h1>Validasi Pengambilan Pesanan</h1>
            <p>{{ $pesanValidasi }}</p>

            <div class="info">
                <div class="row">
                    <div class="label">Invoice</div>
                    <div class="value">{{ $pesanan->nomor_invoice }}</div>
                </div>

                <div class="row">
                    <div class="label">Nama Pembeli</div>
                    <div class="value">{{ $pesanan->user?->name ?? '-' }}</div>
                </div>

                <div class="row">
                    <div class="label">Tanggal Pesanan</div>
                    <div class="value">{{ optional($pesanan->tanggal_pesanan)->format('d-m-Y H:i') }}</div>
                </div>

                <div class="row">
                    <div class="label">Metode Ambil</div>
                    <div class="value">
                        {{ $pesanan->metode_pengambilan === 'ambil_toko' ? 'Ambil di Toko' : 'Kurir Toko' }}
                    </div>
                </div>

                <div class="row">
                    <div class="label">Status Pesanan</div>
                    <div class="value">{{ ucwords(str_replace('_', ' ', $pesanan->status)) }}</div>
                </div>

                <div class="row">
                    <div class="label">Metode Bayar</div>
                    <div class="value">
                        {{ $pesanan->pembayaran?->metode_pembayaran === 'transfer_bank' ? 'Transfer Bank' : 'Cash / COD' }}
                    </div>
                </div>

                <div class="row">
                    <div class="label">Status Pembayaran</div>
                    <div class="value">
                        {{ ucwords(str_replace('_', ' ', $pesanan->pembayaran?->status ?? $pesanan->status_pembayaran)) }}
                    </div>
                </div>

                <div class="row">
                    <div class="label">Total Bayar</div>
                    <div class="value">Rp{{ number_format((float) $pesanan->total_bayar, 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="items">
                <h3>Produk Pesanan</h3>

                @foreach($pesanan->item as $item)
                    <div class="item">
                        <strong>{{ $item->produk?->nama ?? 'Produk' }}</strong><br>
                        Jumlah: {{ $item->jumlah }}<br>
                        Subtotal: Rp{{ number_format((float) $item->subtotal, 0, ',', '.') }}
                    </div>
                @endforeach
            </div>

            <div class="footer">
                @if($valid)
                    Pesanan boleh diserahkan kepada pembeli. Setelah diserahkan, admin dapat mengubah status pesanan menjadi selesai.
                @else
                    Jangan serahkan pesanan sebelum pembayaran lunas dan status pesanan siap diambil.
                @endif
            </div>
        @else
            <div class="empty">
                <span class="badge badge-invalid">QR TIDAK VALID</span>
                <h1>Pesanan Tidak Ditemukan</h1>
                <p>QR Code tidak cocok dengan data pesanan di sistem Si Tahu.</p>
            </div>
        @endif
    </div>
</div>
</body>
</html>