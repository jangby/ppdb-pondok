<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use Illuminate\Http\Request;

class PublicFinanceController extends Controller
{
    public function show($no_daftar)
    {
        // Cari santri berdasarkan No Daftar
        $candidate = Candidate::with(['bills.payment_type', 'transactions'])
            ->where('no_daftar', $no_daftar)
            ->firstOrFail();

        // Hitung Ringkasan
        $totalTagihan = $candidate->bills->sum('nominal_tagihan');
        $totalTerbayar = $candidate->bills->sum('nominal_terbayar');
        $sisaTagihan = $totalTagihan - $totalTerbayar;
        $persentase = $totalTagihan > 0 ? ($totalTerbayar / $totalTagihan) * 100 : 0;

        return view('public.finance.index', compact(
            'candidate', 'totalTagihan', 'totalTerbayar', 'sisaTagihan', 'persentase'
        ));
    }
}