<x-guest-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8 text-center">
                
                <div class="mb-6 flex justify-center">
                    <div class="bg-red-100 p-4 rounded-full">
                        <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                </div>

                <h2 class="text-3xl font-bold text-gray-800 mb-2">Pendaftaran Ditutup</h2>
                <p class="text-gray-600 mb-6">
                    Mohon maaf, pendaftaran santri baru 
                    <strong>{{ $setting->nama_gelombang ?? '' }}</strong> 
                    saat ini sedang tidak aktif.
                </p>

                @if(!empty($setting->pengumuman))
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6 text-sm text-yellow-800">
                        <strong>Informasi:</strong><br>
                        {{ $setting->pengumuman }}
                    </div>
                @endif

                <a href="{{ url('/') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>