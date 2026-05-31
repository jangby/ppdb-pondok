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

    public function checkStatus($no_daftar)
    {
        try {
            // Mencari data dengan relasi testRoom (jika Anda sudah menerapkan Solusi 1 sebelumnya)
            // Jika belum menerapkan Solusi 1, hapus bagian `->with('testRoom')`
            $candidate = Candidate::with('testRoom')->where('no_daftar', $no_daftar)->first();

            if (!$candidate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data pendaftar dengan Nomor Pendaftaran tersebut tidak ditemukan.'
                ], 404);
            }

            // -------------------------------------------------------------------
            // MEMBUAT LINK OTOMATIS
            // Sesuaikan '/pendaftaran' dengan URL route halaman cek pendaftaran di web Anda.
            // url() akan otomatis mengambil domain utama website Anda (contoh: https://domainanda.com)
            // -------------------------------------------------------------------
            $linkCek = url('/cek-pendaftaran/' . $candidate->no_daftar);

            return response()->json([
                'success' => true,
                'message' => 'Data pendaftar berhasil ditemukan.',
                'data' => [
                    // --- DATA PRIBADI ---
                    'no_daftar' => $candidate->no_daftar,
                    'nama' => $candidate->nama_lengkap ?? $candidate->name ?? '-',
                    'nik' => $candidate->nik ?? '-',
                    'nisn' => $candidate->nisn ?? '-',
                    'jenis_kelamin' => $candidate->jenis_kelamin ?? '-',
                    'tempat_lahir' => $candidate->tempat_lahir ?? '-',
                    'tanggal_lahir' => $candidate->tanggal_lahir ?? '-',
                    'no_wa' => $candidate->no_wa ?? $candidate->no_hp ?? '-',
                    
                    // --- DATA AKADEMIK ---
                    'asal_sekolah' => $candidate->asal_sekolah ?? '-',
                    'jenjang' => $candidate->jenjang ?? '-',
                    'jalur' => $candidate->jalur ?? '-',
                    
                    // --- STATUS & TES ---
                    'status' => $candidate->status ?? 'Menunggu',
                    'ruang_tes' => $candidate->testRoom ? $candidate->testRoom->nama_ruangan : 'Silakan cek kartu tes',
                    'jadwal_tes' => $candidate->call_time ?? 'Belum ada jadwal',
                    
                    // --- LINK MENUJU PORTAL CEK ---
                    'link_cek' => $linkCek
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getRincian()
    {
        try {
            // 1. TAMBAHKAN 'id' dan 'with('address')' 
            // 'id' wajib dipanggil agar tabel Candidate bisa menyambung ke tabel Address
            $candidates = Candidate::with('address')->select('id', 'nama_lengkap', 'jenis_kelamin', 'jenjang')->get();

            $santri = [];
            $santriyah = [];
            $progresSantri = 0;
            $progresSantriyah = 0;
            $target = 50; 

            foreach ($candidates as $c) {
                $nama = $c->nama_lengkap ?? 'Tanpa Nama';
                $jenjang = $c->jenjang ?? '-';
                
                // 2. CEK ALAMAT: Hanya untuk yang bukan SMA Lanjutan
                $alamatTeks = '';
                if (strtoupper($jenjang) !== 'SMA LANJUTAN' && $c->address) {
                    $alamatRaw = $c->address->alamat ?? '-';
                    $kecamatanRaw = $c->address->kecamatan ?? '-';
                    // Gunakan simbol '#' sebagai pemisah antara Nama dan Alamat
                    $alamatTeks = " # {$alamatRaw}, Kec. {$kecamatanRaw}";
                }

                // Hasil teks: "Ahmad # Jl. Mawar, Kec. X (SMP)" 
                // Atau jika SMA Lanjutan: "Ciko (SMA Lanjutan)"
                $formatData = $nama . $alamatTeks . ' (' . $jenjang . ')';

                $isSantri = in_array(strtoupper($c->jenis_kelamin), ['L', 'LAKI-LAKI', 'PUTRA']);

                if ($isSantri) {
                    $santri[] = $formatData;
                    if (strtoupper($jenjang) !== 'SMA LANJUTAN') {
                        $progresSantri++;
                    }
                } else {
                    $santriyah[] = $formatData;
                    if (strtoupper($jenjang) !== 'SMA LANJUTAN') {
                        $progresSantriyah++;
                    }
                }
            }

            $persenSantri = min(round(($progresSantri / $target) * 100, 1), 100);
            $persenSantriyah = min(round(($progresSantriyah / $target) * 100, 1), 100);

            return response()->json([
                'success' => true,
                'message' => 'Rincian berhasil diambil',
                'data' => [
                    'santri' => [
                        'list' => $santri, // Berisi Object: {"SMP": ["Ahmad", "Budi"], "SMA": ["Doni"]}
                        'progres' => $progresSantri,
                        'persentase' => $persenSantri,
                        'target' => $target
                    ],
                    'santriyah' => [
                        'list' => $santriyah, 
                        'progres' => $progresSantriyah,
                        'persentase' => $persenSantriyah,
                        'target' => $target
                    ]
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