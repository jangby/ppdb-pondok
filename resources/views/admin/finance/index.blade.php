<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Laporan & Manajemen Keuangan') }}
        </h2>
    </x-slot>

    <div class="py-8" x-data="{
        showModal: false,
        form: {
            deskripsi: '',
            tanggal: '{{ date('Y-m-d') }}',
            nominal: '',
            payment_type_id: ''
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- BAGIAN 1: REKAPITULASI SALDO PER POS --}}
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-700 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    Pos Anggaran (Saldo per Item)
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($recap as $item)
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 relative overflow-hidden">
                        {{-- Hiasan Background --}}
                        <div class="absolute right-0 top-0 h-full w-2 bg-{{ $item->saldo < 0 ? 'red' : 'green' }}-500"></div>
                        
                        <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">{{ $item->nama }}</h4>
                        
                        <div class="mt-2 flex justify-between items-end">
                            <div>
                                <div class="text-2xl font-bold text-gray-800">
                                    Rp {{ number_format($item->saldo, 0, ',', '.') }}
                                </div>
                                <div class="text-xs text-gray-400 mt-1">Sisa Saldo Tersedia</div>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t border-gray-100 text-xs grid grid-cols-2 gap-2">
                            <div>
                                <span class="block text-gray-400">Total Masuk</span>
                                <span class="font-semibold text-green-600">
                                    + {{ number_format($item->pemasukan, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="block text-gray-400">Total Keluar</span>
                                <span class="font-semibold text-red-600">
                                    - {{ number_format($item->pengeluaran, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- BAGIAN 2: DAFTAR PENGELUARAN --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
                    <h3 class="font-bold text-gray-800 text-lg">Riwayat Pengeluaran</h3>
                    
                    <button @click="showModal = true" 
                            class="px-4 py-2 bg-red-600 text-white text-sm font-bold rounded-lg shadow hover:bg-red-700 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Catat Pengeluaran
                    </button>
                </div>

                {{-- Alert --}}
                @if(session('success'))
                    <div class="bg-green-50 text-green-700 px-6 py-3 text-sm font-medium">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-red-50 text-red-700 px-6 py-3 text-sm font-medium">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wider">
                                <th class="px-6 py-3 font-semibold">Tanggal</th>
                                <th class="px-6 py-3 font-semibold">Deskripsi</th>
                                <th class="px-6 py-3 font-semibold">Sumber Dana</th>
                                <th class="px-6 py-3 font-semibold text-right">Nominal</th>
                                <th class="px-6 py-3 font-semibold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($expenses as $expense)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($expense->tanggal)->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    {{ $expense->deskripsi }}
                                    <div class="text-xs text-gray-400 font-normal mt-0.5">Oleh: {{ $expense->user->name ?? 'Admin' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    {{-- Loop sources (meski saat ini sistemnya 1 source, disiapkan untuk future proof) --}}
                                    @foreach($expense->fundSources as $source)
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">
                                            {{ $source->paymentType->nama_pembayaran ?? 'Umum' }}
                                        </span>
                                    @endforeach
                                </td>
                                <td class="px-6 py-4 text-right font-mono text-red-600 font-bold">
                                    Rp {{ number_format($expense->jumlah, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <form action="{{ route('admin.finance.destroy', $expense->id) }}" method="POST" onsubmit="return confirm('Batalkan pengeluaran ini? Saldo akan dikembalikan.');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600 transition" title="Hapus / Batalkan">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    Belum ada data pengeluaran.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $expenses->links() }}
                </div>
            </div>

        </div>

        {{-- MODAL TAMBAH PENGELUARAN --}}
        <div x-show="showModal" style="display: none;" 
             class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" @click="showModal = false"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:w-full sm:max-w-lg">
                    <form action="{{ route('admin.finance.store') }}" method="POST">
                        @csrf
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900">Catat Pengeluaran Baru</h3>
                            </div>
                            
                            <div class="space-y-4">
                                {{-- Deskripsi --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Keperluan / Deskripsi</label>
                                    <input type="text" name="deskripsi" x-model="form.deskripsi" required
                                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                           placeholder="Contoh: Beli Kitab Fathul Qorib 100 exp">
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    {{-- Tanggal --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                                        <input type="date" name="tanggal" x-model="form.tanggal" required
                                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                    </div>
                                    {{-- Nominal --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Nominal (Rp)</label>
                                        <input type="number" name="nominal" x-model="form.nominal" required min="1"
                                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                               placeholder="0">
                                    </div>
                                </div>

                                {{-- Sumber Dana (INTI PERMINTAAN ANDA) --}}
                                <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Ambil Dana Dari Item:</label>
                                    <select name="payment_type_id" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                        <option value="">-- Pilih Sumber Dana --</option>
                                        @foreach($paymentTypes as $type)
                                            <option value="{{ $type->id }}">{{ $type->nama_pembayaran }} ({{ $type->jenjang }})</option>
                                        @endforeach
                                    </select>
                                    <p class="text-xs text-gray-500 mt-2">Saldo item terpilih akan otomatis berkurang.</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="submit" 
                                    class="inline-flex w-full justify-center rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto">
                                Simpan Pengeluaran
                            </button>
                            <button type="button" @click="showModal = false"
                                    class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
    </div>
</x-app-layout>