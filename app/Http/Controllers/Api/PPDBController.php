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

    // =======================================================================
    // 1. API UNTUK MENGECEK TOKEN DARI BOT WA
    // =======================================================================
    public function checkTokenWA($token)
    {
        try {
            // Cari data verifikasi berdasarkan token
            $verification = \App\Models\Verification::where('token', $token)->first();

            if (!$verification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak valid atau tidak ditemukan.'
                ], 404);
            }

            // Cari apakah admin sudah membuatkan data Kandidat (Santri)
            // Asumsi: Anda mengaitkan tabel menggunakan id atau no_wa
            // Sesuaikan query pencarian relasi ini jika Anda memakai foreign_key tertentu (misal: verification_id)
            $candidate = \App\Models\Candidate::whereHas('verification', function($q) use($token) {
                $q->where('token', $token);
            })->first(); 
            
            // Jika relasi di atas gagal, alternatifnya cari berdasarkan No WA:
            // $candidate = \App\Models\Candidate::whereHas('parent', function($q) use($verification) { $q->where('no_wa_ortu', $verification->no_wa); })->first();

            if ($candidate) {
                // FILTER: CEK DOBEL DATA (Jika NIK sudah diisi, berarti pendaftaran sudah selesai)
                if (!empty($candidate->nik)) {
                    return response()->json([
                        'success' => false,
                        'is_completed' => true,
                        'message' => "Data pendaftaran atas nama {$candidate->nama_lengkap} sudah lengkap.",
                        'data' => [
                            'no_daftar' => $candidate->no_daftar,
                            'nama' => $candidate->nama_lengkap
                        ]
                    ]);
                }

                // JIKA BELUM SELESAI (Skenario A: Admin sudah isi nama & nomor daftar, tapi NIK dkk masih kosong)
                return response()->json([
                    'success' => true,
                    'is_completed' => false,
                    'is_admin_filled' => true, // Menandakan bot tidak perlu menanyakan Nama & JK lagi
                    'data' => [
                        'no_daftar' => $candidate->no_daftar,
                        'nama' => $candidate->nama_lengkap,
                        'jenjang' => $candidate->jenjang,
                        'jenis_kelamin' => $candidate->jenis_kelamin,
                    ]
                ]);
            }

            // JIKA KANDIDAT BELUM DIBUAT (Skenario B: Admin belum input apa-apa)
            return response()->json([
                'success' => true,
                'is_completed' => false,
                'is_admin_filled' => false, // Bot HARUS menanyakan Nama, Jenjang & JK
                'data' => [
                    'no_daftar' => null,
                    'nama' => null,
                    'jenjang' => $verification->jenjang ?? null, // Ambil dari form verifikasi awal jika ada
                    'jenis_kelamin' => null,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    // =======================================================================
    // 2. API UNTUK MENYIMPAN DATA DARI BOT WA KETIKA "YA" (SELESAI)
    // =======================================================================
    public function submitDaftarWA(\Illuminate\Http\Request $request)
    {
        // Validasi data masuk dari bot
        $request->validate([
            'token' => 'required|string',
            'nama_lengkap' => 'required|string',
            'jenis_kelamin' => 'required|in:L,P',
            'jenjang' => 'required|string',
            'nik' => 'required|string',
            // ... tambahkan validasi untuk tempat_lahir, tgl_lahir, dll jika diperlukan
        ]);

        $verification = \App\Models\Verification::where('token', $request->token)->first();

        if (!$verification) {
            return response()->json(['success' => false, 'message' => 'Token tidak valid.']);
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // Cari data kandidat seperti di fungsi check
            $candidate = \App\Models\Candidate::whereHas('verification', function($q) use($request) {
                $q->where('token', $request->token);
            })->first();

            if ($candidate) {
                // SKENARIO A: DATA SUDAH ADA, TINGGAL UPDATE (MELENGKAPI)
                $candidate->nik = $request->nik;
                // Jika di bot memperbolehkan edit nama/jenjang, update juga:
                if (empty($candidate->nama_lengkap)) $candidate->nama_lengkap = $request->nama_lengkap;
                if (empty($candidate->jenjang)) $candidate->jenjang = $request->jenjang;
                if (empty($candidate->jenis_kelamin)) $candidate->jenis_kelamin = $request->jenis_kelamin;
                
                // $candidate->tempat_lahir = $request->tempat_lahir; // (Lengkapi sesuai kebutuhan bot)
                
                $candidate->save();
            } else {
                // SKENARIO B: DATA BELUM ADA, BUAT BARU + AUTO GENERATE NOMOR PENDAFTARAN
                $tahun = date('Y');
                // Cari nomor pendaftaran terakhir di tahun ini
                $lastCandidate = \App\Models\Candidate::whereYear('created_at', $tahun)->orderBy('id', 'desc')->first();
                
                // Logika: Ambil 3/4 digit terakhir lalu tambah 1. Contoh: REG-2026-0001
                $urutan = $lastCandidate ? intval(substr($lastCandidate->no_daftar, -4)) + 1 : 1;
                $noDaftar = 'SPMB-' . date('y') . '-' . str_pad($urutan, 4, '0', STR_PAD_LEFT);

                $candidate = \App\Models\Candidate::create([
                    'no_daftar' => $noDaftar,
                    'tahun_masuk' => $tahun,
                    'jalur_pendaftaran' => 'Online',
                    'status_seleksi' => 'Pending',
                    'status' => 'Baru', 
                    'nama_lengkap' => $request->nama_lengkap,
                    'jenjang' => $request->jenjang,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'nik' => $request->nik,
                    // 'tempat_lahir' => $request->tempat_lahir,
                    // ... (masukkan field sisanya)
                ]);

                // [PENTING] Hubungkan/Kaitkan relasi Verification ini ke Candidate yang baru dibuat
                // Sesuaikan kolom ini dengan struktur database Anda yang sebenarnya (jika ada foreign_key)
                // misal: $verification->update(['candidate_id' => $candidate->id]);
            }

            \Illuminate\Support\Facades\DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data biodata berhasil disimpan secara permanen.',
                'data' => [
                    'no_daftar' => $candidate->no_daftar,
                    'nama' => $candidate->nama_lengkap
                ]
            ], 200);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data ke database: ' . $e->getMessage()
            ], 500);
        }
    }
}