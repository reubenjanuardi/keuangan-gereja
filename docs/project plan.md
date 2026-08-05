# 🚀 Implementation Plan — Penyelesaian MVP SIKG

> Dibuat berdasarkan: [Audit Report](file:///C:/Users/Pongo/.gemini/antigravity-ide/brain/5db7e64b-23ba-4398-a0da-2d8a3cf21096/audit_report.md)
> Tanggal: 2026-07-30

---

## Status MVP Saat Ini: 🟢 100% Selesai (MVP Fitur Utama Siap Release)

```
Phase 1 — Gap Closure        ██████████  (100% Selesai - 30 Juli 2026)
Auth Fix — Redirect Bug      ██████████  (100% Selesai - 31 Juli 2026)
Phase 2 — Pelaporan PDF      ██████████  (100% Selesai - 31 Juli 2026)
Phase 3 — Buku Besar & Jurnal██████████  (100% Selesai - 31 Juli 2026)
Phase 4 — RBAC               ░░░░░░░░░░  (Fase Lanjutan / Opsional)
Phase 5 — Multi-Tenant       ░░░░░░░░░░  (Fase Lanjutan / Opsional)
```

---

## 🟢 PHASE 1 — Penutupan Gap Kritis MVP (SELESAI)
**Status: ✅ 100% Selesai | Selesai pada: 30 Juli 2026**

> [!NOTE]
> Seluruh gap kritis integritas data telah selesai diimplementasikan dan diverifikasi dengan baik.

### Task 1.1 — Restrict on Delete: Layer Database

**File:** [MODIFY] [2026_07_29_033112_create_transactions_table.php](file:///c:/laragon/www/keuangan-gereja/database/migrations/2026_07_29_033112_create_transactions_table.php)

Tambahkan `->restrictOnDelete()` secara eksplisit pada FK `kode_akun`:

```php
$table->foreign('kode_akun')
    ->references('kode_akun')
    ->on('chart_of_accounts')
    ->cascadeOnUpdate()
    ->restrictOnDelete(); // ← tambahkan ini
```

> [!NOTE]
> Karena migration sudah dijalankan, buat **migration baru** untuk alter FK daripada mengubah file lama:
> ```bash
> php artisan make:migration add_restrict_delete_to_transactions_kode_akun
> ```

---

### Task 1.2 — Restrict on Delete: Layer Aplikasi (Filament Guard)

**File:** [MODIFY] [ChartOfAccountResource.php](file:///c:/laragon/www/keuangan-gereja/app/Filament/Resources/ChartOfAccountResource.php)

Tambahkan validasi `before()` pada `DeleteAction` di tabel dan pada header halaman Edit:

```php
use Filament\Notifications\Notification;

// Di recordActions() table:
DeleteAction::make()
    ->before(function (ChartOfAccount $record, \Filament\Actions\Action $action) {
        if ($record->transactions()->exists()) {
            Notification::make()
                ->danger()
                ->title('Akun Tidak Dapat Dihapus')
                ->body('Akun "' . $record->nama_akun . '" sudah memiliki riwayat transaksi.')
                ->send();
            $action->cancel();
        }
        // Cek juga apakah punya anak (child accounts)
        if (ChartOfAccount::where('parent_code', $record->kode_akun)->exists()) {
            Notification::make()
                ->danger()
                ->title('Akun Tidak Dapat Dihapus')
                ->body('Akun ini masih memiliki akun anak. Hapus akun anak terlebih dahulu.')
                ->send();
            $action->cancel();
        }
    }),
```

**File:** [MODIFY] [EditChartOfAccount.php](file:///c:/laragon/www/keuangan-gereja/app/Filament/Resources/ChartOfAccountResource/Pages/EditChartOfAccount.php)

Terapkan guard yang sama pada `DeleteAction` di header halaman Edit.

---

### Task 1.3 — DB Transaction Wrapping pada Voucher

**File:** [MODIFY] [CreateVoucher.php](file:///c:/laragon/www/keuangan-gereja/app/Filament/Resources/VoucherResource/Pages/CreateVoucher.php)

```php
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

protected function handleRecordCreation(array $data): Model
{
    return DB::transaction(function () use ($data) {
        return parent::handleRecordCreation($data);
    });
}
```

**File:** [MODIFY] [EditVoucher.php](file:///c:/laragon/www/keuangan-gereja/app/Filament/Resources/VoucherResource/Pages/EditVoucher.php)

```php
use Illuminate\Support\Facades\DB;

protected function handleRecordUpdate(Model $record, array $data): Model
{
    return DB::transaction(function () use ($record, $data) {
        return parent::handleRecordUpdate($record, $data);
    });
}
```

---

### Verifikasi Phase 1
- [x] Coba hapus akun CoA yang sudah punya transaksi → muncul notification error & aksi dibatalkan (Verified)
- [x] Coba hapus akun CoA yang punya anak → muncul notification error & aksi dibatalkan (Verified)
- [x] DB Transaction wrapping pada save & edit voucher → data rollback jika terjadi failure (Verified)

---

## 🟢 BUG FIX — Fix Redirect Inertia ke Filament Admin Panel (SELESAI)
**Status: ✅ 100% Selesai | Selesai pada: 31 Juli 2026**

> [!NOTE]
> Memperbaiki bug di mana setelah login pada endpoint `/login` (Inertia.js), halaman tidak melakukan redirect penuh ke Filament Admin Panel (`/admin`), melainkan memunculkan UI Dashboard di dalam modal preview Inertia.

**File Terkait:**
- [MODIFY] [AuthenticatedSessionController.php](file:///c:/laragon/www/keuangan-gereja/app/Http/Controllers/Auth/AuthenticatedSessionController.php) — Menggunakan `Inertia::location(redirect()->intended('/admin'))` agar mengirimkan header `X-Inertia-Location` (HTTP 409) untuk memicu `window.location.href` penuh.
- [MODIFY] [RegisteredUserController.php](file:///c:/laragon/www/keuangan-gereja/app/Http/Controllers/Auth/RegisteredUserController.php) — Terapkan `Inertia::location()` pada alur registrasi.
- [MODIFY] [ConfirmablePasswordController.php](file:///c:/laragon/www/keuangan-gereja/app/Http/Controllers/Auth/ConfirmablePasswordController.php) — Terapkan `Inertia::location()` pada alur konfirmasi password.

---

## 🟢 PHASE 2 — Cetak Bukti Voucher ke PDF (SELESAI)
**Status: ✅ 100% Selesai | Selesai pada: 31 Juli 2026**

> [!NOTE]
> Fitur pencetakan dokumen voucher dalam format PDF telah selesai diimplementasikan, mencakup tombol aksi di tabel Filament dan halaman Edit, Blade template profesional, route stream/download, dan pengujian otomatis.

### Task 2.1 — Install Package PDF

```bash
composer require barryvdh/laravel-dompdf
```

Package `barryvdh/laravel-dompdf` v3.1.2 telah berhasil diinstall.

---

### Task 2.2 — Buat Blade Template Dokumen Voucher

**File:** [NEW] [voucher.blade.php](file:///c:/laragon/www/keuangan-gereja/resources/views/pdf/voucher.blade.php)

Layout dokumen kas profesional:
- Header: Nama Gereja ("GEREJA KEUANGAN") & SIKG, Judul dokumen ("BUKTI KAS MASUK / KELUAR")
- Info Header Voucher: No Bukti, Tanggal, Pihak Terkait, Jenis Voucher
- Tabel Detail Transaksi: Kode Akun, Nama Akun, Uraian, Nominal
- Footer: Total Nominal & 3 Kolom Tanda Tangan (Dibuat Oleh / Disetujui Oleh / Diterima/Diserahkan Oleh)

---

### Task 2.3 — Buat PDF Controller / Route

**File:** [NEW] [VoucherPdfController.php](file:///c:/laragon/www/keuangan-gereja/app/Http/Controllers/VoucherPdfController.php)
**File:** [MODIFY] [web.php](file:///c:/laragon/www/keuangan-gereja/routes/web.php)

Route:
```php
Route::get('/vouchers/{voucher}/pdf', [VoucherPdfController::class, 'stream'])->name('vouchers.pdf')->middleware('auth');
```

---

### Task 2.4 — Tambahkan Tombol "Cetak PDF" di Filament

**File:** [MODIFY] [VoucherResource.php](file:///c:/laragon/www/keuangan-gereja/app/Filament/Resources/VoucherResource.php) — Tambahkan `Action::make('cetak_pdf')` pada `recordActions()`.
**File:** [MODIFY] [EditVoucher.php](file:///c:/laragon/www/keuangan-gereja/app/Filament/Resources/VoucherResource/Pages/EditVoucher.php) — Tambahkan `Action::make('cetak_pdf')` pada `getHeaderActions()`.

---

### Verifikasi Phase 2
- [x] Klik "Cetak PDF" pada voucher di tabel Filament / halaman Edit → PDF terbuka di tab baru untuk dicetak/view (Verified)
- [x] Verifikasi layout: No Bukti, Tanggal, Pihak Terkait, detail transaksi, & total nominal terformat dengan rapi (Verified)
- [x] Kolom tanda tangan tercetak di posisi bawah dokumen (Verified)
- [x] Automated Feature Test `VoucherPdfTest.php` passing 100% (Verified)

---

## 🟢 PHASE 3 — Laporan Buku Besar & Jurnal (SELESAI)
**Status: ✅ 100% Selesai | Selesai pada: 31 Juli 2026**

> [!NOTE]
> Modul laporan Buku Besar dan Jurnal Transaksi telah selesai dibuat dengan fitur filter interaktif di Filament Admin Panel dan fasilitas cetak PDF lanskap.

### Task 3.1 & 3.2 & 3.3 — Laporan Buku Besar

**File:** [NEW] [LaporanBukuBesar.php](file:///c:/laragon/www/keuangan-gereja/app/Filament/Pages/LaporanBukuBesar.php)
**File:** [NEW] [laporan-buku-besar.blade.php](file:///c:/laragon/www/keuangan-gereja/resources/views/filament/pages/laporan-buku-besar.blade.php)

- Menampilkan mutasi transaksi per akun (grouped by `kode_akun`).
- Filter: Dari Tanggal, Sampai Tanggal, Kode Akun (Searchable), dan Jenis Voucher.
- Subtotal Total Masuk & Total Keluar per Akun.

---

### Task 3.4 — Laporan Jurnal Transaksi

**File:** [NEW] [LaporanJurnal.php](file:///c:/laragon/www/keuangan-gereja/app/Filament/Pages/LaporanJurnal.php)
**File:** [NEW] [laporan-jurnal.blade.php](file:///c:/laragon/www/keuangan-gereja/resources/views/filament/pages/laporan-jurnal.blade.php)

- Menampilkan seluruh jurnal transaksi secara kronologis.
- Filter: Rentang Tanggal & Kategori Akun.
- Menampilkan Grand Total Nominal.

---

### Task 3.5 — Ekspor Laporan ke PDF

**File:** [NEW] [LaporanPdfController.php](file:///c:/laragon/www/keuangan-gereja/app/Http/Controllers/LaporanPdfController.php)
**File:** [NEW] [laporan-buku-besar.blade.php (PDF)](file:///c:/laragon/www/keuangan-gereja/resources/views/pdf/laporan-buku-besar.blade.php)
**File:** [NEW] [laporan-jurnal.blade.php (PDF)](file:///c:/laragon/www/keuangan-gereja/resources/views/pdf/laporan-jurnal.blade.php)

---

### Verifikasi Phase 3
- [x] Filter periode & akun menampilkan data transaksi yang sesuai (Verified)
- [x] Total per akun dan grand total terhitung dengan benar (Verified)
- [x] Ekspor PDF Buku Besar & Jurnal berfungsi lancar dengan format landscape yang rapi (Verified)
- [x] Automated Feature Test `LaporanReportTest.php` passing 100% (Verified)

---

## 🔵 PHASE 4 — Role-Based Access Control (RBAC)
**Estimasi: 3–4 hari kerja | Prioritas: Sedang (PRD Seksi 5 — Fase Lanjutan)**

> [!NOTE]
> Phase ini masuk dalam Roadmap Ekstensibilitas PRD. Dapat dikerjakan setelah Phase 1–3 selesai dan sistem sudah digunakan secara aktif.

### Task 4.1 — Install spatie/laravel-permission

```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

---

### Task 4.2 — Definisikan Role & Permission

| Role | Akses |
|---|---|
| `super_admin` | Full access semua fitur |
| `kasir` | Create & Edit Voucher, View CoA |
| `verifikator` | View semua + approve/reject voucher |
| `auditor` | View-only semua laporan, tidak bisa create/edit |

---

### Task 4.3 — Integrasi dengan Filament

Tambahkan `FilamentShield` atau implementasikan manual:

```php
// Di setiap Resource:
public static function canCreate(): bool
{
    return auth()->user()->hasRole(['super_admin', 'kasir']);
}

public static function canEdit(Model $record): bool
{
    return auth()->user()->hasRole(['super_admin', 'kasir']);
}

public static function canDelete(Model $record): bool
{
    return auth()->user()->hasRole('super_admin');
}
```

---

### Task 4.4 — Halaman Manajemen User & Role

**File:** [NEW] Filament Resource `UserResource.php`

Tambahkan resource untuk manajemen user dengan fitur assign role.

---

### Verifikasi Phase 4
- [ ] Login sebagai kasir: hanya bisa akses Voucher (create/edit), tidak bisa hapus
- [ ] Login sebagai auditor: semua halaman read-only
- [ ] Login sebagai super_admin: full akses

---

## 🟢 PHASE 5 — Multi-Tenancy
**Estimasi: 5–7 hari kerja | Prioritas: Rendah (PRD Seksi 5 — Fase Lanjutan)**

> [!WARNING]
> Phase ini membutuhkan **perubahan skema database yang signifikan**. Lakukan hanya setelah evaluasi kebutuhan bisnis yang jelas (berapa entitas/pos pelayanan yang akan menggunakan sistem?).

### Task 5.1 — Evaluasi & Desain Arsitektur Multi-Tenant

Pilih pendekatan:
- **Single DB + `entity_id` column** (sederhana, cocok untuk skala kecil)
- **Separate schema per tenant** (menggunakan `stancl/tenancy`)

---

### Task 5.2 — Tambahkan Kolom `entity_id` (Jika Pakai Single DB)

Buat migration untuk menambahkan `entity_id` ke semua tabel utama:
- `chart_of_accounts`
- `vouchers`
- `transactions`

---

### Task 5.3 — Global Scope untuk Filtering Otomatis

```php
// Model akan otomatis filter berdasarkan entity login
protected static function booted(): void
{
    static::addGlobalScope('entity', function (Builder $builder) {
        $builder->where('entity_id', auth()->user()->entity_id);
    });
}
```

---

### Verifikasi Phase 5
- [ ] User dari Entity A tidak bisa melihat data Entity B
- [ ] CoA masing-masing entitas terpisah
- [ ] Laporan hanya menampilkan data entitas yang sedang login

---

## 📅 Timeline Rekomendasi

```
Minggu 1  │ Phase 1 (Gap Closure)          ██████████ WAJIB
Minggu 2  │ Phase 2 (PDF)                  ██████████
Minggu 3-4│ Phase 3 (Buku Besar & Jurnal)  ████████████████████
Minggu 5-6│ Phase 4 (RBAC)                 ████████████████████
Minggu 7+ │ Phase 5 (Multi-Tenant)         ████████████████████ (opsional)
```

---

## ✅ Definition of Done — MVP Selesai

MVP dianggap **selesai dan siap production** ketika:

- [x] CoA Management (hierarki, postable, read-only kode) ← *sudah*
- [x] Pencatatan Voucher dengan Repeater ← *sudah*
- [x] Validasi no bukti & kalkulasi real-time ← *sudah*
- [x] **Restrict on Delete CoA** (Phase 1) ← *selesai (30 Juli 2026)*
- [x] **DB Transaction pada save Voucher** (Phase 1) ← *selesai (30 Juli 2026)*
- [x] **Fix Redirect Inertia ke Filament Admin Panel** ← *selesai (31 Juli 2026)*
- [x] **Cetak bukti Voucher ke PDF** (Phase 2) ← *selesai (31 Juli 2026)*
- [x] **Laporan Buku Besar** (Phase 3) ← *selesai (31 Juli 2026)*
- [x] **Laporan Jurnal** (Phase 3) ← *selesai (31 Juli 2026)*
