<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1. Ubah dulu data lama yang ada di database agar tidak terhapus (opsional)
        DB::table('test_rooms')->whereIn('jenis', ['Santri Putra', 'Santri Putri'])->update(['jenis' => 'Santri']);

        // 2. Ubah struktur kolom dari ENUM menjadi VARCHAR (String biasa) agar lebih fleksibel
        DB::statement("ALTER TABLE test_rooms MODIFY COLUMN jenis VARCHAR(50) NOT NULL");
    }

    public function down()
    {
        DB::statement("ALTER TABLE test_rooms MODIFY COLUMN jenis ENUM('Santri Putra', 'Santri Putri', 'Wali') NOT NULL");
    }
};