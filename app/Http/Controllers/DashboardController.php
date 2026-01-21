<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidate;
use App\Models\Setting; // Pastikan model Setting diimport
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Data Kartu Atas
        $totalSantri = Candidate::count();
        $totalLulus  = Candidate::where('status_seleksi', 'Lulus')->count();
        $totalPending = Candidate::where('status_seleksi', 'Pending')->count();
        
        // Placeholder Pendapatan (Bisa diaktifkan jika fitur tagihan sudah jalan)
        $totalPendapatan = 0; 
        // $totalPendapatan = \App\Models\CandidateBill::where('status', 'Lunas')->sum('nominal_tagihan');

        // 2. Data Grafik Pendaftaran (7 Hari Terakhir)
        $grafikHarian = Candidate::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->pluck('total', 'date');

        // Format data untuk ApexCharts
        $chartDates = $grafikHarian->keys()->toArray();
        $chartTotals = $grafikHarian->values()->toArray();

        // 3. Data Grafik Gender
        $genderL = Candidate::where('jenis_kelamin', 'L')->count(); // Pastikan 'L' sesuai database (L/Laki-laki)
        $genderP = Candidate::where('jenis_kelamin', 'P')->count(); // Pastikan 'P' sesuai database (P/Perempuan)

        // 4. Santri Terbaru (5 orang)
        $terbaru = Candidate::latest()->take(5)->get();

        // 5. Status PPDB (PERBAIKAN ERROR DISINI)
        // Kita gunakan helper isOpen() dari Model Setting
        // Hasilnya true/false, kita ubah jadi string 'buka'/'tutup' agar sesuai View
        $ppdbStatus = Setting::isOpen() ? 'buka' : 'tutup';

        return view('dashboard', compact(
            'totalSantri', 'totalLulus', 'totalPending', 'totalPendapatan',
            'chartDates', 'chartTotals', 'genderL', 'genderP',
            'terbaru', 'ppdbStatus'
        ));
    }
}