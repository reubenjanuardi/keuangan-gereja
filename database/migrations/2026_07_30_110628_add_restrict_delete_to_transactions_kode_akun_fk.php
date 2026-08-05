<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Drop FK lama pada kode_akun (yang tidak memiliki onDelete clause eksplisit)
     * lalu buat ulang dengan restrictOnDelete() agar integritas data terjaga.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Drop FK lama
            $table->dropForeign(['kode_akun']);

            // Buat ulang FK dengan restrictOnDelete() secara eksplisit
            $table->foreign('kode_akun')
                ->references('kode_akun')
                ->on('chart_of_accounts')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * Kembalikan FK ke kondisi semula (tanpa klausa onDelete eksplisit).
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['kode_akun']);

            $table->foreign('kode_akun')
                ->references('kode_akun')
                ->on('chart_of_accounts')
                ->cascadeOnUpdate();
        });
    }
};
