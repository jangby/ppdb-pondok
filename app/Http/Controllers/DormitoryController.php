<?php

namespace App\Http\Controllers;

use App\Models\Dormitory;
use App\Models\Candidate;
use Illuminate\Http\Request;

class DormitoryController extends Controller
{
    /**
     * Menampilkan daftar asrama beserta statistik penghuninya.
     */
    public function index()
    {
        // Ambil data asrama, urutkan terbaru, dan hitung jumlah santri di dalamnya
        $dorms = Dormitory::withCount('candidates')->latest()->get();
        
        return view('admin.dormitories.index', compact('dorms'));
    }

    /**
     * Menyimpan data asrama baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_asrama'   => 'required|string|max:255',
            'jenis_asrama'  => 'required|in:Putra,Putri',
            'kapasitas'     => 'required|integer|min:1',
            'link_group_wa' => 'nullable|url',
        ]);

        Dormitory::create([
            'nama_asrama'   => $request->nama_asrama,
            'jenis_asrama'  => $request->jenis_asrama,
            'kapasitas'     => $request->kapasitas,
            'link_group_wa' => $request->link_group_wa,
            'is_active'     => true,
        ]);

        return back()->with('success', 'Asrama baru berhasil ditambahkan.');
    }

    /**
     * Menghapus asrama.
     * Santri yang ada di dalamnya akan otomatis NULL dormitory_id-nya (sesuai migration onDelete set null).
     */
    public function destroy($id)
    {
        $dorm = Dormitory::findOrFail($id);
        $dorm->delete();

        return back()->with('success', 'Asrama berhasil dihapus.');
    }

    /**
     * FITUR SPESIAL: Distribusi Otomatis.
     * Mencari santri Lulus yang belum punya kamar, lalu membaginya secara rata (selang-seling).
     */
    public function autoDistribute()
    {
        // 1. Ambil santri LULUS yang BELUM punya kamar
        $candidates = Candidate::whereNull('dormitory_id')
                        ->where(function($q) {
                            $q->where('status_seleksi', 'LIKE', '%Lulus%')
                              ->orWhere('status_seleksi', 'LIKE', '%Diterima%')
                              ->orWhere('status_seleksi', 'LIKE', '%Approved%');
                        })
                        ->get();

        if ($candidates->isEmpty()) {
            return back()->with('error', 'Tidak ada santri lulus yang perlu ditempatkan (semua sudah punya kamar atau belum lulus).');
        }

        $count = 0;

        foreach ($candidates as $santri) {
            // Panggil fungsi Auto Assign di Model Dormitory
            $dormId = Dormitory::getAutoAssignedDorm($santri->jenis_kelamin);
            
            if ($dormId) {
                $santri->update(['dormitory_id' => $dormId]);
                $count++;
            }
        }

        if ($count > 0) {
            return back()->with('success', "Sukses! $count santri berhasil ditempatkan ke asrama secara otomatis.");
        } else {
            return back()->with('error', 'Gagal menempatkan santri. Pastikan ada Asrama Aktif yang tersedia untuk Putra/Putri.');
        }
    }
}