<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Ruangan - {{ $room->nama_ruangan }} [{{ $room->jenis }}]</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact; } /* Agar warna background tercetak */
        }
    </style>
</head>
<body onload="window.print()" class="bg-gray-100 p-8">

    <div class="max-w-3xl mx-auto bg-white p-8 shadow-xl rounded-xl print:shadow-none print:p-0 print:rounded-none">
        
        <div class="mb-6 no-print flex justify-between">
            <button onclick="window.history.back()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 transition">
                &larr; Kembali ke Dashboard
            </button>
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 transition">
                🖨️ Cetak Sekarang
            </button>
        </div>

        <div class="text-center border-b-4 border-double border-gray-800 pb-6 mb-6">
            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-1">PANITIA PPDB PONDOK PESANTREN</h3>
            <h1 class="text-3xl font-black text-gray-900 uppercase tracking-wide leading-none mb-2">DAFTAR PESERTA TES WAWANCARA</h1>
            <p class="text-gray-600 font-medium">Tahun Ajaran {{ date('Y') }}/{{ date('Y')+1 }}</p>
        </div>

        <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 mb-6 grid grid-cols-3 gap-4">
            <div class="border-r border-gray-200">
                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Nama Ruangan</span>
                <span class="block text-xl font-black text-gray-800">{{ $room->nama_ruangan }}</span>
            </div>
            <div class="border-r border-gray-200 text-center">
                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Peruntukan Peserta</span>
                <span class="inline-block px-3 py-1 rounded-full text-sm font-bold {{ $room->jenis == 'Santri' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                    {{ strtoupper($room->jenis == 'Santri' ? 'Calon Santri' : 'Wali Santri') }}
                </span>
            </div>
            <div class="text-right">
                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Lokasi</span>
                <span class="block text-gray-800 font-medium">{{ $room->lokasi ?? '-' }}</span>
            </div>
        </div>

        <table class="w-full border-collapse border border-gray-300 text-sm">
            <thead>
                <tr class="bg-gray-800 text-white uppercase tracking-wider text-xs font-bold">
                    <th class="border border-gray-800 p-3 w-12 text-center">No</th>
                    <th class="border border-gray-800 p-3 w-40 text-left">No. Registrasi</th>
                    <th class="border border-gray-800 p-3 text-left">Nama Lengkap Peserta</th>
                    <th class="border border-gray-800 p-3 w-32 text-center">Jenjang</th>
                </tr>
            </thead>
            <tbody>
                @forelse($participants as $index => $c)
                <tr class="odd:bg-white even:bg-gray-50">
                    <td class="border border-gray-300 p-3 text-center font-bold text-gray-600">{{ $index + 1 }}</td>
                    <td class="border border-gray-300 p-3 font-mono font-bold text-blue-900">{{ $c->no_daftar }}</td>
                    <td class="border border-gray-300 p-3 uppercase font-bold text-gray-800 text-[13px]">
                        {{ $c->nama_lengkap }}
                    </td>
                    <td class="border border-gray-300 p-3 text-center">
                        <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded font-bold text-xs">
                            {{ $c->jenjang }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="border border-gray-300 p-12 text-center text-gray-400 italic bg-gray-50">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        Belum ada peserta yang dijadwalkan di ruangan ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-6 flex justify-between items-center text-sm text-gray-600 border-t border-gray-300 pt-4">
            <div>
                Dicetak pada: <span class="font-medium">{{ now()->format('d F Y, H:i') }} WIB</span>
            </div>
            <div class="text-right bg-gray-100 border border-gray-200 px-4 py-2 rounded-lg">
                <span class="text-xs font-bold text-gray-500 uppercase">Total Peserta</span>
                <p class="text-xl font-black text-gray-800">{{ $participants->count() }} <span class="text-sm font-medium">Orang</span></p>
            </div>
        </div>

    </div>

</body>
</html>