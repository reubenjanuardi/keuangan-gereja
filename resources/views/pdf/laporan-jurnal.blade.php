<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Jurnal Transaksi</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 1.2cm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9pt;
            color: #1a1a1a;
            line-height: 1.3;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #2d3748;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .header h2 {
            margin: 0;
            font-size: 14pt;
            text-transform: uppercase;
            color: #1a202c;
        }
        .header p {
            margin: 2px 0 0 0;
            font-size: 8pt;
            color: #718096;
        }
        .period-info {
            font-size: 9pt;
            margin-bottom: 12px;
            color: #4a5568;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #cbd5e0;
            padding: 5px 7px;
            font-size: 8.5pt;
        }
        th {
            background-color: #f7fafc;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 7.5pt;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row td {
            font-weight: bold;
            background-color: #edf2f7;
            font-size: 9.5pt;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>LAPORAN JURNAL TRANSAKSI</h2>
        <p>Sistem Informasi Keuangan Gereja (SIKG)</p>
    </div>

    <div class="period-info">
        <strong>Periode:</strong> {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
        @if($kategori)
            | <strong>Kategori:</strong> {{ $kategori }}
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 8%;">Tanggal</th>
                <th style="width: 12%;">No. Bukti</th>
                <th style="width: 8%;">Jenis</th>
                <th style="width: 18%;">Pihak Terkait</th>
                <th style="width: 24%;">Kode & Nama Akun</th>
                <th style="width: 20%;">Uraian</th>
                <th style="width: 10%;" class="text-right">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @forelse($reportData as $tx)
                @php
                    $grandTotal += $tx->nominal;
                @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($tx->voucher->tanggal ?? now())->format('d/m/Y') }}</td>
                    <td><strong>{{ $tx->no_bukti }}</strong></td>
                    <td class="text-center">{{ $tx->voucher->jenis_voucher ?? '-' }}</td>
                    <td>{{ $tx->voucher->pihak_terkait ?? '-' }}</td>
                    <td><code>{{ $tx->kode_akun }}</code> - {{ $tx->chartOfAccount->nama_akun ?? '-' }}</td>
                    <td>{{ $tx->uraian }}</td>
                    <td class="text-right">{{ number_format($tx->nominal, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada transaksi yang ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
        @if(count($reportData) > 0)
            <tfoot>
                <tr class="total-row">
                    <td colspan="6" class="text-right">GRAND TOTAL NOMINAL:</td>
                    <td class="text-right">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

</body>
</html>
