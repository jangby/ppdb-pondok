<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        // Ambil semua setting dan jadikan key-value array agar mudah dipanggil
        // Contoh: $settings['nama_sekolah']
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        
        // Ambil data persyaratan dan decode dari JSON ke Array
        $syaratJson = $settings['syarat_pendaftaran'] ?? '[]';
        $requirements = json_decode($syaratJson, true);
        if (!is_array($requirements)) {
            $requirements = [];
        }

        return view('admin.settings.index', compact('settings', 'requirements'));
    }

    public function update(Request $request)
    {
        // 1. Simpan Pengaturan Umum (Nama, Tanggal, dll)
        // Kita loop semua input kecuali yang khusus persyaratan
        $generalSettings = $request->except(['_token', '_method', 'syarat_nama', 'syarat_jumlah']);
        
        foreach ($generalSettings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // 2. Simpan Daftar Persyaratan (Convert Array to JSON)
        // Kita gabungkan input array nama dan jumlah menjadi satu format JSON
        $names = $request->input('syarat_nama', []);
        $qtys = $request->input('syarat_jumlah', []);
        
        $requirementData = [];
        foreach ($names as $index => $name) {
            if (!empty($name)) {
                $requirementData[] = [
                    'nama' => $name,
                    'jumlah' => $qtys[$index] ?? 1
                ];
            }
        }

        Setting::updateOrCreate(
            ['key' => 'syarat_pendaftaran'],
            ['value' => json_encode($requirementData)]
        );

        return back()->with('success', 'Pengaturan PPDB dan Persyaratan berhasil diperbarui.');
    }
}