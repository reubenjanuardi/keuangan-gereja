<x-filament-panels::page>
    <style>
        .report-section {
            margin-bottom: 24px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        .dark .report-section {
            border-color: rgba(255, 255, 255, 0.1);
            background: #18181b;
        }
        .report-section-header {
            padding: 14px 20px;
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        .dark .report-section-header {
            background: rgba(255, 255, 255, 0.02);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
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
            background: rgba(255, 255, 255, 0.02);
            color: #9ca3af;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        table.data-table td {
            padding: 9px 16px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            color: #334155;
        }
        .dark table.data-table td {
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            color: #d1d5db;
        }
        table.data-table tr:hover td {
            background-color: #f8fafc;
        }
        .dark table.data-table tr:hover td {
            background-color: rgba(255, 255, 255, 0.03);
        }
        table.data-table tfoot td {
            background: #f8fafc;
            font-weight: 700;
            padding: 12px 16px;
            border-top: 2px solid #cbd5e1;
            color: #1e293b;
        }
        .dark table.data-table tfoot td {
            background: rgba(255, 255, 255, 0.02);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: #e5e7eb;
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
            color: #4f46e5;
            white-space: nowrap;
        }
        .dark .col-code {
            color: #818cf8;
        }
        .coa-header-code {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }
        .dark .coa-header-code {
            background: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
            border-color: rgba(96, 165, 250, 0.3);
        }
        .badge-masuk-summary {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }
        .dark .badge-masuk-summary {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border-color: rgba(52, 211, 153, 0.3);
        }
        .badge-keluar-summary {
            background: #fff1f2;
            color: #be123c;
            border: 1px solid #fecdd3;
        }
        .dark .badge-keluar-summary {
            background: rgba(244, 63, 94, 0.15);
            color: #fb7185;
            border-color: rgba(251, 113, 133, 0.3);
        }
        .badge-saldo-pos {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }
        .dark .badge-saldo-pos {
            background: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
            border-color: rgba(96, 165, 250, 0.3);
        }
        .badge-saldo-neg {
            background: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
        }
        .dark .badge-saldo-neg {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border-color: rgba(251, 191, 36, 0.3);
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
                        <span class="coa-header-code font-mono text-xs font-bold px-2 py-0.5 rounded-md">
                            {{ $kodeAkun }}
                        </span>
                        <span class="text-base font-bold text-gray-900 dark:text-white">
                            {{ $firstCoa->nama_akun ?? 'Akun' }}
                        </span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            ({{ $firstCoa->kategori ?? '-' }})
                        </span>
                    </div>

                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                        <span class="badge-masuk-summary text-xs font-semibold px-2.5 py-1 rounded-md">
                            Masuk: Rp {{ number_format($totalMasuk, 0, ',', '.') }}
                        </span>
                        <span class="badge-keluar-summary text-xs font-semibold px-2.5 py-1 rounded-md">
                            Keluar: Rp {{ number_format($totalKeluar, 0, ',', '.') }}
                        </span>
                        <span class="{{ $netSaldo >= 0 ? 'badge-saldo-pos' : 'badge-saldo-neg' }} text-xs font-bold px-2.5 py-1 rounded-md">
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
                                    <td class="text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($tx->voucher->tanggal ?? now())->format('d/m/Y') }}
                                    </td>
                                    <td class="text-gray-700 dark:text-gray-300">{{ $tx->voucher->pihak_terkait ?? '-' }}</td>
                                    <td class="text-gray-800 dark:text-gray-200">{{ $tx->uraian }}</td>
                                    <td class="col-num font-semibold {{ $isMasuk ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400 dark:text-gray-600' }}">
                                        {{ $isMasuk ? 'Rp ' . number_format($tx->nominal, 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="col-num font-semibold {{ !$isMasuk ? 'text-rose-600 dark:text-rose-400' : 'text-gray-400 dark:text-gray-600' }}">
                                        {{ !$isMasuk ? 'Rp ' . number_format($tx->nominal, 0, ',', '.') : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-right uppercase text-[11px] tracking-wider text-gray-700 dark:text-gray-300 font-bold">
                                    Subtotal {{ $kodeAkun }}:
                                </td>
                                <td class="col-num text-emerald-600 dark:text-emerald-400 font-extrabold text-sm">
                                    Rp {{ number_format($totalMasuk, 0, ',', '.') }}
                                </td>
                                <td class="col-num text-rose-600 dark:text-rose-400 font-extrabold text-sm">
                                    Rp {{ number_format($totalKeluar, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @empty
            <div class="text-center py-10 px-5 bg-white dark:bg-[#18181b] border border-gray-200 dark:border-white/10 rounded-xl text-gray-400 dark:text-gray-500">
                Tidak ada transaksi yang ditemukan untuk periode & filter yang dipilih.
            </div>
        @endforelse
    </div>
</x-filament-panels::page>
