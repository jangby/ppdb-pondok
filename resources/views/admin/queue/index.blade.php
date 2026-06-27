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

        <div class="mt-6 bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
            <label class="block text-sm font-bold text-gray-700 mb-2">Panggil Nomor Custom</label>
            <div class="flex flex-col sm:flex-row gap-4">
                <input type="text" id="customQueueInput" placeholder="Masukkan Nomor (Misal: 10 atau A-12)" class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none font-bold text-gray-700 uppercase">
                <button onclick="callCustom()" class="bg-blue-500 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-600 transition flex items-center justify-center gap-2 active:scale-95">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                    Panggil Manual
                </button>
            </div>
        </div>

        <div class="mt-8 text-center text-gray-400 text-xs">
            <p>Tips: Pastikan volume HP/Laptop dikeraskan dan tersambung ke speaker.</p>
        </div>
    </div>

    <script>
        let currentQueue = null;
        let isSpeaking = false;

        // Uncomment dan masukkan URL audio yang valid jika ingin menggunakan bunyi "Ting-Tung"
        // const chime = new Audio('https://www.soundjay.com/buttons/beep-01a.mp3'); 

        async function callNext() {
            const btnNext = document.getElementById('btnNext');
            const btnRecall = document.getElementById('btnRecall');
            
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
                    currentQueue = data.data;
                    updateUI(currentQueue.antrian, currentQueue.nama, currentQueue.no_daftar);
                    speakQueue(currentQueue.antrian);
                } else {
                    alert('Antrian Kosong! Belum ada santri baru yang check-in.');
                }

            } catch (error) {
                console.error(error);
                alert('Terjadi kesalahan koneksi.');
            } finally {
                btnNext.disabled = false;
                btnNext.innerHTML = `<svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                PANGGIL SELANJUTNYA
                <span class="text-xs font-normal text-indigo-200">Otomatis ambil antrian baru</span>`;
            }
        }

        // FITUR BARU: Fungsi untuk memanggil antrian custom
        function callCustom() {
            const inputElement = document.getElementById('customQueueInput');
            const customNumber = inputElement.value.trim().toUpperCase();

            if (!customNumber) {
                alert('Silakan masukkan nomor antrian terlebih dahulu!');
                inputElement.focus();
                return;
            }

            // Set data lokal menjadi custom (karena manual, nama diisi placeholder)
            currentQueue = {
                antrian: customNumber,
                nama: 'Pemanggilan Manual',
                no_daftar: '-'
            };

            // Update UI dan panggil suara
            updateUI(currentQueue.antrian, currentQueue.nama, currentQueue.no_daftar);
            speakQueue(currentQueue.antrian);
            
            // Kosongkan input setelah dipanggil (opsional)
            inputElement.value = '';
        }

        function recall() {
            if (currentQueue) {
                speakQueue(currentQueue.antrian);
            }
        }

        // Fungsi bantuan untuk memperbarui layar dan tombol recall
        function updateUI(antrian, nama, noDaftar) {
            document.getElementById('lblAntrian').innerText = antrian;
            document.getElementById('lblNama').innerText = nama;
            document.getElementById('lblNoDaftar').innerText = noDaftar;

            const btnRecall = document.getElementById('btnRecall');
            btnRecall.disabled = false;
            btnRecall.classList.remove('opacity-50', 'cursor-not-allowed');
        }

        function speakQueue(number) {
            window.speechSynthesis.cancel();

            // Format teks yang akan diucapkan
            const text = `Nomor Antrian, . ${number}. , Silakan menuju meja pemberkasan.`;

            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'id-ID'; 
            utterance.rate = 0.9;     
            utterance.pitch = 1;      

            const voices = window.speechSynthesis.getVoices();
            const idVoice = voices.find(v => v.lang === 'id-ID');
            if(idVoice) utterance.voice = idVoice;

            // Jika variabel chime aktif, gunakan kode di bawah ini:
            // if (typeof chime !== 'undefined') chime.play();
            
            setTimeout(() => {
                window.speechSynthesis.speak(utterance);
            }, 500); // Jeda sebentar sebelum bicara
        }

        window.speechSynthesis.onvoiceschanged = () => {
            window.speechSynthesis.getVoices();
        };
    </script>
</x-app-layout>