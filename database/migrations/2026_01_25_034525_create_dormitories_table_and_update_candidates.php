<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Asrama
        Schema::create('dormitories', function (Blueprint $table) {
            $table->id();
            $table->string('nama_asrama'); // Misal: Asrama Al-Fatih
            $table->enum('jenis_asrama', ['Putra', 'Putri']); // Gender
            $table->integer('kapasitas')->default(0); // Kuota
            $table->string('link_group_wa')->nullable(); // Link WA
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Tambah Relasi di Tabel Santri (Candidates)
        Schema::table('candidates', function (Blueprint $table) {
            // Nullable karena saat daftar mungkin belum ada asrama
            $table->foreignId('dormitory_id')->nullable()->constrained('dormitories')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->dropForeign(['dormitory_id']);
            $table->dropColumn('dormitory_id');
        });
        Schema::dropIfExists('dormitories');
    }
};