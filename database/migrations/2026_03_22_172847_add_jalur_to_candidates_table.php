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
        // Tambahkan kolom jalur, defaultnya reguler
        $table->enum('jalur', ['reguler', 'lanjutan'])->default('reguler')->after('id');
        // Opsional: untuk menyimpan NIS santri lama agar mudah dilacak
        $table->string('nis_lokal')->nullable()->after('jalur'); 
    });
}

public function down()
{
    Schema::table('candidates', function (Blueprint $table) {
        $table->dropColumn(['jalur', 'nis_lokal']);
    });
}
};
