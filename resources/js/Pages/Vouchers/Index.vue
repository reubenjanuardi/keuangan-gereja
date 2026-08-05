<script setup>
import { Link } from '@inertiajs/vue3';

// Menerima data paginasi dari controller
const props = defineProps({
    vouchers: Object,
});

const formatRupiah = (angka) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
    }).format(angka);
};
</script>

<template>
    <div class="mx-auto max-w-7xl py-10 sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
            <!-- Header & Tombol Tambah -->
            <div class="mb-6 flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-800">
                    Daftar Bukti Kas
                </h2>
                <Link
                    :href="route('vouchers.create')"
                    class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700"
                >
                    + Buat Voucher Baru
                </Link>
            </div>

            <!-- Tabel Data -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 border">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                            >
                                No. Bukti
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                            >
                                Tanggal
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                            >
                                Jenis
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                            >
                                Pihak Terkait
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500"
                            >
                                Total Nominal
                            </th>
                            <th
                                class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500"
                            >
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr
                            v-for="voucher in vouchers.data"
                            :key="voucher.no_bukti"
                            class="hover:bg-gray-50"
                        >
                            <td
                                class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900"
                            >
                                {{ voucher.no_bukti }}
                            </td>
                            <td
                                class="whitespace-nowrap px-6 py-4 text-sm text-gray-500"
                            >
                                {{ voucher.tanggal }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm">
                                <span
                                    :class="
                                        voucher.jenis_voucher === 'Masuk'
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-red-100 text-red-800'
                                    "
                                    class="inline-flex rounded-full px-2 text-xs font-semibold leading-5"
                                >
                                    {{ voucher.jenis_voucher }}
                                </span>
                            </td>
                            <td
                                class="whitespace-nowrap px-6 py-4 text-sm text-gray-500"
                            >
                                {{ voucher.pihak_terkait }}
                            </td>
                            <td
                                class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium text-gray-900"
                            >
                                {{ formatRupiah(voucher.total_nominal) }}
                            </td>
                            <td
                                class="whitespace-nowrap px-6 py-4 text-center text-sm font-medium"
                            >
                                <!-- Tombol aksi cetak akan kita buat selanjutnya -->
                                <button
                                    class="text-indigo-600 hover:text-indigo-900"
                                >
                                    Cetak PDF
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pesan jika kosong -->
            <div
                v-if="vouchers.data.length === 0"
                class="py-8 text-center text-gray-500"
            >
                Belum ada data voucher yang direkam.
            </div>
        </div>
    </div>
</template>
