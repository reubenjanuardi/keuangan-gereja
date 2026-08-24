<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RbacSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ─── 1. DAFTAR PERMISSION ───────────────────────────────────────────────
        $permissions = [
            // Modul Akses
            [
                'name' => 'module.keuangan',
                'display_name' => 'Akses Modul Keuangan',
                'group_name' => 'Modul Sistem',
                'description' => 'Izin membuka aplikasi Keuangan di App Launcher & panel /keuangan',
            ],
            [
                'name' => 'module.settings',
                'display_name' => 'Akses Pengaturan Portal',
                'group_name' => 'Modul Sistem',
                'description' => 'Izin membuka aplikasi Pengaturan Portal di App Launcher & panel /settings',
            ],
            [
                'name' => 'module.jemaat',
                'display_name' => 'Akses Data Jemaat',
                'group_name' => 'Modul Sistem',
                'description' => 'Izin membuka aplikasi Data Jemaat di App Launcher',
            ],
            [
                'name' => 'module.aset',
                'display_name' => 'Akses Aset Gereja',
                'group_name' => 'Modul Sistem',
                'description' => 'Izin membuka aplikasi Aset Gereja di App Launcher',
            ],
            [
                'name' => 'module.absensi',
                'display_name' => 'Akses Absensi & SDM',
                'group_name' => 'Modul Sistem',
                'description' => 'Izin membuka aplikasi Absensi & SDM di App Launcher',
            ],
            [
                'name' => 'module.ibadah',
                'display_name' => 'Akses Jadwal & Ibadah',
                'group_name' => 'Modul Sistem',
                'description' => 'Izin membuka aplikasi Jadwal & Ibadah di App Launcher',
            ],
            [
                'name' => 'module.administrasi',
                'display_name' => 'Akses Administrasi Surat',
                'group_name' => 'Modul Sistem',
                'description' => 'Izin membuka aplikasi Administrasi Surat di App Launcher',
            ],
            [
                'name' => 'module.pelkat',
                'display_name' => 'Akses Pelayanan Kategorial',
                'group_name' => 'Modul Sistem',
                'description' => 'Izin membuka aplikasi Pelayanan Kategorial di App Launcher',
            ],
            [
                'name' => 'module.warta',
                'display_name' => 'Akses Warta Jemaat',
                'group_name' => 'Modul Sistem',
                'description' => 'Izin membuka aplikasi Warta Jemaat di App Launcher',
            ],
            [
                'name' => 'module.multimedia',
                'display_name' => 'Akses Multimedia & Studio',
                'group_name' => 'Modul Sistem',
                'description' => 'Izin membuka aplikasi Multimedia di App Launcher',
            ],
            [
                'name' => 'module.konseling',
                'display_name' => 'Akses Pastoral & Konseling',
                'group_name' => 'Modul Sistem',
                'description' => 'Izin membuka aplikasi Pastoral & Konseling di App Launcher',
            ],
            [
                'name' => 'module.helpdesk',
                'display_name' => 'Akses Pusat Bantuan',
                'group_name' => 'Modul Sistem',
                'description' => 'Izin membuka modul Pusat Bantuan di App Launcher',
            ],

            // Keuangan - Voucher
            [
                'name' => 'keuangan.voucher.view',
                'display_name' => 'Lihat Bukti Voucher',
                'group_name' => 'Keuangan - Voucher',
                'description' => 'Melihat daftar dan rincian transaksi bukti voucher',
            ],
            [
                'name' => 'keuangan.voucher.create',
                'display_name' => 'Posting / Buat Voucher Baru',
                'group_name' => 'Keuangan - Voucher',
                'description' => 'Membuat dan mencatat bukti voucher kas/bank baru',
            ],
            [
                'name' => 'keuangan.voucher.edit',
                'display_name' => 'Ubah / Edit Voucher',
                'group_name' => 'Keuangan - Voucher',
                'description' => 'Mengubah data bukti voucher dan transaksi yang sudah tersimpan',
            ],
            [
                'name' => 'keuangan.voucher.delete',
                'display_name' => 'Hapus Voucher',
                'group_name' => 'Keuangan - Voucher',
                'description' => 'Menghapus bukti voucher dan seluruh transaksi terkait',
            ],
            [
                'name' => 'keuangan.voucher.print',
                'display_name' => 'Cetak / Unduh Bukti Voucher PDF',
                'group_name' => 'Keuangan - Voucher',
                'description' => 'Mencetak dokumen resmi bukti kas/bank masuk/keluar',
            ],

            // Keuangan - Akun COA
            [
                'name' => 'keuangan.coa.view',
                'display_name' => 'Lihat Bagan Akun (COA)',
                'group_name' => 'Keuangan - Master Akun',
                'description' => 'Melihat bagan akun mata anggaran gereja',
            ],
            [
                'name' => 'keuangan.coa.manage',
                'display_name' => 'Kelola Bagan Akun (Tambah/Ubah/Hapus)',
                'group_name' => 'Keuangan - Master Akun',
                'description' => 'Menambah, mengubah, dan menghapus kode mata anggaran serta budget PKA',
            ],

            // Keuangan - Laporan
            [
                'name' => 'keuangan.laporan.view',
                'display_name' => 'Lihat Laporan Keuangan',
                'group_name' => 'Keuangan - Laporan',
                'description' => 'Membuka laporan Buku Besar, Jurnal, dan Realisasi Mingguan',
            ],
            [
                'name' => 'keuangan.laporan.export',
                'display_name' => 'Unduh / Ekspor Laporan PDF',
                'group_name' => 'Keuangan - Laporan',
                'description' => 'Mengunduh laporan keuangan dalam format PDF',
            ],

            // Pengaturan Portal
            [
                'name' => 'portal.user.view',
                'display_name' => 'Lihat Daftar Pengguna',
                'group_name' => 'Pengaturan Portal',
                'description' => 'Melihat daftar seluruh pengguna portal',
            ],
            [
                'name' => 'portal.user.manage',
                'display_name' => 'Kelola Pengguna',
                'group_name' => 'Pengaturan Portal',
                'description' => 'Menambah, mengubah status, mereset password, dan menetapkan peran pengguna',
            ],
            [
                'name' => 'portal.role.view',
                'display_name' => 'Lihat Peran & Hak Akses',
                'group_name' => 'Pengaturan Portal',
                'description' => 'Melihat daftar peran dan distribusi izin',
            ],
            [
                'name' => 'portal.role.manage',
                'display_name' => 'Kelola Peran & Hak Akses',
                'group_name' => 'Pengaturan Portal',
                'description' => 'Menambah dan mengubah peran serta menetapkan matriks izin',
            ],
            [
                'name' => 'portal.log.view',
                'display_name' => 'Lihat Log Aktivitas',
                'group_name' => 'Pengaturan Portal',
                'description' => 'Melihat riwayat audit trail dan catatan aktivitas pengguna sistem',
            ],
            [
                'name' => 'portal.config.manage',
                'display_name' => 'Kelola Profil & Konfigurasi Gereja',
                'group_name' => 'Pengaturan Portal',
                'description' => 'Mengubah data profil gereja, logo, dan konfigurasi master portal',
            ],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm['name'], 'guard_name' => 'web'],
                [
                    'display_name' => $perm['display_name'],
                    'group_name' => $perm['group_name'],
                    'description' => $perm['description'],
                ]
            );
        }

        // ─── 2. DAFTAR PERAN (ROLES) ───────────────────────────────────────────
        $roleSuperAdmin = Role::firstOrCreate(
            ['name' => 'Super Admin', 'guard_name' => 'web'],
            [
                'display_name' => 'Super Administrator',
                'description' => 'Memiliki hak akses penuh ke seluruh modul, sistem RBAC, audit log, dan pengaturan master.',
            ]
        );
        $roleSuperAdmin->syncPermissions(Permission::all());

        $roleBendahara = Role::firstOrCreate(
            ['name' => 'Bendahara Keuangan', 'guard_name' => 'web'],
            [
                'display_name' => 'Bendahara Keuangan',
                'description' => 'Memiliki hak akses penuh operasi CRUD di Modul Keuangan (posting, ubah, hapus, kelola COA, cetak laporan).',
            ]
        );
        $roleBendahara->syncPermissions([
            'module.keuangan',
            'keuangan.voucher.view',
            'keuangan.voucher.create',
            'keuangan.voucher.edit',
            'keuangan.voucher.delete',
            'keuangan.voucher.print',
            'keuangan.coa.view',
            'keuangan.coa.manage',
            'keuangan.laporan.view',
            'keuangan.laporan.export',
        ]);

        $roleOperator = Role::firstOrCreate(
            ['name' => 'Operator Kasir', 'guard_name' => 'web'],
            [
                'display_name' => 'Operator Kas & Transaksi (User A)',
                'description' => 'Hanya dapat melihat dan memposting voucher baru di Modul Keuangan. Tidak diizinkan mengedit/menghapus transaksi atau membuka Pengaturan Portal.',
            ]
        );
        $roleOperator->syncPermissions([
            'module.keuangan',
            'keuangan.voucher.view',
            'keuangan.voucher.create',
            'keuangan.voucher.print',
            'keuangan.coa.view',
            'keuangan.laporan.view',
        ]);

        $roleMajelis = Role::firstOrCreate(
            ['name' => 'Majelis Peninjau', 'guard_name' => 'web'],
            [
                'display_name' => 'Majelis / Peninjau Keuangan',
                'description' => 'Akses hanya-baca (read-only) untuk meninjau transaksi dan mengunduh laporan keuangan.',
            ]
        );
        $roleMajelis->syncPermissions([
            'module.keuangan',
            'keuangan.voucher.view',
            'keuangan.voucher.print',
            'keuangan.coa.view',
            'keuangan.laporan.view',
            'keuangan.laporan.export',
        ]);

        // ─── 3. SEED CONTOH USER ───────────────────────────────────────────────
        $usersData = [
            [
                'name' => 'Administrator Portal',
                'email' => 'admin@gpibhosiana.org',
                'role' => 'Super Admin',
            ],
            [
                'name' => 'User B - Bendahara',
                'email' => 'bendahara@gpibhosiana.org',
                'role' => 'Bendahara Keuangan',
            ],
            [
                'name' => 'User A - Operator Kas',
                'email' => 'operator@gpibhosiana.org',
                'role' => 'Operator Kasir',
            ],
            [
                'name' => 'Majelis Penatua',
                'email' => 'majelis@gpibhosiana.org',
                'role' => 'Majelis Peninjau',
            ],
        ];

        foreach ($usersData as $u) {
            $user = User::firstOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => Hash::make('password'),
                    'is_active' => true,
                ]
            );

            $user->syncRoles([$u['role']]);
        }

        // Juga pastikan user existing (jika ada) mendapatkan role Super Admin bila belum punya role
        $existingUsers = User::doesntHave('roles')->get();
        foreach ($existingUsers as $eu) {
            $eu->assignRole('Super Admin');
        }
    }
}
