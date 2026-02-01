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

                        <div class="border-t border-dashed border-gray-200 pt-6 text-left space-y-4">
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

                            <div class="bg-blue-50 p-3 rounded-lg border border-blue-100">
                                <div class="flex justify-between mb-2">
                                    <span class="text-xs text-blue-600 font-bold uppercase">Ruang Santri:</span>
                                    <span id="lblRuangSantri" class="text-xs font-bold text-gray-800">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-xs text-purple-600 font-bold uppercase">Ruang Wali:</span>
                                    <span id="lblRuangWali" class="text-xs font-bold text-gray-800">-</span>
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
        let lastData = null;

        // ==========================================================
        // 1. KONEKSI BLUETOOTH PRINTER
        // ==========================================================
        document.getElementById('connectBtn').addEventListener('click', async () => {
            try {
                addLog('🔍 Mencari perangkat Bluetooth...', 'info');
                const device = await navigator.bluetooth.requestDevice({
                    filters: [{ services: ['000018f0-0000-1000-8000-00805f9b34fb'] }]
                });

                addLog(`🔄 Menghubungkan ke ${device.name}...`, 'info');
                const server = await device.gatt.connect();
                const service = await server.getPrimaryService('000018f0-0000-1000-8000-00805f9b34fb');
                printCharacteristic = await service.getCharacteristic('00002af1-0000-1000-8000-00805f9b34fb');

                document.getElementById('printerName').innerHTML = `Terhubung: <span class="text-green-400 font-bold">${device.name}</span>`;
                document.getElementById('connectBtn').classList.add('hidden');
                addLog('✅ Printer Berhasil Terhubung!', 'success');

            } catch (error) {
                console.error(error);
                addLog('❌ Gagal Konek: ' + error, 'error');
                alert('Gagal menghubungkan printer. Pastikan Bluetooth nyala & pilih printer yang benar.');
            }
        });

        // ==========================================================
        // 2. LOGIKA CETAK 2 STRUK + DELAY
        // ==========================================================
        async function printFullSequence(data) {
            if (!printCharacteristic) {
                addLog('⚠️ Data tersimpan, tapi Printer tidak terhubung.', 'warning');
                return;
            }

            try {
                // TAHAP 1: CETAK STRUK ANTRIAN (UNTUK WALI)
                addLog('🖨️ Mencetak Struk 1 (Antrian)...', 'info');
                await printQueueTicket(data);

                // TAHAP 2: JEDA 3 DETIK (UTK SOBEK KERTAS)
                addLog('⏳ Menunggu 3 detik (Silakan sobek kertas)...', 'warning');
                await new Promise(r => setTimeout(r, 3000)); 

                // TAHAP 3: CETAK STRUK LOGIN (UNTUK SANTRI)
                addLog('🖨️ Mencetak Struk 2 (Tiket Santri)...', 'info');
                await printStudentTicket(data);
                
                addLog('✅ Semua struk berhasil dicetak.', 'success');

            } catch (error) {
                addLog('❌ Error saat nge-print: ' + error, 'error');
            }
        }

        // --- FUNGSI CETAK STRUK 1: ANTRIAN UMUM ---
        async function printQueueTicket(data) {
            const encoder = new TextEncoder();
            
            // Perintah ESC/POS Dasar
            const ESC = '\u001B';
            const GS = '\u001D';
            const center = ESC + 'a' + '\u0001';
            const left = ESC + 'a' + '\u0000';
            const boldOn = ESC + 'E' + '\u0001';
            const boldOff = ESC + 'E' + '\u0000';
            const doubleSize = GS + '!' + '\u0011'; 
            const normalSize = GS + '!' + '\u0000';

            let text = '';
            
            // Header
            text += center + boldOn + "BUKTI REGISTRASI\n" + boldOff;
            text += "PSB PONDOK PESANTREN\n";
            text += "--------------------------------\n";
            
            // Detail
            text += left + "Waktu   : " + data.waktu + "\n";
            text += "No Reg  : " + data.no_daftar + "\n";
            text += "Nama    : " + data.nama.substring(0, 20) + "\n"; 
            text += "Jenjang : " + data.jenjang + "\n";
            text += "--------------------------------\n";
            
            // Info Ruangan
            text += boldOn + "R. Santri: " + (data.r_santri || '-') + "\n";
            text += "R. Wali  : " + (data.r_wali || '-') + boldOff + "\n";
            text += "--------------------------------\n";
            
            // Nomor Antrian (Besar)
            text += center + "NOMOR ANTRIAN ANDA\n";
            text += doubleSize + boldOn + data.antrian + "\n" + normalSize + boldOff;
            
            // Footer
            text += "--------------------------------\n";
            text += "Simpan struk ini untuk\n";
            text += "pemanggilan wali santri.\n\n"; // Jarak sebelum QR

            // Kirim Teks Dulu
            await printCharacteristic.writeValue(encoder.encode(text));

            // CETAK QR CODE (No Daftar)
            await printQRCode(data.no_daftar);

            // Feed Akhir (Biar kertas keluar agak panjang buat disobek)
            await printCharacteristic.writeValue(encoder.encode("\n\n\n"));
        }

        // --- FUNGSI CETAK STRUK 2: TIKET SANTRI ---
        async function printStudentTicket(data) {
            const encoder = new TextEncoder();
            const ESC = '\u001B';
            const GS = '\u001D';
            const center = ESC + 'a' + '\u0001';
            const left = ESC + 'a' + '\u0000';
            const boldOn = ESC + 'E' + '\u0001';
            const boldOff = ESC + 'E' + '\u0000';

            // 1. Definisikan Link Halaman Login Ujian/Interview
            // Pastikan Anda sudah punya route bernama 'interview.santri.login'
            // Atau ganti manual string-nya, misal: "https://ppdb.sekolah.com/login-ujian"
            const linkLogin = "{{ route('interview.santri.login') }}"; 

            let text = '';

            // --- HEADER ---
            text += center + boldOn + "TIKET MASUK TES\n" + boldOff;
            text += "--------------------------------\n";
            
            // --- IDENTITAS (Untuk dibaca siswa saat input manual) ---
            text += left;
            text += "Nama   : " + data.nama.substring(0, 20) + "\n";
            text += "Jenjang: " + (data.jenjang || '-') + "\n";
            text += "--------------------------------\n";
            
            // --- INSTRUKSI ---
            text += center + "Scan QR di bawah ini untuk\n";
            text += "membuka Halaman Ujian:\n\n";

            await printCharacteristic.writeValue(encoder.encode(text));

            // --- CETAK QR CODE (ISINYA LINK WEB) ---
            // Printer akan otomatis generate QR yang berisi link tersebut
            await printQRCode(linkLogin);

            // --- USERNAME/PASSWORD INFO ---
            text = "\n";
            text += left + "Lalu masukkan No. Registrasi:\n";
            text += center + boldOn + "\n" + data.no_daftar + "\n\n" + boldOff; // Dicetak besar biar mudah dibaca
            
            text += "SEMOGA SUKSES!\n\n\n\n"; // Feed akhir

            await printCharacteristic.writeValue(encoder.encode(text));
        }

        // --- HELPER: GENERATE NATIVE ESC/POS QR CODE ---
        async function printQRCode(dataString) {
            // Perintah Native ESC/POS untuk QR Code
            // Ini bekerja di sebagian besar printer thermal Bluetooth (VSC, Panda, Eppos, Xprinter)
            
            const storeLen = dataString.length + 3;
            const pL = storeLen % 256;
            const pH = Math.floor(storeLen / 256);

            // 1. Set Model QR (Model 2)
            let cmdModel = new Uint8Array([29, 40, 107, 4, 0, 49, 65, 50, 0]);
            await printCharacteristic.writeValue(cmdModel);

            // 2. Set Ukuran Module (Besar QR: 6-8 recommended)
            let cmdSize = new Uint8Array([29, 40, 107, 3, 0, 49, 67, 8]); 
            await printCharacteristic.writeValue(cmdSize);

            // 3. Set Error Correction (Level M)
            let cmdErr = new Uint8Array([29, 40, 107, 3, 0, 49, 69, 48]);
            await printCharacteristic.writeValue(cmdErr);

            // 4. Store Data
            let cmdStoreHeader = new Uint8Array([29, 40, 107, pL, pH, 49, 80, 48]);
            let dataBytes = new TextEncoder().encode(dataString);
            
            // Gabungkan Header Store + Data String
            let cmdStoreFull = new Uint8Array(cmdStoreHeader.length + dataBytes.length);
            cmdStoreFull.set(cmdStoreHeader);
            cmdStoreFull.set(dataBytes, cmdStoreHeader.length);
            await printCharacteristic.writeValue(cmdStoreFull);

            // 5. Print QR Code
            let cmdPrint = new Uint8Array([29, 40, 107, 3, 0, 49, 81, 48]);
            await printCharacteristic.writeValue(cmdPrint);
        }

        function rePrintLast() {
            if(lastData) {
                printFullSequence(lastData);
            } else {
                alert("Belum ada data yang discan.");
            }
        }

        // ==========================================================
        // 3. LOGIKA SCANNER
        // ==========================================================
        function onScanSuccess(decodedText, decodedResult) {
            if (isProcessing) return;

            isProcessing = true;
            document.getElementById('loadingIndicator').classList.remove('hidden');
            addLog(`📸 QR Terdeteksi: ${decodedText}`, 'info');

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
                document.getElementById('loadingIndicator').classList.add('hidden');

                if (data.status === 'error') {
                    showStatus('❌ DATA TIDAK DITEMUKAN', 'red');
                    addLog(data.message, 'error');
                    playAudio('error');
                    alert(data.message);
                } else {
                    // Update Tampilan (Termasuk Ruangan)
                    document.getElementById('lblAntrian').innerText = data.data.antrian;
                    document.getElementById('lblNama').innerText = data.data.nama;
                    document.getElementById('lblNoDaftar').innerText = data.data.no_daftar;
                    document.getElementById('lblWaktu').innerText = data.data.waktu;
                    
                    document.getElementById('lblRuangSantri').innerText = data.data.r_santri || '-';
                    document.getElementById('lblRuangWali').innerText = data.data.r_wali || '-';
                    
                    lastData = data.data;

                    if (data.status === 'success') {
                        showStatus('✅ BERHASIL CHECK-IN', 'green');
                        addLog(`Santri ${data.data.nama} check-in.`, 'success');
                        playAudio('success');
                        
                        // AUTO PRINT 2 TIKET (SEQUENCE)
                        printFullSequence(data.data);

                    } else if (data.status === 'warning') {
                        showStatus('⚠️ SUDAH CHECK-IN SEBELUMNYA', 'yellow');
                        addLog(`Peringatan: Santri scan ulang.`, 'warning');
                        playAudio('warning');
                    }
                }

                setTimeout(() => { isProcessing = false; }, 3000);
            })
            .catch(err => {
                isProcessing = false;
                document.getElementById('loadingIndicator').classList.add('hidden');
                addLog('Server Error: ' + err, 'error');
                console.error(err);
            });
        }

        // Setup Scanner
        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", { fps: 10, qrbox: {width: 250, height: 250} }, false
        );
        html5QrcodeScanner.render(onScanSuccess, (error) => {});

        // --- UTILS ---
        function addLog(msg, type) {
            const logArea = document.getElementById('logArea');
            let color = 'text-gray-300';
            if(type === 'error') color = 'text-red-400 font-bold';
            if(type === 'success') color = 'text-green-400 font-bold';
            if(type === 'warning') color = 'text-yellow-400';
            if(type === 'info') color = 'text-blue-400';

            const time = new Date().toLocaleTimeString('id-ID', { hour12: false });
            logArea.innerHTML = `<div class="mb-1 ${color}">[${time}] ${msg}</div>` + logArea.innerHTML;
        }

        function showStatus(text, color) {
            const badge = document.getElementById('statusBadge');
            badge.innerText = text;
            badge.className = 'px-3 py-1 rounded-full text-xs font-bold transition-all duration-300';
            
            if(color === 'green') badge.classList.add('bg-green-100', 'text-green-700');
            else if(color === 'red') badge.classList.add('bg-red-100', 'text-red-700');
            else if(color === 'yellow') badge.classList.add('bg-yellow-100', 'text-yellow-700');
            else badge.classList.add('bg-gray-100', 'text-gray-400');
        }

        function playAudio(type) {
            // Opsional: audio.play()
        }
    </script>
</x-app-layout>