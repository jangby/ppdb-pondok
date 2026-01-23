<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize; // Agar lebar kolom otomatis
use Maatwebsite\Excel\Concerns\WithStyles;     // Agar bisa custom font
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DepositSheet implements FromView, WithTitle, ShouldAutoSize, WithStyles
{
    protected $dataSnapshot;
    protected $gender;
    protected $sheetName;

    public function __construct($dataSnapshot, $gender, $sheetName)
    {
        $this->dataSnapshot = $dataSnapshot;
        $this->gender = $gender;
        $this->sheetName = $sheetName;
    }

    public function view(): View
    {
        // Filter data berdasarkan gender
        $candidates = $this->dataSnapshot->filter(function ($candidate) {
            return $candidate->jenis_kelamin == $this->gender;
        });

        // Ambil item pembayaran dari data pertama (untuk header)
        $paymentTypes = $this->dataSnapshot->first() ? $this->dataSnapshot->first()->payment_items : [];

        // Hitung total kolom untuk Colspan KOP (No + Nama + Item... + Total)
        $totalColumns = 3 + count($paymentTypes); 

        return view('exports.deposit', [
            'candidates' => $candidates,
            'paymentTypes' => $paymentTypes,
            'sheetName' => $this->sheetName,
            'colSpan' => $totalColumns,
            'tanggal' => date('d F Y')
        ]);
    }

    public function title(): string
    {
        return $this->sheetName;
    }

    public function styles(Worksheet $sheet)
    {
        // Styling tambahan khusus Excel (Font Family)
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]], // Nama Yayasan Besar
            2 => ['font' => ['italic' => true, 'size' => 10]], // Alamat
        ];
    }
}