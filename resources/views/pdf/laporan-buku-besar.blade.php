<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Buku Besar</title>
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
        .account-box {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        .account-title {
            font-size: 10pt;
            font-weight: bold;
            background-color: #edf2f7;
            padding: 5px 8px;
            border: 1px solid #cbd5e0;
            border-bottom: none;
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
            background-color: #f7fafc;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>LAPORAN BUKU BESAR</h2>
        <p>Sistem Informasi Keuangan Gereja (SIKG)</p>
    </div>

    <div class="period-info">
        <strong>Periode:</strong> {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
        @if($jenisVoucher)
            | <strong>Jenis:</strong> {{ $jenisVoucher }}
        @endif
    </div>

    @forelse($reportData as $kodeAkun => $transactions)
        @php
            $firstCoa = $transactions->first()->chartOfAccount ?? null;
            $totalMasuk = $transactions->filter(fn($t) => ($t->voucher->jenis_voucher ?? '') === 'Masuk')->sum('nominal');
            $totalKeluar = $transactions->filter(fn($t) => ($t->voucher->jenis_voucher ?? '') === 'Keluar')->sum('nominal');
        @endphp
        <div class="account-box">
            <div class="account-title">
                Akun: {{ $kodeAkun }} - {{ $firstCoa->nama_akun ?? 'Akun' }} (Kategori: {{ $firstCoa->kategori ?? '-' }})
            </div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 12%;">No. Bukti</th>
                        <th style="width: 10%;">Tanggal</th>
                        <th style="width: 20%;">Pihak Terkait</th>
                        <th style="width: 34%;">Uraian</th>
                        <th style="width: 12%;" class="text-right">Masuk (Rp)</th>
                        <th style="width: 12%;" class="text-right">Keluar (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $tx)
                        @php
                            $isMasuk = ($tx->voucher->jenis_voucher ?? '') === 'Masuk';
                        @endphp
                        <tr>
                            <td>{{ $tx->no_bukti }}</td>
                            <td>{{ \Carbon\Carbon::parse($tx->voucher->tanggal ?? now())->format('d/m/Y') }}</td>
                            <td>{{ $tx->voucher->pihak_terkait ?? '-' }}</td>
                            <td>{{ $tx->uraian }}</td>
                            <td class="text-right">{{ $isMasuk ? number_format($tx->nominal, 0, ',', '.') : '-' }}</td>
                            <td class="text-right">{{ !$isMasuk ? number_format($tx->nominal, 0, ',', '.') : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="4" class="text-right">TOTAL:</td>
                        <td class="text-right">{{ number_format($totalMasuk, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totalKeluar, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @empty
        <p style="text-align: center; color: #718096;">Tidak ada transaksi yang ditemukan.</p>
    @endforelse

</body>
</html>
