<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // LANGKAH 1: Ubah kolom 'jenis' jadi VARCHAR/STRING dulu
        // Ini untuk melepaskan ikatan ENUM lama, supaya kita bisa edit datanya
        DB::statement("ALTER TABLE test_rooms MODIFY COLUMN jenis VARCHAR(50)");

        // LANGKAH 2: Update data lama
        // Ubah semua yang tulisannya 'Santri' menjadi 'Santri Putra' (sebagai default)
        DB::table('test_rooms')
            ->where('jenis', 'Santri')
            ->update(['jenis' => 'Santri Putra']);

        // LANGKAH 3: Baru kita kunci lagi dengan ENUM yang baru
        DB::statement("ALTER TABLE test_rooms MODIFY COLUMN jenis ENUM('Santri Putra', 'Santri Putri', 'Wali') NOT NULL DEFAULT 'Santri Putra'");
    }

    public function down(): void
    {
        // Kembalikan ke teks dulu
        DB::statement("ALTER TABLE test_rooms MODIFY COLUMN jenis VARCHAR(50)");

        // Kembalikan nama 'Santri Putra/Putri' jadi 'Santri' saja
        DB::table('test_rooms')
            ->whereIn('jenis', ['Santri Putra', 'Santri Putri'])
            ->update(['jenis' => 'Santri']);

        // Kunci balik ke ENUM lama
        DB::statement("ALTER TABLE test_rooms MODIFY COLUMN jenis ENUM('Santri', 'Wali') NOT NULL DEFAULT 'Santri'");
    }
};