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
    Schema::create('candidate_parents', function (Blueprint $table) {
        $table->id();
        $table->foreignId('candidate_id')->constrained('candidates')->cascadeOnDelete();
        
        // Data Ayah
        $table->string('nama_ayah');
        $table->string('nik_ayah')->nullable();
        $table->string('thn_lahir_ayah')->nullable();
        $table->string('pendidikan_ayah')->nullable();
        $table->string('pekerjaan_ayah')->nullable();
        $table->decimal('penghasilan_ayah', 15, 2)->default(0);
        $table->string('no_hp_ayah')->nullable();
        
        // Data Ibu
        $table->string('nama_ibu');
        $table->string('nik_ibu')->nullable();
        $table->string('thn_lahir_ibu')->nullable();
        $table->string('pendidikan_ibu')->nullable();
        $table->string('pekerjaan_ibu')->nullable();
        $table->decimal('penghasilan_ibu', 15, 2)->default(0);
        $table->string('no_hp_ibu')->nullable();
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_parents');
    }
};
