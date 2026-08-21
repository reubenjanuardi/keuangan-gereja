<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth?.user || { name: 'Pengguna', email: '' });

const searchQuery = ref('');
const selectedCategory = ref('Semua');
const showModal = ref(false);
const activeModalApp = ref(null);
const userDropdownOpen = ref(false);

const apps = [
    {
        id: 'keuangan',
        name: 'Keuangan',
        subtitle: 'Jurnal, Buku Besar & Anggaran',
        category: 'Keuangan',
        route: '/keuangan',
        active: true,
        badge: 'Aktif',
        description: 'Sistem pencatatan kas, persembahan, voucher masuk/keluar, jurnal umum, buku besar, dan laporan keuangan komprehensif GPIB Hosiana.',
        icon: 'accounting',
        color: 'from-amber-500 to-indigo-600',
    },
    {
        id: 'jemaat',
        name: 'Data Jemaat',
        subtitle: 'Sidi, Baptis & Sektor',
        category: 'Administrasi',
        route: '/jemaat',
        active: false,
        badge: 'Segera Hadir',
        description: 'Basis data terpadu jemaat, anggota sidi, baptisan, mutasi atestasi jemaat, dan sensus keluarga gerejawi.',
        icon: 'members',
        color: 'from-teal-400 to-indigo-600',
    },
    {
        id: 'aset',
        name: 'Aset Gereja',
        subtitle: 'Inventaris & Sarana Fisik',
        category: 'Sarana & Aset',
        route: '/aset',
        active: false,
        badge: 'Segera Hadir',
        description: 'Pencatatan barang inventaris gereja, peralatan multimedia, gedung ibadah, dan monitoring pemeliharaan berkala.',
        icon: 'inventory',
        color: 'from-amber-500 to-purple-600',
    },
    {
        id: 'absensi',
        name: 'Absensi & SDM',
        subtitle: 'Presensi Karyawan & Piket',
        category: 'Administrasi',
        route: '/absensi',
        active: false,
        badge: 'Segera Hadir',
        description: 'Pencatatan daftar hadir staf kantor gereja, koster, dan pengaturan jadwal tugas piket kantor.',
        icon: 'timesheet',
        color: 'from-rose-500 to-sky-600',
    },
    {
        id: 'ibadah',
        name: 'Jadwal & Ibadah',
        subtitle: 'Pelayan Firman & Liturgi',
        category: 'Pelayanan',
        route: '/ibadah',
        active: false,
        badge: 'Segera Hadir',
        description: 'Manajemen jadwal ibadah minggu, pengaturan pelayan firman, organis, kantoria, prokes, dan tata kebaktian.',
        icon: 'calendar',
        color: 'from-emerald-400 to-cyan-600',
    },
    {
        id: 'administrasi',
        name: 'Administrasi Surat',
        subtitle: 'Surat Masuk & Keluar',
        category: 'Administrasi',
        route: '/surat',
        active: false,
        badge: 'Segera Hadir',
        description: 'Penomoran surat resmi gereja, pencatatan surat masuk/keluar, surat keterangan baptis, sidi, dan kearsipan.',
        icon: 'documents',
        color: 'from-sky-500 to-amber-500',
    },
    {
        id: 'pelkat',
        name: 'Pelayanan Kategorial',
        subtitle: 'PA, PT, GP, PKP, PKB, PKLU',
        category: 'Pelayanan',
        route: '/pelkat',
        active: false,
        badge: 'Segera Hadir',
        description: 'Koordinasi program kerja dan agenda kegiatan kategorial anak, teruna, pemuda, wanita, pria, dan lansia.',
        icon: 'community',
        color: 'from-pink-500 to-teal-500',
    },
    {
        id: 'warta',
        name: 'Warta Jemaat',
        subtitle: 'Warta Digital & Pengumuman',
        category: 'Informasi',
        route: '/warta',
        active: false,
        badge: 'Segera Hadir',
        description: 'Penyusunan dan distribusi warta jemaat mingguan, pokok doa syafaat, serta jadwal agenda gereja.',
        icon: 'broadcast',
        color: 'from-blue-600 to-violet-600',
    },
    {
        id: 'multimedia',
        name: 'Multimedia & Studio',
        subtitle: 'Live Streaming & Dokumentasi',
        category: 'Sarana & Aset',
        route: '/multimedia',
        active: false,
        badge: 'Segera Hadir',
        description: 'Manajemen perlengkapan siaran ibadah online, sound system, arsip dokumentasi, dan materi slide kebaktian.',
        icon: 'studio',
        color: 'from-violet-500 to-cyan-500',
    },
    {
        id: 'konseling',
        name: 'Pastoral & Konseling',
        subtitle: 'Kunjungan & Pokok Doa',
        category: 'Pelayanan',
        route: '/pastoral',
        active: false,
        badge: 'Segera Hadir',
        description: 'Pengajuan permohonan konseling jemaat, jadwal kunjungan pendeta/majelis, dan rekap pokok doa.',
        icon: 'pastoral',
        color: 'from-rose-500 to-teal-500',
    },
    {
        id: 'helpdesk',
        name: 'Pusat Bantuan',
        subtitle: 'Panduan & Bantuan IT',
        category: 'Bantuan',
        route: '/helpdesk',
        active: false,
        badge: 'Segera Hadir',
        description: 'Panduan lengkap penggunaan portal, pelaporan kendala teknis sistem, dan kontak tim pendukung TI.',
        icon: 'helpdesk',
        color: 'from-teal-500 to-emerald-600',
    },
    {
        id: 'settings',
        name: 'Pengaturan Portal',
        subtitle: 'Hak Akses & Konfigurasi',
        category: 'Sistem',
        route: '/settings',
        active: false,
        badge: 'Segera Hadir',
        description: 'Pengaturan profil institusi, hak akses modul, log aktivitas pengguna, dan konfigurasi master data.',
        icon: 'settings',
        color: 'from-slate-600 to-blue-700',
    },
];

const categories = ['Semua', 'Keuangan', 'Administrasi', 'Pelayanan', 'Sarana & Aset'];

const filteredApps = computed(() => {
    return apps.filter((app) => {
        const matchesCategory =
            selectedCategory.value === 'Semua' ||
            app.category === selectedCategory.value;

        const query = searchQuery.value.toLowerCase().trim();
        const matchesSearch =
            !query ||
            app.name.toLowerCase().includes(query) ||
            app.subtitle.toLowerCase().includes(query) ||
            app.category.toLowerCase().includes(query) ||
            app.description.toLowerCase().includes(query);

        return matchesCategory && matchesSearch;
    });
});

function handleAppClick(app) {
    if (app.active) {
        window.location.href = app.route;
    } else {
        activeModalApp.value = app;
        showModal.value = true;
    }
}

function closeModal() {
    showModal.value = false;
    activeModalApp.value = null;
}

function handleLogout() {
    router.post(route('logout'));
}
</script>

<template>
    <Head title="App Launcher - Portal GPIB Hosiana" />

    <div class="min-h-screen bg-slate-100 flex flex-col selection:bg-blue-900 selection:text-white font-sans">
        <!-- Top Navbar -->
        <header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-sm backdrop-blur-md bg-white/95">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16 gap-4">
                    <!-- Left: Brand -->
                    <div class="flex items-center gap-3 shrink-0">
                        <img src="/favicon.png" alt="GPIB Hosiana" class="h-9 w-9 object-contain" />
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-900 tracking-tight text-base sm:text-lg">
                                    Portal GPIB Hosiana
                                </span>
                                <span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-50 text-blue-900 border border-blue-200">
                                    App Launcher
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Center: Search Bar -->
                    <div class="flex-1 max-w-md hidden md:block">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Cari modul aplikasi..."
                                class="w-full pl-9 pr-8 py-1.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-900 focus:border-transparent transition-all placeholder:text-slate-400"
                            />
                            <button
                                v-if="searchQuery"
                                @click="searchQuery = ''"
                                class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Right: User Menu -->
                    <div class="flex items-center gap-3">
                        <!-- User Profile Dropdown -->
                        <div class="relative">
                            <button
                                @click="userDropdownOpen = !userDropdownOpen"
                                class="flex items-center gap-2.5 p-1.5 rounded-lg hover:bg-slate-100 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-900"
                            >
                                <div class="h-8 w-8 rounded-full bg-blue-900 text-white font-bold flex items-center justify-center text-xs shadow-sm">
                                    {{ user.name ? user.name.charAt(0).toUpperCase() : 'U' }}
                                </div>
                                <span class="hidden lg:block text-sm font-medium text-slate-700 max-w-[120px] truncate text-left">
                                    {{ user.name }}
                                </span>
                                <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div
                                v-if="userDropdownOpen"
                                @click.outside="userDropdownOpen = false"
                                class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-slate-200 py-1.5 z-50 text-sm"
                            >
                                <div class="px-4 py-2 border-b border-slate-100">
                                    <p class="font-semibold text-slate-900 truncate">{{ user.name }}</p>
                                    <p class="text-xs text-slate-500 truncate">{{ user.email }}</p>
                                </div>
                                <Link
                                    :href="route('profile.edit')"
                                    class="flex items-center gap-2 px-4 py-2 text-slate-700 hover:bg-slate-50 transition-colors"
                                >
                                    <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Profil Akun
                                </Link>
                                <button
                                    @click="handleLogout"
                                    class="w-full flex items-center gap-2 px-4 py-2 text-red-600 hover:bg-red-50 transition-colors text-left"
                                >
                                    <svg class="h-4 w-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Keluar (Logout)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Launcher Content -->
        <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
            <!-- Header Greeting & Search on Mobile -->
            <div class="mb-8 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Aplikasi &amp; Layanan Portal
                    </h1>
                    <p class="mt-1 text-sm sm:text-base text-slate-600">
                        Pilih salah satu aplikasi di bawah untuk membuka modul pengelolaan GPIB Jemaat Hosiana.
                    </p>
                </div>

                <!-- Mobile search -->
                <div class="md:hidden w-full">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Cari modul aplikasi..."
                            class="w-full pl-9 pr-8 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-900"
                        />
                    </div>
                </div>
            </div>

            <!-- Category Filter Pills -->
            <div class="flex items-center gap-2 overflow-x-auto py-2 px-1 mb-8 no-scrollbar">
                <button
                    v-for="cat in categories"
                    :key="cat"
                    @click="selectedCategory = cat"
                    :class="[
                        'px-4 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-all duration-200 focus:outline-none',
                        selectedCategory === cat
                            ? 'bg-blue-900 text-white shadow-sm border border-blue-900'
                            : 'bg-white text-slate-600 hover:bg-slate-50 hover:text-slate-900 border border-slate-200 shadow-2xs',
                    ]"
                >
                    {{ cat }}
                </button>
            </div>

            <!-- Apps Grid (Odoo / Launcher Inspired 6 Columns) -->
            <div
                v-if="filteredApps.length > 0"
                class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6 sm:gap-8 justify-items-center"
            >
                <div
                    v-for="app in filteredApps"
                    :key="app.id"
                    @click="handleAppClick(app)"
                    class="group flex flex-col items-center cursor-pointer w-full max-w-[150px] transition-transform duration-200"
                >
                    <!-- App Card Box -->
                    <div
                        class="relative w-full aspect-square bg-white rounded-3xl p-4 sm:p-5 flex items-center justify-center shadow-[0_4px_20px_rgba(0,0,0,0.05)] border border-slate-100 transition-all duration-300 group-hover:shadow-xl group-hover:-translate-y-1.5 group-hover:border-slate-300"
                        :class="{
                            'ring-2 ring-blue-900/20 shadow-blue-900/5': app.active,
                        }"
                    >
                        <!-- Lock / Coming Soon subtle icon -->
                        <span
                            v-if="!app.active"
                            class="absolute top-2.5 right-2.5 h-4 w-4 text-slate-300 group-hover:text-slate-400 transition-colors"
                            title="Segera Hadir"
                        >
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </span>

                        <!-- App Vector Icon -->
                        <div class="w-full h-full flex items-center justify-center transition-transform duration-300 group-hover:scale-110">
                            <!-- 1. Accounting / Keuangan Icon -->
                            <svg v-if="app.icon === 'accounting'" class="w-12 h-12 sm:w-14 sm:h-14" viewBox="0 0 64 64" fill="none">
                                <circle cx="24" cy="24" r="9" fill="#F59E0B" />
                                <circle cx="40" cy="40" r="9" fill="#0D9488" />
                                <path d="M46 18L18 46" stroke="#4F46E5" stroke-width="7" stroke-linecap="round" />
                            </svg>

                            <!-- 2. Data Jemaat Icon -->
                            <svg v-else-if="app.icon === 'members'" class="w-12 h-12 sm:w-14 sm:h-14" viewBox="0 0 64 64" fill="none">
                                <circle cx="32" cy="22" r="10" fill="#0284C7" />
                                <circle cx="18" cy="26" r="7" fill="#F59E0B" />
                                <circle cx="46" cy="26" r="7" fill="#0D9488" />
                                <path d="M14 50C14 42 22 38 32 38C42 38 50 42 50 50" stroke="#4F46E5" stroke-width="6" stroke-linecap="round" />
                            </svg>

                            <!-- 3. Aset Gereja Icon (Isometric 3D Cube) -->
                            <svg v-else-if="app.icon === 'inventory'" class="w-12 h-12 sm:w-14 sm:h-14" viewBox="0 0 64 64" fill="none">
                                <path d="M32 10L50 20L32 30L14 20L32 10Z" fill="#F59E0B" />
                                <path d="M14 20L32 30V52L14 42V20Z" fill="#EA580C" />
                                <path d="M50 20L32 30V52L50 42V20Z" fill="#7C3AED" />
                            </svg>

                            <!-- 4. Absensi & SDM Icon (Timesheet / Clock) -->
                            <svg v-else-if="app.icon === 'timesheet'" class="w-12 h-12 sm:w-14 sm:h-14" viewBox="0 0 64 64" fill="none">
                                <circle cx="32" cy="32" r="22" fill="#0284C7" />
                                <path d="M32 10C44.15 10 54 19.85 54 32C54 44.15 44.15 54 32 54" stroke="#F43F5E" stroke-width="5" stroke-linecap="round" />
                                <circle cx="32" cy="32" r="4" fill="white" />
                                <path d="M32 32L44 20" stroke="white" stroke-width="4" stroke-linecap="round" />
                            </svg>

                            <!-- 5. Jadwal & Ibadah Icon (Calendar / Cross) -->
                            <svg v-else-if="app.icon === 'calendar'" class="w-12 h-12 sm:w-14 sm:h-14" viewBox="0 0 64 64" fill="none">
                                <rect x="12" y="14" width="40" height="38" rx="8" fill="#10B981" />
                                <rect x="12" y="14" width="40" height="12" rx="4" fill="#047857" />
                                <circle cx="22" cy="11" r="3" fill="#F59E0B" />
                                <circle cx="42" cy="11" r="3" fill="#F59E0B" />
                                <path d="M32 32V44M26 38H38" stroke="white" stroke-width="4" stroke-linecap="round" />
                            </svg>

                            <!-- 6. Administrasi Surat Icon (Documents) -->
                            <svg v-else-if="app.icon === 'documents'" class="w-12 h-12 sm:w-14 sm:h-14" viewBox="0 0 64 64" fill="none">
                                <rect x="14" y="18" width="28" height="36" rx="4" fill="#0284C7" transform="rotate(-6 14 18)" />
                                <rect x="22" y="12" width="28" height="36" rx="4" fill="#F59E0B" />
                                <path d="M28 22H42M28 28H38M28 34H42" stroke="white" stroke-width="3" stroke-linecap="round" />
                            </svg>

                            <!-- 7. Pelayanan Kategorial Icon (Partnership / Handshake) -->
                            <svg v-else-if="app.icon === 'community'" class="w-12 h-12 sm:w-14 sm:h-14" viewBox="0 0 64 64" fill="none">
                                <path d="M12 28L26 42L40 28L26 14L12 28Z" fill="#0D9488" />
                                <path d="M24 40L38 54L52 40L38 26L24 40Z" fill="#7C3AED" />
                            </svg>

                            <!-- 8. Warta Jemaat Icon (Paper plane / Broadcast) -->
                            <svg v-else-if="app.icon === 'broadcast'" class="w-12 h-12 sm:w-14 sm:h-14" viewBox="0 0 64 64" fill="none">
                                <path d="M10 32L54 14L36 54L28 36L10 32Z" fill="#2563EB" />
                                <path d="M28 36L54 14" stroke="white" stroke-width="3" stroke-linecap="round" />
                                <path d="M28 36V48L34 42" fill="#1D4ED8" />
                            </svg>

                            <!-- 9. Multimedia & Studio Icon -->
                            <svg v-else-if="app.icon === 'studio'" class="w-12 h-12 sm:w-14 sm:h-14" viewBox="0 0 64 64" fill="none">
                                <path d="M18 16L46 44M46 16L18 44" stroke="#7C3AED" stroke-width="7" stroke-linecap="round" />
                                <path d="M24 10L10 24M54 40L40 54" stroke="#0284C7" stroke-width="6" stroke-linecap="round" />
                            </svg>

                            <!-- 10. Pastoral & Konseling Icon -->
                            <svg v-else-if="app.icon === 'pastoral'" class="w-12 h-12 sm:w-14 sm:h-14" viewBox="0 0 64 64" fill="none">
                                <path d="M32 50C32 50 14 38 14 26C14 18 20 14 26 14C29 14 32 17 32 17C32 17 35 14 38 14C44 14 50 18 50 26C50 38 32 50 32 50Z" fill="#F43F5E" />
                                <path d="M32 22V36M25 29H39" stroke="white" stroke-width="3.5" stroke-linecap="round" />
                            </svg>

                            <!-- 11. Helpdesk Icon -->
                            <svg v-else-if="app.icon === 'helpdesk'" class="w-12 h-12 sm:w-14 sm:h-14" viewBox="0 0 64 64" fill="none">
                                <rect x="12" y="12" width="40" height="40" rx="10" fill="#0D9488" />
                                <path d="M32 20V44M20 32H44" stroke="white" stroke-width="7" stroke-linecap="round" />
                            </svg>

                            <!-- 12. Settings Icon -->
                            <svg v-else class="w-12 h-12 sm:w-14 sm:h-14" viewBox="0 0 64 64" fill="none">
                                <rect x="14" y="14" width="16" height="16" rx="4" fill="#7C3AED" />
                                <rect x="34" y="14" width="16" height="16" rx="4" fill="#EF4444" />
                                <rect x="14" y="34" width="16" height="16" rx="4" fill="#0284C7" />
                                <rect x="34" y="34" width="16" height="16" rx="4" fill="#10B981" />
                            </svg>
                        </div>
                    </div>

                    <!-- App Title Label -->
                    <span class="mt-3 text-sm sm:text-base font-semibold text-slate-800 text-center tracking-tight group-hover:text-blue-900 transition-colors truncate w-full">
                        {{ app.name }}
                    </span>
                    <span class="text-[11px] text-slate-500 text-center truncate w-full hidden sm:block">
                        {{ app.subtitle }}
                    </span>
                </div>
            </div>

            <!-- Empty State for Search -->
            <div v-else class="text-center py-16 bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">
                <div class="h-12 w-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto text-slate-400 mb-3">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-slate-900">Aplikasi tidak ditemukan</h3>
                <p class="text-sm text-slate-500 mt-1">Tidak ada modul yang cocok dengan kata kunci "{{ searchQuery }}".</p>
                <button
                    @click="searchQuery = ''; selectedCategory = 'Semua'"
                    class="mt-4 inline-flex items-center px-3 py-1.5 text-xs font-semibold text-blue-900 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors"
                >
                    Reset Pencarian
                </button>
            </div>
        </main>

        <!-- Footer -->
        <footer class="border-t border-slate-200 bg-white py-6 mt-auto text-center text-xs text-slate-500">
            <div class="max-w-7xl mx-auto px-4">
                &copy; 2026 GPIB Jemaat Hosiana Jakarta. Portal Terintegrasi Administrasi, Keuangan &amp; Pelayanan.
            </div>
        </footer>

        <!-- Modal: Coming Soon Module Notification -->
        <div
            v-if="showModal"
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4"
            @click.self="closeModal"
        >
            <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl p-6 sm:p-8 text-center animate-in fade-in zoom-in-95 duration-200 border border-slate-100">
                <button
                    @click="closeModal"
                    class="absolute top-4 right-4 p-1 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="mx-auto mb-4 h-16 w-16 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 shadow-sm">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>

                <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 mb-2">
                    Tahap Pengembangan
                </div>

                <h3 class="text-xl font-bold text-slate-900 tracking-tight">
                    Modul {{ activeModalApp?.name }}
                </h3>

                <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                    {{ activeModalApp?.description }}
                </p>

                <div class="mt-4 p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-500 text-left flex items-start gap-2.5">
                    <svg class="h-4 w-4 text-blue-900 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>
                        Saat ini sistem yang sudah aktif beroperasi adalah <strong>Modul Keuangan</strong>. Modul lainnya akan diaktifkan secara bertahap.
                    </span>
                </div>

                <div class="mt-6 flex flex-col sm:flex-row gap-3">
                    <a
                        href="/keuangan"
                        class="flex-1 inline-flex items-center justify-center px-4 py-2.5 bg-blue-900 hover:bg-blue-800 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm"
                    >
                        Buka Modul Keuangan
                    </a>
                    <button
                        @click="closeModal"
                        class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition-colors"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
