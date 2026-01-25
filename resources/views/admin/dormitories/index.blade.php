<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Manajemen Asrama') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- TOMBOL AKSI CEPAT: DISTRIBUSI OTOMATIS --}}
        @php
            $pendingSantri = \App\Models\Candidate::whereNull('dormitory_id')
                ->where(function($q) {
                    $q->where('status_seleksi', 'LIKE', '%Lulus%')
                      ->orWhere('status_seleksi', 'LIKE', '%Diterima%')
                      ->orWhere('status_seleksi', 'LIKE', '%Approved%');
                })->count();
        @endphp

        <div class="col-span-1 lg:col-span-3 mb-8">
            <div class="bg-indigo-50 border border-indigo-100 p-6 rounded-2xl flex flex-col md:flex-row justify-between items-center gap-6 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-indigo-100 text-indigo-600 rounded-xl mt-1">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-xl text-indigo-900">Distribusi Otomatis</h3>
                        <p class="text-sm text-indigo-600 mt-1 leading-relaxed">
                            Fitur ini akan membagikan kamar secara adil (selang-seling) kepada santri yang sudah Lulus tapi belum mendapatkan asrama.
                        </p>
                        @if($pendingSantri > 0)
                            <div class="mt-3 inline-flex items-center gap-2 px-3 py-1 bg-white border border-indigo-200 rounded-full text-xs font-bold text-indigo-700 animate-pulse">
                                <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                                {{ $pendingSantri }} Santri Menunggu Penempatan
                            </div>
                        @else
                            <div class="mt-3 inline-flex items-center gap-2 px-3 py-1 bg-white border border-indigo-200 rounded-full text-xs font-bold text-green-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Semua santri lulus sudah dapat kamar
                            </div>
                        @endif
                    </div>
                </div>

                <form action="{{ route('admin.dormitories.distribute') }}" method="POST">
                    @csrf
                    <button type="submit" 
                        class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-200 transition transform active:scale-95 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                        {{ $pendingSantri == 0 ? 'disabled' : '' }}>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        Bagikan Kamar Sekarang
                    </button>
                </form>
            </div>
        </div>

        {{-- ALERT GLOBAL --}}
        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl shadow-sm flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl shadow-sm flex items-center gap-2">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="font-bold">{{ session('error') }}</span>
            </div>
        @endif

        
        {{-- GRID UTAMA: INPUT DAN DAFTAR --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- KOLOM KIRI: FORM TAMBAH ASRAMA --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 h-fit sticky top-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    </div>
                    <h3 class="font-bold text-lg text-gray-800">Tambah Asrama</h3>
                </div>

                <form action="{{ route('admin.dormitories.store') }}" method="POST" class="space-y-5">
                    @csrf
                    
                    {{-- Nama Asrama --}}
                    <div class="group">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-wide group-focus-within:text-indigo-600 transition">Nama Asrama</label>
                        <input type="text" name="nama_asrama" required placeholder="Contoh: Asrama Al-Fatih Lt. 1" 
                            class="w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500 transition-all font-medium placeholder-gray-400">
                    </div>
                    
                    {{-- Radio Peruntukan --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-wide">Peruntukan Santri</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="cursor-pointer relative">
                                <input type="radio" name="jenis_asrama" value="Putra" class="peer sr-only" checked>
                                <div class="text-center py-3 rounded-xl border border-gray-200 text-gray-500 peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 peer-checked:shadow-md transition-all font-bold text-sm flex flex-col items-center gap-1 hover:bg-gray-50">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    Putra
                                </div>
                            </label>
                            <label class="cursor-pointer relative">
                                <input type="radio" name="jenis_asrama" value="Putri" class="peer sr-only">
                                <div class="text-center py-3 rounded-xl border border-gray-200 text-gray-500 peer-checked:bg-pink-500 peer-checked:text-white peer-checked:border-pink-500 peer-checked:shadow-md transition-all font-bold text-sm flex flex-col items-center gap-1 hover:bg-gray-50">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    Putri
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Kapasitas --}}
                    <div class="group">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-wide group-focus-within:text-indigo-600 transition">Kapasitas / Kuota</label>
                        <div class="relative">
                            <input type="number" name="kapasitas" required placeholder="40" min="1"
                                class="w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500 transition-all font-bold text-lg pl-4">
                            <div class="absolute right-4 top-3 text-sm text-gray-400 font-medium">Orang</div>
                        </div>
                    </div>

                    {{-- Link WA --}}
                    <div class="group">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-wide group-focus-within:text-indigo-600 transition">Link Grup WhatsApp</label>
                        <input type="url" name="link_group_wa" placeholder="https://chat.whatsapp.com/..." 
                            class="w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500 transition-all font-medium text-blue-600 placeholder-gray-400">
                    </div>

                    <button type="submit" class="w-full py-3.5 bg-slate-900 hover:bg-black text-white font-bold rounded-xl transition shadow-lg flex items-center justify-center gap-2 mt-2">
                        <span>Simpan Asrama</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </form>
            </div>

            {{-- KOLOM KANAN: LIST ASRAMA --}}
            <div class="lg:col-span-2 space-y-4">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-bold text-gray-800">Daftar Asrama</h3>
                    <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded-md">Total: {{ $dorms->count() }} Gedung</span>
                </div>

                @forelse($dorms as $dorm)
                    @php
                        $percent = $dorm->kapasitas > 0 ? round(($dorm->candidates_count / $dorm->kapasitas) * 100) : 0;
                        $color = $dorm->jenis_asrama == 'Putra' ? 'blue' : 'pink';
                        $bgColor = $dorm->jenis_asrama == 'Putra' ? 'bg-blue-50' : 'bg-pink-50';
                        $textColor = $dorm->jenis_asrama == 'Putra' ? 'text-blue-600' : 'text-pink-600';
                        $borderColor = $dorm->jenis_asrama == 'Putra' ? 'border-blue-100' : 'border-pink-100';
                        $barColor = $dorm->jenis_asrama == 'Putra' ? 'bg-blue-500' : 'bg-pink-500';
                    @endphp

                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow group relative overflow-hidden">
                        
                        {{-- Hiasan Background --}}
                        <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full {{ $bgColor }} opacity-50 group-hover:scale-150 transition-transform duration-500"></div>

                        <div class="flex flex-col md:flex-row gap-5 items-start relative z-10">
                            {{-- Icon Gedung --}}
                            <div class="w-14 h-14 rounded-2xl {{ $bgColor }} {{ $textColor }} flex items-center justify-center text-xl font-bold shrink-0 border {{ $borderColor }}">
                                {{ substr($dorm->nama_asrama, 0, 1) }}
                            </div>

                            {{-- Info Utama --}}
                            <div class="flex-1 w-full">
                                <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-2 mb-3">
                                    <div>
                                        <h4 class="font-bold text-lg text-gray-800 group-hover:text-indigo-600 transition">{{ $dorm->nama_asrama }}</h4>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $bgColor }} {{ $textColor }} mt-1">
                                            @if($dorm->jenis_asrama == 'Putra')
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                            @else
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                            @endif
                                            Khusus {{ $dorm->jenis_asrama }}
                                        </span>
                                    </div>
                                    
                                    {{-- Actions --}}
                                    <div class="flex items-center gap-2">
                                        @if($dorm->link_group_wa)
                                            <a href="{{ $dorm->link_group_wa }}" target="_blank" class="p-2 bg-green-50 text-green-600 border border-green-100 rounded-lg hover:bg-green-100 hover:scale-105 transition" title="Link Grup WhatsApp">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                            </a>
                                        @endif
                                        <form action="{{ route('admin.dormitories.destroy', $dorm->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus asrama ini? Santri di dalamnya akan kehilangan data kamar.')">
                                            @csrf @method('DELETE')
                                            <button class="p-2 bg-gray-50 text-gray-400 border border-gray-100 rounded-lg hover:bg-red-50 hover:text-red-500 hover:border-red-100 transition" title="Hapus Asrama">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                {{-- Progress & Stats --}}
                                <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                                    <div class="flex justify-between text-xs font-bold text-gray-500 mb-2">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                            Terisi: {{ $dorm->candidates_count }}
                                        </span>
                                        <span>Kapasitas: {{ $dorm->kapasitas }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                        <div class="{{ $barColor }} h-2 rounded-full transition-all duration-1000 shadow-sm" style="width: {{ $percent > 100 ? 100 : $percent }}%"></div>
                                    </div>
                                    @if($percent >= 100)
                                        <p class="text-[10px] text-red-500 font-bold mt-1 text-right">PENUH!</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-16 bg-white rounded-2xl border border-dashed border-gray-300">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <p class="text-gray-500 font-bold">Belum ada asrama.</p>
                        <p class="text-xs text-gray-400 mt-1">Tambahkan asrama baru melalui form di sebelah kiri.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>