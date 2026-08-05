<script setup>
import { Link, useForm } from '@inertiajs/vue3';

defineProps({ coaList: Object });

const form = useForm({});

const hapus = (kode_akun) => {
    if (
        confirm(
            'Yakin ingin menghapus akun ini? Pastikan akun tidak terikat transaksi.',
        )
    ) {
        form.delete(route('coa.destroy', kode_akun));
    }
};
</script>

<template>
    <div class="mx-auto max-w-7xl py-10 sm:px-6 lg:px-8">
        <div class="bg-white p-6 shadow-sm sm:rounded-lg">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-800">
                    Master Data: Chart of Accounts
                </h2>
                <Link
                    :href="route('coa.create')"
                    class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700"
                >
                    + Tambah Akun
                </Link>
            </div>

            <table class="min-w-full divide-y divide-gray-200 border">
                <thead class="bg-gray-50">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500"
                        >
                            Kode Akun
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500"
                        >
                            Nama Akun
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500"
                        >
                            Kategori
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500"
                        >
                            Status
                        </th>
                        <th
                            class="px-6 py-3 text-center text-xs font-medium uppercase text-gray-500"
                        >
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr v-for="item in coaList.data" :key="item.kode_akun">
                        <td class="px-6 py-4 text-sm font-bold text-gray-900">
                            {{ item.kode_akun }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <!-- Indentasi visual untuk anak akun -->
                            <span
                                :class="{
                                    'ml-4 text-gray-500': item.parent_code,
                                }"
                                >{{ item.nama_akun }}</span
                            >
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ item.kategori }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span
                                v-if="item.is_postable"
                                class="rounded bg-green-100 px-2 py-1 text-xs text-green-800"
                                >Postable</span
                            >
                            <span
                                v-else
                                class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-800"
                                >Induk (Grup)</span
                            >
                        </td>
                        <td
                            class="space-x-4 px-6 py-4 text-center text-sm font-medium"
                        >
                            <Link
                                :href="route('coa.edit', item.kode_akun)"
                                class="text-indigo-600 hover:text-indigo-900"
                                >Edit</Link
                            >
                            <button
                                @click="hapus(item.kode_akun)"
                                class="text-red-600 hover:text-red-900"
                            >
                                Hapus
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
