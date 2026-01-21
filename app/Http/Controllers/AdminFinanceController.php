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
            // FIX: Menggunakan kolom 'nominal' sesuai migrasi expense_fund_sources
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
            'deskripsi' => 'required|string|max:255', // Di form namanya deskripsi
            'tanggal' => 'required|date',
            'nominal' => 'required|numeric|min:1',
            'payment_type_id' => 'required|exists:payment_types,id',
        ]);

        DB::beginTransaction();
        try {
            // 1. Simpan ke tabel 'expenses'
            // FIX: Kolom DB adalah 'judul_pengeluaran' dan 'total_keluar'
            $expense = Expense::create([
                'judul_pengeluaran' => $request->deskripsi, 
                'tanggal' => $request->tanggal,
                'total_keluar' => $request->nominal, 
                'user_id' => Auth::id(),
                // 'deskripsi' => null, // Opsional biarkan null atau isi jika perlu
            ]);

            // 2. Simpan ke tabel 'expense_fund_sources'
            // FIX: Kolom DB adalah 'nominal'
            ExpenseFundSource::create([
                'expense_id' => $expense->id,
                'payment_type_id' => $request->payment_type_id,
                'nominal' => $request->nominal, 
            ]);

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
        $expense->delete(); // Otomatis cascade delete fund sources sesuai migrasi
        return back()->with('success', 'Pengeluaran dibatalkan.');
    }
}