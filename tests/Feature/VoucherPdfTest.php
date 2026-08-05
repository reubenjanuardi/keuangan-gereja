<?php

use App\Models\ChartOfAccount;
use App\Models\User;
use App\Models\Voucher;
use App\Models\Transaction;

test('guest cannot access voucher pdf', function () {
    $coa = ChartOfAccount::create([
        'kode_akun'  => '4.100',
        'nama_akun'  => 'Akun Test Guest',
        'kategori'   => 'Penerimaan',
        'is_postable' => true,
    ]);

    $voucher = Voucher::create([
        'no_bukti'      => 'BKM-TEST-001',
        'tanggal'       => '2026-07-31',
        'pihak_terkait' => 'Donor Test',
        'jenis_voucher' => 'Masuk',
        'kode_akun'     => $coa->kode_akun,
        'total_nominal' => 500000,
    ]);

    $response = $this->get(route('vouchers.pdf', $voucher));

    $response->assertRedirect(route('login'));
});

test('authenticated user can stream voucher pdf', function () {
    $user = User::factory()->create();

    $coa = ChartOfAccount::create([
        'kode_akun' => '4.101',
        'nama_akun' => 'Persembahan Perpuluhan',
        'kategori' => 'Penerimaan',
        'is_postable' => true,
    ]);

    $voucher = Voucher::create([
        'no_bukti'      => 'BKM-TEST-002',
        'tanggal'       => '2026-07-31',
        'pihak_terkait' => 'Bpk. Yohanes',
        'jenis_voucher' => 'Masuk',
        'kode_akun'     => $coa->kode_akun,
        'total_nominal' => 750000,
    ]);

    Transaction::create([
        'no_bukti' => $voucher->no_bukti,
        'kode_akun' => $coa->kode_akun,
        'uraian' => 'Persembahan perpuluhan bulan Juli',
        'nominal' => 750000,
    ]);

    $response = $this->actingAs($user)->get(route('vouchers.pdf', $voucher));

    $response->assertStatus(200);
    $response->assertHeader('content-type', 'application/pdf');
});
