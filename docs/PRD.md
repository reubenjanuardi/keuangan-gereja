# 📄 Product Requirements Document (PRD)
**Nama Produk:** Sistem Informasi Keuangan Gereja (SIKG)
**Fase Saat Ini:** Pengembangan Minimum Viable Product (MVP)

## 1. Ringkasan Eksekutif
Sistem Informasi Keuangan Gereja adalah aplikasi berbasis web yang dirancang untuk melakukan digitalisasi, sentralisasi, dan agregasi pelaporan arus kas (pemasukan dan pengeluaran) jemaat. Aplikasi ini menggantikan pencatatan manual berbasis *spreadsheet* menjadi sistem *database* relasional yang terstruktur, memastikan integritas data akuntansi dan efisiensi waktu bendahara.

## 2. Spesifikasi Teknis
* **Framework Utama:** Laravel 13
* **Admin Panel / Frontend:** Filament & VueJS (TALL Stack)
* **Database:** Relational Database (PostgreSQL/MySQL) dengan dukungan UUID.
* **Environment:** Saat Development dikelola menggunakan *virtual host* lokal (Laragon).

## 3. Fitur Utama & Kebutuhan Fungsional (Functional Requirements)

### 3.1. Manajemen Master Data: Chart of Accounts (CoA)
* **Hierarki Akun:** Sistem harus mendukung pembuatan mata anggaran bersarang (Induk dan Anak) untuk memudahkan agregasi pelaporan.
* **Aturan Posting (Postable):** Hanya akun anak (level terbawah) dengan status `is_postable = true` yang diizinkan untuk ditarik dan digunakan dalam form transaksi kas.
* **Integritas Kode Akun:** `kode_akun` bertindak sebagai *Primary Key* berjenis *String*. Setelah direkam, kode ini bersifat *read-only* (tidak dapat diubah pada halaman *Edit*) untuk mencegah rusaknya relasi historis.
* **Proteksi Hapus:** Akun tidak dapat dihapus jika sudah memiliki riwayat transaksi terkait (*Restrict on Delete*).

### 3.2. Pencatatan Arus Kas (Voucher & Transaksi)
* **Master-Detail Entry:** Menyediakan satu antarmuka form dinamis (menggunakan komponen *Repeater*) yang memungkinkan pengguna menginput detail *header* dokumen kas sekaligus banyak baris rincian alokasi anggaran secara bersamaan.
* **Standar Identifikasi Dokumen:** Nomor bukti (Voucher ID) harus unik, menggunakan format string yang berkesinambungan, dan divalidasi secara ketat agar sama sekali tidak mengandung tanda koma.
* **Kalkulasi Real-time:** Total nilai nominal kas (Masuk/Keluar) dikalkulasi secara otomatis oleh antarmuka berdasarkan penjumlahan dinamis dari seluruh baris rincian untuk meminimalisir kesalahan hitung manusia (*human error*).
* **Keamanan Penyimpanan:** Menggunakan mekanisme *Database Transaction* saat menyimpan data ke tabel `vouchers` dan `transactions` untuk memastikan kedua tabel tersimpan secara serentak atau dibatalkan sepenuhnya jika terjadi kesalahan sistem.

### 3.3. Modul Pelaporan & Ekspor (Fase Mendatang)
* **Cetak Bukti Fisik:** Kemampuan mengonversi data Voucher kas tunggal ke format dokumen PDF untuk kebutuhan arsip dan tanda tangan basah otorisasi.
* **Buku Besar & Jurnal:** Generator laporan agregasi keuangan bulanan dan triwulanan yang merekapitulasi total nominal berdasarkan hierarki Chart of Accounts.

## 4. Kebutuhan Non-Fungsional (Non-Functional Requirements)
* **UX / UI:** Antarmuka harus mengadopsi standar panel administratif yang bersih, intuitif, dan responsif. Pengelompokan *dropdown* menggunakan penanda visual (seperti ikon folder atau indentasi) harus diimplementasikan untuk navigasi CoA yang panjang.
* **Performa:** Pemuatan *dropdown* dengan ratusan entri CoA harus tetap ringan tanpa jeda (*lag*).

## 5. Rencana Pengembangan Lanjutan (Roadmap Ekstensibilitas)
Arsitektur inti yang dibangun saat ini memiliki potensi untuk diekspansi. Pada fase lanjutan, sistem dapat diintegrasikan dengan kapabilitas *multi-tenant* untuk mengakomodasi pengelolaan keuangan yang terpisah antar pos pelayanan atau entitas turunan lainnya di dalam satu basis data. Selain itu, implementasi kontrol akses berbasis peran (*Role-Based Access Control*) dapat ditambahkan untuk memberikan otorisasi yang berbeda antara staf kasir (hanya *input*), verifikator dokumen, dan auditor (hanya melihat laporan monitoring).