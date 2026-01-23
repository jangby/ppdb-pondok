<?php

namespace App\Exports;

use App\Models\Candidate;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class CandidateSheet implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $jenjang;
    protected $gender;

    public function __construct($jenjang, $gender)
    {
        $this->jenjang = $jenjang;
        $this->gender = $gender;
    }

    public function query()
    {
        return Candidate::query()
            ->with(['address', 'parent'])
            ->where('jenjang', $this->jenjang)
            ->where('jenis_kelamin', $this->gender)
            ->orderBy('nama_lengkap');
    }

    public function title(): string
    {
        // Judul Sheet: "SMP - Putra" / "SMK - Putri"
        $genderLabel = $this->gender == 'L' ? 'Putra' : 'Putri';
        return "{$this->jenjang} - {$genderLabel}";
    }

    public function headings(): array
    {
        return [
            'No', 'No. Daftar', 'NISN', 'NIK', 'No. KK', 
            'Nama Lengkap', 'L/P', 'Tempat Lahir', 'Tgl Lahir', 
            'Anak Ke', 'Jml Sdr', 'Asal Sekolah', 
            'Alamat Lengkap', 'RT', 'RW', 'Desa/Kel', 'Kecamatan', 'Kab/Kota', 'Provinsi', 'Kode Pos',
            'Nama Ayah', 'NIK Ayah', 'Pekerjaan Ayah', 'Penghasilan Ayah', 'No HP Ayah',
            'Nama Ibu', 'NIK Ibu', 'Pekerjaan Ibu', 'Penghasilan Ibu', 'No HP Ibu',
            'Status Seleksi'
        ];
    }

    public function map($candidate): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $candidate->no_daftar,
            $candidate->nisn . ' ', // Tambah spasi agar Excel membacanya sebagai teks (biar angka 0 di depan tidak hilang)
            $candidate->nik . ' ',
            $candidate->no_kk . ' ',
            $candidate->nama_lengkap,
            $candidate->jenis_kelamin,
            $candidate->tempat_lahir,
            $candidate->tanggal_lahir,
            $candidate->anak_ke,
            $candidate->jumlah_saudara,
            $candidate->asal_sekolah,
            
            // Data Alamat
            $candidate->address->alamat ?? '',
            $candidate->address->rt ?? '',
            $candidate->address->rw ?? '',
            $candidate->address->desa ?? '',
            $candidate->address->kecamatan ?? '',
            $candidate->address->kabupaten ?? '',
            $candidate->address->provinsi ?? '',
            $candidate->address->kode_pos ?? '',

            // Data Orang Tua
            $candidate->parent->nama_ayah ?? '',
            $candidate->parent->nik_ayah ? $candidate->parent->nik_ayah . ' ' : '',
            $candidate->parent->pekerjaan_ayah ?? '',
            $candidate->parent->penghasilan_ayah ?? '',
            $candidate->parent->no_hp_ayah ?? '',
            
            $candidate->parent->nama_ibu ?? '',
            $candidate->parent->nik_ibu ? $candidate->parent->nik_ibu . ' ' : '',
            $candidate->parent->pekerjaan_ibu ?? '',
            $candidate->parent->penghasilan_ibu ?? '',
            $candidate->parent->no_hp_ibu ?? '',

            $candidate->status_seleksi,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Styling Header (Bold, Background Abu, Tengah)
        $sheet->getStyle('A1:AE1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => '4F81BD'], // Warna Biru Header
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Border untuk seluruh data
        $sheet->getStyle('A1:AE' . $sheet->getHighestRow())->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ]);
    }
}