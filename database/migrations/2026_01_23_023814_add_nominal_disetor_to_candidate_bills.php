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
    Schema::table('candidate_bills', function (Blueprint $table) {
        $table->bigInteger('nominal_disetor')->default(0)->after('nominal_terbayar');
    });
}

public function down()
{
    Schema::table('candidate_bills', function (Blueprint $table) {
        $table->dropColumn('nominal_disetor');
    });
}
};
