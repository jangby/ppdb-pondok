<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('candidates', function (Blueprint $table) {
            // Menambahkan kolom json untuk menyimpan checklist berkas
            $table->json('kelengkapan_berkas')->nullable()->after('status_seleksi');
        });
    }

    public function down()
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->dropColumn('kelengkapan_berkas');
        });
    }
};