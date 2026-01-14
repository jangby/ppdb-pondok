<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\CandidateAddress;
use App\Models\CandidateParent;
use App\Models\CandidateBill;
use App\Models\PaymentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistrationController extends Controller
{
    // 1. Tampilkan Form Pendaftaran
    public function index()
    {
        return view('pendaftaran.index');
    }

    // 2. Proses Simpan Data
    public function store(Request $request)
    {
        // Validasi Input (Bisa diperketat lagi nanti)
        $request->validate([
            'nama_lengkap' => 'required',
            'nisn' => 'required|unique:candidates,nisn',
            'nik' => 'required|unique:candidates,nik',
            'jenis_kelamin' => 'required',
            'jenjang' => 'required',
            'alamat' => 'required',
            'nama_ayah' => 'required',
            'nama_ibu' => 'required',
        ]);

        // Gunakan Database Transaction agar data aman
        // (Jika satu gagal, semua batal disimpan)
        DB::beginTransaction();

        try {
            // A. Simpan Data Inti Santri
            $candidate = Candidate::create([
                // Generate No Daftar: REG-Tahun-Detik (Unik)
                'no_daftar' => 'REG-' . date('Y') . date('His'), 
                'nisn' => $request->nisn,
                'nik' => $request->nik,
                'no_kk' => $request->no_kk,
                'nama_lengkap' => $request->nama_lengkap,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'anak_ke' => $request->anak_ke,
                'jumlah_saudara' => $request->jumlah_saudara,
                'riwayat_penyakit' => $request->riwayat_penyakit,
                'jenjang' => $request->jenjang,
                'asal_sekolah' => $request->asal_sekolah,
                
                // Field Otomatis
                'tahun_masuk' => date('Y'),
                'jalur_pendaftaran' => 'Online',
                'status_seleksi' => 'Pending',
            ]);

            // B. Simpan Alamat
            CandidateAddress::create([
                'candidate_id' => $candidate->id,
                'alamat' => $request->alamat,
                'rt' => $request->rt,
                'rw' => $request->rw,
                'desa' => $request->desa,
                'kecamatan' => $request->kecamatan,
                'kabupaten' => $request->kabupaten ?? 'Default Kab', // Sesuaikan form nanti
                'provinsi' => $request->provinsi ?? 'Default Prov',
                'kode_pos' => $request->kode_pos,
            ]);

            // C. Simpan Orang Tua
            CandidateParent::create([
                'candidate_id' => $candidate->id,
                'nama_ayah' => $request->nama_ayah,
                'nik_ayah' => $request->nik_ayah,
                'thn_lahir_ayah' => $request->thn_lahir_ayah,
                'pendidikan_ayah' => $request->pendidikan_ayah,
                'pekerjaan_ayah' => $request->pekerjaan_ayah,
                'penghasilan_ayah' => $request->penghasilan_ayah ?? 0,
                'no_hp_ayah' => $request->no_hp_ayah,
                
                'nama_ibu' => $request->nama_ibu,
                'nik_ibu' => $request->nik_ibu,
                'thn_lahir_ibu' => $request->thn_lahir_ibu,
                'pendidikan_ibu' => $request->pendidikan_ibu,
                'pekerjaan_ibu' => $request->pekerjaan_ibu,
                'penghasilan_ibu' => $request->penghasilan_ibu ?? 0,
                'no_hp_ibu' => $request->no_hp_ibu,
            ]);

            // D. GENERATE TAGIHAN OTOMATIS
            // Ambil jenis pembayaran yang sesuai jenjang (SMP/SMK) atau yang 'Semua'
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

            DB::commit(); // Simpan semua perubahan
            
            // Redirect ke halaman sukses
            return redirect()->route('pendaftaran.sukses', ['id' => $candidate->no_daftar]);

        } catch (\Exception $e) {
            DB::rollback(); // Batalkan semua jika error
            return back()->withErrors(['msg' => 'Gagal Mendaftar: ' . $e->getMessage()]);
        }
    }

    // 3. Halaman Sukses
    public function sukses($no_daftar)
    {
        return view('pendaftaran.sukses', compact('no_daftar'));
    }
}