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
        // 1. Cari data verifikasi berdasarkan token
        $verification = \App\Models\Verification::where('token', $token)->first();

        if (!$verification) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid.'
            ], 404);
        }

        // 2. Cari Candidate berdasarkan file_perjanjian 
        // (Ini adalah field yang menghubungkan tabel Verification dan Candidate di kodingan form Anda)
        $candidate = \App\Models\Candidate::where('file_perjanjian', $verification->file_perjanjian)->first();

        if ($candidate) {
            // Cek apakah data sudah lengkap (NIK sebagai indikator utama)
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

            return response()->json([
                'success' => true,
                'is_completed' => false,
                'is_admin_filled' => true, 
                'data' => [
                    'no_daftar' => $candidate->no_daftar,
                    'nama' => $candidate->nama_lengkap,
                    'jenjang' => $candidate->jenjang,
                    'jenis_kelamin' => $candidate->jenis_kelamin,
                ]
            ]);
        }

        // Jika Candidate belum dibuat, ambil data dari tabel Verification
        return response()->json([
            'success' => true,
            'is_completed' => false,
            'is_admin_filled' => false,
            'data' => [
                'no_daftar' => null,
                'nama' => null, // Nama akan diminta ke user jika admin belum buat kandidat
                'jenjang' => $verification->jenjang,
                'jenis_kelamin' => null,
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
    }
}

    public function submitDaftarWA(\Illuminate\Http\Request $request)
{
    // 1. Validasi ketat sesuai field yang Anda perlukan
    $request->validate([
        'token' => 'required|string',
        'nama_lengkap' => 'required|string',
        'jenis_kelamin' => 'required|in:L,P',
        'jenjang' => 'required|string',
        'nik' => 'required|string',
        'no_kk' => 'required|string',
        'alamat' => 'required|string',
        'desa' => 'required|string',
        'kecamatan' => 'required|string',
        'kabupaten' => 'required|string',
        'provinsi' => 'required|string',
        'no_hp_ayah' => 'required|string',
        'no_hp_ibu' => 'required|string',
    ]);

    $verification = \App\Models\Verification::where('token', $request->token)->first();
    if (!$verification) {
        return response()->json(['success' => false, 'message' => 'Token tidak valid.'], 404);
    }

    \Illuminate\Support\Facades\DB::beginTransaction();
    try {
        // Cari Candidate berdasarkan file_perjanjian (sesuai jembatan data Anda)
        $candidate = \App\Models\Candidate::where('file_perjanjian', $verification->file_perjanjian)->first();

        if ($candidate) {
            // Update data santri
            $candidate->update([
                'nik' => $request->nik,
                'nama_lengkap' => $request->nama_lengkap,
                'jenjang' => $request->jenjang,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => \Carbon\Carbon::createFromFormat('d-m-Y', $request->tanggal_lahir)->format('Y-m-d'),
                'nisn' => $request->nisn,
                'no_kk' => $request->no_kk,
            ]);
        } else {
            // Buat baru jika belum ada
            $tahun = date('Y');
            $last = \App\Models\Candidate::whereYear('created_at', $tahun)->orderBy('id', 'desc')->first();
            $urutan = $last ? intval(substr($last->no_daftar, -4)) + 1 : 1;
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
                'nisn' => $request->nisn,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => \Carbon\Carbon::createFromFormat('d-m-Y', $request->tanggal_lahir)->format('Y-m-d'),
                'no_kk' => $request->no_kk,
                'file_perjanjian' => $verification->file_perjanjian, // Penting untuk relasi
            ]);
        }

        // 2. Update/Buat Alamat (Tabel candidate_addresses)
        \App\Models\CandidateAddress::updateOrCreate(
            ['candidate_id' => $candidate->id],
            [
                'alamat' => $request->alamat,
                'rt' => $request->rt,
                'rw' => $request->rw,
                'desa' => $request->desa,
                'kecamatan' => $request->kecamatan,
                'kabupaten' => $request->kabupaten,
                'provinsi' => $request->provinsi,
            ]
        );

        // 3. Update/Buat Data Orang Tua (Tabel candidate_parents)
        \App\Models\CandidateParent::updateOrCreate(
            ['candidate_id' => $candidate->id],
            [
                'nama_ayah' => $request->nama_ayah,
                'pekerjaan_ayah' => $request->pekerjaan_ayah,
                'no_hp_ayah' => $request->no_hp_ayah,
                'penghasilan_ayah' => $request->penghasilan_ayah,
                'nama_ibu' => $request->nama_ibu,
                'pekerjaan_ibu' => $request->pekerjaan_ibu,
                'no_hp_ibu' => $request->no_hp_ibu,
                'penghasilan_ibu' => $request->penghasilan_ibu,
            ]
        );

        \Illuminate\Support\Facades\DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Data biodata berhasil disimpan.',
            'data' => ['no_daftar' => $candidate->no_daftar, 'nama' => $candidate->nama_lengkap]
        ], 200);

    } catch (\Exception $e) {
        \Illuminate\Support\Facades\DB::rollBack();
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
}