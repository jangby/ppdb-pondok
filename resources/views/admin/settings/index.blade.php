<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Pengaturan Sistem PPDB') }}
        </h2>
    </x-slot>

    <div class="py-8" x-data="settingsHandler()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded shadow-sm flex items-center gap-3">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-green-700 font-medium">{{ session('success') }}</p>
                </div>
            @endif

            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    {{-- BAGIAN KIRI: PENGATURAN UMUM --}}
                    <div class="lg:col-span-2 space-y-6">
                        
                        {{-- Card 1: Identitas & Status --}}
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                Identitas & Status PPDB
                            </h3>
                            
                            <div class="space-y-4">
                                {{-- Nama Sekolah --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pondok / Sekolah</label>
                                    <input type="text" name="nama_sekolah" value="{{ $settings['nama_sekolah'] ?? 'Pondok Pesantren Al-Hidayah' }}" 
                                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                </div>

                                {{-- Status Pendaftaran --}}
                                <div class="p-4 rounded-lg bg-gray-50 border border-gray-200">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Status Pendaftaran Saat Ini:</label>
                                    <div class="flex items-center gap-6">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" name="status_ppdb" value="buka" class="w-4 h-4 text-green-600 focus:ring-green-500"
                                                {{ ($settings['status_ppdb'] ?? 'tutup') == 'buka' ? 'checked' : '' }}>
                                            <span class="text-green-700 font-bold bg-green-100 px-3 py-1 rounded-full text-sm">DIBUKA</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" name="status_ppdb" value="tutup" class="w-4 h-4 text-red-600 focus:ring-red-500"
                                                {{ ($settings['status_ppdb'] ?? 'tutup') == 'tutup' ? 'checked' : '' }}>
                                            <span class="text-red-700 font-bold bg-red-100 px-3 py-1 rounded-full text-sm">DITUTUP</span>
                                        </label>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">Jika ditutup, calon santri tidak akan bisa mengakses formulir pendaftaran.</p>
                                </div>

                                {{-- Tanggal --}}
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Buka</label>
                                        <input type="date" name="tgl_buka" value="{{ $settings['tgl_buka'] ?? '' }}" 
                                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Tutup</label>
                                        <input type="date" name="tgl_tutup" value="{{ $settings['tgl_tutup'] ?? '' }}" 
                                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Card 2: Kontak & Informasi --}}
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                Kontak & Informasi
                            </h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp Admin</label>
                                    <input type="text" name="whatsapp_admin" value="{{ $settings['whatsapp_admin'] ?? '' }}" placeholder="Contoh: 628123456789"
                                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Pengumuman Dashboard</label>
                                    <textarea name="pengumuman" rows="3" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Informasi yang akan tampil di dashboard calon santri...">{{ $settings['pengumuman'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- BAGIAN KANAN: PERSYARATAN (DINAMIS) --}}
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 h-full flex flex-col">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                Persyaratan Dokumen
                            </h3>
                            
                            <div class="bg-blue-50 text-blue-700 text-xs p-3 rounded-lg mb-4">
                                Tambahkan daftar dokumen yang wajib dibawa/dikumpulkan santri. 
                            </div>

                            {{-- LIST PERSYARATAN --}}
                            <div class="flex-1 space-y-3 overflow-y-auto max-h-[500px] pr-1">
                                <template x-for="(item, index) in requirements" :key="index">
                                    <div class="flex gap-2 items-start bg-gray-50 p-2 rounded-lg border border-gray-200">
                                        <div class="flex-1 space-y-2">
                                            <input type="text" name="syarat_nama[]" x-model="item.nama" 
                                                   placeholder="Nama Dokumen (Misal: FC KK)" 
                                                   class="w-full text-sm rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                                            
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs text-gray-500">Jumlah:</span>
                                                <input type="number" name="syarat_jumlah[]" x-model="item.jumlah" min="1" 
                                                       class="w-20 text-sm rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                                                <span class="text-xs text-gray-500">lembar</span>
                                            </div>
                                        </div>
                                        <button type="button" @click="removeRequirement(index)" class="text-red-400 hover:text-red-600 p-1">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </template>
                                
                                <div x-show="requirements.length === 0" class="text-center py-6 text-gray-400 text-sm italic border-2 border-dashed border-gray-200 rounded-lg">
                                    Belum ada persyaratan.
                                </div>
                            </div>

                            <button type="button" @click="addRequirement()" 
                                    class="mt-4 w-full py-2 border-2 border-dashed border-blue-300 text-blue-600 font-bold rounded-lg hover:bg-blue-50 transition flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Tambah Item
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Action Bar --}}
                <div class="mt-8 flex justify-end">
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white font-bold rounded-lg shadow-lg hover:bg-blue-700 transition flex items-center gap-2 transform hover:scale-105 duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Simpan Semua Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Script Logic AlpineJS --}}
    <script>
        function settingsHandler() {
            return {
                // Ambil data dari Controller, atau default kosong
                requirements: @json($requirements),

                addRequirement() {
                    this.requirements.push({
                        nama: '',
                        jumlah: 1
                    });
                },

                removeRequirement(index) {
                    this.requirements.splice(index, 1);
                }
            }
        }
    </script>
</x-app-layout>