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
        // Langkah 1: Buat tabel dan pastikan kolom target unik
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('kode_akun')->unique();
            $table->string('nama_akun');
            $table->string('kategori')->nullable();
            $table->string('parent_code')->nullable();
            $table->boolean('is_postable')->default(true);
            $table->timestamps();
        });

        // Langkah 2: Tambahkan relasi foreign key di blok terpisah
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->foreign('parent_code')
                ->references('kode_akun')
                ->on('chart_of_accounts')
                ->onUpdate('cascade');
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
