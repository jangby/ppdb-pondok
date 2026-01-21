<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            Pengaturan PPDB
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p class="font-bold">Berhasil!</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-6 border-b pb-4">
                            <h3 class="text-lg font-medium text-gray-900">Status Pendaftaran</h3>
                            <p class="text-sm text-gray-600 mb-4">Atur apakah pendaftaran dibuka atau ditutup.</p>
                            
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_open" value="1" class="sr-only peer" {{ old('is_open', $setting->is_open ?? false) ? 'checked' : '' }}>
                                <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-indigo-600"></div>
                                <span class="ml-3 text-sm font-medium text-gray-900">Buka Pendaftaran (Aktif)</span>
                            </label>
                            <p class="text-xs text-gray-500 mt-2">*Jika dimatikan, pendaftaran akan tutup meskipun tanggal masih berlaku.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Gelombang</label>
                                <input type="text" name="nama_gelombang" value="{{ old('nama_gelombang', $setting->nama_gelombang ?? 'Gelombang 1') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Contoh: Gelombang 1 Tahun 2026">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Buka Otomatis</label>
                                <input type="date" name="tanggal_buka" value="{{ old('tanggal_buka', $setting->tanggal_buka ?? '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Tutup Otomatis</label>
                                <input type="date" name="tanggal_tutup" value="{{ old('tanggal_tutup', $setting->tanggal_tutup ?? '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pengumuman / Informasi (Opsional)</label>
                            <textarea name="pengumuman" rows="4" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Informasi tambahan untuk calon santri...">{{ old('pengumuman', $setting->pengumuman ?? '') }}</textarea>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700 transition">
                                Simpan Pengaturan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>