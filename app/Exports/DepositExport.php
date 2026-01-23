<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DepositExport implements WithMultipleSheets
{
    protected $dataSnapshot;

    public function __construct($dataSnapshot)
    {
        $this->dataSnapshot = $dataSnapshot;
    }

    public function sheets(): array
    {
        return [
            new DepositSheet($this->dataSnapshot, 'L', 'Santri Putra'),
            new DepositSheet($this->dataSnapshot, 'P', 'Santri Putri'),
        ];
    }
}