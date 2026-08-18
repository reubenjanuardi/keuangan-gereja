<x-filament-panels::page>
    <style>
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }
        .kpi-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .dark .kpi-card {
            background: #18181b;
            border-color: #27272a;
        }
        .kpi-icon-box {
            width: 36px !important;
            height: 36px !important;
            min-width: 36px !important;
            min-height: 36px !important;
            max-width: 36px !important;
            max-height: 36px !important;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .kpi-icon-box svg {
            width: 20px !important;
            height: 20px !important;
            min-width: 20px !important;
            min-height: 20px !important;
            max-width: 20px !important;
            max-height: 20px !important;
            display: block;
        }
        .report-table-wrapper {
            width: 100%;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            margin-bottom: 24px;
            overflow: hidden;
        }
        .dark .report-table-wrapper {
            border-color: #27272a;
            background: #18181b;
        }
        .report-table-header {
            padding: 14px 20px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            background: #f8fafc;
        }
        .dark .report-table-header {
            background: #27272a;
            border-color: #3f3f46;
        }
        table.report-table {
            width: 100%;
            border-collapse: collapse !important;
            text-align: left;
            font-size: 13.5px;
        }
        table.report-table th {
            background: #f1f5f9;
            color: #475569;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
            padding: 10px 16px;
            border-bottom: 1px solid #cbd5e1;
        }
        .dark table.report-table th {
            background: #27272a;
            color: #a1a1aa;
            border-color: #3f3f46;
        }
        table.report-table td {
            padding: 9px 16px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        .dark table.report-table td {
            border-color: #27272a;
        }
        table.report-table tr:hover td {
            background-color: #f8fafc;
        }
        .dark table.report-table tr:hover td {
            background-color: rgba(39, 39, 42, 0.5);
        }
        table.report-table tr.row-depth-0 td {
            background-color: #f1f5f9 !important;
            font-weight: 700;
            color: #0f172a;
            border-top: 1px solid #cbd5e1;
            border-bottom: 1px solid #cbd5e1;
        }
        .dark table.report-table tr.row-depth-0 td {
            background-color: #27272a !important;
            color: #f4f4f5;
            border-color: #3f3f46;
        }
        table.report-table tr.row-depth-1 td {
            background-color: #f8fafc !important;
            font-weight: 600;
            color: #1e293b;
        }
        .dark table.report-table tr.row-depth-1 td {
            background-color: rgba(39, 39, 42, 0.4) !important;
            color: #e4e4e7;
        }
        table.report-table tfoot td {
            background: #f1f5f9;
            font-weight: 700;
            padding: 12px 16px;
            border-top: 2px solid #cbd5e1;
        }
        .dark table.report-table tfoot td {
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
            white-space: nowrap;
        }
        .tab-btn {
            padding: 8px 16px;
            font-size: 13.5px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.15s ease;
            cursor: pointer;
            border: 1px solid transparent;
        }
        .tab-btn-active {
            background: #2563eb;
            color: #ffffff;
            box-shadow: 0 1px 2px rgba(37,99,235,0.2);
        }
        .tab-btn-inactive {
            background: #f1f5f9;
            color: #475569;
        }
        .tab-btn-inactive:hover {
            background: #e2e8f0;
            color: #1e293b;
        }
        .dark .tab-btn-inactive {
            background: #27272a;
            color: #a1a1aa;
        }
        .dark .tab-btn-inactive:hover {
            background: #3f3f46;
            color: #f4f4f5;
        }
    </style>

    {{-- Form Filter Periode Section --}}
    <x-filament::section>
        {{ $this->form }}
    </x-filament::section>

    @php
        $data = $this->reportData;
        $totalPenerimaan = $data['totalPenerimaan'] ?? 0;
        $totalPengeluaran = $data['totalPengeluaran'] ?? 0;
        $surplusDefisit = $data['surplusDefisit'] ?? 0;
        $totalSaldoAwal = $data['totalSaldoAwal'] ?? 0;
        $totalSaldoAkhir = $data['totalSaldoAkhir'] ?? 0;
        $penerimaan = $data['penerimaan'] ?? [];
        $pengeluaran = $data['pengeluaran'] ?? [];
        $kasBank = $data['kasBank'] ?? [];
    @endphp

    {{-- Ringkasan KPI Cards --}}
    <div class="kpi-grid">
        {{-- Card 1: Penerimaan --}}
        <div class="kpi-card">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">
                    Total Penerimaan
                </span>
                <div class="kpi-icon-box" style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0;">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 4.5-15 15m0 0h11.25m-11.25 0V8.25" />
                    </svg>
                </div>
            </div>
            <div style="margin-top: 8px; font-size: 22px; font-weight: 800; color: #059669; font-family: monospace;">
                Rp {{ number_format($totalPenerimaan, 0, ',', '.') }}
            </div>
            <div style="font-size: 11px; color: #64748b; margin-top: 4px;">
                Realisasi periode berjalan
            </div>
        </div>

        {{-- Card 2: Pengeluaran --}}
        <div class="kpi-card">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">
                    Total Pengeluaran
                </span>
                <div class="kpi-icon-box" style="background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3;">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" />
                    </svg>
                </div>
            </div>
            <div style="margin-top: 8px; font-size: 22px; font-weight: 800; color: #e11d48; font-family: monospace;">
                Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
            </div>
            <div style="font-size: 11px; color: #64748b; margin-top: 4px;">
                Realisasi periode berjalan
            </div>
        </div>

        {{-- Card 3: Surplus / Defisit --}}
        <div class="kpi-card">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">
                    {{ $surplusDefisit >= 0 ? 'Surplus Mingguan' : 'Defisit Mingguan' }}
                </span>
                <div class="kpi-icon-box" style="background: {{ $surplusDefisit >= 0 ? '#eff6ff' : '#fffbeb' }}; color: {{ $surplusDefisit >= 0 ? '#2563eb' : '#d97706' }}; border: 1px solid {{ $surplusDefisit >= 0 ? '#bfdbfe' : '#fde68a' }};">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0 0 12 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-16.5 0c-.99.203-1.99.377-3 .52m16.5 0v5.334c0 .9-.54 1.71-1.37 2.05-1.07.44-2.2.77-3.38.98m-11.75-8.364v5.334c0 .9.54 1.71 1.37 2.05 1.07.44 2.2.77 3.38.98m0 0a24.28 24.28 0 0 0 10.25 0" />
                    </svg>
                </div>
            </div>
            <div style="margin-top: 8px; font-size: 22px; font-weight: 800; color: {{ $surplusDefisit >= 0 ? '#2563eb' : '#d97706' }}; font-family: monospace;">
                Rp {{ number_format($surplusDefisit, 0, ',', '.') }}
            </div>
            <div style="font-size: 11px; color: #64748b; margin-top: 4px;">
                Penerimaan - Pengeluaran
            </div>
        </div>

        {{-- Card 4: Saldo Akhir --}}
        <div class="kpi-card">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">
                    Total Saldo Akhir
                </span>
                <div class="kpi-icon-box" style="background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe;">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6H2.25m0 0v10.5m0-10.5h19.5m0 0v10.5m0-10.5c.727 0 1.453.198 1.453.75v.75m-1.453-.75H3.75m18 0v10.5m0 0a60.07 60.07 0 0 1-15.797 2.101c-.727.198-1.453-.342-1.453-1.096V18.75" />
                    </svg>
                </div>
            </div>
            <div style="margin-top: 8px; font-size: 22px; font-weight: 800; color: #4f46e5; font-family: monospace;">
                Rp {{ number_format($totalSaldoAkhir, 0, ',', '.') }}
            </div>
            <div style="font-size: 11px; color: #64748b; margin-top: 4px;">
                Posisi Kas & Bank per akhir periode
            </div>
        </div>
    </div>

    {{-- Tab Filter Button Bar --}}
    <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px;">
        <button
            type="button"
            wire:click="setTab('all')"
            class="tab-btn {{ $activeTab === 'all' ? 'tab-btn-active' : 'tab-btn-inactive' }}"
        >
            Semua Bagian
        </button>
        <button
            type="button"
            wire:click="setTab('penerimaan')"
            class="tab-btn {{ $activeTab === 'penerimaan' ? 'tab-btn-active' : 'tab-btn-inactive' }}"
        >
            1. Penerimaan ({{ count($penerimaan) }})
        </button>
        <button
            type="button"
            wire:click="setTab('pengeluaran')"
            class="tab-btn {{ $activeTab === 'pengeluaran' ? 'tab-btn-active' : 'tab-btn-inactive' }}"
        >
            2. Pengeluaran ({{ count($pengeluaran) }})
        </button>
        <button
            type="button"
            wire:click="setTab('kas_bank')"
            class="tab-btn {{ $activeTab === 'kas_bank' ? 'tab-btn-active' : 'tab-btn-inactive' }}"
        >
            3. Saldo Kas & Bank ({{ count($kasBank) }})
        </button>
    </div>

    {{-- BAGIAN 1: PENERIMAAN --}}
    @if($activeTab === 'all' || $activeTab === 'penerimaan')
        <div class="report-table-wrapper">
            <div class="report-table-header">
                <div>
                    <div style="font-size: 15px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; border-radius: 6px; background: #059669; color: #ffffff; font-size: 11px; font-weight: 800;">1</span>
                        Buku Pembantu Realisasi Mingguan - PENERIMAAN
                    </div>
                    <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                        Periode: {{ \Carbon\Carbon::parse($this->startDate)->format('d/m/Y') }} s.d. {{ \Carbon\Carbon::parse($this->endDate)->format('d/m/Y') }}
                        @if($this->mingguKe) (Minggu ke-{{ $this->mingguKe }}) @endif
                    </div>
                </div>
                <div style="font-size: 13px; font-weight: 700; background: #ecfdf5; color: #047857; padding: 4px 12px; border-radius: 6px; border: 1px solid #a7f3d0;">
                    Total: Rp {{ number_format($totalPenerimaan, 0, ',', '.') }}
                </div>
            </div>

            <div style="overflow-x: auto;">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th style="width: 150px;">No. MA</th>
                            <th>Mata Anggaran</th>
                            <th style="width: 220px; text-align: right;">Jumlah (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penerimaan as $row)
                            @php
                                $isGroup = $row['is_group'];
                                $depth = $row['depth'];
                                $paddingLeft = ($depth * 20) + 16;
                            @endphp
                            <tr class="row-depth-{{ $depth }}">
                                <td class="col-code">{{ $row['kode_akun'] }}</td>
                                <td style="padding-left: {{ $paddingLeft }}px;">
                                    @if(!$isGroup)
                                        <span style="display: inline-block; width: 5px; height: 5px; border-radius: 50%; background: #94a3b8; margin-right: 8px; vertical-align: middle;"></span>
                                    @endif
                                    <span>{{ $row['nama_akun'] }}</span>
                                </td>
                                <td class="col-num" style="{{ $row['amount'] > 0 ? 'font-weight: 700; color: #0f172a;' : 'color: #94a3b8;' }}">
                                    {{ number_format($row['amount'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; padding: 30px; color: #94a3b8;">
                                    Tidak ada mata anggaran penerimaan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" style="text-align: right; text-transform: uppercase; letter-spacing: 0.5px; font-size: 12px;">
                                TOTAL PENERIMAAN:
                            </td>
                            <td class="col-num" style="color: #047857; font-size: 15px; font-weight: 800;">
                                Rp {{ number_format($totalPenerimaan, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif

    {{-- BAGIAN 2: PENGELUARAN --}}
    @if($activeTab === 'all' || $activeTab === 'pengeluaran')
        <div class="report-table-wrapper">
            <div class="report-table-header">
                <div>
                    <div style="font-size: 15px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; border-radius: 6px; background: #e11d48; color: #ffffff; font-size: 11px; font-weight: 800;">2</span>
                        Buku Pembantu Realisasi Mingguan - PENGELUARAN
                    </div>
                    <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                        Periode: {{ \Carbon\Carbon::parse($this->startDate)->format('d/m/Y') }} s.d. {{ \Carbon\Carbon::parse($this->endDate)->format('d/m/Y') }}
                        @if($this->mingguKe) (Minggu ke-{{ $this->mingguKe }}) @endif
                    </div>
                </div>
                <div style="font-size: 13px; font-weight: 700; background: #fff1f2; color: #be123c; padding: 4px 12px; border-radius: 6px; border: 1px solid #fecdd3;">
                    Total: Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                </div>
            </div>

            <div style="overflow-x: auto;">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th style="width: 150px;">No. MA</th>
                            <th>Mata Anggaran</th>
                            <th style="width: 220px; text-align: right;">Jumlah (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengeluaran as $row)
                            @php
                                $isGroup = $row['is_group'];
                                $depth = $row['depth'];
                                $paddingLeft = ($depth * 20) + 16;
                            @endphp
                            <tr class="row-depth-{{ $depth }}">
                                <td class="col-code">{{ $row['kode_akun'] }}</td>
                                <td style="padding-left: {{ $paddingLeft }}px;">
                                    @if(!$isGroup)
                                        <span style="display: inline-block; width: 5px; height: 5px; border-radius: 50%; background: #94a3b8; margin-right: 8px; vertical-align: middle;"></span>
                                    @endif
                                    <span>{{ $row['nama_akun'] }}</span>
                                </td>
                                <td class="col-num" style="{{ $row['amount'] > 0 ? 'font-weight: 700; color: #0f172a;' : 'color: #94a3b8;' }}">
                                    {{ number_format($row['amount'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; padding: 30px; color: #94a3b8;">
                                    Tidak ada mata anggaran pengeluaran.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" style="text-align: right; text-transform: uppercase; letter-spacing: 0.5px; font-size: 12px;">
                                TOTAL PENGELUARAN:
                            </td>
                            <td class="col-num" style="color: #be123c; font-size: 15px; font-weight: 800;">
                                Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif

    {{-- BAGIAN 3: SALDO KAS & BANK --}}
    @if($activeTab === 'all' || $activeTab === 'kas_bank')
        <div class="report-table-wrapper">
            <div class="report-table-header">
                <div>
                    <div style="font-size: 15px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; border-radius: 6px; background: #4f46e5; color: #ffffff; font-size: 11px; font-weight: 800;">3</span>
                        Posisi Saldo Kas & Bank
                    </div>
                    <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                        Saldo Awal (sebelum {{ \Carbon\Carbon::parse($this->startDate)->format('d/m/Y') }}) vs Saldo Akhir (per {{ \Carbon\Carbon::parse($this->endDate)->format('d/m/Y') }})
                    </div>
                </div>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <span style="font-size: 12px; font-weight: 600; background: #f1f5f9; color: #334155; padding: 4px 10px; border-radius: 6px; border: 1px solid #cbd5e1;">
                        Saldo Awal: Rp {{ number_format($totalSaldoAwal, 0, ',', '.') }}
                    </span>
                    <span style="font-size: 12px; font-weight: 700; background: #eef2ff; color: #4338ca; padding: 4px 10px; border-radius: 6px; border: 1px solid #c7d2fe;">
                        Saldo Akhir: Rp {{ number_format($totalSaldoAkhir, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <div style="overflow-x: auto;">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th style="width: 150px;">No. MA</th>
                            <th>Mata Anggaran</th>
                            <th style="width: 200px; text-align: right;">A. Saldo Awal (Rp)</th>
                            <th style="width: 200px; text-align: right;">B. Saldo Akhir (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kasBank as $row)
                            @php
                                $isGroup = $row['is_group'];
                                $depth = $row['depth'];
                                $paddingLeft = ($depth * 20) + 16;
                            @endphp
                            <tr class="row-depth-{{ $depth }}">
                                <td class="col-code">{{ $row['kode_akun'] }}</td>
                                <td style="padding-left: {{ $paddingLeft }}px;">
                                    @if(!$isGroup)
                                        <span style="display: inline-block; width: 5px; height: 5px; border-radius: 50%; background: #94a3b8; margin-right: 8px; vertical-align: middle;"></span>
                                    @endif
                                    <span>{{ $row['nama_akun'] }}</span>
                                </td>
                                <td class="col-num" style="{{ $row['saldo_awal'] != 0 ? 'font-weight: 700; color: #0f172a;' : 'color: #94a3b8;' }}">
                                    {{ number_format($row['saldo_awal'], 0, ',', '.') }}
                                </td>
                                <td class="col-num" style="{{ $row['saldo_akhir'] != 0 ? 'font-weight: 700; color: #0f172a;' : 'color: #94a3b8;' }}">
                                    {{ number_format($row['saldo_akhir'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 30px; color: #94a3b8;">
                                    Tidak ada akun Kas & Bank.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" style="text-align: right; text-transform: uppercase; letter-spacing: 0.5px; font-size: 12px;">
                                TOTAL SALDO KAS & BANK:
                            </td>
                            <td class="col-num" style="font-size: 14px; font-weight: 700; color: #1e293b;">
                                Rp {{ number_format($totalSaldoAwal, 0, ',', '.') }}
                            </td>
                            <td class="col-num" style="font-size: 14px; font-weight: 800; color: #4338ca;">
                                Rp {{ number_format($totalSaldoAkhir, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif
</x-filament-panels::page>
