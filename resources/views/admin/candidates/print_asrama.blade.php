<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Asrama - {{ $candidate->nama_lengkap }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #e2e8f0; }
        
        /* Setting Ukuran Kertas Setengah F4 (Sekitar 21.5cm x 16.5cm) */
        @page {
            size: 215mm 165mm landscape;
            margin: 10mm;
        }

        .paper-half-f4 {
            width: 195mm; 
            height: 145mm;
            background: white;
            margin: 20px auto;
            padding: 15px 30px;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
            box-sizing: border-box;

            /* --- TAMBAHAN GARIS TEBAL UNTUK GUNTING --- */
            border: 4px dashed #64748b; /* Garis putus-putus tebal warna slate/abu-abu kebiruan */
            border-radius: 12px; /* Diberi sedikit lengkungan agar terlihat seperti kartu premium */
        }

        @media print {
            body { background: white; margin: 0; padding: 0; }
            .paper-half-f4 { 
                margin: 0 auto; 
                box-shadow: none; 
                /* Pastikan garis tepi tetap ikut tercetak saat diprint */
                border: 4px dashed #64748b !important; 
            }
            .no-print { display: none !important; }
            .print-exact { -webkit-print-color-adjust: exact; color-adjust: exact; }
        }
        
        /* Elemen Dekoratif */
        .deco-line { width: 100%; height: 4px; background: #1e3a8a; margin: 10px 0 20px; }
        .deco-line-thin { width: 100%; height: 1px; background: #1e3a8a; margin-top: 2px; }
    </style>
</head>
<body>

    <div class="text-center py-4 no-print flex flex-col items-center justify-center gap-2">
        <button onclick="window.print()" class="bg-blue-600 text-white font-bold px-6 py-2 rounded-lg shadow hover:bg-blue-700 transition">🖨️ Cetak Kartu</button>
        <p class="text-sm text-gray-500">💡 Tips: Gunting kartu mengikuti garis putus-putus</p>
    </div>

    <div class="paper-half-f4 print-exact">
        
        {{-- KOP SURAT --}}
        <div class="flex items-center justify-center gap-6 mb-2 mt-2">
            {{-- Menggunakan logo default laravel/ui atau bisa diganti path gambar asli pondok --}}
            <img src="{{ asset('assets/logo pondok.jpg') }}" alt="Logo" class="h-20 object-contain" onerror="this.style.display='none'">
            <div class="text-center">
                <h1 class="text-2xl font-black uppercase text-blue-900 leading-tight">
                    {{ $settings['nama_sekolah'] ?? 'PONDOK PESANTREN AL-HIKAM' }}
                </h1>
                <p class="text-sm font-semibold text-gray-700">PANITIA PENERIMAAN SANTRI BARU (PSB)</p>
                <p class="text-xs text-gray-500 italic mt-1">{{ $settings['alamat_sekolah'] ?? 'Jl. Pesantren No. 1, Kab. Contoh' }}</p>
            </div>
        </div>
        
        <div class="deco-line"><div class="deco-line-thin"></div></div>

        {{-- JUDUL KARTU --}}
        <div class="text-center mb-6">
            <h2 class="text-xl font-black tracking-widest text-gray-800 uppercase border-2 border-gray-800 inline-block px-6 py-1 rounded-full bg-gray-50">KARTU PENEMPATAN ASRAMA</h2>
        </div>

        {{-- ISI DATA --}}
        <div class="flex gap-8">
            <div class="flex-1 space-y-4">
                <table class="w-full text-base">
                    <tr>
                        <td class="font-bold text-gray-600 w-36 pb-2">No. Registrasi</td>
                        <td class="pb-2 font-mono font-bold text-gray-900 text-lg">: {{ $candidate->no_daftar }}</td>
                    </tr>
                    <tr>
                        <td class="font-bold text-gray-600 pb-2">Nama Santri</td>
                        <td class="pb-2 font-bold text-gray-900 text-lg uppercase">: {{ $candidate->nama_lengkap }}</td>
                    </tr>
                    <tr>
                        <td class="font-bold text-gray-600 pb-2">Jenis Kelamin</td>
                        <td class="pb-2 font-bold text-gray-900">: {{ $candidate->jenis_kelamin == 'L' ? 'Laki-Laki (Santri)' : 'Perempuan (Santriyah)' }}</td>
                    </tr>
                    <tr>
                        <td class="font-bold text-gray-600 pb-2 align-top">Alamat Asal</td>
                        <td class="pb-2 font-semibold text-gray-700 leading-snug">
                            : {{ $candidate->address->alamat ?? '-' }} <br>
                            <span class="pl-2">Kec. {{ $candidate->address->kecamatan ?? '-' }}, {{ $candidate->address->kabupaten ?? '-' }}</span>
                        </td>
                    </tr>
                </table>
            </div>

            {{-- KOTAK NAMA ASRAMA --}}
            <div class="w-64 flex flex-col items-center justify-center border-4 border-blue-900 rounded-2xl bg-blue-50 p-4 shadow-inner">
                <span class="text-xs font-bold text-blue-800 uppercase tracking-widest mb-2 text-center">Menempati Asrama / Kamar</span>
                <span class="text-3xl font-black text-center text-blue-900 leading-none">
                    {{ $candidate->dormitory->nama_asrama ?? 'BELUM DITENTUKAN' }}
                </span>
                
                @if($candidate->dormitory && $candidate->dormitory->jenis_asrama)
                <span class="mt-3 px-3 py-1 bg-blue-900 text-white text-xs font-bold rounded-full">
                    Kawasan {{ $candidate->dormitory->jenis_asrama }}
                </span>
                @endif
            </div>
        </div>

        {{-- CATATAN BAWAH & TTD --}}
        <div class="absolute bottom-6 left-8 right-8 flex justify-between items-end border-t border-dashed border-gray-400 pt-3">
            <div class="text-xs text-gray-500 font-medium bg-white px-2">
                * Kartu ini harap dibawa dan ditunjukkan kepada Pengurus Asrama <br>saat kedatangan pertama santri di Pondok Pesantren.
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-500 mb-10">Panitia PSB</p>
                <p class="text-sm font-bold text-gray-800 border-b border-gray-800 inline-block px-4">________________</p>
            </div>
        </div>
        
    </div>

</body>
</html>