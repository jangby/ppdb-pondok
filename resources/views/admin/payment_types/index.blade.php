<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-gradient-to-r from-teal-500 to-emerald-600 rounded-lg shadow-lg text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Jenis Pembayaran') }}
                </h2>
                <p class="text-xs text-gray-500">Atur komponen biaya pendaftaran untuk setiap jenjang.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($totalBiaya as $jenjang => $total)
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-50 rounded-full group-hover:bg-blue-100 transition"></div>
                    <div class="relative z-10">
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Total Biaya</div>
                        <h3 class="text-xl font-bold text-gray-800">{{ $jenjang }}</h3>
                        <div class="mt-4 flex items-baseline gap-1">
                            <span class="text-sm font-semibold text-gray-500">Rp</span>
                            <span class="text-2xl font-extrabold text-blue-600">{{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                        <p class="text-[10px] text-gray-400 mt-2">Akumulasi biaya {{ $jenjang }} + Umum</p>
                    </div>
                </div>
                @endforeach

                <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-6 shadow-lg text-white relative overflow-hidden flex flex-col justify-center">
                    <div class="relative z-10">
                        <h3 class="font-bold text-lg mb-1">Informasi</h3>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            Biaya bertanda <b>"Semua"</b> akan otomatis ditambahkan ke total tagihan semua jenjang.
                        </p>
                    </div>
                    <div class="absolute right-2 bottom-2 opacity-10">
                        <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-8">
                
                <div class="flex-1 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                        <h3 class="font-bold text-gray-800">Daftar Item Pembayaran</h3>
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">{{ $payments->count() }} Item</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                                <tr>
                                    <th class="px-6 py-4 font-bold">Nama Pembayaran</th>
                                    <th class="px-6 py-4 font-bold">Nominal</th>
                                    <th class="px-6 py-4 font-bold">Untuk Jenjang</th>
                                    <th class="px-6 py-4 font-bold text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($payments as $payment)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-medium text-gray-800">{{ $payment->nama_pembayaran }}</td>
                                    <td class="px-6 py-4 font-bold text-emerald-600">
                                        Rp {{ number_format($payment->nominal, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($payment->jenjang == 'Semua')
                                            <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-bold">Semua</span>
                                        @else
                                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">{{ $payment->jenjang }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center flex justify-center gap-2">
                                        <button onclick="openEditModal('{{ $payment->id }}', '{{ $payment->nama_pembayaran }}', '{{ $payment->nominal }}', '{{ $payment->jenjang }}')" 
                                                class="p-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </button>
                                        
                                        <form action="{{ route('admin.payment_types.destroy', $payment->id) }}" method="POST" onsubmit="return confirm('Yakin hapus item ini? Tagihan santri terkait akan ikut terhapus!');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic">Belum ada data pembayaran.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="w-full lg:w-1/3">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-6">
                        <h3 class="font-bold text-gray-800 text-lg mb-4">Tambah Pembayaran Baru</h3>
                        <form action="{{ route('admin.payment_types.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Nama Pembayaran</label>
                                <input type="text" name="nama_pembayaran" class="w-full rounded-xl border-gray-300 focus:ring-blue-500 shadow-sm text-sm" placeholder="Contoh: Uang Gedung" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Nominal (Rp)</label>
                                <input type="number" name="nominal" class="w-full rounded-xl border-gray-300 focus:ring-blue-500 shadow-sm text-sm" placeholder="0" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Berlaku Untuk</label>
                                <select name="jenjang" class="w-full rounded-xl border-gray-300 focus:ring-blue-500 shadow-sm text-sm" required>
                                    <option value="Semua">Semua Jenjang</option>
                                    @foreach($jenjangs as $j)
                                        <option value="{{ $j }}">Khusus {{ $j }}</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-400 mt-1">Pilih 'Semua' jika biaya ini berlaku untuk semua santri.</p>
                            </div>
                            <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-500/30 transition transform active:scale-95">
                                Simpan Data
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeEditModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">Edit Data Pembayaran</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Nama Pembayaran</label>
                                <input type="text" name="nama_pembayaran" id="edit_nama" class="w-full rounded-xl border-gray-300 focus:ring-blue-500 shadow-sm text-sm" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Nominal (Rp)</label>
                                <input type="number" name="nominal" id="edit_nominal" class="w-full rounded-xl border-gray-300 focus:ring-blue-500 shadow-sm text-sm" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Berlaku Untuk</label>
                                <select name="jenjang" id="edit_jenjang" class="w-full rounded-xl border-gray-300 focus:ring-blue-500 shadow-sm text-sm" required>
                                    <option value="Semua">Semua Jenjang</option>
                                    @foreach($jenjangs as $j)
                                        <option value="{{ $j }}">Khusus {{ $j }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Simpan Perubahan
                        </button>
                        <button type="button" onclick="closeEditModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openEditModal(id, nama, nominal, jenjang) {
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('edit_nama').value = nama;
            document.getElementById('edit_nominal').value = nominal;
            document.getElementById('edit_jenjang').value = jenjang;
            
            // [PERBAIKAN DISINI]
            // Gunakan URL yang sesuai dengan route di web.php ('/admin/jenis-pembayaran')
            let form = document.getElementById('editForm');
            form.action = "{{ url('/admin/jenis-pembayaran') }}/" + id;
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
</x-app-layout>