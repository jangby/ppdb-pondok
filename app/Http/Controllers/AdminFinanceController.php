<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Model
use App\Models\Expense; 
use App\Models\Candidate;
use App\Models\CandidateBill;
use App\Models\PaymentType;
use App\Models\Transaction; // Penting untuk Laporan PDF

// Library
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DepositExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; 
use Barryvdh\DomPDF\Facade\Pdf; // Penting untuk Laporan PDF
use Carbon\Carbon;

class AdminFinanceController extends Controller
{
    /**
     * Menampilkan Halaman Dashboard Keuangan
     */
    public function index()
    {
        // Saya gunakan paginate(10) agar cocok dengan view yang ada tombol halamannya (links)
        // Jika Anda ingin semua data tampil tanpa halaman, ganti ->paginate(10) menjadi ->get()
        $expenses = Expense::with('user')->latest()->paginate(10); 
        
        $paymentTypes = PaymentType::all(); 

        return view('admin.finance.index', compact('expenses', 'paymentTypes'));
    }

    /**
     * Menyimpan Pengeluaran Manual
     */
    public function store(Request $request)
    {
        $request->validate([
            'keterangan' => 'required|string',
            'nominal'    => 'required|numeric',
            'tanggal'    => 'required|date',
            'source_id'  => 'nullable|exists:payment_types,id',
        ]);

        $judulFix = $request->keterangan;
        
        if ($request->source_id) {
            $sumber = PaymentType::find($request->source_id);
            if ($sumber) {
                $judulFix = $judulFix . " (Sumber: " . $sumber->nama_pembayaran . ")";
            }
        }

        Expense::create([
            'user_id'           => auth()->id(),
            'judul_pengeluaran' => $judulFix,
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
     * ==========================================
     * FITUR 1: DOWNLOAD EXCEL (CUT-OFF SETORAN)
     * ==========================================
     * Ini adalah kode asli Anda, saya pastikan tetap ada.
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
        
        // 2. Siapkan Data
        $candidatesToExport = collect();

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

        // 3. Database Transaction
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
                'user_id'           => Auth::id(),
                'judul_pengeluaran' => "Setoran Keuangan (Auto): $itemNames", 
                'total_keluar'      => $totalSetor,
                'tanggal'           => now(),
            ]);
        });

        // 4. Download Excel
        $timestamp = date('d-m-Y_H-i');
        return Excel::download(new DepositExport($candidatesToExport), "Rekap_Setoran_{$timestamp}.xlsx");
    }

    /**
     * ==========================================
     * FITUR 2: CETAK LAPORAN PDF (BARU)
     * ==========================================
     * Kode ini sudah diperbaiki (tidak ada error metode_pembayaran/transaction)
     */
    public function printReport(Request $request)
    {
        // 1. Ambil Filter Tanggal (Opsional, default bulan ini)
        $startDate = $request->start_date ?? date('Y-m-01');
        $endDate   = $request->end_date ?? date('Y-m-d');

        // 2. Query Data Gabungan (Transaksi Masuk & Pengeluaran)
        
        // A. Pemasukan (Dari tabel transactions)
        // PERBAIKAN: Menggunakan 'tanggal_bayar' dan hardcode 'Tunai' karena kolom metode_pembayaran tidak ada
        $transaksi = Transaction::select(
                'id',
                'tanggal_bayar as tanggal', 
                DB::raw("'Pemasukan' as jenis"),
                DB::raw("CONCAT('Terima dari ', (SELECT nama_lengkap FROM candidates WHERE candidates.id = transactions.candidate_id)) as keterangan"),
                'total_bayar as nominal',
                DB::raw("'Tunai' as via") 
            )
            ->whereDate('tanggal_bayar', '>=', $startDate)
            ->whereDate('tanggal_bayar', '<=', $endDate);

        // B. Pengeluaran (Dari tabel expenses)
        $pengeluaran = Expense::select(
                'id',
                'tanggal',
                DB::raw("'Pengeluaran' as jenis"),
                'judul_pengeluaran as keterangan',
                'total_keluar as nominal',
                DB::raw("'Cash' as via")
            )
            ->whereDate('tanggal', '>=', $startDate)
            ->whereDate('tanggal', '<=', $endDate);

        // C. Gabungkan keduanya
        $riwayat = $transaksi->union($pengeluaran)
                    ->orderBy('tanggal', 'asc') // Urutkan dari terlama
                    ->get();

        // 3. Hitung Total Saldo di Periode Ini
        $totalMasuk = $riwayat->where('jenis', 'Pemasukan')->sum('nominal');
        $totalKeluar = $riwayat->where('jenis', 'Pengeluaran')->sum('nominal');
        $saldoAkhir = $totalMasuk - $totalKeluar;

        // 4. Ambil Setting Sekolah untuk Kop Surat
        $sekolah = [
            'nama'   => \App\Models\Setting::where('key', 'nama_sekolah')->value('value') ?? 'Pondok Pesantren',
            'alamat' => \App\Models\Setting::where('key', 'alamat_sekolah')->value('value') ?? 'Alamat Belum Diisi',
        ];

        // 5. Generate PDF
        $pdf = Pdf::loadView('admin.finance.print_report', compact(
            'riwayat', 'startDate', 'endDate', 'totalMasuk', 'totalKeluar', 'saldoAkhir', 'sekolah'
        ));

        // Set ukuran kertas A4 Portrait
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('Laporan-Keuangan-' . date('Ymd-His') . '.pdf');
    }

    public function reconstructBill($id)
    {
        $bill = CandidateBill::findOrFail($id);
        $masterPayment = $bill->payment_type;

        // Update nominal mengikuti master terbaru
        $bill->nominal_tagihan = $masterPayment->nominal;

        // Logika Status & Uang Kembalian (Refund)
        if ($bill->nominal_terbayar >= $bill->nominal_tagihan) {
            $bill->status = 'Lunas';
            
            $kembalian = $bill->nominal_terbayar - $bill->nominal_tagihan;
            if ($kembalian > 0) {
                // Di sini Anda bisa menambahkan logika membuat record "Pengeluaran/Refund"
                // Atau sekadar menyimpannya dengan status Lunas + Lebih Bayar
                session()->flash('warning', 'Rekonstruksi berhasil. Terdapat LEBIH BAYAR sebesar Rp ' . number_format($kembalian, 0, ',', '.') . '. Harap kembalikan uang ke santri/wali.');
            } else {
                session()->flash('success', 'Rekonstruksi berhasil. Status tetap Lunas pas.');
            }

        } else {
            // Jika nominal terbayar kurang dari tagihan baru
            $bill->status = ($bill->nominal_terbayar > 0) ? 'Cicilan' : 'Belum Lunas';
            session()->flash('info', 'Rekonstruksi berhasil. Status berubah menjadi Belum Lunas/Cicilan. Sisa tagihan menyesuaikan.');
        }

        $bill->save();

        return redirect()->back();
    }

    /**
     * Mereset anomali tagihan menjadi Belum Bayar (Rp 0)
     */
    public function fixAnomaly($id)
    {
        $bill = CandidateBill::findOrFail($id);
        
        // Kembalikan nominal terbayar menjadi 0 dan status menjadi Belum Lunas
        $bill->nominal_terbayar = 0;
        $bill->status = 'Belum Lunas';
        $bill->save();
        
        return back()->with('success', 'Tagihan berhasil di-reset menjadi Belum Bayar (Rp 0). Silakan input ulang nominal pembayarannya lewat Kasir agar tercatat di Riwayat Transaksi.');
    }
}