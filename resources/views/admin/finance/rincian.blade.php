<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.finance.index') }}" class="p-2 bg-white rounded-lg shadow-sm border border-gray-200 hover:bg-gray-50 transition text-gray-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Rincian Pembayaran per Item') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @forelse($jenjangs as $jenjang => $paymentTypes)
                <div class="mb-8">
                    {{-- Judul Jenjang --}}
                    <div class="flex items-center gap-3 mb-4 pl-2">
                        <div class="h-8 w-1 bg-indigo-600 rounded-full"></div>
                        <h3 class="text-xl font-black text-gray-800 uppercase tracking-wider">Jenjang: {{ $jenjang }}</h3>
                    </div>

                    <div class="space-y-4">
                        @foreach($paymentTypes as $type)
                            {{-- Accordion Item --}}
                            <details class="group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden [&_summary::-webkit-details-marker]:hidden">
                                
                                {{-- Header Accordion --}}
                                <summary class="flex justify-between items-center p-6 cursor-pointer list-none hover:bg-indigo-50/50 transition duration-200">
                                    <div class="flex-1 pr-8">
                                        <h4 class="font-bold text-gray-800 text-lg mb-2 flex items-center gap-2">
                                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                            {{ $type->nama_pembayaran }}
                                        </h4>
                                        
                                        {{-- Progress Bar Container --}}
                                        <div class="w-full">
                                            <div class="flex items-center gap-3">
                                                <div class="flex-1 h-2.5 bg-gray-100 rounded-full overflow-hidden shadow-inner">
                                                    <div class="h-full {{ $type->progress == 100 ? 'bg-green-500' : 'bg-indigo-500' }} rounded-full transition-all duration-1000" style="width: {{ $type->progress }}%"></div>
                                                </div>
                                                <span class="text-sm font-black {{ $type->progress == 100 ? 'text-green-600' : 'text-indigo-600' }}">{{ $type->progress }}%</span>
                                            </div>
                                            <div class="flex justify-between text-xs font-medium text-gray-500 mt-1.5">
                                                <span>Terkumpul: <b class="text-gray-700">Rp {{ number_format($type->total_terkumpul, 0, ',', '.') }}</b></span>
                                                <span>Target: <b class="text-gray-700">Rp {{ number_format($type->total_target, 0, ',', '.') }}</b></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {{-- Arrow Icon --}}
                                    <div class="w-10 h-10 rounded-full bg-gray-50 border border-gray-200 flex items-center justify-center text-gray-400 group-open:bg-indigo-100 group-open:text-indigo-600 group-open:border-indigo-200 group-open:rotate-180 transition-all shadow-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </summary>

                                {{-- Body Accordion (Tabel Data) --}}
                                <div class="px-6 pb-6 pt-2 border-t border-gray-100">
                                    @if($type->paid_bills->count() > 0)
                                        <div class="overflow-x-auto rounded-xl border border-gray-200 mt-4">
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-12">No</th>
                                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No. Daftar</th>
                                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Santri</th>
                                                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Nominal Bayar</th>
                                                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-100">
                                                    @foreach($type->paid_bills as $index => $bill)
                                                    <tr class="hover:bg-gray-50 transition">
                                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $index + 1 }}</td>
                                                        <td class="px-4 py-3 text-sm font-mono text-gray-600">{{ $bill->candidate->no_daftar }}</td>
                                                        <td class="px-4 py-3 text-sm font-bold text-gray-800">
                                                            <a href="{{ route('admin.candidates.show', $bill->candidate_id) }}" class="hover:text-indigo-600 hover:underline">
                                                                {{ $bill->candidate->nama_lengkap }}
                                                            </a>
                                                        </td>
                                                        <td class="px-4 py-3 text-sm text-right font-bold text-indigo-600">
                                                            Rp {{ number_format($bill->nominal_terbayar, 0, ',', '.') }}
                                                        </td>
                                                        <td class="px-4 py-3 text-center">
                                                            @if($bill->sisa_tagihan == 0)
                                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700 border border-green-200">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                                    LUNAS
                                                                </span>
                                                            @else
                                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-yellow-100 text-yellow-700 border border-yellow-200">
                                                                    CICILAN
                                                                </span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-8 bg-gray-50 rounded-xl mt-4 border border-dashed border-gray-300">
                                            <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <p class="text-sm font-bold text-gray-500">Belum ada pembayaran</p>
                                            <p class="text-xs text-gray-400 mt-1">Belum ada satupun santri yang membayar untuk item ini.</p>
                                        </div>
                                    @endif
                                </div>
                            </details>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="text-center py-16 bg-white rounded-2xl shadow-sm border border-gray-100">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    <p class="text-lg font-bold text-gray-600">Tidak ada data tagihan</p>
                </div>
            @endforelse

        </div>
    </div>
</x-app-layout>