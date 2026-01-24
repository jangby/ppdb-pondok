<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\InterviewQuestion;
use App\Models\InterviewAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SantriInterviewController extends Controller
{
    // 1. Halaman Login (Input No Daftar)
    public function login()
    {
        return view('interview.santri.login');
    }

    // 2. Proses Cek No Daftar
    public function check(Request $request)
    {
        $request->validate([
            'no_daftar' => 'required|string',
        ]);

        // Cari Santri berdasarkan No Daftar (Pastikan statusnya Lulus Administrasi)
        $candidate = Candidate::where('no_daftar', $request->no_daftar)
                        ->whereIn('status_seleksi', ['Lulus', 'Diterima', 'Approved', 'Lulus Administrasi'])
                        ->first();

        if (!$candidate) {
            return back()->with('error', 'Nomor pendaftaran tidak ditemukan atau belum lulus administrasi.');
        }

        // Simpan ID santri ke Session sementara
        Session::put('santri_id', $candidate->id);

        return redirect()->route('interview.santri.form');
    }

    // 3. Halaman Form Soal
    public function form()
    {
        // Cek Session
        if (!Session::has('santri_id')) {
            return redirect()->route('interview.santri.login');
        }

        $candidate = Candidate::find(Session::get('santri_id'));
        
        // Ambil Soal khusus TARGET: SANTRI
        $questions = InterviewQuestion::where('target', 'Santri')
                        ->where('is_active', true)
                        ->orderBy('order')
                        ->get();

        // Load jawaban lama jika ada (biar bisa edit)
        $existingAnswers = InterviewAnswer::where('candidate_id', $candidate->id)
                            ->pluck('answer', 'interview_question_id')
                            ->toArray();

        return view('interview.santri.form', compact('candidate', 'questions', 'existingAnswers'));
    }

    // 4. Simpan Jawaban
    public function store(Request $request)
    {
        if (!Session::has('santri_id')) return redirect()->route('interview.santri.login');

        $candidateId = Session::get('santri_id');
        $answers = $request->input('answers', []);

        foreach ($answers as $questionId => $answerVal) {
            $finalAnswer = is_array($answerVal) ? implode(', ', $answerVal) : $answerVal;

            InterviewAnswer::updateOrCreate(
                ['candidate_id' => $candidateId, 'interview_question_id' => $questionId],
                ['answer' => $finalAnswer]
            );
        }

        return redirect()->route('interview.santri.success');
    }

    // 5. Halaman Sukses
    public function success()
    {
        // Hapus session agar tidak bisa back
        Session::forget('santri_id');
        return view('interview.santri.success');
    }
}