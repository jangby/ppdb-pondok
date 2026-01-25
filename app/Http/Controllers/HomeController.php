<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\PaymentType;
use App\Models\Candidate;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Ambil Pengaturan (Status, Nama Sekolah, Syarat)
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        // Decode syarat dari JSON ke Array
        $syaratRaw = $settings['syarat_pendaftaran'] ?? '[]';
        $syarat = json_decode($syaratRaw, true);
        if (!is_array($syarat)) {
            $syarat = [];
        }

        // 2. Ambil Data Biaya & Kelompokkan per Jenjang
        $allPayments = PaymentType::all();
        
        // Ambil biaya umum (yang jenjangnya 'Semua')
        $biayaUmum = $allPayments->where('jenjang', 'Semua');
        $totalBiayaUmum = $biayaUmum->sum('nominal');

        // Siapkan array jenjang yang ada di sistem (selain 'Semua')
        // Kita ambil unique value dari kolom jenjang, filter 'Semua'
        $jenjangTersedia = $allPayments->pluck('jenjang')->unique()->filter(fn($j) => $j !== 'Semua')->values();

        // Struktur data untuk view
        $rincianBiaya = [];
        foreach ($jenjangTersedia as $jenjang) {
            $biayaKhusus = $allPayments->where('jenjang', $jenjang);
            $totalKhusus = $biayaKhusus->sum('nominal');

            $rincianBiaya[$jenjang] = [
                'items' => $biayaKhusus->merge($biayaUmum), // Gabungkan biaya khusus + umum
                'total' => $totalKhusus + $totalBiayaUmum
            ];
        }

        return view('welcome', compact('settings', 'syarat', 'rincianBiaya', 'biayaUmum'));
    }

    public function kartuTes($no_daftar)
{
    // Cari santri berdasarkan no_daftar
    $candidate = Candidate::where('no_daftar', $no_daftar)->firstOrFail();

    // Tampilkan view kartu
    return view('public.kartu_tes', compact('candidate'));
}
}