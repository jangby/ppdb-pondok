<x-app-layout>
    <div class="max-w-md mx-auto p-4 bg-gray-50 min-h-screen pb-10">
        
        <a href="{{ route('monitor.santri') }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 mb-4 font-semibold">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Dashboard
        </a>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 mb-4 text-center">
            <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-3 text-2xl font-bold">
                {{ substr($santri->nama_lengkap, 0, 1) }}
            </div>
            <h2 class="text-xl font-bold text-gray-900">{{ $santri->nama_lengkap }}</h2>
            <p class="text-sm text-gray-500 mb-2">{{ $santri->no_daftar }}</p>
            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-1 rounded-full">{{ $santri->jenjang }}</span>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-100 font-bold text-gray-700 text-sm">
                Informasi Biodata
            </div>
            <div class="p-4 space-y-3 text-sm">
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-500">Tempat, Tgl Lahir</span>
                    <span class="font-medium text-gray-900 text-right">{{ $santri->tempat_lahir }}, {{ \Carbon\Carbon::parse($santri->tanggal_lahir)->translatedFormat('d M Y') }}</span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-500">Asal Sekolah</span>
                    <span class="font-medium text-gray-900 text-right">{{ $santri->asal_sekolah }}</span>
                </div>
                <div class="flex flex-col border-b pb-2">
                    <span class="text-gray-500 mb-1">Nama Orang Tua (Ayah / Ibu)</span>
                    <span class="font-medium text-gray-900">{{ $santri->parent->nama_ayah ?? '-' }} / {{ $santri->parent->nama_ibu ?? '-' }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-gray-500 mb-1">Alamat Lengkap</span>
                    <span class="font-medium text-gray-900 leading-relaxed">{{ $santri->address->alamat ?? '-' }}, RT {{ $santri->address->rt ?? '-' }}/RW {{ $santri->address->rw ?? '-' }}, Kec. {{ $santri->address->kecamatan ?? '-' }}, Kab. {{ $santri->address->kabupaten ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>