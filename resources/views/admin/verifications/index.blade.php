<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Antrian Verifikasi</h2>
                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded dark:bg-blue-200 dark:text-blue-800">
                    {{ count($verifications) }} Permintaan
                </span>
            </div>

            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <strong class="font-bold">Sukses!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="p-4 text-sm font-bold text-gray-600 uppercase tracking-wider">Info Kontak</th>
                                <th class="p-4 text-sm font-bold text-gray-600 uppercase tracking-wider">Status / Jenis</th>
                                <th class="p-4 text-sm font-bold text-gray-600 uppercase tracking-wider">Berkas / Bukti</th>
                                <th class="p-4 text-sm font-bold text-gray-600 uppercase tracking-wider text-right">Aksi Admin</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($verifications as $v)
                            <tr class="hover:bg-slate-50 transition">
                                {{-- KOLOM 1: INFO KONTAK --}}
                                <td class="p-4 align-top">
                                    <div class="font-bold text-gray-800 text-sm">{{ $v->no_wa }}</div>
                                    <div class="text-xs text-gray-500 mt-1">{{ $v->created_at->diffForHumans() }}</div>
                                    @if($v->jenjang)
                                        <div class="mt-2 inline-block px-2 py-0.5 bg-gray-100 text-gray-600 text-[10px] font-bold rounded border border-gray-300">
                                            Jenjang: {{ $v->jenjang }}
                                        </div>
                                    @endif
                                </td>

                                {{-- KOLOM 2: STATUS --}}
                                <td class="p-4 align-top">
                                    @if($v->status == 'pending')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-100 text-amber-700 rounded-full text-xs font-bold border border-amber-200">
                                            <span class="w-2 h-2 bg-amber-500 rounded-full animate-pulse"></span>
                                            Cek Berkas Perjanjian
                                        </span>
                                    @elseif($v->status_pembayaran == 'pending')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-100 text-blue-700 rounded-full text-xs font-bold border border-blue-200">
                                            <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
                                            Cek Bukti Transfer
                                        </span>
                                    @endif
                                </td>

                                {{-- KOLOM 3: PREVIEW FILE --}}
                                <td class="p-4 align-top space-y-2">
                                    {{-- File Perjanjian --}}
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        <a href="{{ asset('storage/'.$v->file_perjanjian) }}" target="_blank" class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline">
                                            Lihat Surat Perjanjian
                                        </a>
                                    </div>

                                    {{-- Bukti Transfer (Jika Ada) --}}
                                    @if($v->bukti_transfer)
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                            <a href="{{ asset('storage/'.$v->bukti_transfer) }}" target="_blank" class="text-xs font-bold text-emerald-600 hover:text-emerald-800 hover:underline">
                                                Lihat Bukti Transfer
                                            </a>
                                        </div>
                                    @endif
                                </td>

                                {{-- KOLOM 4: AKSI --}}
                                <td class="p-4 align-top text-right">
                                    <div class="flex justify-end gap-2">
                                        
                                        {{-- TOMBOL SETUJUI --}}
                                        <form action="{{ route('admin.verifications.approve', $v->id) }}" method="POST">
                                            @csrf
                                            @if($v->status == 'pending')
                                                <button type="submit" class="bg-gradient-to-r from-emerald-500 to-emerald-600 text-white px-4 py-2 rounded-lg text-xs font-bold shadow-md hover:from-emerald-600 hover:to-emerald-700 transition flex items-center gap-2" onclick="return confirm('Setujui Berkas Perjanjian & Kirim Tagihan ke WA?')">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    ACC Perjanjian
                                                </button>
                                            @elseif($v->status_pembayaran == 'pending')
                                                <button type="submit" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-4 py-2 rounded-lg text-xs font-bold shadow-md hover:from-blue-600 hover:to-blue-700 transition flex items-center gap-2" onclick="return confirm('Terima Pembayaran & Kirim Link Biodata ke WA?')">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    ACC Pembayaran
                                                </button>
                                            @endif
                                        </form>

                                        {{-- TOMBOL TOLAK --}}
                                        <div x-data="{ open: false }">
                                            <button @click="open = !open" type="button" class="bg-white border border-red-200 text-red-600 px-3 py-2 rounded-lg text-xs font-bold hover:bg-red-50 transition flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                Tolak
                                            </button>

                                            {{-- Modal Kecil untuk Alasan Tolak --}}
                                            <div x-show="open" @click.outside="open = false" class="absolute right-10 mt-2 w-64 bg-white border border-gray-200 shadow-xl rounded-xl p-3 z-50">
                                                <form action="{{ route('admin.verifications.reject', $v->id) }}" method="POST">
                                                    @csrf
                                                    <label class="block text-xs font-bold text-gray-700 mb-1">Alasan Penolakan:</label>
                                                    <textarea name="alasan" rows="2" class="w-full text-xs border-gray-300 rounded-lg mb-2" placeholder="Cth: Foto buram / Nominal salah..."></textarea>
                                                    <button type="submit" class="w-full bg-red-600 text-white text-xs font-bold py-1.5 rounded hover:bg-red-700">Konfirmasi Tolak</button>
                                                </form>
                                            </div>
                                        </div>

                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p class="text-sm">Tidak ada antrian verifikasi saat ini.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>