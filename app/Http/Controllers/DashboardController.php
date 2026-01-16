<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidate;
use App\Models\Setting;
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
        
        // Asumsi pendapatan dari tabel tagihan (sesuaikan jika logic berbeda)
        // $totalPendapatan = \App\Models\CandidateBill::where('status', 'Lunas')->sum('nominal_tagihan');
        $totalPendapatan = 0; // Placeholder jika belum ada transaksi

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
        $genderL = Candidate::where('jenis_kelamin', 'Laki-laki')->count();
        $genderP = Candidate::where('jenis_kelamin', 'Perempuan')->count();

        // 4. Santri Terbaru (5 orang)
        $terbaru = Candidate::latest()->take(5)->get();

        // 5. Status PPDB (Untuk Toggle)
        $ppdbStatus = Setting::where('key', 'ppdb_status')->value('value') ?? 'tutup';

        return view('dashboard', compact(
            'totalSantri', 'totalLulus', 'totalPending', 'totalPendapatan',
            'chartDates', 'chartTotals', 'genderL', 'genderP',
            'terbaru', 'ppdbStatus'
        ));
    }
}