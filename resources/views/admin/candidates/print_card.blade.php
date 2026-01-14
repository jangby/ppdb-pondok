<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Tanda Diterima - {{ $candidate->nama_lengkap }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@700&family=Inter:wght@400;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6; 
        }

        /* Konfigurasi Cetak */
        @media print {
            body {
                background-color: white;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
            .print-shadow-none {
                box-shadow: none !important;
                border: 1px solid #ddd;
            }
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.05;
            width: 80%;
            z-index: 0;
            pointer-events: none;
        }
    </style>
</head>
<body class="flex flex-col items-center justify-center min-h-screen py-10">

    <div class="no-print mb-6 flex gap-3">
        <a href="{{ route('admin.candidates.show', $candidate->id) }}" class="px-5 py-2 bg-gray-500 text-white rounded-lg shadow hover:bg-gray-600 transition">
            &larr; Kembali
        </a>
        <button onclick="window.print()" class="px-5 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak Kartu
        </button>
    </div>

    <div class="relative bg-white w-[800px] h-[500px] shadow-2xl rounded-xl overflow-hidden print-shadow-none border border-gray-200">
        
        <img src="https://via.placeholder.com/500x500.png?text=LOGO+SEKOLAH" alt="Watermark" class="watermark grayscale">

        <div class="relative z-10 bg-indigo-900 text-white px-8 py-5 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-md text-indigo-900 font-bold overflow-hidden">
                   {{-- GANTI DENGAN LOGO ASLI --}}
                   <img src="https://via.placeholder.com/100" class="w-full h-full object-cover">
                </div>
                <div>
                    <h1 class="text-xl font-bold uppercase tracking-wider font-[Cinzel]">Nama Pesantren / Sekolah</h1>
                    <p class="text-indigo-200 text-sm font-light">Jl. Contoh Alamat No. 123, Kabupaten Tasikmalaya</p>
                </div>
            </div>
            <div class="text-right">
                <h2 class="text-2xl font-bold text-yellow-400 tracking-widest">KARTU BUKTI</h2>
                <p class="text-sm uppercase tracking-widest text-white/80">Kelulusan Santri Baru</p>
            </div>
        </div>

        <div class="h-2 bg-yellow-500 w-full relative z-10"></div>

        <div class="relative z-10 p-8 flex gap-8 h-full">
            
            <div class="w-1/3 flex flex-col items-center">
                <div class="w-40 h-52 bg-gray-200 border-4 border-white shadow-lg mb-4 overflow-hidden relative">
                    {{-- Placeholder jika foto kosong --}}
                    @if(false) {{-- Ganti logic ini jika ada foto --}}
                        <img src="{{ asset('storage/' . $candidate->foto) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-100">
                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                    @endif
                </div>

                <div class="bg-green-600 text-white px-6 py-1.5 rounded-full font-bold uppercase tracking-widest text-sm shadow-md border-2 border-green-700">
                    DITERIMA
                </div>
            </div>

            <div class="w-2/3 flex flex-col justify-between pb-12">
                <div>
                    <h3 class="text-indigo-900 font-bold text-2xl mb-1">{{ $candidate->nama_lengkap }}</h3>
                    <p class="text-gray-500 font-medium mb-6">No. Daftar: <span class="text-black">{{ $candidate->no_daftar }}</span></p>

                    <table class="w-full text-sm">
                        <tr class="border-b border-gray-100">
                            <td class="py-2 text-gray-500 w-32">NISN</td>
                            <td class="py-2 font-semibold text-gray-800">: {{ $candidate->nisn ?? '-' }}</td>
                        </tr>
                        <tr class="border-b border-gray-100">
                            <td class="py-2 text-gray-500">Jenjang</td>
                            <td class="py-2 font-semibold text-gray-800">: {{ $candidate->jenjang }}</td>
                        </tr>
                        <tr class="border-b border-gray-100">
                            <td class="py-2 text-gray-500">Asal Sekolah</td>
                            <td class="py-2 font-semibold text-gray-800">: {{ $candidate->asal_sekolah }}</td>
                        </tr>
                        <tr class="border-b border-gray-100">
                            <td class="py-2 text-gray-500">Nama Ayah</td>
                            <td class="py-2 font-semibold text-gray-800">: {{ $candidate->parent->nama_ayah ?? '-' }}</td>
                        </tr>
                         <tr class="border-b border-gray-100">
                            <td class="py-2 text-gray-500">Alamat</td>
                            <td class="py-2 font-semibold text-gray-800 truncate max-w-xs">: {{ $candidate->address->kabupaten ?? '-' }}, {{ $candidate->address->provinsi ?? '-' }}</td>
                        </tr>
                    </table>
                </div>

                <div class="flex justify-between items-end mt-4">
                    <div class="text-center">
                        {{-- Placeholder QR CODE --}}
                        <div class="w-20 h-20 bg-white border border-gray-300 p-1">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $candidate->no_daftar }}" class="w-full h-full">
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1">Scan untuk validasi</p>
                    </div>

                    <div class="text-center">
                        <p class="text-xs text-gray-500 mb-10">Tasikmalaya, {{ date('d F Y') }} <br> Panitia PSB</p>
                        <p class="font-bold text-indigo-900 underline">H. Admin Pesantren</p>
                        <p class="text-xs text-gray-500">Ketua Panitia</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="absolute bottom-0 w-full h-3 bg-indigo-900 z-10"></div>
    </div>

</body>
</html>