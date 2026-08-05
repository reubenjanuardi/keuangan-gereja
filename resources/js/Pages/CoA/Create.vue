<script setup>
import { useForm, Link } from '@inertiajs/vue3';

defineProps({ parents: Array });

const form = useForm({
    kode_akun: '',
    nama_akun: '',
    kategori: 'Penerimaan',
    parent_code: '',
    is_postable: true,
});

const submit = () => {
    form.post(route('coa.store'));
};
</script>

<template>
    <div class="mx-auto max-w-3xl py-10 sm:px-6 lg:px-8">
        <div class="bg-white p-6 shadow-sm sm:rounded-lg">
            <h2 class="mb-6 text-xl font-bold text-gray-800">
                Tambah Mata Anggaran
            </h2>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700"
                        >Kode Akun</label
                    >
                    <input
                        v-model="form.kode_akun"
                        type="text"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700"
                        >Nama Akun / Uraian</label
                    >
                    <input
                        v-model="form.nama_akun"
                        type="text"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300"
                    />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Kategori Utama</label
                        >
                        <select
                            v-model="form.kategori"
                            class="mt-1 block w-full rounded-md border-gray-300"
                        >
                            <option value="Penerimaan">Penerimaan</option>
                            <option value="Pengeluaran">Pengeluaran</option>
                            <option value="Kas & Bank">Kas & Bank</option>
                            <option value="Hutang / Piutang">
                                Hutang / Piutang
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Akun Induk (Opsional)</label
                        >
                        <select
                            v-model="form.parent_code"
                            class="mt-1 block w-full rounded-md border-gray-300"
                        >
                            <option value="">-- Tidak Ada Induk --</option>
                            <option
                                v-for="p in parents"
                                :key="p.kode_akun"
                                :value="p.kode_akun"
                            >
                                {{ p.kode_akun }} - {{ p.nama_akun }}
                            </option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="mt-4 flex items-center">
                        <input
                            type="checkbox"
                            v-model="form.is_postable"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm"
                        />
                        <span class="ml-2 text-sm text-gray-600"
                            >Akun ini bisa dipilih saat input transaksi
                            (Postable)</span
                        >
                    </label>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <Link
                        :href="route('coa.index')"
                        class="rounded-md border px-4 py-2 text-gray-600 hover:bg-gray-50"
                        >Batal</Link
                    >
                    <button
                        type="submit"
                        class="rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700"
                    >
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
