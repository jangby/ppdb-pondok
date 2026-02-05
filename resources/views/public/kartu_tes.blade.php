<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Tes - {{ $candidate->nama_lengkap }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="bg-white max-w-sm w-full rounded-2xl shadow-xl overflow-hidden border border-gray-200">
        <div class="bg-blue-600 p-6 text-center text-white">
            <h1 class="font-bold text-xl uppercase tracking-wider">Kartu Tes PPDB</h1>
            <p class="text-blue-100 text-xs mt-1">Tunjukkan QR ini ke Panitia</p>
        </div>

        <div class="p-8 flex flex-col items-center justify-center bg-white">
            <div class="p-2 border-2 border-dashed border-gray-300 rounded-xl mb-4">
                {{-- Generate QR langsung dari API QRServer --}}
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ $candidate->no_daftar }}" 
                     alt="QR Code" 
                     class="w-48 h-48 object-contain">
            </div>

            <p class="text-xs text-gray-400 font-mono mb-1">NO. DAFTAR</p>
            <h2 class="text-2xl font-black text-gray-800 tracking-widest">{{ $candidate->no_daftar }}</h2>
        </div>

        <div class="px-6 pb-6">
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 space-y-3">
                <div>
                    <p class="text-[10px] text-gray-400 uppercase font-bold">Nama Lengkap</p>
                    <p class="font-bold text-gray-800">{{ $candidate->nama_lengkap }}</p>
                </div>
                <div>
                    <p class="text-[10px] text-gray-400 uppercase font-bold">Jenjang</p>
                    <p class="font-medium text-gray-700">{{ $candidate->jenjang }}</p>
                </div>
            </div>

            <div class="mt-6 text-center">
                <p class="text-xs text-gray-400">Silakan screenshot halaman ini sebagai cadangan.</p>
            </div>
        </div>
    </div>

</body>
</html>