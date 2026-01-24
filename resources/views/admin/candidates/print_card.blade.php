<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Kelulusan - {{ $candidate->nama_lengkap }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700;900&family=Noto+Serif:wght@400;700&display=swap" rel="stylesheet">
    
    <style>
        @page { size: A4 portrait; margin: 0; }
        body { 
            font-family: 'Noto Serif', serif; 
            background: #fdfdfd; 
            color: #111;
            margin: 0;
            padding: 40px;
            -webkit-print-color-adjust: exact;
        }

        /* CONTAINER DENGAN BINGKAI GANDA */
        .certificate-border {
            border: 5px double #166534; /* Emerald 800 */
            padding: 5px;
            height: 98vh;
            box-sizing: border-box;
            position: relative;
            background: white;
        }
        .inner-border {
            border: 2px solid #15803d; /* Emerald 700 */
            height: 100%;
            padding: 30px 50px;
            box-sizing: border-box;
            position: relative;
            z-index: 2;
        }

        /* WATERMARK LOGO */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.08;
            z-index: 1;
            width: 400px;
            pointer-events: none;
        }

        /* KOP SURAT */
        header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 20px;
            margin-bottom: 30px;
            position: relative;
        }
        .logo {
            position: absolute;
            top: 0;
            left: 20px;
            width: 90px;
            height: 90px;
            object-fit: contain;
        }
        .institution {
            font-family: 'Merriweather', serif;
            font-weight: 900;
            font-size: 24px;
            color: #166534;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
        }
        .subtitle {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin: 5px 0;
            color: #333;
        }
        .address {
            font-size: 11px;
            color: #555;
            font-style: italic;
            max-width: 80%;
            margin: 0 auto;
        }

        /* JUDUL SURAT */
        .title-section {
            text-align: center;
            margin-bottom: 40px;
        }
        .doc-title {
            font-size: 20px;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .doc-number {
            font-size: 12px;
        }

        /* ISI BIODATA */
        .content {
            font-size: 14px;
            line-height: 1.6;
        }
        .intro-text {
            margin-bottom: 20px;
            text-align: justify;
        }
        
        table.biodata {
            width: 100%;
            margin-left: 20px;
            margin-bottom: 30px;
        }
        table.biodata td {
            padding: 6px 0;
            vertical-align: top;
        }
        .label { width: 160px; font-weight: bold; }
        .sep { width: 20px; }
        .val { font-weight: bold; color: #000; font-size: 15px; }

        /* KOTAK STATUS LULUS */
        .status-box {
            text-align: center;
            margin: 30px auto;
            padding: 15px;
            border: 2px solid #166534;
            background-color: #f0fdf4;
            width: 70%;
            font-weight: bold;
            font-size: 18px;
            color: #166534;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* TANDA TANGAN */
        .footer {
            margin-top: 60px;
            display: flex;
            justify-content: flex-end;
        }
        .signature-box {
            text-align: center;
            width: 250px;
        }
        .place-date { font-size: 14px; margin-bottom: 5px; }
        .role { font-weight: bold; margin-bottom: 70px; }
        .name { font-weight: bold; text-decoration: underline; font-size: 15px; }
        .nip { font-size: 12px; }

        /* TOMBOL PRINT (HILANG SAAT PRINT) */
        .no-print {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }
        .btn-print {
            background: #166534;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 50px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            font-family: sans-serif;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }
        .btn-print:hover { background: #14532d; transform: scale(1.05); }

        @media print {
            .no-print { display: none; }
            body { padding: 0; background: white; }
            .certificate-border { border: none; height: auto; }
            .inner-border { border: 3px double #000; height: auto; padding: 20px 40px; margin: 10px; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()" class="btn-print">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><path d="M6 14h12v8H6z"/></svg>
            Cetak Kartu
        </button>
    </div>

    <div class="certificate-border">
        <div class="inner-border">
            
            @if(!empty($settings['logo_sekolah']))
                <img src="{{ asset('storage/'.$settings['logo_sekolah']) }}" class="watermark" alt="Watermark">
            @endif

            <header>
                @if(!empty($settings['logo_sekolah']))
                    <img src="{{ asset('storage/'.$settings['logo_sekolah']) }}" class="logo" alt="Logo">
                @else
                    <div class="logo" style="background:#ddd; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:30px; border-radius:50%;">P</div>
                @endif

                <h1 class="institution">{{ $settings['nama_sekolah'] ?? 'PONDOK PESANTREN AL-HIDAYAH' }}</h1>
                <p class="subtitle">PANITIA PENERIMAAN SANTRI BARU (PPDB)</p>
                <p class="subtitle">TAHUN AJARAN {{ date('Y') }}/{{ date('Y')+1 }}</p>
                <p class="address">{{ $settings['alamat_sekolah'] ?? 'Alamat belum diatur di menu pengaturan.' }}</p>
            </header>

            <div class="title-section">
                <div class="doc-title">SURAT KETERANGAN LULUS SELEKSI</div>
                <div class="doc-number">Nomor: {{ $candidate->no_daftar }}/PPDB/{{ date('Y') }}</div>
            </div>

            <div class="content">
                <p class="intro-text">
                    Berdasarkan hasil seleksi administrasi dan tes yang telah dilakukan, Panitia Penerimaan Santri Baru (PPDB) 
                    <strong>{{ $settings['nama_sekolah'] ?? 'Pondok Pesantren' }}</strong> dengan ini menerangkan bahwa:
                </p>

                <table class="biodata">
                    <tr>
                        <td class="label">Nama Lengkap</td>
                        <td class="sep">:</td>
                        <td class="val" style="text-transform: uppercase;">{{ $candidate->nama_lengkap }}</td>
                    </tr>
                    <tr>
                        <td class="label">No. Pendaftaran</td>
                        <td class="sep">:</td>
                        <td class="val">{{ $candidate->no_daftar }}</td>
                    </tr>
                    <tr>
                        <td class="label">NISN</td>
                        <td class="sep">:</td>
                        <td class="val">{{ $candidate->nisn ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Asal Sekolah</td>
                        <td class="sep">:</td>
                        <td class="val">{{ $candidate->asal_sekolah ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Jenjang Diterima</td>
                        <td class="sep">:</td>
                        <td class="val">{{ $candidate->jenjang }}</td>
                    </tr>
                    <tr>
                        <td class="label">Nama Orang Tua</td>
                        <td class="sep">:</td>
                        <td class="val">{{ $candidate->parent->nama_ayah ?? $candidate->parent->nama_ibu ?? '-' }}</td>
                    </tr>
                </table>

                <p style="text-align: center;">Dinyatakan:</p>

                <div class="status-box">
                    LULUS / DITERIMA
                </div>

                <p class="intro-text">
                    Sebagai Santri Baru Tahun Ajaran {{ date('Y') }}/{{ date('Y')+1 }}. 
                    Harap segera melakukan daftar ulang dengan membawa kartu ini sebagai bukti kelulusan yang sah.
                </p>
            </div>

            <div class="footer">
                <div class="signature-box">
                    <div class="place-date">Ditetapkan di: Garut, {{ date('d F Y') }}</div>
                    <div class="role">Panitia PPDB,</div>
                    
                    <div class="name">__________________________</div>
                    <div class="nip">NIP/NIY. ...........................</div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>