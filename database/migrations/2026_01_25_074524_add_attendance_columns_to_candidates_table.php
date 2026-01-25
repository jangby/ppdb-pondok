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
    Schema::table('candidates', function (Blueprint $table) {
        $table->timestamp('waktu_hadir')->nullable(); // Waktu scan
        $table->integer('nomor_antrian')->nullable(); // Urutan kedatangan
    });
}

public function down()
{
    Schema::table('candidates', function (Blueprint $table) {
        $table->dropColumn(['waktu_hadir', 'nomor_antrian']);
    });
}
};
