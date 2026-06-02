<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\CandidateBill;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http; 
use Illuminate\Support\Facades\Log; // [PENTING] Tambahkan Log
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
                
                // --- FITUR TOLERANSI ANOMALI ---
                $sisa_sekarang = $bill->nominal_tagihan - $bill->nominal_terbayar;
                // Jika sisa tagihan menyisakan angka anomali (misal di bawah Rp 10), genapkan otomatis.
                if ($sisa_sekarang > 0 && $sisa_sekarang <= 10) {
                    $bill->nominal_terbayar = $bill->nominal_tagihan; 
                }
                // -------------------------------
                
                // Cek Lunas
                if ($bill->nominal_terbayar >= $bill->nominal_tagihan) {
                    $bill->status = 'Lunas';
                } else {
                    $bill->status = 'Cicilan';
                }
                $bill->save();
            }

            DB::commit();

            // ====================================================================
            // [BARU] TEMBAK WEBHOOK KE BOT BESERTA LOGGING & FORMAT NOMOR
            // ====================================================================
            try {
                $candidate = Candidate::with('parent')->find($candidate_id);

                // Cari No WA Wali (Prioritas 1: Tabel Verification, Prioritas 2: Tabel Parent)
                $no_wa = null;
                $verification = \App\Models\Verification::where('file_perjanjian', $candidate->file_perjanjian)->first();

                if ($verification && !empty($verification->no_wa)) {
                    $no_wa = $verification->no_wa;
                } elseif ($candidate->parent && !empty($candidate->parent->no_hp_ayah)) {
                    $no_wa = $candidate->parent->no_hp_ayah;
                }

                if ($no_wa) {
                    // FORMATTING NOMOR WA: Hapus spasi/strip, ubah '08' jadi '628'
                    $chatId = preg_replace('/[^0-9]/', '', $no_wa);
                    if (substr($chatId, 0, 1) == '0') {
                        $chatId = '62' . substr($chatId, 1);
                    }

                    Log::info("[WEBHOOK KASIR] Mencoba kirim notif ke: {$chatId} untuk santri: {$candidate->nama_lengkap}");

                    // Tembak ke Node.js bot
                    $response = Http::timeout(5)->post('http://72.61.208.130:5000/api/notifikasi-ppdb', [
                        'no_wa'  => $chatId,
                        'tipe'   => 'terima_bayar',
                        'nama'   => $candidate->nama_lengkap,
                        'detail' => $totalReceived 
                    ]);

                    Log::info("[WEBHOOK KASIR] Response dari Bot: " . $response->body());
                } else {
                    Log::warning("[WEBHOOK KASIR] Batal kirim WA. Nomor WA tidak ditemukan di database untuk kandidat ID: {$candidate_id}");
                }
            } catch (\Exception $e) {
                Log::error("[WEBHOOK KASIR] Terjadi Error Webhook: " . $e->getMessage());
            }
            // ====================================================================

            return back()->with('success', 'Pembayaran berhasil disimpan & Notifikasi WA terkirim!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function print($id)
    {
        $transaction = Transaction::with(['candidate', 'details.bill.payment_type', 'admin'])
                        ->findOrFail($id);

        $settings = Setting::all()->pluck('value', 'key');
        $customPaper = [0, 0, 226.77, 1000];

        $pdf = Pdf::loadView('admin.receipt.thermal', compact('transaction', 'settings'))
                    ->setPaper($customPaper);

        return $pdf->stream('Struk-' . $transaction->kode_transaksi . '.pdf');
    }

    public function getDataForPrinter($id)
    {
        $transaction = \App\Models\Transaction::with(['candidate.bills', 'details.bill.payment_type'])->findOrFail($id);
        $candidate = $transaction->candidate;

        $totalTagihan = $candidate->bills->sum('nominal_tagihan');
        $totalTerbayar = $candidate->bills->sum('nominal_terbayar');
        $sisaTagihan = $totalTagihan - $totalTerbayar;

        return response()->json([
            'status' => 'success',
            'data' => [
                'invoice'        => $transaction->kode_transaksi, 
                'tanggal'        => $transaction->created_at->format('d/m/Y H:i'),
                'nama'           => $candidate->nama_lengkap,
                'no_daftar'      => $candidate->no_daftar,
                'jenis'          => $transaction->details->pluck('bill.payment_type.nama_pembayaran')->implode(', '),
                'bayar_sekarang' => number_format($transaction->total_bayar, 0, ',', '.'),
                'total_tagihan'  => number_format($totalTagihan, 0, ',', '.'),
                'sisa_tagihan'   => number_format($sisaTagihan, 0, ',', '.'),
                'keterangan'     => $transaction->keterangan ?? '-',
                'petugas'        => auth()->user()->name ?? 'Admin',
            ]
        ]);
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $transaction = Transaction::with('details')->findOrFail($id);

            foreach ($transaction->details as $detail) {
                $bill = CandidateBill::lockForUpdate()->find($detail->candidate_bill_id);
                
                if ($bill) {
                    $bill->nominal_terbayar -= $detail->nominal;
                    
                    if ($bill->nominal_terbayar <= 0) {
                        $bill->nominal_terbayar = 0;
                        $bill->status = 'Belum Lunas';
                    } elseif ($bill->nominal_terbayar < $bill->nominal_tagihan) {
                        $bill->status = 'Cicilan';
                    }
                    
                    $bill->save();
                }
            }

            $transaction->details()->delete(); 
            $transaction->delete();

            DB::commit();
            return back()->with('success', 'Transaksi berhasil dibatalkan dan tagihan telah disesuaikan kembali.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
        }
    }
}