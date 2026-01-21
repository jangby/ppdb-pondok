<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::create([
            'nama_gelombang' => 'Gelombang 1 Tahun 2026',
            'is_open' => true, // Default BUKA
            'tanggal_buka' => now(),
            'tanggal_tutup' => now()->addMonths(1),
        ]);
    }
}