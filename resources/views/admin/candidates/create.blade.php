<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Pendaftaran Santri Baru</title>
    {{-- Menggunakan CDN Tailwind agar tampilan bagus tanpa compile --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 text-gray-800">

    <nav class="bg-white shadow-sm w-full z-50 sticky top-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center gap-2">
                    {{-- Tombol Kembali ke Halaman Depan --}}
                    <a href="{{ route('home') }}" class="flex items-center gap-2 text-decoration-none group">
                        <div class="w-8 h-8 bg-green-100 text-green-600 rounded-lg flex items-center justify-center font-bold group-hover:bg-green-600 group-hover:text-white transition">
                            &larr;
                        </div>
                        <span class="font-bold text-lg text-green-800 group-hover:text-green-600 transition">Kembali ke Beranda</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Formulir Pendaftaran Online</h1>
                <p class="text-gray-600 mt-2">Silakan isi data diri calon santri dengan benar dan lengkap.</p>
            </div>

            {{-- Tampilkan Error Validasi --}}
            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded shadow-sm">
                    <div class="flex">
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Gagal Menyimpan! Periksa inputan berikut:</h3>
                            <ul class="list-disc pl-5 mt-1 text-sm text-red-700">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- FORM --}}
            <form action="{{ route('pendaftaran.store') }}" method="POST" class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
                @csrf

                {{-- A. DATA CALON SANTRI --}}
                <div class="bg-green-600 px-6 py-4 border-b border-green-700">
                    <h3 class="font-bold text-lg text-white flex items-center gap-2">
                        A. Data Calon Santri
                    </h3>
                </div>
                
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap (Sesuai Ijazah/KK) <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 p-2 border" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NISN</label>
                        <input type="text" name="nisn" value="{{ old('nisn') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 p-2 border">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
                        <input type="text" name="nik" value="{{ old('nik') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 p-2 border">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenjang <span class="text-red-500">*</span></label>
                        <select name="jenjang" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 p-2 border" required>
                            <option value="">Pilih Jenjang</option>
                            <option value="SD" {{ old('jenjang') == 'SD' ? 'selected' : '' }}>SD</option>
                            <option value="SMP" {{ old('jenjang') == 'SMP' ? 'selected' : '' }}>SMP</option>
                            <option value="SMA" {{ old('jenjang') == 'SMA' ? 'selected' : '' }}>SMA</option>
                            <option value="SMK" {{ old('jenjang') == 'SMK' ? 'selected' : '' }}>SMK</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
                        <select name="jenis_kelamin" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 p-2 border" required>
                            <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 p-2 border">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 p-2 border">
                    </div>
                    
                    <div>
                         <label class="block text-sm font-medium text-gray-700 mb-1">No. KK</label>
                         <input type="text" name="no_kk" value="{{ old('no_kk') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 p-2 border">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Asal Sekolah</label>
                        <input type="text" name="asal_sekolah" value="{{ old('asal_sekolah') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 p-2 border">
                    </div>
                </div>

                {{-- B. ALAMAT --}}
                <div class="bg-green-50 px-6 py-3 border-y border-green-100">
                    <h3 class="font-bold text-md text-green-800">B. Alamat Domisili</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap (Jalan/Dusun)</label>
                        <textarea name="alamat" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 p-2 border">{{ old('alamat') }}</textarea>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-1/2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">RT</label>
                            <input type="text" name="rt" value="{{ old('rt') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 p-2 border">
                        </div>
                        <div class="w-1/2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">RW</label>
                            <input type="text" name="rw" value="{{ old('rw') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 p-2 border">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Desa/Kelurahan</label>
                        <input type="text" name="desa" value="{{ old('desa') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 p-2 border">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
                        <input type="text" name="kecamatan" value="{{ old('kecamatan') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 p-2 border">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kabupaten/Kota</label>
                        <input type="text" name="kabupaten" value="{{ old('kabupaten') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 p-2 border">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
                        <input type="text" name="provinsi" value="{{ old('provinsi') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 p-2 border">
                    </div>
                     <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode Pos</label>
                        <input type="text" name="kode_pos" value="{{ old('kode_pos') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 p-2 border">
                    </div>
                </div>

                {{-- C. ORANG TUA --}}
                <div class="bg-green-50 px-6 py-3 border-y border-green-100">
                    <h3 class="font-bold text-md text-green-800">C. Data Orang Tua</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8 divide-y md:divide-y-0 md:divide-x divide-gray-200">
                    {{-- Ayah --}}
                    <div>
                        <h4 class="font-bold text-gray-500 text-sm border-b pb-2 mb-3">DATA AYAH</h4>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Ayah</label>
                            <input type="text" name="nama_ayah" value="{{ old('nama_ayah') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 p-2 border">
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">NIK Ayah</label>
                            <input type="text" name="nik_ayah" value="{{ old('nik_ayah') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 p-2 border">
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. HP Ayah</label>
                            <input type="text" name="no_hp_ayah" value="{{ old('no_hp_ayah') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 p-2 border">
                        </div>
                         <div class="grid grid-cols-2 gap-2 mb-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Thn Lahir</label>
                                <input type="number" name="thn_lahir_ayah" class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2 border">
                            </div>
                             <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Pendidikan</label>
                                <select name="pendidikan_ayah" class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2 border">
                                    <option value="">- Pilih -</option>
                                    <option value="SD">SD</option>
                                    <option value="SMP">SMP</option>
                                    <option value="SMA">SMA</option>
                                    <option value="S1">S1</option>
                                    <option value="S2">S2</option>
                                    <option value="S3">S3</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                        </div>
                         <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan Ayah</label>
                            <input type="text" name="pekerjaan_ayah" value="{{ old('pekerjaan_ayah') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 p-2 border">
                        </div>
                    </div>

                    {{-- Ibu --}}
                    <div class="md:pl-4 md:pt-0 pt-4">
                        <h4 class="font-bold text-gray-500 text-sm border-b pb-2 mb-3">DATA IBU</h4>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Ibu</label>
                            <input type="text" name="nama_ibu" value="{{ old('nama_ibu') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 p-2 border">
                        </div>
                        <div class="mb-3">
                             <label class="block text-sm font-medium text-gray-700 mb-1">NIK Ibu</label>
                            <input type="text" name="nik_ibu" value="{{ old('nik_ibu') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 p-2 border">
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. HP Ibu</label>
                            <input type="text" name="no_hp_ibu" value="{{ old('no_hp_ibu') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 p-2 border">
                        </div>
                        <div class="grid grid-cols-2 gap-2 mb-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Thn Lahir</label>
                                <input type="number" name="thn_lahir_ibu" class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2 border">
                            </div>
                             <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Pendidikan</label>
                                <select name="pendidikan_ibu" class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2 border">
                                    <option value="">- Pilih -</option>
                                    <option value="SD">SD</option>
                                    <option value="SMP">SMP</option>
                                    <option value="SMA">SMA</option>
                                    <option value="S1">S1</option>
                                    <option value="S2">S2</option>
                                    <option value="S3">S3</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                        </div>
                         <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan Ibu</label>
                            <input type="text" name="pekerjaan_ibu" value="{{ old('pekerjaan_ibu') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 p-2 border">
                        </div>
                    </div>
                </div>

                {{-- TOMBOL SUBMIT --}}
                <div class="bg-gray-50 px-6 py-6 border-t border-gray-100 flex justify-end">
                    <button type="submit" class="px-8 py-3 bg-green-600 text-white font-bold rounded-lg text-lg hover:bg-green-700 transition shadow-lg flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Kirim Pendaftaran
                    </button>
                </div>

            </form>
        </div>
    </div>

    <footer class="bg-gray-900 text-gray-400 py-8 text-center mt-10">
        <p>&copy; {{ date('Y') }} Ponpes Al-Hidayah. All rights reserved.</p>
    </footer>

</body>
</html>