<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidate;
use App\Models\Expense;
use App\Models\CandidateBill;
use App\Models\Transaction; 
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // ==========================================
        // 1. STATISTIK KARTU (DIPERBAIKI)
        // ==========================================
        
        $totalSantri = Candidate::count();

        // [FIX] Menggunakan logika fleksibel (LIKE) agar menangkap 'Lulus Administrasi', 'Diterima', dll.
        // Kita cek kolom 'status_seleksi' (sesuai modul interview) ATAU 'status' (jika ada kolom lama)
        $santriLulus = Candidate::where(function($q) {
            $q->where('status_seleksi', 'LIKE', '%Lulus%')
              ->orWhere('status_seleksi', 'LIKE', '%Diterima%')
              ->orWhere('status_seleksi', 'LIKE', '%Approved%')
              // Opsional: Cek kolom 'status' juga untuk backward compatibility
              ->orWhere('status', 'LIKE', '%Lulus%'); 
        })->count();

        // [FIX] Santri Baru biasanya statusnya 'Baru' atau 'Pending'
        $santriBaru = Candidate::where(function($q) {
            $q->where('status_seleksi', 'Pending')
              ->orWhere('status', 'Baru');
        })->count();


        // ==========================================
        // 2. KEUANGAN (Total Saldo)
        // ==========================================
        
        // Pemasukan: Total nominal yang sudah dibayar di tabel tagihan
        $totalPemasukan = CandidateBill::sum('nominal_terbayar');
        
        // Pengeluaran: Total dari tabel expenses
        $totalPengeluaran = Expense::sum('total_keluar');
        
        $saldoSaatIni = $totalPemasukan - $totalPengeluaran;


        // ==========================================
        // 3. GRAFIK TAHUN INI
        // ==========================================
        $currentYear = date('Y');
        $months = range(1, 12);
        
        // Data Grafik Pemasukan
        $incomeData = DB::table('candidate_bills')
            ->selectRaw('MONTH(updated_at) as month, SUM(nominal_terbayar) as total')
            ->whereYear('updated_at', $currentYear)
            ->where('nominal_terbayar', '>', 0)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Data Grafik Pengeluaran
        $expenseData = Expense::selectRaw('MONTH(tanggal) as month, SUM(total_keluar) as total')
            ->whereYear('tanggal', $currentYear)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Mapping data agar urut Jan-Des (Isi 0 jika kosong)
        $chartDataIncome = [];
        $chartDataExpense = [];
        
        foreach ($months as $month) {
            $chartDataIncome[] = $incomeData[$month] ?? 0;
            $chartDataExpense[] = $expenseData[$month] ?? 0;
        }


        // ==========================================
        // 4. DATA TABEL TERBARU
        // ==========================================
        $latestCandidates = Candidate::latest()->take(5)->get();
        $latestExpenses = Expense::with('user')->latest()->take(5)->get();

        return view('dashboard', compact(
            'totalSantri', 'santriLulus', 'santriBaru',
            'totalPemasukan', 'totalPengeluaran', 'saldoSaatIni',
            'chartDataIncome', 'chartDataExpense',
            'latestCandidates', 'latestExpenses'
        ));
    }
}