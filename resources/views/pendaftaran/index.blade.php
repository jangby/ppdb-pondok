<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PPDB Online Pondok Pesantren</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">

    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <div class="bg-green-600 rounded-t-lg p-6 text-center text-white">
            <h1 class="text-3xl font-bold">Formulir Pendaftaran Santri Baru</h1>
            <p class="mt-2">Silakan isi data dengan benar dan lengkap</p>
        </div>

        <div class="bg-white rounded-b-lg shadow-lg p-6 md:p-10">
            
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <strong>Terjadi Kesalahan!</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>- {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('pendaftaran.store') }}" method="POST">
                @csrf

                <div class="mb-8 border-b pb-4">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">A. Data Santri</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="w-full mt-1 p-2 border rounded-md focus:ring-green-500 focus:border-green-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Jenjang</label>
                            <select name="jenjang" class="w-full mt-1 p-2 border rounded-md" required>
                                <option value="SMP">SMP</option>
                                <option value="SMK">SMK</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">NISN</label>
                            <input type="text" name="nisn" class="w-full mt-1 p-2 border rounded-md" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">NIK</label>
                            <input type="text" name="nik" class="w-full mt-1 p-2 border rounded-md" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">No KK</label>
                            <input type="text" name="no_kk" class="w-full mt-1 p-2 border rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="w-full mt-1 p-2 border rounded-md" required>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="w-full mt-1 p-2 border rounded-md" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="w-full mt-1 p-2 border rounded-md" required>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Anak Ke-</label>
                                <input type="number" name="anak_ke" class="w-full mt-1 p-2 border rounded-md" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Jml Saudara</label>
                                <input type="number" name="jumlah_saudara" class="w-full mt-1 p-2 border rounded-md" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Riwayat Penyakit</label>
                            <input type="text" name="riwayat_penyakit" class="w-full mt-1 p-2 border rounded-md" placeholder="-">
                        </div>
                    </div>
                </div>

                <div class="mb-8 border-b pb-4">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">B. Data Alamat</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-600">Alamat (Kp/Jalan)</label>
                            <textarea name="alamat" class="w-full mt-1 p-2 border rounded-md" rows="2" required></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-600">RT</label>
                                <input type="text" name="rt" class="w-full mt-1 p-2 border rounded-md">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">RW</label>
                                <input type="text" name="rw" class="w-full mt-1 p-2 border rounded-md">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Desa/Kelurahan</label>
                            <input type="text" name="desa" class="w-full mt-1 p-2 border rounded-md" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Kecamatan</label>
                            <input type="text" name="kecamatan" class="w-full mt-1 p-2 border rounded-md" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Kode Pos</label>
                            <input type="text" name="kode_pos" class="w-full mt-1 p-2 border rounded-md">
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">C. Data Orang Tua</h2>
                    
                    <h3 class="font-bold text-gray-500 mt-2">Data Ayah</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <input type="text" name="nama_ayah" placeholder="Nama Ayah" class="p-2 border rounded-md" required>
                        <input type="text" name="nik_ayah" placeholder="NIK Ayah" class="p-2 border rounded-md">
                        <input type="text" name="thn_lahir_ayah" placeholder="Thn Lahir" class="p-2 border rounded-md">
                        <input type="text" name="pekerjaan_ayah" placeholder="Pekerjaan" class="p-2 border rounded-md">
                        <input type="text" name="no_hp_ayah" placeholder="No HP/WA" class="p-2 border rounded-md">
                    </div>

                    <h3 class="font-bold text-gray-500 mt-2">Data Ibu</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="text" name="nama_ibu" placeholder="Nama Ibu" class="p-2 border rounded-md" required>
                        <input type="text" name="nik_ibu" placeholder="NIK Ibu" class="p-2 border rounded-md">
                        <input type="text" name="thn_lahir_ibu" placeholder="Thn Lahir" class="p-2 border rounded-md">
                        <input type="text" name="pekerjaan_ibu" placeholder="Pekerjaan" class="p-2 border rounded-md">
                        <input type="text" name="no_hp_ibu" placeholder="No HP/WA" class="p-2 border rounded-md">
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg shadow-lg transition duration-300">
                        Kirim Pendaftaran
                    </button>
                </div>

            </form>
        </div>
        <p class="text-center text-gray-500 text-sm mt-4">&copy; {{ date('Y') }} Pondok Pesantren.</p>
    </div>

</body>
</html>