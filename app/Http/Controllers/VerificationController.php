<?php

namespace App\Http\Controllers;

use App\Models\Verification;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VerificationController extends Controller
{
    // Halaman Upload Berkas (Tahap 1)
    public function showUploadForm()
    {
        // 1. Cek Apakah Pendaftaran Buka
        if (!Setting::isOpen()) {
            return redirect()->route('home')->with('error', 'Pendaftaran Tutup');
        }

        // 2. [BARU] Cek Apakah Verifikasi Wajib?
        $wajibVerifikasi = Setting::getValue('verification_active', '1'); // Default 1 (Wajib)

        if ($wajibVerifikasi == '0') {
            // -- LOGIKA BYPASS (LEWATI VERIFIKASI) --
            
            // Buat Token Otomatis
            $autoToken = Str::random(60);

            // Buat Data Verifikasi Dummy (Agar tidak error saat relasi)
            Verification::create([
                'no_wa'           => '000000000000',      // Nomor dummy
                'file_perjanjian' => 'skipped_by_system', // Penanda dilewati
                'token'           => $autoToken,
                'status'          => 'approved'          // Langsung Status Lolos/Disetujui
            ]);

            // Langsung lempar ke halaman Form Biodata dengan Token tadi
            return redirect()->route('pendaftaran.form', ['token' => $autoToken]);
        }
        
        // 3. Jika Wajib Verifikasi, Tampilkan Form Upload Biasa
        $template = Setting::getValue('template_perjanjian');
        return view('pendaftaran.verify', compact('template'));
    }

    // Proses Simpan Berkas User (Jika Wajib)
    public function store(Request $request)
    {
        $request->validate([
            'no_wa' => 'required|numeric|digits_between:10,15',
            'berkas' => 'required|mimes:pdf,jpg,jpeg,png|max:2048', // Max 2MB
        ]);

        // Format No WA (628xxx)
        $wa = $request->no_wa;
        if (substr($wa, 0, 1) == '0') {
            $wa = '62' . substr($wa, 1);
        }

        // Simpan File
        $path = $request->file('berkas')->store('verifikasi_files', 'public');

        // Simpan ke Database
        Verification::create([
            'no_wa' => $wa,
            'file_perjanjian' => $path,
            'token' => Str::random(60), // Token Unik Panjang
            'status' => 'pending'
        ]);

        return redirect()->route('pendaftaran.verify.success');
    }

    public function showSuccess()
    {
        return view('pendaftaran.verify_success');
    }
}