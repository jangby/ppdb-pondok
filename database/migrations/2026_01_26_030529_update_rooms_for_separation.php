<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    // 1. Tambah Jenis di Tabel Ruangan
    Schema::table('test_rooms', function (Blueprint $table) {
        $table->enum('jenis', ['Santri', 'Wali'])->default('Santri')->after('nama_ruangan');
    });

    // 2. Ubah Tabel Candidates (Hapus yang lama, buat 2 baru)
    Schema::table('candidates', function (Blueprint $table) {
        // Hapus kolom lama jika ada
        if (Schema::hasColumn('candidates', 'test_room_id')) {
            $table->dropForeign(['test_room_id']);
            $table->dropColumn('test_room_id');
        }

        // Buat 2 kolom baru
        $table->foreignId('santri_room_id')->nullable()->constrained('test_rooms')->nullOnDelete();
        $table->foreignId('wali_room_id')->nullable()->constrained('test_rooms')->nullOnDelete();
    });
}

public function down()
{
    // Rollback logic...
}
};
