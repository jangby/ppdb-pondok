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
    Schema::table('candidates', function (Blueprint $table) {
        // Default 'Baru' agar data lama otomatis dianggap pendaftar baru
        $table->enum('status', ['Baru', 'Lulus', 'Tidak Lulus', 'Cadangan'])->default('Baru')->after('jenjang'); 
    });
}

public function down(): void
{
    Schema::table('candidates', function (Blueprint $table) {
        $table->dropColumn('status');
    });
}
};
