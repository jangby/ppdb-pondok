<?php

namespace App\Exports;

use App\Models\Candidate;
use App\Models\Setting; // Import Model Setting
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CandidateGenderSheet implements FromView, WithTitle, ShouldAutoSize, WithStyles
{
    private $gender;
    private $title;

    public function __construct($gender, $title)
    {
        $this->gender = $gender;
        $this->title = $title;
    }

    public function view(): View
    {
        // Ambil Nama Sekolah dari Setting untuk Kop Surat
        $namaSekolah = Setting::getValue('nama_sekolah', 'Pondok Pesantren');
        
        return view('admin.candidates.excel', [
            'candidates' => Candidate::with(['address', 'parent']) // Load relasi alamat & ortu
                            ->where('jenis_kelamin', $this->gender)
                            ->orderBy('nama_lengkap', 'asc')
                            ->get(),
            'gender_label' => $this->gender == 'L' ? 'LAKI-LAKI' : 'PEREMPUAN',
            'school_name' => $namaSekolah,
            'date' => date('d-m-Y'),
        ]);
    }

    public function title(): string
    {
        return $this->title;
    }

    // Styling Tambahan (Opsional, agar font default rapi)
    public function styles(Worksheet $sheet)
    {
        return [
            // Style default untuk seluruh sheet
            1 => ['font' => ['bold' => true, 'size' => 14]], // Baris 1 (Nama Sekolah) Bold Besar
            2 => ['font' => ['bold' => true, 'size' => 12]], // Baris 2 (Judul) Bold
        ];
    }
}