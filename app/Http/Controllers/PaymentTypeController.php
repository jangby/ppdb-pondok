<?php

namespace App\Http\Controllers;

use App\Models\PaymentType;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Candidate;
use App\Models\CandidateBill;

class PaymentTypeController extends Controller
{
    public function index()
    {
        // 1. Ambil Data Pembayaran
        $payments = PaymentType::latest()->get();

        // 2. Ambil Daftar Jenjang dari Setting
        $jenjangs = json_decode(Setting::getValue('list_jenjang'), true) ?? ['SMP', 'SMK'];

        // 3. [BARU] Hitung Total Biaya Per Jenjang
        // Inisialisasi array total dengan 0
        $totalBiaya = array_fill_keys($jenjangs, 0);

        foreach ($payments as $p) {
            if ($p->jenjang == 'Semua') {
                // Jika jenisnya 'Semua', tambahkan ke semua jenjang
                foreach ($jenjangs as $j) {
                    $totalBiaya[$j] += $p->nominal;
                }
            } elseif (in_array($p->jenjang, $jenjangs)) {
                // Jika spesifik, tambahkan ke jenjang itu saja
                $totalBiaya[$p->jenjang] += $p->nominal;
            }
        }

        return view('admin.payment_types.index', compact('payments', 'jenjangs', 'totalBiaya'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pembayaran' => 'required|string',
            'nominal' => 'required|numeric',
            'jenjang' => 'required|string'
        ]);

        $paymentType = PaymentType::create($request->all());

        // OTOMATIS TAMBAH TAGIHAN KE SANTRI YANG RELEVAN
        // Cari santri yang jenjangnya sesuai (atau semua) dan statusnya Lulus/Diterima
        $candidates = Candidate::whereIn('jenjang', [$request->jenjang, 'Semua'])
                               ->where('status', 'Lulus')
                               ->get();

        foreach ($candidates as $candidate) {
            // Cek apakah ini khusus lanjutan atau reguler berdasarkan nama pembayaran
            $isLanjutanBill = str_contains(strtolower($paymentType->nama_pembayaran), 'lanjutan');
            $isLanjutanCandidate = ($candidate->jalur == 'lanjutan');

            // Jika tagihan lanjutan tapi anak reguler, atau tagihan reguler tapi anak lanjutan -> Skip
            if ($isLanjutanBill != $isLanjutanCandidate) {
                continue; 
            }

            CandidateBill::firstOrCreate([
                'candidate_id' => $candidate->id,
                'payment_type_id' => $paymentType->id
            ], [
                'nominal_tagihan' => $paymentType->nominal,
                'nominal_terbayar' => 0,
                'status' => 'Belum Lunas'
            ]);
        }

        return redirect()->route('admin.payment_types.index')->with('success', 'Jenis Pembayaran dan Tagihan Santri berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $paymentType = PaymentType::findOrFail($id);
        $oldNominal = $paymentType->nominal;
        
        $paymentType->update($request->all());

        // OTOMATIS UPDATE NOMINAL TAGIHAN JIKA BELUM ADA PEMBAYARAN (Rp 0)
        if ($oldNominal != $paymentType->nominal) {
            CandidateBill::where('payment_type_id', $paymentType->id)
                ->where('nominal_terbayar', 0) // Hanya yang belum bayar sama sekali
                ->update(['nominal_tagihan' => $paymentType->nominal]);
        }

        return redirect()->route('admin.payment_types.index')->with('success', 'Jenis Pembayaran diperbarui!');
    }

    public function destroy($id)
    {
        $paymentType = PaymentType::findOrFail($id);

        // OTOMATIS HAPUS TAGIHAN YANG BELUM DIBAYAR
        CandidateBill::where('payment_type_id', $paymentType->id)
            ->where('nominal_terbayar', 0)
            ->delete();

        // Peringatan jika ada tagihan yang sudah dibayar (tidak bisa dihapus otomatis)
        $paidBills = CandidateBill::where('payment_type_id', $paymentType->id)->where('nominal_terbayar', '>', 0)->count();

        if ($paidBills > 0) {
            // Opsi: Soft delete master, atau beri peringatan
            return redirect()->route('admin.payment_types.index')->with('error', 'Item dihapus dari daftar, tapi ada '.$paidBills.' tagihan santri yang sudah terbayar (uang masuk) tidak kami hapus demi keamanan laporan kas.');
        }

        $paymentType->delete();
        return redirect()->route('admin.payment_types.index')->with('success', 'Jenis Pembayaran dihapus!');
    }
}