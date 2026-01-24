<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.interview.dashboard') }}" class="p-2 bg-gray-100 rounded-full hover:bg-gray-200 transition text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                Detail Hasil Seleksi
            </h2>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- 1. PROFIL SINGKAT & TOMBOL AKSI --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="flex items-center gap-5">
                {{-- Avatar Inisial --}}
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-2xl font-bold text-white shadow-lg shadow-blue-500/30">
                    {{ substr($candidate->nama_lengkap, 0, 1) }}
                </div>
                <div>
                    <h3 class="font-bold text-2xl text-gray-800">{{ $candidate->nama_lengkap }}</h3>
                    <div class="flex flex-wrap items-center gap-2 mt-1 text-sm text-gray-500">
                        <span class="bg-gray-100 px-2 py-0.5 rounded font-bold text-gray-600">{{ $candidate->no_daftar }}</span>
                        <span>•</span>
                        <span class="font-medium">{{ $candidate->jenjang }}</span>
                        <span>•</span>
                        <span class="truncate max-w-[150px]">{{ $candidate->asal_sekolah ?? '-' }}</span>
                    </div>
                </div>
            </div>
            
            <div class="flex gap-3">
                {{-- Tombol Cetak (Membuka Tab Baru ke Route Print) --}}
                <a href="{{ route('admin.interview.result.print', $candidate->id) }}" target="_blank" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Dokumen (PDF)
                </a>

                {{-- Tombol Lihat Profil Database --}}
                <a href="{{ route('admin.candidates.show', $candidate->id) }}" class="px-5 py-2.5 bg-slate-900 text-white font-bold rounded-xl hover:bg-slate-800 transition text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Lihat Profil
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            {{-- 2. KOLOM KIRI: JAWABAN SANTRI --}}
            <div class="bg-white rounded-2xl shadow-sm border border-emerald-100 overflow-hidden h-fit">
                <div class="px-6 py-4 bg-emerald-50 border-b border-emerald-100 flex items-center gap-3">
                    <div class="p-1.5 bg-emerald-200 rounded text-emerald-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-emerald-800">Asesmen Mandiri Santri</h3>
                        <p class="text-[10px] text-emerald-600 font-bold">Diisi oleh santri via scan QR</p>
                    </div>
                </div>
                <div class="p-6 space-y-6">
                    @forelse($santriAnswers as $ans)
                        @php
                            // SMART FLAGGING (SANTRI)
                            $text = strtolower($ans->answer);
                            
                            // Kata kunci BAHAYA (Merah)
                            $dangerKeywords = ['merokok', 'narkoba', 'mencuri', 'bertato', 'kabur', 'tawuran', 'minum keras'];
                            
                            // Kata kunci PERINGATAN (Kuning)
                            $warningKeywords = ['sakit', 'asma', 'maag', 'alergi', 'patah', 'sesak', 'sering pusing', 'tidak suka', 'dipaksa'];

                            $isDanger = \Illuminate\Support\Str::contains($text, $dangerKeywords);
                            $isWarning = \Illuminate\Support\Str::contains($text, $warningKeywords);
                        @endphp

                        <div class="border-b border-gray-50 last:border-0 pb-4 last:pb-0 group hover:bg-gray-50/50 p-2 rounded transition">
                            <p class="text-[10px] font-bold text-gray-400 uppercase mb-1 tracking-wider">Pertanyaan {{ $loop->iteration }}</p>
                            <p class="font-bold text-gray-800 text-sm mb-2 leading-relaxed">{{ $ans->question->question }}</p>
                            
                            <div class="p-3 rounded-lg text-sm border flex items-start gap-2
                                {{ $isDanger ? 'bg-red-50 border-red-200 text-red-800' : 
                                  ($isWarning ? 'bg-amber-50 border-amber-200 text-amber-800' : 'bg-slate-50 border-slate-100 text-slate-700') }}">
                                
                                {{-- Icon Alert --}}
                                @if($isDanger)
                                    <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                @elseif($isWarning)
                                    <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @endif

                                <span class="font-medium">{{ $ans->answer }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <div class="inline-block p-3 bg-gray-50 rounded-full text-gray-300 mb-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            </div>
                            <p class="text-sm text-gray-400">Belum ada data asesmen santri.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- 3. KOLOM KANAN: JAWABAN WALI --}}
            <div class="bg-white rounded-2xl shadow-sm border border-purple-100 overflow-hidden h-fit">
                <div class="px-6 py-4 bg-purple-50 border-b border-purple-100 flex items-center gap-3">
                    <div class="p-1.5 bg-purple-200 rounded text-purple-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-purple-800">Wawancara Wali Santri</h3>
                        <p class="text-[10px] text-purple-600 font-bold">Diisi oleh panitia saat wawancara</p>
                    </div>
                </div>
                <div class="p-6 space-y-6">
                    @forelse($waliAnswers as $ans)
                        @php
                            // SMART FLAGGING (WALI)
                            $text = strtolower($ans->answer);
                            
                            // Kata kunci BAHAYA (Merah)
                            $dangerKeywords = ['tidak sanggup', 'keberatan', 'hutang', 'menolak', 'tidak setuju', 'biaya mahal'];
                            
                            // Kata kunci PERINGATAN (Kuning)
                            $warningKeywords = ['ragu', 'cicil', 'kurang', 'usahakan', 'belum punya', 'nanti dulu'];

                            $isDanger = \Illuminate\Support\Str::contains($text, $dangerKeywords);
                            $isWarning = \Illuminate\Support\Str::contains($text, $warningKeywords);
                        @endphp

                        <div class="border-b border-gray-50 last:border-0 pb-4 last:pb-0 group hover:bg-gray-50/50 p-2 rounded transition">
                            <p class="text-[10px] font-bold text-gray-400 uppercase mb-1 tracking-wider">Pertanyaan {{ $loop->iteration }}</p>
                            <p class="font-bold text-gray-800 text-sm mb-2 leading-relaxed">{{ $ans->question->question }}</p>
                            
                            <div class="p-3 rounded-lg text-sm border flex items-start gap-2
                                {{ $isDanger ? 'bg-red-50 border-red-200 text-red-800' : 
                                  ($isWarning ? 'bg-amber-50 border-amber-200 text-amber-800' : 'bg-slate-50 border-slate-100 text-slate-700') }}">
                                
                                @if($isDanger)
                                    <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                @elseif($isWarning)
                                    <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @endif

                                <span class="font-medium">{{ $ans->answer }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <div class="inline-block p-3 bg-gray-50 rounded-full text-gray-300 mb-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            </div>
                            <p class="text-sm text-gray-400">Belum ada data wawancara wali.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>
</x-app-layout>