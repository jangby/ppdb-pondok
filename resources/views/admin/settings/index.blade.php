<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Pengaturan PPDB & Tampilan') }}
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

            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- TABS NAVIGATION (Simple) --}}
                <div class="mb-6 border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button type="button" @click="activeTab = 'umum'" 
                            :class="activeTab === 'umum' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Pengaturan Umum
                        </button>
                        <button type="button" @click="activeTab = 'media'" 
                            :class="activeTab === 'media' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Tampilan & Media (Banner/Galeri)
                        </button>
                        <button type="button" @click="activeTab = 'syarat'" 
                            :class="activeTab === 'syarat' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Persyaratan Dokumen
                        </button>
                    </nav>
                </div>

                {{-- TAB CONTENT: UMUM --}}
                <div x-show="activeTab === 'umum'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <h3 class="font-bold text-gray-800 mb-4">Identitas Sekolah</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Sekolah</label>
                                <input type="text" name="nama_sekolah" value="{{ $settings['nama_sekolah'] ?? '' }}" class="w-full rounded-lg border-gray-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Gelombang</label>
                                <input type="text" name="nama_gelombang" value="{{ $settings['nama_gelombang'] ?? 'Gelombang 1' }}" class="w-full rounded-lg border-gray-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">WhatsApp Admin (Format: 628...)</label>
                                <input type="number" name="whatsapp_admin" value="{{ $settings['whatsapp_admin'] ?? '' }}" class="w-full rounded-lg border-gray-300">
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <h3 class="font-bold text-gray-800 mb-4">Status & Jadwal</h3>
                        <div class="space-y-4">
                            <div class="p-3 bg-gray-50 rounded-lg border">
                                <span class="block text-sm font-bold text-gray-700 mb-2">Status Pendaftaran:</span>
                                <div class="flex gap-4">
                                    <label class="flex items-center gap-2 cursor-pointer"><input type="radio" name="status_ppdb" value="buka" {{ ($settings['status_ppdb'] ?? '') == 'buka' ? 'checked' : '' }}> <span class="text-green-600 font-bold">DIBUKA</span></label>
                                    <label class="flex items-center gap-2 cursor-pointer"><input type="radio" name="status_ppdb" value="tutup" {{ ($settings['status_ppdb'] ?? '') == 'tutup' ? 'checked' : '' }}> <span class="text-red-600 font-bold">DITUTUP</span></label>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div><label class="text-xs">Tgl Buka</label><input type="date" name="tgl_buka" value="{{ $settings['tgl_buka'] ?? '' }}" class="w-full rounded-lg border-gray-300"></div>
                                <div><label class="text-xs">Tgl Tutup</label><input type="date" name="tgl_tutup" value="{{ $settings['tgl_tutup'] ?? '' }}" class="w-full rounded-lg border-gray-300"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB CONTENT: MEDIA (BANNER & GALERI) --}}
                <div x-show="activeTab === 'media'" class="space-y-6">
                    
                    {{-- Banner Section --}}
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">1. Banner Halaman Utama</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Upload Foto Banner (Landscape)</label>
                                <input type="file" name="banner_image" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <p class="text-xs text-gray-400 mt-1">*Otomatis dikompres. Disarankan ukuran 1200x600px.</p>
                                
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul / Slogan Banner</label>
                                    <textarea name="pengumuman" rows="2" class="w-full rounded-lg border-gray-300" placeholder="Contoh: Mewujudkan Generasi Rabbani...">{{ $settings['pengumuman'] ?? '' }}</textarea>
                                </div>
                                <div class="mt-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Singkat</label>
                                    <textarea name="deskripsi_banner" rows="2" class="w-full rounded-lg border-gray-300" placeholder="Sub-text di bawah judul...">{{ $settings['deskripsi_banner'] ?? '' }}</textarea>
                                </div>
                            </div>
                            <div class="border rounded-lg p-2 bg-gray-50 flex items-center justify-center min-h-[150px]">
                                @if(!empty($settings['banner_image']))
                                    <img src="{{ asset('storage/'.$settings['banner_image']) }}" class="max-h-48 rounded shadow-sm object-cover w-full">
                                @else
                                    <span class="text-gray-400 text-sm">Belum ada banner upload</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
    <h4 class="font-bold text-yellow-800 mb-2">Berkas Perjanjian Santri</h4>
    <div class="flex flex-col gap-2">
        <label class="text-sm text-gray-600">Upload Template (PDF/Word) untuk didownload calon santri:</label>
        <input type="file" name="template_perjanjian" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-yellow-100 file:text-yellow-700 hover:file:bg-yellow-200">
        
        @if(!empty($settings['template_perjanjian']))
            <a href="{{ asset('storage/'.$settings['template_perjanjian']) }}" target="_blank" class="text-sm text-blue-600 underline mt-2">
                Lihat Template Saat Ini
            </a>
        @endif
    </div>
</div>

                    {{-- Fasilitas Section --}}
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">2. Daftar Fasilitas Unggulan</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <template x-for="(fac, index) in facilities" :key="index">
                                <div class="flex gap-2">
                                    <input type="text" name="fasilitas_nama[]" x-model="facilities[index]" class="w-full rounded-lg border-gray-300 text-sm" placeholder="Nama Fasilitas">
                                    <button type="button" @click="facilities.splice(index, 1)" class="text-red-500 hover:text-red-700 font-bold px-2">X</button>
                                </div>
                            </template>
                        </div>
                        <button type="button" @click="facilities.push('')" class="mt-3 text-sm text-blue-600 font-bold hover:underline">+ Tambah Fasilitas</button>
                    </div>

                    {{-- Gallery Section --}}
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">3. Galeri Kegiatan (Upload Banyak)</h3>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tambah Foto Baru</label>
                            <input type="file" name="gallery_files[]" multiple accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                            <p class="text-xs text-gray-400 mt-1">*Bisa pilih banyak foto sekaligus. Otomatis dikompres.</p>
                        </div>

                        {{-- Preview Existing Gallery --}}
                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mt-4">
                            @foreach($galleries as $index => $img)
                                <div class="relative group rounded-lg overflow-hidden border border-gray-200">
                                    <img src="{{ asset('storage/'.$img) }}" class="w-full h-24 object-cover">
                                    {{-- Delete Button (Overlay) --}}
                                    <div class="absolute inset-0 bg-black/50 hidden group-hover:flex items-center justify-center transition">
                                        <button type="submit" form="delete-gallery-{{ $index }}" class="bg-red-600 text-white px-2 py-1 rounded text-xs">Hapus</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- TAB CONTENT: SYARAT --}}
                <div x-show="activeTab === 'syarat'" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4">Daftar Dokumen Persyaratan</h3>
                    <div class="space-y-3">
                        <template x-for="(item, index) in requirements" :key="index">
                            <div class="flex gap-3">
                                <input type="text" name="syarat_nama[]" x-model="item.nama" placeholder="Nama Dokumen" class="flex-1 rounded-lg border-gray-300 text-sm">
                                <input type="number" name="syarat_jumlah[]" x-model="item.jumlah" class="w-20 rounded-lg border-gray-300 text-sm text-center" placeholder="Jml">
                                <button type="button" @click="requirements.splice(index, 1)" class="text-red-500 hover:text-red-700 font-bold px-2">X</button>
                            </div>
                        </template>
                    </div>
                    <button type="button" @click="requirements.push({nama:'', jumlah:1})" class="mt-4 w-full py-2 border-2 border-dashed border-gray-300 text-gray-500 rounded-lg hover:border-blue-400 hover:text-blue-500 transition">+ Tambah Item</button>
                </div>

                {{-- ACTION BAR --}}
                <div class="mt-8 flex justify-end">
                    <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-bold rounded-xl shadow-lg hover:bg-blue-700 transition transform hover:scale-105">
                        Simpan Semua Perubahan
                    </button>
                </div>
            </form>

            {{-- Hidden Forms for Delete Gallery --}}
            @foreach($galleries as $index => $img)
                <form id="delete-gallery-{{ $index }}" action="{{ route('admin.settings.delete_gallery') }}" method="POST" class="hidden">
                    @csrf @method('DELETE')
                    <input type="hidden" name="index" value="{{ $index }}">
                </form>
            @endforeach

        </div>
    </div>

    <script>
        function settingsHandler() {
            return {
                activeTab: 'media', // Default tab open
                requirements: @json($requirements),
                facilities: @json($facilities),
            }
        }
    </script>
</x-app-layout>