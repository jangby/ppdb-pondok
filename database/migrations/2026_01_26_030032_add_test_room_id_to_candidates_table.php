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
        $table->foreignId('test_room_id')->nullable()->constrained('test_rooms')->nullOnDelete();
    });
}

public function down()
{
    Schema::table('candidates', function (Blueprint $table) {
        $table->dropConstrainedForeignId('test_room_id');
    });
}
};
