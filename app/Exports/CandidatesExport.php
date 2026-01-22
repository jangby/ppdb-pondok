<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Exportable;

class CandidatesExport implements WithMultipleSheets
{
    use Exportable;

    public function sheets(): array
    {
        return [
            new CandidateGenderSheet('L', 'Santri Laki-laki'),
            new CandidateGenderSheet('P', 'Santri Perempuan'),
        ];
    }
}