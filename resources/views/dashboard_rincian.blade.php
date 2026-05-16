<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="p-2 bg-white rounded-lg shadow-sm border border-gray-200 hover:bg-gray-50 transition text-gray-500" title="Kembali ke Dashboard">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daftar Santri yang Sudah Membayar') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- PANEL FILTER SELEKSI --}}
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                <form method="GET" action="{{ route('dashboard.pemasukan.rincian') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    
                    {{-- Opsi Filter Jenjang --}}
                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Saring Jenjang</label>
                        <select name="jenjang" class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500 text-sm cursor-pointer shadow-sm" onchange="this.form.submit()">
                            <option value="Semua">Semua Jenjang</option>
                            @foreach($listJenjang as $jenjang)
                                <option value="{{ $jenjang }}" {{ request('jenjang') == $jenjang ? 'selected' : '' }}>{{ $jenjang }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Opsi Filter Gender --}}
                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Saring Jenis Kelamin</label>
                        <select name="gender" class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500 text-sm cursor-pointer shadow-sm" onchange="this.form.submit()">
                            <option value="Semua">Semua Gender</option>
                            <option value="L" {{ request('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ request('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    {{-- Tombol Reset Filter --}}
                    <div class="flex items-end">
                        <a href="{{ route('dashboard.pemasukan.rincian') }}" class="flex items-center justify-center w-full px-4 py-2 bg-gray-50 hover:bg-gray-100 text-gray-600 rounded-xl text-sm font-bold transition border border-gray-200 h-[42px]">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Reset Saringan
                        </a>
                    </div>
                </form>
            </div>

            {{-- DAFTAR KARTU SANTRI --}}
            <div class="space-y-4">
                @forelse($candidates as $candidate)
                    <details class="group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden [&_summary::-webkit-details-marker]:hidden">
                        
                        <summary class="flex flex-col lg:flex-row justify-between items-start lg:items-center p-6 cursor-pointer hover:bg-green-50/30 transition duration-200 gap-6">
                            
                            {{-- Info Profil --}}
                            <div class="flex items-center gap-4 w-full lg:w-1/3">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center text-white text-xl font-black shadow-md shadow-green-200 shrink-0">
                                    {{ substr($candidate->nama_lengkap, 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800 text-lg leading-tight">{{ $candidate->nama_lengkap }}</h4>
                                    <div class="text-[11px] text-gray-500 font-mono flex flex-wrap gap-1.5 items-center mt-1">
                                        <span class="bg-gray-100 px-2 py-0.5 rounded text-gray-700 font-bold">#{{ $candidate->no_daftar }}</span>
                                        <span class="bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded font-bold">{{ $candidate->jenjang }}</span>
                                        @if($candidate->jenis_kelamin == 'L')
                                            <span class="bg-blue-50 text-blue-600 px-2 py-0.5 rounded font-bold">Laki-laki</span>
                                        @else
                                            <span class="bg-pink-50 text-pink-600 px-2 py-0.5 rounded font-bold">Perempuan</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Progress Bar Kelulusan --}}
                            <div class="w-full lg:w-1/3">
                                <div class="flex justify-between text-xs font-bold mb-1.5">
                                    <span class="{{ $candidate->progress == 100 ? 'text-green-600' : 'text-blue-600' }}">{{ $candidate->progress }}% Terbayar</span>
                                    <span class="text-gray-400">Total: Rp {{ number_format($candidate->total_tagihan, 0, ',', '.') }}</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2.5 shadow-inner overflow-hidden">
                                    <div class="h-full {{ $candidate->progress == 100 ? 'bg-green-500' : 'bg-blue-500' }} rounded-full transition-all duration-500" style="width: {{ $candidate->progress }}%"></div>
                                </div>
                            </div>

                            {{-- Angka Finansial Ringkas --}}
                            <div class="w-full lg:w-auto flex justify-between items-center gap-8">
                                <div class="flex gap-6">
                                    <div class="text-right">
                                        <div class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Uang Masuk</div>
                                        <div class="text-lg font-black text-green-600">Rp {{ number_format($candidate->total_terbayar, 0, ',', '.') }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Sisa Bayar</div>
                                        <div class="text-lg font-black {{ $candidate->sisa_tagihan > 0 ? 'text-red-500' : 'text-gray-400' }}">Rp {{ number_format($candidate->sisa_tagihan, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                                <div class="w-10 h-10 rounded-full bg-gray-50 border border-gray-200 flex items-center justify-center text-gray-400 group-open:bg-green-100 group-open:text-green-600 group-open:border-green-200 group-open:rotate-180 transition-all shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </summary>

                        {{-- AKREDIAL RINCIAN ITEM (BISA DI-KLIK) --}}
                        <div class="px-6 pb-6 pt-2 border-t border-gray-100 bg-gray-50/50">
                            <div class="flex justify-between items-end mb-3">
                                <h5 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    Komponen Pembayaran Terperinci
                                </h5>
                                <a href="{{ route('admin.candidates.show', $candidate->id) }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline">Ke Halaman Kasir &rarr;</a>
                            </div>
                            
                            <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Nama Item Pembayaran</th>
                                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase">Total Tagihan</th>
                                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase">Uang Masuk</th>
                                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach($candidate->bills as $bill)
                                        <tr class="hover:bg-gray-50 transition {{ $bill->nominal_terbayar > 0 ? '' : 'opacity-40 grayscale' }}">
                                            <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $bill->payment_type->nama_pembayaran ?? 'Item Khusus' }}</td>
                                            <td class="px-4 py-3 text-sm text-right text-gray-500">Rp {{ number_format($bill->nominal_tagihan, 0, ',', '.') }}</td>
                                            <td class="px-4 py-3 text-sm text-right font-bold {{ $bill->nominal_terbayar > 0 ? 'text-green-600' : 'text-gray-400' }}">Rp {{ number_format($bill->nominal_terbayar, 0, ',', '.') }}</td>
                                            <td class="px-4 py-3 text-center">
                                                @if($bill->sisa_tagihan == 0 && $bill->nominal_tagihan > 0)
                                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-700 border border-green-200">LUNAS</span>
                                                @elseif($bill->nominal_terbayar > 0)
                                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-yellow-100 text-yellow-700 border border-yellow-200">CICILAN</span>
                                                @else
                                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-400 border border-gray-200">BELUM BAYAR</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </details>
                @empty
                    {{-- Kondisi jika data kosong karena filter tidak cocok --}}
                    <div class="text-center py-20 bg-white rounded-2xl shadow-sm border border-gray-100">
                        <div class="bg-gray-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                        </div>
                        <p class="text-lg font-bold text-gray-700">Data Tidak Ditemukan</p>
                        <p class="text-gray-400 mt-1 text-sm">Tidak ada santri dengan kombinasi filter tersebut yang terdeteksi sudah membayar.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>