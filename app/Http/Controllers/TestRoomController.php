<?php

namespace App\Http\Controllers;

use App\Models\TestRoom;
use App\Models\Candidate;
use Illuminate\Http\Request;
use App\Models\Setting; // <--- Tambahkan baris ini

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
        // UPDATE: Validasi jenis sekarang menerima 3 opsi
        $request->validate([
            'nama_ruangan' => 'required|string|max:255',
            'jenis'        => 'required|in:Santri Putra,Santri Putri,Wali', 
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
     * 4. Distribusi Otomatis (UPDATE UTAMA DISINI)
     */
    public function autoDistribute()
    {
        // Ambil kandidat yang belum punya ruangan
        $candidates = Candidate::where('status_seleksi', '!=', 'Ditolak')
                               ->where(function($q) {
                                   $q->whereNull('santri_room_id')
                                     ->orWhereNull('wali_room_id');
                               })
                               ->get();

        if ($candidates->isEmpty()) {
            return back()->with('warning', 'Semua calon santri sudah mendapatkan ruangan.');
        }

        foreach ($candidates as $candidate) {
            
            // A. LOGIKA RUANGAN SANTRI (DIPISAH GENDER)
            if (!$candidate->santri_room_id) {
                
                // Tentukan jenis ruangan berdasarkan jenis kelamin kandidat
                // ASUMSI: Kolom jenis kelamin di tabel candidates bernama 'jenis_kelamin'
                // dan isinya 'L' (Laki-laki) atau 'P' (Perempuan)
                
                $targetJenis = null;
                if ($candidate->jenis_kelamin == 'L') {
                    $targetJenis = 'Santri Putra';
                } elseif ($candidate->jenis_kelamin == 'P') {
                    $targetJenis = 'Santri Putri';
                }

                if ($targetJenis) {
                    // Cari ruangan sesuai gender yang isinya paling sedikit
                    $roomSantri = TestRoom::where('jenis', $targetJenis)
                                    ->withCount('candidates_santri')
                                    ->orderBy('candidates_santri_count', 'asc')
                                    ->first();
                    
                    // Masukkan santri ke ruangan tersebut
                    if ($roomSantri) {
                        // Cek apakah kapasitas masih cukup (opsional, tapi bagus)
                        if ($roomSantri->candidates_santri_count < $roomSantri->kapasitas) {
                            $candidate->update(['santri_room_id' => $roomSantri->id]);
                        }
                    }
                }
            }

            // B. LOGIKA RUANGAN WALI (TETAP SAMA/CAMPUR)
            if (!$candidate->wali_room_id) {
                $roomWali = TestRoom::where('jenis', 'Wali')
                                ->withCount('candidates_wali')
                                ->orderBy('candidates_wali_count', 'asc')
                                ->first();
                
                if ($roomWali) {
                     // Cek kapasitas wali juga
                    if ($roomWali->candidates_wali_count < $roomWali->kapasitas) {
                        $candidate->update(['wali_room_id' => $roomWali->id]);
                    }
                }
            }
        }

        return back()->with('success', "Distribusi otomatis selesai! Santri putra & putri telah dipisahkan.");
    }

    /**
     * 5. Cetak Daftar Peserta
     */
    public function print($id)
    {
        $room = TestRoom::findOrFail($id);

        // UPDATE: Cek string jenisnya mengandung kata 'Santri'
        if (str_contains($room->jenis, 'Santri')) {
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