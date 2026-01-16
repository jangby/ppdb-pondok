<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting; // Panggil model setting

class HomeController extends Controller
{
    public function index()
    {
        // Ambil status dari database, default 'tutup' jika belum disetting
        $setting = Setting::where('key', 'ppdb_status')->first();
        $status_ppdb = $setting ? $setting->value : 'tutup';

        return view('welcome', compact('status_ppdb'));
    }
}