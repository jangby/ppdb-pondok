<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Mengubah pilihan enum dengan menambahkan 'pimpinan'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'santri', 'pimpinan') DEFAULT 'santri'");
    }

    public function down(): void
    {
        // Mengembalikan ke awal jika di-rollback
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'santri') DEFAULT 'santri'");
    }
};