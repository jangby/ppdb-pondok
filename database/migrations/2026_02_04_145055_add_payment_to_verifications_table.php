<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('verifications', function (Blueprint $table) {
            // Jenjang yang dipilih saat mau bayar (agar admin tahu tarifnya)
            $table->string('jenjang')->nullable()->after('token'); 
            
            // File bukti transfer
            $table->string('bukti_transfer')->nullable()->after('jenjang');
            
            // Status khusus pembayaran: 'unpaid' (belum), 'pending' (sedang dicek), 'paid' (lunas/valid), 'rejected' (ditolak)
            $table->enum('status_pembayaran', ['unpaid', 'pending', 'paid', 'rejected'])
                  ->default('unpaid')
                  ->after('status');
                  
            // Catatan jika pembayaran ditolak
            $table->text('catatan_pembayaran')->nullable()->after('status_pembayaran');
        });
    }

    public function down(): void
    {
        Schema::table('verifications', function (Blueprint $table) {
            $table->dropColumn(['jenjang', 'bukti_transfer', 'status_pembayaran', 'catatan_pembayaran']);
        });
    }
};