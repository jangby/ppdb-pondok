<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Models\Setting;

class CandidatesExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        $sheets = [];
        
        // 1. Ambil Daftar Jenjang dari Setting (misal: SMP, SMK, MA)
        $jenjangs = json_decode(Setting::getValue('list_jenjang'), true) ?? ['SMP', 'SMK'];

        // 2. Loop setiap jenjang, buat sheet Putra dan Putri terpisah
        foreach ($jenjangs as $jenjang) {
            // Sheet Putra
            $sheets[] = new CandidateSheet($jenjang, 'L');
            
            // Sheet Putri
            $sheets[] = new CandidateSheet($jenjang, 'P');
        }

        return $sheets;
    }
}