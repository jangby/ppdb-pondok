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
    Schema::create('expense_fund_sources', function (Blueprint $table) {
        $table->id();
        $table->foreignId('expense_id')->constrained('expenses')->cascadeOnDelete();
        $table->foreignId('payment_type_id')->constrained('payment_types'); // Sumber dana
        $table->decimal('nominal', 15, 2); // Berapa ambilnya
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_fund_sources');
    }
};
