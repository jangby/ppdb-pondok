<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight flex items-center gap-2">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
            {{ __('Meja Registrasi & Cetak Antrian') }}
        </h2>
    </x-slot>

    <div class="py-12 px-4 max-w-7xl mx-auto">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2 space-y-6">
                
                <div class="bg-slate-900 text-white p-6 rounded-2xl shadow-lg relative overflow-hidden">
                    <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-4">
                        <div>
                            <h3 class="font-bold text-lg flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                Printer Thermal
                            </h3>
                            <p class="text-slate-400 text-sm mt-1" id="printerName">Status: Belum Terhubung</p>
                        </div>
                        <button id="connectBtn" class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-3 rounded-xl font-bold transition shadow-lg flex items-center gap-2">
                            Hubungkan Bluetooth
                        </button>
                    </div>
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-blue-600 rounded-full blur-3xl opacity-20"></div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                    <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Kamera Pemindai QR</h3>
                    <div class="bg-gray-100 rounded-xl overflow-hidden border-2 border-dashed border-gray-300 relative min-h-[300px]">
                        <div id="reader" width="100%"></div>
                    </div>
                    <p class="text-center text-xs text-gray-400 mt-3">Pastikan QR Code terlihat jelas dan pencahayaan cukup.</p>
                </div>
            </div>

            <div class="space-y-6">
                
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                    <div class="bg-blue-600 p-4 text-center">
                        <h4 class="text-white text-xs font-bold uppercase tracking-widest opacity-80">Nomor Antrian Anda</h4>
                    </div>
                    
                    <div class="p-8 text-center relative">
                        <div id="loadingIndicator" class="hidden absolute inset-0 bg-white/90 z-20 flex items-center justify-center backdrop-blur-sm">
                            <div class="text-center">
                                <svg class="animate-spin w-10 h-10 text-blue-600 mx-auto mb-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <p class="text-sm font-bold text-gray-600">Memproses...</p>
                            </div>
                        </div>

                        <h1 id="lblAntrian" class="text-8xl font-black text-gray-800 mb-2 leading-none">--</h1>
                        
                        <div id="statusContainer" class="h-8 mb-6">
                            <span id="statusBadge" class="px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-400">
                                Menunggu Scan...
                            </span>
                        </div>

                        <div class="border-t border-dashed border-gray-200 pt-6 text-left space-y-3">
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase font-bold">Nama Santri</p>
                                <p id="lblNama" class="font-bold text-gray-800 text-lg truncate">-</p>
                            </div>
                            <div class="flex justify-between">
                                <div>
                                    <p class="text-[10px] text-gray-400 uppercase font-bold">No. Daftar</p>
                                    <p id="lblNoDaftar" class="font-mono text-gray-600 font-medium">-</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] text-gray-400 uppercase font-bold">Waktu Hadir</p>
                                    <p id="lblWaktu" class="text-gray-600 font-medium">-</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 border-t border-gray-100 text-center">
                        <button onclick="rePrintLast()" class="text-xs text-blue-600 hover:text-blue-800 font-bold underline">
                            Cetak Ulang Terakhir
                        </button>
                    </div>
                </div>

                <div class="bg-gray-800 text-gray-300 p-4 rounded-xl text-xs font-mono h-48 overflow-y-auto border border-gray-700 shadow-inner" id="logArea">
                    <div class="text-gray-500 italic pb-2 border-b border-gray-700 mb-2">System Log Ready...</div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <script>
        // --- VARIABLE GLOBAL ---
        let printCharacteristic = null;
        let isProcessing = false;
        let lastData = null; // Menyimpan data terakhir untuk fitur cetak ulang

        // ==========================================================
        // 1. LOGIKA KONEKSI BLUETOOTH PRINTER (Web Bluetooth API)
        // ==========================================================
        document.getElementById('connectBtn').addEventListener('click', async () => {
            try {
                addLog('🔍 Mencari perangkat Bluetooth...', 'info');
                
                // Request Device
                const device = await navigator.bluetooth.requestDevice({
                    filters: [{ services: ['000018f0-0000-1000-8000-00805f9b34fb'] }] // UUID Umum Thermal Printer
                });

                addLog(`🔄 Menghubungkan ke ${device.name}...`, 'info');
                
                const server = await device.gatt.connect();
                const service = await server.getPrimaryService('000018f0-0000-1000-8000-00805f9b34fb');
                
                // Characteristic untuk Write Data
                printCharacteristic = await service.getCharacteristic('00002af1-0000-1000-8000-00805f9b34fb');

                // Update UI Jika Berhasil
                document.getElementById('printerName').innerHTML = `Terhubung: <span class="text-green-400 font-bold">${device.name}</span>`;
                document.getElementById('connectBtn').classList.add('hidden'); // Sembunyikan tombol
                addLog('✅ Printer Berhasil Terhubung!', 'success');
                
                // Test Print Suara (Opsional)
                // printText("Printer Ready!\n\n");

            } catch (error) {
                console.error(error);
                addLog('❌ Gagal Konek: ' + error, 'error');
                alert('Gagal menghubungkan printer. Pastikan Bluetooth nyala & pilih printer yang benar.');
            }
        });

        // ==========================================================
        // 2. FUNGSI CETAK STRUK (ESC/POS COMMANDS)
        // ==========================================================
        async function printTicket(data) {
            if (!printCharacteristic) {
                addLog('⚠️ Data tersimpan, tapi Printer tidak terhubung.', 'warning');
                return;
            }

            try {
                addLog('🖨️ Mencetak struk...', 'info');

                // Konstanta ESC/POS
                const ESC = '\u001B';
                const GS = '\u001D';
                const center = ESC + 'a' + '\u0001';
                const left = ESC + 'a' + '\u0000';
                const boldOn = ESC + 'E' + '\u0001';
                const boldOff = ESC + 'E' + '\u0000';
                const doubleSize = GS + '!' + '\u0011'; // 2x Height & Width
                const normalSize = GS + '!' + '\u0000';

                let text = '';
                
                // Header
                text += center + boldOn + "BUKTI REGISTRASI ULANG\n" + boldOff;
                text += "PPDB PONDOK PESANTREN\n";
                text += "--------------------------------\n";
                
                // Detail
                text += left + "Waktu   : " + data.waktu + "\n";
                text += "No Reg  : " + data.no_daftar + "\n";
                text += "Nama    : " + data.nama.substring(0, 20) + "\n"; // Potong nama panjang
                text += "Jenjang : " + data.jenjang + "\n";
                text += "--------------------------------\n";
                
                // Nomor Antrian (Besar)
                text += center + "NOMOR ANTRIAN ANDA\n";
                text += doubleSize + boldOn + data.antrian + "\n" + normalSize + boldOff;
                
                // Footer
                text += "--------------------------------\n";
                text += "Mohon menunggu panggilan\n";
                text += "dari panitia.\n\n\n\n"; // Feed lines agar kertas keluar

                // Kirim ke Printer
                const encoder = new TextEncoder();
                await printCharacteristic.writeValue(encoder.encode(text));
                
                addLog('✅ Struk berhasil dicetak.', 'success');

            } catch (error) {
                addLog('❌ Error saat nge-print: ' + error, 'error');
            }
        }

        // Fitur Cetak Ulang Manual
        function rePrintLast() {
            if(lastData) {
                printTicket(lastData);
            } else {
                alert("Belum ada data yang discan.");
            }
        }

        // ==========================================================
        // 3. LOGIKA SCANNER & FETCH API
        // ==========================================================
        function onScanSuccess(decodedText, decodedResult) {
            if (isProcessing) return; // Mencegah double scan cepat

            isProcessing = true;
            document.getElementById('loadingIndicator').classList.remove('hidden');
            addLog(`📸 QR Terdeteksi: ${decodedText}`, 'info');

            // Kirim Data ke Controller Laravel
            fetch("{{ route('admin.attendance.process') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ code: decodedText })
            })
            .then(response => response.json())
            .then(data => {
                // Sembunyikan Loading
                document.getElementById('loadingIndicator').classList.add('hidden');

                if (data.status === 'error') {
                    // Jika Santri Tidak Ditemukan
                    showStatus('❌ DATA TIDAK DITEMUKAN', 'red');
                    addLog(data.message, 'error');
                    playAudio('error');
                    alert(data.message);
                } else {
                    // Update Tampilan Kartu
                    document.getElementById('lblAntrian').innerText = data.data.antrian;
                    document.getElementById('lblNama').innerText = data.data.nama;
                    document.getElementById('lblNoDaftar').innerText = data.data.no_daftar;
                    document.getElementById('lblWaktu').innerText = data.data.waktu;
                    
                    // Simpan data untuk tombol re-print
                    lastData = data.data;

                    if (data.status === 'success') {
                        // SUKSES BARU
                        showStatus('✅ BERHASIL CHECK-IN', 'green');
                        addLog(`Santri ${data.data.nama} check-in. Antrian: ${data.data.antrian}`, 'success');
                        playAudio('success');
                        
                        // Auto Print
                        printTicket(data.data);

                    } else if (data.status === 'warning') {
                        // SUDAH PERNAH SCAN
                        showStatus('⚠️ SUDAH CHECK-IN SEBELUMNYA', 'yellow');
                        addLog(`Peringatan: Santri ${data.data.nama} scan ulang.`, 'warning');
                        playAudio('warning');
                        // Tidak auto print, tapi bisa print ulang manual
                    }
                }

                // Jeda 3 detik sebelum bisa scan lagi
                setTimeout(() => { isProcessing = false; }, 3000);
            })
            .catch(err => {
                isProcessing = false;
                document.getElementById('loadingIndicator').classList.add('hidden');
                addLog('Server Error: ' + err, 'error');
                console.error(err);
            });
        }

        function onScanFailure(error) {
            // handle scan failure, usually better to ignore and keep scanning.
            // console.warn(`Code scan error = ${error}`);
        }

        // Inisialisasi Scanner
        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader",
            { fps: 10, qrbox: {width: 250, height: 250} },
            /* verbose= */ false
        );
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);


        // ==========================================================
        // 4. UTILITY FUNCTIONS (Log, UI, Audio)
        // ==========================================================
        function addLog(msg, type) {
            const logArea = document.getElementById('logArea');
            let color = 'text-gray-300';
            if(type === 'error') color = 'text-red-400 font-bold';
            if(type === 'success') color = 'text-green-400 font-bold';
            if(type === 'warning') color = 'text-yellow-400';

            const time = new Date().toLocaleTimeString('id-ID', { hour12: false });
            logArea.innerHTML = `<div class="mb-1 ${color}">[${time}] ${msg}</div>` + logArea.innerHTML;
        }

        function showStatus(text, color) {
            const badge = document.getElementById('statusBadge');
            badge.innerText = text;
            
            // Reset classes
            badge.className = 'px-3 py-1 rounded-full text-xs font-bold transition-all duration-300';
            
            if(color === 'green') badge.classList.add('bg-green-100', 'text-green-700');
            else if(color === 'red') badge.classList.add('bg-red-100', 'text-red-700');
            else if(color === 'yellow') badge.classList.add('bg-yellow-100', 'text-yellow-700');
            else badge.classList.add('bg-gray-100', 'text-gray-400');
        }

        function playAudio(type) {
            // Opsional: Bisa tambahkan file audio beep.mp3 di public folder
            // const audio = new Audio('/sounds/' + type + '.mp3');
            // audio.play().catch(e => console.log('Audio play blocked'));
        }
    </script>
</x-app-layout>