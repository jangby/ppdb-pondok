<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status Pendaftaran - PPDB</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-100 min-h-screen flex flex-col justify-center items-center px-6">

    <div class="max-w-md w-full">
        {{-- LOGO / HEADER --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-indigo-600 rounded-3xl shadow-xl shadow-indigo-200 mb-4">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.040L3 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622l-0.382-3.016z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-800">Cek Status & Keuangan</h1>
            <p class="text-slate-500 text-sm mt-1">Masukkan nomor pendaftaran santri untuk melihat rincian.</p>
        </div>

        <div class="bg-white rounded-3xl shadow-xl p-8 border border-slate-100">
            {{-- ALERT ERROR --}}
            @if(session('error'))
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-xl">
                    <p class="text-xs text-red-700 font-bold">{{ session('error') }}</p>
                </div>
            @endif

            <form action="{{ route('public.finance.check') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Nomor Pendaftaran</label>
                    <input type="text" name="no_daftar" placeholder="Contoh: REG-2025XXXX" required
                        class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all text-center font-bold text-lg tracking-widest uppercase">
                    @error('no_daftar') <p class="text-red-500 text-[10px] mt-1 ml-1 font-bold uppercase">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="w-full py-4 bg-indigo-600 text-white font-bold rounded-2xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition transform active:scale-95 flex items-center justify-center gap-2">
                    LIHAT RINCIAN
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                </button>
            </form>
        </div>

        <div class="mt-8 text-center">
            <a href="/" class="text-xs font-bold text-slate-400 hover:text-indigo-600 transition uppercase tracking-widest"> Kembali ke Beranda </a>
        </div>
    </div>

</body>
</html>