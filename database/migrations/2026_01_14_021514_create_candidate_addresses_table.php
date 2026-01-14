<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('candidate_addresses', function (Blueprint $table) {
        $table->id();
        $table->foreignId('candidate_id')->constrained('candidates')->cascadeOnDelete();
        $table->string('alamat'); // Kp/Jl/Blok
        $table->string('rt', 5)->nullable();
        $table->string('rw', 5)->nullable();
        $table->string('desa');
        $table->string('kecamatan');
        $table->string('kode_pos', 10)->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_addresses');
    }
};
