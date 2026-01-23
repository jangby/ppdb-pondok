<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <div class="p-2 bg-indigo-100 rounded-lg text-indigo-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                {{ __('Atur Jenis Pembayaran') }}
            </h2>
        </div>
    </x-slot>

    {{-- AlpineJS Scope --}}
    <div class="py-8" x-data="{
        showModal: false,
        isEdit: false,
        form: {
            id: null,
            nama_pembayaran: '',
            nominal: '',
            jenjang: 'Semua',
            actionUrl: ''
        },
        openCreate() {
            this.isEdit = false;
            this.form.id = null;
            this.form.nama_pembayaran = '';
            this.form.nominal = '';
            this.form.jenjang = 'Semua';
            this.form.actionUrl = '{{ route('admin.payment_types.store') }}';
            this.showModal = true;
        },
        openEdit(item) {
            this.isEdit = true;
            this.form.id = item.id;
            this.form.nama_pembayaran = item.nama_pembayaran;
            this.form.nominal = item.nominal;
            this.form.jenjang = item.jenjang;
            this.form.actionUrl = '{{ url('admin/jenis-pembayaran') }}/' + item.id;
            this.showModal = true;
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- 1. INFO CARD --}}
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden">
                <div class="absolute top-0 right-0 -mr-4 -mt-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
                <div class="relative z-10 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold">Total Item Tagihan</h3>
                        <p class="text-sm text-indigo-100 mt-1">Tagihan ini akan otomatis muncul saat santri mendaftar sesuai jenjangnya.</p>
                    </div>
                    <div class="text-4xl font-extrabold">{{ $payments->count() }}</div>
                </div>
            </div>
            
            {{-- 2. TABEL DATA --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                {{-- Header Tabel --}}
                <div class="px-6 py-5 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4 bg-gray-50/50">
                    <div>
                        <h3 class="font-bold text-gray-800 text-lg">Daftar Tagihan PPDB</h3>
                        <p class="text-xs text-gray-500">Kelola biaya pendaftaran untuk setiap jenjang.</p>
                    </div>
                    <button @click="openCreate()" 
                            class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-xl shadow-md hover:bg-indigo-700 hover:shadow-lg transition transform hover:-translate-y-0.5 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Tagihan Baru
                    </button>
                </div>

                {{-- Alert Sukses --}}
                @if(session('success'))
                    <div class="mx-6 mt-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-green-700 text-sm font-medium">{{ session('success') }}</p>
                    </div>
                @endif

                {{-- Tabel --}}
                <div class="overflow-x-auto p-4">
                    <table class="w-full text-left border-collapse rounded-lg overflow-hidden">
                        <thead class="bg-gray-100 text-gray-500 uppercase text-xs tracking-wider font-bold">
                            <tr>
                                <th class="px-6 py-4 rounded-tl-lg">Nama Pembayaran</th>
                                <th class="px-6 py-4">Target Jenjang</th>
                                <th class="px-6 py-4 text-right">Nominal (Rp)</th>
                                <th class="px-6 py-4 text-center rounded-tr-lg">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($payments as $item)
                            <tr class="hover:bg-indigo-50/30 transition duration-150 group">
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 rounded-lg bg-indigo-50 text-indigo-600 group-hover:bg-indigo-100 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        </div>
                                        <span class="font-bold text-gray-800">{{ $item->nama_pembayaran }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    @if($item->jenjang == 'Semua')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-700 border border-purple-200">
                                            Semua Jenjang
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700 border border-blue-200">
                                            Khusus {{ $item->jenjang }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <span class="font-mono font-bold text-gray-700 bg-gray-50 px-2 py-1 rounded border border-gray-200">
                                        Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <div class="flex justify-center gap-2">
                                        <button @click="openEdit({{ $item }})"
                                                class="text-gray-500 hover:text-indigo-600 p-2 hover:bg-indigo-50 rounded-lg transition" title="Edit Data">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <form action="{{ route('admin.payment_types.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tagihan ini?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-gray-500 hover:text-red-600 p-2 hover:bg-red-50 rounded-lg transition" title="Hapus Permanen">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="bg-gray-50 rounded-full p-4 mb-3">
                                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </div>
                                        <p class="text-gray-500 font-medium">Belum ada data tagihan.</p>
                                        <p class="text-gray-400 text-xs mt-1">Klik tombol "Tambah Tagihan Baru" untuk memulai.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- MODAL FORM (Glassmorphism Style) --}}
        <div x-show="showModal" style="display: none;" 
             class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            
            {{-- Backdrop Blur --}}
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" 
                 x-show="showModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="showModal = false"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:w-full sm:max-w-lg border border-gray-100"
                     x-show="showModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    
                    <form :action="form.actionUrl" method="POST">
                        @csrf
                        <input type="hidden" name="_method" :value="isEdit ? 'PUT' : 'POST'">
                        
                        {{-- Header Modal --}}
                        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                <svg x-show="!isEdit" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                <svg x-show="isEdit" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                <span x-text="isEdit ? 'Edit Tagihan' : 'Tambah Tagihan Baru'"></span>
                            </h3>
                        </div>
                        
                        <div class="px-6 py-6 space-y-5">
                            {{-- Nama Pembayaran --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Nama Pembayaran</label>
                                <input type="text" name="nama_pembayaran" x-model="form.nama_pembayaran" required
                                       class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3"
                                       placeholder="Contoh: Uang Gedung, SPP Bulan Juli">
                            </div>

                            {{-- Jenjang (Loop Dinamis) --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Target Jenjang</label>
                                <select name="jenjang" x-model="form.jenjang" required
                                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3">
                                    <option value="Semua">Semua Jenjang (Umum)</option>
                                    {{-- Loop dari Variabel Controller --}}
                                    @foreach($jenjangs as $j)
                                        <option value="{{ $j }}">{{ $j }}</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Pilih "Semua" jika tagihan ini berlaku untuk semua jenjang.</p>
                            </div>

                            {{-- Nominal --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Nominal (Rp)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 font-bold">Rp</span>
                                    </div>
                                    <input type="number" name="nominal" x-model="form.nominal" required min="0"
                                           class="block w-full rounded-xl border-gray-300 pl-10 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3"
                                           placeholder="0">
                                </div>
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3">
                            <button type="submit" 
                                    class="inline-flex w-full justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white shadow hover:bg-indigo-700 sm:w-auto transition">
                                Simpan Data
                            </button>
                            <button type="button" @click="showModal = false"
                                    class="inline-flex w-full justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:w-auto transition">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>