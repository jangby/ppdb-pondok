<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitor Antrian - Layar Besar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800;900&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* Animasi Halus saat Angka Berubah */
        .number-transition { transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1); }
        .number-changing { opacity: 0; transform: translateY(40px) scale(0.9); }

        /* Background Pattern Halus */
        .bg-pattern {
            background-color: #f8fafc;
            background-image: radial-gradient(#cbd5e1 2px, transparent 2px);
            background-size: 40px 40px;
        }

        /* Hilangkan Scrollbar */
        ::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="bg-pattern h-screen w-screen flex flex-col overflow-hidden">

    {{-- HEADER: LOGO DAN JAM --}}
    <header class="bg-blue-900 text-white px-10 py-6 flex justify-between items-center shadow-lg relative z-20">
        <div class="flex items-center gap-6">
            {{-- Logo Pondok (Bisa diganti image img tag) --}}
            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center text-blue-900 font-black text-2xl shadow-inner">
                {{ substr(App\Models\Setting::getValue('nama_sekolah') ?? 'P', 0, 1) }}
            </div>
            <div>
                <h1 class="text-4xl font-black uppercase tracking-wider">{{ App\Models\Setting::getValue('nama_sekolah') ?? 'PONDOK PESANTREN' }}</h1>
                <p class="text-blue-300 font-semibold tracking-widest mt-1 text-lg">MONITOR ANTRIAN PENDAFTARAN</p>
            </div>
        </div>
        <div class="text-right border-l-4 border-blue-700 pl-8">
            <h2 id="clock" class="text-5xl font-mono font-black tracking-tighter">00:00:00</h2>
            <p id="date" class="text-blue-300 font-semibold mt-1 text-lg">Memuat tanggal...</p>
        </div>
    </header>

    {{-- KONTEN UTAMA: NOMOR ANTRIAN --}}
    <main class="flex-1 relative flex items-center justify-center overflow-hidden">
        
        {{-- Hiasan Background Layar --}}
        <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-blue-300 rounded-full blur-[150px] opacity-20 -translate-y-1/2 translate-x-1/3"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-indigo-300 rounded-full blur-[150px] opacity-20 translate-y-1/3 -translate-x-1/3"></div>

        <div class="bg-white/80 backdrop-blur-2xl rounded-[3rem] p-16 shadow-2xl border-4 border-white relative w-full max-w-6xl text-center flex flex-col items-center">
            
            <div class="inline-flex items-center gap-3 px-6 py-2 rounded-full bg-blue-50 border-2 border-blue-100 text-blue-700 text-xl font-bold uppercase tracking-[0.2em] mb-8 shadow-sm">
                <span class="relative flex h-4 w-4">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500"></span>
                </span>
                Sedang Dilayani
            </div>
            
            {{-- AREA NOMOR ANTRIAN (SANGAT BESAR) --}}
            <div class="relative h-[22rem] w-full flex items-center justify-center my-4">
                <h1 id="lblAntrian" class="text-[20rem] font-black text-gray-900 leading-none tracking-tighter drop-shadow-md number-transition">
                    --
                </h1>
            </div>

            <div class="w-2/3 h-1.5 bg-gradient-to-r from-transparent via-gray-300 to-transparent my-10"></div>

            {{-- AREA NAMA SANTRI --}}
            <div class="space-y-4">
                <h3 id="lblNama" class="text-6xl font-extrabold text-gray-800 truncate px-4">
                    Menunggu Panggilan...
                </h3>
                <div class="mt-4">
                    <span id="lblNoDaftar" class="inline-block bg-gray-100 border-2 border-gray-200 text-gray-500 text-3xl font-mono font-bold px-8 py-3 rounded-2xl">
                        No. Daftar: -
                    </span>
                </div>
            </div>

        </div>
    </main>

    {{-- FOOTER: TEKS BERJALAN (MARQUEE) --}}
    <footer class="bg-blue-900 text-white flex items-center relative z-20 h-16 shadow-inner">
        <div class="bg-blue-600 h-full flex items-center px-8 font-black uppercase tracking-widest text-xl shrink-0 shadow-lg relative z-10">
            📌 INFORMASI
        </div>
        <marquee class="text-2xl font-semibold text-blue-100 flex-1 tracking-wide" scrollamount="10">
            Selamat datang para calon wali santri di {{ App\Models\Setting::getValue('nama_sekolah') ?? 'Pondok Pesantren' }}. Harap mempersiapkan berkas-berkas pendaftaran dan memperhatikan layar monitor untuk mengetahui giliran Anda.
        </marquee>
    </footer>

    {{-- SCRIPT: CLOCK & AUTO-UPDATE --}}
    <script>
        // 1. JAM DIGITAL REAL-TIME
        function updateClock() {
            const now = new Date();
            document.getElementById('clock').innerText = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }).replace(/\./g, ':');
            document.getElementById('date').innerText = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        }
        setInterval(updateClock, 1000);
        updateClock();

        // 2. CHECK STATUS ANTRIAN (Tiap 3 Detik)
        setInterval(checkStatus, 3000); 

        // Fungsi opsional untuk membunyikan bel (Bisa disiapkan file audio bel.mp3 di folder public/assets)
        // const audioBel = new Audio('{{ asset("assets/bel.mp3") }}'); 

        async function checkStatus() {
            try {
                const response = await fetch("{{ route('public.queue.check') }}");
                const result = await response.json();

                if (result.data) {
                    const antrianEl = document.getElementById('lblAntrian');
                    const namaEl = document.getElementById('lblNama');
                    const noEl = document.getElementById('lblNoDaftar');

                    const currentAntrianStr = String(result.data.antrian);

                    // Update DOM hanya jika nomor berubah
                    if (antrianEl.innerText !== currentAntrianStr && antrianEl.innerText !== '--') {
                        
                        // Mainkan suara bel (Hapus komentar di bawah jika ada file audio)
                        // audioBel.play().catch(e => console.log("Auto-play diblokir browser"));
                        
                        antrianEl.classList.add('number-changing');
                        namaEl.classList.add('opacity-0'); // Sembunyikan nama sebentar
                        
                        setTimeout(() => {
                            antrianEl.innerText = currentAntrianStr;
                            namaEl.innerText = result.data.nama;
                            noEl.innerText = "No. Daftar: " + result.data.no_daftar;
                            
                            antrianEl.classList.remove('number-changing');
                            namaEl.classList.remove('opacity-0');
                            namaEl.style.transition = "opacity 0.5s ease";
                        }, 500); 
                    } else if (antrianEl.innerText === '--') {
                        // Load pertama kali tanpa animasi
                        antrianEl.innerText = currentAntrianStr;
                        namaEl.innerText = result.data.nama;
                        noEl.innerText = "No. Daftar: " + result.data.no_daftar;
                    }
                }
            } catch (error) {
                console.error("Gagal memuat antrian:", error);
            }
        }
        
        // Panggil pertama kali saat halaman dibuka
        checkStatus();
    </script>
</body>
</html>