<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitor Antrian - Live</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Font yang lebih modern */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap');
        body { font-family: 'Inter', sans-serif; }

        /* Animasi Halus saat Angka Berubah */
        .number-transition { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .number-changing { opacity: 0; transform: translateY(20px) scale(0.9); }

        /* Background Pattern Halus */
        .bg-pattern {
            background-color: #f8fafc;
            background-image: radial-gradient(#e2e8f0 1px, transparent 1px);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="bg-pattern h-screen flex flex-col items-center justify-center relative overflow-hidden">

    <div class="absolute top-0 right-0 w-96 h-96 bg-blue-200 rounded-full blur-[120px] opacity-30 -translate-y-1/2 translate-x-1/2"></div>
    <div class="absolute bottom-0 left-0 w-80 h-80 bg-indigo-200 rounded-full blur-[100px] opacity-30 translate-y-1/2 -translate-x-1/2"></div>

    <main class="relative z-10 w-full max-w-md px-6 text-center">
        
        <div class="mb-8">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border border-blue-100 text-blue-700 text-xs font-bold uppercase tracking-wider shadow-sm">
                <span class="relative flex h-2 w-2">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-600"></span>
                </span>
                Live Update
            </span>
            <h1 class="mt-4 text-2xl font-bold text-gray-800">Antrian Pemberkasan</h1>
            <p class="text-gray-500 text-sm">PPDB Pondok Pesantren</p>
        </div>
        
        <div class="bg-white/70 backdrop-blur-xl rounded-[2.5rem] p-10 shadow-2xl border border-white/50 relative overflow-hidden group">
            
            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-blue-400 via-indigo-500 to-blue-400"></div>
            
            <p class="text-blue-600 text-xs font-bold uppercase tracking-[0.2em] mb-6">Sedang Dilayani</p>
            
            <div class="relative h-40 flex items-center justify-center">
                <h1 id="lblAntrian" class="text-[10rem] font-black text-gray-900 leading-none tracking-tighter drop-shadow-sm number-transition">
                    --
                </h1>
            </div>

            <div class="flex items-center gap-4 my-8 opacity-50">
                <div class="h-px bg-gray-300 flex-1"></div>
                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"></path></svg>
                <div class="h-px bg-gray-300 flex-1"></div>
            </div>

            <div class="space-y-2">
                <h3 id="lblNama" class="text-xl font-bold text-gray-800 truncate">
                    Menunggu panggilan...
                </h3>
                <span id="lblNoDaftar" class="inline-block bg-gray-100 text-gray-500 text-xs font-mono font-bold px-3 py-1 rounded-lg">
                    No. Daftar: -
                </span>
            </div>
        </div>

        <p class="mt-10 text-gray-500 text-sm px-6 leading-relaxed font-medium">
            Silakan bersiap jika nomor Anda sudah dekat.<br>
            <span class="text-gray-400 text-xs font-normal">Halaman ini diperbarui otomatis.</span>
        </p>

    </main>

    <script>
        setInterval(checkStatus, 3000); // Cek tiap 3 detik

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
                    if (antrianEl.innerText !== currentAntrianStr) {
                        
                        // 1. Tambahkan class animasi keluar
                        antrianEl.classList.add('number-changing');
                        
                        // 2. Tunggu animasi selesai, lalu update teks
                        setTimeout(() => {
                            antrianEl.innerText = currentAntrianStr;
                            namaEl.innerText = result.data.nama;
                            noEl.innerText = "No. Daftar: " + result.data.no_daftar;
                            
                            // 3. Hapus class animasi agar muncul kembali
                            antrianEl.classList.remove('number-changing');
                        }, 300); // Sesuaikan dengan durasi transisi CSS (0.3s)
                    }
                }
            } catch (error) {
                console.error("Gagal memuat antrian:", error);
            }
        }
        checkStatus();
    </script>
</body>
</html>