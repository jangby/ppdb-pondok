<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                {{ __('Data Calon Santri') }}
            </h2>
            
            <div class="flex gap-2">
                {{-- [TOMBOL EXPORT EXCEL - WARNA HIJAU] --}}
                <a href="{{ route('admin.candidates.export') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Export Excel
                </a>

                {{-- [TOMBOL TAMBAH MANUAL - WARNA BIRU] --}}
                <a href="{{ route('admin.candidates.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Manual
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            {{-- 1. KARTU STATISTIK (KPI) --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                {{-- Total --}}
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Pendaftar</p>
                        <p class="text-2xl font-extrabold text-gray-800 mt-1">{{ $kpi['total'] }}</p>
                    </div>
                    <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>

                {{-- Laki-laki --}}
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Laki-laki</p>
                        <p class="text-2xl font-extrabold text-gray-800 mt-1">{{ $kpi['laki'] }}</p>
                    </div>
                    <div class="p-3 bg-cyan-50 text-cyan-600 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                </div>

                {{-- Perempuan --}}
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Perempuan</p>
                        <p class="text-2xl font-extrabold text-gray-800 mt-1">{{ $kpi['perempuan'] }}</p>
                    </div>
                    <div class="p-3 bg-pink-50 text-pink-600 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                </div>

                {{-- Diterima --}}
                <div class="bg-gradient-to-br from-emerald-500 to-green-600 p-5 rounded-2xl shadow-lg shadow-emerald-200 text-white flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-emerald-100 uppercase tracking-wider">Lulus Seleksi</p>
                        <p class="text-2xl font-extrabold mt-1">{{ $kpi['diterima'] }}</p>
                    </div>
                    <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>

            {{-- 2. FORM PENCARIAN & FILTER --}}
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <form method="GET" action="{{ route('admin.candidates.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    
                    {{-- Input Pencarian --}}
                    <div class="md:col-span-1 relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / No Daftar / NISN..." class="w-full pl-10 rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm">
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>

                    {{-- Filter Jenjang (Dinamis) --}}
                    <div>
                        <select name="jenjang" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm cursor-pointer shadow-sm" onchange="this.form.submit()">
                            <option value="Semua">Semua Jenjang</option>
                            @foreach($jenjangs as $j)
                                <option value="{{ $j }}" {{ request('jenjang') == $j ? 'selected' : '' }}>{{ $j }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Status --}}
                    <div>
                        <select name="status" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm cursor-pointer shadow-sm" onchange="this.form.submit()">
                            <option value="Semua">Semua Status</option>
                            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Lulus" {{ request('status') == 'Lulus' ? 'selected' : '' }}>Lulus / Diterima</option>
                            <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>

                    {{-- Tombol Reset --}}
                    <div>
                        <a href="{{ route('admin.candidates.index') }}" class="flex items-center justify-center w-full px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg text-sm font-medium transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Reset Filter
                        </a>
                    </div>
                </form>
            </div>

            {{-- 3. TABEL DATA SANTRI --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-4 font-bold tracking-wider">No. Daftar</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Nama Santri</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Jenjang</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Asal Sekolah</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Status</th>
                                <th class="px-6 py-4 font-bold tracking-wider text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($candidates as $candidate)
                                <tr class="bg-white hover:bg-blue-50/50 transition duration-150">
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                        {{ $candidate->no_daftar }}
                                        <div class="text-[10px] text-gray-400 mt-0.5">{{ $candidate->created_at->format('d M Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-800">{{ $candidate->nama_lengkap }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5 flex items-center gap-1">
                                            @if($candidate->jenis_kelamin == 'L')
                                                <span class="text-blue-500 bg-blue-50 px-1 rounded">Laki-laki</span>
                                            @else
                                                <span class="text-pink-500 bg-pink-50 px-1 rounded">Perempuan</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700 border border-gray-200">
                                            {{ $candidate->jenjang }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 truncate max-w-[150px]" title="{{ $candidate->asal_sekolah }}">
                                        {{ $candidate->asal_sekolah ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($candidate->status_seleksi == 'Lulus' || $candidate->status_seleksi == 'Diterima' || $candidate->status_seleksi == 'Approved')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Lulus
                                            </span>
                                        @elseif($candidate->status_seleksi == 'Ditolak')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Ditolak
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 animate-pulse"></span> Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
    <div class="flex items-center justify-center gap-2">
        {{-- 1. Tombol Detail --}}
        <a href="{{ route('admin.candidates.show', $candidate->id) }}" 
           class="text-blue-600 hover:text-white hover:bg-blue-600 bg-blue-50 border border-blue-200 p-2 rounded-lg transition" 
           title="Detail & Edit">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
        </a>

        {{-- 2. Tombol Kirim QR Code (Ungu) --}}
        <form action="{{ route('admin.attendance.send_qr', $candidate->id) }}" method="POST" onsubmit="return confirm('Kirim QR Code Absensi ke WA Wali Santri ini?');">
            @csrf
            <button type="submit" class="text-purple-600 hover:text-white hover:bg-purple-600 bg-purple-50 border border-purple-200 p-2 rounded-lg transition" title="Kirim QR Code ke WA">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
            </button>
        </form>

        {{-- 3. Tombol Pengingat / Status Hadir --}}
        @if($candidate->waktu_hadir)
            {{-- Jika Sudah Hadir (Tanda Ceklis Hijau) --}}
            <div class="text-green-600 bg-green-50 border border-green-200 p-2 rounded-lg cursor-help" title="Sudah Hadir (Antrian: {{ $candidate->nomor_antrian }})">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
        @else
            {{-- Jika Belum Hadir (Tombol Lonceng Kuning) --}}
            <form action="{{ route('admin.attendance.remind', $candidate->id) }}" method="POST" onsubmit="return confirm('Kirim Pengingat Jadwal ke WA Wali?');">
                @csrf
                <button type="submit" class="text-orange-600 hover:text-white hover:bg-orange-600 bg-orange-50 border border-orange-200 p-2 rounded-lg transition" title="Kirim Pengingat (Belum Hadir)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </button>
            </form>
        @endif
    </div>
</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="bg-gray-50 rounded-full p-4 mb-3">
                                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </div>
                                            <p class="text-gray-500 font-medium">Belum ada data calon santri.</p>
                                            <p class="text-gray-400 text-xs mt-1">Coba ubah filter atau tambah data manual.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- Pagination --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    {{ $candidates->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>