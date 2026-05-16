<x-guest-layout>
    <div class="max-w-3xl mx-auto p-6 text-center space-y-8 py-12">
        
        {{-- Ikon Ceklis Besar --}}
        <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-green-100 shadow-sm border-4 border-white ring-4 ring-green-50">
            <svg class="h-12 w-12 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
            </svg>
        </div>

        {{-- Pesan Menenangkan --}}
        <div>
            <h2 class="text-3xl font-extrabold text-gray-900 mb-2 tracking-tight">Data Berhasil Diterima!</h2>
            <p class="text-gray-500 text-base md:text-lg">Alhamdulillah, data diri Calon Santri dan Orang Tua sudah <b class="text-green-600">berhasil tersimpan</b> di sistem kami.</p>
        </div>

        {{-- Alert Jika Mereka Klik Link WA Lagi --}}
        @if(session('info'))
            <div class="bg-blue-50 border border-blue-200 text-blue-700 px-5 py-3 rounded-xl inline-flex items-center gap-2 shadow-sm animate-bounce-short">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="font-medium text-sm">{{ session('info') }}</span>
            </div>
        @endif

        {{-- Kotak Nomor Pendaftaran --}}
        <div class="bg-white border-2 border-dashed border-gray-300 rounded-2xl p-6 max-w-sm mx-auto shadow-sm">
            <p class="text-xs text-gray-500 font-bold uppercase tracking-widest mb-1">Nomor Pendaftaran</p>
            <p class="text-4xl font-black text-indigo-600 tracking-widest">{{ $candidate->no_daftar ?? $no_daftar }}</p>
            <p class="text-xs text-gray-400 mt-2 italic">Harap simpan nomor ini dengan baik.</p>
        </div>

        {{-- Kotak Redirect Edukasi --}}
        <div class="bg-indigo-50 rounded-2xl p-6 md:p-8 mt-8 border border-indigo-100 max-w-2xl mx-auto shadow-inner">
            <h3 class="text-indigo-900 font-bold mb-2 text-lg">Ingin melihat progres atau mengubah data?</h3>
            <p class="text-sm text-indigo-700 mb-6 leading-relaxed">
                Bapak/Ibu tidak perlu mengisi formulir dari awal lagi. Anda dapat memantau progres pembayaran, mengecek status kelulusan, dan melakukan perubahan data mandiri melalui Portal Calon Santri.
            </p>
            
            <a href="{{ route('public.pendaftaran.show', $candidate->no_daftar ?? $no_daftar) }}" class="inline-flex justify-center items-center gap-2 w-full sm:w-auto px-8 py-3.5 bg-indigo-600 text-white font-bold rounded-xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition transform hover:-translate-y-0.5">
                Ke Portal Pengecekan
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>

    </div>
</x-guest-layout>