<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Voucher - {{ $voucher->no_bukti }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1.8cm 1.8cm 1.5cm 1.8cm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            color: #000;
            line-height: 1.4;
        }

        /* ── TOP HEADER ─────────────────────────────────────── */
        .top-header {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .top-header td {
            vertical-align: top;
            padding: 0;
        }
        .church-info {
            font-size: 10pt;
            line-height: 1.6;
        }
        .church-info .church-name {
            font-size: 11pt;
            font-weight: bold;
        }
        .no-box {
            width: 220px;
            text-align: right;
        }
        .no-box .no-label {
            font-size: 10pt;
            margin-bottom: 3px;
        }
        .account-box {
            width: 220px;
            border-collapse: collapse;
            border: 1px solid #000;
        }
        .account-box th {
            border: 1px solid #000;
            padding: 4px 8px;
            font-size: 9pt;
            font-weight: bold;
            text-align: center;
        }
        .account-box td {
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 9pt;
            text-align: center;
            height: 28px;
            vertical-align: middle;
        }

        /* ── DOCUMENT TITLE ─────────────────────────────────── */
        .doc-title {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            margin: 12px 0 14px 0;
            letter-spacing: 0.5px;
        }

        /* ── META INFO (Terima dari, Terbilang) ─────────────── */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .meta-table td {
            padding: 2px 4px;
            vertical-align: top;
        }
        .meta-label {
            width: 100px;
            font-weight: bold;
            white-space: nowrap;
        }
        .meta-colon {
            width: 12px;
        }

        /* ── ITEMS TABLE ────────────────────────────────────── */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 10pt;
            font-weight: bold;
            text-align: center;
            background-color: #fff;
        }
        .items-table td {
            border: 1px solid #000;
            padding: 5px 8px;
            font-size: 10pt;
            vertical-align: top;
        }
        .items-table .no-col  { width: 8%;  text-align: center; }
        .items-table .desc-col { width: 67%; }
        .items-table .amt-col  { width: 25%; text-align: right; }
        .items-table .item-row td {
            height: 22px;
        }
        /* Blank filler rows to give the table a fixed visual height */
        .items-table .blank-row td {
            height: 22px;
        }
        .total-row td {
            font-weight: bold;
            font-size: 10pt;
            background-color: #fff;
        }
        .total-label {
            text-align: right;
            border: 1px solid #000;
        }
        .total-amount {
            text-align: right;
            border: 1px solid #000;
        }

        /* ── SIGNATURE SECTION ──────────────────────────────── */
        .signature-wrapper {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .signature-wrapper > tbody > tr > td {
            vertical-align: top;
            padding: 0;
        }

        /* Left: 3-box signature block */
        .sig-left-table {
            border-collapse: collapse;
            width: 260px;
        }
        .sig-left-table th {
            border: 1px solid #000;
            padding: 5px 10px;
            font-size: 9.5pt;
            font-weight: bold;
            text-align: center;
            width: 86px;
        }
        .sig-left-table td {
            border: 1px solid #000;
            height: 60px;
            width: 86px;
            vertical-align: bottom;
            padding: 4px 8px;
            font-size: 8pt;
        }

        /* Right: city + penerima */
        .sig-right {
            text-align: right;
            vertical-align: top;
            padding-top: 0;
            font-size: 10pt;
        }
        .sig-right .city-line {
            margin-bottom: 50px;
        }
        .sig-right .penerima-label {
            font-weight: bold;
        }
    </style>
</head>
<body>

    {{-- ══════════════════════════════════════════════════ --}}
    {{-- TOP HEADER: Church info (left) + No + Account (right) --}}
    {{-- ══════════════════════════════════════════════════ --}}
    <table class="top-header">
        <tr>
            {{-- Left: church identity --}}
            <td class="church-info">
                <div class="church-name">{{ $settings['church_name'] }}</div>
                <div>{{ $settings['church_address1'] }}</div>
                <div>{{ $settings['church_address2'] }}</div>
            </td>

            {{-- Right: No + account code box --}}
            <td class="no-box">
                <div class="no-label"><strong>No :</strong> {{ $voucher->no_bukti }}</div>
                <table class="account-box">
                    <thead>
                        <tr>
                            <th style="width:50%;">Kode</th>
                            <th style="width:50%;">Account</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $voucher->kode_akun }}</td>
                            <td>{{ $voucher->chartOfAccount->nama_akun ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    {{-- ══════════════════════════════════════════════════ --}}
    {{-- DOCUMENT TITLE --}}
    {{-- ══════════════════════════════════════════════════ --}}
    <div class="doc-title">
        BUKTI KAS / BANK {{ strtoupper($voucher->jenis_voucher) }}
    </div>

    {{-- ══════════════════════════════════════════════════ --}}
    {{-- TERIMA DARI + TERBILANG --}}
    {{-- ══════════════════════════════════════════════════ --}}
    <table class="meta-table">
        <tr>
            <td class="meta-label">Terima dari</td>
            <td class="meta-colon">:</td>
            <td>{{ $voucher->pihak_terkait }}</td>
        </tr>
        <tr>
            <td class="meta-label">Terbilang</td>
            <td class="meta-colon">:</td>
            <td><em>{{ $terbilang }}</em></td>
        </tr>
    </table>

    {{-- ══════════════════════════════════════════════════ --}}
    {{-- ITEM TABLE --}}
    {{-- ══════════════════════════════════════════════════ --}}
    <table class="items-table">
        <thead>
            <tr>
                <th class="no-col">No</th>
                <th class="desc-col">Keterangan</th>
                <th class="amt-col">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($voucher->transactions as $idx => $tx)
                <tr class="item-row">
                    <td class="no-col">{{ $idx + 1 }}</td>
                    <td class="desc-col">{{ $tx->uraian }}</td>
                    <td class="amt-col">{{ number_format($tx->nominal, 0, ',', '.') }}</td>
                </tr>
            @endforeach

            {{-- Filler blank rows to give a fixed table height (min 8 rows total) --}}
            @php $fillerCount = max(0, 7 - count($voucher->transactions)); @endphp
            @for($i = 0; $i < $fillerCount; $i++)
                <tr class="blank-row">
                    <td class="no-col">&nbsp;</td>
                    <td class="desc-col">&nbsp;</td>
                    <td class="amt-col">&nbsp;</td>
                </tr>
            @endfor
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="2" class="total-label">TOTAL</td>
                <td class="total-amount">Rp{{ number_format($voucher->total_nominal, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- ══════════════════════════════════════════════════ --}}
    {{-- SIGNATURE SECTION --}}
    {{-- ══════════════════════════════════════════════════ --}}
    <table class="signature-wrapper">
        <tr>
            {{-- LEFT: Kasir | Ketua IV | Bendahara --}}
            <td>
                <table class="sig-left-table">
                    <thead>
                        <tr>
                            <th>Kasir</th>
                            <th>Ketua IV</th>
                            <th>Bendahara</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    </tbody>
                </table>
            </td>

            {{-- RIGHT: Jakarta, [date] + Tanda Tangan Penerima --}}
            <td class="sig-right">
                <div class="city-line">
                    Jakarta, {{ \Carbon\Carbon::parse($voucher->tanggal)->translatedFormat('d F Y') }}
                </div>
                <div class="penerima-label">Tanda Tangan Penerima</div>
            </td>
        </tr>
    </table>

</body>
</html>
