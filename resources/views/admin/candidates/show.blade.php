<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manajemen Data Santri') }}
            </h2>
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
    </x-slot>

    <div class="py-8 bg-gray-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

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
                                {{-- JIKA TIDAK ADA FILE --}}
                                <div class="text-center py-8 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <p class="text-sm font-bold text-gray-500">Berkas tidak tersedia</p>
                                    <p class="text-xs text-gray-400 mt-1">Santri ini mendaftar manual.</p>
                                </div>
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
                                    <p class="font-semibold text-gray-800">{{ $candidate->parent->nama_ayah }}</p>
                                    <a href="https://wa.me/{{ $candidate->parent->no_hp_ayah }}" target="_blank" class="text-xs text-green-600 font-bold hover:underline flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                        {{ $candidate->parent->no_hp_ayah }}
                                    </a>
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
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </span>
                                Kasir Pembayaran
                            </h3>
                            <span class="text-[10px] font-bold text-gray-300 bg-gray-800 px-3 py-1 rounded-full border border-gray-700">MODE INPUT MANUAL</span>
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
                                    <a href="{{ route('admin.transactions.print', $trx->id) }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-800 bg-blue-50 px-3 py-1.5 rounded-lg hover:bg-blue-100 transition">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        Cetak Struk
                                    </a>
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
</x-app-layout>