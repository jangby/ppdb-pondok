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
    Schema::create('candidates', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id')->nullable(); // Bisa null jika daftar offline (belum punya akun)
        
        // Data Sistem
        $table->string('no_daftar')->unique(); // Misal: REG-2024001
        $table->year('tahun_masuk');
        $table->enum('jalur_pendaftaran', ['Online', 'Offline']);
        $table->enum('status_seleksi', ['Pending', 'Diterima', 'Ditolak'])->default('Pending');
        
        // Biodata Santri
        $table->string('nisn', 20)->unique()->nullable();
        $table->string('nik', 20)->unique()->nullable();
        $table->string('no_kk', 20)->nullable();
        $table->string('nama_lengkap');
        $table->enum('jenis_kelamin', ['L', 'P']);
        $table->string('tempat_lahir');
        $table->date('tanggal_lahir');
        $table->integer('anak_ke');
        $table->integer('jumlah_saudara');
        $table->text('riwayat_penyakit')->nullable();
        $table->enum('jenjang', ['SMP', 'SMK']);
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
