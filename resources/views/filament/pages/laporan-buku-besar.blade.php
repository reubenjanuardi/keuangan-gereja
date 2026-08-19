<x-filament-panels::page>
    <style>
        .report-section {
            margin-bottom: 24px;
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
            padding: 12px 16px;
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
    </style>

    {{-- Form Filter Section --}}
    <x-filament::section>
        {{ $this->form }}
    </x-filament::section>

    {{-- Data Section --}}
    <div style="margin-top: 20px;">
        @forelse($this->reportData as $kodeAkun => $transactions)
            @php
                $firstCoa = $transactions->first()->chartOfAccount ?? null;
                $totalMasuk = $transactions->filter(fn($t) => in_array($t->voucher->jenis_voucher ?? '', ['Masuk', 'BKM', 'BBM'], true))->sum('nominal');
                $totalKeluar = $transactions->filter(fn($t) => in_array($t->voucher->jenis_voucher ?? '', ['Keluar', 'BKK', 'BBK'], true))->sum('nominal');
                $netSaldo = $totalMasuk - $totalKeluar;
            @endphp

            <div class="report-section">
                <div class="report-section-header">
                    <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                        <span style="font-family: monospace; font-size: 12px; font-weight: 800; background: #eff6ff; color: #1d4ed8; padding: 3px 8px; border-radius: 6px; border: 1px solid #bfdbfe;">
                            {{ $kodeAkun }}
                        </span>
                        <span style="font-size: 16px; font-weight: 700; color: #0f172a;" class="dark:text-white">
                            {{ $firstCoa->nama_akun ?? 'Akun' }}
                        </span>
                        <span style="font-size: 12px; color: #64748b;">
                            ({{ $firstCoa->kategori ?? '-' }})
                        </span>
                    </div>

                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                        <span style="font-size: 12px; font-weight: 600; background: #ecfdf5; color: #047857; padding: 4px 10px; border-radius: 6px; border: 1px solid #a7f3d0;">
                            Masuk: Rp {{ number_format($totalMasuk, 0, ',', '.') }}
                        </span>
                        <span style="font-size: 12px; font-weight: 600; background: #fff1f2; color: #be123c; padding: 4px 10px; border-radius: 6px; border: 1px solid #fecdd3;">
                            Keluar: Rp {{ number_format($totalKeluar, 0, ',', '.') }}
                        </span>
                        <span style="font-size: 12px; font-weight: 700; background: {{ $netSaldo >= 0 ? '#eff6ff' : '#fffbeb' }}; color: {{ $netSaldo >= 0 ? '#1d4ed8' : '#b45309' }}; padding: 4px 10px; border-radius: 6px; border: 1px solid {{ $netSaldo >= 0 ? '#bfdbfe' : '#fde68a' }};">
                            Saldo: Rp {{ number_format($netSaldo, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <div style="overflow-x: auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 140px;">No. Bukti</th>
                                <th style="width: 110px;">Tanggal</th>
                                <th style="width: 180px;">Pihak Terkait</th>
                                <th>Uraian</th>
                                <th style="width: 160px; text-align: right;">Masuk (Debet)</th>
                                <th style="width: 160px; text-align: right;">Keluar (Kredit)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $tx)
                                @php
                                    $isMasuk = in_array($tx->voucher->jenis_voucher ?? '', ['Masuk', 'BKM', 'BBM'], true);
                                @endphp
                                <tr>
                                    <td class="col-code">{{ $tx->no_bukti }}</td>
                                    <td style="color: #475569; white-space: nowrap;">
                                        {{ \Carbon\Carbon::parse($tx->voucher->tanggal ?? now())->format('d/m/Y') }}
                                    </td>
                                    <td style="color: #334155;">{{ $tx->voucher->pihak_terkait ?? '-' }}</td>
                                    <td style="color: #0f172a;" class="dark:text-gray-100">{{ $tx->uraian }}</td>
                                    <td class="col-num" style="color: #059669; font-weight: 600;">
                                        {{ $isMasuk ? 'Rp ' . number_format($tx->nominal, 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="col-num" style="color: #e11d48; font-weight: 600;">
                                        {{ !$isMasuk ? 'Rp ' . number_format($tx->nominal, 0, ',', '.') : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" style="text-align: right; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px;">
                                    Subtotal {{ $kodeAkun }}:
                                </td>
                                <td class="col-num" style="color: #047857; font-weight: 800;">
                                    Rp {{ number_format($totalMasuk, 0, ',', '.') }}
                                </td>
                                <td class="col-num" style="color: #be123c; font-weight: 800;">
                                    Rp {{ number_format($totalKeluar, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @empty
            <div style="text-align: center; padding: 40px 20px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; color: #64748b;">
                Tidak ada transaksi yang ditemukan untuk periode & filter yang dipilih.
            </div>
        @endforelse
    </div>
</x-filament-panels::page>
