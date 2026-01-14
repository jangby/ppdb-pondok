<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PaymentType;

class PaymentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'nama_pembayaran' => 'Uang Pendaftaran',
                'nominal' => 100000,
                'jenjang' => 'Semua',
            ],
            [
                'nama_pembayaran' => 'Uang Gedung (Pembangunan)',
                'nominal' => 2500000,
                'jenjang' => 'Semua',
            ],
            [
                'nama_pembayaran' => 'Seragam Lengkap',
                'nominal' => 600000,
                'jenjang' => 'SMP',
            ],
            [
                'nama_pembayaran' => 'Seragam Lengkap',
                'nominal' => 750000,
                'jenjang' => 'SMK',
            ],
            [
                'nama_pembayaran' => 'SPP Bulan Juli',
                'nominal' => 300000,
                'jenjang' => 'Semua',
            ],
        ];

        foreach ($data as $item) {
            PaymentType::create($item);
        }
    }
}