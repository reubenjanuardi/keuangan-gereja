<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add kode_akun (mata anggaran) to vouchers table.
     * One voucher must have exactly one mata anggaran (kode_akun).
     * The kode_akun on each transaction row will be propagated from this header field.
     */
    public function up(): void
    {
        // 1. Add kode_akun to vouchers (nullable first for backfill)
        Schema::table('vouchers', function (Blueprint $table) {
            $table->string('kode_akun')->nullable()->after('jenis_voucher');
            $table->foreign('kode_akun')
                ->references('kode_akun')
                ->on('chart_of_accounts')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
        });

        // 2. Backfill vouchers.kode_akun from the first transaction row of each voucher
        //    Using a subquery approach compatible with both MySQL and SQLite.
        $vouchers = \Illuminate\Support\Facades\DB::table('vouchers')
            ->whereNull('kode_akun')
            ->pluck('no_bukti');

        foreach ($vouchers as $noBukti) {
            $firstTxKodeAkun = \Illuminate\Support\Facades\DB::table('transactions')
                ->where('no_bukti', $noBukti)
                ->value('kode_akun');

            if ($firstTxKodeAkun) {
                \Illuminate\Support\Facades\DB::table('vouchers')
                    ->where('no_bukti', $noBukti)
                    ->update(['kode_akun' => $firstTxKodeAkun]);
            }
        }

        // 3. Make kode_akun NOT NULL now that backfill is done
        //    Only enforce in MySQL/MariaDB; SQLite handles this gracefully via recreate
        if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
            Schema::table('vouchers', function (Blueprint $table) {
                $table->string('kode_akun')->nullable(false)->change();
            });
        }
    }

    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropForeign(['kode_akun']);
            $table->dropColumn('kode_akun');
        });
    }
};
