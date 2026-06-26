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
     * LOGIKA BARU: SEQUENTIAL FILLING (Isi Penuh Satu per Satu)
     * Fungsi ini akan mengisi asrama yang paling dulu dibuat sampai batas kapasitasnya.
     * Jika sudah penuh, baru bergeser ke asrama selanjutnya.
     */
    public static function getAutoAssignedDorm($genderSantri) // 'L' atau 'P'
    {
        // Mapping Gender L/P ke Putra/Putri
        $jenis = ($genderSantri == 'L') ? 'Putra' : 'Putri';

        // Ambil semua asrama aktif sesuai gender
        // Urutkan dari yang paling pertama dibuat (created_at asc / id asc)
        $dorms = self::where('jenis_asrama', $jenis)
                    ->where('is_active', true)
                    ->orderBy('id', 'asc') 
                    ->withCount('candidates') // Hitung jumlah santri yg sudah ada
                    ->get();

        if ($dorms->isEmpty()) return null;

        // Cari asrama PERTAMA yang jumlah penghuninya MASIH DI BAWAH batas kapasitas
        $selectedDorm = $dorms->first(function ($dorm) {
            return $dorm->candidates_count < $dorm->kapasitas;
        });

        // Jika ditemukan asrama yang belum penuh, kembalikan ID-nya
        if ($selectedDorm) {
            return $selectedDorm->id;
        }

        // Jika kode sampai di sini, berarti SEMUA ASRAMA SUDAH PENUH.
        // Return null agar santri sisanya tidak dipaksakan masuk.
        return null;
    }
}