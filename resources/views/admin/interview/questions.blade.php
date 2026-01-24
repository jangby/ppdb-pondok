<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-purple-600 rounded-lg shadow-lg text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
            </div>
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Bank Soal Wawancara') }}
                </h2>
                <p class="text-xs text-gray-500">Atur pertanyaan untuk sesi wawancara Wali & profiling Santri.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ activeTab: 'Wali', showModal: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- ALERT --}}
            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-xl shadow-sm flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <p class="text-emerald-700 text-sm font-bold">{{ session('success') }}</p>
                </div>
            @endif

            <div class="flex flex-col md:flex-row gap-6">
                
                {{-- 1. LEFT PANEL: FORM INPUT --}}
                <div class="w-full md:w-1/3">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 sticky top-24">
                        <h3 class="font-bold text-gray-800 text-lg mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Buat Pertanyaan Baru
                        </h3>
                        
                        <form action="{{ route('admin.interview.questions.store') }}" method="POST">
                            @csrf
                            
                            {{-- Target --}}
                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Untuk Siapa?</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="target" value="Wali" class="peer sr-only" checked @click="activeTab = 'Wali'">
                                        <div class="text-center py-2 rounded-lg border border-gray-200 bg-gray-50 text-gray-500 peer-checked:bg-purple-600 peer-checked:text-white peer-checked:border-purple-600 transition text-sm font-bold">
                                            Orang Tua / Wali
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="target" value="Santri" class="peer sr-only" @click="activeTab = 'Santri'">
                                        <div class="text-center py-2 rounded-lg border border-gray-200 bg-gray-50 text-gray-500 peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 transition text-sm font-bold">
                                            Calon Santri
                                        </div>
                                    </label>
                                </div>
                            </div>

                            {{-- Pertanyaan --}}
                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Bunyi Pertanyaan</label>
                                <textarea name="question" rows="3" required class="w-full rounded-xl border-gray-300 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Contoh: Apakah memiliki riwayat penyakit berat?"></textarea>
                            </div>

                            {{-- Tipe Input --}}
                            <div class="mb-4" x-data="{ type: 'text' }">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Tipe Jawaban</label>
                                <select name="type" x-model="type" class="w-full rounded-xl border-gray-300 focus:ring-purple-500 focus:border-purple-500 text-sm cursor-pointer">
                                    <option value="text">Isian Teks (Essay)</option>
                                    <option value="choice">Pilihan Ganda (Ya/Tidak/Lainnya)</option>
                                    <option value="scale">Skala Angka (1-10)</option>
                                </select>

                                {{-- Opsi (Hanya muncul jika Choice) --}}
                                <div x-show="type === 'choice'" class="mt-3 bg-yellow-50 p-3 rounded-lg border border-yellow-200 animate-fade-in-down" style="display: none;">
                                    <label class="block text-[10px] font-bold text-yellow-700 uppercase mb-1">Opsi Jawaban (Pisahkan dengan koma)</label>
                                    <input type="text" name="options" class="w-full rounded-lg border-yellow-300 focus:ring-yellow-500 focus:border-yellow-500 text-sm" placeholder="Ya, Tidak, Ragu-ragu">
                                    <p class="text-[10px] text-yellow-600 mt-1">*Contoh: Sangat Baik, Cukup, Kurang</p>
                                </div>
                            </div>

                            <button type="submit" class="w-full py-3 bg-gray-900 hover:bg-black text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition transform active:scale-95 text-sm">
                                Simpan Pertanyaan
                            </button>
                        </form>
                    </div>
                </div>

                {{-- 2. RIGHT PANEL: LIST PERTANYAAN --}}
                <div class="w-full md:w-2/3">
                    
                    {{-- Tabs Header --}}
                    <div class="flex border-b border-gray-200 mb-6">
                        <button class="px-6 py-3 font-bold text-sm border-b-2 transition"
                            :class="activeTab === 'Wali' ? 'border-purple-600 text-purple-700' : 'border-transparent text-gray-400 hover:text-gray-600'"
                            @click="activeTab = 'Wali'">
                            Soal Wawancara Wali
                        </button>
                        <button class="px-6 py-3 font-bold text-sm border-b-2 transition"
                            :class="activeTab === 'Santri' ? 'border-blue-600 text-blue-700' : 'border-transparent text-gray-400 hover:text-gray-600'"
                            @click="activeTab = 'Santri'">
                            Soal Asesmen Santri
                        </button>
                    </div>

                    {{-- Content Wali --}}
                    <div x-show="activeTab === 'Wali'" class="space-y-3">
                        @forelse($questionsWali as $q)
                            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex justify-between items-start group hover:border-purple-200 transition">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="bg-gray-100 text-gray-600 text-[10px] font-bold px-2 py-0.5 rounded uppercase">{{ $q->type }}</span>
                                        @if($q->type == 'choice')
                                            <span class="text-[10px] text-gray-400">Options: {{ implode(', ', $q->options ?? []) }}</span>
                                        @endif
                                    </div>
                                    <p class="font-bold text-gray-800">{{ $q->question }}</p>
                                </div>
                                <form action="{{ route('admin.interview.questions.destroy', $q->id) }}" method="POST" onsubmit="return confirm('Hapus soal ini?')">
                                    @csrf @method('DELETE')
                                    <button class="text-gray-300 hover:text-red-500 p-2 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        @empty
                            <div class="text-center py-10 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                                <p class="text-gray-400 text-sm">Belum ada pertanyaan untuk Wali.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- Content Santri --}}
                    <div x-show="activeTab === 'Santri'" class="space-y-3" style="display: none;">
                        @forelse($questionsSantri as $q)
                            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex justify-between items-start group hover:border-blue-200 transition">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="bg-gray-100 text-gray-600 text-[10px] font-bold px-2 py-0.5 rounded uppercase">{{ $q->type }}</span>
                                        @if($q->type == 'choice')
                                            <span class="text-[10px] text-gray-400">Options: {{ implode(', ', $q->options ?? []) }}</span>
                                        @endif
                                    </div>
                                    <p class="font-bold text-gray-800">{{ $q->question }}</p>
                                </div>
                                <form action="{{ route('admin.interview.questions.destroy', $q->id) }}" method="POST" onsubmit="return confirm('Hapus soal ini?')">
                                    @csrf @method('DELETE')
                                    <button class="text-gray-300 hover:text-red-500 p-2 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        @empty
                            <div class="text-center py-10 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                                <p class="text-gray-400 text-sm">Belum ada pertanyaan untuk Santri.</p>
                            </div>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>