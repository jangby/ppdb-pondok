<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>Verifikasi Berkas - PPDB</title>
    
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        h1, h2, h3, h4, h5, h6 { font-family: 'Outfit', sans-serif; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* Background Animasi */
        .ambient-bg { position: absolute; width: 100%; height: 100%; overflow: hidden; z-index: -1; background-color: #f8fafc; }
        .blob-green { position: absolute; top: -10%; right: -5%; width: 500px; height: 500px; background: radial-gradient(circle, rgba(16, 185, 129, 0.15) 0%, rgba(255,255,255,0) 70%); border-radius: 50%; filter: blur(60px); }
        .blob-teal { position: absolute; bottom: 10%; left: -10%; width: 400px; height: 400px; background: radial-gradient(circle, rgba(20, 184, 166, 0.1) 0%, rgba(255,255,255,0) 70%); border-radius: 50%; filter: blur(60px); }
    </style>
</head>
<body class="bg-slate-50 text-slate-600 antialiased h-screen flex flex-col justify-center relative overflow-hidden">

    {{-- Background --}}
    <div class="fixed inset-0 pointer-events-none ambient-bg">
        <div class="blob-green animate-pulse" style="animation-duration: 8s;"></div>
        <div class="blob-teal animate-pulse" style="animation-duration: 10s;"></div>
    </div>

    {{-- Tombol Kembali (Pojok Kiri Atas) --}}
    <div class="absolute top-6 left-6 z-20">
        <a href="{{ url('/') }}" class="flex items-center gap-2 text-slate-500 hover:text-emerald-600 transition text-sm font-bold bg-white/50 backdrop-blur-sm px-4 py-2 rounded-full border border-slate-200 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    {{-- Main Content --}}
    <div class="w-full max-w-lg mx-auto px-4 relative z-10">
        
        <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-2xl border border-white overflow-hidden">
            
            {{-- Progress Bar --}}
            <div class="h-1.5 w-full bg-slate-100">
                <div class="h-full bg-emerald-500 w-1/3 rounded-r-full shadow-[0_0_10px_rgba(16,185,129,0.5)]"></div>
            </div>

            <div class="p-8 md:p-10">
                {{-- Header --}}
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-emerald-100 text-emerald-600 rounded-2xl mb-4 text-xl font-bold">
                        1
                    </div>
                    <h2 class="text-2xl font-extrabold text-slate-900">Verifikasi Berkas</h2>
                    <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                        Langkah awal pendaftaran. Unduh template, tanda tangani, dan upload kembali untuk mendapatkan akses formulir.
                    </p>
                </div>

                {{-- Alert Download Template --}}
                @if($template)
                <div class="mb-8 bg-blue-50 border border-blue-100 rounded-2xl p-5 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-16 h-16 bg-blue-100 rounded-bl-full -mr-4 -mt-4 opacity-50"></div>
                    <div class="relative z-10 text-center">
                        <p class="text-xs font-bold text-blue-500 uppercase tracking-widest mb-1">Langkah 1</p>
                        <h3 class="font-bold text-slate-800 text-sm mb-3">Belum punya surat perjanjian?</h3>
                        <a href="{{ asset('storage/'.$template) }}" target="_blank" class="inline-flex items-center justify-center gap-2 w-full py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-sm shadow-lg shadow-blue-500/20 transition transform active:scale-95">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Download Template PDF
                        </a>
                    </div>
                </div>
                @endif

                {{-- Form Area --}}
                <form action="{{ route('pendaftaran.verify.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    {{-- Input WA --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Nomor WhatsApp Orangtua/Wali Aktif</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            </div>
                            <input type="number" name="no_wa" required placeholder="Contoh: 08123456789" 
                                class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition font-medium text-slate-800 placeholder-slate-400">
                        </div>
                        @error('no_wa') <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p> @enderror
                        <p class="text-[10px] text-slate-400 mt-2 ml-1">
                            *Link formulir pendaftaran akan dikirim ke nomor ini.
                        </p>
                    </div>

                    {{-- Input File --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Upload Berkas (Foto/PDF)</label>
                        <div class="relative group">
                            <input type="file" name="berkas" accept=".pdf,.jpg,.jpeg,.png" required 
                                class="block w-full text-sm text-slate-500
                                file:mr-4 file:py-3 file:px-6
                                file:rounded-xl file:border-0
                                file:text-sm file:font-bold
                                file:bg-emerald-50 file:text-emerald-700
                                hover:file:bg-emerald-100
                                bg-white border border-slate-200 rounded-xl cursor-pointer p-1 shadow-sm transition
                                ">
                        </div>
                        @error('berkas') <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p> @enderror
                        <p class="text-[10px] text-slate-400 mt-2 ml-1">
                            *Pastikan berkas sudah ditandatangani. Max 2MB.
                        </p>
                    </div>

                    {{-- Tombol Submit --}}
                    <button type="submit" class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-xl shadow-emerald-500/30 transition transform active:scale-95 flex items-center justify-center gap-2 mt-4">
                        <span>Kirim Berkas</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>

                </form>
            </div>
        </div>

        {{-- Footer Help --}}
        <div class="text-center mt-8">
            <p class="text-xs text-slate-500">Butuh bantuan?</p>
            <a href="https://wa.me/" class="text-xs font-bold text-emerald-600 hover:underline">Hubungi Panitia</a>
        </div>

    </div>

</body>
</html>