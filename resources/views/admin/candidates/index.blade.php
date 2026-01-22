<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Manajemen Data Santri') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- 1. KPI CARDS (STATISTIK INFORMATIF) --}}
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                {{-- Total --}}
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Santri</span>
                    <span class="text-3xl font-extrabold text-gray-800 mt-1">{{ $kpi['total'] }}</span>
                </div>
                {{-- Laki-laki --}}
                <div class="bg-blue-50 p-4 rounded-xl shadow-sm border border-blue-100 flex flex-col items-center justify-center text-center">
                    <span class="text-xs font-bold text-blue-400 uppercase tracking-wider">Laki-laki</span>
                    <span class="text-3xl font-extrabold text-blue-600 mt-1">{{ $kpi['laki'] }}</span>
                </div>
                {{-- Perempuan --}}
                <div class="bg-pink-50 p-4 rounded-xl shadow-sm border border-pink-100 flex flex-col items-center justify-center text-center">
                    <span class="text-xs font-bold text-pink-400 uppercase tracking-wider">Perempuan</span>
                    <span class="text-3xl font-extrabold text-pink-600 mt-1">{{ $kpi['perempuan'] }}</span>
                </div>
                {{-- Baru --}}
                <div class="bg-yellow-50 p-4 rounded-xl shadow-sm border border-yellow-100 flex flex-col items-center justify-center text-center">
                    <span class="text-xs font-bold text-yellow-600 uppercase tracking-wider">Status Baru</span>
                    <span class="text-3xl font-extrabold text-yellow-700 mt-1">{{ $kpi['baru'] }}</span>
                </div>
                {{-- Lulus --}}
                <div class="bg-green-50 p-4 rounded-xl shadow-sm border border-green-100 flex flex-col items-center justify-center text-center">
                    <span class="text-xs font-bold text-green-600 uppercase tracking-wider">Diterima</span>
                    <span class="text-3xl font-extrabold text-green-700 mt-1">{{ $kpi['diterima'] }}</span>
                </div>
            </div>

            {{-- 2. TOOLBAR (SEARCH, FILTER, EXPORT, ADD) --}}
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
                
                {{-- Kiri: Search & Filter --}}
                <form method="GET" action="{{ route('admin.candidates.index') }}" class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
                    {{-- Search --}}
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / No Daftar..." 
                               class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 w-full md:w-64">
                        <div class="absolute left-3 top-2.5 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>

                    {{-- Filter Jenjang --}}
                    <select name="jenjang" onchange="this.form.submit()" class="py-2 pl-3 pr-8 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                        <option value="Semua">Semua Jenjang</option>
                        <option value="SMP" {{ request('jenjang') == 'SMP' ? 'selected' : '' }}>SMP</option>
                        <option value="SMK" {{ request('jenjang') == 'SMK' ? 'selected' : '' }}>SMK</option>
                    </select>

                    {{-- Filter Status --}}
                    <select name="status" onchange="this.form.submit()" class="py-2 pl-3 pr-8 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                        <option value="Semua">Semua Status</option>
                        <option value="Baru" {{ request('status') == 'Baru' ? 'selected' : '' }}>Baru</option>
                        <option value="Lulus" {{ request('status') == 'Lulus' ? 'selected' : '' }}>Lulus</option>
                        <option value="Tidak Lulus" {{ request('status') == 'Tidak Lulus' ? 'selected' : '' }}>Tidak Lulus</option>
                    </select>
                </form>

                {{-- Kanan: Tombol Action --}}
                <div class="flex items-center gap-3 w-full md:w-auto justify-end">
                    
                    {{-- TOMBOL EXCEL --}}
                    <a href="{{ route('admin.candidates.export') }}" target="_blank"
                       class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-lg transition shadow hover:shadow-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        <span>Export Excel</span>
                    </a>

                    {{-- TOMBOL TAMBAH MANUAL (+) --}}
                    <a href="{{ route('admin.candidates.create') }}" 
                       class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg transition shadow hover:shadow-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>Tambah Santri</span>
                    </a>
                </div>
            </div>

            {{-- Alert --}}
            @if(session('success'))
                <div class="bg-green-50 text-green-700 px-6 py-3 rounded-lg border border-green-200 text-sm font-medium">
                    {{ session('success') }}
                </div>
            @endif

            {{-- 3. TABEL DATA --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50/50 text-gray-500 uppercase text-xs tracking-wider">
                            <tr>
                                <th class="px-6 py-4 font-semibold">No. Daftar</th>
                                <th class="px-6 py-4 font-semibold">Nama Santri</th>
                                <th class="px-6 py-4 font-semibold">Jenjang</th>
                                <th class="px-6 py-4 font-semibold text-center">JK</th>
                                <th class="px-6 py-4 font-semibold text-center">Status</th>
                                <th class="px-6 py-4 font-semibold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($candidates as $candidate)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-mono text-gray-600 text-xs">
                                    {{ $candidate->no_daftar }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $candidate->nama_lengkap }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">
                                        {{ \Carbon\Carbon::parse($candidate->created_at)->format('d M Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $candidate->jenjang }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold 
                                        {{ $candidate->jenis_kelamin == 'L' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }}">
                                        {{ $candidate->jenis_kelamin }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $statusClass = match($candidate->status) {
                                            'Lulus' => 'bg-green-100 text-green-700 border-green-200',
                                            'Tidak Lulus' => 'bg-red-100 text-red-700 border-red-200',
                                            default => 'bg-yellow-100 text-yellow-700 border-yellow-200'
                                        };
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $statusClass }}">
                                        {{ $candidate->status ?? 'Baru' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- Detail --}}
                                        <a href="{{ route('admin.candidates.show', $candidate->id) }}" 
                                           class="text-blue-600 hover:bg-blue-50 p-1.5 rounded-lg transition" title="Lihat Detail">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                        {{-- Edit --}}
                                        <a href="{{ route('admin.candidates.edit', $candidate->id) }}" 
                                           class="text-yellow-600 hover:bg-yellow-50 p-1.5 rounded-lg transition" title="Edit Data">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        {{-- Delete --}}
                                        <form action="{{ route('admin.candidates.update', $candidate->id) }}" method="POST" onsubmit="return confirm('Hapus data santri ini?')">
                                            @csrf @method('DELETE') <button type="submit" class="text-red-600 hover:bg-red-50 p-1.5 rounded-lg transition" title="Hapus">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    Tidak ada data santri ditemukan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $candidates->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>