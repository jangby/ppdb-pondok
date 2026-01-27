<?php

namespace App\Http\Controllers;

use App\Models\TestRoom;
use App\Models\Candidate;
use Illuminate\Http\Request;

class TestRoomController extends Controller
{
    /**
     * 1. Menampilkan Daftar Ruangan
     */
    public function index()
    {
        // Ambil semua ruangan beserta hitungan jumlah pesertanya
        // Kita menggunakan withCount pada relasi spesifik (santri/wali)
        $rooms = TestRoom::withCount(['candidates_santri', 'candidates_wali'])
                         ->orderBy('jenis', 'asc') // Urutkan biar rapi (Santri dulu atau Wali dulu)
                         ->orderBy('nama_ruangan', 'asc')
                         ->get();

        return view('admin.test_rooms.index', compact('rooms'));
    }

    /**
     * 2. Simpan Ruangan Baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_ruangan' => 'required|string|max:255',
            'jenis'        => 'required|in:Santri,Wali', // Wajib pilih jenis
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
        // Data santri yang ada di ruangan ini otomatis jadi NULL (karena onDelete set null di migrasi)
        TestRoom::destroy($id);
        return back()->with('success', 'Ruangan berhasil dihapus.');
    }

    /**
     * 4. Distribusi Otomatis (Pemerataan Peserta)
     * Mencari ruangan yang paling kosong untuk setiap santri/wali
     */
    public function autoDistribute()
    {
        // Ambil kandidat yang Lulus tapi belum punya ruangan (salah satu atau keduanya kosong)
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
            
            // A. LOGIKA RUANGAN SANTRI
            if (!$candidate->santri_room_id) {
                // Cari ruangan jenis 'Santri' yang isinya paling sedikit
                $roomSantri = TestRoom::where('jenis', 'Santri')
                                ->withCount('candidates_santri')
                                ->orderBy('candidates_santri_count', 'asc') // Sort dari yang terkecil
                                ->first();
                
                if ($roomSantri) {
                    $candidate->update(['santri_room_id' => $roomSantri->id]);
                }
            }

            // B. LOGIKA RUANGAN WALI
            if (!$candidate->wali_room_id) {
                // Cari ruangan jenis 'Wali' yang isinya paling sedikit
                $roomWali = TestRoom::where('jenis', 'Wali')
                                ->withCount('candidates_wali')
                                ->orderBy('candidates_wali_count', 'asc') // Sort dari yang terkecil
                                ->first();
                
                if ($roomWali) {
                    $candidate->update(['wali_room_id' => $roomWali->id]);
                }
            }
        }

        return back()->with('success', "Distribusi otomatis selesai!");
    }

    /**
     * 5. Cetak Daftar Peserta Per Ruangan (Tempelan Pintu)
     */
    public function print($id)
    {
        $room = TestRoom::findOrFail($id);

        // Ambil peserta yang benar sesuai jenis ruangannya
        if ($room->jenis == 'Santri') {
            // Jika ini ruangan santri, ambil dari relasi candidates_santri
            $participants = $room->candidates_santri()
                                 ->where('status_seleksi', '!=', 'Ditolak')
                                 ->orderBy('nama_lengkap')
                                 ->get();
        } else {
            // Jika ini ruangan wali, ambil dari relasi candidates_wali
            $participants = $room->candidates_wali()
                                 ->where('status_seleksi', '!=', 'Ditolak')
                                 ->orderBy('nama_lengkap')
                                 ->get();
        }

        return view('admin.test_rooms.print', compact('room', 'participants'));
    }
}