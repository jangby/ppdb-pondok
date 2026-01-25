<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            {{ __('Loket Panggilan Pemberkasan') }}
        </h2>
    </x-slot>

    <div class="py-12 px-4 max-w-4xl mx-auto">
        
        <div class="bg-blue-600 text-white p-6 rounded-2xl shadow-lg mb-8 text-center relative overflow-hidden">
            <div class="relative z-10">
                <h3 class="text-blue-100 font-bold tracking-widest text-sm uppercase mb-2">Nomor Antrian Aktif (Di Loket Anda)</h3>
                
                <h1 id="lblAntrian" class="text-9xl font-black mb-2">--</h1>
                
                <div class="border-t border-blue-400/50 pt-4 mt-4 w-1/2 mx-auto">
                    <p id="lblNama" class="text-2xl font-bold truncate">-</p>
                    <p id="lblNoDaftar" class="text-sm text-blue-200">-</p>
                </div>
            </div>
            <div class="absolute -right-10 -bottom-20 w-60 h-60 bg-white opacity-10 rounded-full blur-3xl"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <button onclick="recall()" id="btnRecall" disabled class="bg-white border-2 border-gray-200 text-gray-500 py-6 rounded-2xl font-bold text-xl hover:bg-gray-50 hover:text-gray-700 transition shadow-sm flex flex-col items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                Panggil Ulang
                <span class="text-xs font-normal text-gray-400">Bunyikan suara lagi</span>
            </button>

            <button onclick="callNext()" id="btnNext" class="bg-indigo-600 text-white py-6 rounded-2xl font-bold text-xl hover:bg-indigo-700 hover:shadow-lg hover:scale-[1.02] transition flex flex-col items-center justify-center gap-2 active:scale-95">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                PANGGIL SELANJUTNYA
                <span class="text-xs font-normal text-indigo-200">Otomatis ambil antrian baru</span>
            </button>

        </div>

        <div class="mt-8 text-center text-gray-400 text-xs">
            <p>Tips: Pastikan volume HP/Laptop dikeraskan dan tersambung ke speaker.</p>
        </div>
    </div>

    <script>
        // Data antrian yang sedang aktif di layar panitia ini
        let currentQueue = null;
        let isSpeaking = false;

        // Siapkan Audio Chime (Ting-Tung) - Opsional, ambil dari internet/aset
        // Kalau tidak ada file mp3, hapus bagian audio.play() nanti
        // const chime = new Audio('https://www.soundjay.com/buttons/beep-01a.mp3'); 

        async function callNext() {
            const btnNext = document.getElementById('btnNext');
            const btnRecall = document.getElementById('btnRecall');
            
            // Loading State
            btnNext.disabled = true;
            btnNext.innerHTML = '<svg class="animate-spin h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Mencari...';

            try {
                const response = await fetch("{{ route('admin.queue.next') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    }
                });
                const data = await response.json();

                if (data.status === 'success') {
                    // Update Data Lokal
                    currentQueue = data.data;
                    
                    // Update Tampilan UI
                    document.getElementById('lblAntrian').innerText = currentQueue.antrian;
                    document.getElementById('lblNama').innerText = currentQueue.nama;
                    document.getElementById('lblNoDaftar').innerText = currentQueue.no_daftar;

                    // Aktifkan Tombol Recall
                    btnRecall.disabled = false;
                    btnRecall.classList.remove('opacity-50', 'cursor-not-allowed');

                    // Mainkan Suara
                    speakQueue(currentQueue.antrian);

                } else {
                    alert('Antrian Kosong! Belum ada santri baru yang check-in.');
                }

            } catch (error) {
                console.error(error);
                alert('Terjadi kesalahan koneksi.');
            } finally {
                // Reset Tombol
                btnNext.disabled = false;
                btnNext.innerHTML = `<svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                PANGGIL SELANJUTNYA
                <span class="text-xs font-normal text-indigo-200">Otomatis ambil antrian baru</span>`;
            }
        }

        function recall() {
            if (currentQueue) {
                speakQueue(currentQueue.antrian);
            }
        }

        // --- FUNGSI TEXT TO SPEECH (BAHASA INDONESIA) ---
        function speakQueue(number) {
            // Batalkan suara sebelumnya jika ada
            window.speechSynthesis.cancel();

            // 1. Siapkan Kalimat
            // Trik: Tambahkan spasi/koma agar intonasi Google natural
            const text = `Nomor Antrian, . ${number}. , Silakan menuju meja pemberkasan.`;

            // 2. Buat Utterance
            const utterance = new SpeechSynthesisUtterance(text);
            
            // 3. Konfigurasi Bahasa
            utterance.lang = 'id-ID'; // Bahasa Indonesia
            utterance.rate = 0.9;     // Kecepatan (0.1 - 10) - Agak lambat biar jelas
            utterance.pitch = 1;      // Nada

            // Opsional: Pilih Voice Wanita Indonesia jika tersedia di browser
            const voices = window.speechSynthesis.getVoices();
            const idVoice = voices.find(v => v.lang === 'id-ID');
            if(idVoice) utterance.voice = idVoice;

            // 4. Mainkan (Bunyi Beep dulu kalau mau)
            // chime.play(); 
            // setTimeout(() => {
                window.speechSynthesis.speak(utterance);
            // }, 1000);
        }

        // Memuat voice list saat halaman dibuka (kadang perlu delay di Chrome)
        window.speechSynthesis.onvoiceschanged = () => {
            window.speechSynthesis.getVoices();
        };
    </script>
</x-app-layout>