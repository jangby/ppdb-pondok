<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interview_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Contoh: "Sesi Pagi - Ruang 1"
            $table->string('token')->unique(); // Kode acak (slug) untuk URL
            $table->boolean('is_active')->default(true); // Saklar On/Off
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interview_sessions');
    }
};