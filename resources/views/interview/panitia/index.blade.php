<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>List Peserta - Wawancara</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        /* Hilangkan highlight biru saat tap di mobile */
        * { -webkit-tap-highlight-color: transparent; }
    </style>
</head>
<body class="pb-20">

    {{-- 1. HEADER & SEARCH (Sticky) --}}
    <div class="fixed top-0 left-0 w-full bg-white shadow-sm z-50">
        <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center bg-slate-900 text-white">
            <div>
                <h1 class="font-bold text-lg leading-none">Ruang Wawancara</h1>
                <p class="text-[10px] text-slate-400 mt-1">{{ $session->title }}</p>
            </div>
            <div class="flex items-center gap-2 px-2 py-1 bg-slate-800 rounded-lg">
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                <span class="text-[10px] font-bold">Online</span>
            </div>
        </div>

        <div class="p-3 bg-white">
            <form method="GET">
                <div class="relative">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari Nama / No Daftar..." 
                        class="w-full bg-slate-100 text-slate-800 border-none rounded-xl py-3 pl-11 pr-4 text-sm font-bold focus:ring-2 focus:ring-purple-500 transition placeholder-slate-400">
                    <svg class="w-5 h-5 text-slate-400 absolute left-4 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </form>
        </div>
    </div>

    {{-- Spacer untuk Fixed Header --}}
    <div class="h-40"></div>

    {{-- 2. LIST SANTRI --}}
    <div class="px-4 space-y-3">
        
        {{-- Alert Sukses --}}
        @if(session('success'))
            <div class="bg-emerald-100 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm font-bold shadow-sm flex items-center gap-3 animate-bounce">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                {{ session('success') }}
            </div>
        @endif

        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider pl-1 mb-2">Daftar Peserta ({{ $candidates->count() }})</p>

        @forelse($candidates as $c)
            @php
                // Cek apakah santri ini sudah pernah diwawancara (Ada data di tabel jawaban)
                $isDone = \App\Models\InterviewAnswer::where('candidate_id', $c->id)->exists();
            @endphp

            <a href="{{ route('interview.panitia.form', ['token' => $token, 'candidate_id' => $c->id]) }}" 
               class="block bg-white p-4 rounded-2xl border {{ $isDone ? 'border-emerald-200 bg-emerald-50/30' : 'border-gray-100' }} shadow-sm active:scale-[0.98] transition relative overflow-hidden">
                
                {{-- Indikator Sudah Selesai --}}
                @if($isDone)
                    <div class="absolute top-0 right-0 bg-emerald-500 text-white text-[9px] font-bold px-3 py-1 rounded-bl-xl">
                        SUDAH DIISI
                    </div>
                @endif

                <div class="flex items-center gap-4">
                    {{-- Avatar Inisial --}}
                    <div class="w-12 h-12 rounded-full {{ $isDone ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-500' }} flex items-center justify-center font-bold text-lg shrink-0">
                        {{ substr($c->nama_lengkap, 0, 1) }}
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-slate-800 truncate">{{ $c->nama_lengkap }}</h3>
                        <p class="text-xs text-slate-500 truncate mt-0.5">
                            {{ $c->no_daftar }} • <span class="font-semibold text-purple-600">{{ $c->jenjang }}</span>
                        </p>
                    </div>

                    {{-- Arrow Icon --}}
                    <div class="text-slate-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </div>
                </div>
            </a>
        @empty
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <h3 class="text-slate-500 font-bold">Data tidak ditemukan</h3>
                <p class="text-xs text-slate-400 mt-1">Coba cari dengan kata kunci lain.</p>
            </div>
        @endforelse
    </div>

</body>
</html>