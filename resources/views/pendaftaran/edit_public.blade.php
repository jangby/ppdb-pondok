<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Biodata Santri - {{ $candidate->nama_lengkap }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .radio-input { position: absolute; opacity: 0; width: 0; height: 0; }
        .radio-input:checked + .radio-content {
            border-color: #10b981; background-color: rgba(16, 185, 129, 0.05); box-shadow: 0 0 0 1px #10b981;
        }
        .custom-input:focus ~ .input-icon { color: #059669; transform: translateY(-50%) scale(1.1); }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-8 px-4 sm:py-12">

    <div class="fixed inset-0 z-0 pointer-events-none">
        <div class="absolute top-0 left-0 w-full h-[500px] bg-gradient-to-b from-emerald-700 to-slate-100"></div>
    </div>

    <div class="relative z-10 max-w-5xl mx-auto">
        
        {{-- HEADER --}}
        <div class="bg-white rounded-t-3xl shadow-xl overflow-hidden mb-1">
            <div class="relative bg-slate-900 p-8 sm:p-10 text-center overflow-hidden">
                <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
                <div class="relative z-10">
                    <span class="inline-block py-1 px-3 rounded-full bg-white/10 border border-white/20 text-emerald-300 text-[10px] font-bold uppercase tracking-widest mb-4">Mode Edit Mandiri</span>
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight mb-2">Edit Biodata Santri</h1>
                    <p class="text-slate-400 text-sm max-w-lg mx-auto">Perbarui data Anda dengan teliti sesuai dokumen resmi.</p>
                </div>
            </div>
            <div class="h-1.5 w-full bg-slate-100">
                <div class="h-full bg-gradient-to-r from-emerald-400 to-green-600 w-full rounded-r-full"></div>
            </div>
        </div>

        <form action="{{ route('pendaftaran.update_public', $candidate->no_daftar) }}" method="POST" class="bg-white rounded-b-3xl shadow-xl p-6 sm:p-10 space-y-10">
            @csrf
            @method('PUT')

            {{-- ALERT ERROR --}}
            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan:</h3>
                            <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- SECTION A: DATA PRIBADI --}}
            <section>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-lg">A</div>
                    <h2 class="text-xl font-bold text-slate-800">Data Pribadi Santri</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Jenjang --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">Pilih Jenjang Pendidikan</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($validJenjang as $item)
                                <label class="cursor-pointer relative">
                                    <input type="radio" name="jenjang" value="{{ $item }}" {{ old('jenjang', $candidate->jenjang) == $item ? 'checked' : '' }} required class="radio-input">
                                    <div class="radio-content flex items-center gap-3 p-4 rounded-xl border-2 border-slate-100 bg-white hover:border-emerald-200 transition-all">
                                        <div class="radio-icon w-10 h-10 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-lg">{{ substr($item, 0, 1) }}</div>
                                        <div>
                                            <span class="block font-bold text-slate-800">{{ $item }}</span>
                                            <span class="text-xs text-slate-500">Program Pendidikan</span>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Nama Lengkap --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">Nama Lengkap</label>
                        <div class="relative">
                            <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $candidate->nama_lengkap) }}" class="custom-input w-full pl-12 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none" required>
                            <svg class="input-icon absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                    </div>

                    {{-- Jenis Kelamin --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">Jenis Kelamin</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="cursor-pointer relative">
                                <input type="radio" name="jenis_kelamin" value="L" {{ old('jenis_kelamin', $candidate->jenis_kelamin) == 'L' ? 'checked' : '' }} required class="radio-input">
                                <div class="radio-content flex flex-col items-center justify-center text-center py-4 rounded-xl border-2 border-slate-100 bg-white hover:border-emerald-200">
                                    <span class="font-bold text-slate-700">Laki-laki</span>
                                </div>
                            </label>
                            <label class="cursor-pointer relative">
                                <input type="radio" name="jenis_kelamin" value="P" {{ old('jenis_kelamin', $candidate->jenis_kelamin) == 'P' ? 'checked' : '' }} class="radio-input">
                                <div class="radio-content flex flex-col items-center justify-center text-center py-4 rounded-xl border-2 border-slate-100 bg-white hover:border-emerald-200">
                                    <span class="font-bold text-slate-700">Perempuan</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- TTL --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $candidate->tempat_lahir) }}" class="custom-input w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $candidate->tanggal_lahir) }}" class="custom-input w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none" required>
                    </div>

                    {{-- NISN & NIK (Readonly) --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">NISN (Terkunci)</label>
                        <input type="number" name="nisn" value="{{ old('nisn', $candidate->nisn) }}" class="w-full px-4 py-3.5 bg-slate-100 border border-slate-200 rounded-xl outline-none text-slate-500 font-bold" readonly>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">NIK (Terkunci)</label>
                        <input type="number" name="nik" value="{{ old('nik', $candidate->nik) }}" class="w-full px-4 py-3.5 bg-slate-100 border border-slate-200 rounded-xl outline-none text-slate-500 font-bold" readonly>
                    </div>
                    
                    {{-- Asal Sekolah --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">Asal Sekolah</label>
                        <input type="text" name="asal_sekolah" value="{{ old('asal_sekolah', $candidate->asal_sekolah) }}" class="custom-input w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none" required>
                    </div>

                    {{-- No KK, Anak Ke, Jml Saudara --}}
                    <div class="grid grid-cols-3 gap-4 md:col-span-2">
                         <div class="col-span-1">
                             <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">No. KK</label>
                             <input type="number" name="no_kk" value="{{ old('no_kk', $candidate->no_kk) }}" class="custom-input w-full px-4 text-center py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none" required>
                         </div>
                         <div class="col-span-1">
                             <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">Anak Ke</label>
                             <input type="number" name="anak_ke" value="{{ old('anak_ke', $candidate->anak_ke) }}" class="custom-input w-full px-4 text-center py-3.5 bg-slate-50 border border-slate-200 rounded-xl outline-none">
                         </div>
                         <div class="col-span-1">
                             <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">Jml Saudara</label>
                             <input type="number" name="jumlah_saudara" value="{{ old('jumlah_saudara', $candidate->jumlah_saudara) }}" class="custom-input w-full px-4 text-center py-3.5 bg-slate-50 border border-slate-200 rounded-xl outline-none">
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
                        <textarea name="alamat" rows="2" class="custom-input w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none" required>{{ old('alamat', $candidate->address->alamat) }}</textarea>
                    </div>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div><label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">RT</label><input type="number" name="rt" value="{{ old('rt', $candidate->address->rt) }}" class="custom-input w-full px-3 py-3.5 bg-slate-50 border border-slate-200 rounded-xl outline-none"></div>
                        <div><label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">RW</label><input type="number" name="rw" value="{{ old('rw', $candidate->address->rw) }}" class="custom-input w-full px-3 py-3.5 bg-slate-50 border border-slate-200 rounded-xl outline-none"></div>
                        <div class="col-span-2"><label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">Kode Pos</label><input type="number" name="kode_pos" value="{{ old('kode_pos', $candidate->address->kode_pos) }}" class="custom-input w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl outline-none"></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">Desa / Kelurahan</label><input type="text" name="desa" value="{{ old('desa', $candidate->address->desa) }}" class="custom-input w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl outline-none" required></div>
                        <div><label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">Kecamatan</label><input type="text" name="kecamatan" value="{{ old('kecamatan', $candidate->address->kecamatan) }}" class="custom-input w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl outline-none" required></div>
                        <div><label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">Kabupaten</label><input type="text" name="kabupaten" value="{{ old('kabupaten', $candidate->address->kabupaten) }}" class="custom-input w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl outline-none" required></div>
                        <div><label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">Provinsi</label><input type="text" name="provinsi" value="{{ old('provinsi', $candidate->address->provinsi) }}" class="custom-input w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl outline-none" required></div>
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
                    {{-- AYAH --}}
                    <div class="bg-blue-50/50 rounded-2xl p-5 border border-blue-100">
                        <div class="mt-2 space-y-4">
                            <div><label class="text-xs font-bold text-blue-800 uppercase">Nama Ayah</label><input type="text" name="nama_ayah" value="{{ old('nama_ayah', $candidate->parent->nama_ayah) }}" class="w-full px-4 py-3 bg-white border border-blue-200 rounded-xl outline-none" required></div>
                            <div><label class="text-xs font-bold text-blue-800 uppercase">NIK Ayah</label><input type="number" name="nik_ayah" value="{{ old('nik_ayah', $candidate->parent->nik_ayah) }}" class="w-full px-4 py-3 bg-white border border-blue-200 rounded-xl outline-none"></div>
                            <div><label class="text-xs font-bold text-blue-800 uppercase">Pekerjaan</label><input type="text" name="pekerjaan_ayah" value="{{ old('pekerjaan_ayah', $candidate->parent->pekerjaan_ayah) }}" class="w-full px-4 py-3 bg-white border border-blue-200 rounded-xl outline-none" required></div>
                            <div><label class="text-xs font-bold text-blue-800 uppercase">No. HP / WA</label><input type="number" name="no_hp_ayah" value="{{ old('no_hp_ayah', $candidate->parent->no_hp_ayah) }}" class="w-full px-4 py-3 bg-white border border-blue-200 rounded-xl outline-none" required></div>
                            <div><label class="text-xs font-bold text-blue-800 uppercase">Penghasilan (Rp)</label><input type="text" name="penghasilan_ayah" value="{{ old('penghasilan_ayah', $candidate->parent->penghasilan_ayah) }}" class="w-full px-4 py-3 bg-white border border-blue-200 rounded-xl outline-none"></div>
                        </div>
                    </div>

                    {{-- IBU --}}
                    <div class="bg-pink-50/50 rounded-2xl p-5 border border-pink-100">
                        <div class="mt-2 space-y-4">
                            <div><label class="text-xs font-bold text-pink-800 uppercase">Nama Ibu</label><input type="text" name="nama_ibu" value="{{ old('nama_ibu', $candidate->parent->nama_ibu) }}" class="w-full px-4 py-3 bg-white border border-pink-200 rounded-xl outline-none" required></div>
                            <div><label class="text-xs font-bold text-pink-800 uppercase">NIK Ibu</label><input type="number" name="nik_ibu" value="{{ old('nik_ibu', $candidate->parent->nik_ibu) }}" class="w-full px-4 py-3 bg-white border border-pink-200 rounded-xl outline-none"></div>
                            <div><label class="text-xs font-bold text-pink-800 uppercase">Pekerjaan</label><input type="text" name="pekerjaan_ibu" value="{{ old('pekerjaan_ibu', $candidate->parent->pekerjaan_ibu) }}" class="w-full px-4 py-3 bg-white border border-pink-200 rounded-xl outline-none" required></div>
                            <div><label class="text-xs font-bold text-pink-800 uppercase">No. HP / WA</label><input type="number" name="no_hp_ibu" value="{{ old('no_hp_ibu', $candidate->parent->no_hp_ibu) }}" class="w-full px-4 py-3 bg-white border border-pink-200 rounded-xl outline-none" required></div>
                            <div><label class="text-xs font-bold text-pink-800 uppercase">Penghasilan (Rp)</label><input type="text" name="penghasilan_ibu" value="{{ old('penghasilan_ibu', $candidate->parent->penghasilan_ibu) }}" class="w-full px-4 py-3 bg-white border border-pink-200 rounded-xl outline-none"></div>
                        </div>
                    </div>
                </div>
            </section>

            <div class="pt-6 flex flex-col sm:flex-row gap-4">
                <button type="submit" class="flex-1 py-4 px-4 text-lg font-bold rounded-2xl text-white bg-slate-900 hover:bg-emerald-600 shadow-xl transition-all">Simpan Perubahan</button>
                <a href="{{ route('public.finance.show', $candidate->no_daftar) }}" class="flex-1 py-4 px-4 text-lg font-bold rounded-2xl text-slate-500 bg-slate-100 hover:bg-slate-200 text-center transition-all">Batal</a>
            </div>

        </form>
    </div>
</body>
</html>