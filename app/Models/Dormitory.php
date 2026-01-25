<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dormitory extends Model
{
    protected $guarded = [];

    // Relasi: Satu asrama punya banyak santri
    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }

    /**
     * LOGIKA UTAMA: AUTO ASSIGN (ROUND ROBIN / BALANCING)
     * Fungsi ini akan mencari asrama yang paling sedikit penghuninya
     * berdasarkan gender santri. Ini otomatis menciptakan efek "selang-seling".
     */
    public static function getAutoAssignedDorm($genderSantri) // 'L' atau 'P'
    {
        // Mapping Gender L/P ke Putra/Putri
        $jenis = ($genderSantri == 'L') ? 'Putra' : 'Putri';

        // Ambil semua asrama aktif sesuai gender beserta jumlah penghuninya
        $dorms = self::where('jenis_asrama', $jenis)
                    ->where('is_active', true)
                    ->withCount('candidates') // Hitung jumlah santri yg sudah ada
                    ->get();

        if ($dorms->isEmpty()) return null;

        // Cari asrama dengan penghuni TERDIKIT (Load Balancing)
        // Logika ini otomatis membuat urutan A -> B -> A -> B
        $selectedDorm = $dorms->sortBy('candidates_count')->first();

        // Cek Kuota (Opsional: Jika penuh, cari yang lain atau tetap paksa masuk)
        if ($selectedDorm->candidates_count >= $selectedDorm->kapasitas) {
            // Jika mau strict kuota, bisa return null atau throw error
            // Tapi untuk sekarang kita biarkan masuk (over quota) agar santri tetap dapat kamar
        }

        return $selectedDorm->id;
    }
}