<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Setting;

class PublicFinanceController extends Controller
{
    // Menampilkan halaman form input nomor pendaftaran
    public function index()
    {
        return view('public.finance.check');
    }

    // Memproses input dan mengalihkan ke halaman rincian
    public function check(Request $request)
    {
        $request->validate([
            'no_daftar' => 'required|string',
        ], [
            'no_daftar.required' => 'Nomor pendaftaran wajib diisi.'
        ]);

        // Cek apakah nomor pendaftaran ada
        $exists = Candidate::where('no_daftar', $request->no_daftar)->exists();

        if (!$exists) {
            return back()->with('error', 'Nomor pendaftaran tidak ditemukan. Silakan periksa kembali.');
        }

        // Jika ada, arahkan ke halaman rincian yang sudah dibuat sebelumnya
        return redirect()->route('public.finance.show', $request->no_daftar);
    }
    
    public function show($no_daftar)
    {
        $candidate = Candidate::with(['bills.payment_type', 'transactions.admin'])
            ->where('no_daftar', $no_daftar)
            ->firstOrFail();

        $totalTagihan = $candidate->bills->sum('nominal_tagihan');
        $totalTerbayar = $candidate->bills->sum('nominal_terbayar');
        $sisaTagihan = $totalTagihan - $totalTerbayar;
        $persentase = $totalTagihan > 0 ? ($totalTerbayar / $totalTagihan) * 100 : 0;

        return view('public.finance.index', compact(
            'candidate', 'totalTagihan', 'totalTerbayar', 'sisaTagihan', 'persentase'
        ));
    }

    public function downloadReceipt($no_daftar, $transaction_id)
    {
        $transaction = Transaction::with(['candidate', 'details.bill.payment_type', 'admin'])
                        ->findOrFail($transaction_id);

        if ($transaction->candidate->no_daftar !== $no_daftar) {
            abort(403);
        }

        $settings = Setting::all()->pluck('value', 'key');
        
        // Data untuk QR Code
        $qrcodeUrl = route('public.finance.show', $no_daftar);

        // Ukuran kertas Thermal 80mm
        $customPaper = [0, 0, 226.77, 1000];

        $pdf = Pdf::loadView('admin.receipt.thermal', compact('transaction', 'settings', 'qrcodeUrl'))
                    ->setPaper($customPaper);

        // MENGGUNAKAN ->download() AGAR LANGSUNG TERUNDUH
        return $pdf->download('Struk-' . $transaction->kode_transaksi . '.pdf');
    }
}