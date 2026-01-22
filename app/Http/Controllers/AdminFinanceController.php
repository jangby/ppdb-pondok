<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseFundSource;
use App\Models\PaymentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminFinanceController extends Controller
{
    public function index()
    {
        // 1. REKAPITULASI (Saldo per Item)
        $recap = PaymentType::all()->map(function ($type) {
            // Pemasukan: Dari tagihan santri
            $pemasukan = $type->bills()->sum('nominal_terbayar');

            // Pengeluaran: Dari tabel pivot expense_fund_sources
            $pengeluaran = DB::table('expense_fund_sources')
                             ->where('payment_type_id', $type->id)
                             ->sum('nominal'); 

            return (object) [
                'id' => $type->id,
                'nama' => $type->nama_pembayaran,
                'pemasukan' => $pemasukan,
                'pengeluaran' => $pengeluaran,
                'saldo' => $pemasukan - $pengeluaran
            ];
        });

        // 2. RIWAYAT PENGELUARAN
        $expenses = Expense::with('fundSources.paymentType')
                        ->latest()
                        ->paginate(10);

        // 3. DROPDOWN SUMBER DANA
        $paymentTypes = PaymentType::all();

        return view('admin.finance.index', compact('recap', 'expenses', 'paymentTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'deskripsi' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'nominal' => 'required|numeric|min:1',
            'payment_type_id' => 'required|exists:payment_types,id',
        ]);

        DB::beginTransaction();
        try {
            // PERBAIKAN: Gunakan Manual Assignment agar lebih aman dari isu Mass Assignment
            // 1. Simpan Data Pengeluaran Utama
            $expense = new Expense();
            $expense->judul_pengeluaran = $request->deskripsi;
            $expense->tanggal = $request->tanggal;
            $expense->total_keluar = $request->nominal; // Paksa isi kolom total_keluar
            $expense->user_id = Auth::id();
            $expense->save();

            // 2. Simpan Detail Sumber Dana
            $source = new ExpenseFundSource();
            $source->expense_id = $expense->id;
            $source->payment_type_id = $request->payment_type_id;
            $source->nominal = $request->nominal; // Paksa isi kolom nominal
            $source->save();

            DB::commit();
            return back()->with('success', 'Pengeluaran berhasil dicatat.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        $expense->delete(); 
        return back()->with('success', 'Pengeluaran dibatalkan.');
    }
}