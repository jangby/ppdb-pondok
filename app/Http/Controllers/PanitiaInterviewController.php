<?php

namespace App\Http\Controllers;

use App\Models\InterviewSession;
use App\Models\Candidate;
use App\Models\InterviewQuestion;
use App\Models\InterviewAnswer;
use App\Models\Dormitory; // [BARU] Untuk Auto Asrama
use App\Models\Setting;   // [BARU] Untuk Info Sekolah
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // [BARU] Untuk Kirim WA
use Illuminate\Support\Facades\Log;  // [BARU] Untuk Log WA

class PanitiaInterviewController extends Controller
{
    // Helper: Cek Token Sesi Valid & Aktif
    private function checkSession($token)
    {
        $session = InterviewSession::where('token', $token)->where('is_active', true)->first();
        if (!$session) abort(403, 'Sesi wawancara tidak aktif atau token salah. Silakan hubungi Admin.');
        return $session;
    }

    public function index($token, Request $request)
    {
        $session = $this->checkSession($token);
        $query = Candidate::query();

        // Filter Status
        $query->where(function($q) {
            $q->where('status_seleksi', 'LIKE', '%Lulus%')
              ->orWhere('status_seleksi', 'LIKE', '%Diterima%')
              ->orWhere('status_seleksi', 'LIKE', '%Approved%')
              ->orWhere('status_seleksi', 'LIKE', '%Verifikasi%');
        });

        // Search
        if ($request->has('q') && !empty($request->q)) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%$search%")
                  ->orWhere('no_daftar', 'like', "%$search%");
            });
        }

        $candidates = $query->latest()->limit(100)->get();
        return view('interview.panitia.index', compact('candidates', 'session', 'token'));
    }

    public function form($token, $candidate_id)
    {
        $session = $this->checkSession($token);
        $candidate = Candidate::findOrFail($candidate_id);
        
        $questions = InterviewQuestion::where('target', 'Wali')
                        ->where('is_active', true)
                        ->orderBy('order')
                        ->get();

        $existingAnswers = InterviewAnswer::where('candidate_id', $candidate_id)
                            ->pluck('answer', 'interview_question_id')
                            ->toArray();

        return view('interview.panitia.form', compact('candidate', 'questions', 'token', 'existingAnswers'));
    }

    public function store(Request $request, $token, $candidate_id)
    {
        $this->checkSession($token);
        
        // 1. Simpan Jawaban Panitia (Wali)
        $answers = $request->input('answers', []);
        foreach ($answers as $questionId => $answerVal) {
            $finalAnswer = is_array($answerVal) ? implode(', ', $answerVal) : $answerVal;
            InterviewAnswer::updateOrCreate(
                ['candidate_id' => $candidate_id, 'interview_question_id' => $questionId],
                ['answer' => $finalAnswer]
            );
        }

        // ============================================================
        // [LOGIKA BARU] CEK KELENGKAPAN & AUTO LULUS + AUTO ASRAMA
        // ============================================================
        
        $candidate = Candidate::findOrFail($candidate_id);

        // Cek apakah Santri SUDAH mengisi asesmen mandiri?
        // Kita cek apakah ada jawaban di tabel interview_answers yang soalnya bertarget 'Santri'
        $hasSantriAnswers = InterviewAnswer::where('candidate_id', $candidate_id)
                            ->whereHas('question', fn($q) => $q->where('target', 'Santri'))
                            ->exists();

        // Jika Santri SUDAH isi & Panitia BARU SAJA submit (Wali juga sudah)
        if ($hasSantriAnswers) {
            
            // A. Update Status Jadi Lulus (Jika belum)
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

            // Refresh data candidate untuk mengambil data asrama terbaru
            $candidate->refresh();

            // C. Kirim Notifikasi WA (Lulus + Info Asrama)
            $this->sendWhatsAppNotification($candidate);
            
            return redirect()->route('interview.panitia.index', $token)
                ->with('success', 'Wawancara Selesai! Santri dinyatakan LULUS & Info Asrama telah dikirim ke WA Wali.');
        }

        return redirect()->route('interview.panitia.index', $token)
            ->with('success', 'Data wawancara Wali tersimpan. (Menunggu Santri mengisi asesmen untuk kelulusan otomatis)');
    }

    // --- HELPER KIRIM WA ---
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

            // 3. Siapkan Data Sekolah & Asrama
            $namaSekolah = Setting::where('key', 'nama_sekolah')->value('value') ?? 'Pondok Pesantren';
            
            // Cek apakah punya asrama
            $infoAsrama = "";
            if ($candidate->dormitory) {
                $infoAsrama = "🏠 Penempatan Asrama:\n"
                            . "*{$candidate->dormitory->nama_asrama}*\n";
                
                if ($candidate->dormitory->link_group_wa) {
                    $infoAsrama .= "🔗 Link Grup Asrama: {$candidate->dormitory->link_group_wa}\n";
                } else {
                    $infoAsrama .= "(Link grup belum tersedia)\n";
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
            ])->post(env('WAHA_BASE_URL', 'http://72.61.208.130:3000') . '/api/sendText', [
                'session' => 'default',
                'chatId'  => $chatId,
                'text'    => $pesanWA
            ]);

            Log::info("WA Hasil Interview Terkirim ke: " . $candidate->nama_lengkap);

        } catch (\Exception $e) {
            Log::error("Gagal Kirim WA Interview: " . $e->getMessage());
        }
    }
}