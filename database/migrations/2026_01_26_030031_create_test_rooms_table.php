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
    Schema::create('test_rooms', function (Blueprint $table) {
        $table->id();
        $table->string('nama_ruangan'); // Contoh: Ruang A1, Lab Komputer 1
        $table->string('lokasi')->nullable(); // Contoh: Gedung B Lt 2
        $table->integer('kapasitas')->default(20);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_rooms');
    }
};
