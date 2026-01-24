<?php

namespace App\Exports;

use App\Models\Candidate;
use App\Models\InterviewQuestion;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class InterviewPerSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize
{
    protected $jenjang;
    protected $gender;
    protected $genderLabel;
    protected $questions;

    public function __construct($jenjang, $gender)
    {
        $this->jenjang = $jenjang;
        $this->gender = $gender;
        $this->genderLabel = ($gender == 'L') ? 'Putra' : 'Putri';
        
        // Ambil pertanyaan agar kolom dinamis
        $this->questions = InterviewQuestion::where('is_active', true)
                            ->orderBy('target')
                            ->orderBy('order')
                            ->get();
    }

    // 1. Judul Sheet (Tab di Bawah Excel)
    public function title(): string
    {
        // Contoh: SMP - Putra
        return "{$this->jenjang} - {$this->genderLabel}";
    }

    // 2. Ambil Data Sesuai Jenjang & Gender
    public function collection()
    {
        return Candidate::where('jenjang', $this->jenjang)
            ->where('jenis_kelamin', $this->gender)
            ->whereHas('interview_answers') // Hanya yang sudah ada jawaban
            ->with('interview_answers')
            ->get();
    }

    // 3. Header Kolom
    public function headings(): array
    {
        $headers = [
            'NO',
            'NO PENDAFTARAN',
            'NAMA LENGKAP',
            'ASAL SEKOLAH',
            'NAMA WALI',
            'NO HP WALI'
        ];

        // Tambahkan Header Pertanyaan
        foreach ($this->questions as $q) {
            // Kita singkat targetnya biar kolom tidak terlalu lebar
            $target = $q->target == 'Wali' ? '[ORTU]' : '[SANTRI]';
            $headers[] = "$target " . $q->question;
        }

        return $headers;
    }

    // 4. Mapping Data (Isi Baris)
    public function map($candidate): array
    {
        static $no = 0;
        $no++;

        $row = [
            $no,
            $candidate->no_daftar,
            strtoupper($candidate->nama_lengkap),
            $candidate->asal_sekolah,
            $candidate->parent->nama_ayah ?? '-',
            $candidate->parent->no_hp ?? '-',
        ];

        // Loop jawaban
        foreach ($this->questions as $q) {
            $answer = $candidate->interview_answers->firstWhere('interview_question_id', $q->id);
            $text = $answer ? $answer->answer : '-';
            
            // Bersihkan teks dari karakter aneh agar Excel rapi
            $row[] = trim(preg_replace('/\s+/', ' ', $text));
        }

        return $row;
    }

    // 5. Styling (Supaya Cantik & Mudah Dibaca)
    public function styles(Worksheet $sheet)
    {
        // Hitung jumlah kolom total
        $lastColumn = $sheet->getHighestColumn();
        $highestRow = $sheet->getHighestRow();

        // Style A: Header (Baris 1)
        $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2F855A'] // Warna Hijau Emerald
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ]);

        // Style B: Seluruh Data (Border & Wrap Text)
        $sheet->getStyle("A2:{$lastColumn}{$highestRow}")->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP, // Teks mulai dari atas
                'wrapText' => true // PENTING: Agar teks panjang turun ke bawah (tidak memanjang samping)
            ]
        ]);

        // Set Lebar Kolom Manual untuk Pertanyaan (Agar Wrap Text berfungsi baik)
        // Mulai dari kolom G (Kolom ke-7) sampai akhir
        $columnIndex = 7; 
        foreach ($this->questions as $q) {
            $colString = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex);
            $sheet->getColumnDimension($colString)->setWidth(40); // Lebar fix 40 biar rapi
            $columnIndex++;
        }

        return [];
    }
}