<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Berhasil - PPDB Pesantren</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

    <div class="bg-white p-8 rounded-lg shadow-xl max-w-md w-full text-center">
        <div class="mb-4">
            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100">
                <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
        </div>

        <h2 class="text-2xl font-bold text-gray-800 mb-2">Alhamdulillah!</h2>
        <p class="text-gray-600 mb-6">Pendaftaran Anda berhasil dikirim ke sistem kami.</p>

        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Nomor Pendaftaran Anda</p>
            <p class="text-3xl font-mono font-bold text-green-700 mt-1">{{ $no_daftar }}</p>
        </div>

        <div class="text-left text-sm text-gray-600 mb-8 space-y-2">
            <p><strong>Langkah Selanjutnya:</strong></p>
            <ul class="list-disc pl-5">
                <li>Simpan Nomor Pendaftaran di atas.</li>
                <li>Tunggu konfirmasi dari Admin melalui WhatsApp orang tua.</li>
                <li>Jika mendaftar Offline, silakan tunjukkan nomor ini ke panitia.</li>
            </ul>
        </div>

        <div class="space-y-3">
            <button onclick="window.print()" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded transition">
                Cetak Bukti
            </button>
            <a href="{{ route('home') }}" class="block w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded transition">
                Kembali ke Halaman Depan
            </a>
        </div>
    </div>

</body>
</html>