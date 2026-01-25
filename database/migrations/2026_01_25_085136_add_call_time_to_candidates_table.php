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
        // Menandakan kapan dipanggil. Jika NULL = Belum dipanggil.
        $table->timestamp('waktu_panggil')->nullable(); 
        
        // Opsional: Mencatat siapa panitia yang memanggil (jika perlu audit)
        $table->unsignedBigInteger('dipanggil_oleh')->nullable();
    });
}

public function down()
{
    Schema::table('candidates', function (Blueprint $table) {
        $table->dropColumn(['waktu_panggil', 'dipanggil_oleh']);
    });
}
};
