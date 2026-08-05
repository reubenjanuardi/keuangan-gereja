<x-filament-panels::page>
    <div class="mb-6 rounded-xl bg-white p-6 shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-800">
        {{ $this->form }}
    </div>

    <div class="space-y-6">
        @forelse($this->reportData as $kodeAkun => $transactions)
            @php
                $firstCoa = $transactions->first()->chartOfAccount ?? null;
                $totalMasuk = $transactions->filter(fn($t) => ($t->voucher->jenis_voucher ?? '') === 'Masuk')->sum('nominal');
                $totalKeluar = $transactions->filter(fn($t) => ($t->voucher->jenis_voucher ?? '') === 'Keluar')->sum('nominal');
            @endphp
            <div class="overflow-hidden rounded-xl bg-white shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-800">
                <div class="bg-gray-50 dark:bg-gray-800/50 px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center flex-wrap gap-2">
                    <div>
                        <h3 class="font-bold text-lg text-gray-900 dark:text-gray-100">
                            {{ $kodeAkun }} - {{ $firstCoa->nama_akun ?? 'Akun' }}
                        </h3>
                        <p class="text-xs text-gray-500">Kategori: {{ $firstCoa->kategori ?? '-' }}</p>
                    </div>
                    <div class="text-right text-xs space-x-1">
                        <span class="inline-block bg-green-100 text-green-800 px-2.5 py-1 rounded-md font-semibold">Total Masuk: Rp {{ number_format($totalMasuk, 0, ',', '.') }}</span>
                        <span class="inline-block bg-red-100 text-red-800 px-2.5 py-1 rounded-md font-semibold">Total Keluar: Rp {{ number_format($totalKeluar, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-700 dark:text-gray-300">
                        <thead class="bg-gray-100/70 text-xs uppercase text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">No. Bukti</th>
                                <th class="px-6 py-3">Tanggal</th>
                                <th class="px-6 py-3">Pihak Terkait</th>
                                <th class="px-6 py-3">Uraian</th>
                                <th class="px-6 py-3 text-right">Masuk (Debet)</th>
                                <th class="px-6 py-3 text-right">Keluar (Kredit)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                            @foreach($transactions as $tx)
                                @php
                                    $isMasuk = ($tx->voucher->jenis_voucher ?? '') === 'Masuk';
                                @endphp
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40">
                                    <td class="px-6 py-3 font-semibold text-indigo-600 dark:text-indigo-400">{{ $tx->no_bukti }}</td>
                                    <td class="px-6 py-3">{{ \Carbon\Carbon::parse($tx->voucher->tanggal ?? now())->format('d/m/Y') }}</td>
                                    <td class="px-6 py-3">{{ $tx->voucher->pihak_terkait ?? '-' }}</td>
                                    <td class="px-6 py-3">{{ $tx->uraian }}</td>
                                    <td class="px-6 py-3 text-right font-medium text-green-600">
                                        {{ $isMasuk ? 'Rp ' . number_format($tx->nominal, 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="px-6 py-3 text-right font-medium text-red-600">
                                        {{ !$isMasuk ? 'Rp ' . number_format($tx->nominal, 0, ',', '.') : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-800/80 font-bold border-t border-gray-200 dark:border-gray-700">
                            <tr>
                                <td colspan="4" class="px-6 py-3 text-right">TOTAL:</td>
                                <td class="px-6 py-3 text-right text-green-700">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</td>
                                <td class="px-6 py-3 text-right text-red-700">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @empty
            <div class="rounded-xl bg-white p-12 text-center shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-800">
                <p class="text-gray-500 dark:text-gray-400">Tidak ada transaksi yang ditemukan untuk periode & filter yang dipilih.</p>
            </div>
        @endforelse
    </div>
</x-filament-panels::page>
