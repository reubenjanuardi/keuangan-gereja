<?php

use App\Models\ChartOfAccount;
use App\Models\User;
use App\Models\Voucher;
use App\Models\Transaction;

test('guest cannot access report pages or pdfs', function () {
    $this->get('/keuangan/laporan-buku-besar')->assertRedirect();
    $this->get('/keuangan/laporan-jurnal')->assertRedirect();
    $this->get(route('laporan.buku-besar.pdf'))->assertRedirect(route('login'));
    $this->get(route('laporan.jurnal.pdf'))->assertRedirect(route('login'));
});

test('authenticated user can view laporan buku besar page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/keuangan/laporan-buku-besar');

    $response->assertStatus(200);
});

test('authenticated user can view laporan jurnal page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/keuangan/laporan-jurnal');

    $response->assertStatus(200);
});

test('authenticated user can stream pdf for buku besar and jurnal', function () {
    $user = User::factory()->create();

    $coa = ChartOfAccount::create([
        'kode_akun' => '4.102',
        'nama_akun' => 'Persembahan Syukur',
        'kategori' => 'Penerimaan',
        'is_postable' => true,
    ]);

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

    $pdfBukuBesar = $this->actingAs($user)->get(route('laporan.buku-besar.pdf'));
    $pdfBukuBesar->assertStatus(200);
    $pdfBukuBesar->assertHeader('content-type', 'application/pdf');

    $pdfJurnal = $this->actingAs($user)->get(route('laporan.jurnal.pdf'));
    $pdfJurnal->assertStatus(200);
    $pdfJurnal->assertHeader('content-type', 'application/pdf');
});
