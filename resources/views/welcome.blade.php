<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>PPDB {{ $settings['nama_sekolah'] ?? 'Pondok Pesantren' }}</title>
    
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* Glassmorphism Navbar */
        .glass-nav { 
            background: rgba(255, 255, 255, 0.9); 
            backdrop-filter: blur(12px); 
            -webkit-backdrop-filter: blur(12px); 
            border-bottom: 1px solid rgba(0,0,0,0.05); 
        }

        /* Hero Gradient Overlay */
        .hero-overlay {
            background: linear-gradient(to bottom, rgba(15, 23, 42, 0.3) 0%, rgba(15, 23, 42, 0.8) 100%);
        }

        /* Horizontal Scroll Hide */
        .hide-scroll::-webkit-scrollbar { display: none; }
        .hide-scroll { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="antialiased text-slate-600 bg-slate-50 selection:bg-emerald-500 selection:text-white">

    {{-- 1. NAVBAR (Sticky & Compact) --}}
    <nav class="fixed w-full z-50 glass-nav transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                {{-- Logo --}}
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 bg-emerald-600 rounded-xl flex items-center justify-center text-white font-extrabold text-lg shadow-lg shadow-emerald-500/20">
                        P
                    </div>
                    <div class="flex flex-col">
                        <span class="font-bold text-slate-900 leading-none text-sm">PPDB Online</span>
                        <span class="text-[10px] text-slate-500 font-medium tracking-wide uppercase mt-0.5">Penerimaan Santri</span>
                    </div>
                </div>

                {{-- Login Button --}}
                <div>
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-[10px] font-bold text-white bg-slate-900 px-4 py-2 rounded-full shadow-md">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-[10px] font-bold text-slate-700 bg-slate-100 border border-slate-200 px-4 py-2 rounded-full hover:bg-white transition">
                            Masuk Admin
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- 2. HERO SECTION (Immersive & Rapi) --}}
    @php
        $bannerUrl = !empty($settings['banner_image']) ? asset('storage/'.$settings['banner_image']) : null;
    @endphp
    <div class="relative min-h-[75vh] flex items-center justify-center overflow-hidden pt-16">
        
        {{-- Background Image --}}
        <div class="absolute inset-0 z-0">
            @if($bannerUrl)
                <img src="{{ $bannerUrl }}" class="w-full h-full object-cover object-center" alt="Banner Sekolah">
            @else
                <div class="w-full h-full bg-slate-900 relative">
                    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
                </div>
            @endif
            <div class="absolute inset-0 hero-overlay"></div>
        </div>

        {{-- Content --}}
        <div class="relative z-10 max-w-7xl mx-auto px-5 text-center mt-4">
            
            {{-- Status Badge --}}
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/10 mb-6 shadow-xl">
                @if(($settings['status_ppdb'] ?? 'tutup') == 'buka')
                    <span class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="text-[10px] font-bold text-emerald-200 tracking-wider uppercase">Pendaftaran Dibuka</span>
                @else
                    <span class="h-2 w-2 rounded-full bg-red-500"></span>
                    <span class="text-[10px] font-bold text-red-200 tracking-wider uppercase">Ditutup</span>
                @endif
            </div>

            <h1 class="text-3xl sm:text-5xl md:text-6xl font-extrabold text-white leading-tight tracking-tight mb-3 drop-shadow-lg">
                {{ $settings['pengumuman'] ?? 'Penerimaan Santri Baru' }}
            </h1>
            
            <p class="text-slate-300 text-xs sm:text-base max-w-lg mx-auto mb-8 leading-relaxed font-light opacity-90 px-2">
                {{ $settings['deskripsi_banner'] ?? $settings['nama_sekolah'] ?? 'Mewujudkan generasi Rabbani yang unggul dalam IMTAQ dan IPTEK.' }}
            </p>

            {{-- Action Buttons --}}
            <div class="flex flex-col gap-3 w-full max-w-xs mx-auto">
                @if(($settings['status_ppdb'] ?? 'tutup') == 'buka')
                    <a href="{{ route('pendaftaran.create') }}" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold rounded-xl shadow-lg shadow-emerald-900/20 transition active:scale-95 flex items-center justify-center gap-2">
                        <span>Daftar Sekarang</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </a>
                @else
                    <button disabled class="w-full py-3.5 bg-slate-800 text-slate-500 text-sm font-bold rounded-xl cursor-not-allowed border border-slate-700">
                        Belum Dibuka
                    </button>
                @endif
                
                <a href="#alur" class="w-full py-3.5 bg-white/10 backdrop-blur-md border border-white/20 text-white text-sm font-bold rounded-xl hover:bg-white/20 transition active:scale-95">
                    Lihat Alur & Biaya
                </a>
            </div>
        </div>
    </div>

    {{-- 3. QUICK STATS (Floating Card) --}}
    <div class="relative z-20 px-4 -mt-8">
        <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 p-4 max-w-4xl mx-auto flex justify-between items-center text-center divide-x divide-slate-100">
            <div class="w-1/3 px-1">
                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider mb-1">Gelombang</p>
                <p class="text-slate-900 font-bold text-xs sm:text-sm truncate">{{ $settings['nama_gelombang'] ?? 'Satu' }}</p>
            </div>
            <div class="w-1/3 px-1">
                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider mb-1">Buka</p>
                <p class="text-emerald-600 font-bold text-xs sm:text-sm">{{ $settings['tgl_buka'] ? \Carbon\Carbon::parse($settings['tgl_buka'])->format('d M') : '-' }}</p>
            </div>
            <div class="w-1/3 px-1">
                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider mb-1">Tutup</p>
                <p class="text-red-500 font-bold text-xs sm:text-sm">{{ $settings['tgl_tutup'] ? \Carbon\Carbon::parse($settings['tgl_tutup'])->format('d M') : '-' }}</p>
            </div>
        </div>
    </div>

    {{-- 4. ALUR PENDAFTARAN (Timeline Style - Lebih Rapi di Mobile) --}}
    <div id="alur" class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-5">
            <div class="flex items-center gap-3 mb-6">
                <div class="h-6 w-1 bg-emerald-500 rounded-full"></div>
                <h2 class="text-lg font-bold text-slate-900">Alur Pendaftaran</h2>
            </div>

            <div class="space-y-4">
                {{-- Step 1 --}}
                <div class="flex gap-4">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-sm">1</div>
                        <div class="h-full w-0.5 bg-slate-100 my-1"></div>
                    </div>
                    <div class="pb-6">
                        <h3 class="text-sm font-bold text-slate-800">Ambil & Isi Berkas</h3>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Download template surat perjanjian, cetak, isi lengkap, dan tanda tangani.</p>
                    </div>
                </div>

                {{-- Step 2 --}}
                <div class="flex gap-4">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-sm">2</div>
                        <div class="h-full w-0.5 bg-slate-100 my-1"></div>
                    </div>
                    <div class="pb-6">
                        <h3 class="text-sm font-bold text-slate-800">Upload Verifikasi</h3>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Foto berkas tersebut lalu upload di menu pendaftaran beserta No WA aktif.</p>
                    </div>
                </div>

                {{-- Step 3 --}}
                <div class="flex gap-4">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-sm">3</div>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">Terima Link Formulir</h3>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Jika disetujui, Admin akan mengirim link formulir biodata via WhatsApp.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 5. FASILITAS (Horizontal Scroll - App Like Feel) --}}
    @php $facilities = json_decode($settings['fasilitas_sekolah'] ?? '[]', true); @endphp
    @if(count($facilities) > 0)
    <div class="py-10 bg-slate-50 border-y border-slate-200 overflow-hidden">
        <div class="max-w-7xl mx-auto px-5">
            <h2 class="text-lg font-bold text-slate-900 mb-4">Fasilitas</h2>
            
            {{-- Scroll Container --}}
            <div class="flex overflow-x-auto gap-3 pb-4 hide-scroll">
                @foreach($facilities as $fac)
                    <div class="flex-shrink-0 px-4 py-2 bg-white border border-slate-200 rounded-xl shadow-sm flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                        <span class="text-xs font-semibold text-slate-700 whitespace-nowrap">{{ $fac }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- 6. GALLERY (Compact Grid) --}}
    @php $galleries = json_decode($settings['galeri_sekolah'] ?? '[]', true); @endphp
    @if(count($galleries) > 0)
    <div class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-5">
            <div class="flex justify-between items-end mb-5">
                <h2 class="text-lg font-bold text-slate-900">Galeri</h2>
                <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider">Kegiatan</span>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                @foreach($galleries as $img)
                    <div class="aspect-square rounded-lg overflow-hidden bg-slate-100">
                        <img src="{{ asset('storage/'.$img) }}" loading="lazy" class="h-full w-full object-cover">
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- 7. BIAYA (Clean Cards) --}}
    <div id="biaya" class="py-12 bg-slate-50">
        <div class="max-w-7xl mx-auto px-5">
            <h2 class="text-lg font-bold text-slate-900 mb-2">Biaya Pendidikan</h2>
            <p class="text-xs text-slate-500 mb-6">Estimasi biaya masuk awal.</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($rincianBiaya as $jenjang => $data)
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Jenjang</span>
                            <h3 class="text-base font-bold text-slate-800">{{ $jenjang }}</h3>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total</span>
                            <p class="text-lg font-black text-emerald-600">Rp {{ number_format($data['total'], 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="space-y-2 border-t border-slate-100 pt-3 mb-4">
                        @foreach($data['items'] as $item)
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-500">{{ $item->nama_pembayaran }}</span>
                            <span class="font-bold text-slate-700">{{ number_format($item->nominal, 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                    </div>

                    @if(($settings['status_ppdb'] ?? 'tutup') == 'buka')
                        <a href="{{ route('pendaftaran.create') }}" class="block w-full py-2.5 bg-slate-900 text-white text-xs font-bold text-center rounded-lg hover:bg-emerald-600 transition">
                            Daftar {{ $jenjang }}
                        </a>
                    @else
                         <button disabled class="block w-full py-2.5 bg-slate-100 text-slate-400 text-xs font-bold text-center rounded-lg cursor-not-allowed">
                            Ditutup
                        </button>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- 8. PERSYARATAN (Simple List) --}}
    <div class="py-10 bg-white border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-5">
            <div class="bg-emerald-50 rounded-xl p-5 border border-emerald-100">
                <h3 class="text-sm font-bold text-emerald-900 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Persyaratan Fisik
                </h3>
                <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @forelse($syarat as $item)
                        <li class="flex items-center justify-between text-xs text-emerald-800 bg-white px-3 py-2 rounded-lg border border-emerald-100/50">
                            <span>{{ $item['nama'] }}</span>
                            <span class="font-bold bg-emerald-100 px-1.5 py-0.5 rounded text-[10px]">{{ $item['jumlah'] }}x</span>
                        </li>
                    @empty
                        <li class="text-xs text-emerald-600/50 italic">Belum ada data.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    {{-- FOOTER --}}
    <footer class="bg-slate-900 text-white py-10">
        <div class="max-w-7xl mx-auto px-5 text-center">
            <h2 class="text-base font-bold">{{ $settings['nama_sekolah'] ?? 'PPDB Online' }}</h2>
            <p class="text-[10px] text-slate-400 mt-1">Platform Pendaftaran Digital.</p>
            
            <div class="mt-6">
                <a href="https://wa.me/{{ $settings['whatsapp_admin'] ?? '' }}" target="_blank" class="inline-flex items-center gap-2 px-5 py-2 bg-emerald-600 rounded-full text-xs font-bold hover:bg-emerald-500 transition">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                    WhatsApp Admin
                </a>
            </div>
            <div class="mt-8 text-[10px] text-slate-500">
                &copy; {{ date('Y') }} All Rights Reserved.
            </div>
        </div>
    </footer>

</body>
</html>