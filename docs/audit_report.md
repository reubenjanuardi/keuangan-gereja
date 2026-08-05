# 🔍 Audit Code Base vs PRD — SIKG (Sistem Informasi Keuangan Gereja)

> Audit dilakukan pada: 2026-07-30

---

## Ringkasan Eksekutif

Berdasarkan hasil audit, **implementasi inti MVP sudah sangat sejalan dengan spesifikasi PRD**. Hampir seluruh fitur fungsional pada fase MVP (Seksi 3.1 & 3.2) telah diimplementasikan dengan benar. Terdapat beberapa gap kecil pada proteksi data dan satu fitur yang terlewat, serta seluruh Seksi 3.3 (Pelaporan) yang masih belum dieksekusi.

---

## ✅ Fitur yang Sudah Sejalan dengan PRD

### 3.1 Manajemen Master Data: Chart of Accounts (CoA)

| Requirement PRD | Status | Bukti di Kode |
|---|---|---|
| Hierarki akun bersarang (Induk & Anak) | ✅ **Selesai** | `parent_code` nullable di [migration CoA](file:///c:/laragon/www/keuangan-gereja/database/migrations/2026_07_29_033110_create_chart_of_accounts_table.php#L20), self-referencing FK |
| `is_postable = true` sebagai filter form transaksi | ✅ **Selesai** | [VoucherResource.php#L87](file:///c:/laragon/www/keuangan-gereja/app/Filament/Resources/VoucherResource.php#L87) `->where('is_postable', true)` |
| `kode_akun` sebagai Primary Key string | ✅ **Selesai** | [ChartOfAccount.php](file:///c:/laragon/www/keuangan-gereja/app/Models/ChartOfAccount.php) — `$primaryKey = 'kode_akun'`, `$keyType = 'string'` |
| `kode_akun` read-only saat Edit | ✅ **Selesai** | [ChartOfAccountResource.php#L42](file:///c:/laragon/www/keuangan-gereja/app/Filament/Resources/ChartOfAccountResource.php#L42) — `->disabled(fn => $operation === 'edit')` |
| Visual indentasi dropdown CoA | ✅ **Selesai** | [ChartOfAccountResource.php#L131-L159](file:///c:/laragon/www/keuangan-gereja/app/Filament/Resources/ChartOfAccountResource.php#L131) — fungsi `getParentAccountOptions()` dengan `str_repeat('— ', $depth)` |

### 3.2 Pencatatan Arus Kas (Voucher & Transaksi)

| Requirement PRD | Status | Bukti di Kode |
|---|---|---|
| Master-Detail Entry dengan Repeater | ✅ **Selesai** | [VoucherResource.php#L66-L103](file:///c:/laragon/www/keuangan-gereja/app/Filament/Resources/VoucherResource.php#L66) — `Repeater::make('transactions')` |
| Nomor bukti unik & tidak boleh ada koma | ✅ **Selesai** | [VoucherResource.php#L41-L45](file:///c:/laragon/www/keuangan-gereja/app/Filament/Resources/VoucherResource.php#L41) — `->unique()` + `regex:/^[^,]+$/` |
| Kalkulasi real-time total nominal | ✅ **Selesai** | [VoucherResource.php#L72-L77](file:///c:/laragon/www/keuangan-gereja/app/Filament/Resources/VoucherResource.php#L72) — `afterStateUpdated` + `calculateTotalNominal()` |
| UUID pada tabel transactions | ✅ **Selesai** | [Transaction.php](file:///c:/laragon/www/keuangan-gereja/app/Models/Transaction.php) — `use HasUuids` + [migration](file:///c:/laragon/www/keuangan-gereja/database/migrations/2026_07_29_033112_create_transactions_table.php#L15) `uuid('id')` |

### Teknis Stack

| Requirement PRD | Status | Bukti |
|---|---|---|
| Laravel 13 | ✅ **Selesai** | [composer.json#L13](file:///c:/laragon/www/keuangan-gereja/composer.json#L13) — `"laravel/framework": "^13.8"` |
| Filament (Admin Panel) | ✅ **Selesai** | [composer.json#L10](file:///c:/laragon/www/keuangan-gereja/composer.json#L10) — `"filament/filament": "5.7"` |
| Relational Database dengan UUID | ✅ **Selesai** | Migrations + `HasUuids` trait |

---

## ⚠️ Gap & Inkonsistensi yang Ditemukan

### Gap 1 — Proteksi Hapus Akun (Restrict on Delete) ❌ BELUM DIIMPLEMENTASIKAN

**PRD Seksi 3.1:** *"Akun tidak dapat dihapus jika sudah memiliki riwayat transaksi terkait (Restrict on Delete)."*

**Kondisi saat ini:**
- Di [migration CoA](file:///c:/laragon/www/keuangan-gereja/database/migrations/2026_07_29_033110_create_chart_of_accounts_table.php#L29), FK dari `parent_code` ke `chart_of_accounts` sudah pakai `cascadeOnUpdate()`, tapi **tidak ada** `restrictOnDelete()` / `->onDelete('restrict')`.
- Di [migration transactions](file:///c:/laragon/www/keuangan-gereja/database/migrations/2026_07_29_033112_create_transactions_table.php#L24), FK dari `kode_akun` ke `chart_of_accounts` **tidak ada** klausa `onDelete` sama sekali — ini akan default ke `RESTRICT` di beberapa engine, namun **tidak eksplisit dan tidak dijamin konsisten** lintas database.
- Di [ChartOfAccountResource.php](file:///c:/laragon/www/keuangan-gereja/app/Filament/Resources/ChartOfAccountResource.php#L109), `DeleteAction` tidak memiliki logika validasi di layer aplikasi.

**Risiko:** Jika database engine tidak menegakkan FK secara ketat (misal SQLite dev vs MySQL prod), akun yang punya transaksi bisa terhapus.

---

### Gap 2 — Database Transaction Wrapping ❌ BELUM DIIMPLEMENTASIKAN

**PRD Seksi 3.2:** *"Menggunakan mekanisme Database Transaction saat menyimpan data ke tabel `vouchers` dan `transactions`..."*

**Kondisi saat ini:**
- [CreateVoucher.php](file:///c:/laragon/www/keuangan-gereja/app/Filament/Resources/VoucherResource/Pages/CreateVoucher.php) hanya menggunakan `mutateFormDataBeforeCreate()` tanpa membungkus operasi simpan dengan `DB::transaction()`.
- [EditVoucher.php](file:///c:/laragon/www/keuangan-gereja/app/Filament/Resources/VoucherResource/Pages/EditVoucher.php) sama — tidak ada `DB::transaction()`.

**Risiko:** Jika terjadi error saat menyimpan detail transaksi setelah voucher header tersimpan, data akan inkonsisten (header ada tapi detail transaksi hilang).

---

### Gap 3 — VueJS Belum Ada ⚠️ CATATAN

**PRD Seksi 2:** *"Admin Panel / Frontend: Filament & VueJS (TALL Stack)"*

**Kondisi saat ini:** Seluruh antarmuka menggunakan Filament murni. VueJS ada di `package.json` (via Vite + Inertia), namun **belum ada komponen Vue** yang dibuat. Ini masih wajar di fase MVP karena Filament sudah mencukupi kebutuhan admin panel.

---

## 🔴 Fitur PRD yang Belum Dieksekusi (Roadmap)

### Seksi 3.3 — Modul Pelaporan & Ekspor

| Fitur | Status | Prioritas |
|---|---|---|
| **Cetak Bukti PDF** — ekspor Voucher tunggal ke PDF untuk arsip & tanda tangan | ❌ Belum | Tinggi |
| **Buku Besar** — rekap transaksi per akun dalam periode tertentu | ❌ Belum | Tinggi |
| **Jurnal** — laporan agregasi bulanan/triwulanan berdasarkan hierarki CoA | ❌ Belum | Sedang |

### Seksi 5 — Roadmap Ekstensibilitas (Fase Lanjutan)

| Fitur | Status | Prioritas |
|---|---|---|
| **Multi-Tenant** — pemisahan keuangan per pos pelayanan/entitas dalam satu DB | ❌ Belum | Rendah (fase lanjutan) |
| **Role-Based Access Control (RBAC)** — kasir (input only), verifikator, auditor | ❌ Belum | Sedang (fase lanjutan) |

---

## 📋 Rekomendasi Plan Pengembangan Selanjutnya

### 🔥 Prioritas Tinggi — Selesaikan Gap MVP

#### 1. Implementasikan Restrict on Delete untuk CoA

Tambahkan validasi di layer aplikasi pada `EditChartOfAccount` atau override `DeleteAction`:

```php
// Di ChartOfAccountResource.php — tambahkan guard pada DeleteAction
DeleteAction::make()
    ->before(function (ChartOfAccount $record, $action) {
        if ($record->transactions()->exists()) {
            Notification::make()
                ->danger()
                ->title('Akun tidak dapat dihapus')
                ->body('Akun ini sudah memiliki riwayat transaksi.')
                ->send();
            $action->cancel();
        }
    })
```

Dan perbaiki migration (atau buat migration baru) untuk menegakkan FK secara eksplisit:
```php
$table->foreign('kode_akun')
    ->references('kode_akun')
    ->on('chart_of_accounts')
    ->restrictOnDelete(); // ← tambahkan ini
```

#### 2. Implementasikan DB::transaction() pada Create & Edit Voucher

```php
// CreateVoucher.php
protected function handleRecordCreation(array $data): Model
{
    return DB::transaction(fn () => parent::handleRecordCreation($data));
}
```

---

### 📄 Prioritas Tinggi — Modul Pelaporan (Seksi 3.3)

#### 3. Cetak Bukti Voucher ke PDF

- Gunakan package `barryvdh/laravel-dompdf` atau `spatie/laravel-pdf`.
- Buat Filament `Action` di halaman List/Edit Voucher: tombol "Cetak PDF".
- Buat Blade view template untuk layout dokumen voucher.

#### 4. Laporan Buku Besar

- Buat Filament Resource baru: `LaporanResource` (read-only).
- Atau gunakan Filament **Widget** dengan filter periode (bulan/tahun) + filter `kode_akun`.
- Query agregasi transaksi dikelompokkan per akun dengan total debit/kredit.

#### 5. Laporan Jurnal Bulanan/Triwulanan

- Laporan tabel yang merekap total nominal per hierarki CoA.
- Tampilkan dalam format: Induk Akun → sub-total → Grand Total.

---

### 🛡️ Prioritas Sedang — RBAC (Seksi 5)

#### 6. Role-Based Access Control

- Gunakan `spatie/laravel-permission` yang sudah kompatibel dengan Filament.
- Definisikan 3 role: `kasir` (create/edit voucher), `verifikator`, `auditor` (view-only laporan).
- Proteksi setiap Resource dengan `canCreate()`, `canEdit()`, `canDelete()` berdasarkan role.

---

### 🏗️ Prioritas Rendah — Multi-Tenant (Seksi 5)

#### 7. Multi-Tenancy

- Evaluasi kebutuhan terlebih dulu (berapa banyak pos pelayanan?).
- Pertimbangkan package `stancl/tenancy` atau pendekatan `team_id` sederhana.
- Rancang ulang skema database untuk menambah kolom `team_id` / `entity_id` di semua tabel.

---

## 📊 Scorecard Keseluruhan

| Aspek | Score | Keterangan |
|---|---|---|
| Stack Teknis | 🟢 100% | Laravel 13 + Filament 5 ✓ |
| CoA Management | 🟡 80% | Semua fitur ada, proteksi hapus kurang eksplisit |
| Pencatatan Voucher | 🟡 85% | Lengkap, DB Transaction belum diimplementasikan |
| Pelaporan & Ekspor | 🔴 0% | Belum ada (fase mendatang per PRD) |
| RBAC & Multi-Tenant | 🔴 0% | Belum ada (roadmap fase lanjutan per PRD) |
| **Total MVP Readiness** | **🟡 ~75%** | MVP hampir selesai, 2 gap penting perlu ditutup |
