<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pondok Pesantren Assa'adah - Mencetak Generasi Qur'ani</title>
    
    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    
    {{-- Google Fonts: Plus Jakarta Sans (Modern & Clean) --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    {{-- Alpine.js untuk interaksi Mobile Menu (Ringan) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .hero-pattern {
            background-color: #064e3b;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%230fa968' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased" x-data="{ mobileMenuOpen: false, scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 20)">

    <nav :class="scrolled ? 'bg-white/95 backdrop-blur-md shadow-md py-2' : 'bg-transparent py-4'" class="fixed w-full z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center text-white font-bold shadow-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <div>
                        <span :class="scrolled ? 'text-green-900' : 'text-white'" class="font-bold text-xl tracking-tight block leading-none transition-colors">Assa'adah</span>
                        <span :class="scrolled ? 'text-gray-500' : 'text-green-100'" class="text-xs font-medium tracking-wide transition-colors">Pondok Pesantren Modern</span>
                    </div>
                </div>

                <div class="hidden md:flex items-center space-x-8">
                    <a href="#home" :class="scrolled ? 'text-gray-600 hover:text-green-600' : 'text-white/90 hover:text-white'" class="font-medium transition">Beranda</a>
                    <a href="#profil" :class="scrolled ? 'text-gray-600 hover:text-green-600' : 'text-white/90 hover:text-white'" class="font-medium transition">Profil</a>
                    <a href="#program" :class="scrolled ? 'text-gray-600 hover:text-green-600' : 'text-white/90 hover:text-white'" class="font-medium transition">Program</a>
                    <a href="#ppdb" class="px-5 py-2.5 bg-yellow-500 text-white rounded-full font-bold shadow-lg hover:bg-yellow-400 hover:shadow-yellow-500/30 transition transform hover:-translate-y-0.5">
                        Daftar PPDB
                    </a>
                </div>

                <div class="md:hidden flex items-center">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" :class="scrolled ? 'text-gray-800' : 'text-white'" class="focus:outline-none">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            <path x-show="mobileMenuOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-5"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="md:hidden bg-white shadow-xl absolute w-full border-t border-gray-100">
            <div class="px-4 pt-2 pb-6 space-y-2">
                <a href="#home" @click="mobileMenuOpen = false" class="block px-3 py-3 rounded-md text-base font-medium text-gray-700 hover:text-green-600 hover:bg-green-50">Beranda</a>
                <a href="#profil" @click="mobileMenuOpen = false" class="block px-3 py-3 rounded-md text-base font-medium text-gray-700 hover:text-green-600 hover:bg-green-50">Profil Pondok</a>
                <a href="#program" @click="mobileMenuOpen = false" class="block px-3 py-3 rounded-md text-base font-medium text-gray-700 hover:text-green-600 hover:bg-green-50">Program Unggulan</a>
                <a href="#ppdb" @click="mobileMenuOpen = false" class="block w-full text-center mt-4 px-5 py-3 bg-green-600 text-white rounded-lg font-bold shadow hover:bg-green-700">
                    Daftar Sekarang
                </a>
            </div>
        </div>
    </nav>

    <section id="home" class="relative h-screen min-h-[600px] flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0 z-0">
            {{-- GANTI URL FOTO GEDUNG SEKOLAH DISINI --}}
            <img src="https://images.unsplash.com/photo-1598396328328-9c178652d37c?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80" alt="Background" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-green-900/90 via-green-900/70 to-black/30"></div>
        </div>

        <div class="relative z-10 text-center px-4 max-w-4xl mx-auto mt-16">
            <span class="inline-block py-1 px-3 rounded-full bg-green-500/20 border border-green-400/30 text-green-100 text-sm font-semibold mb-6 backdrop-blur-sm animate-pulse">
                Penerimaan Santri Baru Tahun {{ date('Y') }} Dibuka
            </span>
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-extrabold text-white leading-tight mb-6 drop-shadow-lg">
                Membangun Generasi <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-yellow-500">Islami & Berprestasi</span>
            </h1>
            <p class="text-lg md:text-xl text-gray-200 mb-10 max-w-2xl mx-auto font-light leading-relaxed">
                Pondok Pesantren Assa'adah memadukan kurikulum pesantren salaf dan pendidikan modern untuk mencetak pemimpin masa depan.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#ppdb" class="px-8 py-4 bg-green-600 text-white rounded-full font-bold text-lg hover:bg-green-700 transition shadow-lg shadow-green-600/30">
                    Daftar Sekarang
                </a>
                <a href="#profil" class="px-8 py-4 bg-white/10 backdrop-blur-md border border-white/30 text-white rounded-full font-bold text-lg hover:bg-white/20 transition">
                    Tentang Kami
                </a>
            </div>
        </div>

        <div class="absolute bottom-0 w-full overflow-hidden leading-none">
            <svg class="relative block w-full h-[60px] md:h-[100px]" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="fill-gray-50"></path>
            </svg>
        </div>
    </section>

    <div class="relative z-20 -mt-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-xl p-8 grid grid-cols-2 md:grid-cols-4 gap-8 text-center border-b-4 border-yellow-500">
            <div>
                <div class="text-3xl md:text-4xl font-bold text-green-700 mb-1">1500+</div>
                <div class="text-sm text-gray-500 font-medium uppercase tracking-wider">Santri</div>
            </div>
            <div>
                <div class="text-3xl md:text-4xl font-bold text-green-700 mb-1">85</div>
                <div class="text-sm text-gray-500 font-medium uppercase tracking-wider">Guru & Staff</div>
            </div>
            <div>
                <div class="text-3xl md:text-4xl font-bold text-green-700 mb-1">20</div>
                <div class="text-sm text-gray-500 font-medium uppercase tracking-wider">Ekstrakurikuler</div>
            </div>
            <div>
                <div class="text-3xl md:text-4xl font-bold text-green-700 mb-1">100%</div>
                <div class="text-sm text-gray-500 font-medium uppercase tracking-wider">Lulusan Terbaik</div>
            </div>
        </div>
    </div>

    <section id="profil" class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="relative">
                    <div class="absolute -top-4 -left-4 w-24 h-24 bg-yellow-400 rounded-full opacity-20 blur-2xl"></div>
                    <div class="relative rounded-2xl overflow-hidden shadow-2xl border-4 border-white">
                        <img src="https://images.unsplash.com/photo-1519817650390-64a93db51149?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80" alt="Kegiatan Santri" class="w-full h-full object-cover hover:scale-105 transition duration-500">
                    </div>
                    <div class="absolute -bottom-6 -right-6 bg-white p-4 rounded-xl shadow-xl max-w-xs hidden md:block">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center text-green-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-800">Terakreditasi A</p>
                                <p class="text-xs text-gray-500">Kementerian Agama RI</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-green-600 font-bold uppercase tracking-wide text-sm mb-2">Tentang Assa'adah</h4>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6 leading-tight">
                        Tempat Terbaik untuk <br> Menimba Ilmu Agama & Umum
                    </h2>
                    <p class="text-gray-600 mb-6 leading-relaxed text-lg">
                        Pondok Pesantren Assa'adah berkomitmen untuk melahirkan generasi yang tidak hanya hafal Al-Qur'an, tetapi juga menguasai ilmu pengetahuan teknologi serta memiliki akhlakul karimah yang luhur.
                    </p>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-yellow-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-gray-700">Kurikulum Terpadu (Nasional & Pesantren)</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-yellow-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-gray-700">Fasilitas Asrama & Kelas Modern</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-yellow-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-gray-700">Program Tahfidz Intensif & Bahasa Asing</span>
                        </li>
                    </ul>
                    <a href="#program" class="text-green-700 font-bold hover:text-green-800 inline-flex items-center gap-2">
                        Lihat Program Kami 
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section id="program" class="py-24 bg-white relative">
        <div class="absolute top-0 right-0 w-1/3 h-full bg-green-50/50 -z-10"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h4 class="text-green-600 font-bold uppercase tracking-wide text-sm mb-2">Pendidikan Berkualitas</h4>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Program Unggulan</h2>
                <div class="w-24 h-1.5 bg-yellow-500 mx-auto mt-4 rounded-full"></div>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="group bg-white rounded-2xl p-8 shadow-lg border border-gray-100 hover:shadow-2xl hover:-translate-y-2 transition duration-300">
                    <div class="w-16 h-16 bg-green-100 text-green-600 rounded-2xl flex items-center justify-center mb-6 text-3xl group-hover:bg-green-600 group-hover:text-white transition duration-300">
                        📖
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Tahfidz Al-Qur'an</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Program menghafal Al-Qur'an dengan target hafalan minimal 15 Juz untuk lulusan SMP dan 30 Juz untuk lulusan SMA.
                    </p>
                </div>

                <div class="group bg-white rounded-2xl p-8 shadow-lg border border-gray-100 hover:shadow-2xl hover:-translate-y-2 transition duration-300">
                    <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mb-6 text-3xl group-hover:bg-blue-600 group-hover:text-white transition duration-300">
                        🗣️
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Bahasa Arab & Inggris</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Wajib berbahasa resmi (Arab & Inggris) dalam kegiatan sehari-hari untuk mempersiapkan santri di kancah global.
                    </p>
                </div>

                <div class="group bg-white rounded-2xl p-8 shadow-lg border border-gray-100 hover:shadow-2xl hover:-translate-y-2 transition duration-300">
                    <div class="w-16 h-16 bg-yellow-100 text-yellow-600 rounded-2xl flex items-center justify-center mb-6 text-3xl group-hover:bg-yellow-500 group-hover:text-white transition duration-300">
                        💻
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">IT & Digital Skill</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Pelatihan komputer, desain grafis, dan coding dasar untuk membekali santri dengan kemampuan teknologi masa kini.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section id="ppdb" class="py-24 hero-pattern text-white relative">
        <div class="absolute inset-0 bg-green-900/90"></div>
        <div class="relative z-10 max-w-4xl mx-auto px-4 text-center">
            
            <h2 class="text-3xl md:text-5xl font-bold mb-6">Penerimaan Peserta Didik Baru</h2>
            <p class="text-green-100 text-lg mb-12">Bergabunglah menjadi bagian dari keluarga besar Pondok Pesantren Assa'adah. <br class="hidden md:block"> Wujudkan impian menjadi Hafidz Qur'an dan Intelektual Muslim.</p>

            {{-- LOGIC PHP BLADE --}}
            @if(strtolower($status_ppdb) == 'buka')
                
                {{-- STATUS: DIBUKA --}}
                <div class="bg-white text-gray-800 rounded-3xl p-8 md:p-12 shadow-2xl mx-auto max-w-2xl transform transition hover:scale-105 duration-300">
                    <div class="inline-block bg-green-100 text-green-700 px-6 py-2 rounded-full font-bold text-sm uppercase tracking-wide mb-6 animate-bounce">
                        ● Pendaftaran Sedang Dibuka
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Siapkan Berkas Anda</h3>
                    <p class="text-gray-600 mb-8">
                        Silakan klik tombol di bawah ini untuk mengisi formulir pendaftaran secara online. Pastikan data yang diinput benar.
                    </p>
                    <a href="{{ route('pendaftaran.create') }}" class="block w-full py-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl text-lg shadow-lg shadow-green-600/30 transition">
                        Isi Formulir Pendaftaran &rarr;
                    </a>
                </div>

            @else
                
                {{-- STATUS: DITUTUP --}}
                <div class="bg-white text-gray-800 rounded-3xl p-8 md:p-12 shadow-2xl mx-auto max-w-2xl border-t-8 border-red-500">
                    <div class="inline-block bg-red-100 text-red-700 px-6 py-2 rounded-full font-bold text-sm uppercase tracking-wide mb-6">
                        ● Pendaftaran Ditutup
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Mohon Maaf</h3>
                    <p class="text-gray-600 mb-8">
                        Kuota pendaftaran santri baru saat ini sudah penuh atau belum dibuka. Silakan hubungi admin untuk informasi lebih lanjut.
                    </p>
                    <a href="#" class="inline-flex items-center justify-center gap-2 w-full py-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition">
                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                        Hubungi via WhatsApp
                    </a>
                </div>

            @endif
        </div>
    </section>

    <footer class="bg-gray-900 text-gray-300 py-12 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <h3 class="text-white text-2xl font-bold mb-4 flex items-center gap-2">
                        <div class="w-8 h-8 bg-green-600 rounded flex items-center justify-center text-sm">A</div>
                        Assa'adah
                    </h3>
                    <p class="text-gray-400 leading-relaxed mb-6 max-w-sm">
                        Mewujudkan lembaga pendidikan Islam yang unggul, kompetitif, dan melahirkan generasi berakhlakul karimah.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-green-600 transition text-white">FB</a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-pink-600 transition text-white">IG</a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-red-600 transition text-white">YT</a>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-white font-bold uppercase mb-4 tracking-wider">Tautan Cepat</h4>
                    <ul class="space-y-2">
                        <li><a href="#home" class="hover:text-green-500 transition">Beranda</a></li>
                        <li><a href="#profil" class="hover:text-green-500 transition">Profil Pondok</a></li>
                        <li><a href="#program" class="hover:text-green-500 transition">Program Unggulan</a></li>
                        <li><a href="#ppdb" class="hover:text-green-500 transition">Info PPDB</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-bold uppercase mb-4 tracking-wider">Kontak Kami</h4>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-green-500 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                            <span>Jl. Raya Contoh No. 123, Kabupaten Tasikmalaya, Jawa Barat</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <span>(0265) 123456</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} Pondok Pesantren Assa'adah. Developed by Tim IT.
            </div>
        </div>
    </footer>

</body>
</html>