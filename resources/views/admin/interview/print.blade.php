<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Seleksi - {{ $candidate->nama_lengkap }}</title>
    <style>
        @page { size: A4 portrait; margin: 2cm; }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            color: #000;
            background: #fff;
            margin: 0; padding: 0;
            -webkit-print-color-adjust: exact;
        }

        /* UTILS */
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .page-break { page-break-before: always; }
        
        /* KOP SURAT RESMI */
        .kop-surat {
            display: flex;
            align-items: center;
            border-bottom: 4px double #000; /* Garis Ganda */
            padding-bottom: 12px;
            margin-bottom: 25px;
            position: relative;
        }
        .logo-container { width: 90px; text-align: center; }
        .logo-img { width: 80px; height: auto; object-fit: contain; }
        .kop-text { flex: 1; text-align: center; padding-right: 90px; /* Biar tengah sempurna */ }
        .yayasan { font-size: 10pt; font-weight: bold; letter-spacing: 1px; margin-bottom: 2px; }
        .sekolah { font-size: 16pt; font-weight: 900; text-transform: uppercase; margin-bottom: 2px; font-family: Arial, Helvetica, sans-serif; color: #166534; }
        .alamat { font-size: 9pt; font-style: italic; color: #333; }

        /* JUDUL DOKUMEN */
        .doc-title { text-align: center; margin-bottom: 20px; }
        .doc-title h2 { margin: 0; font-size: 13pt; text-decoration: underline; text-transform: uppercase; }
        .doc-title p { margin: 2px 0 0 0; font-size: 10pt; }

        /* TABEL DATA DIRI */
        .info-table { width: 100%; margin-bottom: 20px; font-size: 11pt; border-collapse: collapse; }
        .info-table td { padding: 3px 5px; vertical-align: top; }
        .label { width: 160px; font-weight: bold; }
        
        /* TABEL JAWABAN */
        .result-table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        .result-table th, .result-table td { border: 1px solid #000; padding: 6px 8px; vertical-align: top; }
        .result-table th { background-color: #f3f3f3 !important; font-weight: bold; text-align: center; }
        
        /* TANDA TANGAN */
        .signature-grid { display: flex; justify-content: space-between; margin-top: 30px; page-break-inside: avoid; }
        .sig-box { text-align: center; width: 40%; }
        .sig-space { height: 70px; }
        .sig-name { font-weight: bold; text-decoration: underline; }

        /* NO PRINT BUTTON */
        .no-print {
            position: fixed; top: 20px; right: 20px;
            background: #2563eb; color: white;
            padding: 10px 20px; border-radius: 50px;
            text-decoration: none; font-family: sans-serif; font-weight: bold; font-size: 14px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            z-index: 9999;
        }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>

    <a href="javascript:window.print()" class="no-print">🖨️ Cetak Dokumen</a>

    <div class="page">
        <div class="kop-surat">
            <div class="logo-container">
                @if(!empty($settings['logo_sekolah']))
                    <img src="{{ asset('storage/'.$settings['logo_sekolah']) }}" class="logo-img" alt="Logo">
                @endif
            </div>
            <div class="kop-text">
                <div class="yayasan">PANITIA PENERIMAAN SANTRI BARU (PPDB)</div>
                <div class="sekolah">{{ $settings['nama_sekolah'] ?? 'PONDOK PESANTREN AL-HIDAYAH' }}</div>
                <div class="alamat">{{ $settings['alamat_sekolah'] ?? 'Alamat belum diatur di menu pengaturan.' }}</div>
            </div>
        </div>

        <div class="doc-title">
            <h2>BERITA ACARA ASESMEN SANTRI</h2>
            <p>Nomor: {{ $candidate->no_daftar }}/ASESMEN/{{ date('Y') }}</p>
        </div>

        <table class="info-table">
            <tr><td class="label">Nama Lengkap</td><td>: {{ $candidate->nama_lengkap }}</td></tr>
            <tr><td class="label">No. Pendaftaran</td><td>: {{ $candidate->no_daftar }}</td></tr>
            <tr><td class="label">Jenjang Pendidikan</td><td>: {{ $candidate->jenjang }}</td></tr>
        </table>

        <table class="result-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 45%;">Pertanyaan Asesmen</th>
                    <th style="width: 50%;">Jawaban Santri</th>
                </tr>
            </thead>
            <tbody>
                @forelse($santriAnswers as $ans)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $ans->question->question }}</td>
                        <td style="font-weight: bold;">{{ $ans->answer }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center" style="font-style: italic; padding: 20px;">Belum ada data asesmen santri.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="signature-grid">
            <div class="sig-box">
                <p>Mengetahui,<br>Penguji / Pewawancara</p>
                <div class="sig-space"></div>
                <p class="sig-name">( ....................................... )</p>
            </div>
            <div class="sig-box">
                <p>Garut, {{ date('d F Y') }}<br>Calon Santri</p>
                <div class="sig-space"></div>
                <p class="sig-name">{{ $candidate->nama_lengkap }}</p>
            </div>
        </div>
    </div>

    <div class="page-break"></div>

    <div class="page">
        <div class="kop-surat">
            <div class="logo-container">
                @if(!empty($settings['logo_sekolah']))
                    <img src="{{ asset('storage/'.$settings['logo_sekolah']) }}" class="logo-img" alt="Logo">
                @endif
            </div>
            <div class="kop-text">
                <div class="yayasan">PANITIA PENERIMAAN SANTRI BARU (PPDB)</div>
                <div class="sekolah">{{ $settings['nama_sekolah'] ?? 'PONDOK PESANTREN AL-HIDAYAH' }}</div>
                <div class="alamat">{{ $settings['alamat_sekolah'] ?? 'Alamat belum diatur di menu pengaturan.' }}</div>
            </div>
        </div>

        <div class="doc-title">
            <h2>HASIL WAWANCARA WALI SANTRI</h2>
            <p>Nomor: {{ $candidate->no_daftar }}/WALI/{{ date('Y') }}</p>
        </div>

        <table class="info-table">
            <tr><td class="label">Nama Santri</td><td>: {{ $candidate->nama_lengkap }}</td></tr>
            <tr><td class="label">Nama Wali</td><td>: {{ $candidate->parent->nama_ayah ?? $candidate->parent->nama_ibu ?? '-' }}</td></tr>
            <tr><td class="label">Nomor HP/WA</td><td>: {{ $candidate->parent->no_hp ?? '-' }}</td></tr>
        </table>

        <table class="result-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 45%;">Materi Wawancara</th>
                    <th style="width: 50%;">Jawaban / Komitmen Wali</th>
                </tr>
            </thead>
            <tbody>
                @forelse($waliAnswers as $ans)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $ans->question->question }}</td>
                        <td style="font-weight: bold;">{{ $ans->answer }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center" style="font-style: italic; padding: 20px;">Belum ada data wawancara wali.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div style="border: 2px dashed #333; padding: 15px; margin-bottom: 30px;">
            <strong>Catatan Khusus Panitia:</strong>
            <br><br><br><br>
        </div>

        <div class="signature-grid">
            <div class="sig-box">
                <p>Mengetahui,<br>Pewawancara</p>
                <div class="sig-space"></div>
                <p class="sig-name">( ....................................... )</p>
            </div>
            <div class="sig-box">
                <p>Garut, {{ date('d F Y') }}<br>Orang Tua / Wali</p>
                <div class="sig-space"></div>
                <p class="sig-name">{{ $candidate->parent->nama_ayah ?? '( ....................................... )' }}</p>
            </div>
        </div>
    </div>

</body>
</html>