<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $dataset['title'] }} - SiTahu</title>
    <style>
        @page { size: A4 landscape; margin: 14mm; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            color: #1f2937;
            font-family: Arial, Helvetica, sans-serif;
            background: #f8fafc;
        }
        .toolbar {
            position: sticky;
            top: 0;
            z-index: 20;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
        }
        .toolbar-title { font-weight: 800; }
        .toolbar-actions { display: flex; gap: 8px; }
        .btn {
            border: 1px solid #d1d5db;
            background: #fff;
            color: #111827;
            padding: 9px 14px;
            border-radius: 10px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }
        .btn-primary {
            border-color: #c89335;
            background: #c89335;
            color: #fff;
        }
        .paper {
            width: 100%;
            min-height: calc(100vh - 72px);
            padding: 18px;
            background: #fff;
        }
        .header {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            align-items: flex-start;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 3px solid #c89335;
        }
        h1 {
            margin: 0;
            font-size: 22px;
            line-height: 1.2;
        }
        .meta {
            margin-top: 6px;
            color: #6b7280;
            font-size: 12px;
            font-weight: 700;
        }
        .brand {
            text-align: right;
            color: #7a5618;
            font-size: 13px;
            font-weight: 800;
        }
        .totals {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 8px;
            margin-bottom: 14px;
        }
        .total-card {
            border: 1px solid #f1d49c;
            background: #fff8ea;
            border-radius: 10px;
            padding: 9px 10px;
        }
        .total-label {
            color: #7a5618;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .05em;
        }
        .total-value {
            margin-top: 4px;
            color: #111827;
            font-size: 13px;
            font-weight: 800;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 7px 8px;
            vertical-align: top;
            font-size: 11px;
            line-height: 1.35;
            word-break: break-word;
        }
        th {
            background: #fff8ea;
            color: #7a5618;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .03em;
        }
        tbody tr:nth-child(even) td { background: #fafafa; }
        .empty {
            padding: 28px;
            text-align: center;
            color: #6b7280;
            font-weight: 700;
        }
        .footer {
            margin-top: 14px;
            color: #6b7280;
            font-size: 10px;
            text-align: right;
        }
        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .paper { padding: 0; min-height: auto; }
            th, td { font-size: 9.6px; padding: 5px 6px; }
            h1 { font-size: 18px; }
            .totals { grid-template-columns: repeat(4, 1fr); gap: 6px; }
            .total-card { padding: 6px 8px; border-radius: 6px; }
            .total-value { font-size: 11px; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <div class="toolbar-title">{{ $dataset['title'] }}</div>
        <div class="toolbar-actions">
            <button class="btn btn-primary" onclick="window.print()">Cetak / Simpan PDF</button>
            <button class="btn" onclick="window.close()">Tutup</button>
        </div>
    </div>

    <main class="paper">
        <div class="header">
            <div>
                <h1>{{ $dataset['title'] }}</h1>
                <div class="meta">Periode: {{ $tanggalMulai->format('d/m/Y') }} - {{ $tanggalSelesai->format('d/m/Y') }}</div>
                <div class="meta">Dicetak: {{ now()->format('d/m/Y H:i') }}</div>
            </div>
            <div class="brand">
                SiTahu<br>
                Laporan {{ ucfirst($jenis) }}
            </div>
        </div>

        @if(!empty($dataset['totals']))
            <section class="totals">
                @foreach($dataset['totals'] as $total)
                    <div class="total-card">
                        <div class="total-label">{{ $total['label'] ?? '' }}</div>
                        <div class="total-value">{{ $total['value'] ?? '' }}</div>
                    </div>
                @endforeach
            </section>
        @endif

        <table>
            <thead>
                <tr>
                    @foreach($dataset['headers'] as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($dataset['rows'] as $row)
                    <tr>
                        @foreach($row as $cell)
                            <td>{{ $cell }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td class="empty" colspan="{{ max(1, count($dataset['headers'])) }}">Tidak ada data pada filter ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            Total data: {{ number_format(count($dataset['rows'])) }}
        </div>
    </main>

    <script>
        window.addEventListener('load', function () {
            setTimeout(function () { window.print(); }, 350);
        });
    </script>
</body>
</html>
