<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Bank Pertanyaan (Configurable by Admin)
        Schema::create('interview_questions', function (Blueprint $table) {
            $table->id();
            // Target: Apakah pertanyaan ini untuk 'Wali' atau 'Santri'
            $table->enum('target', ['Wali', 'Santri']); 
            
            // Pertanyaannya apa
            $table->string('question'); 
            
            // Tipe Input: Teks (Essay), Pilihan (Radio/Select), Skala (1-5)
            $table->enum('type', ['text', 'choice', 'scale']); 
            
            // Opsi jawaban (JSON) jika tipe 'choice'. Contoh: ["Ya", "Tidak"] atau ["Sangat Baik", "Cukup", "Kurang"]
            $table->json('options')->nullable(); 
            
            $table->integer('order')->default(0); // Urutan soal
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Tabel Jawaban (Menampung Data)
        Schema::create('interview_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('candidates')->onDelete('cascade');
            $table->foreignId('interview_question_id')->constrained('interview_questions')->onDelete('cascade');
            
            // Menyimpan jawaban. Jika pilihan ganda, simpan teks pilihannya.
            $table->text('answer'); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interview_answers');
        Schema::dropIfExists('interview_questions');
    }
};