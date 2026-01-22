<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Setting extends Model
{
    use HasFactory;

    // Izinkan semua kolom diisi (id, key, value)
    protected $guarded = ['id'];

    /**
     * Helper untuk mengambil value berdasarkan key
     */
    public static function getValue($key, $default = null)
    {
        // Cache sederhana bisa ditambahkan di sini jika perlu
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Cek Status Pendaftaran (Disesuaikan dengan Format Key-Value)
     */
    public static function isOpen()
    {
        // 1. Ambil Data dari Key-Value
        $status = self::getValue('status_ppdb', 'tutup'); // Default tutup
        $tglBuka = self::getValue('tgl_buka');
        $tglTutup = self::getValue('tgl_tutup');

        // 2. Cek Saklar Manual (Buka/Tutup)
        if ($status !== 'buka') {
            return false;
        }

        // 3. Cek Rentang Tanggal (Jika diisi)
        $today = Carbon::now()->startOfDay();

        if ($tglBuka) {
            $start = Carbon::parse($tglBuka)->startOfDay();
            if ($today->lt($start)) return false; // Belum buka (Hari ini < Tanggal Buka)
        }

        if ($tglTutup) {
            $end = Carbon::parse($tglTutup)->endOfDay();
            if ($today->gt($end)) return false; // Sudah lewat (Hari ini > Tanggal Tutup)
        }

        return true;
    }
}