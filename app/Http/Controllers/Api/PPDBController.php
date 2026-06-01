<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PPDBController extends Controller
{
    public function getStat()
    {
        try {
            $total = Candidate::count();

            $jenjangStats = Candidate::select('jenjang', DB::raw('count(*) as total'))
                ->whereNotNull('jenjang')
                ->groupBy('jenjang')
                ->pluck('total', 'jenjang');

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
            $candidate = Candidate::with('testRoom')->where('no_daftar', $no_daftar)->first();

            if (!$candidate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data pendaftar dengan Nomor Pendaftaran tersebut tidak ditemukan.'
                ], 404);
            }

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
            $candidates = Candidate::with('address')->select('id', 'nama_lengkap', 'jenis_kelamin', 'jenjang')->get();

            $santri = [];
            $santriyah = [];
            $progresSantri = 0;
            $progresSantriyah = 0;
            $target = 50; 

            foreach ($candidates as $c) {
                $nama = $c->nama_lengkap ?? 'Tanpa Nama';
                $jenjang = $c->jenjang ?? '-';
                
                $alamatTeks = '';
                if (strtoupper($jenjang) !== 'SMA LANJUTAN' && $c->address) {
                    $alamatRaw = $c->address->alamat ?? '-';
                    $kecamatanRaw = $c->address->kecamatan ?? '-';
                    $alamatTeks = " # {$alamatRaw}, Kec. {$kecamatanRaw}";
                }

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
                        'list' => $santri,
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
    // API UNTUK MENGECEK TOKEN DARI BOT WA
    // =======================================================================
    public function checkTokenWA($token)
    {
        try {
            $verification = \App\Models\Verification::where('token', $token)->first();

            if (!$verification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak valid.'
                ], 404);
            }

            $candidate = \App\Models\Candidate::where('file_perjanjian', $verification->file_perjanjian)->first();

        if ($candidate) {
            // [PERBAIKAN]: Gunakan patokan 'tempat_lahir' seperti di form web, BUKAN menggunakan 'nik'
            if (!empty($candidate->tempat_lahir) && $candidate->tempat_lahir !== '-') {
                return response()->json([
                    'success' => false,
                    'is_completed' => true,
                    'message' => "Data pendaftaran atas nama *{$candidate->nama_lengkap}* sudah lengkap dan masuk ke sistem. Anda tidak perlu mengisi formulir via WA lagi.",
                    'data' => [
                        'no_daftar' => $candidate->no_daftar,
                        'nama' => $candidate->nama_lengkap
                    ]
                ]);
            }

            // Jika tempat lahir masih '-', berarti data masih dummy dari admin. Izinkan bot WA bertanya!
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

            return response()->json([
                'success' => true,
                'is_completed' => false,
                'is_admin_filled' => false,
                'data' => [
                    'no_daftar' => null,
                    'nama' => null,
                    'jenjang' => $verification->jenjang,
                    'jenis_kelamin' => null,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // =======================================================================
    // API UNTUK MENYIMPAN DATA DARI BOT WA
    // =======================================================================
    public function submitDaftarWA(\Illuminate\Http\Request $request)
    {
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

        DB::beginTransaction();
        try {
            $candidate = \App\Models\Candidate::where('file_perjanjian', $verification->file_perjanjian)->first();

            // 1. Simpan/Update Data Santri
            if ($candidate) {
                $candidate->update([
                    'nik' => $request->nik,
                    'nama_lengkap' => $request->nama_lengkap,
                    'jenjang' => $request->jenjang,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'tempat_lahir' => $request->tempat_lahir,
                    'tanggal_lahir' => Carbon::createFromFormat('d-m-Y', $request->tanggal_lahir)->format('Y-m-d'),
                    'nisn' => $request->nisn,
                    'no_kk' => $request->no_kk,
                    
                    // Tambahan Relasi/Opsional
                    'anak_ke' => $request->anak_ke,
                    'jumlah_saudara' => $request->jumlah_saudara,
                    'asal_sekolah' => $request->asal_sekolah,
                ]);
            } else {
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
                    'tanggal_lahir' => Carbon::createFromFormat('d-m-Y', $request->tanggal_lahir)->format('Y-m-d'),
                    'no_kk' => $request->no_kk,
                    'file_perjanjian' => $verification->file_perjanjian, 
                    
                    // Tambahan Relasi/Opsional
                    'anak_ke' => $request->anak_ke,
                    'jumlah_saudara' => $request->jumlah_saudara,
                    'asal_sekolah' => $request->asal_sekolah,
                ]);
            }

            // 2. Simpan/Update Alamat
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
                    'kode_pos' => $request->kode_pos, // Tambahan agar kode pos tidak hilang
                ]
            );

            // 3. Simpan/Update Data Orang Tua
            \App\Models\CandidateParent::updateOrCreate(
                ['candidate_id' => $candidate->id],
                [
                    'nama_ayah' => $request->nama_ayah,
                    'nik_ayah' => $request->nik_ayah, // Tambahan
                    'pekerjaan_ayah' => $request->pekerjaan_ayah,
                    'no_hp_ayah' => $request->no_hp_ayah,
                    'penghasilan_ayah' => $request->penghasilan_ayah,
                    
                    'nama_ibu' => $request->nama_ibu,
                    'nik_ibu' => $request->nik_ibu, // Tambahan
                    'pekerjaan_ibu' => $request->pekerjaan_ibu,
                    'no_hp_ibu' => $request->no_hp_ibu,
                    'penghasilan_ibu' => $request->penghasilan_ibu,
                ]
            );

            // ==========================================
            // 4. GENERATE TAGIHAN OTOMATIS (TAMBAHAN BARU)
            // ==========================================
            $biaya = \App\Models\PaymentType::where('jenjang', 'Semua')
                        ->orWhere('jenjang', $request->jenjang)
                        ->get();

            foreach ($biaya as $item) {
                \App\Models\CandidateBill::firstOrCreate(
                    [
                        'candidate_id' => $candidate->id,
                        'payment_type_id' => $item->id,
                    ],
                    [
                        'nominal_tagihan' => $item->nominal,
                        'nominal_terbayar' => 0,
                        'status' => 'Belum Lunas',
                    ]
                );
            }
            // ==========================================

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data biodata berhasil disimpan.',
                'data' => [
                    'no_daftar' => $candidate->no_daftar, 
                    'nama' => $candidate->nama_lengkap
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}