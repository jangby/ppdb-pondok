<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Setting;

class PublicFinanceController extends Controller
{
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