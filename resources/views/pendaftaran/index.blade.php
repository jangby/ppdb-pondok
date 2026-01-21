<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Formulir Pendaftaran</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-50">

    <div class="min-h-screen pb-32 md:pb-12">
        
        <div class="bg-gradient-to-br from-indigo-900 via-indigo-800 to-blue-900 pt-8 pb-32 rounded-b-[30px] md:rounded-b-[50px] shadow-2xl relative overflow-hidden">
            <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 30px 30px;"></div>
            
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
                <a href="{{ url('/') }}" class="inline-flex items-center text-blue-200 hover:text-white mb-6 md:mb-8 transition group bg-white/10 px-4 py-2 rounded-full backdrop-blur-sm hover:bg-white/20">
                    <svg class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span class="font-medium text-sm">Kembali ke Beranda</span>
                </a>
                <h1 class="text-3xl md:text-5xl lg:text-6xl font-extrabold text-white tracking-tight leading-tight mb-2 md:mb-4">
                    Formulir Pendaftaran
                </h1>
                <p class="text-base md:text-xl text-blue-100 max-w-2xl mx-auto font-light px-2">
                    Lengkapi biodata calon santri di bawah ini dengan data yang valid dan benar.
                </p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 -mt-20 md:-mt-24 relative z-20">
            
            @if($errors->any())
                <div class="bg-white border-l-8 border-red-500 p-4 md:p-6 mb-6 md:mb-8 rounded-r-xl shadow-xl animate-fade-in-down mx-1 md:mx-0">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 bg-red-100 rounded-full p-2">
                            <svg class="h-5 w-5 md:h-6 md:w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div class="ml-3 md:ml-4">
                            <h3 class="text-base md:text-lg font-bold text-gray-900">Mohon Periksa Kembali Inputan Anda</h3>
                            <ul class="mt-2 list-disc list-inside text-sm text-red-600 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('pendaftaran.store') }}" method="POST" class="space-y-6 md:space-y-8">
                @csrf
                
                <div class="bg-white rounded-2xl md:rounded-3xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-50 to-white px-5 py-4 md:px-8 md:py-6 border-b border-gray-100 flex items-center gap-3 md:gap-4">
                        <span class="flex-shrink-0 flex items-center justify-center w-10 h-10 md:w-12 md:h-12 rounded-xl md:rounded-2xl bg-blue-600 text-white font-bold text-lg md:text-xl shadow-lg shadow-blue-600/20">1</span>
                        <div>
                            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Identitas Diri</h2>
                            <p class="text-xs md:text-sm text-gray-500">Data pribadi calon santri sesuai dokumen.</p>
                        </div>
                    </div>
                    
                    <div class="p-5 md:p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 md:gap-6 lg:gap-8">
                            
                            <div class="lg:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 bg-gray-50 focus:bg-white transition text-base" placeholder="Sesuai Akta Kelahiran / Ijazah" required>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Daftar Ke Jenjang <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <select name="jenjang" class="w-full appearance-none rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 bg-gray-50 focus:bg-white transition cursor-pointer text-base" required>
                                        <option value="">Pilih Jenjang</option>
                                        <option value="SMP" {{ old('jenjang') == 'SMP' ? 'selected' : '' }}>SMP / MTs</option>
                                        <option value="SMK" {{ old('jenjang') == 'SMK' ? 'selected' : '' }}>SMK / MA</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">NISN <span class="text-red-500">*</span></label>
                                <input type="number" name="nisn" value="{{ old('nisn') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 bg-gray-50 focus:bg-white transition text-base" placeholder="10 Digit" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">NIK Santri <span class="text-red-500">*</span></label>
                                <input type="number" name="nik" value="{{ old('nik') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 bg-gray-50 focus:bg-white transition text-base" placeholder="16 Digit" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">No. KK</label>
                                <input type="number" name="no_kk" value="{{ old('no_kk') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 bg-gray-50 focus:bg-white transition text-base" placeholder="16 Digit">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Tempat Lahir <span class="text-red-500">*</span></label>
                                <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 bg-gray-50 focus:bg-white transition text-base" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Lahir <span class="text-red-500">*</span></label>
                                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 bg-gray-50 focus:bg-white transition cursor-pointer text-base" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Jenis Kelamin <span class="text-red-500">*</span></label>
                                <select name="jenis_kelamin" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 bg-gray-50 focus:bg-white transition text-base" required>
                                    <option value="">- Pilih -</option>
                                    <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Anak Ke-</label>
                                    <input type="number" name="anak_ke" value="{{ old('anak_ke') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 bg-gray-50 focus:bg-white transition text-base">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Jml Sdr</label>
                                    <input type="number" name="jumlah_saudara" value="{{ old('jumlah_saudara') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 bg-gray-50 focus:bg-white transition text-base">
                                </div>
                            </div>

                             <div class="lg:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Asal Sekolah</label>
                                <input type="text" name="asal_sekolah" value="{{ old('asal_sekolah') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 bg-gray-50 focus:bg-white transition text-base" placeholder="Nama SD/MI/SMP Asal">
                            </div>

                            <div class="lg:col-span-3">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Riwayat Penyakit (Opsional)</label>
                                <input type="text" name="riwayat_penyakit" value="{{ old('riwayat_penyakit') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 bg-gray-50 focus:bg-white transition text-base" placeholder="Sebutkan jika ada riwayat penyakit berat / alergi">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl md:rounded-3xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-50 to-white px-5 py-4 md:px-8 md:py-6 border-b border-gray-100 flex items-center gap-3 md:gap-4">
                        <span class="flex-shrink-0 flex items-center justify-center w-10 h-10 md:w-12 md:h-12 rounded-xl md:rounded-2xl bg-green-600 text-white font-bold text-lg md:text-xl shadow-lg shadow-green-600/20">2</span>
                        <div>
                            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Alamat Domisili</h2>
                            <p class="text-xs md:text-sm text-gray-500">Alamat tempat tinggal saat ini.</p>
                        </div>
                    </div>
                    
                    <div class="p-5 md:p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 md:gap-6 lg:gap-8">
                            
                            <div class="lg:col-span-4">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Jalan / Kampung / Dusun <span class="text-red-500">*</span></label>
                                <input type="text" name="alamat" value="{{ old('alamat') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 py-3 px-4 bg-gray-50 focus:bg-white transition text-base" placeholder="Contoh: Jl. Merdeka No. 45" required>
                            </div>
                            
                            <div class="lg:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">RT / RW</label>
                                <div class="grid grid-cols-2 gap-4">
                                    <input type="text" name="rt" value="{{ old('rt') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 py-3 px-4 bg-gray-50 focus:bg-white transition text-center text-base" placeholder="RT">
                                    <input type="text" name="rw" value="{{ old('rw') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 py-3 px-4 bg-gray-50 focus:bg-white transition text-center text-base" placeholder="RW">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Desa / Kelurahan <span class="text-red-500">*</span></label>
                                <input type="text" name="desa" value="{{ old('desa') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 py-3 px-4 bg-gray-50 focus:bg-white transition text-base" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Kecamatan <span class="text-red-500">*</span></label>
                                <input type="text" name="kecamatan" value="{{ old('kecamatan') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 py-3 px-4 bg-gray-50 focus:bg-white transition text-base" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Kabupaten / Kota <span class="text-red-500">*</span></label>
                                <input type="text" name="kabupaten" value="{{ old('kabupaten') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 py-3 px-4 bg-gray-50 focus:bg-white transition text-base" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Provinsi <span class="text-red-500">*</span></label>
                                <input type="text" name="provinsi" value="{{ old('provinsi') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 py-3 px-4 bg-gray-50 focus:bg-white transition text-base" required>
                            </div>
                            
                            <div class="lg:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Kode Pos</label>
                                <input type="number" name="kode_pos" value="{{ old('kode_pos') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 py-3 px-4 bg-gray-50 focus:bg-white transition text-base">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl md:rounded-3xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-50 to-white px-5 py-4 md:px-8 md:py-6 border-b border-gray-100 flex items-center gap-3 md:gap-4">
                        <span class="flex-shrink-0 flex items-center justify-center w-10 h-10 md:w-12 md:h-12 rounded-xl md:rounded-2xl bg-purple-600 text-white font-bold text-lg md:text-xl shadow-lg shadow-purple-600/20">3</span>
                        <div>
                            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Data Orang Tua</h2>
                            <p class="text-xs md:text-sm text-gray-500">Informasi Ayah dan Ibu kandung.</p>
                        </div>
                    </div>

                    <div class="p-5 md:p-8">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 relative">
                            
                            <div class="hidden lg:block absolute left-1/2 top-4 bottom-4 w-px bg-gray-200"></div>

                            <div class="space-y-5 md:space-y-6">
                                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-100 text-blue-800 font-bold text-sm tracking-wide mb-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    DATA AYAH
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Ayah <span class="text-red-500">*</span></label>
                                    <input type="text" name="nama_ayah" value="{{ old('nama_ayah') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 py-3 px-4 bg-gray-50 focus:bg-white transition text-base" required>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">NIK Ayah</label>
                                        <input type="number" name="nik_ayah" value="{{ old('nik_ayah') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 py-3 px-4 bg-gray-50 focus:bg-white transition text-base">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Tahun Lahir</label>
                                        <input type="number" name="thn_lahir_ayah" value="{{ old('thn_lahir_ayah') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 py-3 px-4 bg-gray-50 focus:bg-white transition text-base" placeholder="Contoh: 1980">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Pendidikan</label>
                                    <select name="pendidikan_ayah" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 py-3 px-4 bg-gray-50 focus:bg-white transition text-base">
                                        <option value="">- Pilih -</option>
                                        <option value="SD" {{ old('pendidikan_ayah') == 'SD' ? 'selected' : '' }}>SD</option>
                                        <option value="SMP" {{ old('pendidikan_ayah') == 'SMP' ? 'selected' : '' }}>SMP</option>
                                        <option value="SMA" {{ old('pendidikan_ayah') == 'SMA' ? 'selected' : '' }}>SMA</option>
                                        <option value="S1" {{ old('pendidikan_ayah') == 'S1' ? 'selected' : '' }}>S1 (Sarjana)</option>
                                        <option value="S2" {{ old('pendidikan_ayah') == 'S2' ? 'selected' : '' }}>S2 (Magister)</option>
                                        <option value="Lainnya" {{ old('pendidikan_ayah') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Pekerjaan</label>
                                    <input type="text" name="pekerjaan_ayah" value="{{ old('pekerjaan_ayah') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 py-3 px-4 bg-gray-50 focus:bg-white transition text-base">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Penghasilan (Rp)</label>
                                    <input type="number" name="penghasilan_ayah" value="{{ old('penghasilan_ayah', 0) }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 py-3 px-4 bg-gray-50 focus:bg-white transition text-base">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">No. HP / WA Ayah <span class="text-red-500">*</span></label>
                                    <input type="number" name="no_hp_ayah" value="{{ old('no_hp_ayah') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 py-3 px-4 bg-gray-50 focus:bg-white transition text-base" required>
                                </div>
                            </div>

                            <div class="space-y-5 md:space-y-6 pt-8 lg:pt-0 border-t-2 border-dashed lg:border-t-0 border-gray-100 lg:border-gray-200">
                                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-pink-100 text-pink-800 font-bold text-sm tracking-wide mb-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    DATA IBU
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Ibu <span class="text-red-500">*</span></label>
                                    <input type="text" name="nama_ibu" value="{{ old('nama_ibu') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 py-3 px-4 bg-gray-50 focus:bg-white transition text-base" required>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">NIK Ibu</label>
                                        <input type="number" name="nik_ibu" value="{{ old('nik_ibu') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 py-3 px-4 bg-gray-50 focus:bg-white transition text-base">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Tahun Lahir</label>
                                        <input type="number" name="thn_lahir_ibu" value="{{ old('thn_lahir_ibu') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 py-3 px-4 bg-gray-50 focus:bg-white transition text-base" placeholder="Contoh: 1982">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Pendidikan</label>
                                    <select name="pendidikan_ibu" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 py-3 px-4 bg-gray-50 focus:bg-white transition text-base">
                                        <option value="">- Pilih -</option>
                                        <option value="SD" {{ old('pendidikan_ibu') == 'SD' ? 'selected' : '' }}>SD</option>
                                        <option value="SMP" {{ old('pendidikan_ibu') == 'SMP' ? 'selected' : '' }}>SMP</option>
                                        <option value="SMA" {{ old('pendidikan_ibu') == 'SMA' ? 'selected' : '' }}>SMA</option>
                                        <option value="S1" {{ old('pendidikan_ibu') == 'S1' ? 'selected' : '' }}>S1 (Sarjana)</option>
                                        <option value="S2" {{ old('pendidikan_ibu') == 'S2' ? 'selected' : '' }}>S2 (Magister)</option>
                                        <option value="Lainnya" {{ old('pendidikan_ibu') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Pekerjaan</label>
                                    <input type="text" name="pekerjaan_ibu" value="{{ old('pekerjaan_ibu') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 py-3 px-4 bg-gray-50 focus:bg-white transition text-base">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Penghasilan (Rp)</label>
                                    <input type="number" name="penghasilan_ibu" value="{{ old('penghasilan_ibu', 0) }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 py-3 px-4 bg-gray-50 focus:bg-white transition text-base">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">No. HP / WA Ibu</label>
                                    <input type="number" name="no_hp_ibu" value="{{ old('no_hp_ibu') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 py-3 px-4 bg-gray-50 focus:bg-white transition text-base">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="fixed bottom-0 left-0 right-0 bg-white/90 backdrop-blur-md border-t border-gray-200 p-4 shadow-[0_-5px_15px_rgba(0,0,0,0.05)] md:static md:bg-transparent md:border-0 md:p-0 md:shadow-none z-50">
                    <div class="max-w-7xl mx-auto flex justify-center">
                        <button type="submit" class="w-full md:w-auto md:min-w-[320px] px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl md:rounded-2xl shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 flex items-center justify-center gap-3 text-lg group">
                            <span>Kirim Pendaftaran</span>
                            <svg class="w-6 h-6 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</body>
</html>