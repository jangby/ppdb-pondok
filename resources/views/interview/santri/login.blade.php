<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>Login Santri - Asesmen Digital</title>
    
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        h1, h2, h3 { font-family: 'Outfit', sans-serif; }

        /* Animated Background Blobs (Green Theme) */
        .blob {
            position: absolute;
            filter: blur(60px);
            z-index: -1;
            opacity: 0.5;
            animation: move 15s infinite alternate;
        }
        .blob-1 { top: -10%; left: -10%; width: 400px; height: 400px; background: #34d399; animation-delay: 0s; } /* Emerald 400 */
        .blob-2 { bottom: -10%; right: -10%; width: 350px; height: 350px; background: #2dd4bf; animation-delay: -5s; } /* Teal 400 */
        .blob-3 { top: 40%; left: 40%; width: 250px; height: 250px; background: #a7f3d0; animation-delay: -8s; opacity: 0.6; } /* Emerald 200 */

        @keyframes move {
            from { transform: translate(0, 0) scale(1) rotate(0deg); }
            to { transform: translate(30px, -30px) scale(1.1) rotate(20deg); }
        }

        /* Glassmorphism Card Light */
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 20px 40px -5px rgba(16, 185, 129, 0.15); /* Green Shadow */
        }

        /* Input Styling */
        .input-glow:focus {
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.2);
            border-color: #10b981;
        }

        /* Pattern Overlay */
        .pattern-grid {
            background-image: radial-gradient(#10b981 0.5px, transparent 0.5px);
            background-size: 20px 20px;
            opacity: 0.1;
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4 relative overflow-hidden text-slate-800 selection:bg-emerald-200 selection:text-emerald-900">

    {{-- Background Animations --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="pattern-grid absolute inset-0"></div>
        <div class="blob blob-1 rounded-full"></div>
        <div class="blob blob-2 rounded-full"></div>
        <div class="blob blob-3 rounded-full"></div>
    </div>

    {{-- Main Card --}}
    <div class="glass-card w-full max-w-sm rounded-[2rem] p-8 md:p-10 relative z-10 flex flex-col items-center text-center">
        
        {{-- Icon Top --}}
        <div class="w-20 h-20 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-500/20 mb-6 transform rotate-3 hover:rotate-0 transition duration-500">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>

        {{-- Titles --}}
        <h1 class="text-3xl font-bold mb-2 tracking-tight text-slate-800">Asesmen Santri</h1>
        <p class="text-slate-500 text-sm mb-8 leading-relaxed font-medium">
            Masukkan nomor pendaftaran Anda untuk masuk ke ruang ujian.
        </p>

        {{-- Error Alert --}}
        @if(session('error'))
            <div class="w-full bg-red-50 border border-red-100 text-red-600 px-4 py-3 rounded-xl text-xs font-bold mb-6 flex items-center gap-2 animate-bounce shadow-sm">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- Form --}}
        <form action="{{ route('interview.santri.check') }}" method="POST" class="w-full space-y-5">
            @csrf
            
            <div class="group text-left">
                <label class="block text-[10px] font-bold text-emerald-600 uppercase tracking-widest mb-2 ml-1">Nomor Pendaftaran</label>
                <div class="relative">
                    <input type="text" name="no_daftar" required 
                        class="input-glow w-full bg-white border border-slate-200 rounded-xl py-4 px-12 text-center text-slate-800 font-bold text-lg tracking-widest placeholder-slate-300 focus:outline-none transition uppercase shadow-sm"
                        placeholder="OFF-202X..." autocomplete="off">
                    
                    {{-- Icon Input --}}
                    <div class="absolute left-4 top-4 text-emerald-500/50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path></svg>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full py-4 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/30 transition transform active:scale-95 flex items-center justify-center gap-2 group">
                <span>Mulai Mengerjakan</span>
                <svg class="w-5 h-5 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
            </button>
        </form>

        {{-- Footer --}}
        <div class="mt-8 pt-6 border-t border-slate-200/60 w-full">
            <p class="text-[10px] text-slate-400 font-medium">
                &copy; {{ date('Y') }} Panitia PPDB. <br>Harap jujur dalam mengisi data.
            </p>
        </div>
    </div>

</body>
</html>