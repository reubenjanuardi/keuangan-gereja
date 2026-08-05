<script setup>
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

// Menerima data coaList dari Controller
const props = defineProps({
    coaList: Array,
});

const formattedCoaList = computed(() => {
    return props.coaList.map((coa) => {
        // Hitung kedalaman level berdasarkan jumlah titik (misal: 21.01 = 1 titik)
        const depth = (coa.kode_akun.match(/\./g) || []).length;

        // Buat jarak indentasi menggunakan Non-Breaking Space (\u00A0)
        const indent = '\u00A0\u00A0\u00A0\u00A0'.repeat(depth);

        let label = '';
        if (!coa.is_postable) {
            // Jika tidak postable, beri icon Folder
            label = `${indent}📂 ${coa.kode_akun} - ${coa.nama_akun}`;
        } else {
            // Jika postable, beri icon panah turunan
            label = `${indent}↳ ${coa.kode_akun} - ${coa.nama_akun}`;
        }

        return {
            ...coa,
            display_label: label,
        };
    });
});

// Inisialisasi state form menggunakan Inertia
const form = useForm({
    no_bukti: '',
    tanggal: '',
    pihak_terkait: '',
    jenis_voucher: 'Masuk',
    rincian: [
        { kode_akun: '', uraian: '', nominal: 0 }, // Baris pertama default
    ],
});

// Fitur Reaktif: Menambah baris rincian
const tambahBaris = () => {
    form.rincian.push({ kode_akun: '', uraian: '', nominal: 0 });
};

// Fitur Reaktif: Menghapus baris rincian
const hapusBaris = (index) => {
    if (form.rincian.length > 1) {
        form.rincian.splice(index, 1);
    }
};

// Computed property untuk menghitung total secara real-time di UI
const totalNominal = computed(() => {
    return form.rincian.reduce(
        (total, item) => total + Number(item.nominal || 0),
        0,
    );
});

// Format Rupiah untuk UI
const formatRupiah = (angka) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
    }).format(angka);
};

// Fungsi Submit
const submit = () => {
    // Validasi sederhana di sisi client untuk memastikan format no_bukti kontinu (tanpa koma)
    if (form.no_bukti.includes(',')) {
        alert('Nomor Bukti tidak boleh mengandung tanda koma.');
        return;
    }

    form.post(route('vouchers.store'), {
        onSuccess: () => {
            form.reset();
            alert('Voucher berhasil disimpan!');
        },
    });
};
</script>

<template>
    <div class="mx-auto max-w-7xl py-10 sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
            <h2 class="mb-6 text-2xl font-bold text-gray-800">
                Buat Bukti Kas
            </h2>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- HEADER VOUCHER -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Nomor Bukti</label
                        >
                        <input
                            v-model="form.no_bukti"
                            type="text"
                            placeholder="BKM20260728001 (Tanpa koma)"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        />
                        <div
                            v-if="form.errors.no_bukti"
                            class="mt-1 text-sm text-red-500"
                        >
                            {{ form.errors.no_bukti }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Tanggal</label
                        >
                        <input
                            v-model="form.tanggal"
                            type="date"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        />
                        <div
                            v-if="form.errors.tanggal"
                            class="mt-1 text-sm text-red-500"
                        >
                            {{ form.errors.tanggal }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Terima Dari / Dibayar Ke</label
                        >
                        <input
                            v-model="form.pihak_terkait"
                            type="text"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Jenis Voucher</label
                        >
                        <select
                            v-model="form.jenis_voucher"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="Masuk">Kas Masuk</option>
                            <option value="Keluar">Kas Keluar</option>
                        </select>
                    </div>
                </div>

                <hr class="my-6 border-gray-200" />

                <!-- DETAIL TRANSAKSI (DYNAMIC ROWS) -->
                <h3 class="mb-4 text-lg font-semibold text-gray-700">
                    Rincian Transaksi
                </h3>

                <div class="space-y-4">
                    <div
                        v-for="(item, index) in form.rincian"
                        :key="index"
                        class="flex items-start gap-4 rounded-md border bg-gray-50 p-4"
                    >
                        <div class="flex-1">
                            <label
                                class="block text-xs font-medium uppercase text-gray-500"
                                >Mata Anggaran (CoA)</label
                            >

                            <!-- Tambahkan class font-mono agar spasi indentasinya sejajar rata -->
                            <select
                                v-model="item.kode_akun"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 font-sans text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="" disabled>
                                    -- Pilih Mata Anggaran --
                                </option>

                                <!-- Looping dari computed property -->
                                <option
                                    v-for="coa in formattedCoaList"
                                    :key="coa.kode_akun"
                                    :value="coa.kode_akun"
                                    :disabled="!coa.is_postable"
                                    :class="
                                        !coa.is_postable
                                            ? 'bg-gray-100 font-bold'
                                            : ''
                                    "
                                >
                                    {{ coa.display_label }}
                                </option>
                            </select>
                        </div>

                        <div class="flex-1">
                            <label
                                class="block text-xs font-medium uppercase text-gray-500"
                                >Uraian / Keterangan</label
                            >
                            <input
                                v-model="item.uraian"
                                type="text"
                                required
                                placeholder="Rincian spesifik..."
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>

                        <div class="w-48">
                            <label
                                class="block text-xs font-medium uppercase text-gray-500"
                                >Nominal (Rp)</label
                            >
                            <input
                                v-model="item.nominal"
                                type="number"
                                min="1"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>

                        <div class="pt-6">
                            <button
                                @click.prevent="hapusBaris(index)"
                                v-if="form.rincian.length > 1"
                                type="button"
                                class="px-2 text-xl font-bold text-red-500 transition hover:text-red-700"
                            >
                                &times;
                            </button>
                        </div>
                    </div>
                </div>

                <button
                    @click.prevent="tambahBaris"
                    type="button"
                    class="mt-4 text-sm font-semibold text-indigo-600 hover:text-indigo-800"
                >
                    + Tambah Baris Rincian
                </button>

                <!-- TOTAL & SUBMIT -->
                <div
                    class="mt-8 flex items-center justify-between rounded-md bg-gray-100 p-4"
                >
                    <div>
                        <span class="font-medium text-gray-600"
                            >Total Keseluruhan:
                        </span>
                        <span class="text-2xl font-bold text-gray-900">{{
                            formatRupiah(totalNominal)
                        }}</span>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-md bg-indigo-600 px-6 py-2 text-white transition hover:bg-indigo-700 disabled:opacity-50"
                    >
                        {{
                            form.processing ? 'Menyimpan...' : 'Simpan Voucher'
                        }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
