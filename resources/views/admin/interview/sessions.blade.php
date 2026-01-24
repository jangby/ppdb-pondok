<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-blue-600 rounded-lg shadow-lg text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
            </div>
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Kelola Sesi & QR Code') }}
                </h2>
                <p class="text-xs text-gray-500">Buat sesi baru untuk panitia melakukan wawancara.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8">
            <h3 class="font-bold text-gray-800 mb-4">Buat Sesi Baru</h3>
            <form action="{{ route('admin.interview.sessions.store') }}" method="POST" class="flex flex-col md:flex-row gap-4">
                @csrf
                <div class="flex-1">
                    <input type="text" name="title" required placeholder="Nama Sesi (Contoh: Meja 1 - Ust. Hanif)" class="w-full rounded-xl border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <button type="submit" class="bg-slate-900 text-white px-6 py-3 rounded-xl font-bold hover:bg-slate-800 transition shadow-lg flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Buat Sesi
                </button>
            </form>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-xl shadow-sm">
                <p class="text-emerald-700 text-sm font-bold">{{ session('success') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($sessions as $session)
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden relative group hover:shadow-md transition">
                    
                    <div class="absolute top-4 right-4 z-10">
                        <span class="px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider {{ $session->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $session->is_active ? 'AKTIF' : 'NON-AKTIF' }}
                        </span>
                    </div>

                    <div class="p-6 text-center">
                        <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-500 font-bold text-lg">
                            {{ substr($session->title, 0, 1) }}
                        </div>

                        <h3 class="font-bold text-lg text-gray-800 mb-1 truncate" title="{{ $session->title }}">{{ $session->title }}</h3>
                        <p class="text-[10px] text-gray-400 mb-4">{{ $session->created_at->diffForHumans() }}</p>
                        
                        <div class="bg-white p-2 border border-gray-200 rounded-xl inline-block mb-4 shadow-inner">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ route('interview.panitia.index', $session->token) }}" alt="QR Code" class="w-32 h-32">
                        </div>

                        <p class="text-xs text-gray-500 mb-4 px-4 leading-relaxed">
                            Scan QR ini menggunakan HP Panitia untuk mulai mewawancarai santri.
                        </p>

                        <div class="grid grid-cols-2 gap-2">
                            <form action="{{ route('admin.interview.sessions.toggle', $session->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="w-full py-2 rounded-lg font-bold text-xs transition {{ $session->is_active ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-green-50 text-green-600 hover:bg-green-100' }}">
                                    {{ $session->is_active ? 'Matikan' : 'Aktifkan' }}
                                </button>
                            </form>
                            
                            <button onclick="copyLink('{{ route('interview.panitia.index', $session->token) }}')" class="w-full py-2 rounded-lg font-bold text-xs bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                                Salin Link
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12 bg-gray-50 rounded-2xl border border-dashed border-gray-300">
                    <p class="text-gray-500 font-medium">Belum ada sesi wawancara yang dibuat.</p>
                </div>
            @endforelse
        </div>
    </div>

    <script>
        function copyLink(url) {
            navigator.clipboard.writeText(url).then(() => {
                alert('Link wawancara berhasil disalin!');
            });
        }
    </script>
</x-app-layout>