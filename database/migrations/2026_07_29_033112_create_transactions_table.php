<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('no_bukti');
            $table->string('kode_akun');
            $table->text('uraian');
            $table->decimal('nominal', 15, 2);
            $table->timestamps();

            // Relasi (Foreign Keys)
            $table->foreign('no_bukti')->references('no_bukti')->on('vouchers')->cascadeOnDelete();
            $table->foreign('kode_akun')->references('kode_akun')->on('chart_of_accounts')->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
