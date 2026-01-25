<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg shadow-lg text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </div>
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Konfigurasi Sistem PPDB') }}
                </h2>
                <p class="text-xs text-gray-500">Kelola identitas sekolah, logo, alur, dan tampilan website.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="settingsHandler()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-xl shadow-sm flex items-center gap-3 animate-fade-in-down">
                    <div class="p-2 bg-emerald-100 rounded-full text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-emerald-800 text-sm">Berhasil Disimpan!</h4>
                        <p class="text-emerald-600 text-sm">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="flex flex-col lg:flex-row gap-8">
                    
                    {{-- SIDEBAR NAVIGATION --}}
                    <div class="lg:w-64 flex-shrink-0">
                        <nav class="flex flex-row lg:flex-col gap-2 overflow-x-auto lg:overflow-visible pb-4 lg:pb-0 sticky top-4">
                            <button type="button" @click="activeTab = 'umum'" :class="activeTab === 'umum' ? 'bg-blue-600 text-white shadow-md ring-2 ring-blue-300' : 'bg-white text-gray-600 hover:bg-gray-50 hover:text-blue-600'" class="flex items-center gap-3 px-4 py-3 rounded-xl font-semibold text-sm transition-all whitespace-nowrap">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg> Umum & Alur
                            </button>
                            <button type="button" @click="activeTab = 'jenjang'" :class="activeTab === 'jenjang' ? 'bg-blue-600 text-white shadow-md ring-2 ring-blue-300' : 'bg-white text-gray-600 hover:bg-gray-50 hover:text-blue-600'" class="flex items-center gap-3 px-4 py-3 rounded-xl font-semibold text-sm transition-all whitespace-nowrap">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg> Jenjang Pendidikan
                            </button>
                            <button type="button" @click="activeTab = 'media'" :class="activeTab === 'media' ? 'bg-blue-600 text-white shadow-md ring-2 ring-blue-300' : 'bg-white text-gray-600 hover:bg-gray-50 hover:text-blue-600'" class="flex items-center gap-3 px-4 py-3 rounded-xl font-semibold text-sm transition-all whitespace-nowrap">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg> Tampilan & Galeri
                            </button>
                            <button type="button" @click="activeTab = 'syarat'" :class="activeTab === 'syarat' ? 'bg-blue-600 text-white shadow-md ring-2 ring-blue-300' : 'bg-white text-gray-600 hover:bg-gray-50 hover:text-blue-600'" class="flex items-center gap-3 px-4 py-3 rounded-xl font-semibold text-sm transition-all whitespace-nowrap">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg> Persyaratan
                            </button>
                        </nav>
                    </div>

                    {{-- CONTENT AREA --}}
                    <div class="flex-1 space-y-6">

                        {{-- TAB 1: UMUM --}}
                        <div x-show="activeTab === 'umum'" x-transition class="space-y-6">
                            
                            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                                <h3 class="font-bold text-gray-800 text-lg mb-6 border-b pb-4">Identitas Sekolah</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    
                                    {{-- [BARU] Input Logo Sekolah --}}
                                    <div class="col-span-2 flex items-center gap-6 p-4 bg-slate-50 rounded-xl border border-slate-200">
                                        <div class="shrink-0">
                                            <div class="w-20 h-20 bg-white rounded-lg border border-slate-300 flex items-center justify-center overflow-hidden shadow-sm">
                                                @if(!empty($settings['logo_sekolah']))
                                                    <img src="{{ asset('storage/'.$settings['logo_sekolah']) }}" class="w-full h-full object-contain p-1">
                                                @else
                                                    <span class="text-xs text-gray-400 font-bold text-center">No Logo</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <label class="block text-sm font-bold text-gray-700 mb-1">Logo Sekolah / Pondok</label>
                                            <input type="file" name="logo_sekolah" accept="image/*" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200 transition cursor-pointer">
                                            <p class="text-[10px] text-gray-500 mt-1">Format: PNG/JPG (Transparan direkomendasikan). Max: 1MB.</p>
                                        </div>
                                    </div>

                                    <div class="col-span-2">
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama Sekolah / Pesantren</label>
                                        <input type="text" name="nama_sekolah" value="{{ $settings['nama_sekolah'] ?? '' }}" class="w-full rounded-xl border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-3" placeholder="Contoh: Pondok Pesantren Al-Hidayah">
                                    </div>
                                    
                                    <div class="col-span-2">
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Alamat Lengkap Pondok</label>
                                        <textarea name="alamat_sekolah" rows="3" class="w-full rounded-xl border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-3" placeholder="Jalan, Desa, Kecamatan, Kabupaten...">{{ $settings['alamat_sekolah'] ?? '' }}</textarea>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama Gelombang</label>
                                        <input type="text" name="nama_gelombang" value="{{ $settings['nama_gelombang'] ?? 'Gelombang 1' }}" class="w-full rounded-xl border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-3">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">WhatsApp Admin (628...)</label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 font-bold text-sm">+</span>
                                            <input type="number" name="whatsapp_admin" value="{{ $settings['whatsapp_admin'] ?? '' }}" class="w-full pl-7 rounded-xl border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-3">
                                        </div>
                                    </div>

                                    {{-- [BARU] LINK GRUP WA PONDOK --}}
<div class="col-span-1">
    <label class="block text-sm font-bold text-gray-700 mb-2">Link Grup WA Pondok (Informasi Umum)</label>
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-green-600">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
        </div>
        <input type="url" name="link_grup_wa_pondok" 
               value="{{ $settings['link_grup_wa_pondok'] ?? '' }}"
               class="w-full pl-10 rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500"
               placeholder="https://chat.whatsapp.com/...">
    </div>
    <p class="text-xs text-gray-500 mt-1">Link ini akan dikirim otomatis saat santri Lulus Administrasi.</p>
</div>
                                </div>
                            </div>

                            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                                <h3 class="font-bold text-gray-800 text-lg mb-6 border-b pb-4">Status & Alur Pendaftaran</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-3">Status Pendaftaran</label>
                                        <div class="flex gap-4">
                                            <label class="cursor-pointer flex-1">
                                                <input type="radio" name="status_ppdb" value="buka" class="peer sr-only" {{ ($settings['status_ppdb'] ?? '') == 'buka' ? 'checked' : '' }}>
                                                <div class="p-4 rounded-xl border-2 border-gray-200 bg-gray-50 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 transition text-center hover:bg-white">
                                                    <div class="font-bold text-lg">DIBUKA</div>
                                                    <div class="text-xs text-gray-500 peer-checked:text-emerald-600">Terima Pendaftaran</div>
                                                </div>
                                            </label>
                                            <label class="cursor-pointer flex-1">
                                                <input type="radio" name="status_ppdb" value="tutup" class="peer sr-only" {{ ($settings['status_ppdb'] ?? '') == 'tutup' ? 'checked' : '' }}>
                                                <div class="p-4 rounded-xl border-2 border-gray-200 bg-gray-50 peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-700 transition text-center hover:bg-white">
                                                    <div class="font-bold text-lg">DITUTUP</div>
                                                    <div class="text-xs text-gray-500 peer-checked:text-red-600">Tutup Sementara</div>
                                                </div>
                                            </label>
                                        </div>
                                        <div class="mt-4 grid grid-cols-2 gap-3">
                                            <div><label class="text-xs font-bold text-gray-500">Tgl Buka</label><input type="date" name="tgl_buka" value="{{ $settings['tgl_buka'] ?? '' }}" class="w-full rounded-lg border-gray-300 text-xs"></div>
                                            <div><label class="text-xs font-bold text-gray-500">Tgl Tutup</label><input type="date" name="tgl_tutup" value="{{ $settings['tgl_tutup'] ?? '' }}" class="w-full rounded-lg border-gray-300 text-xs"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-3">Metode Verifikasi</label>
                                        <div class="space-y-3">
                                            <label class="cursor-pointer relative block">
                                                <input type="radio" name="verification_active" value="1" class="peer sr-only" {{ ($settings['verification_active'] ?? '1') == '1' ? 'checked' : '' }}>
                                                <div class="p-3 rounded-xl border-2 border-gray-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 flex items-center gap-3 transition">
                                                    <div class="w-5 h-5 rounded-full border border-gray-300 bg-white peer-checked:bg-blue-500 peer-checked:border-blue-500 flex items-center justify-center"><div class="w-2 h-2 rounded-full bg-white"></div></div>
                                                    <div><span class="block font-bold text-gray-800 text-sm">Wajib Upload Berkas</span><span class="block text-xs text-gray-500">Upload & verifikasi admin dulu.</span></div>
                                                </div>
                                            </label>
                                            <label class="cursor-pointer relative block">
                                                <input type="radio" name="verification_active" value="0" class="peer sr-only" {{ ($settings['verification_active'] ?? '1') == '0' ? 'checked' : '' }}>
                                                <div class="p-3 rounded-xl border-2 border-gray-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 flex items-center gap-3 transition">
                                                    <div class="w-5 h-5 rounded-full border border-gray-300 bg-white peer-checked:bg-blue-500 peer-checked:border-blue-500 flex items-center justify-center"><div class="w-2 h-2 rounded-full bg-white"></div></div>
                                                    <div><span class="block font-bold text-gray-800 text-sm">Mode Cepat (Langsung)</span><span class="block text-xs text-gray-500">Tanpa verifikasi berkas awal.</span></div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- TAB 2: JENJANG --}}
                        <div x-show="activeTab === 'jenjang'" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                            <div class="flex justify-between items-center mb-6 border-b pb-4">
                                <h3 class="font-bold text-gray-800 text-lg">Manajemen Jenjang Pendidikan</h3>
                                <button type="button" @click="jenjangs.push('')" class="text-sm font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 px-4 py-2 rounded-lg transition flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Tambah Jenjang
                                </button>
                            </div>
                            <div class="space-y-3 max-w-2xl">
                                <template x-for="(item, index) in jenjangs" :key="index">
                                    <div class="group flex items-center gap-3 bg-gray-50 p-3 rounded-xl border border-gray-200 focus-within:border-blue-400 focus-within:ring-2 focus-within:ring-blue-100 transition">
                                        <div class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center font-bold text-gray-400 text-xs" x-text="index + 1"></div>
                                        <input type="text" name="jenjang_nama[]" x-model="jenjangs[index]" class="flex-1 bg-transparent border-none focus:ring-0 text-gray-800 font-medium placeholder-gray-400" placeholder="Nama Jenjang (Contoh: SMP IT)">
                                        <button type="button" @click="jenjangs.splice(index, 1)" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition opacity-0 group-hover:opacity-100">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- TAB 3: MEDIA --}}
                        <div x-show="activeTab === 'media'" class="space-y-6">
                            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                                <h3 class="font-bold text-gray-800 text-lg mb-6 border-b pb-4">Banner & Halaman Depan</h3>
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-2">Upload Banner Utama</label>
                                            <input type="file" name="banner_image" accept="image/*" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200 transition cursor-pointer">
                                        </div>
                                        <div><label class="block text-sm font-bold text-gray-700 mb-2">Judul Utama</label><input name="pengumuman" value="{{ $settings['pengumuman'] ?? '' }}" class="w-full rounded-xl border-gray-300 focus:ring-blue-500 shadow-sm"></div>
                                        <div><label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi Singkat</label><textarea name="deskripsi_banner" rows="2" class="w-full rounded-xl border-gray-300 focus:ring-blue-500 shadow-sm">{{ $settings['deskripsi_banner'] ?? '' }}</textarea></div>
                                    </div>
                                    <div class="bg-gray-100 rounded-xl overflow-hidden relative group h-64 border border-gray-200 shadow-inner">
                                        @if(!empty($settings['banner_image'])) <img src="{{ asset('storage/'.$settings['banner_image']) }}" class="w-full h-full object-cover"> @else <div class="flex items-center justify-center h-full text-gray-400 text-sm">Belum ada banner</div> @endif
                                    </div>
                                </div>
                            </div>
                            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 flex flex-col md:flex-row items-center gap-6">
                                <div class="p-4 bg-amber-100 text-amber-600 rounded-full"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg></div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-amber-900 text-lg mb-1">Template Surat Perjanjian</h4>
                                    <p class="text-amber-700 text-sm mb-4">Upload file .doc/.pdf yang akan didownload calon santri.</p>
                                    <div class="flex gap-4">
                                        <input type="file" name="template_perjanjian" class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-amber-200 file:text-amber-800 hover:file:bg-amber-300 transition cursor-pointer">
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                                <div class="flex justify-between items-center mb-6"><h3 class="font-bold text-gray-800 text-lg">Galeri Kegiatan</h3><span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold">{{ count($galleries) }} Foto</span></div>
                                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4 mb-6">
                                    <label class="aspect-square rounded-xl border-2 border-dashed border-blue-300 bg-blue-50 hover:bg-blue-100 transition cursor-pointer flex flex-col items-center justify-center text-blue-500"><svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg><span class="text-xs font-bold">Tambah Foto</span><input type="file" name="gallery_files[]" multiple accept="image/*" class="hidden"></label>
                                    @foreach($galleries as $index => $img)
                                        <div class="relative group aspect-square rounded-xl overflow-hidden shadow-sm bg-gray-100">
                                            <img src="{{ asset('storage/'.$img) }}" class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
                                            <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition flex items-center justify-center backdrop-blur-[2px]">
                                                <button type="submit" form="delete-gallery-{{ $index }}" class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-full shadow-lg transition transform hover:scale-110"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- TAB 4: SYARAT --}}
                        <div x-show="activeTab === 'syarat'" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                            <div class="max-w-3xl">
                                <h3 class="font-bold text-gray-800 text-lg mb-2">Daftar Dokumen Persyaratan</h3>
                                <div class="space-y-3">
                                    <template x-for="(item, index) in requirements" :key="index">
                                        <div class="flex gap-3 items-center bg-gray-50 p-3 rounded-xl border border-gray-200">
                                            <span class="w-6 text-center text-gray-400 font-bold text-sm" x-text="index+1"></span>
                                            <div class="flex-1"><input type="text" name="syarat_nama[]" x-model="item.nama" class="w-full bg-white rounded-lg border-gray-300 text-sm focus:ring-blue-500 shadow-sm"></div>
                                            <div class="w-24 relative"><input type="number" name="syarat_jumlah[]" x-model="item.jumlah" class="w-full bg-white rounded-lg border-gray-300 text-sm text-center focus:ring-blue-500 shadow-sm"></div>
                                            <button type="button" @click="requirements.splice(index, 1)" class="text-gray-400 hover:text-red-500 p-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                                        </div>
                                    </template>
                                </div>
                                <button type="button" @click="requirements.push({nama:'', jumlah:1})" class="mt-4 w-full py-3 border-2 border-dashed border-gray-300 text-gray-500 rounded-xl hover:border-blue-500 hover:text-blue-600 transition font-bold flex items-center justify-center gap-2">+ Tambah Persyaratan</button>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="fixed bottom-6 right-6 z-40">
                    <button type="submit" class="flex items-center gap-3 px-8 py-4 bg-gray-900 text-white font-bold rounded-full shadow-2xl hover:bg-blue-700 hover:scale-105 transition transform focus:ring-4 focus:ring-blue-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>

            @foreach($galleries as $index => $img)
                <form id="delete-gallery-{{ $index }}" action="{{ route('admin.settings.delete_gallery') }}" method="POST" class="hidden">@csrf @method('DELETE')<input type="hidden" name="index" value="{{ $index }}"></form>
            @endforeach
        </div>
    </div>

    <script>
        function settingsHandler() {
            return {
                activeTab: 'umum',
                requirements: @json($requirements),
                facilities: @json($facilities),
                jenjangs: @json($jenjangs),
            }
        }
    </script>
</x-app-layout>