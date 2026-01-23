<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        // Data Default dengan format Key-Value
        $defaults = [
            'nama_sekolah' => 'Pondok Pesantren Al-Hidayah',
            'nama_gelombang' => 'Gelombang 1 Tahun 2026',
            'status_ppdb' => 'buka',
            'tgl_buka' => date('Y-m-d'),
            'tgl_tutup' => date('Y-m-d', strtotime('+1 month')),
            'whatsapp_admin' => '6281234567890',
            'pengumuman' => 'Penerimaan Santri Baru Tahun Ajaran 2026/2027 telah dibuka.',
            
            // --- [BARU] Default Fitur Verifikasi (1 = Wajib, 0 = Matikan) ---
            'verification_active' => '1', 

            'syarat_pendaftaran' => json_encode([
                ['nama' => 'Foto Copy Kartu Keluarga', 'jumlah' => 2],
                ['nama' => 'Foto Copy Akta Kelahiran', 'jumlah' => 2],
                ['nama' => 'Pas Foto 3x4 Background Merah', 'jumlah' => 4],
            ]),
        ];

        foreach ($defaults as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}