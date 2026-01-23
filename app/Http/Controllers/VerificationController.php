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
        // Cek status PPDB
        if (!Setting::isOpen()) {
            return redirect()->route('home')->with('error', 'Pendaftaran Tutup');
        }
        
        $template = Setting::getValue('template_perjanjian');
        return view('pendaftaran.verify', compact('template'));
    }

    // Proses Simpan Berkas User
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

    // TAMBAHKAN METHOD BARU INI
    public function showSuccess()
    {
        return view('pendaftaran.verify_success');
    }
}