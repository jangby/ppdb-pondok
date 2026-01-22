<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PPDB {{ $settings['nama_sekolah'] ?? 'Pondok Pesantren' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-700 bg-gray-50">

    {{-- NAVBAR --}}
    <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-2">
                    {{-- Logo Placeholder --}}
                    <div class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center text-white font-bold">
                        P
                    </div>
                    <span class="font-bold text-xl tracking-tight text-gray-900">
                        PPDB Online
                    </span>
                </div>
                <div>
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="font-semibold text-gray-600 hover:text-green-600">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-green-600 mr-4">Masuk</a>
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    {{-- HERO SECTION --}}
    <div class="relative bg-white overflow-hidden">
        <div class="absolute inset-0">
            {{-- Pattern Background --}}
            <svg class="absolute right-0 top-0 h-full w-1/2 translate-x-1/2 transform text-gray-50 lg:w-full" fill="currentColor" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
                <polygon points="50,0 100,0 50,100 0,100" />
            </svg>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-24 lg:pt-32">
            <div class="text-center">
                {{-- STATUS BADGE --}}
                <div class="flex justify-center mb-6">
                    @if(($settings['status_ppdb'] ?? 'tutup') == 'buka')
                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-semibold bg-green-100 text-green-800 animate-pulse border border-green-200">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            Pendaftaran Sedang Dibuka
                        </span>
                    @else
                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-semibold bg-red-100 text-red-800 border border-red-200">
                            <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                            Pendaftaran Ditutup
                        </span>
                    @endif
                </div>

                <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl md:text-6xl">
                    <span class="block xl:inline">Selamat Datang di PPDB</span>
                    <span class="block text-green-600 xl:inline">{{ $settings['nama_sekolah'] ?? 'Pondok Pesantren' }}</span>
                </h1>
                <p class="mt-3 max-w-md mx-auto text-base text-gray-500 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                    {{ $settings['pengumuman'] ?? 'Mewujudkan generasi santri yang berakhlak mulia, cerdas, dan mandiri berlandaskan Ahlussunnah wal Jamaah.' }}
                </p>
                
                <div class="mt-10 max-w-sm mx-auto sm:max-w-none sm:flex sm:justify-center gap-4">
                    @if(($settings['status_ppdb'] ?? 'tutup') == 'buka')
                        <a href="{{ route('pendaftaran.create') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-green-600 hover:bg-green-700 md:py-4 md:text-lg md:px-10 shadow-lg hover:shadow-green-500/30 transition transform hover:-translate-y-1">
                            Daftar Sekarang
                        </a>
                    @else
                        <button disabled class="w-full flex items-center justify-center px-8 py-3 border border-gray-300 text-base font-medium rounded-xl text-gray-400 bg-gray-100 cursor-not-allowed md:py-4 md:text-lg md:px-10">
                            Pendaftaran Belum Dibuka
                        </button>
                    @endif
                    <a href="#biaya" class="mt-3 w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-xl text-green-700 bg-green-100 hover:bg-green-200 md:py-4 md:text-lg md:px-10 sm:mt-0">
                        Lihat Biaya
                    </a>
                </div>
                
                <p class="mt-4 text-sm text-gray-400">
                    Gelombang: {{ $settings['nama_gelombang'] ?? '-' }} • 
                    Buka: {{ $settings['tgl_buka'] ?? '-' }} s/d {{ $settings['tgl_tutup'] ?? '-' }}
                </p>
            </div>
        </div>
    </div>

    {{-- PERSYARATAN SECTION --}}
    <div class="py-16 bg-white border-y border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-base font-semibold text-green-600 tracking-wide uppercase">Persyaratan</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    Dokumen yang Diperlukan
                </p>
                <p class="mt-4 max-w-2xl text-xl text-gray-500 mx-auto">
                    Persiapkan dokumen fisik berikut untuk diserahkan saat validasi data di sekretariat.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($syarat as $item)
                <div class="flex items-start p-4 rounded-xl border border-gray-100 hover:shadow-lg transition bg-gray-50">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-10 w-10 rounded-lg bg-green-100 text-green-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ $item['nama'] }}</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Sebanyak <span class="font-bold text-gray-700">{{ $item['jumlah'] }} rangkap</span>
                        </p>
                    </div>
                </div>
                @empty
                <div class="col-span-3 text-center text-gray-400 italic">
                    Belum ada persyaratan yang diinput oleh panitia.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- BIAYA SECTION --}}
    <div id="biaya" class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-base font-semibold text-green-600 tracking-wide uppercase">Rincian Biaya</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    Investasi Pendidikan
                </p>
                <p class="mt-4 max-w-2xl text-xl text-gray-500 mx-auto">
                    Rincian biaya pendaftaran ulang berdasarkan jenjang pendidikan.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($rincianBiaya as $jenjang => $data)
                <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-shadow duration-300 border border-gray-200 overflow-hidden flex flex-col">
                    <div class="p-6 bg-gray-900 text-white text-center">
                        <h3 class="text-xl font-bold uppercase tracking-wider">{{ $jenjang }}</h3>
                        <div class="mt-4 flex justify-center items-baseline text-4xl font-extrabold">
                            <span class="text-xl mr-1">Rp</span>
                            {{ number_format($data['total'], 0, ',', '.') }}
                        </div>
                        <p class="mt-1 text-sm text-gray-400">Total Biaya Awal Masuk</p>
                    </div>
                    
                    <div class="flex-1 p-6 bg-white">
                        <ul class="space-y-4">
                            @foreach($data['items'] as $item)
                            <li class="flex items-start justify-between">
                                <div class="flex items-center">
                                    <svg class="flex-shrink-0 h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    <span class="ml-3 text-sm text-gray-700">{{ $item->nama_pembayaran }}</span>
                                </div>
                                <span class="text-sm font-semibold text-gray-900">
                                    {{ number_format($item->nominal, 0, ',', '.') }}
                                </span>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="p-6 bg-gray-50 border-t border-gray-100">
                        @if(($settings['status_ppdb'] ?? 'tutup') == 'buka')
                            <a href="{{ route('pendaftaran.create') }}" class="block w-full bg-green-600 border border-transparent rounded-lg py-3 px-4 text-center text-sm font-bold text-white hover:bg-green-700 transition">
                                Daftar Jenjang {{ $jenjang }}
                            </a>
                        @else
                             <button disabled class="block w-full bg-gray-200 border border-transparent rounded-lg py-3 px-4 text-center text-sm font-bold text-gray-400 cursor-not-allowed">
                                Pendaftaran Ditutup
                            </button>
                        @endif
                    </div>
                </div>
                @empty
                <div class="col-span-3 text-center py-12">
                    <div class="inline-block p-4 rounded-full bg-gray-100 mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <p class="text-gray-500">Belum ada rincian biaya yang diatur oleh admin.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- FOOTER --}}
    <footer class="bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">{{ $settings['nama_sekolah'] ?? 'PPDB Online' }}</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        Sistem Penerimaan Peserta Didik Baru Online. Memudahkan proses pendaftaran, seleksi, dan daftar ulang calon santri.
                    </p>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-4">Kontak Panitia</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            WhatsApp: {{ $settings['whatsapp_admin'] ?? '-' }}
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Sekretariat Pondok
                        </li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-4">Menu</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="{{ route('login') }}" class="hover:text-white">Login Admin</a></li>
                        <li><a href="{{ route('pendaftaran.create') }}" class="hover:text-white">Daftar Sekarang</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 border-t border-gray-800 pt-8 text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} {{ $settings['nama_sekolah'] ?? 'PPDB' }}. All rights reserved.
            </div>
        </div>
    </footer>

</body>
</html>