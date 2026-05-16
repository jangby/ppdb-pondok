<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\InterviewQuestion;
use App\Models\InterviewAnswer;
use App\Models\Dormitory; // [PENTING] Untuk Auto Asrama
use App\Models\Setting;   // [PENTING] Untuk Info Sekolah
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // [PENTING] Untuk WA
use Illuminate\Support\Facades\Log;
use App\Models\InterviewSession;

class SantriInterviewController extends Controller
{
    // Halaman Login Santri (Scan QR)
    public function login()
    {
        // 1. CEK APAKAH ADA SESI YANG AKTIF?
        $activeSession = InterviewSession::where('is_active', true)->first();

        // 2. JIKA TIDAK ADA SESI AKTIF -> TAMPILKAN HALAMAN TUNGGU
        if (!$activeSession) {
            // PERBAIKAN: Sesuaikan dengan folder resources/views/interview/santri/wait.blade.php
            return view('interview.santri.wait'); 
        }

        // 3. JIKA ADA SESI AKTIF -> TAMPILKAN LOGIN
        // PERBAIKAN: Sesuaikan dengan folder resources/views/interview/santri/login.blade.php
        return view('interview.santri.login');
    }

    // Proses Cek Nomor Pendaftaran
    public function check(Request $request)
    {
        $request->validate([
            'no_daftar' => 'required|string'
        ]);

        // Cek lagi sesi (untuk keamanan ganda)
        $activeSession = InterviewSession::where('is_active', true)->first();
        if (!$activeSession) {
            return back()->with('error', 'Sesi ujian belum dibuka oleh panitia.');
        }

        // Cari Santri
        $candidate = Candidate::where('no_daftar', $request->no_daftar)->first();

        if (!$candidate) {
            return back()->with('error', 'Nomor Pendaftaran tidak ditemukan.');
        }

        // Simpan sesi santri (login)
        session(['santri_id' => $candidate->id]);
        session(['santri_nama' => $candidate->nama_lengkap]);

        // Redirect ke form
        return redirect()->route('interview.santri.form');
    }
    
    // Halaman Form Soal
    public function form()
    {
        if (!session('santri_id')) {
            return redirect()->route('interview.santri.login');
        }
        
        // Cek Sesi Lagi (Biar kalau ditutup tengah jalan, santri keluar)
        $activeSession = InterviewSession::where('is_active', true)->first();
        if (!$activeSession) {
            return redirect()->route('interview.santri.login');
        }

        // Ambil Data Pertanyaan
        $questions = \App\Models\InterviewQuestion::where('is_active', true)->get();
        
        // PERBAIKAN: Sesuaikan dengan folder resources/views/interview/santri/form.blade.php
        return view('interview.santri.form', compact('questions'));
    }

    // Simpan Jawaban Santri
    public function store(Request $request)
    {
        if (!session()->has('santri_id')) return redirect()->route('interview.santri.login');
        
        $candidateId = session('santri_id');
        $candidate = Candidate::findOrFail($candidateId);

        // 1. Simpan Jawaban
        $answers = $request->input('answers', []);
        foreach ($answers as $questionId => $answerVal) {
            $finalAnswer = is_array($answerVal) ? implode(', ', $answerVal) : $answerVal;
            InterviewAnswer::updateOrCreate(
                ['candidate_id' => $candidateId, 'interview_question_id' => $questionId],
                ['answer' => $finalAnswer]
            );
        }

        // ============================================================
        // [LOGIKA BARU] CEK APAKAH PANITIA (WALI) SUDAH SELESAI JUGA?
        // ============================================================
        
        // Cek apakah ada jawaban WALI di database?
        $hasWaliAnswers = InterviewAnswer::where('candidate_id', $candidateId)
                            ->whereHas('question', fn($q) => $q->where('target', 'Wali'))
                            ->exists();

        // JIKA Panitia sudah submit duluan, dan Santri baru submit sekarang (Keduanya Lengkap)
        if ($hasWaliAnswers) {
            
            // A. Update Status Jadi Lulus
            if (!in_array($candidate->status_seleksi, ['Lulus', 'Diterima'])) {
                $candidate->update(['status_seleksi' => 'Lulus']);
            }

            // B. Auto Assign Asrama (Jika belum punya kamar)
            if (is_null($candidate->dormitory_id)) {
                $dormId = Dormitory::getAutoAssignedDorm($candidate->jenis_kelamin);
                if ($dormId) {
                    $candidate->update(['dormitory_id' => $dormId]);
                }
            }

            // Refresh data agar dapat relasi asrama terbaru
            $candidate->refresh();

            // C. Kirim Notifikasi WA (Lulus + Info Asrama)
            $this->sendWhatsAppNotification($candidate);
            
            Log::info("Wawancara Lengkap (Triggered by Santri): WA Terkirim ke " . $candidate->nama_lengkap);
        }

        return redirect()->route('interview.santri.success');
    }

    public function success()
    {
        return view('interview.santri.success');
    }

    // --- HELPER KIRIM WA (SAMA DENGAN PANITIA CONTROLLER) ---
    private function sendWhatsAppNotification($candidate)
    {
        try {
            // 1. Ambil No HP
            $rawNo = $candidate->parent->no_hp_ayah ?? $candidate->parent->no_hp_ibu;
            if (empty($rawNo)) return;

            // 2. Format No HP
            $cleanNo = preg_replace('/[^0-9]/', '', $rawNo); 
            if (substr($cleanNo, 0, 1) == '0') $cleanNo = '62' . substr($cleanNo, 1);
            elseif (substr($cleanNo, 0, 2) != '62') $cleanNo = '62' . $cleanNo;
            $chatId = $cleanNo . '@c.us';

            // 3. Data Info
            $namaSekolah = Setting::where('key', 'nama_sekolah')->value('value') ?? 'Pondok Pesantren';
            
            // Info Asrama
            $infoAsrama = "";
            if ($candidate->dormitory) {
                $infoAsrama = "🏠 Penempatan Asrama:\n"
                            . "*{$candidate->dormitory->nama_asrama}*\n";
                
                if ($candidate->dormitory->link_group_wa) {
                    $infoAsrama .= "🔗 Link Grup Asrama: {$candidate->dormitory->link_group_wa}\n";
                }
            } else {
                $infoAsrama = "🏠 Penempatan Asrama: *Masih dalam proses*\n";
            }

            // 4. Susun Pesan
            $pesanWA = "Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\n"
                     . "Yth. Bapak/Ibu Wali Santri,\n"
                     . "Alhamdulillah, rangkaian seleksi telah selesai. Kami ucapkan *SELAMAT!*.\n\n"
                     . "Putra/Putri Anda:\n"
                     . "👤 Nama: *{$candidate->nama_lengkap}*\n"
                     . "📝 No. Daftar: *{$candidate->no_daftar}*\n"
                     . "🎓 Jenjang: *{$candidate->jenjang}*\n\n"
                     . "Dinyatakan *LULUS SELEKSI INTERVIEW* di *{$namaSekolah}*.\n\n"
                     . "--------------------------------\n"
                     . "ℹ️ INFO ASRAMA & KOORDINASI\n"
                     . "--------------------------------\n"
                     . $infoAsrama . "\n"
                     . "Mohon segera bergabung ke grup WhatsApp Asrama diatas untuk koordinasi perlengkapan dan kedatangan.\n\n"
                     . "Terima kasih.\n"
                     . "Wassalamu'alaikum Warahmatullahi Wabarakatuh.";

            // 5. Kirim API
            Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Api-Key'    => env('WAHA_API_KEY', '0f0eb5d196b6459781f7d854aac5050e'), 
            ])->post(env('WAHA_BASE_URL', 'http://72.61.208.130:3001') . '/api/sendText', [
                'session' => 'default',
                'chatId'  => $chatId,
                'text'    => $pesanWA
            ]);

        } catch (\Exception $e) {
            Log::error("Gagal Kirim WA Santri: " . $e->getMessage());
        }
    }
}