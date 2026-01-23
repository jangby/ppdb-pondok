<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\Setting; // [PENTING] Import Model Setting

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
        // 1. Ambil Pengaturan Sekolah (Nama & Alamat)
        $settings = Setting::all()->pluck('value', 'key');
        $namaSekolah = $settings['nama_sekolah'] ?? 'YAYASAN PONDOK PESANTREN';
        $alamatSekolah = $settings['alamat_sekolah'] ?? 'Alamat Belum Diatur';

        // 2. Filter data berdasarkan gender
        $candidates = $this->dataSnapshot->filter(function ($candidate) {
            return $candidate->jenis_kelamin == $this->gender;
        });

        // 3. Ambil item pembayaran dari data pertama (untuk header tabel)
        $paymentTypes = $this->dataSnapshot->first() ? $this->dataSnapshot->first()->payment_items : [];

        // 4. Hitung total kolom untuk Colspan KOP (No + NoReg + Nama + Item... + Total)
        // Default minimal 5 kolom agar kop tidak sempit
        $totalColumns = max(4 + count($paymentTypes), 5); 

        return view('exports.deposit', [
            'candidates' => $candidates,
            'paymentTypes' => $paymentTypes,
            'sheetName' => $this->sheetName,
            'colSpan' => $totalColumns,
            'tanggal' => date('d F Y'),
            
            // Kirim Data Sekolah ke View
            'namaSekolah' => $namaSekolah,
            'alamatSekolah' => $alamatSekolah
        ]);
    }

    public function title(): string
    {
        return $this->sheetName;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]], // Baris 1: Nama Sekolah
            2 => ['font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '555555']]], // Baris 2: Alamat
            3 => ['font' => ['bold' => true, 'underline' => true, 'size' => 12]], // Baris 3: Judul Laporan
        ];
    }
}