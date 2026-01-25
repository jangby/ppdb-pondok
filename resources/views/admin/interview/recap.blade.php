<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 leading-tight flex items-center gap-2">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                {{ __('Rekapitulasi Kehadiran') }}
            </h2>
            
            <a href="{{ route('admin.attendance.index') }}" class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-gray-700 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z"></path></svg>
                Kembali ke Scanner
            </a>
        </div>
    </x-slot>

    <div class="py-8 px-4 max-w-7xl mx-auto space-y-8">
        
        {{-- 1. STATISTIK CARD --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Peserta</p>
                    <p class="text-3xl font-black text-gray-800 mt-1">{{ $total }}</p>
                </div>
                <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-green-500 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-green-600 uppercase tracking-wider">Sudah Hadir</p>
                    <p class="text-3xl font-black text-gray-800 mt-1">{{ $present->count() }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ round(($present->count() / ($total > 0 ? $total : 1)) * 100) }}% Kehadiran</p>
                </div>
                <div class="p-3 bg-green-50 text-green-600 rounded-xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-red-500 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-red-600 uppercase tracking-wider">Belum Hadir</p>
                    <p class="text-3xl font-black text-gray-800 mt-1">{{ $absent->count() }}</p>
                </div>
                <div class="p-3 bg-red-50 text-red-600 rounded-xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded shadow-sm" role="alert">
                <p class="font-bold">Sukses!</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        {{-- 2. LIST DATA --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            {{-- KOLOM KIRI: SUDAH HADIR --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-green-50 px-6 py-4 border-b border-green-100 flex justify-between items-center">
                    <h3 class="font-bold text-green-800 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Data Sudah Hadir
                    </h3>
                    <span class="bg-green-200 text-green-800 text-xs font-bold px-2 py-1 rounded-full">{{ $present->count() }}</span>
                </div>
                <div class="max-h-[500px] overflow-y-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50 sticky top-0">
                            <tr>
                                <th class="px-4 py-3">Antrian</th>
                                <th class="px-4 py-3">Nama Santri</th>
                                <th class="px-4 py-3 text-right">Jam</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($present->sortByDesc('waktu_hadir') as $p)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <span class="font-bold text-lg text-gray-800">#{{ $p->nomor_antrian }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $p->nama_lengkap }}</div>
                                    <div class="text-xs text-gray-500">{{ $p->no_daftar }}</div>
                                </td>
                                <td class="px-4 py-3 text-right font-mono text-gray-600">
                                    {{ \Carbon\Carbon::parse($p->waktu_hadir)->format('H:i') }}
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="px-4 py-8 text-center text-gray-400 text-xs">Belum ada yang hadir</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- KOLOM KANAN: BELUM HADIR --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col h-full">
                <div class="bg-red-50 px-6 py-4 border-b border-red-100 flex justify-between items-center">
                    <h3 class="font-bold text-red-800 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Belum Hadir
                    </h3>
                    <span class="bg-red-200 text-red-800 text-xs font-bold px-2 py-1 rounded-full">{{ $absent->count() }}</span>
                </div>
                
                {{-- TOMBOL BLAST WA (Hanya muncul jika ada yg belum hadir) --}}
                @if($absent->count() > 0)
                <div class="p-4 bg-red-50 border-b border-red-100 text-center">
                    <form action="{{ route('admin.attendance.mass_remind') }}" method="POST" onsubmit="return confirm('⚠️ PERHATIAN!\n\nAkan mengirim pesan WA ke {{ $absent->count() }} orang tua sekaligus.\nProses ini mungkin memakan waktu beberapa detik.\n\nLanjutkan?');">
                        @csrf
                        <button type="submit" class="w-full bg-red-600 text-white font-bold py-3 px-4 rounded-xl shadow-lg hover:bg-red-700 active:scale-95 transition flex items-center justify-center gap-2 animate-pulse">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                            KIRIM PENGINGAT KE SEMUA ({{ $absent->count() }})
                        </button>
                    </form>
                    <p class="text-[10px] text-red-500 mt-2">*Pesan akan dikirim bertahap (jeda 2 detik) untuk mencegah blokir WA.</p>
                </div>
                @endif

                <div class="max-h-[500px] overflow-y-auto flex-1">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50 sticky top-0">
                            <tr>
                                <th class="px-4 py-3">Nama Santri</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($absent as $a)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $a->nama_lengkap }}</div>
                                    <div class="text-xs text-gray-500">{{ $a->no_daftar }} • {{ $a->jenjang }}</div>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    {{-- Tombol Ingatkan Personal --}}
                                    <form action="{{ route('admin.attendance.remind', $a->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-xs font-bold text-orange-600 hover:text-orange-800 bg-orange-50 px-2 py-1 rounded border border-orange-200">
                                            Ingatkan
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="px-4 py-8 text-center text-gray-400 text-xs">Semua sudah hadir! Alhamdulillah.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>