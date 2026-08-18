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
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->string('kode_akun')->primary()->unique();
            $table->string('nama_akun');
            $table->enum('kategori', ['Penerimaan', 'Pengeluaran', 'Kas & Bank', 'Hutang / Piutang']);

            // Kolom untuk menentukan siapa induknya (Bisa null jika dia adalah level teratas)
            $table->string('parent_code')->nullable();

            // Penanda: Apakah akun ini bisa dipilih untuk transaksi? 
            // (Induk biasanya FALSE, Anak biasanya TRUE)
            $table->boolean('is_postable')->default(true);

            $table->timestamps();

            // Relasi ke dirinya sendiri (Self-referencing)
            $table->foreign('parent_code')->references('kode_akun')->on('chart_of_accounts')->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chart_of_accounts');
    }
};
