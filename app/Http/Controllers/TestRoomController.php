<?php

namespace App\Http\Controllers;

use App\Models\TestRoom;
use App\Models\Candidate;
use Illuminate\Http\Request;
use App\Models\Setting;

class TestRoomController extends Controller
{
    /**
     * 1. Menampilkan Daftar Ruangan
     */
    public function index()
    {
        $rooms = TestRoom::withCount(['candidates_santri', 'candidates_wali'])
                         ->orderBy('jenis', 'asc') 
                         ->orderBy('nama_ruangan', 'asc')
                         ->get();

        return view('admin.test_rooms.index', compact('rooms'));
    }

    /**
     * 2. Simpan Ruangan Baru
     */
    public function store(Request $request)
    {
        // VALIDASI BARU: Hanya menerima Santri atau Wali
        $request->validate([
            'nama_ruangan' => 'required|string|max:255',
            'jenis'        => 'required|in:Santri,Wali', 
            'lokasi'       => 'nullable|string',
            'kapasitas'    => 'required|integer|min:1'
        ]);

        TestRoom::create($request->all());

        return back()->with('success', 'Ruangan tes berhasil ditambahkan.');
    }

    /**
     * 3. Hapus Ruangan
     */
    public function destroy($id)
    {
        TestRoom::destroy($id);
        return back()->with('success', 'Ruangan berhasil dihapus.');
    }

    /**
     * 4. Distribusi Otomatis
     */
    public function autoDistribute()
    {
        // 1. Ambil kandidat yang statusnya BUKAN Ditolak (Berarti masuk Pending & Lulus)
        // 2. KECUALIKAN jenjang SMA Lanjutan
        $candidates = Candidate::where('status_seleksi', '!=', 'Ditolak')
                               ->where('jenjang', '!=', 'SMA Lanjutan')
                               ->where(function($q) {
                                   $q->whereNull('santri_room_id')
                                     ->orWhereNull('wali_room_id');
                               })
                               ->get();

        if ($candidates->isEmpty()) {
            return back()->with('warning', 'Semua calon santri yang valid sudah mendapatkan ruangan atau tidak ada data (SMA Lanjutan diabaikan).');
        }

        foreach ($candidates as $candidate) {
            
            // A. LOGIKA RUANGAN SANTRI (CAMPUR PUTRA & PUTRI)
            if (!$candidate->santri_room_id) {
                // Cari ruangan 'Santri' yang masih kosong/paling sedikit
                $roomSantri = TestRoom::where('jenis', 'Santri')
                                ->withCount('candidates_santri')
                                ->orderBy('candidates_santri_count', 'asc')
                                ->first();
                
                if ($roomSantri && $roomSantri->candidates_santri_count < $roomSantri->kapasitas) {
                    $candidate->update(['santri_room_id' => $roomSantri->id]);
                }
            }

            // B. LOGIKA RUANGAN WALI
            if (!$candidate->wali_room_id) {
                $roomWali = TestRoom::where('jenis', 'Wali')
                                ->withCount('candidates_wali')
                                ->orderBy('candidates_wali_count', 'asc')
                                ->first();
                
                if ($roomWali && $roomWali->candidates_wali_count < $roomWali->kapasitas) {
                    $candidate->update(['wali_room_id' => $roomWali->id]);
                }
            }
        }

        return back()->with('success', "Distribusi otomatis selesai! Santri (Putra & Putri) dan Wali telah dibagikan ke ruangannya masing-masing.");
    }

    /**
     * 5. Cetak Daftar Peserta
     */
    public function print($id)
    {
        $room = TestRoom::findOrFail($id);

        if ($room->jenis == 'Santri') {
            $participants = $room->candidates_santri()
                                 ->where('status_seleksi', '!=', 'Ditolak')
                                 ->orderBy('nama_lengkap')
                                 ->get();
        } else {
            $participants = $room->candidates_wali()
                                 ->where('status_seleksi', '!=', 'Ditolak')
                                 ->orderBy('nama_lengkap')
                                 ->get();
        }

        return view('admin.test_rooms.print', compact('room', 'participants'));
    }
}