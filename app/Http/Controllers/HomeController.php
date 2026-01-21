<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting; // Pastikan Model Setting di-import

class HomeController extends Controller
{
    public function index()
    {
        // 1. Ambil data setting (untuk ditampilkan di view jika perlu)
        $setting = Setting::first();

        // 2. Cek status menggunakan Helper 'isOpen' dari Model
        // Hasilnya true (buka) atau false (tutup)
        $isOpen = Setting::isOpen();

        // 3. Konversi ke string 'buka'/'tutup' agar sesuai dengan logika di View welcome Anda
        $status_ppdb = $isOpen ? 'buka' : 'tutup';

        // Kirim variabel ke view
        return view('welcome', compact('status_ppdb', 'setting'));
    }
}