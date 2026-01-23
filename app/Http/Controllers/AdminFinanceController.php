<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense; 
use App\Models\Candidate;
use App\Models\CandidateBill;
use App\Models\PaymentType;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DepositExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // Import Auth

class AdminFinanceController extends Controller
{
    /**
     * Menampilkan Halaman Dashboard Keuangan
     */
    public function index()
    {
        // Ambil data pengeluaran
        $expenses = Expense::latest()->get(); 
        
        // Ambil data PaymentType untuk Dropdown Sumber Dana
        $paymentTypes = PaymentType::all(); 

        return view('admin.finance.index', compact('expenses', 'paymentTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'keterangan' => 'required|string',
            'nominal'    => 'required|numeric',
            'tanggal'    => 'required|date',
            'source_id'  => 'nullable|exists:payment_types,id', // Validasi input baru
        ]);

        // LOGIKA BARU: Gabungkan Nama Item ke Judul
        $judulFix = $request->keterangan;
        
        if ($request->source_id) {
            $sumber = PaymentType::find($request->source_id);
            if ($sumber) {
                // Hasilnya: "Beli ATK (Sumber: SPP)"
                $judulFix = $judulFix . " (Sumber: " . $sumber->nama_pembayaran . ")";
            }
        }

        Expense::create([
            'user_id'           => auth()->id(),
            'judul_pengeluaran' => $judulFix, // Simpan judul yang sudah digabung
            'total_keluar'      => $request->nominal,
            'tanggal'           => $request->tanggal,
        ]);

        return back()->with('success', 'Data pengeluaran berhasil dicatat.');
    }

    /**
     * Menghapus Pengeluaran
     */
    public function destroy($id)
    {
        $data = Expense::findOrFail($id);
        $data->delete();

        return back()->with('success', 'Data pengeluaran berhasil dihapus.');
    }

    /**
     * LOGIKA UTAMA: Export Excel & Cut-Off Setoran
     */
    public function exportDeposit(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'items' => 'required|array|min:1',
        ], [
            'items.required' => 'Silakan pilih minimal satu item pembayaran.',
        ]);

        $selectedItemIds = $request->items;
        $totalSetor = 0;
        
        // 2. SIAPKAN DATA UNTUK EXCEL
        $candidatesToExport = collect();

        // Cari santri yang punya saldo mengendap
        $candidates = Candidate::whereHas('bills', function($q) use ($selectedItemIds) {
            $q->whereIn('payment_type_id', $selectedItemIds)
              ->whereRaw('(nominal_terbayar - nominal_disetor) > 0');
        })->with(['bills' => function($q) use ($selectedItemIds) {
            $q->whereIn('payment_type_id', $selectedItemIds);
        }])->orderBy('nama_lengkap', 'ASC')->get();

        foreach ($candidates as $candidate) {
            $paymentItems = [];
            $totalRow = 0;
            $hasDeposit = false;

            $headers = PaymentType::whereIn('id', $selectedItemIds)->orderBy('id')->get();

            foreach ($headers as $type) {
                $bill = $candidate->bills->where('payment_type_id', $type->id)->first();
                $saldo = $bill ? ($bill->nominal_terbayar - $bill->nominal_disetor) : 0;

                if ($saldo > 0) $hasDeposit = true;

                $paymentItems[] = [
                    'name' => $type->nama_pembayaran,
                    'amount' => $saldo 
                ];
                $totalRow += $saldo;
            }

            if ($hasDeposit) {
                $candidatesToExport->push((object)[
                    'no_daftar' => $candidate->no_daftar,
                    'nama_lengkap' => $candidate->nama_lengkap,
                    'jenis_kelamin' => $candidate->jenis_kelamin,
                    'payment_items' => $paymentItems,
                    'total_row' => $totalRow
                ]);
                $totalSetor += $totalRow;
            }
        }

        if ($totalSetor == 0) {
            return back()->with('error', 'Tidak ada dana mengendap (belum disetor) untuk item yang dipilih.');
        }

        // 3. DATABASE TRANSACTION
        DB::transaction(function () use ($selectedItemIds, $totalSetor) {
            
            // A. Update nominal_disetor (Reset Saldo)
            $bills = CandidateBill::whereIn('payment_type_id', $selectedItemIds)
                        ->whereRaw('nominal_terbayar > nominal_disetor')
                        ->get();

            foreach($bills as $bill) {
                $bill->update(['nominal_disetor' => $bill->nominal_terbayar]);
            }

            // B. Catat Otomatis ke Expenses
            $itemNames = PaymentType::whereIn('id', $selectedItemIds)->pluck('nama_pembayaran')->implode(', ');
            
            Expense::create([
                'user_id'           => Auth::id(), // <--- PERBAIKAN: Masukkan ID Admin
                'judul_pengeluaran' => "Setoran Keuangan (Auto): $itemNames", 
                'total_keluar'      => $totalSetor,
                'tanggal'           => now(),
            ]);
        });

        // 4. DOWNLOAD EXCEL
        $timestamp = date('d-m-Y_H-i');
        return Excel::download(new DepositExport($candidatesToExport), "Rekap_Setoran_{$timestamp}.xlsx");
    }
}