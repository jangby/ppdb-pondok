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
    Schema::table('verifications', function (Blueprint $table) {
        $table->boolean('wa_tahap1_sent')->default(0)->after('status');
        $table->boolean('wa_tahap2_sent')->default(0)->after('status_pembayaran');
    });
}

public function down(): void
{
    Schema::table('verifications', function (Blueprint $table) {
        $table->dropColumn(['wa_tahap1_sent', 'wa_tahap2_sent']);
    });
}
};
