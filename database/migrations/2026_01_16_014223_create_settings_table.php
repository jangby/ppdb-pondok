<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kita drop dulu jika ada, lalu buat baru dengan struktur Key-Value
        Schema::dropIfExists('settings');
        
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // Contoh: 'nama_sekolah', 'syarat_pendaftaran'
            $table->text('value')->nullable(); // Isi datanya (bisa teks panjang atau JSON)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};