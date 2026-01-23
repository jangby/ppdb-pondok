<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Formulir Biodata Santri</title>
    
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* Custom Scrollbar (Valid CSS) */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Hiding Radio Input Default */
        .radio-input { position: absolute; opacity: 0; width: 0; height: 0; }
        
        /* Peer Checked Logic for Cards */
        .radio-input:checked + .radio-content {
            border-color: #10b981; /* Emerald-500 */
            background-color: rgba(16, 185, 129, 0.05);
            box-shadow: 0 0 0 1px #10b981;
        }
        .radio-input:checked + .radio-content .radio-icon {
            color: #059669; /* Emerald-600 */
            background-color: #d1fae5; /* Emerald-100 */
        }

        /* Focus Ring Animation for Inputs */
        .custom-input:focus ~ .input-icon { color: #059669; transform: translateY(-50%) scale(1.1); }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-8 px-4 sm:py-12">

    {{-- Background Decoration --}}
    <div class="fixed inset-0 z-0 pointer-events-none">
        <div class="absolute top-0 left-0 w-full h-[500px] bg-gradient-to-b from-emerald-700 to-slate-100"></div>
    </div>

    <div class="relative z-10 max-w-5xl mx-auto">
        
        {{-- Header Card --}}
        <div class="bg-white rounded-t-3xl shadow-xl overflow-hidden mb-1">
            <div class="relative bg-slate-900 p-8 sm:p-10 text-center overflow-hidden">
                {{-- Decorative Pattern --}}
                <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
                
                {{-- Blobs --}}
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-emerald-500 rounded-full blur-3xl opacity-20"></div>
                <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-blue-500 rounded-full blur-3xl opacity-20"></div>

                <div class="relative z-10">
                    <span class="inline-block py-1 px-3 rounded-full bg-white/10 border border-white/20 text-emerald-300 text-[10px] font-bold uppercase tracking-widest mb-4">
                        Langkah 2 dari 2
                    </span>
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight mb-2">Formulir Pendaftaran</h1>
                    <p class="text-slate-400 text-sm max-w-lg mx-auto">
                        Silakan lengkapi biodata calon santri di bawah ini dengan data yang valid dan terbaru.
                    </p>
                </div>
            </div>
            
            {{-- Progress Bar --}}
            <div class="h-1.5 w-full bg-slate-100">
                <div class="h-full bg-gradient-to-r from-emerald-400 to-green-600 w-full rounded-r-full shadow-[0_0_10px_rgba(16,185,129,0.5)]"></div>
            </div>
        </div>

        <form action="{{ route('pendaftaran.store') }}" method="POST" class="bg-white rounded-b-3xl shadow-xl p-6 sm:p-10 space-y-10">
            @csrf
            <input type="hidden" name="token" value="{{ $token ?? '' }}">

            {{-- SECTION A: DATA PRIBADI --}}
            <section>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-lg">A</div>
                    <h2 class="text-xl font-bold text-slate-800">Data Pribadi Santri</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    {{-- Jenjang Selection (Card Style) --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">Pilih Jenjang Pendidikan</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <label class="cursor-pointer relative">
                                <input type="radio" name="jenjang" value="SMP" required class="radio-input">
                                <div class="radio-content flex items-center gap-3 p-4 rounded-xl border-2 border-slate-100 bg-white hover:border-emerald-200 transition-all duration-200">
                                    <div class="radio-icon w-10 h-10 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center transition-colors">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                    </div>
                                    <div>
                                        <span class="block font-bold text-slate-800">SMP (Reguler)</span>
                                        <span class="text-xs text-slate-500">Sekolah Menengah Pertama</span>
                                    </div>
                                </div>
                            </label>
                            <label class="cursor-pointer relative">
                                <input type="radio" name="jenjang" value="SMK" class="radio-input">
                                <div class="radio-content flex items-center gap-3 p-4 rounded-xl border-2 border-slate-100 bg-white hover:border-emerald-200 transition-all duration-200">
                                    <div class="radio-icon w-10 h-10 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center transition-colors">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                                    </div>
                                    <div>
                                        <span class="block font-bold text-slate-800">SMK (Kejuruan)</span>
                                        <span class="text-xs text-slate-500">Sekolah Menengah Kejuruan</span>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Nama Lengkap --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">Nama Lengkap</label>
                        <div class="relative">
                            <input type="text" name="nama_lengkap" class="custom-input w-full pl-12 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-slate-800 placeholder-slate-400 transition-all outline-none font-medium" placeholder="Contoh: Muhammad Rizky" required>
                            <svg class="input-icon absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 w-5 h-5 transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                    </div>

                    {{-- Jenis Kelamin (Card Style) --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">Jenis Kelamin</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="cursor-pointer relative">
                                <input type="radio" name="jenis_kelamin" value="L" required class="radio-input">
                                <div class="radio-content flex flex-col items-center justify-center text-center py-4 rounded-xl border-2 border-slate-100 bg-white hover:border-emerald-200 transition-all">
                                    <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mb-1 transition-colors">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    </div>
                                    <span class="font-bold text-slate-700">Laki-laki</span>
                                </div>
                            </label>
                            <label class="cursor-pointer relative">
                                <input type="radio" name="jenis_kelamin" value="P" class="radio-input">
                                <div class="radio-content flex flex-col items-center justify-center text-center py-4 rounded-xl border-2 border-slate-100 bg-white hover:border-emerald-200 transition-all">
                                    <div class="w-12 h-12 rounded-full bg-pink-50 text-pink-600 flex items-center justify-center mb-1 transition-colors">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    </div>
                                    <span class="font-bold text-slate-700">Perempuan</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Detail Kelahiran --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">Tempat Lahir</label>
                        <div class="relative">
                            <input type="text" name="tempat_lahir" class="custom-input w-full pl-12 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-slate-800 placeholder-slate-400 transition-all outline-none font-medium" placeholder="Kota Kelahiran" required>
                            <svg class="input-icon absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 w-5 h-5 transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">Tanggal Lahir</label>
                        <div class="relative">
                            <input type="date" name="tanggal_lahir" class="custom-input w-full pl-12 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-slate-800 placeholder-slate-400 transition-all outline-none font-medium" required>
                            <svg class="input-icon absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 w-5 h-5 transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    </div>

                    {{-- NISN & NIK --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">NISN</label>
                        <div class="relative">
                            <input type="number" name="nisn" class="custom-input w-full pl-12 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-slate-800 placeholder-slate-400 transition-all outline-none font-medium" placeholder="Nomor Induk Siswa" required>
                            <svg class="input-icon absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 w-5 h-5 transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">NIK</label>
                        <div class="relative">
                            <input type="number" name="nik" class="custom-input w-full pl-12 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-slate-800 placeholder-slate-400 transition-all outline-none font-medium" placeholder="NIK (Sesuai KK)" required>
                            <svg class="input-icon absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 w-5 h-5 transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        </div>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">Asal Sekolah</label>
                        <div class="relative">
                            <input type="text" name="asal_sekolah" class="custom-input w-full pl-12 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-slate-800 placeholder-slate-400 transition-all outline-none font-medium" placeholder="Nama Sekolah Asal" required>
                            <svg class="input-icon absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 w-5 h-5 transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                    </div>

                    {{-- Data Tambahan --}}
                    <div class="grid grid-cols-3 gap-4 md:col-span-2">
                         <div class="col-span-1">
                             <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">No. KK</label>
                             <input type="number" name="no_kk" class="custom-input w-full px-4 text-center py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none" placeholder="No. KK" required>
                         </div>
                         <div class="col-span-1">
                             <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">Anak Ke</label>
                             <input type="number" name="anak_ke" class="custom-input w-full px-4 text-center py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none" placeholder="1">
                         </div>
                         <div class="col-span-1">
                             <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">Jml Saudara</label>
                             <input type="number" name="jumlah_saudara" class="custom-input w-full px-4 text-center py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none" placeholder="2">
                         </div>
                    </div>
                </div>
            </section>

            <hr class="border-dashed border-slate-200">

            {{-- SECTION B: ALAMAT --}}
            <section>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-lg">B</div>
                    <h2 class="text-xl font-bold text-slate-800">Alamat Domisili</h2>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">Alamat Lengkap</label>
                        <div class="relative">
                            <textarea name="alamat" rows="2" class="custom-input w-full pl-12 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none resize-none" placeholder="Nama Jalan, Dusun, atau Patokan Rumah" required></textarea>
                            <svg class="input-icon absolute left-4 top-6 text-slate-400 w-5 h-5 transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">RT</label>
                            <input type="number" name="rt" class="custom-input w-full px-3 text-center py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none" placeholder="001">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">RW</label>
                            <input type="number" name="rw" class="custom-input w-full px-3 text-center py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none" placeholder="002">
                        </div>
                        <div class="col-span-2">
                             <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">Kode Pos</label>
                             <input type="number" name="kode_pos" class="custom-input w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none" placeholder="45218">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">Desa / Kelurahan</label>
                            <input type="text" name="desa" class="custom-input w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">Kecamatan</label>
                            <input type="text" name="kecamatan" class="custom-input w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">Kabupaten / Kota</label>
                            <input type="text" name="kabupaten" class="custom-input w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">Provinsi</label>
                            <input type="text" name="provinsi" class="custom-input w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none" required>
                        </div>
                    </div>
                </div>
            </section>

            <hr class="border-dashed border-slate-200">

            {{-- SECTION C: ORANG TUA --}}
            <section>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center font-bold text-lg">C</div>
                    <h2 class="text-xl font-bold text-slate-800">Data Orang Tua</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- AYAH CARD --}}
                    <div class="bg-blue-50/50 rounded-2xl p-5 border border-blue-100 relative group hover:border-blue-300 transition-colors">
                        <div class="absolute -top-3 left-4 bg-blue-100 text-blue-700 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wide">
                            Ayah Kandung
                        </div>
                        <div class="mt-2 space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-blue-800 uppercase tracking-wider mb-2 ml-1">Nama Ayah</label>
                                <input type="text" name="nama_ayah" class="w-full px-4 py-3 bg-white border border-blue-200 rounded-xl focus:ring-2 focus:ring-blue-400 outline-none" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-blue-800 uppercase tracking-wider mb-2 ml-1">NIK Ayah</label>
                                <input type="number" name="nik_ayah" class="w-full px-4 py-3 bg-white border border-blue-200 rounded-xl focus:ring-2 focus:ring-blue-400 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-blue-800 uppercase tracking-wider mb-2 ml-1">Pekerjaan</label>
                                <input type="text" name="pekerjaan_ayah" class="w-full px-4 py-3 bg-white border border-blue-200 rounded-xl focus:ring-2 focus:ring-blue-400 outline-none" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-blue-800 uppercase tracking-wider mb-2 ml-1">No. HP / WA</label>
                                <input type="number" name="no_hp_ayah" class="w-full px-4 py-3 bg-white border border-blue-200 rounded-xl focus:ring-2 focus:ring-blue-400 outline-none" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-blue-800 uppercase tracking-wider mb-2 ml-1">Penghasilan (Rp)</label>
                                <input type="text" name="penghasilan_ayah" class="w-full px-4 py-3 bg-white border border-blue-200 rounded-xl focus:ring-2 focus:ring-blue-400 outline-none" placeholder="Contoh: 3.000.000">
                            </div>
                        </div>
                    </div>

                    {{-- IBU CARD --}}
                    <div class="bg-pink-50/50 rounded-2xl p-5 border border-pink-100 relative group hover:border-pink-300 transition-colors">
                        <div class="absolute -top-3 left-4 bg-pink-100 text-pink-700 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wide">
                            Ibu Kandung
                        </div>
                        <div class="mt-2 space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-pink-800 uppercase tracking-wider mb-2 ml-1">Nama Ibu</label>
                                <input type="text" name="nama_ibu" class="w-full px-4 py-3 bg-white border border-pink-200 rounded-xl focus:ring-2 focus:ring-pink-400 outline-none" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-pink-800 uppercase tracking-wider mb-2 ml-1">NIK Ibu</label>
                                <input type="number" name="nik_ibu" class="w-full px-4 py-3 bg-white border border-pink-200 rounded-xl focus:ring-2 focus:ring-pink-400 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-pink-800 uppercase tracking-wider mb-2 ml-1">Pekerjaan</label>
                                <input type="text" name="pekerjaan_ibu" class="w-full px-4 py-3 bg-white border border-pink-200 rounded-xl focus:ring-2 focus:ring-pink-400 outline-none" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-pink-800 uppercase tracking-wider mb-2 ml-1">No. HP / WA</label>
                                <input type="number" name="no_hp_ibu" class="w-full px-4 py-3 bg-white border border-pink-200 rounded-xl focus:ring-2 focus:ring-pink-400 outline-none" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-pink-800 uppercase tracking-wider mb-2 ml-1">Penghasilan (Rp)</label>
                                <input type="text" name="penghasilan_ibu" class="w-full px-4 py-3 bg-white border border-pink-200 rounded-xl focus:ring-2 focus:ring-pink-400 outline-none" placeholder="Contoh: 0">
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- SUBMIT --}}
            <div class="pt-6">
                <button type="submit" class="group relative w-full flex justify-center py-4 px-4 border border-transparent text-lg font-bold rounded-2xl text-white bg-slate-900 hover:bg-emerald-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 shadow-xl transition-all duration-300 hover:scale-[1.01]">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-6 w-6 text-white/50 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </span>
                    Simpan & Daftar Sekarang
                </button>
                <p class="text-center text-xs text-slate-400 mt-4">
                    Data yang dikirim akan disimpan dengan aman dan digunakan untuk keperluan administrasi PPDB.
                </p>
            </div>

        </form>
    </div>

</body>
</html>