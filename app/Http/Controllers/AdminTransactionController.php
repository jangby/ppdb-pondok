<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\CandidateBill;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Setting; // [PENTING] Tambahkan Model Setting
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminTransactionController extends Controller
{
    public function store(Request $request, $candidate_id)
    {
        // $request->payments adalah array: [id_tagihan => nominal_bayar]
        $inputs = $request->input('payments', []);
        
        // Filter: Hapus input yang kosong atau 0
        $payments = array_filter($inputs, fn($value) => $value > 0);

        if (empty($payments)) {
            return back()->with('error', 'Tidak ada nominal yang dimasukkan.');
        }

        DB::beginTransaction();
        try {
            // 1. Hitung Total Uang Masuk
            $totalReceived = array_sum($payments);

            // 2. Buat Header Transaksi
            $transaction = Transaction::create([
                'candidate_id' => $candidate_id,
                'user_id' => Auth::id(), // Admin yang login
                'kode_transaksi' => 'TRX-' . time(),
                'total_bayar' => $totalReceived,
                'tanggal_bayar' => now(),
                'keterangan' => 'Pembayaran via Admin Panel',
            ]);

            // 3. Proses Rincian Pembayaran
            foreach ($payments as $billId => $nominal) {
                // Ambil data tagihan asli untuk validasi
                $bill = CandidateBill::lockForUpdate()->findOrFail($billId);

                // Validasi: Jangan sampai bayar lebih dari sisa hutang
                if ($nominal > $bill->sisa_tagihan) {
                    throw new \Exception("Nominal pembayaran untuk {$bill->payment_type->nama_pembayaran} melebihi sisa tagihan!");
                }

                // Simpan Detail
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'candidate_bill_id' => $billId,
                    'nominal' => $nominal
                ]);

                // Update Tagihan
                $bill->nominal_terbayar += $nominal;
                
                // Cek Lunas
                if ($bill->nominal_terbayar >= $bill->nominal_tagihan) {
                    $bill->status = 'Lunas';
                } else {
                    $bill->status = 'Cicilan';
                }
                $bill->save();
            }

            DB::commit();
            return back()->with('success', 'Pembayaran berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function print($id)
    {
        $transaction = Transaction::with(['candidate', 'details.bill.payment_type', 'admin'])
                        ->findOrFail($id);

        // [BARU] Ambil Pengaturan Sekolah untuk Header Struk
        $settings = Setting::all()->pluck('value', 'key');

        // Konfigurasi ukuran kertas 80mm (Thermal Printer Standard)
        // Format: [0, 0, width, height]
        // Width 226.77 pt setara 80mm. Height 1000 agar panjang ke bawah (continuous).
        $customPaper = [0, 0, 226.77, 1000];

        $pdf = Pdf::loadView('admin.receipt.thermal', compact('transaction', 'settings'))
                    ->setPaper($customPaper);

        return $pdf->stream('Struk-' . $transaction->kode_transaksi . '.pdf');
    }
}