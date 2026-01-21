<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pondok Pesantren Assa'adah - Mencetak Generasi Qur'ani</title>
    
    {{-- Tailwind CSS & Plugins --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .hero-bg {
            background-image: url('https://images.unsplash.com/photo-1598396328328-9c178652d37c?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
        }
        .pattern-grid {
            background-image: radial-gradient(#166534 1px, transparent 1px);
            background-size: 20px 20px;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased" x-data="{ scrolled: false, mobileMenu: false }" @scroll.window="scrolled = (window.pageYOffset > 20)">

    <nav :class="scrolled ? 'bg-white/95 backdrop-blur-md shadow-lg py-2' : 'bg-transparent py-4'" class="fixed w-full z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-600 rounded-xl flex items-center justify-center text-white shadow-lg transform rotate-3 hover:rotate-0 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <div class="leading-tight">
                        <span :class="scrolled ? 'text-green-900' : 'text-white'" class="font-extrabold text-xl tracking-tight block">Assa'adah</span>
                        <span :class="scrolled ? 'text-gray-500' : 'text-green-100'" class="text-xs font-medium tracking-wide">Ponpes Modern</span>
                    </div>
                </div>

                <div class="hidden md:flex items-center space-x-8">
                    <a href="#home" :class="scrolled ? 'text-gray-600 hover:text-green-600' : 'text-white/90 hover:text-white'" class="font-medium transition hover:-translate-y-0.5">Beranda</a>
                    <a href="#profil" :class="scrolled ? 'text-gray-600 hover:text-green-600' : 'text-white/90 hover:text-white'" class="font-medium transition hover:-translate-y-0.5">Profil</a>
                    <a href="#program" :class="scrolled ? 'text-gray-600 hover:text-green-600' : 'text-white/90 hover:text-white'" class="font-medium transition hover:-translate-y-0.5">Program</a>
                    <a href="#ppdb" class="px-5 py-2.5 bg-yellow-500 text-white rounded-full font-bold shadow-lg hover:bg-yellow-400 hover:shadow-yellow-500/30 transition transform hover:-translate-y-1">
                        Daftar PPDB
                    </a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-5 py-2.5 bg-green-700 text-white rounded-full font-bold shadow-lg hover:bg-green-800 transition">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" :class="scrolled ? 'text-gray-600' : 'text-white'" class="font-medium hover:underline">Login Admin</a>
                    @endauth
                </div>

                <div class="md:hidden flex items-center">
                    <button @click="mobileMenu = !mobileMenu" class="text-white focus:outline-none">
                        <svg :class="scrolled ? 'text-gray-800' : 'text-white'" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div x-show="mobileMenu" @click.away="mobileMenu = false" x-transition class="md:hidden bg-white shadow-xl absolute w-full border-t border-gray-100">
            <div class="px-4 pt-2 pb-6 space-y-2">
                <a href="#home" class="block px-3 py-3 rounded-md text-base font-medium text-gray-700 hover:text-green-600 hover:bg-green-50">Beranda</a>
                <a href="#profil" class="block px-3 py-3 rounded-md text-base font-medium text-gray-700 hover:text-green-600 hover:bg-green-50">Profil</a>
                <a href="#program" class="block px-3 py-3 rounded-md text-base font-medium text-gray-700 hover:text-green-600 hover:bg-green-50">Program</a>
                <a href="#ppdb" class="block w-full text-center mt-4 px-5 py-3 bg-green-600 text-white rounded-lg font-bold shadow hover:bg-green-700">
                    Daftar Sekarang
                </a>
            </div>
        </div>
    </nav>

    <section id="home" class="relative h-screen min-h-[600px] flex items-center justify-center hero-bg">
        <div class="absolute inset-0 bg-gradient-to-b from-green-900/90 via-green-800/80 to-green-900/90"></div>
        
        <div class="relative z-10 text-center px-4 max-w-5xl mx-auto mt-16">
            <div class="inline-flex items-center gap-2 py-1 px-4 rounded-full bg-white/10 border border-white/20 text-white text-sm font-semibold mb-8 backdrop-blur-md animate-fade-in-up">
                <span class="w-2 h-2 bg-yellow-400 rounded-full animate-pulse"></span>
                {{ $setting->nama_gelombang ?? 'Penerimaan Santri Baru' }}
            </div>
            
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-extrabold text-white leading-tight mb-6 drop-shadow-2xl tracking-tight">
                Membangun Generasi <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-yellow-500">Islami & Berprestasi</span>
            </h1>
            
            <p class="text-lg md:text-xl text-green-100 mb-10 max-w-2xl mx-auto font-light leading-relaxed">
                Pondok Pesantren Assa'adah memadukan kurikulum pesantren salafiyah dan pendidikan modern berbasis teknologi untuk mencetak pemimpin masa depan.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="#ppdb" class="w-full sm:w-auto px-8 py-4 bg-green-500 hover:bg-green-600 text-white rounded-full font-bold text-lg transition shadow-[0_0_20px_rgba(34,197,94,0.5)] transform hover:-translate-y-1">
                    Daftar Sekarang
                </a>
                <a href="#profil" class="w-full sm:w-auto px-8 py-4 bg-white/10 backdrop-blur-sm border border-white/30 text-white rounded-full font-bold text-lg hover:bg-white/20 transition">
                    Tentang Kami
                </a>
            </div>
        </div>

        <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none">
            <svg class="relative block w-full h-[60px] md:h-[100px]" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="fill-gray-50"></path>
            </svg>
        </div>
    </section>

    <div class="relative z-20 -mt-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-3xl shadow-xl p-8 md:p-12 grid grid-cols-2 md:grid-cols-4 gap-8 text-center border-b-4 border-yellow-500">
            <div class="space-y-2">
                <div class="text-4xl md:text-5xl font-extrabold text-green-700">1.5K+</div>
                <div class="text-sm font-bold text-gray-400 uppercase tracking-widest">Santri Aktif</div>
            </div>
            <div class="space-y-2">
                <div class="text-4xl md:text-5xl font-extrabold text-green-700">85</div>
                <div class="text-sm font-bold text-gray-400 uppercase tracking-widest">Guru & Staff</div>
            </div>
            <div class="space-y-2">
                <div class="text-4xl md:text-5xl font-extrabold text-green-700">20+</div>
                <div class="text-sm font-bold text-gray-400 uppercase tracking-widest">Ekstrakurikuler</div>
            </div>
            <div class="space-y-2">
                <div class="text-4xl md:text-5xl font-extrabold text-green-700">100%</div>
                <div class="text-sm font-bold text-gray-400 uppercase tracking-widest">Lulusan Terbaik</div>
            </div>
        </div>
    </div>

    <section id="profil" class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="relative">
                    <div class="absolute -top-10 -left-10 w-32 h-32 bg-yellow-400/30 rounded-full blur-3xl"></div>
                    <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-green-400/30 rounded-full blur-3xl"></div>
                    <div class="relative rounded-2xl overflow-hidden shadow-2xl border-8 border-white transform rotate-2 hover:rotate-0 transition duration-500">
                        <img src="https://images.unsplash.com/photo-1519817650390-64a93db51149?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80" alt="Santri Belajar" class="w-full h-full object-cover">
                    </div>
                </div>
                
                <div class="space-y-6">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-green-100 text-green-700 text-sm font-bold uppercase tracking-wider">
                        Tentang Kami
                    </div>
                    <h2 class="text-3xl md:text-5xl font-bold text-gray-900 leading-tight">
                        Tempat Terbaik Menimba <br> Ilmu Agama & Umum
                    </h2>
                    <p class="text-gray-600 text-lg leading-relaxed">
                        Kami percaya keseimbangan antara ilmu agama (Imtaq) dan ilmu pengetahuan (Iptek) adalah kunci keberhasilan di dunia dan akhirat. Kurikulum kami dirancang khusus untuk memenuhi kebutuhan zaman.
                    </p>
                    
                    <ul class="space-y-4">
                        <li class="flex items-start gap-4 p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                            <div class="flex-shrink-0 w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center text-yellow-600 font-bold">1</div>
                            <div>
                                <h4 class="font-bold text-gray-900">Kurikulum Terpadu</h4>
                                <p class="text-sm text-gray-500">Menggabungkan kurikulum Nasional (Kemdikbud) dan Kepesantrenan.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-4 p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                            <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center text-green-600 font-bold">2</div>
                            <div>
                                <h4 class="font-bold text-gray-900">Program Tahfidz Intensif</h4>
                                <p class="text-sm text-gray-500">Target hafalan minimal 15 Juz untuk lulusan SMP dan 30 Juz untuk SMA.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section id="program" class="py-24 bg-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-1/2 h-full bg-green-50/50 -z-10 rounded-l-full blur-3xl opacity-50"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-green-600 font-bold uppercase tracking-wider text-sm">Program Unggulan</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-2 mb-4">Pendidikan Berkualitas Tinggi</h2>
                <div class="w-20 h-1.5 bg-yellow-500 mx-auto rounded-full"></div>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="group bg-white rounded-3xl p-8 shadow-xl border border-gray-100 hover:shadow-2xl hover:-translate-y-2 transition duration-300 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-green-100 rounded-bl-full -mr-10 -mt-10 opacity-50 group-hover:scale-110 transition"></div>
                    <div class="relative z-10">
                        <div class="w-14 h-14 bg-green-600 text-white rounded-2xl flex items-center justify-center text-2xl mb-6 shadow-lg shadow-green-600/30">
                            📖
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Tahfidz Al-Qur'an</h3>
                        <p class="text-gray-600">Bimbingan intensif menghafal Al-Qur'an dengan metode talaqqi yang bersanad.</p>
                    </div>
                </div>

                <div class="group bg-white rounded-3xl p-8 shadow-xl border border-gray-100 hover:shadow-2xl hover:-translate-y-2 transition duration-300 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-100 rounded-bl-full -mr-10 -mt-10 opacity-50 group-hover:scale-110 transition"></div>
                    <div class="relative z-10">
                        <div class="w-14 h-14 bg-blue-600 text-white rounded-2xl flex items-center justify-center text-2xl mb-6 shadow-lg shadow-blue-600/30">
                            🗣️
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Bahasa Asing</h3>
                        <p class="text-gray-600">Wajib berbahasa Arab dan Inggris dalam percakapan sehari-hari di lingkungan pondok.</p>
                    </div>
                </div>

                <div class="group bg-white rounded-3xl p-8 shadow-xl border border-gray-100 hover:shadow-2xl hover:-translate-y-2 transition duration-300 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-yellow-100 rounded-bl-full -mr-10 -mt-10 opacity-50 group-hover:scale-110 transition"></div>
                    <div class="relative z-10">
                        <div class="w-14 h-14 bg-yellow-500 text-white rounded-2xl flex items-center justify-center text-2xl mb-6 shadow-lg shadow-yellow-500/30">
                            💻
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Teknologi Digital</h3>
                        <p class="text-gray-600">Ekstrakurikuler coding, desain grafis, dan multimedia untuk skill masa depan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="ppdb" class="py-28 bg-green-900 relative overflow-hidden">
        <div class="absolute inset-0 pattern-grid opacity-10"></div>
        <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-b from-green-900 via-transparent to-green-900"></div>
        
        <div class="relative z-10 max-w-4xl mx-auto px-4 text-center">
            <h2 class="text-3xl md:text-5xl font-bold text-white mb-6">Penerimaan Peserta Didik Baru</h2>
            <p class="text-green-100 text-lg mb-12 max-w-2xl mx-auto">
                Siapkan diri Anda untuk menjadi bagian dari keluarga besar kami.
                <br>
                <span class="text-yellow-400 font-semibold">{{ $setting->nama_gelombang ?? 'Tahun Ajaran Baru' }}</span>
            </p>

            @if($status_ppdb == 'buka')
                <div class="bg-white rounded-3xl p-8 md:p-12 shadow-2xl mx-auto max-w-xl transform transition hover:scale-105 duration-300">
                    <div class="inline-block bg-green-100 text-green-700 px-4 py-1 rounded-full font-bold text-xs uppercase tracking-wide mb-6 animate-pulse">
                        ● Pendaftaran Dibuka
                    </div>
                    
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Siapkan Berkas & Daftar</h3>
                    
                    @if(!empty($setting->pengumuman))
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 text-left rounded-r-lg">
                            <p class="text-sm text-blue-700">{{ $setting->pengumuman }}</p>
                        </div>
                    @endif

                    <p class="text-gray-600 mb-8">Klik tombol di bawah ini untuk mengisi formulir pendaftaran online.</p>
                    
                    <a href="{{ route('pendaftaran.create') }}" class="block w-full py-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl text-lg shadow-lg shadow-green-600/30 transition flex items-center justify-center gap-2">
                        <span>Isi Formulir Pendaftaran</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>
            @else
                <div class="bg-white rounded-3xl p-8 md:p-12 shadow-2xl mx-auto max-w-xl border-t-8 border-red-500">
                    <div class="inline-block bg-red-100 text-red-700 px-4 py-1 rounded-full font-bold text-xs uppercase tracking-wide mb-6">
                        ● Pendaftaran Ditutup
                    </div>
                    
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Mohon Maaf</h3>
                    
                    @if(!empty($setting->pengumuman))
                        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6 text-left rounded-r-lg">
                            <p class="text-sm text-yellow-700">{{ $setting->pengumuman }}</p>
                        </div>
                    @else
                        <p class="text-gray-600 mb-8">Kuota pendaftaran saat ini sudah penuh atau periode pendaftaran belum dibuka.</p>
                    @endif

                    <button disabled class="w-full py-4 bg-gray-100 text-gray-400 font-bold rounded-xl cursor-not-allowed">
                        Formulir Tidak Tersedia
                    </button>
                </div>
            @endif
        </div>
    </section>

    <footer class="bg-gray-900 text-gray-300 py-12 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-8 mb-8">
                <div>
                    <h3 class="text-white text-xl font-bold mb-4 flex items-center gap-2">
                        <div class="w-8 h-8 bg-green-600 rounded flex items-center justify-center text-sm">A</div>
                        Assa'adah
                    </h3>
                    <p class="text-sm text-gray-400 leading-relaxed">
                        Jl. Raya Pesantren No. 123<br>
                        Kecamatan Cibereum, Kota Tasikmalaya<br>
                        Jawa Barat 46196
                    </p>
                </div>
                <div>
                    <h4 class="text-white font-bold uppercase mb-4 text-sm tracking-wider">Tautan</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#home" class="hover:text-green-500 transition">Beranda</a></li>
                        <li><a href="#profil" class="hover:text-green-500 transition">Profil</a></li>
                        <li><a href="#ppdb" class="hover:text-green-500 transition">Info PPDB</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-bold uppercase mb-4 text-sm tracking-wider">Kontak</h4>
                    <ul class="space-y-2 text-sm">
                        <li>(0265) 123456</li>
                        <li>admin@pesantren-assaadah.sch.id</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center text-xs text-gray-500">
                &copy; {{ date('Y') }} Pondok Pesantren Assa'adah. All rights reserved.
            </div>
        </div>
    </footer>

</body>
</html>