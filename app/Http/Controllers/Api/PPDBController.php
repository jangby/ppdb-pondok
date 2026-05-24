<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Setting; // Pastikan model Setting di-import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PPDBController extends Controller
{
    public function getStat()
    {
        try {
            // 1. Menghitung total seluruh pendaftar
            $total = Candidate::count();

            // 2. Menghitung berdasarkan Jenjang (Dinamis)
            // Cara ini akan otomatis mengelompokkan dan menghitung semua jenjang yang ada
            // Hasilnya akan berupa array format: ["SMP" => 90, "SMA" => 60, "SMK" => 10]
            $jenjangStats = Candidate::select('jenjang', DB::raw('count(*) as total'))
                ->whereNotNull('jenjang')
                ->groupBy('jenjang')
                ->pluck('total', 'jenjang');

            // 3. Menghitung berdasarkan Status (Dinamis)
            // Mengelompokkan otomatis berdasarkan status pendaftaran (Menunggu, Terverifikasi, dll)
            $statusStats = Candidate::select('status', DB::raw('count(*) as total'))
                ->whereNotNull('status')
                ->groupBy('status')
                ->pluck('total', 'status');

            return response()->json([
                'success' => true,
                'message' => 'Data statistik PPDB berhasil diambil',
                'data' => [
                    'total' => $total,
                    'jenjang' => $jenjangStats,
                    'status' => $statusStats
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkStatus($nik)
    {
        try {
            // Mencari data kandidat berdasarkan NIK
            // Anda bisa mengganti 'nik' dengan 'nisn' atau 'nomor_pendaftaran' sesuai field yang ada di database Anda.
            // Kita juga memuat relasi testRoom jika ada (berdasarkan migrasi test_rooms)
            $candidate = Candidate::with('testRoom')->where('nik', $nik)->first();

            // Jika data tidak ditemukan
            if (!$candidate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data pendaftar dengan NIK tersebut tidak ditemukan.'
                ], 404);
            }

            // Jika data ditemukan, kita format responsnya
            return response()->json([
                'success' => true,
                'message' => 'Data pendaftar berhasil ditemukan.',
                'data' => [
                    // Sesuaikan nama field (seperti 'nama_lengkap') dengan struktur asli di tabel candidates
                    'nama' => $candidate->nama_lengkap ?? $candidate->name ?? 'Tidak diketahui',
                    'asal_sekolah' => $candidate->asal_sekolah ?? '-',
                    'jenjang' => $candidate->jenjang ?? '-',
                    'jalur' => $candidate->jalur ?? '-',
                    'status' => $candidate->status ?? 'Menunggu',
                    // Mengambil nama ruangan dari tabel relasi test_rooms jika sudah diset
                    'ruang_tes' => $candidate->testRoom ? $candidate->testRoom->nama_ruangan : 'Belum ditentukan',
                    // call_time dari migrasi jadwal panggilan tes
                    'jadwal_tes' => $candidate->call_time ?? 'Belum ada jadwal', 
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }
}