<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>PPDB {{ $settings['nama_sekolah'] ?? 'Pondok Pesantren' }}</title>
    
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Typography */
        h1, h2, h3, h4, h5, h6 { font-family: 'Outfit', sans-serif; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* Ambient Green Background */
        .ambient-bg {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
            background-color: #f8fafc; /* Slate-50 */
        }
        .blob-green {
            position: absolute;
            top: -10%; right: -5%;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.15) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
            filter: blur(60px);
        }
        .blob-teal {
            position: absolute;
            bottom: 10%; left: -10%;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(20, 184, 166, 0.1) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
            filter: blur(60px);
        }

        /* Glass Card Clean */
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 4px 20px rgba(16, 185, 129, 0.05); /* Greenish shadow */
        }
        
        .nav-glass {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(0,0,0,0.03);
        }

        /* Hide Scrollbar */
        .hide-scroll::-webkit-scrollbar { display: none; }
        .hide-scroll { -ms-overflow-style: none; scrollbar-width: none; }

        /* Custom Gradients */
        .text-gradient-green {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-600 antialiased selection:bg-emerald-100 selection:text-emerald-900">

    {{-- BACKGROUND DEKORATIF --}}
    <div class="fixed inset-0 pointer-events-none ambient-bg">
        <div class="blob-green animate-pulse" style="animation-duration: 8s;"></div>
        <div class="blob-teal animate-pulse" style="animation-duration: 10s;"></div>
    </div>

    {{-- 1. NAVBAR (Compact & Clean) --}}
    <nav class="fixed top-0 w-full z-50 nav-glass transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="flex justify-between items-center h-14 md:h-16">
                {{-- Logo --}}
                <div class="flex items-center gap-2">
                    {{-- [DINAMIS] Tampilkan Logo jika ada, jika tidak pakai inisial --}}
                    @if(!empty($settings['logo_sekolah']))
                        <img src="{{ asset('storage/'.$settings['logo_sekolah']) }}" class="w-8 h-8 md:w-10 md:h-10 object-contain">
                    @else
                        <div class="w-8 h-8 bg-emerald-600 rounded-lg flex items-center justify-center text-white font-bold text-sm shadow-md shadow-emerald-200">
                            {{ substr($settings['nama_sekolah'] ?? 'P', 0, 1) }}
                        </div>
                    @endif
                    
                    <div class="flex flex-col">
                        <span class="font-bold text-slate-800 text-xs md:text-sm tracking-tight leading-none">PPDB Online</span>
                        <span class="text-[9px] md:text-[10px] text-emerald-600 font-medium uppercase tracking-wide mt-0.5">
                            {{ substr($settings['nama_sekolah'] ?? 'Pondok Pesantren', 0, 18) }}
                        </span>
                    </div>
                </div>

                {{-- Action Button --}}
                @auth
                    <a href="{{ url('/dashboard') }}" class="text-[10px] font-bold bg-slate-900 text-white px-4 py-1.5 rounded-full hover:bg-slate-800 transition">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="group flex items-center gap-1.5 text-[10px] font-bold text-slate-600 bg-white border border-slate-200 px-3 py-1.5 rounded-full hover:border-emerald-300 hover:text-emerald-600 transition shadow-sm">
                        <svg class="w-3 h-3 text-slate-400 group-hover:text-emerald-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        <span>Login Admin</span>
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- 2. HERO SECTION (Mobile Optimized) --}}
    @php
        $bannerUrl = !empty($settings['banner_image']) ? asset('storage/'.$settings['banner_image']) : null;
        $isOpen = ($settings['status_ppdb'] ?? 'tutup') == 'buka';
    @endphp
    <section class="relative pt-20 pb-10 md:pt-32 md:pb-20 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 relative z-10 text-center">
            
            {{-- Status Badge (Kecil Rapi) --}}
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border border-slate-100 shadow-sm mb-6">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $isOpen ? 'bg-emerald-400' : 'bg-red-400' }} opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 {{ $isOpen ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                </span>
                <span class="text-[10px] md:text-xs font-bold uppercase tracking-wider {{ $isOpen ? 'text-emerald-600' : 'text-red-600' }}">
                    {{ $isOpen ? 'Pendaftaran Dibuka' : 'Pendaftaran Ditutup' }}
                </span>
            </div>

            <h1 class="text-3xl sm:text-5xl md:text-6xl font-extrabold text-slate-900 leading-[1.15] mb-4">
                {{ $settings['pengumuman'] ?? 'Penerimaan Santri Baru' }}
            </h1>
            
            <p class="text-xs sm:text-sm md:text-lg text-slate-500 mb-8 leading-relaxed max-w-xl mx-auto px-4">
                {{ $settings['deskripsi_banner'] ?? 'Mewujudkan generasi Rabbani yang unggul dalam IMTAQ dan IPTEK.' }}
            </p>

            {{-- Buttons (Compact on Mobile) --}}
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 w-full max-w-[260px] sm:max-w-md mx-auto">
                @if($isOpen)
                    <a href="{{ route('pendaftaran.create') }}" class="w-full sm:w-auto px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-lg shadow-emerald-600/20 transition transform active:scale-95 flex items-center justify-center gap-2">
                        <span>Daftar Sekarang</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </a>
                @else
                    <button disabled class="w-full sm:w-auto px-6 py-3 bg-slate-100 text-slate-400 text-xs sm:text-sm font-bold rounded-xl cursor-not-allowed border border-slate-200">
                        Belum Dibuka
                    </button>
                @endif
                
                <a href="#info" class="w-full sm:w-auto px-6 py-3 bg-white border border-slate-200 text-slate-600 hover:text-emerald-600 hover:border-emerald-200 text-xs sm:text-sm font-bold rounded-xl transition active:scale-95">
                    Informasi & Alur
                </a>
            </div>

            {{-- Info Cards Grid (Mobile First) --}}
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mt-10 max-w-3xl mx-auto px-2">
                {{-- Gelombang --}}
                <div class="glass-card p-3 md:p-4 rounded-xl text-left">
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider mb-1">Gelombang</p>
                    <p class="text-xs md:text-base font-bold text-slate-800 truncate">{{ $settings['nama_gelombang'] ?? '-' }}</p>
                </div>
                {{-- Periode --}}
                <div class="glass-card p-3 md:p-4 rounded-xl text-left">
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider mb-1">Periode</p>
                    <p class="text-xs md:text-base font-bold text-emerald-600">
                        {{ $settings['tgl_buka'] ? date('d M', strtotime($settings['tgl_buka'])) : '-' }} s/d 
                        {{ $settings['tgl_tutup'] ? date('d M', strtotime($settings['tgl_tutup'])) : '-' }}
                    </p>
                </div>
                {{-- Lokasi (Span 2 col on mobile) --}}
                <div class="glass-card p-3 md:p-4 rounded-xl text-left col-span-2 md:col-span-1">
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider mb-1">Lokasi</p>
                    <p class="text-xs md:text-sm font-medium text-slate-600 line-clamp-1">
                        {{ $settings['alamat_sekolah'] ?? 'Belum diatur' }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- 3. ALUR PENDAFTARAN (Vertical Steps Kecil) --}}
    <section id="info" class="py-10 md:py-16 bg-white border-t border-slate-100">
        <div class="max-w-2xl mx-auto px-4 sm:px-6">
            <div class="text-center mb-8">
                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md uppercase tracking-wider">Panduan</span>
                <h2 class="text-xl md:text-3xl font-bold text-slate-900 mt-2">Alur Pendaftaran</h2>
            </div>

            @php $isVerifActive = ($settings['verification_active'] ?? '1') == '1'; @endphp

            <div class="space-y-3">
                @if($isVerifActive)
                    {{-- STEP 1 --}}
                    <div class="flex gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100 items-start">
                        <div class="w-8 h-8 rounded-full bg-white border border-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-xs shadow-sm shrink-0 mt-0.5">1</div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Download Berkas</h3>
                            <p class="text-[11px] text-slate-500 leading-snug mt-1">Unduh template surat perjanjian, cetak, isi manual, dan tanda tangani.</p>
                        </div>
                    </div>
                    {{-- STEP 2 --}}
                    <div class="flex gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100 items-start">
                        <div class="w-8 h-8 rounded-full bg-white border border-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs shadow-sm shrink-0 mt-0.5">2</div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Upload & Verifikasi</h3>
                            <p class="text-[11px] text-slate-500 leading-snug mt-1">Upload foto berkas. Admin akan memverifikasi data Anda.</p>
                        </div>
                    </div>
                    {{-- STEP 3 --}}
                    <div class="flex gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100 items-start">
                        <div class="w-8 h-8 rounded-full bg-white border border-purple-100 text-purple-600 flex items-center justify-center font-bold text-xs shadow-sm shrink-0 mt-0.5">3</div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Isi Formulir & Lulus</h3>
                            <p class="text-[11px] text-slate-500 leading-snug mt-1">Dapatkan token via WA, isi biodata lengkap, dan cetak bukti pendaftaran.</p>
                        </div>
                    </div>
                @else
                    {{-- BYPASS MODE --}}
                    <div class="p-5 rounded-2xl bg-emerald-50 border border-emerald-100 text-center">
                        <div class="text-2xl mb-2">🚀</div>
                        <h3 class="text-sm font-bold text-emerald-900">Pendaftaran Langsung</h3>
                        <p class="text-[11px] text-emerald-700 mt-1">Klik tombol daftar di atas, isi formulir biodata lengkap, selesai.</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- 4. BIAYA (Horizontal Cards Kecil) --}}
    @php $rincianBiaya = $rincianBiaya ?? []; @endphp
    <section class="py-10 md:py-16 bg-slate-50 border-y border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="flex justify-between items-end mb-6">
                <h2 class="text-lg md:text-2xl font-bold text-slate-900">Biaya Pendidikan</h2>
                <span class="text-[10px] text-slate-400">Geser untuk melihat &rarr;</span>
            </div>

            <div class="flex overflow-x-auto gap-3 pb-6 hide-scroll snap-x snap-mandatory">
                @foreach($rincianBiaya as $jenjang => $data)
                <div class="snap-center shrink-0 w-[240px] md:w-[280px] bg-white rounded-2xl p-5 border border-slate-200 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-20 h-20 bg-emerald-50 rounded-bl-full -mr-4 -mt-4"></div>
                    
                    <div class="relative z-10">
                        <span class="px-2 py-1 rounded bg-slate-100 text-[10px] font-bold text-slate-500 uppercase tracking-wide mb-3 inline-block">{{ $jenjang }}</span>
                        <p class="text-[10px] text-slate-400">Total Estimasi</p>
                        <p class="text-xl md:text-2xl font-extrabold text-emerald-600 mb-4">Rp {{ number_format($data['total'], 0, ',', '.') }}</p>
                        
                        <div class="space-y-2 border-t border-slate-100 pt-3">
                            @foreach($data['items'] as $item)
                            <div class="flex justify-between text-[10px]">
                                <span class="text-slate-500 truncate max-w-[60%]">{{ $item->nama_pembayaran }}</span>
                                <span class="font-bold text-slate-700">{{ number_format($item->nominal, 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- 5. FASILITAS & PERSYARATAN (Grid 2 Kolom) --}}
    <section class="py-10 md:py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Fasilitas --}}
                <div>
                    <h3 class="text-sm md:text-lg font-bold text-slate-900 mb-4">Fasilitas Unggulan</h3>
                    <div class="flex flex-wrap gap-2">
                        @php $facilities = json_decode($settings['fasilitas_sekolah'] ?? '[]', true); @endphp
                        @foreach(array_slice($facilities, 0, 8) as $fac)
                            <span class="px-3 py-1.5 bg-slate-50 border border-slate-100 rounded-lg text-[10px] md:text-xs font-semibold text-slate-600">
                                {{ $fac }}
                            </span>
                        @endforeach
                    </div>
                </div>

                {{-- Persyaratan --}}
                <div>
                    <h3 class="text-sm md:text-lg font-bold text-slate-900 mb-4">Persyaratan Berkas</h3>
                    <ul class="space-y-2">
                        @foreach($syarat as $item)
                            <li class="flex items-center justify-between p-2 rounded-lg border border-slate-100 text-[11px] md:text-xs text-slate-600">
                                <span>{{ $item['nama'] }}</span>
                                <span class="font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded">{{ $item['jumlah'] }}x</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

            </div>
        </div>
    </section>

    {{-- 6. GALERI (Compact Grid) --}}
    @php $galleries = json_decode($settings['galeri_sekolah'] ?? '[]', true); @endphp
    @if(count($galleries) > 0)
    <section class="py-10 bg-slate-50 border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <h2 class="text-sm md:text-lg font-bold text-slate-900 mb-4">Galeri Kegiatan</h2>
            <div class="grid grid-cols-3 md:grid-cols-5 gap-2">
                @foreach($galleries as $img)
                    <div class="aspect-square rounded-lg overflow-hidden bg-slate-200">
                        <img src="{{ asset('storage/'.$img) }}" loading="lazy" class="w-full h-full object-cover">
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- FOOTER (Clean & Simple) --}}
    <footer class="bg-white py-8 border-t border-slate-100 text-center">
        <div class="max-w-md mx-auto px-4">
            <div class="w-10 h-10 bg-slate-900 rounded-xl flex items-center justify-center text-white font-bold text-lg mx-auto mb-4 overflow-hidden p-1">
                @if(!empty($settings['logo_sekolah']))
                    <img src="{{ asset('storage/'.$settings['logo_sekolah']) }}" class="w-full h-full object-contain">
                @else
                    {{ substr($settings['nama_sekolah'] ?? 'P', 0, 1) }}
                @endif
            </div>
            <h3 class="text-sm font-bold text-slate-900 mb-1">{{ $settings['nama_sekolah'] ?? 'PPDB Online' }}</h3>
            <p class="text-[10px] text-slate-400 mb-6 leading-relaxed">{{ $settings['alamat_sekolah'] ?? 'Alamat belum diatur.' }}</p>
            
            <a href="https://wa.me/{{ $settings['whatsapp_admin'] ?? '' }}" target="_blank" class="inline-flex items-center gap-2 px-5 py-2 bg-emerald-600 rounded-full text-[10px] font-bold text-white hover:bg-emerald-700 transition">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                Chat WhatsApp
            </a>
            
            <p class="text-[9px] text-slate-400 mt-6">&copy; {{ date('Y') }} All Rights Reserved.</p>
        </div>
    </footer>

</body>
</html>