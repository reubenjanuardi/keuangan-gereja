<?php

use App\Models\ChartOfAccount;
use App\Models\User;
use App\Models\Voucher;
use App\Models\Transaction;
use App\Models\ActivityLog;

test('guest cannot access report pages or exports', function () {
    $this->get('/keuangan/laporan-buku-besar')->assertRedirect();
    $this->get('/keuangan/laporan-jurnal')->assertRedirect();
    $this->get('/keuangan/laporan-realisasi-mingguan')->assertRedirect();

    $this->get(route('laporan.buku-besar.pdf'))->assertRedirect(route('login'));
    $this->get(route('laporan.buku-besar.excel'))->assertRedirect(route('login'));

    $this->get(route('laporan.jurnal.pdf'))->assertRedirect(route('login'));
    $this->get(route('laporan.jurnal.excel'))->assertRedirect(route('login'));

    $this->get(route('laporan.realisasi-mingguan.pdf'))->assertRedirect(route('login'));
    $this->get(route('laporan.realisasi-mingguan.excel'))->assertRedirect(route('login'));
});

test('authenticated user can view report pages', function () {
    $this->seed(\Database\Seeders\RbacSeeder::class);
    $user = User::where('email', 'bendahara@gpibhosiana.org')->first();

    $this->actingAs($user)->get('/keuangan/laporan-buku-besar')->assertStatus(200);
    $this->actingAs($user)->get('/keuangan/laporan-jurnal')->assertStatus(200);
    $this->actingAs($user)->get('/keuangan/laporan-realisasi-mingguan')->assertStatus(200);
});

test('authenticated user can stream pdf and download excel for buku besar, jurnal, and realisasi mingguan', function () {
    $this->seed(\Database\Seeders\RbacSeeder::class);
    $user = User::where('email', 'bendahara@gpibhosiana.org')->first();

    $coa = ChartOfAccount::firstOrCreate(
        ['kode_akun' => '4.102'],
        [
            'nama_akun' => 'Persembahan Syukur',
            'kategori' => 'Penerimaan',
            'is_postable' => true,
        ]
    );

    $voucher = Voucher::create([
        'no_bukti'      => 'BKM-RPT-001',
        'tanggal'       => now()->toDateString(),
        'pihak_terkait' => 'Jemaat S',
        'jenis_voucher' => 'Masuk',
        'kode_akun'     => $coa->kode_akun,
        'total_nominal' => 200000,
    ]);

    Transaction::create([
        'no_bukti' => $voucher->no_bukti,
        'kode_akun' => $coa->kode_akun,
        'uraian' => 'Persembahan ibadah minggu',
        'nominal' => 200000,
    ]);

    // 1. Test PDF exports
    $pdfBukuBesar = $this->actingAs($user)->get(route('laporan.buku-besar.pdf'));
    $pdfBukuBesar->assertStatus(200);
    $pdfBukuBesar->assertHeader('content-type', 'application/pdf');

    $pdfJurnal = $this->actingAs($user)->get(route('laporan.jurnal.pdf'));
    $pdfJurnal->assertStatus(200);
    $pdfJurnal->assertHeader('content-type', 'application/pdf');

    $pdfRealisasi = $this->actingAs($user)->get(route('laporan.realisasi-mingguan.pdf'));
    $pdfRealisasi->assertStatus(200);
    $pdfRealisasi->assertHeader('content-type', 'application/pdf');

    // 2. Test Excel exports (.xlsx)
    $excelBukuBesar = $this->actingAs($user)->get(route('laporan.buku-besar.excel'));
    $excelBukuBesar->assertStatus(200);
    $excelBukuBesar->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

    $excelJurnal = $this->actingAs($user)->get(route('laporan.jurnal.excel'));
    $excelJurnal->assertStatus(200);
    $excelJurnal->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

    $excelRealisasi = $this->actingAs($user)->get(route('laporan.realisasi-mingguan.excel'));
    $excelRealisasi->assertStatus(200);
    $excelRealisasi->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

    // 3. Verify Activity Logs were recorded
    expect(ActivityLog::where('description', 'Mengunduh Laporan Buku Besar Excel')->exists())->toBeTrue()
        ->and(ActivityLog::where('description', 'Mengunduh Laporan Jurnal Transaksi Excel')->exists())->toBeTrue()
        ->and(ActivityLog::where('description', 'Mengunduh Laporan Realisasi Mingguan Excel')->exists())->toBeTrue();
});
