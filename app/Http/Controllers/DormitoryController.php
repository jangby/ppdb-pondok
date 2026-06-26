<?php

namespace App\Http\Controllers;

use App\Models\Dormitory;
use App\Models\Candidate;
use Illuminate\Http\Request;

class DormitoryController extends Controller
{
    public function index()
    {
        // PERBARUAN: Tambahkan with('candidates') untuk menarik daftar anggota asrama
        $dorms = Dormitory::withCount('candidates')
                    ->with(['candidates' => function($q) {
                        $q->orderBy('nama_lengkap', 'asc'); // Urutkan nama sesuai abjad
                    }])
                    ->latest()
                    ->get();
                    
        return view('admin.dormitories.index', compact('dorms'));
    }

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

    public function destroy($id)
    {
        $dorm = Dormitory::findOrFail($id);
        $dorm->delete();

        return back()->with('success', 'Asrama berhasil dihapus.');
    }

    public function autoDistribute()
    {
        // Ambil SEMUA santri yang BELUM punya kamar (Baik Pending/Lulus)
        // KECUALIKAN jenjang "SMA Lanjutan"
        $candidates = Candidate::whereNull('dormitory_id')
                        ->where('jenjang', '!=', 'SMA Lanjutan')
                        ->orderBy('created_at', 'asc')
                        ->get();

        if ($candidates->isEmpty()) {
            return back()->with('error', 'Tidak ada santri yang perlu ditempatkan (semua sudah punya kamar atau hanya tersisa jenjang SMA Lanjutan).');
        }

        $count = 0;

        foreach ($candidates as $santri) {
            $dormId = Dormitory::getAutoAssignedDorm($santri->jenis_kelamin);
            
            if ($dormId) {
                $santri->update(['dormitory_id' => $dormId]);
                $count++;
            }
        }

        if ($count > 0) {
            return back()->with('success', "Sukses! $count santri berhasil ditempatkan. (Sistem mengisi asrama satu per satu hingga penuh).");
        } else {
            return back()->with('error', 'Gagal menempatkan santri. Kemungkinan kapasitas SELURUH Asrama (Putra/Putri) sudah PENUH. Silakan tambah asrama baru atau tambah kapasitasnya.');
        }
    }
}