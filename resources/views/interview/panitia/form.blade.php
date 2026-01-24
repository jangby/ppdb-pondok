<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>Isi Wawancara - {{ $candidate->nama_lengkap }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        /* Style Radio Button Card */
        .radio-box:checked + div {
            border-color: #7c3aed; /* Purple 600 */
            background-color: #f5f3ff; /* Purple 50 */
            color: #6d28d9;
        }
        .radio-box:checked + div .check-icon { opacity: 1; transform: scale(1); }
    </style>
</head>
<body class="pb-32">

    {{-- 1. HEADER --}}
    <div class="bg-white px-4 py-3 sticky top-0 z-50 shadow-sm border-b border-gray-100 flex items-center gap-3">
        {{-- TOMBOL KEMBALI (FIX ROUTE ERROR) --}}
        <a href="{{ route('interview.panitia.index', $token) }}" class="p-2 bg-slate-100 hover:bg-slate-200 rounded-full text-slate-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div class="flex-1 min-w-0">
            <h1 class="font-bold text-sm text-slate-800 truncate">{{ $candidate->nama_lengkap }}</h1>
            <p class="text-[10px] text-slate-500 font-medium uppercase tracking-wide">Form Wawancara Wali</p>
        </div>
    </div>

    {{-- 2. FORM AREA --}}
    <form action="{{ route('interview.panitia.store', ['token' => $token, 'candidate_id' => $candidate->id]) }}" method="POST" class="p-4 space-y-6">
        @csrf

        @foreach($questions as $q)
            @php 
                // Ambil jawaban lama jika ada (untuk edit)
                $oldVal = $existingAnswers[$q->id] ?? ''; 
            @endphp

            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex gap-3 mb-3">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-slate-900 text-white text-xs font-bold shrink-0">
                        {{ $loop->iteration }}
                    </span>
                    <label class="text-sm font-bold text-slate-800 leading-snug">{{ $q->question }}</label>
                </div>

                {{-- TIPE: TEXT (ESSAY) --}}
                @if($q->type == 'text')
                    <textarea name="answers[{{ $q->id }}]" rows="3" 
                        class="w-full bg-slate-50 border-slate-200 rounded-xl text-sm focus:ring-purple-500 focus:border-purple-500 p-3 placeholder-slate-400" 
                        placeholder="Tulis jawaban disini...">{{ $oldVal }}</textarea>
                
                {{-- TIPE: SCALE (1-10) --}}
                @elseif($q->type == 'scale')
                    <div class="flex gap-2 overflow-x-auto pb-2 hide-scroll">
                        @for($i=1; $i<=10; $i++)
                            <label class="flex-shrink-0 cursor-pointer group">
                                <input type="radio" name="answers[{{ $q->id }}]" value="{{ $i }}" class="radio-box sr-only" {{ $oldVal == $i ? 'checked' : '' }}>
                                <div class="w-10 h-10 rounded-lg border border-slate-200 flex items-center justify-center font-bold text-slate-500 transition group-hover:border-purple-300">
                                    {{ $i }}
                                </div>
                            </label>
                        @endfor
                    </div>
                    <div class="flex justify-between text-[10px] text-slate-400 px-1 mt-1 font-medium">
                        <span>Kurang</span>
                        <span>Sangat Baik</span>
                    </div>

                {{-- TIPE: CHOICE (PILIHAN GANDA) --}}
                @elseif($q->type == 'choice')
                    <div class="space-y-2">
                        @foreach($q->options as $opt)
                            <label class="block cursor-pointer relative">
                                <input type="radio" name="answers[{{ $q->id }}]" value="{{ $opt }}" class="radio-box sr-only" {{ $oldVal == $opt ? 'checked' : '' }}>
                                <div class="p-3.5 pl-4 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 transition flex items-center justify-between">
                                    <span>{{ $opt }}</span>
                                    
                                    {{-- Ikon Centang (Hidden by default) --}}
                                    <div class="check-icon w-5 h-5 bg-purple-600 rounded-full flex items-center justify-center text-white opacity-0 transform scale-50 transition-all duration-200">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach

        {{-- 3. FLOAT SUBMIT BUTTON --}}
        <div class="fixed bottom-0 left-0 w-full p-4 bg-white border-t border-gray-100 shadow-[0_-4px_20px_rgba(0,0,0,0.05)] z-40">
            <button type="submit" class="w-full py-4 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-2xl shadow-lg active:scale-[0.98] transition flex items-center justify-center gap-2">
                <span>Simpan Hasil Wawancara</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </button>
        </div>
    </form>

</body>
</html>