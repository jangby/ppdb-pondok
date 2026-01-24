<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidate;
use App\Models\InterviewSession;
use App\Models\InterviewQuestion;
use App\Models\InterviewAnswer;
use Illuminate\Support\Facades\DB;
use App\Exports\InterviewExport;
use Maatwebsite\Excel\Facades\Excel;

class InterviewDashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. KPI & STATISTIK
        // Ambil santri yang lolos tahap administrasi (berhak tes)
        // Gunakan operator LIKE agar menangkap 'Lulus', 'Lulus Administrasi', dll
        $baseQuery = Candidate::where(function($q) {
            $q->where('status_seleksi', 'LIKE', '%Lulus%')
              ->orWhere('status_seleksi', 'LIKE', '%Diterima%')
              ->orWhere('status_seleksi', 'LIKE', '%Approved%');
        });

        $totalPeserta = $baseQuery->count();
        
        // Hitung yang sudah ada jawaban (minimal 1)
        $sudahWawancara = InterviewAnswer::distinct('candidate_id')->count('candidate_id');
        $belumWawancara = max(0, $totalPeserta - $sudahWawancara);
        $progress = $totalPeserta > 0 ? round(($sudahWawancara / $totalPeserta) * 100) : 0;

        // 2. DATA TABEL MONITORING (Pagination)
        // Kita eager load jawaban untuk mengecek status per baris
        $candidates = $baseQuery->latest()
            ->with(['interview_answers.question']) // Load jawaban & jenis soalnya
            ->paginate(10);

        // 3. SESSION AKTIF
        $activeSessions = InterviewSession::where('is_active', true)->get();

        // 4. CHART DATA (Contoh: Soal Pilihan Ganda Pertama)
        $chartQuestion = InterviewQuestion::where('type', 'choice')->where('is_active', true)->first();
        $chartData = [];
        
        if ($chartQuestion) {
            $chartData['question'] = $chartQuestion->question;
            $answers = InterviewAnswer::where('interview_question_id', $chartQuestion->id)
                        ->select('answer', DB::raw('count(*) as total'))
                        ->groupBy('answer')
                        ->pluck('total', 'answer')
                        ->toArray();
            
            $chartData['labels'] = array_keys($answers);
            $chartData['series'] = array_values($answers);
        }

        return view('admin.interview.dashboard', compact(
            'totalPeserta', 'sudahWawancara', 'belumWawancara', 
            'progress', 'activeSessions', 'chartData', 'candidates'
        ));
    }

    // [BARU] Halaman Detail Hasil
    public function result($id)
    {
        $candidate = Candidate::with('parent')->findOrFail($id);
        
        // Ambil semua jawaban, urutkan berdasarkan urutan soal
        $answers = InterviewAnswer::with('question')
                    ->where('candidate_id', $id)
                    ->get()
                    ->sortBy('question.order');

        // Pisahkan Jawaban Santri vs Wali
        $santriAnswers = $answers->filter(fn($a) => $a->question->target == 'Santri');
        $waliAnswers = $answers->filter(fn($a) => $a->question->target == 'Wali');

        return view('admin.interview.result', compact('candidate', 'santriAnswers', 'waliAnswers'));
    }

    // Tambahkan method ini di dalam class
public function exportExcel()
{
    return Excel::download(new InterviewExport, 'Rekap-Wawancara-' . date('Y-m-d') . '.xlsx');
}

// Method Cetak Laporan
    public function printResult($id)
    {
        $candidate = \App\Models\Candidate::with('parent')->findOrFail($id);
        
        // Ambil Pengaturan (Logo, Nama Sekolah, Alamat)
        $settings = \App\Models\Setting::all()->pluck('value', 'key');

        $answers = \App\Models\InterviewAnswer::with('question')
                    ->where('candidate_id', $id)
                    ->get()
                    ->sortBy('question.order');

        $santriAnswers = $answers->filter(fn($a) => $a->question->target == 'Santri');
        $waliAnswers = $answers->filter(fn($a) => $a->question->target == 'Wali');

        return view('admin.interview.print', compact('candidate', 'santriAnswers', 'waliAnswers', 'settings'));
    }
}
