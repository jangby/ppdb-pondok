<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        // Ambil data setting pertama, jika tidak ada buat objek baru (kosong)
        $setting = Setting::first() ?? new Setting();
        
        return view('admin.settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_gelombang' => 'required|string',
            'tanggal_buka'   => 'nullable|date',
            'tanggal_tutup'  => 'nullable|date',
        ]);

        // Cek apakah data sudah ada
        $setting = Setting::first();

        $data = [
            'nama_gelombang' => $request->nama_gelombang,
            'tanggal_buka'   => $request->tanggal_buka,
            'tanggal_tutup'  => $request->tanggal_tutup,
            'pengumuman'     => $request->pengumuman,
            // Checkbox HTML tidak mengirim value jika tidak dicentang, jadi kita pakai trik ini:
            'is_open'        => $request->has('is_open') ? 1 : 0,
        ];

        if ($setting) {
            $setting->update($data);
        } else {
            Setting::create($data);
        }

        return back()->with('success', 'Pengaturan PPDB berhasil disimpan.');
    }
}