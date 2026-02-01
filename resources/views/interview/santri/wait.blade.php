<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menunggu Sesi Dibuka</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-6">

    <div class="max-w-md w-full text-center">
        
        {{-- Ilustrasi Jam / Gembok --}}
        <div class="relative w-32 h-32 mx-auto mb-8">
            <div class="absolute inset-0 bg-yellow-100 rounded-full animate-ping opacity-75"></div>
            <div class="relative bg-white p-6 rounded-full shadow-xl border-4 border-yellow-50 flex items-center justify-center">
                <svg class="w-12 h-12 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <h1 class="text-2xl font-extrabold text-gray-800 mb-2">SESI BELUM DIBUKA</h1>
        <p class="text-gray-500 mb-8 leading-relaxed">
            Mohon maaf, halaman tes belum dapat diakses.<br>
            Silakan tunggu instruksi dari Panitia Pengawas.
        </p>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Status Saat Ini</p>
            <div class="flex items-center justify-center gap-2">
                <span class="relative flex h-3 w-3">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                </span>
                <span class="font-bold text-red-600">TERKUNCI / STANDBY</span>
            </div>
        </div>

        {{-- Tombol Refresh --}}
        <button onclick="window.location.reload()" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-indigo-200 transition transform hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            Coba Refresh Halaman
        </button>

        <p class="text-xs text-gray-400 mt-6">
            Pondok Pesantren Assa'adah &copy; {{ date('Y') }}
        </p>

    </div>

</body>
</html>