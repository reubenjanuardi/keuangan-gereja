<x-filament-panels::page>
    <div class="mb-6 rounded-xl bg-white p-6 shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-800">
        {{ $this->form }}
    </div>

    <div class="overflow-hidden rounded-xl bg-white shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-800">
        <div class="bg-gray-50 dark:bg-gray-800/50 px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center">
            <h3 class="font-bold text-lg text-gray-900 dark:text-gray-100">
                Laporan Jurnal Transaksi
            </h3>
            <span class="text-xs text-gray-500 font-semibold">Total: {{ count($this->reportData) }} Baris Transaksi</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-700 dark:text-gray-300">
                <thead class="bg-gray-100/70 text-xs uppercase text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">No. Bukti</th>
                        <th class="px-6 py-3">Jenis</th>
                        <th class="px-6 py-3">Pihak Terkait</th>
                        <th class="px-6 py-3">Kode & Nama Akun</th>
                        <th class="px-6 py-3">Uraian</th>
                        <th class="px-6 py-3 text-right">Nominal (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                    @php $grandTotal = 0; @endphp
                    @forelse($this->reportData as $tx)
                        @php
                            $grandTotal += $tx->nominal;
                            $isMasuk = ($tx->voucher->jenis_voucher ?? '') === 'Masuk';
                        @endphp
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40">
                            <td class="px-6 py-3">{{ \Carbon\Carbon::parse($tx->voucher->tanggal ?? now())->format('d/m/Y') }}</td>
                            <td class="px-6 py-3 font-semibold text-indigo-600 dark:text-indigo-400">{{ $tx->no_bukti }}</td>
                            <td class="px-6 py-3">
                                <span class="px-2 py-0.5 text-xs rounded font-semibold {{ $isMasuk ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $tx->voucher->jenis_voucher ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-3">{{ $tx->voucher->pihak_terkait ?? '-' }}</td>
                            <td class="px-6 py-3">
                                <span class="font-mono text-xs text-gray-600">{{ $tx->kode_akun }}</span> - {{ $tx->chartOfAccount->nama_akun ?? '-' }}
                            </td>
                            <td class="px-6 py-3">{{ $tx->uraian }}</td>
                            <td class="px-6 py-3 text-right font-medium">
                                Rp {{ number_format($tx->nominal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">Tidak ada transaksi yang ditemukan untuk periode dan filter yang dipilih.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if(count($this->reportData) > 0)
                    <tfoot class="bg-gray-50 dark:bg-gray-800/80 font-bold border-t border-gray-200 dark:border-gray-700">
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-right uppercase">Grand Total Nominal:</td>
                            <td class="px-6 py-4 text-right text-indigo-600 dark:text-indigo-400 text-base">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</x-filament-panels::page>
