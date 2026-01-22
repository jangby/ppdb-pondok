<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\CandidateAddress;
use App\Models\CandidateParent;
use App\Models\CandidateBill;
use App\Models\PaymentType;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistrationController extends Controller
{
    public function index()
    {
        if (!Setting::isOpen()) {
            $setting = Setting::first();
            return view('pendaftaran.tutup', compact('setting'));
        }
        return view('pendaftaran.index');
    }

    public function store(Request $request)
    {
        if (!Setting::isOpen()) {
            return redirect()->route('home')->with('error', 'Pendaftaran sudah ditutup.');
        }

        // VALIDASI
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nisn' => 'required|numeric|unique:candidates,nisn',
            'nik' => 'required|numeric|unique:candidates,nik',
            'jenjang' => 'required|in:SMP,SMK',
            'asal_sekolah' => 'required|string', // Pastikan divalidasi
            
            // Orang Tua
            'nama_ayah' => 'required|string',
            'nama_ibu' => 'required|string',
            'no_hp_ayah' => 'required|numeric',
            
            // Alamat
            'alamat' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            // BERSIHKAN FORMAT RUPIAH / TITIK (PENTING!)
            // Mengubah "2.500.000" menjadi "2500000"
            $gajiAyah = preg_replace('/[^0-9]/', '', $request->penghasilan_ayah);
            $gajiIbu = preg_replace('/[^0-9]/', '', $request->penghasilan_ibu);

            // 1. Simpan Data Santri
            $candidate = Candidate::create([
                'no_daftar' => 'REG-' . date('Y') . date('His'),
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
                'asal_sekolah' => $request->asal_sekolah, // SUDAH ADA
                'tahun_masuk' => date('Y'),
                'jalur_pendaftaran' => 'Online',
                'status' => 'Baru', // Pastikan status terisi
            ]);

            // 2. Simpan Alamat
            CandidateAddress::create([
                'candidate_id' => $candidate->id,
                'alamat' => $request->alamat,
                'rt' => $request->rt,
                'rw' => $request->rw,
                'desa' => $request->desa,
                'kecamatan' => $request->kecamatan,
                'kabupaten' => $request->kabupaten,
                'provinsi' => $request->provinsi,
                'kode_pos' => $request->kode_pos,
            ]);

            // 3. Simpan Orang Tua (Pakai variabel yang sudah dibersihkan)
            CandidateParent::create([
                'candidate_id' => $candidate->id,
                'nama_ayah' => $request->nama_ayah,
                'nik_ayah' => $request->nik_ayah,
                'thn_lahir_ayah' => $request->thn_lahir_ayah,
                'pendidikan_ayah' => $request->pendidikan_ayah,
                'pekerjaan_ayah' => $request->pekerjaan_ayah,
                'penghasilan_ayah' => (int) $gajiAyah, // Convert ke Integer
                'no_hp_ayah' => $request->no_hp_ayah,
                
                'nama_ibu' => $request->nama_ibu,
                'nik_ibu' => $request->nik_ibu,
                'thn_lahir_ibu' => $request->thn_lahir_ibu,
                'pendidikan_ibu' => $request->pendidikan_ibu,
                'pekerjaan_ibu' => $request->pekerjaan_ibu,
                'penghasilan_ibu' => (int) $gajiIbu, // Convert ke Integer
                'no_hp_ibu' => $request->no_hp_ibu,
            ]);

            // 4. Generate Tagihan
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

            DB::commit();
            return redirect()->route('pendaftaran.sukses', ['no_daftar' => $candidate->no_daftar]);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function sukses($no_daftar)
    {
        return view('pendaftaran.sukses', compact('no_daftar'));
    }
}