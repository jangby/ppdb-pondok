<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Santri Lanjutan (Internal)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('admin.candidates.lanjutan.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">NIS Santri Lanjutan</label>
                            <input type="text" name="nis_lokal" class="form-input w-full rounded-md shadow-sm" required placeholder="Contoh: 123456">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-input w-full rounded-md shadow-sm" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select w-full rounded-md shadow-sm" required>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Jenjang Tujuan</label>
                            <select name="jenjang" class="form-select w-full rounded-md shadow-sm" required>
                                <option value="" disabled selected>-- Pilih Jenjang --</option>
                                @php
                                    // Mengambil data dari tabel settings
                                    $setting = \App\Models\Setting::first();
                                    
                                    // Memastikan formatnya array (jika kosong diatur fallback ke SMP/SMK)
                                    $jenjang_tersedia = $setting && is_array($setting->jenjang_tersedia) 
                                                        ? $setting->jenjang_tersedia 
                                                        : ['SMA'];
                                @endphp
                                
                                {{-- Looping data jenjang dari database --}}
                                @foreach($jenjang_tersedia as $jnj)
                                    <option value="{{ $jnj }}">{{ $jnj }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Daftarkan & Generate Tagihan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>