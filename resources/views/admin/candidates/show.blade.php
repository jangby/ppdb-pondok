<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manajemen Data Santri') }}
            </h2>
            <div class="flex items-center gap-4">
                {{-- TOMBOL CONNECT PRINTER THERMAL --}}
                <button id="connectBtn" class="bg-slate-800 text-white text-xs px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-slate-700 transition shadow-sm border border-slate-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span id="printerStatus">Hubungkan Printer (Cetak Struk)</span>
                </button>

                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('admin.candidates.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900">
                                Data Santri
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Detail Profile</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

        @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Berhasil!</strong>
                    <span class="block sm:inline">{!! session('success') !!}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Gagal!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            {{-- 1. HERO PROFILE CARD --}}
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden relative">
                {{-- Background Decoration --}}
                <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-50 rounded-full -mr-16 -mt-16 z-0"></div>
                
                <div class="relative z-10 p-8 flex flex-col md:flex-row gap-8 items-start">
                    {{-- Avatar / Initials --}}
                    <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-3xl font-black shadow-lg shadow-indigo-200">
                        {{ substr($candidate->nama_lengkap, 0, 1) }}
                    </div>

                    {{-- Info Utama --}}
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide bg-indigo-100 text-indigo-700 border border-indigo-200">
                                {{ $candidate->jenjang }}
                            </span>
                            <span class="text-sm text-gray-400 font-mono font-semibold tracking-wider">#{{ $candidate->no_daftar }}</span>
                            @if($candidate->status_seleksi == 'Diterima')
                                <span class="flex items-center gap-1 text-xs font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded-full border border-green-100">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    LULUS SELEKSI
                                </span>
                            @endif
                        </div>
                        
                        <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight leading-tight mb-2">
                            {{ $candidate->nama_lengkap }}
                        </h1>
                        
                        <div class="flex flex-wrap gap-4 text-sm text-gray-500">
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Daftar: {{ $candidate->created_at->translatedFormat('d F Y') }}
                            </div>
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                {{ $candidate->address->kabupaten ?? 'Alamat Belum Lengkap' }}
                            </div>
                            
                            {{-- Info Ruangan Tes --}}
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                <span class="font-bold text-gray-700">R. Santri: {{ $candidate->santri_room->nama_ruangan ?? '-' }} | R. Wali: {{ $candidate->wali_room->nama_ruangan ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-col gap-2 min-w-[140px]">
                        <a href="{{ route('admin.candidates.edit', $candidate->id) }}" class="flex items-center justify-center gap-2 px-4 py-2.5 bg-white border-2 border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 hover:border-gray-300 transition shadow-sm">
                            Edit Data
                        </a>
                        <form action="{{ route('admin.candidates.destroy', $candidate->id) }}" method="POST" onsubmit="return confirm('Hapus permanen? Data tidak bisa dikembalikan.');">
                            @csrf @method('DELETE')
                            <button class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-red-50 text-red-600 font-bold rounded-xl hover:bg-red-100 transition border border-red-100">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- 2. FINANCIAL STATS (DASHBOARD STYLE) --}}
            @php
                $totalTagihan = $candidate->bills->sum('nominal_tagihan');
                $totalTerbayar = $candidate->bills->sum('nominal_terbayar');
                $sisaTagihan = $totalTagihan - $totalTerbayar;
                $persen = $totalTagihan > 0 ? ($totalTerbayar / $totalTagihan) * 100 : 0;
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition">
                    <div class="absolute right-0 top-0 w-24 h-24 bg-blue-50 rounded-bl-full -mr-4 -mt-4 opacity-50 group-hover:scale-110 transition"></div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Total Tagihan</p>
                    <p class="text-2xl font-black text-gray-800">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</p>
                    <div class="mt-4 h-1 w-full bg-gray-100 rounded-full"></div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition">
                    <div class="absolute right-0 top-0 w-24 h-24 bg-green-50 rounded-bl-full -mr-4 -mt-4 opacity-50 group-hover:scale-110 transition"></div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Sudah Dibayar</p>
                    <p class="text-2xl font-black text-green-600">Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</p>
                    <div class="mt-4 flex items-center gap-2">
                        <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-green-500 rounded-full" style="width: {{ $persen }}%"></div>
                        </div>
                        <span class="text-[10px] font-bold text-green-600">{{ round($persen) }}%</span>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition">
                    <div class="absolute right-0 top-0 w-24 h-24 bg-red-50 rounded-bl-full -mr-4 -mt-4 opacity-50 group-hover:scale-110 transition"></div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Sisa Kewajiban</p>
                    <p class="text-2xl font-black text-red-600">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</p>
                    <div class="mt-4 text-xs font-medium text-red-400">
                        {{ $sisaTagihan > 0 ? 'Segera lunasi pembayaran' : 'Lunas sepenuhnya' }}
                    </div>
                </div>
            </div>

            {{-- 3. MAIN CONTENT GRID --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                
                {{-- LEFT COLUMN (SIDEBAR) --}}
                <div class="space-y-8">
                    
                    {{-- [PENTING] CARD FILE PERJANJIAN --}}
                    <div class="bg-white rounded-2xl shadow-md border border-indigo-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4 flex items-center gap-3">
                            <div class="p-2 bg-white/20 rounded-lg text-white">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <h3 class="font-bold text-white text-lg">Dokumen Penting</h3>
                        </div>
                        
                        <div class="p-6">
                            @if($candidate->file_perjanjian)
                                {{-- JIKA ADA FILE --}}
                                <div class="flex items-center gap-4 bg-gray-50 p-4 rounded-xl border border-gray-200 mb-4">
                                    <img src="https://cdn-icons-png.flaticon.com/512/337/337946.png" class="w-10 h-10 opacity-80" alt="PDF Icon">
                                    <div class="overflow-hidden">
                                        <p class="text-sm font-bold text-gray-900 truncate">Surat_Perjanjian.pdf</p>
                                        <p class="text-[10px] text-gray-500">{{ $candidate->created_at->format('d M Y') }}</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <a href="{{ asset('storage/' . $candidate->file_perjanjian) }}" target="_blank" class="flex items-center justify-center gap-2 px-4 py-2 bg-gray-800 text-white text-xs font-bold rounded-lg hover:bg-gray-900 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        Preview
                                    </a>
                                    <a href="{{ asset('storage/' . $candidate->file_perjanjian) }}" download class="flex items-center justify-center gap-2 px-4 py-2 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-lg hover:bg-indigo-100 transition border border-indigo-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        Download
                                    </a>
                                </div>
                            @else
                                {{-- JIKA TIDAK ADA FILE - FORM UPLOAD --}}
                                <form action="{{ route('admin.candidates.upload_perjanjian', $candidate->id) }}" method="POST" enctype="multipart/form-data" class="text-center py-6 bg-gray-50 rounded-xl border border-dashed border-indigo-300 hover:bg-indigo-50/50 transition">
                                    @csrf
                                    <svg class="w-10 h-10 text-indigo-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                    <p class="text-sm font-bold text-gray-600 mb-1">Berkas tidak tersedia</p>
                                    <p class="text-xs text-gray-500 mb-4">Upload Surat Perjanjian (PDF, Max 2MB)</p>
                                    
                                    <div class="flex flex-col items-center justify-center gap-3 px-4">
                                        <input type="file" name="file_perjanjian" accept=".pdf" required 
                                            class="block w-full text-xs text-gray-500 
                                            file:mr-4 file:py-2 file:px-4 
                                            file:rounded-full file:border-0 
                                            file:text-xs file:font-bold 
                                            file:bg-indigo-100 file:text-indigo-700 
                                            hover:file:bg-indigo-200 cursor-pointer">
                                        
                                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 transition shadow-sm w-full sm:w-auto flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                            Simpan Dokumen
                                        </button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>

                    {{-- CARD STATUS SELEKSI --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6">
                            <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Status Seleksi</h3>
                            
                            <form action="{{ route('admin.candidates.updateStatus', $candidate->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <div class="mb-4">
                                    <select name="status_seleksi" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-medium">
                                        <option value="Pending" {{ $candidate->status_seleksi == 'Pending' ? 'selected' : '' }}>⏳ Pending Review</option>
                                        <option value="Diterima" {{ $candidate->status_seleksi == 'Diterima' ? 'selected' : '' }}>✅ Diterima (Lulus)</option>
                                        <option value="Ditolak" {{ $candidate->status_seleksi == 'Ditolak' ? 'selected' : '' }}>❌ Ditolak / Gagal</option>
                                        <option value="Cadangan" {{ $candidate->status_seleksi == 'Cadangan' ? 'selected' : '' }}>⚠️ Cadangan</option>
                                    </select>
                                </div>
                                <button type="submit" class="w-full bg-gray-900 text-white px-4 py-2.5 rounded-xl font-bold text-sm hover:bg-black transition shadow-lg">
                                    Simpan Perubahan
                                </button>
                            </form>

                            <div class="mt-4 pt-4 border-t border-dashed border-gray-200">
                                @if($candidate->status_seleksi == 'Diterima')
                                    <a href="{{ route('admin.candidates.print', $candidate->id) }}" target="_blank" class="flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-green-500 text-white rounded-xl font-bold text-sm hover:bg-green-600 transition shadow-md shadow-green-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        Cetak Kartu Lulus
                                    </a>
                                @else
                                    <button disabled class="w-full px-4 py-2.5 bg-gray-100 text-gray-400 rounded-xl font-bold text-sm cursor-not-allowed">
                                        Cetak Kartu (Dikunci)
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- CARD KONTAK ORTU --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Kontak Cepat</h3>
                        <div class="space-y-4">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 uppercase font-bold">Ayah</p>
                                    
                                    {{-- PERBAIKAN: Gunakan optional() agar tidak error jika data ortu kosong --}}
                                    <p class="font-semibold text-gray-800">
                                        {{ optional($candidate->parent)->nama_ayah ?? 'Belum diisi' }}
                                    </p>

                                    @if(optional($candidate->parent)->no_hp_ayah)
                                        <a href="https://wa.me/{{ $candidate->parent->no_hp_ayah }}" target="_blank" class="text-xs text-green-600 font-bold hover:underline flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                            {{ $candidate->parent->no_hp_ayah }}
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-400 italic">No HP tidak tersedia</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT COLUMN (MAIN) --}}
                <div class="lg:col-span-2 space-y-8">
                    
                    {{-- 1. KASIR PEMBAYARAN (Modern Dark Theme Header) --}}
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="bg-gray-900 px-6 py-5 flex justify-between items-center">
        <h3 class="font-bold text-white flex items-center gap-2 text-lg">
            <span class="p-1 bg-gray-700 rounded text-green-400">
                {{-- Icon Uang --}}
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </span>
            Kasir Pembayaran
        </h3>
        
        <div class="flex items-center gap-2">
            {{-- [TOMBOL BARU] KIRIM NOTIFIKASI --}}
            @if($sisaTagihan > 0)
            <form action="{{ route('admin.candidates.notify_bill', $candidate->id) }}" method="POST" onsubmit="return confirm('Kirim pengingat tagihan ke WA Orang Tua?');">
                @csrf
                <button type="submit" class="text-[10px] font-bold text-white bg-green-600 hover:bg-green-500 px-3 py-1.5 rounded-full border border-green-500 flex items-center gap-1 transition shadow-lg shadow-green-900/50">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                    Kirim Tagihan WA
                </button>
            </form>
            @endif
            
            <span class="text-[10px] font-bold text-gray-300 bg-gray-800 px-3 py-1 rounded-full border border-gray-700">MODE INPUT MANUAL</span>
        </div>
    </div>
                        
                        <div class="p-6">
                            <form action="{{ route('admin.transactions.store', $candidate->id) }}" method="POST">
                                @csrf
                                <div class="overflow-hidden rounded-xl border border-gray-200 mb-6">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase">Item Tagihan</th>
                                                <th class="px-5 py-4 text-right text-xs font-bold text-gray-500 uppercase">Tagihan</th>
                                                <th class="px-5 py-4 text-right text-xs font-bold text-gray-500 uppercase">Sisa Hutang</th>
                                                <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase w-56">Nominal Bayar</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-100">
                                            @foreach($candidate->bills as $bill)
                                            <tr class="hover:bg-gray-50 transition {{ $bill->sisa_tagihan == 0 ? 'bg-green-50/50' : '' }}">
                                                <td class="px-5 py-4 text-sm font-medium text-gray-900">
                                                    {{ $bill->payment_type->nama_pembayaran }}
                                                    @if($bill->sisa_tagihan == 0)
                                                        <span class="ml-2 text-[10px] px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-bold border border-green-200">LUNAS</span>
                                                    @endif

                                                    {{-- FITUR REKONSTRUKSI TAGIHAN: Muncul jika tarif master berubah dan santri sudah pernah mencicil/bayar --}}
    @if($bill->nominal_tagihan != $bill->payment_type->nominal && $bill->nominal_terbayar > 0)
        <div class="mt-3 p-2.5 bg-yellow-50 border border-yellow-200 rounded-lg text-xs shadow-sm block w-full">
            <div class="text-yellow-800 mb-2 leading-relaxed">
                <strong class="block text-yellow-900">⚠️ Tarif Berubah!</strong>
                Harga dasar saat ini: <span class="font-bold">Rp {{ number_format($bill->payment_type->nominal, 0, ',', '.') }}</span>
            </div>
            
            {{-- PERBAIKAN: Tanpa tag form, gunakan formaction langsung di dalam button --}}
            <button type="submit" 
                    formaction="{{ route('admin.bills.reconstruct', $bill->id) }}"
                    onclick="return confirm('Yakin ingin merekonstruksi tagihan ini? Status Lunas/Cicilan dan Sisa Hutang akan dihitung ulang secara otomatis.');" 
                    class="w-full bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1.5 rounded font-bold transition flex items-center justify-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                Sesuaikan Tagihan
            </button>
        </div>
    @endif

    {{-- FITUR RESET ANOMALI: Muncul jika ada sisa tagihan aneh (misal 1-100 perak) --}}
    @if($bill->sisa_tagihan > 0 && $bill->sisa_tagihan <= 100)
        <div class="mt-2 block w-full">
            <button type="submit" 
                    formaction="{{ route('admin.bills.fix_anomaly', $bill->id) }}"
                    onclick="return confirm('Sisa tagihan anomali: Rp {{ $bill->sisa_tagihan }}.\n\nYakin ingin MERESET tagihan ini kembali menjadi Belum Bayar (Rp 0)?\nSetelah direset, Anda bisa menginputkan ulang pembayarannya dengan benar agar tercatat di Riwayat Transaksi.');" 
                    class="w-full bg-orange-500 hover:bg-orange-600 text-white px-2 py-1.5 rounded-lg font-bold transition flex items-center justify-center gap-1 text-xs shadow-sm border border-orange-600">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                Reset ke Belum Bayar
            </button>
        </div>
    @endif
                                                </td>
                                                <td class="px-5 py-4 text-sm text-gray-500 text-right">
                                                    {{ number_format($bill->nominal_tagihan, 0, ',', '.') }}
                                                </td>
                                                <td class="px-5 py-4 text-sm font-bold text-right {{ $bill->sisa_tagihan > 0 ? 'text-red-600' : 'text-green-600' }}">
                                                    {{ number_format($bill->sisa_tagihan, 0, ',', '.') }}
                                                </td>
                                                <td class="px-5 py-4">
                                                    @if($bill->sisa_tagihan > 0)
                                                        <div class="relative rounded-lg shadow-sm">
                                                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                                <span class="text-gray-500 sm:text-xs font-bold">Rp</span>
                                                            </div>
                                                            <input type="number" 
                                                                   name="payments[{{ $bill->id }}]" 
                                                                   class="block w-full rounded-lg border-gray-300 pl-10 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2" 
                                                                   placeholder="0"
                                                                   min="0"
                                                                   max="{{ $bill->sisa_tagihan }}">
                                                        </div>
                                                    @else
                                                        <div class="text-center text-xs text-green-600 font-bold flex items-center justify-center gap-1">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                            Selesai
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="flex flex-col sm:flex-row justify-between items-center bg-indigo-50 p-4 rounded-xl border border-indigo-100 gap-4">
                                    <div class="flex items-center gap-3 text-sm text-indigo-900">
                                        <div class="p-2 bg-indigo-100 rounded-lg text-indigo-600">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <div>
                                            <span class="font-bold block">Konfirmasi Pembayaran</span>
                                            <span class="text-xs opacity-80">Pastikan uang fisik diterima sebelum klik proses.</span>
                                        </div>
                                    </div>
                                    <button type="submit" class="w-full sm:w-auto bg-indigo-600 text-white px-8 py-3 rounded-xl font-bold text-sm hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">
                                        Proses Pembayaran
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- 2. RIWAYAT TRANSAKSI --}}
                    @if($candidate->transactions->count() > 0)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                            <h3 class="font-bold text-gray-800">Riwayat Transaksi</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            @foreach($candidate->transactions as $trx)
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-5 border rounded-xl hover:bg-gray-50 transition border-gray-200 bg-white shadow-sm">
                                <div>
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="font-mono font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded text-xs border border-indigo-100">{{ $trx->kode_transaksi }}</span>
                                        <span class="text-xs text-gray-500 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            {{ $trx->created_at->format('d M Y, H:i') }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-600 flex flex-wrap gap-2">
                                        @foreach($trx->details as $detail)
                                            <span class="inline-flex items-center gap-1 bg-gray-100 px-2 py-1 rounded border border-gray-200">
                                                <span>{{ $detail->bill->payment_type->nama_pembayaran }}</span>
                                                <span class="font-bold text-gray-800">Rp {{ number_format($detail->nominal) }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                    <div class="mt-2 text-[10px] text-gray-400">
                                        Petugas: {{ $trx->admin->name ?? 'System' }}
                                    </div>
                                </div>
                                <div class="text-right mt-3 sm:mt-0 flex flex-col items-end gap-2">
                                    <div class="font-black text-lg text-gray-800">
                                        Rp {{ number_format($trx->total_bayar, 0, ',', '.') }}
                                    </div>
                                    
                                    {{-- TOMBOL CETAK & AKSI --}}
<div class="flex flex-wrap items-center justify-end gap-2 mt-2">
    
    {{-- 1. Cetak Bluetooth (JS) --}}
    <button onclick="printReceipt({{ $trx->id }})" class="inline-flex items-center justify-center gap-1.5 text-xs font-bold text-blue-700 bg-blue-50 px-3 py-2 rounded-lg hover:bg-blue-100 transition border border-blue-200 shadow-sm" title="Cetak Struk Thermal">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
        Struk
    </button>
    
    {{-- 2. Cetak PDF (Backup) --}}
    <a href="{{ route('admin.transactions.print', $trx->id) }}" target="_blank" class="inline-flex items-center justify-center gap-1.5 text-xs font-bold text-gray-700 bg-gray-50 px-3 py-2 rounded-lg hover:bg-gray-100 transition border border-gray-200 shadow-sm" title="Print Biasa (PDF A4)">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
        PDF
    </a>

    {{-- 3. Copy WA --}}
    <button type="button" onclick="copyDetailToWA(this)" class="inline-flex items-center justify-center gap-1.5 text-xs font-bold text-green-700 bg-green-50 px-3 py-2 rounded-lg hover:bg-green-100 transition border border-green-200 shadow-sm" title="Copy Struk untuk WhatsApp">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
        <span class="btn-text">Copy WA</span>
    </button>

    {{-- 4. TOMBOL BATAL/HAPUS TRANSAKSI --}}
    <form action="{{ route('admin.transactions.destroy', $trx->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin MEMBATALKAN transaksi ini?\n\nSaldo tagihan santri akan dikembalikan seperti sebelum transaksi ini terjadi.');" class="m-0 p-0 inline-flex">
        @csrf
        @method('DELETE')
        <button type="submit" class="inline-flex items-center justify-center gap-1.5 text-xs font-bold text-red-700 bg-red-50 px-3 py-2 rounded-lg hover:bg-red-100 transition border border-red-200 shadow-sm" title="Batalkan Transaksi Ini">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            <span class="btn-text">Batal</span>
        </button>
    </form>
</div>

                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- 3. BIODATA DETAIL --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                            <h3 class="font-bold text-gray-800">Biodata Lengkap</h3>
                        </div>
                        <div class="p-6">
                            <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6 text-sm">
                                <div class="border-b border-gray-100 pb-2">
                                    <dt class="text-xs font-bold text-gray-400 uppercase mb-1">NISN</dt>
                                    <dd class="text-gray-900 font-bold text-base">{{ $candidate->nisn ?? '-' }}</dd>
                                </div>
                                <div class="border-b border-gray-100 pb-2">
                                    <dt class="text-xs font-bold text-gray-400 uppercase mb-1">NIK</dt>
                                    <dd class="text-gray-900 font-bold text-base">{{ $candidate->nik ?? '-' }}</dd>
                                </div>
                                <div class="border-b border-gray-100 pb-2">
                                    <dt class="text-xs font-bold text-gray-400 uppercase mb-1">TTL</dt>
                                    <dd class="text-gray-900 font-medium">
                                        {{ $candidate->tempat_lahir }}, {{ \Carbon\Carbon::parse($candidate->tanggal_lahir)->translatedFormat('d F Y') }}
                                    </dd>
                                </div>
                                <div class="border-b border-gray-100 pb-2">
                                    <dt class="text-xs font-bold text-gray-400 uppercase mb-1">Gender</dt>
                                    <dd class="text-gray-900 font-medium">{{ $candidate->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</dd>
                                </div>
                                <div class="border-b border-gray-100 pb-2">
                                    <dt class="text-xs font-bold text-gray-400 uppercase mb-1">Asal Sekolah</dt>
                                    <dd class="text-gray-900 font-medium">{{ $candidate->asal_sekolah }}</dd>
                                </div>
                                <div class="border-b border-gray-100 pb-2">
                                    <dt class="text-xs font-bold text-gray-400 uppercase mb-1">Riwayat Penyakit</dt>
                                    <dd class="text-gray-900 font-medium">{{ $candidate->riwayat_penyakit ?? '-' }}</dd>
                                </div>
                                <div class="col-span-1 md:col-span-2">
                                    <dt class="text-xs font-bold text-gray-400 uppercase mb-1">Alamat Lengkap</dt>
                                    <dd class="text-gray-700 bg-gray-50 p-4 rounded-xl border border-gray-200 leading-relaxed">
                                        {{ $candidate->address->alamat ?? '' }}<br>
                                        <span class="text-xs text-gray-500 block mt-1">
                                            RT {{ $candidate->address->rt ?? '-' }} / RW {{ $candidate->address->rw ?? '-' }},
                                            Desa {{ $candidate->address->desa ?? '-' }}, Kec. {{ $candidate->address->kecamatan ?? '-' }},
                                            {{ $candidate->address->kabupaten ?? '-' }}, {{ $candidate->address->provinsi ?? '-' }} {{ $candidate->address->kode_pos ?? '' }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- TEMPLATE TERSEMBUNYI UNTUK GAMBAR WA --}}
    <div id="waImageTemplate" style="position: absolute; left: -9999px; top: 0; background: white; padding: 20px; width: 600px; color: black; font-family: sans-serif; box-sizing: border-box;">
        <div style="text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 15px;">
            <h2 style="margin: 0; font-size: 18px; font-weight: bold; text-transform: uppercase;">Rincian Pembayaran Santri</h2>
            <p style="margin: 5px 0 0 0; font-size: 14px;">Tanggal Update: {{ date('d M Y, H:i') }}</p>
        </div>
        
        <table style="width: 100%; margin-bottom: 15px; font-size: 14px;">
            <tr>
                <td style="width: 100px; font-weight: bold; padding: 2px 0;">Nama Santri</td>
                <td style="padding: 2px 0;">: {{ $candidate->nama_lengkap }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; padding: 2px 0;">No. Daftar</td>
                <td style="padding: 2px 0;">: {{ $candidate->no_daftar }}</td>
            </tr>
        </table>

        <table style="width: 100%; border-collapse: collapse; font-size: 12px; margin-bottom: 15px;">
            <thead>
                <tr style="background-color: #f3f4f6;">
                    <th style="border: 1px solid #d1d5db; padding: 8px; text-align: left;">Item Pembayaran</th>
                    <th style="border: 1px solid #d1d5db; padding: 8px; text-align: right;">Tagihan</th>
                    <th style="border: 1px solid #d1d5db; padding: 8px; text-align: right;">Terbayar</th>
                    <th style="border: 1px solid #d1d5db; padding: 8px; text-align: right;">Sisa</th>
                </tr>
            </thead>
            <tbody>
                @foreach($candidate->bills as $bill)
                <tr>
                    <td style="border: 1px solid #d1d5db; padding: 6px 8px;">
                        {{ $bill->payment_type->nama_pembayaran }}
                        @if($bill->sisa_tagihan == 0) <span style="color: green; font-weight: bold; font-size: 10px;">(LUNAS)</span> @endif
                    </td>
                    <td style="border: 1px solid #d1d5db; padding: 6px 8px; text-align: right;">Rp {{ number_format($bill->nominal_tagihan, 0, ',', '.') }}</td>
                    <td style="border: 1px solid #d1d5db; padding: 6px 8px; text-align: right;">Rp {{ number_format($bill->nominal_terbayar, 0, ',', '.') }}</td>
                    <td style="border: 1px solid #d1d5db; padding: 6px 8px; text-align: right; color: {{ $bill->sisa_tagihan > 0 ? 'red' : 'black' }}; font-weight: {{ $bill->sisa_tagihan > 0 ? 'bold' : 'normal' }};">
                        Rp {{ number_format($bill->sisa_tagihan, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="font-weight: bold; background-color: #e5e7eb;">
                    <td style="border: 1px solid #d1d5db; padding: 8px; text-align: right;">TOTAL:</td>
                    <td style="border: 1px solid #d1d5db; padding: 8px; text-align: right;">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</td>
                    <td style="border: 1px solid #d1d5db; padding: 8px; text-align: right; color: green;">Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</td>
                    <td style="border: 1px solid #d1d5db; padding: 8px; text-align: right; color: red;">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
        
        <div style="text-align: center; font-size: 11px; color: #6b7280; font-style: italic;">
            Simpan bukti rincian ini. Terima kasih telah menyelesaikan administrasi.
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    
    <script>
        async function copyDetailToWA(btnElement) {
            const originalText = btnElement.querySelector('.btn-text').innerText;
            btnElement.querySelector('.btn-text').innerText = "Loading...";
            btnElement.disabled = true;

            const elementToCapture = document.getElementById('waImageTemplate');

            try {
                // Proses render HTML ke Canvas
                const canvas = await html2canvas(elementToCapture, { 
                    scale: 2, // Resolusi tinggi agar tidak pecah di WA
                    backgroundColor: "#ffffff"
                });

                // Ubah ke Blob (File Gambar)
                canvas.toBlob(async function(blob) {
                    try {
                        // Tulis ke Clipboard (Memori Copy-Paste)
                        const item = new ClipboardItem({ "image/png": blob });
                        await navigator.clipboard.write([item]);
                        
                        // Notifikasi sukses
                        btnElement.querySelector('.btn-text').innerText = "Tersalin! ✔️";
                        btnElement.classList.replace('bg-green-50', 'bg-green-600');
                        btnElement.classList.replace('text-green-600', 'text-white');
                        
                        alert("✅ Rincian berhasil disalin!\nSilakan buka WhatsApp wali santri, lalu tekan Ctrl+V (Paste) pada kolom chat.");
                    } catch (err) {
                        alert("⚠️ Browser Anda tidak mendukung fitur copy gambar otomatis. Gunakan browser versi terbaru (Chrome/Edge).");
                        console.error(err);
                    }
                });
            } catch (err) {
                alert("Gagal memproses gambar.");
                console.error(err);
            } finally {
                // Kembalikan tombol ke semula setelah 3 detik
                setTimeout(() => {
                    btnElement.querySelector('.btn-text').innerText = originalText;
                    btnElement.disabled = false;
                    btnElement.classList.replace('bg-green-600', 'bg-green-50');
                    btnElement.classList.replace('text-white', 'text-green-600');
                }, 3000);
            }
        }
    </script>

    {{-- SCRIPT BLUETOOTH PRINTER --}}
    <script>
    let printCharacteristic = null;

    // 1. KONEKSI BLUETOOTH (Tetap sama)
    document.getElementById('connectBtn').addEventListener('click', async () => {
        try {
            const device = await navigator.bluetooth.requestDevice({
                filters: [{ services: ['000018f0-0000-1000-8000-00805f9b34fb'] }]
            });
            const server = await device.gatt.connect();
            const service = await server.getPrimaryService('000018f0-0000-1000-8000-00805f9b34fb');
            printCharacteristic = await service.getCharacteristic('00002af1-0000-1000-8000-00805f9b34fb');
            
            const btn = document.getElementById('connectBtn');
            btn.classList.replace('bg-slate-800', 'bg-green-600');
            document.getElementById('printerStatus').innerText = "Printer Terhubung: " + device.name;
            alert('Printer Berhasil Terhubung!');
        } catch (error) {
            alert('Gagal Connect: ' + error);
        }
    });

    // --- FUNGSI BARU: MENGIRIM DATA DALAM POTONGAN KECIL (CHUNKS) ---
    async function writeChunks(characteristic, data) {
        const CHUNK_SIZE = 100; // Ukuran aman untuk printer thermal Bluetooth
        for (let i = 0; i < data.length; i += CHUNK_SIZE) {
            const chunk = data.slice(i, i + CHUNK_SIZE);
            await characteristic.writeValue(chunk);
            // Beri jeda sangat singkat agar printer tidak overload
            await new Promise(resolve => setTimeout(resolve, 20)); 
        }
    }

    // 2. FUNGSI CETAK STRUK PEMBAYARAN
    async function printReceipt(transactionId) {
        if (!printCharacteristic) {
            alert('⚠️ Harap hubungkan Printer Thermal Bluetooth terlebih dahulu.');
            return;
        }

        try {
            document.body.style.cursor = 'wait';

            // Ambil Data dari Server
            const response = await fetch(`/admin/transaksi/${transactionId}/print-data`);
            const result = await response.json();
            if(result.status !== 'success') throw new Error("Gagal mengambil data");
            const data = result.data;
            
            const qrUrl = window.location.origin + "/cek-pendaftaran/" + data.no_daftar;
            const encoder = new TextEncoder();
            
            // Perintah ESC/POS
            const ESC = '\u001B';
            const GS = '\u001D';
            const center = ESC + 'a' + '\u0001';
            const left = ESC + 'a' + '\u0000';
            const boldOn = ESC + 'E' + '\u0001';
            const boldOff = ESC + 'E' + '\u0000';
            const doubleSize = GS + '!' + '\u0011'; 
            const normalSize = GS + '!' + '\u0000';

            let text = "";
            text += center + boldOn + "BUKTI PEMBAYARAN\n" + boldOff;
            text += "PSB PONPES AL-HIKAM\n";
            text += "--------------------------------\n";
            text += left;
            text += "No Invoice : #" + data.invoice + "\n";
            text += "Tanggal    : " + data.tanggal + "\n";
            text += "Nama       : " + data.nama.substring(0, 20) + "\n";
            text += "No Daftar  : " + data.no_daftar + "\n";
            text += "--------------------------------\n";
            text += boldOn + "Pembayaran:" + boldOff + "\n";
            text += data.jenis + "\n";
            text += "--------------------------------\n";
            text += "Total Tagihan  : Rp " + data.total_tagihan + "\n";
            text += boldOn + "Bayar Sekarang : Rp " + data.bayar_sekarang + boldOff + "\n";
            text += "Sisa Tagihan   : Rp " + data.sisa_tagihan + "\n";
            text += "--------------------------------\n";
            text += center + "\nTOTAL BAYAR SAAT INI:\n";
            text += doubleSize + "Rp " + data.bayar_sekarang + normalSize + "\n";
            text += "--------------------------------\n";
            text += left + "Petugas: " + data.petugas + "\n\n";
            text += center + "Scan QR untuk melihat\nRIWAYAT & STATUS LENGKAP:\n";

            // GUNAKAN writeChunks untuk teks utama
            await writeChunks(printCharacteristic, encoder.encode(text));

            // CETAK QR CODE
            await printQRCode(qrUrl);

            // FEED AKHIR
            let footer = "\nSimpan struk ini sebagai\nbukti pembayaran yang sah.\n\n\n\n";
            await writeChunks(printCharacteristic, encoder.encode(footer));

        } catch (error) {
            alert('Gagal Mencetak: ' + error);
        } finally {
            document.body.style.cursor = 'default';
        }
    }

    // FUNGSI BANTUAN CETAK QR (Tetap sama, tapi gunakan writeValue karena data QR pendek)
    async function printQRCode(dataString) {
        const storeLen = dataString.length + 3;
        const pL = storeLen % 256;
        const pH = Math.floor(storeLen / 256);

        let cmdModel = new Uint8Array([29, 40, 107, 4, 0, 49, 65, 50, 0]);
        let cmdSize = new Uint8Array([29, 40, 107, 3, 0, 49, 67, 6]); 
        let cmdErr = new Uint8Array([29, 40, 107, 3, 0, 49, 69, 48]);
        let cmdStoreHeader = new Uint8Array([29, 40, 107, pL, pH, 49, 80, 48]);
        let dataBytes = new TextEncoder().encode(dataString);
        let cmdStoreFull = new Uint8Array(cmdStoreHeader.length + dataBytes.length);
        cmdStoreFull.set(cmdStoreHeader);
        cmdStoreFull.set(dataBytes, cmdStoreHeader.length);
        let cmdPrint = new Uint8Array([29, 40, 107, 3, 0, 49, 81, 48]);

        await printCharacteristic.writeValue(cmdModel);
        await printCharacteristic.writeValue(cmdSize);
        await printCharacteristic.writeValue(cmdErr);
        await printCharacteristic.writeValue(cmdStoreFull);
        await printCharacteristic.writeValue(cmdPrint);
    }
</script>
</x-app-layout>