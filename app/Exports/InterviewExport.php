<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Models\Candidate;

class InterviewExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        $sheets = [];

        // 1. Ambil Semua Jenjang yang tersedia (SMP, SMA, SMK, dll)
        $jenjangs = Candidate::select('jenjang')->distinct()->pluck('jenjang');

        // 2. Loop setiap jenjang
        foreach ($jenjangs as $jenjang) {
            
            // Cek apakah ada Santri Laki-laki di jenjang ini?
            $hasPutra = Candidate::where('jenjang', $jenjang)->where('jenis_kelamin', 'L')->exists();
            if ($hasPutra) {
                // Buat Sheet Putra
                $sheets[] = new InterviewPerSheet($jenjang, 'L');
            }

            // Cek apakah ada Santri Perempuan di jenjang ini?
            $hasPutri = Candidate::where('jenjang', $jenjang)->where('jenis_kelamin', 'P')->exists();
            if ($hasPutri) {
                // Buat Sheet Putri
                $sheets[] = new InterviewPerSheet($jenjang, 'P');
            }
        }

        return $sheets;
    }
}