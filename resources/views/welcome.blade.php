<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Portal Terintegrasi GPIB Hosiana</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col bg-slate-50 text-slate-900 font-sans antialiased">
    <!-- Navbar -->
    <header class="border-b border-slate-200 bg-white/80 backdrop-blur-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('favicon.png') }}" alt="Logo GPIB Hosiana" class="h-9 w-9 object-contain">
                    <span class="text-lg font-semibold text-slate-900 tracking-tight">Portal GPIB Hosiana</span>
                </div>
                <div>
                    <a href="/login" class="inline-flex items-center justify-center rounded-lg bg-blue-900 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-800 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-900 focus:ring-offset-2">
                        Masuk Portal
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1">
        <!-- Hero Section -->
        <section class="relative overflow-hidden py-16 sm:py-24 lg:py-32">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="max-w-3xl mx-auto text-center flex flex-col items-center">
                    <div class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-medium text-slate-600 shadow-sm mb-6">
                        <span class="inline-block h-2 w-2 rounded-full bg-blue-900 mr-2"></span>
                        Portal Terintegrasi GPIB Jemaat Hosiana Jakarta
                    </div>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 leading-tight">
                        Sistem Informasi &amp; Manajemen Terintegrasi
                    </h1>
                    <p class="mt-6 text-base sm:text-lg text-slate-600 leading-relaxed max-w-2xl">
                        Satu platform sentral untuk pengelolaan data jemaat, administrasi pelayanan, jadwal ibadah, hingga transparansi laporan keuangan GPIB Jemaat Hosiana Jakarta.
                    </p>
                    <div class="mt-8 flex items-center justify-center gap-4">
                        <a href="/dashboard" class="inline-flex items-center justify-center rounded-lg bg-blue-900 px-6 py-3 text-base font-semibold text-white shadow-sm hover:bg-blue-800 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-900 focus:ring-offset-2">
                            Akses Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Grid Section -->
        <section class="py-12 sm:py-16 bg-white border-y border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
                    <!-- Feature 1: Administrasi & Data Jemaat -->
                    <div class="bg-white border border-slate-200 rounded-xl p-6 sm:p-8 shadow-sm flex flex-col justify-between hover:border-slate-300 transition-colors duration-200">
                        <div>
                            <div class="h-10 w-10 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-900 mb-5">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-slate-900 tracking-tight">Administrasi &amp; Data Jemaat</h3>
                            <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                                Pengelolaan basis data anggota sidi, baptis, mutasi, dan pencatatan surat-menyurat secara digital.
                            </p>
                        </div>
                    </div>

                    <!-- Feature 2: Keuangan & Aset -->
                    <div class="bg-white border border-slate-200 rounded-xl p-6 sm:p-8 shadow-sm flex flex-col justify-between hover:border-slate-300 transition-colors duration-200">
                        <div>
                            <div class="h-10 w-10 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-900 mb-5">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6H2.25m0 0v10.5m0-10.5h19.5m0 0v10.5m0-10.5h-1.5a.75.75 0 0 1-.75-.75V4.5m-15 0a.75.75 0 0 1 .75-.75h13.5a.75.75 0 0 1 .75.75m-15 0v.75m16.5-.75v.75m-16.5 0h16.5M6 9h.75v.75H6V9Zm3.75 0h.75v.75H9.75V9Zm3.75 0h.75v.75h-.75V9Zm3.75 0h.75v.75h-.75V9Zm-11.25 3h.75v.75H6V12Zm3.75 0h.75v.75H9.75V12Zm3.75 0h.75v.75h-.75V12Zm3.75 0h.75v.75h-.75V12Zm-11.25 3h.75v.75H6V15Zm3.75 0h.75v.75H9.75V15Zm3.75 0h.75v.75h-.75V15Zm3.75 0h.75v.75h-.75V15Z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-slate-900 tracking-tight">Keuangan &amp; Aset</h3>
                            <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                                Pencatatan jurnal, buku besar, pemeliharaan inventaris, dan pembuatan laporan anggaran otomatis.
                            </p>
                        </div>
                    </div>

                    <!-- Feature 3: Jadwal & Pelayanan -->
                    <div class="bg-white border border-slate-200 rounded-xl p-6 sm:p-8 shadow-sm flex flex-col justify-between hover:border-slate-300 transition-colors duration-200">
                        <div>
                            <div class="h-10 w-10 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-900 mb-5">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-slate-900 tracking-tight">Jadwal &amp; Pelayanan</h3>
                            <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                                Manajemen jadwal ibadah, pengaturan pelayan firman, dan koordinasi agenda kategorial.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200 bg-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
                <p class="text-sm text-slate-500">
                    &copy; 2026 GPIB Jemaat Hosiana Jakarta. All rights reserved.
                </p>
                <p class="text-sm text-slate-500">
                    Jl. Rajawali Selatan V No. 7, Jakarta Pusat 10772
                </p>
            </div>
        </div>
    </footer>
</body>
</html>
