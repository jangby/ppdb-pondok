<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah tipe kolom 'jenjang' dari ENUM menjadi VARCHAR (String Bebas)
        // Kita gunakan Raw SQL agar kompatibel dengan semua versi MySQL/MariaDB
        DB::statement("ALTER TABLE candidates MODIFY COLUMN jenjang VARCHAR(50) NOT NULL");
        DB::statement("ALTER TABLE payment_types MODIFY COLUMN jenjang VARCHAR(50) NOT NULL");
    }

    public function down(): void
    {
        // Kembalikan ke ENUM (Opsional)
        // DB::statement("ALTER TABLE candidates MODIFY COLUMN jenjang ENUM('SMP', 'SMK') NOT NULL");
    }
};