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
    Schema::create('transactions', function (Blueprint $table) {
        $table->id();
        $table->string('kode_transaksi')->unique(); // TRX-001
        $table->foreignId('candidate_id')->constrained('candidates');
        $table->foreignId('user_id'); // Admin yang menginput (diambil dari tabel users)
        
        $table->decimal('total_bayar', 15, 2); // Total uang fisik yang diterima
        $table->date('tanggal_bayar');
        $table->text('keterangan')->nullable();
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
