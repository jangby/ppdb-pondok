<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight flex items-center gap-2">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
            {{ __('Meja Registrasi & Cetak Antrian') }}
        </h2>
        <a href="{{ route('admin.interview.attendance.mobile') }}" target="_blank" 
       class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl font-bold flex items-center gap-2 transition shadow-lg">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
        Mode Mobile
    </a>
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

                {{-- KOTAK PENCARIAN MANUAL --}}
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 mb-6 relative z-40">
                    <h3 class="font-bold text-gray-800 mb-4 border-b pb-2 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        Pencarian Manual
                    </h3>
                    <div class="relative">
                        <input type="text" id="searchManualInput" placeholder="Ketik nama santri atau no. daftar..." autocomplete="off" class="w-full pl-4 pr-10 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm font-medium">
                        <div class="absolute right-3 top-3 text-gray-400">
                            <div class="w-5 h-5 animate-spin border-2 border-blue-500 border-t-transparent rounded-full hidden" id="searchSpinner"></div>
                        </div>
                    </div>
                    
                    <div id="searchDropdown" class="absolute w-full left-0 mt-2 bg-white border border-gray-200 rounded-xl shadow-2xl hidden max-h-60 overflow-y-auto z-50">
                        <ul id="searchList" class="divide-y divide-gray-100"></ul>
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

        // TAMBAHKAN FUNGSI INI
        function addLog(message, type = 'info') {
            const logArea = document.getElementById('logArea');
            if (!logArea) return;

            // Ambil waktu saat ini
            const time = new Date().toLocaleTimeString('id-ID', { hour12: false });
            
            // Tentukan warna berdasarkan tipe log
            let color = 'text-gray-300';
            if (type === 'success') color = 'text-green-400 font-bold';
            else if (type === 'warning') color = 'text-yellow-400';
            else if (type === 'error') color = 'text-red-400 font-bold';

            // Buat elemen teks baru
            const logItem = document.createElement('div');
            logItem.className = `mb-1 ${color}`;
            logItem.innerHTML = `<span class="text-gray-500">[${time}]</span> ${message}`;
            
            // Masukkan ke kotak log
            logArea.appendChild(logItem);
            
            // Scroll otomatis ke bawah agar log terbaru selalu terlihat
            logArea.scrollTop = logArea.scrollHeight;
        }

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

            // CETAK QR CODE (LINK KE HALAMAN CEK PENDAFTARAN WALI)
            const urlWali = `{{ url('/cek-pendaftaran') }}/${data.no_daftar}`;
            await printQRCode(urlWali);

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
            const linkLogin = `{{ route('interview.santri.login') }}?no_daftar=${data.no_daftar}`;

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
            
            // --- LOGIKA PEMOTONG URL SUPER AMAN ---
            let finalCode = decodedText.trim();
            
            if (finalCode.includes('http')) {
                try {
                    let urlObj = new URL(finalCode);
                    if (urlObj.searchParams.has('no_daftar')) {
                        finalCode = urlObj.searchParams.get('no_daftar'); // Dari URL Login Santri
                    } else {
                        // Memecah URL berdasarkan '/' dan mengambil teks yang paling belakang (REG-xxxx)
                        let pathSegments = urlObj.pathname.split('/').filter(Boolean);
                        finalCode = pathSegments.pop(); 
                    }
                } catch (e) {
                    console.log("Bukan URL yang valid");
                }
            } else if (finalCode.includes('cek-pendaftaran')) {
                // Fallback jika tidak pakai HTTP
                finalCode = finalCode.split('/').pop().trim();
            }
            // ----------------------------------------

            document.getElementById('loadingIndicator').classList.remove('hidden');
            
            // Update the log call to use the addLog function if it exists (desktop), otherwise just console.log (mobile)
            if (typeof addLog === "function") {
                 addLog(`📸 QR Terdeteksi: ${finalCode}`, 'info');
            }

            fetch("{{ route('admin.attendance.process') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ code: finalCode }) // Gunakan finalCode yang sudah dibersihkan
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('loadingIndicator').classList.add('hidden');

                if (data.status === 'error') {
                    if (typeof showStatus === "function") showStatus('❌ DATA TIDAK DITEMUKAN', 'red');
                    if (typeof addLog === "function") addLog(data.message, 'error');
                    if (typeof playAudio === "function") playAudio('error');
                    alert(data.message);
                } else {
                    // Update Tampilan (Termasuk Ruangan) - These IDs must exist in your HTML
                    const elAntrian = document.getElementById('lblAntrian');
                    const elNama = document.getElementById('lblNama');
                    const elNoDaftar = document.getElementById('lblNoDaftar');
                    const elWaktu = document.getElementById('lblWaktu');
                    const elRuangSantri = document.getElementById('lblRuangSantri');
                    const elRuangWali = document.getElementById('lblRuangWali');

                    if(elAntrian) elAntrian.innerText = data.data.antrian;
                    if(elNama) elNama.innerText = data.data.nama;
                    if(elNoDaftar) elNoDaftar.innerText = data.data.no_daftar;
                    if(elWaktu) elWaktu.innerText = data.data.waktu;
                    if(elRuangSantri) elRuangSantri.innerText = data.data.r_santri || '-';
                    if(elRuangWali) elRuangWali.innerText = data.data.r_wali || '-';
                    
                    lastData = data.data;

                    if (data.status === 'success') {
                        if (typeof showStatus === "function") showStatus('✅ BERHASIL CHECK-IN', 'green');
                        if (typeof addLog === "function") addLog(`Santri ${data.data.nama} check-in.`, 'success');
                        if (typeof playAudio === "function") playAudio('success');
                        
                        // AUTO PRINT 2 TIKET (SEQUENCE)
                        if (typeof printFullSequence === "function") printFullSequence(data.data);

                    } else if (data.status === 'warning') {
                        if (typeof showStatus === "function") showStatus('⚠️ SUDAH CHECK-IN SEBELUMNYA', 'yellow');
                        if (typeof addLog === "function") addLog(`Peringatan: Santri scan ulang.`, 'warning');
                        if (typeof playAudio === "function") playAudio('warning');
                    }
                }

                setTimeout(() => { isProcessing = false; }, 3000);
            })
            .catch(err => {
                isProcessing = false;
                document.getElementById('loadingIndicator').classList.add('hidden');
                if (typeof addLog === "function") addLog('Server Error: ' + err, 'error');
                console.error(err);
            });
        }

        // ==========================================================
        // 4. LOGIKA PENCARIAN MANUAL (LIVE SEARCH)
        // ==========================================================
        const searchInput = document.getElementById('searchManualInput');
        const searchDropdown = document.getElementById('searchDropdown');
        const searchList = document.getElementById('searchList');
        const searchSpinner = document.getElementById('searchSpinner');
        let searchTimeout = null;

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const keyword = this.value.trim();

            if (keyword.length < 2) {
                searchDropdown.classList.add('hidden');
                searchSpinner.classList.add('hidden');
                return;
            }

            searchSpinner.classList.remove('hidden');

            searchTimeout = setTimeout(() => {
                fetch(`{{ route('admin.interview.attendance.search') }}?q=${keyword}`)
                    .then(res => res.json())
                    .then(data => {
                        searchSpinner.classList.add('hidden');
                        searchList.innerHTML = '';
                        
                        if (data.length === 0) {
                            searchList.innerHTML = `<li class="p-4 text-center text-sm text-gray-500">❌ Tidak ada santri yang cocok.</li>`;
                        } else {
                            data.forEach(item => {
                                // Tanda visual jika santri sudah check-in
                                const status = item.waktu_hadir 
                                    ? `<span class="text-[10px] bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-bold">Telah Hadir</span>` 
                                    : `<span class="text-[10px] bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full font-bold">Belum Hadir</span>`;

                                const li = document.createElement('li');
                                li.className = "p-3 hover:bg-blue-50 cursor-pointer transition flex justify-between items-center group";
                                li.innerHTML = `
                                    <div>
                                        <div class="font-bold text-gray-800 text-sm group-hover:text-blue-700">${item.nama_lengkap}</div>
                                        <div class="text-xs text-gray-500 font-mono mt-0.5">${item.no_daftar} • ${item.jenjang}</div>
                                    </div>
                                    <div class="flex flex-col items-end gap-1">
                                        ${status}
                                        <span class="text-[9px] text-blue-500 font-bold opacity-0 group-hover:opacity-100 transition">PILIH & CHECK-IN</span>
                                    </div>
                                `;
                                
                                // JIKA NAMA DIKLIK -> SIMULASIKAN SEPERTI SCAN BARCODE!
                                li.addEventListener('click', () => {
                                    searchInput.value = ''; // Kosongkan input
                                    searchDropdown.classList.add('hidden'); // Tutup dropdown hasil
                                    onScanSuccess(item.no_daftar, null); // Kirim No Daftar ke fungsi scanner
                                });

                                searchList.appendChild(li);
                            });
                        }
                        searchDropdown.classList.remove('hidden');
                    })
                    .catch(err => {
                        searchSpinner.classList.add('hidden');
                        console.error('Search error:', err);
                    });
            }, 400); // Tunggu 400ms setelah mengetik untuk mengurangi beban server
        });

        // Sembunyikan dropdown jika panitia mengklik di luar area pencarian
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
                searchDropdown.classList.add('hidden');
            }
        });
    </script>
</x-app-layout>