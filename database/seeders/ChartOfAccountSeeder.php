<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChartOfAccount;

class ChartOfAccountSeeder extends Seeder
{
    public function run(): void
    {
        // Arahkan ke file CSV di folder database/seeders/
        $csvPath = base_path('database/seeders/CoA_Hosiana.csv');

        if (!file_exists($csvPath)) {
            $this->command->error("File CoA_Hosiana.csv tidak ditemukan di database/seeders/");
            return;
        }

        $csvFile = fopen($csvPath, 'r');
        $firstline = true;

        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
            if (!$firstline) {
                if (empty($data) || count($data) < 4 || empty(trim($data[0]))) {
                    continue;
                }

                // Mapping kolom CSV (0: Nomor Akun, 1: Uraian, 2: Kategori, 3: is_postable)
                $kodeAkun = trim($data[0]);
                $namaAkun = trim($data[1]);
                $rawKat = strtoupper(trim($data[2]));
                $kategori = match ($rawKat) {
                    'PENERIMAAN' => 'Penerimaan',
                    'PENGELUARAN' => 'Pengeluaran',
                    'KAS & BANK' => 'Kas & Bank',
                    'HUTANG / PIUTANG' => 'Hutang / Piutang',
                    default => ucfirst(strtolower(trim($data[2]))),
                };
                $isPostable = strtolower(trim($data[3])) === 'true' ? true : false;

                $parentCode = null;

                // Menentukan Parent Code berdasarkan titik (.) atau prefix kode
                if (strpos($kodeAkun, '.') !== false) {
                    $parts = explode('.', $kodeAkun);
                    array_pop($parts); // Hapus segmen terakhir
                    $parentCode = implode('.', $parts);

                    // Pastikan hierarki induknya tercipta lebih dulu agar tidak error Foreign Key
                    $this->createMissingParents($parentCode, $kategori);
                } elseif (strlen($kodeAkun) === 3 && str_starts_with($kodeAkun, '11')) {
                    $parentCode = '11';
                } elseif (strlen($kodeAkun) === 2 && str_starts_with($kodeAkun, '1')) {
                    $parentCode = '1';
                }

                ChartOfAccount::updateOrCreate(
                    ['kode_akun' => $kodeAkun],
                    [
                        'nama_akun' => $namaAkun,
                        'kategori' => $kategori,
                        'parent_code' => $parentCode,
                        'is_postable' => $isPostable,
                    ]
                );
            }
            $firstline = false;
        }

        fclose($csvFile);
        $this->command->info("Data CoA Hosiana berhasil di-import!");
    }

    /**
     * Fungsi rekursif untuk menambal akun induk yang hilang di CSV
     */
    private function createMissingParents($parentCode, $kategori)
    {
        if (empty($parentCode)) return;

        $grandParentCode = null;
        if (strpos($parentCode, '.') !== false) {
            $parts = explode('.', $parentCode);
            array_pop($parts);
            $grandParentCode = implode('.', $parts);

            // Panggil dirinya sendiri untuk memastikan kakek-buyut akunnya juga ada
            $this->createMissingParents($grandParentCode, $kategori);
        } elseif (strlen($parentCode) === 3 && str_starts_with($parentCode, '11')) {
            $grandParentCode = '11';
            $this->createMissingParents($grandParentCode, $kategori);
        } elseif (strlen($parentCode) === 2 && str_starts_with($parentCode, '1')) {
            $grandParentCode = '1';
            $this->createMissingParents($grandParentCode, $kategori);
        }

        // Buat parent jika ternyata tidak ada di database
        ChartOfAccount::firstOrCreate(
            ['kode_akun' => $parentCode],
            [
                'nama_akun' => 'Akun Induk ' . $parentCode,
                'kategori' => $kategori,
                'parent_code' => $grandParentCode,
                'is_postable' => false, // Induk tidak boleh di-post
            ]
        );
    }
}
