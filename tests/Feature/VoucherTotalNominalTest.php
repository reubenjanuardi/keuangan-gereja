<?php

use App\Models\ChartOfAccount;
use App\Models\User;
use App\Models\Voucher;
use App\Models\Transaction;
use App\Http\Controllers\VoucherPdfController;

test('voucher total_nominal is correctly calculated from transactions', function () {
    $coa = ChartOfAccount::create([
        'kode_akun'  => '5.101',
        'nama_akun'  => 'Beban Operasional',
        'kategori'   => 'Pengeluaran',
        'is_postable' => true,
    ]);

    $voucher = Voucher::create([
        'no_bukti'      => 'BKM-CALC-001',
        'tanggal'       => '2026-07-31',
        'pihak_terkait' => 'Vendor A',
        'jenis_voucher' => 'Keluar',
        'kode_akun'     => $coa->kode_akun,
        'total_nominal' => 0,
    ]);

    Transaction::create([
        'no_bukti' => $voucher->no_bukti,
        'kode_akun' => $coa->kode_akun,
        'uraian'    => 'Item 1',
        'nominal'   => 150000,
    ]);

    Transaction::create([
        'no_bukti'  => $voucher->no_bukti,
        'kode_akun' => $coa->kode_akun,
        'uraian'    => 'Item 2',
        'nominal'   => 250000,
    ]);

    $total = $voucher->transactions()->sum('nominal');
    $voucher->update(['total_nominal' => $total]);

    expect((float) $voucher->fresh()->total_nominal)->toBe(400000.0);
});

test('all transaction rows share the same kode_akun as the voucher header', function () {
    $coa = ChartOfAccount::create([
        'kode_akun'  => '4.201',
        'nama_akun'  => 'Persembahan Bulanan',
        'kategori'   => 'Penerimaan',
        'is_postable' => true,
    ]);

    $voucher = Voucher::create([
        'no_bukti'      => 'BKM-RULE-001',
        'tanggal'       => '2026-07-31',
        'pihak_terkait' => 'Jemaat X',
        'jenis_voucher' => 'Masuk',
        'kode_akun'     => $coa->kode_akun,  // ← header-level mata anggaran
        'total_nominal' => 0,
    ]);

    Transaction::create(['no_bukti' => $voucher->no_bukti, 'kode_akun' => $coa->kode_akun, 'uraian' => 'Uraian 1', 'nominal' => 100000]);
    Transaction::create(['no_bukti' => $voucher->no_bukti, 'kode_akun' => $coa->kode_akun, 'uraian' => 'Uraian 2', 'nominal' => 200000]);
    Transaction::create(['no_bukti' => $voucher->no_bukti, 'kode_akun' => $coa->kode_akun, 'uraian' => 'Uraian 3', 'nominal' => 300000]);

    // Business rule: every transaction row must have same kode_akun as voucher header
    $txKodeAkuns = $voucher->transactions()->pluck('kode_akun')->unique()->values()->toArray();
    expect($txKodeAkuns)->toBe([$coa->kode_akun]);
});

test('terbilang helper converts numbers to Indonesian words correctly', function () {
    expect(VoucherPdfController::terbilang(0))->toBe('Nol');
    expect(VoucherPdfController::terbilang(1))->toBe('Satu');
    expect(VoucherPdfController::terbilang(11))->toBe('Sebelas');
    expect(VoucherPdfController::terbilang(100))->toBe('Seratus');
    expect(VoucherPdfController::terbilang(1000))->toBe('Seribu');
    expect(VoucherPdfController::terbilang(150000))->toBe('Seratus Lima Puluh Ribu');
    expect(VoucherPdfController::terbilang(1000000))->toBe('Satu Juta');
    expect(VoucherPdfController::terbilang(2500000))->toBe('Dua Juta Lima Ratus Ribu');
});
