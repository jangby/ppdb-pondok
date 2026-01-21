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
    Schema::create('settings', function (Blueprint $table) {
        $table->id();
        $table->string('nama_gelombang')->default('Gelombang 1');
        $table->date('tanggal_buka')->nullable();
        $table->date('tanggal_tutup')->nullable();
        $table->boolean('is_open')->default(true); // Saklar Utama (ON/OFF)
        $table->text('pengumuman')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
