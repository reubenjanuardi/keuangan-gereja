<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Buku Besar</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 1.2cm 1.5cm 1.2cm 1.5cm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8.5pt;
            color: #111;
            line-height: 1.3;
        }

        /* ── HEADER KOP SURAT ──────────────────────────────── */
        .kop-header {
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }
        .church-title {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .church-sub {
            font-size: 8pt;
            color: #444;
            margin-top: 2px;
        }
        .doc-title {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 6px 0 4px 0;
            letter-spacing: 0.5px;
        }
        .period-subtitle {
            text-align: center;
            font-size: 8.5pt;
            color: #444;
            margin-bottom: 14px;
        }

        /* ── ACCOUNT BOX & TABLES ──────────────────────────── */
        .account-box {
            margin-bottom: 14px;
            page-break-inside: avoid;
        }
        .account-header-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-bottom: none;
        }
        .account-header-table td {
            padding: 5px 8px;
            font-size: 9pt;
            font-weight: bold;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
        }
        table.data-table th {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 5px 7px;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            text-align: left;
        }
        table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 7px;
            font-size: 8pt;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-mono { font-family: monospace; }
        .total-row td {
            font-weight: bold;
            background-color: #f8fafc;
            border-top: 2px solid #cbd5e1;
        }
    </style>
</head>
<body>

    {{-- KOP HEADER --}}
    <table class="kop-header">
        <tr>
            <td style="width: 70%; vertical-align: top;">
                <div class="church-title">{{ $churchName ?? 'GPIB JEMAAT HOSIANA' }}</div>
                @if(!empty($churchAddress1))
                    <div class="church-sub">{{ $churchAddress1 }}</div>
                @endif
                @if(!empty($churchAddress2))
                    <div class="church-sub">{{ $churchAddress2 }}</div>
                @endif
            </td>
            <td style="width: 30%; text-align: right; vertical-align: bottom; font-size: 8pt; color: #555;">
                Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB
            </td>
        </tr>
    </table>

    <div class="doc-title">LAPORAN BUKU BESAR</div>
    <div class="period-subtitle">
        Periode: <strong>{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</strong>
        @if(!empty($jenisVoucher))
            | Jenis: <strong>{{ $jenisVoucher }}</strong>
        @endif
        @if(!empty($kodeAkun))
            | Akun: <strong>{{ $kodeAkun }}</strong>
        @endif
    </div>

    @forelse($reportData as $kode => $transactions)
        @php
            $firstCoa = $transactions->first()->chartOfAccount ?? null;
            $totalMasuk = $transactions->filter(fn($t) => in_array($t->voucher->jenis_voucher ?? '', ['Masuk', 'BKM', 'BBM'], true))->sum('nominal');
            $totalKeluar = $transactions->filter(fn($t) => in_array($t->voucher->jenis_voucher ?? '', ['Keluar', 'BKK', 'BBK'], true))->sum('nominal');
            $netSaldo = $totalMasuk - $totalKeluar;
        @endphp
        <div class="account-box">
            <table class="account-header-table">
                <tr>
                    <td style="width: 60%;">
                        Akun: <span class="font-mono">{{ $kode }}</span> - {{ $firstCoa->nama_akun ?? 'Akun' }}
                        <span style="font-weight: normal; font-size: 8pt; color: #555;">(Kategori: {{ $firstCoa->kategori ?? '-' }})</span>
                    </td>
                    <td style="width: 40%; text-align: right; font-size: 8pt;">
                        Masuk: Rp {{ number_format($totalMasuk, 0, ',', '.') }} |
                        Keluar: Rp {{ number_format($totalKeluar, 0, ',', '.') }} |
                        Saldo: Rp {{ number_format($netSaldo, 0, ',', '.') }}
                    </td>
                </tr>
            </table>

            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 12%;">No. Bukti</th>
                        <th style="width: 9%;" class="text-center">Tanggal</th>
                        <th style="width: 18%;">Pihak Terkait</th>
                        <th style="width: 37%;">Uraian</th>
                        <th style="width: 12%;" class="text-right">Masuk (Debet)</th>
                        <th style="width: 12%;" class="text-right">Keluar (Kredit)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $tx)
                        @php
                            $isMasuk = in_array($tx->voucher->jenis_voucher ?? '', ['Masuk', 'BKM', 'BBM'], true);
                        @endphp
                        <tr>
                            <td class="font-mono" style="font-weight: bold;">{{ $tx->no_bukti }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($tx->voucher->tanggal ?? now())->format('d/m/Y') }}</td>
                            <td>{{ $tx->voucher->pihak_terkait ?? '-' }}</td>
                            <td>{{ $tx->uraian }}</td>
                            <td class="text-right font-mono">{{ $isMasuk ? number_format($tx->nominal, 0, ',', '.') : '-' }}</td>
                            <td class="text-right font-mono">{{ !$isMasuk ? number_format($tx->nominal, 0, ',', '.') : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="4" class="text-right uppercase">Subtotal {{ $kode }}:</td>
                        <td class="text-right font-mono">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</td>
                        <td class="text-right font-mono">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @empty
        <div style="text-align: center; color: #64748b; padding: 30px; border: 1px dashed #cbd5e1;">
            Tidak ada transaksi yang ditemukan untuk periode dan kriteria filter yang dipilih.
        </div>
    @endforelse

</body>
</html>
