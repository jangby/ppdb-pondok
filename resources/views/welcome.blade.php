<?php
    use App\Models\Setting;
    use Carbon\Carbon;

    // --- [1] DATA UTAMA ---
    $namaSekolah = Setting::getValue('nama_sekolah', 'Pondok Pesantren');
    $alamatSekolah = Setting::getValue('alamat_sekolah', 'Alamat Sekolah Belum Diisi');
    $logo = Setting::getValue('logo_sekolah'); 
    
    // Banner & Status
    $bannerPath = Setting::getValue('banner_image'); 
    $bannerUrl = $bannerPath ? asset('storage/' . $bannerPath) : null;
    $gelombang = Setting::getValue('nama_gelombang', 'Gelombang Umum');
    $status = Setting::isOpen(); 
    
    // Tanggal
    Carbon::setLocale('id');
    $tglBuka = Setting::getValue('tgl_buka') ? Carbon::parse(Setting::getValue('tgl_buka'))->translatedFormat('d F Y') : '-';
    $tglTutup = Setting::getValue('tgl_tutup') ? Carbon::parse(Setting::getValue('tgl_tutup'))->translatedFormat('d F Y') : '-';
    
    $wajibVerifikasi = Setting::getValue('verification_active', '1') == '1';
    $wa = Setting::getValue('whatsapp_admin', '6281234567890');
    $syaratList = json_decode(Setting::getValue('syarat_pendaftaran'), true) ?? [];

    // --- [2] LOGIKA INFINITE SCROLL ---
    // Kita duplikasi array jenjang agar bisa looping tanpa putus (Seamless)
    $listJenjangAsli = json_decode(Setting::getValue('list_jenjang'), true) ?? [];
    $listJenjang = array_merge($listJenjangAsli, $listJenjangAsli); // Duplikasi x2
?>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>PPDB {{ $namaSekolah }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        h1, h2, h3, h4, h5, h6 { font-family: 'Outfit', sans-serif; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        .ambient-bg { position: absolute; width: 100%; height: 100%; overflow: hidden; z-index: -1; background-color: #f8fafc; }
        .blob-green { position: absolute; top: -10%; right: -5%; width: 500px; height: 500px; background: radial-gradient(circle, rgba(16, 185, 129, 0.15) 0%, rgba(255,255,255,0) 70%); border-radius: 50%; filter: blur(60px); }
        .blob-teal { position: absolute; bottom: 10%; left: -10%; width: 400px; height: 400px; background: radial-gradient(circle, rgba(20, 184, 166, 0.1) 0%, rgba(255,255,255,0) 70%); border-radius: 50%; filter: blur(60px); }

        .glass-card { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.8); box-shadow: 0 4px 20px rgba(16, 185, 129, 0.05); }
        .nav-glass { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(16px); border-bottom: 1px solid rgba(0,0,0,0.03); }

        /* Hide Scrollbar */
        .hide-scroll::-webkit-scrollbar { display: none; }
        .hide-scroll { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-slate-50 text-slate-600 antialiased selection:bg-emerald-100 selection:text-emerald-900" x-data="{ showBrochure: false }">

    <div class="fixed inset-0 pointer-events-none ambient-bg">
        <div class="blob-green animate-pulse" style="animation-duration: 8s;"></div>
        <div class="blob-teal animate-pulse" style="animation-duration: 10s;"></div>
    </div>

    {{-- NAVBAR --}}
    <nav class="fixed top-0 w-full z-50 nav-glass transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="flex justify-between items-center h-14 md:h-16">
                <div class="flex items-center gap-2">
                    @if(!empty($logo))
                        <img src="{{ asset('storage/'.$logo) }}" class="w-8 h-8 md:w-10 md:h-10 object-contain">
                    @else
                        <div class="w-8 h-8 bg-emerald-600 rounded-lg flex items-center justify-center text-white font-bold text-sm shadow-md shadow-emerald-200">{{ substr($namaSekolah, 0, 1) }}</div>
                    @endif
                    <div class="flex flex-col">
                        <span class="font-bold text-slate-800 text-xs md:text-sm tracking-tight leading-none">PPDB Online</span>
                        <span class="text-[9px] md:text-[10px] text-emerald-600 font-medium uppercase tracking-wide mt-0.5">{{ substr($namaSekolah, 0, 18) }}</span>
                    </div>
                </div>
                @auth
                    <a href="{{ url('/dashboard') }}" class="text-[10px] font-bold bg-slate-900 text-white px-4 py-1.5 rounded-full hover:bg-slate-800 transition">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-[10px] font-bold text-slate-600 bg-white border border-slate-200 px-3 py-1.5 rounded-full hover:border-emerald-300 hover:text-emerald-600 transition shadow-sm">Login Admin</a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- HERO SECTION --}}
    <section class="relative pt-20 pb-10 md:pt-32 md:pb-20 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 relative z-10 text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border border-slate-100 shadow-sm mb-6">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $status ? 'bg-emerald-400' : 'bg-red-400' }} opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 {{ $status ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                </span>
                <span class="text-[10px] md:text-xs font-bold uppercase tracking-wider {{ $status ? 'text-emerald-600' : 'text-red-600' }}">
                    {{ $status ? 'Pendaftaran Dibuka' : 'Pendaftaran Ditutup' }}
                </span>
            </div>

            <h1 class="text-3xl sm:text-5xl md:text-6xl font-extrabold text-slate-900 leading-[1.15] mb-4">
                {{ Setting::getValue('pengumuman', 'Penerimaan Santri Baru') }}
            </h1>
            <p class="text-xs sm:text-sm md:text-lg text-slate-500 mb-8 leading-relaxed max-w-xl mx-auto px-4">
                {{ Setting::getValue('deskripsi_banner', 'Mewujudkan generasi Rabbani yang unggul dalam IMTAQ dan IPTEK.') }}
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 w-full max-w-[260px] sm:max-w-md mx-auto">
                @if($status)
                    <a href="{{ route('pendaftaran.create') }}" class="w-full sm:w-auto px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-lg shadow-emerald-600/20 transition transform active:scale-95 flex items-center justify-center gap-2">
                        <span>Daftar Sekarang</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </a>
                @else
                    <button disabled class="w-full sm:w-auto px-6 py-3 bg-slate-100 text-slate-400 text-xs sm:text-sm font-bold rounded-xl cursor-not-allowed border border-slate-200">Belum Dibuka</button>
                @endif
                <a href="#info" class="w-full sm:w-auto px-6 py-3 bg-white border border-slate-200 text-slate-600 hover:text-emerald-600 hover:border-emerald-200 text-xs sm:text-sm font-bold rounded-xl transition active:scale-95">
                    Informasi & Alur
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mt-10 max-w-3xl mx-auto px-2">
                <div class="glass-card p-3 md:p-4 rounded-xl text-left"><p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider mb-1">Gelombang</p><p class="text-xs md:text-base font-bold text-slate-800 truncate">{{ $gelombang }}</p></div>
                <div class="glass-card p-3 md:p-4 rounded-xl text-left"><p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider mb-1">Periode</p><p class="text-xs md:text-base font-bold text-emerald-600">{{ $tglBuka }} s/d {{ $tglTutup }}</p></div>
                <div class="glass-card p-3 md:p-4 rounded-xl text-left col-span-2 md:col-span-1"><p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider mb-1">Lokasi</p><p class="text-xs md:text-sm font-medium text-slate-600 line-clamp-1">{{ $alamatSekolah }}</p></div>
            </div>
        </div>
    </section>

    {{-- ALUR PENDAFTARAN --}}
    <section id="info" class="py-10 md:py-16 bg-white border-t border-slate-100">
        <div class="max-w-2xl mx-auto px-4 sm:px-6">
            <div class="text-center mb-8">
                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md uppercase tracking-wider">Panduan</span>
                <h2 class="text-xl md:text-3xl font-bold text-slate-900 mt-2">Alur Pendaftaran</h2>
            </div>
            <div class="space-y-3">
                @if($wajibVerifikasi)
                    <div class="flex gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100 items-start">
                        <div class="w-8 h-8 rounded-full bg-white border border-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-xs shadow-sm shrink-0 mt-0.5">1</div>
                        <div><h3 class="text-sm font-bold text-slate-800">Download Berkas</h3><p class="text-[11px] text-slate-500 leading-snug mt-1">Unduh template surat perjanjian, cetak, isi manual, dan tanda tangani.</p></div>
                    </div>
                    <div class="flex gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100 items-start">
                        <div class="w-8 h-8 rounded-full bg-white border border-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs shadow-sm shrink-0 mt-0.5">2</div>
                        <div><h3 class="text-sm font-bold text-slate-800">Upload & Verifikasi</h3><p class="text-[11px] text-slate-500 leading-snug mt-1">Upload foto berkas. Admin akan memverifikasi data Anda.</p></div>
                    </div>
                    <div class="flex gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100 items-start">
                        <div class="w-8 h-8 rounded-full bg-white border border-purple-100 text-purple-600 flex items-center justify-center font-bold text-xs shadow-sm shrink-0 mt-0.5">3</div>
                        <div><h3 class="text-sm font-bold text-slate-800">Isi Formulir & Lulus</h3><p class="text-[11px] text-slate-500 leading-snug mt-1">Dapatkan token via WA, isi biodata lengkap, dan cetak bukti pendaftaran.</p></div>
                    </div>
                @else
                    <div class="p-5 rounded-2xl bg-emerald-50 border border-emerald-100 text-center">
                        <div class="text-2xl mb-2">🚀</div>
                        <h3 class="text-sm font-bold text-emerald-900">Pendaftaran Langsung</h3>
                        <p class="text-[11px] text-emerald-700 mt-1">Klik tombol daftar di atas, isi formulir biodata lengkap, selesai.</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- INFORMASI PEMBAYARAN (AUTO SCROLL HORIZONTAL) --}}
    @if(count($listJenjang) > 0)
    <section class="py-10 md:py-16 bg-slate-50 border-y border-slate-200 overflow-hidden" 
             x-data="{
                scrollAmount: 0,
                startScroll() {
                    this.timer = setInterval(() => {
                        let el = this.$refs.scrollContainer;
                        // Jika sudah mencapai setengah (titik duplikasi), reset ke 0 secara instan
                        if (el.scrollLeft >= el.scrollWidth / 2) {
                            el.scrollLeft = 0; 
                        } else {
                            el.scrollLeft += 1; // Kecepatan Scroll (Makin besar makin cepat)
                        }
                    }, 25); // Interval update (Makin kecil makin halus)
                },
                init() {
                    // Gunakan IntersectionObserver agar scroll jalan otomatis saat terlihat
                    let observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if(entry.isIntersecting) {
                                this.startScroll();
                            } else {
                                clearInterval(this.timer);
                            }
                        });
                    });
                    observer.observe(this.$el);
                }
             }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="text-center mb-8">
                <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-md uppercase tracking-wider">Info Biaya</span>
                <h2 class="text-xl md:text-3xl font-bold text-slate-900 mt-2">Informasi Pembayaran</h2>
                <p class="text-xs text-slate-500 mt-2">Klik jenjang untuk menanyakan biaya via WhatsApp.</p>
            </div>

            {{-- Container Scroll --}}
            <div class="relative w-full overflow-hidden">
                <div x-ref="scrollContainer" class="flex gap-4 overflow-x-auto hide-scroll pb-4 px-4 items-center whitespace-nowrap" style="scroll-behavior: auto;">
                    @foreach($listJenjang as $index => $jenjang)
                        @php
                            $pesan = "Assalamu'alaikum Admin, saya ingin menanyakan rincian pembayaran untuk jenjang *{$jenjang}*. Mohon informasinya.";
                            $linkWa = "https://wa.me/{$wa}?text=" . urlencode($pesan);
                        @endphp
                        <a href="{{ $linkWa }}" target="_blank" class="shrink-0 bg-white rounded-2xl p-6 border border-slate-200 shadow-sm w-[200px] md:w-[220px] text-center relative overflow-hidden group hover:border-emerald-300 hover:shadow-md transition cursor-pointer">
                            <div class="absolute top-0 right-0 w-12 h-12 bg-emerald-50 rounded-bl-full -mr-3 -mt-3 group-hover:bg-emerald-100 transition"></div>
                            <div class="w-12 h-12 mx-auto bg-slate-100 rounded-full flex items-center justify-center text-xl mb-3 group-hover:bg-green-500 group-hover:text-white transition">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                            </div>
                            <h3 class="text-lg md:text-xl font-extrabold text-slate-800">{{ $jenjang }}</h3>
                            <p class="text-[10px] text-slate-400 mt-1 group-hover:text-emerald-500">Tanya Biaya via WA &rarr;</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- BROSUR (FULL SCREEN MODAL + DOWNLOAD) --}}
    @if($bannerUrl)
    <section class="py-10 md:py-16 bg-white border-b border-slate-100">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center">
            <div class="mb-6">
                <h2 class="text-lg md:text-2xl font-bold text-slate-900">Brosur Informasi</h2>
                <p class="text-xs text-slate-500 mt-1">Klik gambar untuk memperbesar.</p>
            </div>

            <div class="relative rounded-2xl overflow-hidden shadow-lg border border-slate-200 bg-slate-50 group cursor-zoom-in" @click="showBrochure = true">
                <img src="{{ $bannerUrl }}" alt="Brosur PPDB" class="w-full h-auto object-cover max-h-[400px]">
                <div class="absolute inset-0 bg-black/10 flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-300">
                    <span class="bg-black/50 text-white px-4 py-2 rounded-full text-xs font-bold backdrop-blur-sm">🔍 Lihat Full Screen</span>
                </div>
            </div>
        </div>
    </section>

    {{-- MODAL BROSUR --}}
    <div x-show="showBrochure" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[100] flex items-center justify-center bg-black/90 p-4 backdrop-blur-sm"
         style="display: none;">
         
         <button @click="showBrochure = false" class="absolute top-4 right-4 text-white hover:text-gray-300 z-[101]">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
         </button>

         <div class="relative max-w-full max-h-full flex flex-col items-center gap-4">
             <img src="{{ $bannerUrl }}" class="max-w-full max-h-[80vh] object-contain rounded-lg shadow-2xl" @click.outside="showBrochure = false">
             
             {{-- TOMBOL DOWNLOAD DALAM MODAL --}}
             <a href="{{ $bannerUrl }}" download="Brosur-PPDB.jpg" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-full font-bold text-sm shadow-lg transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Download Brosur
             </a>
         </div>
    </div>
    @endif

    {{-- FASILITAS & PERSYARATAN --}}
    <section class="py-10 md:py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Fasilitas --}}
                <div>
                    <h3 class="text-sm md:text-lg font-bold text-slate-900 mb-4">Fasilitas</h3>
                    <div class="flex flex-wrap gap-2">
                        @php 
                            // Ambil data fasilitas (Tanpa Batasan)
                            $facilities = json_decode(Setting::getValue('fasilitas_sekolah'), true) ?? []; 
                        @endphp

                        {{-- Loop langsung ke semua data (Hapus array_slice) --}}
                        @foreach($facilities as $fac)
                            <span class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-[10px] md:text-xs font-semibold text-slate-600 shadow-sm hover:border-emerald-400 hover:text-emerald-600 transition cursor-default">
                                {{ $fac }}
                            </span>
                        @endforeach

                        @if(empty($facilities))
                            <span class="text-xs text-slate-400 italic">Belum ada data fasilitas.</span>
                        @endif
                    </div>
                </div>

                {{-- Persyaratan --}}
                <div>
                    <h3 class="text-sm md:text-lg font-bold text-slate-900 mb-4">Persyaratan Berkas</h3>
                    <ul class="space-y-2">
                        @foreach($syaratList as $item)
                            <li class="flex items-center justify-between p-2 rounded-lg bg-white border border-slate-200 text-[11px] md:text-xs text-slate-600 shadow-sm">
                                <span>{{ $item['nama'] }}</span>
                                <span class="font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded">{{ $item['jumlah'] }}x</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

            </div>
        </div>
    </section>

    {{-- GALERI --}}
    @php $galleries = json_decode(Setting::getValue('galeri_sekolah'), true) ?? []; @endphp
    @if(count($galleries) > 0)
    <section class="py-10 bg-white border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <h2 class="text-sm md:text-lg font-bold text-slate-900 mb-4">Galeri Kegiatan</h2>
            <div class="grid grid-cols-3 md:grid-cols-5 gap-2">
                @foreach($galleries as $img)
                    <div class="aspect-square rounded-lg overflow-hidden bg-slate-200 hover:opacity-90 transition cursor-pointer">
                        <img src="{{ asset('storage/'.$img) }}" loading="lazy" class="w-full h-full object-cover">
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- FOOTER --}}
    <footer class="bg-white py-8 border-t border-slate-100 text-center">
        <div class="max-w-md mx-auto px-4">
            <div class="w-10 h-10 bg-slate-900 rounded-xl flex items-center justify-center text-white font-bold text-lg mx-auto mb-4 overflow-hidden p-1">
                @if(!empty($logo))<img src="{{ asset('storage/'.$logo) }}" class="w-full h-full object-contain">@else {{ substr($namaSekolah, 0, 1) }} @endif
            </div>
            <h3 class="text-sm font-bold text-slate-900 mb-1">{{ $namaSekolah }}</h3>
            <p class="text-[10px] text-slate-400 mb-6 leading-relaxed">{{ $alamatSekolah }}</p>
            <a href="https://wa.me/{{ $wa }}" target="_blank" class="inline-flex items-center gap-2 px-5 py-2 bg-emerald-600 rounded-full text-[10px] font-bold text-white hover:bg-emerald-700 transition">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                Chat WhatsApp
            </a>
            <p class="text-[9px] text-slate-400 mt-6">&copy; {{ date('Y') }} All Rights Reserved.</p>
        </div>
    </footer>

</body>
</html>