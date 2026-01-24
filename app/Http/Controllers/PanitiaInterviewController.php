<?php

namespace App\Http\Controllers;

use App\Models\InterviewSession;
use App\Models\Candidate;
use App\Models\InterviewQuestion;
use App\Models\InterviewAnswer;
use Illuminate\Http\Request;

class PanitiaInterviewController extends Controller
{
    // Helper: Cek Token Sesi Valid & Aktif
    private function checkSession($token)
    {
        $session = InterviewSession::where('token', $token)->where('is_active', true)->first();
        if (!$session) abort(403, 'Sesi wawancara tidak aktif atau token salah. Silakan hubungi Admin.');
        return $session;
    }

    // Halaman List Santri (Mobile View)
    public function index($token, Request $request)
    {
        $session = $this->checkSession($token);

        $query = Candidate::query();
        
        // 1. Logika Pencarian (Jika ada input search)
        if ($request->has('q') && !empty($request->q)) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%$search%")
                  ->orWhere('no_daftar', 'like', "%$search%");
            });
        } else {
            // 2. [PERBAIKAN DISINI] Filter Default
            // Kita gunakan whereIn agar bisa menangkap berbagai variasi status "Lulus"
            // Pastikan status di database Anda masuk ke salah satu list ini
            $query->whereIn('status_seleksi', ['Lulus', 'Diterima', 'Approved', 'Lulus Administrasi']);
        }

        // Urutkan dari yang terbaru, ambil 50 data agar tidak terlalu berat di HP
        $candidates = $query->latest()->limit(50)->get();

        return view('interview.panitia.index', compact('candidates', 'session', 'token'));
    }

    // Halaman Form Pertanyaan
    public function form($token, $candidate_id)
    {
        $session = $this->checkSession($token);
        $candidate = Candidate::findOrFail($candidate_id);
        
        // Ambil Soal khusus Wali yang Aktif
        $questions = InterviewQuestion::where('target', 'Wali')
                        ->where('is_active', true)
                        ->orderBy('order')
                        ->get();

        // Cek apakah sudah pernah diwawancara sebelumnya (Optional: Load jawaban lama)
        $existingAnswers = InterviewAnswer::where('candidate_id', $candidate_id)
                            ->pluck('answer', 'interview_question_id')
                            ->toArray();

        return view('interview.panitia.form', compact('candidate', 'questions', 'token', 'existingAnswers'));
    }

    // Simpan Jawaban
    public function store(Request $request, $token, $candidate_id)
    {
        $this->checkSession($token);
        
        // Validasi input array
        $answers = $request->input('answers', []);
        
        foreach ($answers as $questionId => $answerVal) {
            // Jika jawaban array (checkbox), gabungkan jadi string koma
            $finalAnswer = is_array($answerVal) ? implode(', ', $answerVal) : $answerVal;

            InterviewAnswer::updateOrCreate(
                [
                    'candidate_id' => $candidate_id,
                    'interview_question_id' => $questionId
                ],
                [
                    'answer' => $finalAnswer
                ]
            );
        }

        return redirect()->route('interview.panitia.index', $token)
            ->with('success', 'Data wawancara berhasil disimpan untuk ' . Candidate::find($candidate_id)->nama_lengkap);
    }
}