<x-filament-panels::page>
    <style>
        .report-section {
            margin-top: 20px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        .dark .report-section {
            border-color: #27272a;
            background: #18181b;
        }
        .report-section-header {
            padding: 14px 20px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        .dark .report-section-header {
            background: #27272a;
            border-color: #3f3f46;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse !important;
            text-align: left;
            font-size: 13.5px;
        }
        table.data-table th {
            background: #f1f5f9;
            color: #475569;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
            padding: 10px 16px;
            border-bottom: 1px solid #cbd5e1;
        }
        .dark table.data-table th {
            background: #27272a;
            color: #a1a1aa;
            border-color: #3f3f46;
        }
        table.data-table td {
            padding: 9px 16px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        .dark table.data-table td {
            border-color: #27272a;
        }
        table.data-table tr:hover td {
            background-color: #f8fafc;
        }
        .dark table.data-table tr:hover td {
            background-color: rgba(39, 39, 42, 0.5);
        }
        table.data-table tfoot td {
            background: #f8fafc;
            font-weight: 700;
            padding: 14px 16px;
            border-top: 2px solid #cbd5e1;
        }
        .dark table.data-table tfoot td {
            background: #27272a;
            border-color: #52525b;
        }
        .col-num {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            text-align: right;
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
        }
        .col-code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 12px;
            font-weight: 700;
            color: #2563eb;
            white-space: nowrap;
        }
        .dark .col-code {
            color: #60a5fa;
        }
        .badge-masuk {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }
        .badge-keluar {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            background: #fff1f2;
            color: #be123c;
            border: 1px solid #fecdd3;
        }
    </style>

    {{-- Form Filter Section --}}
    <x-filament::section>
        {{ $this->form }}
    </x-filament::section>

    {{-- Journal Table Section --}}
    <div class="report-section">
        <div class="report-section-header">
            <span style="font-size: 16px; font-weight: 700; color: #0f172a;" class="dark:text-white">
                Laporan Jurnal Transaksi
            </span>
            <span style="font-size: 12px; font-weight: 600; background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 6px; border: 1px solid #cbd5e1;">
                Total: {{ count($this->reportData) }} Baris Transaksi
            </span>
        </div>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 100px;">Tanggal</th>
                        <th style="width: 140px;">No. Bukti</th>
                        <th style="width: 90px; text-align: center;">Jenis</th>
                        <th style="width: 180px;">Pihak Terkait</th>
                        <th style="width: 240px;">Akun Anggaran</th>
                        <th>Uraian</th>
                        <th style="width: 160px; text-align: right;">Nominal (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @php $grandTotal = 0; @endphp
                    @forelse($this->reportData as $tx)
                        @php
                            $grandTotal += $tx->nominal;
                            $isMasuk = ($tx->voucher->jenis_voucher ?? '') === 'Masuk';
                        @endphp
                        <tr>
                            <td style="color: #475569; white-space: nowrap;">
                                {{ \Carbon\Carbon::parse($tx->voucher->tanggal ?? now())->format('d/m/Y') }}
                            </td>
                            <td class="col-code">{{ $tx->no_bukti }}</td>
                            <td style="text-align: center; white-space: nowrap;">
                                @if($isMasuk)
                                    <span class="badge-masuk">Masuk</span>
                                @else
                                    <span class="badge-keluar">Keluar</span>
                                @endif
                            </td>
                            <td style="color: #334155;">{{ $tx->voucher->pihak_terkait ?? '-' }}</td>
                            <td>
                                <span style="font-family: monospace; font-size: 12px; font-weight: 700; color: #0f172a;" class="dark:text-white">{{ $tx->kode_akun }}</span>
                                <span style="font-size: 12px; color: #64748b; margin-left: 4px;">- {{ $tx->chartOfAccount->nama_akun ?? '-' }}</span>
                            </td>
                            <td style="color: #0f172a;" class="dark:text-gray-100">{{ $tx->uraian }}</td>
                            <td class="col-num" style="font-weight: 700; color: #0f172a;" class="dark:text-white">
                                Rp {{ number_format($tx->nominal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: #64748b;">
                                Tidak ada transaksi yang ditemukan untuk periode dan filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if(count($this->reportData) > 0)
                    <tfoot>
                        <tr>
                            <td colspan="6" style="text-align: right; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px;">
                                GRAND TOTAL NOMINAL:
                            </td>
                            <td class="col-num" style="color: #1d4ed8; font-size: 16px; font-weight: 800;">
                                Rp {{ number_format($grandTotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</x-filament-panels::page>
