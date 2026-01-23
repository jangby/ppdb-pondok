<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pendaftaran Berhasil</title>
    
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .pattern-bg { background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%2310b981' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E"); }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4 pattern-bg">

    <div class="max-w-md w-full bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden relative">
        
        {{-- Confetti Decoration (CSS Only) --}}
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-emerald-400 via-green-500 to-teal-500"></div>

        <div class="p-8 text-center">
            
            {{-- Animated Success Icon --}}
            <div class="w-24 h-24 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-6 animate-[bounce_2s_infinite]">
                <div class="w-16 h-16 bg-emerald-500 rounded-full flex items-center justify-center shadow-lg shadow-emerald-500/40">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                </div>
            </div>

            <h1 class="text-2xl font-extrabold text-slate-900 mb-2">Alhamdulillah!</h1>
            <p class="text-slate-500 text-sm mb-8">
                Data pendaftaran Anda berhasil disimpan.
            </p>

            {{-- Nomor Pendaftaran Card --}}
            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200 border-dashed relative mb-8">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Nomor Pendaftaran</p>
                <p class="text-3xl font-black text-slate-800 tracking-tight">{{ $no_daftar }}</p>
                
                {{-- Copy Button --}}
                <button onclick="navigator.clipboard.writeText('{{ $no_daftar }}'); alert('Nomor disalin!')" class="absolute top-4 right-4 text-slate-400 hover:text-emerald-600 transition" title="Salin">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                </button>
            </div>

            {{-- Next Steps --}}
            <div class="text-left space-y-4 mb-8">
                <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-2">Langkah Selanjutnya:</h3>
                
                <div class="flex gap-3">
                    <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-[10px] font-bold flex-shrink-0">1</div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Simpan <b>Nomor Pendaftaran</b> di atas.
                    </p>
                </div>
                
                <div class="flex gap-3">
                    <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-[10px] font-bold flex-shrink-0">2</div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Admin akan melakukan verifikasi data biodata Anda.
                    </p>
                </div>

                <div class="flex gap-3">
                    <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-[10px] font-bold flex-shrink-0">3</div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Tunggu informasi jadwal tes seleksi yang akan dikirim melalui WhatsApp.
                    </p>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="grid grid-cols-1 gap-3">
                {{-- Tombol Cetak (Opsional, diarahkan ke route cetak jika ada) --}}
                {{-- 
                <a href="#" class="flex items-center justify-center gap-2 w-full py-3 bg-slate-900 text-white font-bold rounded-xl hover:bg-slate-800 transition shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Bukti Pendaftaran
                </a> 
                --}}

                <a href="{{ route('home') }}" class="w-full py-3 bg-white border border-slate-200 text-slate-600 font-bold rounded-xl hover:bg-slate-50 transition">
                    Kembali ke Beranda
                </a>
            </div>

        </div>
        
        <div class="bg-slate-50 p-4 text-center border-t border-slate-100">
            <p class="text-[10px] text-slate-400">Terima kasih telah mendaftar.</p>
        </div>

    </div>

</body>
</html>