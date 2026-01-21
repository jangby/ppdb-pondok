<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    // Izinkan kolom-kolom ini diisi
    protected $fillable = [
        'nama_gelombang',
        'tanggal_buka',
        'tanggal_tutup',
        'is_open',
        'pengumuman',
    ];

    // Casting tipe data
    protected $casts = [
        'is_open' => 'boolean',
        'tanggal_buka' => 'date',
        'tanggal_tutup' => 'date',
    ];

    // Fungsi Helper untuk Cek Status (PENTING)
    public static function isOpen()
    {
        $setting = self::first();

        // 1. Jika data belum ada atau diset manual CLOSED
        if (!$setting || !$setting->is_open) {
            return false;
        }

        // 2. Cek Tanggal Otomatis (Jika diisi)
        $today = now()->startOfDay();
        
        if ($setting->tanggal_buka && $today->lt($setting->tanggal_buka)) return false; // Belum buka
        if ($setting->tanggal_tutup && $today->gt($setting->tanggal_tutup)) return false; // Sudah lewat

        return true;
    }
}