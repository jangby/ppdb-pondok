<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Berkas Terkirim</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl border border-slate-100 p-8 text-center relative overflow-hidden">
        
        {{-- Background Decoration --}}
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-emerald-400 to-green-600"></div>
        
        {{-- Animated Icon --}}
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 animate-bounce">
            <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        </div>

        <h2 class="text-2xl font-extrabold text-slate-900 mb-2">Berkas Berhasil Diupload!</h2>
        <p class="text-slate-500 text-sm leading-relaxed mb-8">
            Terima kasih. Data Anda telah masuk ke sistem kami dan sedang dalam antrean peninjauan Admin.
        </p>

        {{-- Info Card --}}
        <div class="bg-slate-50 rounded-xl p-5 border border-slate-200 text-left mb-8">
            <h3 class="text-sm font-bold text-slate-800 mb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Apa Selanjutnya?
            </h3>
            <ul class="space-y-3">
                <li class="flex gap-3 text-xs text-slate-600">
                    <span class="flex-shrink-0 w-5 h-5 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-bold">1</span>
                    <span>Admin akan mengecek kelengkapan berkas Anda.</span>
                </li>
                <li class="flex gap-3 text-xs text-slate-600">
                    <span class="flex-shrink-0 w-5 h-5 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-bold">2</span>
                    <span>Jika <b>DITERIMA</b>, Anda akan menerima pesan WhatsApp berisi Link Formulir Biodata.</span>
                </li>
                <li class="flex gap-3 text-xs text-slate-600">
                    <span class="flex-shrink-0 w-5 h-5 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-bold">3</span>
                    <span>Jika <b>DITOLAK</b>, Admin akan menginfokan alasannya via WhatsApp.</span>
                </li>
            </ul>
        </div>

        <div class="space-y-3">
            <a href="{{ route('home') }}" class="block w-full py-3 px-4 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl transition shadow-lg">
                Kembali ke Beranda
            </a>
            <p class="text-[10px] text-slate-400">
                Pastikan nomor WhatsApp Anda aktif.
            </p>
        </div>

    </div>

</body>
</html>