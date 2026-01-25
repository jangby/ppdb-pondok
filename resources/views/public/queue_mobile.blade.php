<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Loket Panggilan</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Efek tekan tombol seperti aplikasi */
        .btn-press:active { transform: scale(0.95); }
        body { background-color: #f3f4f6; }
    </style>
</head>
<body class="h-screen flex flex-col">

    <div class="bg-indigo-600 text-white p-4 shadow-md text-center">
        <h1 class="font-bold text-lg uppercase tracking-wider">Loket Pemberkasan</h1>
        <p class="text-xs text-indigo-200">Mode Petugas</p>
    </div>

    <div class="flex-1 flex flex-col justify-center items-center p-6 space-y-6">
        
        <div class="w-full bg-white rounded-3xl shadow-xl p-8 text-center border border-gray-100 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-2 bg-indigo-500"></div>
            
            <p class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-2">Sedang Dipanggil</p>
            
            <h1 id="lblAntrian" class="text-8xl font-black text-gray-800 leading-none mb-4">--</h1>
            
            <div class="border-t border-gray-100 pt-4">
                <h2 id="lblNama" class="text-xl font-bold text-gray-800 truncate px-2">-</h2>
                <p id="lblNoDaftar" class="text-sm text-gray-500 font-mono mt-1">-</p>
            </div>
        </div>

        <div id="statusInfo" class="text-gray-400 text-xs text-center animate-pulse">
            Menunggu perintah...
        </div>

    </div>

    <div class="bg-white p-4 pb-8 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)] rounded-t-3xl border-t border-gray-100">
        <div class="grid grid-cols-5 gap-4">
            
            <button onclick="recall()" id="btnRecall" disabled class="col-span-2 bg-gray-100 text-gray-500 rounded-2xl p-4 font-bold flex flex-col items-center justify-center gap-1 btn-press disabled:opacity-50 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                <span class="text-xs">Ulang</span>
            </button>

            <button onclick="callNext()" id="btnNext" class="col-span-3 bg-indigo-600 text-white rounded-2xl p-4 font-bold flex flex-col items-center justify-center gap-1 shadow-lg shadow-indigo-200 btn-press transition">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                <span class="text-lg">SELANJUTNYA</span>
            </button>
        </div>
    </div>

    <script>
        let currentQueue = null;

        async function callNext() {
            const btnNext = document.getElementById('btnNext');
            const statusInfo = document.getElementById('statusInfo');
            
            // UI Loading
            btnNext.disabled = true;
            btnNext.classList.add('opacity-75');
            statusInfo.innerText = "Mencari data antrian...";

            try {
                const response = await fetch("{{ route('public.queue.next') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                const data = await response.json();

                if (data.status === 'success') {
                    currentQueue = data.data;
                    
                    // Update Tampilan
                    document.getElementById('lblAntrian').innerText = currentQueue.antrian;
                    document.getElementById('lblNama').innerText = currentQueue.nama;
                    document.getElementById('lblNoDaftar').innerText = currentQueue.no_daftar;

                    // Enable Recall
                    document.getElementById('btnRecall').disabled = false;
                    document.getElementById('btnRecall').classList.remove('bg-gray-100', 'text-gray-500');
                    document.getElementById('btnRecall').classList.add('bg-orange-100', 'text-orange-600');

                    statusInfo.innerText = "Memanggil nomor " + currentQueue.antrian + "...";
                    speakQueue(currentQueue.antrian);

                } else {
                    statusInfo.innerText = "Antrian Kosong.";
                    alert('Belum ada antrian baru.');
                }

            } catch (error) {
                console.error(error);
                statusInfo.innerText = "Error Koneksi.";
                alert('Gagal menghubungi server.');
            } finally {
                btnNext.disabled = false;
                btnNext.classList.remove('opacity-75');
            }
        }

        function recall() {
            if (currentQueue) {
                speakQueue(currentQueue.antrian);
            }
        }

        function speakQueue(number) {
            window.speechSynthesis.cancel();
            const text = `Nomor Antrian, . ${number}. , Silakan ke loket pemberkasan.`;
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'id-ID'; 
            utterance.rate = 0.9;
            window.speechSynthesis.speak(utterance);
        }
    </script>
</body>
</html>