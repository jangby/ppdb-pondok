<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>Soal Asesmen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f0f9ff; }
        .radio-box:checked + div { border-color: #3b82f6; background-color: #eff6ff; color: #2563eb; }
    </style>
</head>
<body class="pb-32">

    {{-- HEADER --}}
    <div class="bg-white px-5 py-4 sticky top-0 z-50 shadow-sm border-b border-gray-100">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="font-bold text-slate-800">Halo, {{ explode(' ', $candidate->nama_lengkap)[0] }}! 👋</h1>
                <p class="text-xs text-slate-500">Jawablah dengan jujur.</p>
            </div>
            <div class="px-2 py-1 bg-blue-50 text-blue-600 text-[10px] font-bold rounded-lg border border-blue-100">
                Santri
            </div>
        </div>
    </div>

    {{-- FORM --}}
    <form action="{{ route('interview.santri.store') }}" method="POST" class="p-4 space-y-6">
        @csrf

        @foreach($questions as $q)
            @php $oldVal = $existingAnswers[$q->id] ?? ''; @endphp

            <div class="bg-white p-5 rounded-2xl shadow-sm border border-blue-50">
                <div class="flex gap-3 mb-4">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-bold shrink-0">
                        {{ $loop->iteration }}
                    </span>
                    <label class="text-sm font-bold text-slate-800 leading-snug">{{ $q->question }}</label>
                </div>

                @if($q->type == 'text')
                    <textarea name="answers[{{ $q->id }}]" rows="3" class="w-full bg-slate-50 border-slate-200 rounded-xl text-sm p-3 focus:ring-blue-500 focus:border-blue-500" placeholder="Ketik jawabanmu...">{{ $oldVal }}</textarea>
                
                @elseif($q->type == 'scale')
                    <div class="flex gap-2 overflow-x-auto pb-2">
                        @for($i=1; $i<=10; $i++)
                            <label class="flex-shrink-0 cursor-pointer">
                                <input type="radio" name="answers[{{ $q->id }}]" value="{{ $i }}" class="radio-box sr-only" {{ $oldVal == $i ? 'checked' : '' }}>
                                <div class="w-10 h-10 rounded-lg border border-slate-200 flex items-center justify-center font-bold text-slate-500 hover:bg-blue-50 transition">
                                    {{ $i }}
                                </div>
                            </label>
                        @endfor
                    </div>
                
                @elseif($q->type == 'choice')
                    <div class="space-y-2">
                        @foreach($q->options as $opt)
                            <label class="block cursor-pointer">
                                <input type="radio" name="answers[{{ $q->id }}]" value="{{ $opt }}" class="radio-box sr-only" {{ $oldVal == $opt ? 'checked' : '' }}>
                                <div class="p-3 pl-4 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 transition flex items-center gap-2">
                                    <div class="w-4 h-4 rounded-full border border-slate-300"></div>
                                    {{ $opt }}
                                </div>
                            </label>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach

        {{-- SUBMIT --}}
        <div class="fixed bottom-0 left-0 w-full p-4 bg-white border-t border-gray-100 z-40">
            <button type="submit" class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-lg active:scale-95 transition">
                Kirim Jawaban
            </button>
        </div>
    </form>

</body>
</html>