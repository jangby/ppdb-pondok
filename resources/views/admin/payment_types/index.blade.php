<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Keuangan Pondok') }}
        </h2>
    </x-slot>

    {{-- 
        AlpineJS Data Scope 
        Mengatur visibilitas modal dan data form
    --}}
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

            {{-- 1. KARTU REKAPITULASI (SALDO PER POS) --}}
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-700 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    Pos Anggaran (Saldo per Item)
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($recap as $item)
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition">
                        {{-- Indikator Warna (Merah jika minus, Hijau jika aman) --}}
                        <div class="absolute right-0 top-0 h-full w-2 {{ $item->saldo < 0 ? 'bg-red-500' : 'bg-green-500' }}"></div>
                        
                        <div class="flex justify-between items-start">
                            <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">{{ $item->nama }}</h4>
                            @if($item->saldo < 0)
                                <span class="bg-red-100 text-red-700 text-xs px-2 py-0.5 rounded-full font-bold">Defisit</span>
                            @endif
                        </div>
                        
                        <div class="mt-3">
                            <div class="text-2xl font-bold text-gray-800">
                                Rp {{ number_format($item->saldo, 0, ',', '.') }}
                            </div>
                            <div class="text-xs text-gray-400 mt-1">Sisa Saldo Tersedia</div>
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

            {{-- 2. TABEL RIWAYAT PENGELUARAN --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
                    <h3 class="font-bold text-gray-800 text-lg">Riwayat Pengeluaran</h3>
                    
                    <button @click="showModal = true" 
                            class="px-4 py-2 bg-red-600 text-white text-sm font-bold rounded-lg shadow hover:bg-red-700 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Catat Pengeluaran
                    </button>
                </div>

                {{-- Alert Sukses/Error --}}
                @if(session('success'))
                    <div class="bg-green-50 text-green-700 px-6 py-3 text-sm font-medium border-l-4 border-green-500">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-red-50 text-red-700 px-6 py-3 text-sm font-medium border-l-4 border-red-500">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wider">
                                <th class="px-6 py-3 font-semibold">Tanggal</th>
                                <th class="px-6 py-3 font-semibold">Keperluan (Judul)</th>
                                <th class="px-6 py-3 font-semibold">Sumber Dana</th>
                                <th class="px-6 py-3 font-semibold text-right">Nominal</th>
                                <th class="px-6 py-3 font-semibold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($expenses as $expense)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($expense->tanggal)->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    {{ $expense->judul_pengeluaran }}
                                    <div class="text-xs text-gray-400 font-normal mt-0.5">
                                        Oleh: {{ $expense->user->name ?? 'Admin' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @foreach($expense->fundSources as $source)
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">
                                            {{ $source->paymentType->nama_pembayaran ?? 'Dana Umum' }}
                                        </span>
                                    @endforeach
                                </td>
                                <td class="px-6 py-4 text-right font-mono text-red-600 font-bold">
                                    Rp {{ number_format($expense->total_keluar, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <form action="{{ route('admin.finance.destroy', $expense->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pengeluaran ini? Saldo akan dikembalikan.');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600 transition p-1 rounded hover:bg-red-50" title="Hapus / Batalkan">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="bg-gray-100 rounded-full p-3 mb-2">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </div>
                                        <p>Belum ada data pengeluaran.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- Pagination --}}
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $expenses->links() }}
                </div>
            </div>

        </div>

        {{-- 3. MODAL TAMBAH PENGELUARAN --}}
        <div x-show="showModal" 
             style="display: none;" 
             class="fixed inset-0 z-50 overflow-y-auto" 
             aria-labelledby="modal-title" role="dialog" aria-modal="true">
            
            {{-- Backdrop --}}
            <div x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" 
                 @click="showModal = false"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    
                    <form action="{{ route('admin.finance.store') }}" method="POST">
                        @csrf
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start mb-4">
                                <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                    <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">Catat Pengeluaran Baru</h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">Silakan isi detail pengeluaran di bawah ini.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="space-y-4">
                                {{-- Judul / Keperluan --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Judul Pengeluaran / Keperluan</label>
                                    <input type="text" name="deskripsi" x-model="form.deskripsi" required
                                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"
                                           placeholder="Contoh: Beli Kitab Fathul Qorib 100 exp">
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    {{-- Tanggal --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                                        <input type="date" name="tanggal" x-model="form.tanggal" required
                                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                                    </div>
                                    {{-- Nominal --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Nominal (Rp)</label>
                                        <input type="number" name="nominal" x-model="form.nominal" required min="1"
                                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"
                                               placeholder="0">
                                    </div>
                                </div>

                                {{-- Sumber Dana --}}
                                <div class="bg-red-50 p-4 rounded-lg border border-red-100">
                                    <label class="block text-sm font-bold text-gray-800 mb-2">Ambil Dana Dari Item:</label>
                                    <select name="payment_type_id" required 
                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                                        <option value="">-- Pilih Sumber Dana --</option>
                                        @foreach($paymentTypes as $type)
                                            <option value="{{ $type->id }}">
                                                {{ $type->nama_pembayaran }} ({{ $type->jenjang }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="text-xs text-red-600 mt-2 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Saldo pada item yang dipilih akan otomatis berkurang.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="submit" 
                                    class="inline-flex w-full justify-center rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto transition">
                                Simpan Pengeluaran
                            </button>
                            <button type="button" @click="showModal = false"
                                    class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
    </div>
</x-app-layout>