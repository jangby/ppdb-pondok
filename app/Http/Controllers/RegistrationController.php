<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\CandidateAddress;
use App\Models\CandidateParent;
use App\Models\CandidateBill;
use App\Models\PaymentType;
use App\Models\Setting; // Pastikan Model Setting sudah dibuat
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistrationController extends Controller
{
    // 1. Tampilkan Form Pendaftaran
    public function index()
    {
        // Cek Status Pendaftaran dari Database
        if (!Setting::isOpen()) {
            $setting = Setting::first();
            // Pastikan Anda sudah membuat view 'pendaftaran.tutup' sesuai panduan sebelumnya
            return view('pendaftaran.tutup', compact('setting'));
        }

        return view('pendaftaran.index');
    }

    // 2. Proses Simpan Data
    public function store(Request $request)
    {
        // Cek lagi saat submit (untuk keamanan)
        if (!Setting::isOpen()) {
            return redirect()->route('home')->with('error', 'Pendaftaran sudah ditutup.');
        }

        // A. Validasi Input
        $request->validate([
            // Data Pribadi
            'nama_lengkap' => 'required|string|max:255',
            'nisn' => 'required|numeric|unique:candidates,nisn',
            'nik' => 'required|numeric|unique:candidates,nik',
            'no_kk' => 'nullable|numeric',
            'tempat_lahir' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'jenjang' => 'required|in:SMP,SMK',
            
            // Alamat (Wajib Lengkap)
            'alamat' => 'required|string',
            'desa' => 'required|string',
            'kecamatan' => 'required|string',
            'kabupaten' => 'required|string', // Kolom Baru
            'provinsi' => 'required|string',   // Kolom Baru
            
            // Orang Tua
            'nama_ayah' => 'required|string',
            'nama_ibu' => 'required|string',
            'no_hp_ayah' => 'required|numeric',
        ]);

        DB::beginTransaction();

        try {
            // B. Simpan Data Inti Santri
            $candidate = Candidate::create([
                'no_daftar' => 'REG-' . date('Y') . date('His'), // Generate No Daftar Unik
                'nisn' => $request->nisn,
                'nik' => $request->nik,
                'no_kk' => $request->no_kk,
                'nama_lengkap' => $request->nama_lengkap,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'anak_ke' => $request->anak_ke ?? 1,
                'jumlah_saudara' => $request->jumlah_saudara ?? 0,
                'riwayat_penyakit' => $request->riwayat_penyakit,
                'jenjang' => $request->jenjang,
                'asal_sekolah' => $request->asal_sekolah,
                
                // Field Sistem Otomatis
                'tahun_masuk' => date('Y'),
                'jalur_pendaftaran' => 'Online',
                'status_seleksi' => 'Pending',
            ]);

            // C. Simpan Alamat (LENGKAP dengan Kabupaten & Provinsi)
            CandidateAddress::create([
                'candidate_id' => $candidate->id,
                'alamat' => $request->alamat,
                'rt' => $request->rt,
                'rw' => $request->rw,
                'desa' => $request->desa,
                'kecamatan' => $request->kecamatan,
                'kabupaten' => $request->kabupaten, // Ambil dari input
                'provinsi' => $request->provinsi,   // Ambil dari input
                'kode_pos' => $request->kode_pos,
            ]);

            // D. Simpan Orang Tua (LENGKAP dengan Penghasilan)
            CandidateParent::create([
                'candidate_id' => $candidate->id,
                'nama_ayah' => $request->nama_ayah,
                'nik_ayah' => $request->nik_ayah,
                'thn_lahir_ayah' => $request->thn_lahir_ayah,
                'pendidikan_ayah' => $request->pendidikan_ayah,
                'pekerjaan_ayah' => $request->pekerjaan_ayah,
                'penghasilan_ayah' => $request->penghasilan_ayah ?? 0, // Input atau 0
                'no_hp_ayah' => $request->no_hp_ayah,
                
                'nama_ibu' => $request->nama_ibu,
                'nik_ibu' => $request->nik_ibu,
                'thn_lahir_ibu' => $request->thn_lahir_ibu,
                'pendidikan_ibu' => $request->pendidikan_ibu,
                'pekerjaan_ibu' => $request->pekerjaan_ibu,
                'penghasilan_ibu' => $request->penghasilan_ibu ?? 0, // Input atau 0
                'no_hp_ibu' => $request->no_hp_ibu,
            ]);

            // E. Generate Tagihan Otomatis
            $biaya = PaymentType::where('jenjang', 'Semua')
                        ->orWhere('jenjang', $request->jenjang)
                        ->get();

            foreach ($biaya as $item) {
                CandidateBill::create([
                    'candidate_id' => $candidate->id,
                    'payment_type_id' => $item->id,
                    'nominal_tagihan' => $item->nominal,
                    'nominal_terbayar' => 0,
                    'status' => 'Belum Lunas',
                ]);
            }

            DB::commit(); // Simpan permanen

            // Redirect ke halaman Sukses
            return redirect()->route('pendaftaran.sukses', ['id' => $candidate->no_daftar]);

        } catch (\Exception $e) {
            DB::rollback(); // Batalkan jika error
            return back()->with('error', 'Gagal Mendaftar: ' . $e->getMessage())->withInput();
        }
    }

    // 3. Halaman Sukses
    public function sukses($no_daftar)
    {
        return view('pendaftaran.sukses', compact('no_daftar'));
    }
}