<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create app_settings table for storing church name, address, and other configurable values.
     */
    public function up(): void
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed with default values
        \Illuminate\Support\Facades\DB::table('app_settings')->insert([
            ['key' => 'church_name',    'value' => 'GPIB Jemaat Hosiana',          'created_at' => now(), 'updated_at' => now()],
            ['key' => 'church_address1','value' => 'Jl. Rajawali Selatan V No. 7', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'church_address2','value' => 'Jakarta Pusat 10772',           'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
