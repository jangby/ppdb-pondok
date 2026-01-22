<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Atur Jenis Pembayaran') }}
        </h2>
    </x-slot>

    {{-- AlpineJS Scope untuk Modal CRUD --}}
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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                {{-- Header Tabel --}}
                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800 text-lg">Daftar Item Pembayaran</h3>
                    <button @click="openCreate()" 
                            class="px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg shadow hover:bg-blue-700 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Item
                    </button>
                </div>

                {{-- Alert Sukses --}}
                @if(session('success'))
                    <div class="bg-green-50 text-green-700 px-6 py-3 text-sm font-medium border-l-4 border-green-500">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Tabel Data --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wider">
                            <tr>
                                <th class="px-6 py-4 font-semibold">Nama Pembayaran</th>
                                <th class="px-6 py-4 font-semibold">Jenjang</th>
                                <th class="px-6 py-4 font-semibold text-right">Nominal Wajib</th>
                                <th class="px-6 py-4 font-semibold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($payments as $item)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $item->nama_pembayaran }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold 
                                        {{ $item->jenjang == 'Semua' ? 'bg-gray-100 text-gray-600' : 'bg-blue-100 text-blue-700' }}">
                                        {{ $item->jenjang }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right font-mono text-gray-700">
                                    Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center gap-2">
                                        <button @click="openEdit({{ $item }})"
                                                class="text-yellow-600 hover:text-yellow-800 p-1 bg-yellow-50 rounded hover:bg-yellow-100 transition" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <form action="{{ route('admin.payment_types.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus item ini?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 p-1 bg-red-50 rounded hover:bg-red-100 transition" title="Hapus">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500 italic">
                                    Belum ada jenis pembayaran yang diatur.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        {{-- MODAL FORM (Create / Edit) --}}
        <div x-show="showModal" style="display: none;" 
             class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" @click="showModal = false"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:w-full sm:max-w-lg">
                    <form :action="form.actionUrl" method="POST">
                        @csrf
                        {{-- Spoofing Method untuk Edit (PUT) --}}
                        <input type="hidden" name="_method" :value="isEdit ? 'PUT' : 'POST'">
                        
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-bold leading-6 text-gray-900 mb-4">
                                <span x-text="isEdit ? 'Edit Item Pembayaran' : 'Tambah Item Pembayaran'"></span>
                            </h3>
                            
                            <div class="space-y-4">
                                {{-- Nama Pembayaran --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nama Pembayaran</label>
                                    <input type="text" name="nama_pembayaran" x-model="form.nama_pembayaran" required
                                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           placeholder="Contoh: Uang Gedung">
                                </div>

                                {{-- Jenjang --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Jenjang</label>
                                    <select name="jenjang" x-model="form.jenjang" required
                                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="Semua">Semua Jenjang</option>
                                        <option value="SD">SD</option>
                                        <option value="SMP">SMP</option>
                                        <option value="SMA">SMA</option>
                                        <option value="SMK">SMK</option>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">Pilih "Semua" jika tagihan berlaku untuk semua santri.</p>
                                </div>

                                {{-- Nominal --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nominal (Rp)</label>
                                    <input type="number" name="nominal" x-model="form.nominal" required min="0"
                                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           placeholder="0">
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="submit" 
                                    class="inline-flex w-full justify-center rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 sm:ml-3 sm:w-auto">
                                Simpan
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