<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Mobile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
</head>
<body class="bg-gray-100 min-h-screen pb-20">
    
    <div class="max-w-md mx-auto pt-6 px-4">
        
        {{-- HEADER & TOMBOL AKSI --}}
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-black text-gray-800">Check-in Mobile</h2>
            <button onclick="window.close()" class="text-xs font-bold text-red-600 bg-red-50 px-3 py-1.5 rounded-lg border border-red-200">
                Tutup
            </button>
        </div>

        <div class="flex gap-2 mb-6">
            <button id="connectBtn" class="flex-1 text-xs font-bold text-white bg-blue-600 px-3 py-2.5 rounded-xl shadow-md hover:bg-blue-700 transition">
                🖨️ Connect Printer
            </button>
            <button onclick="rePrintLast()" class="flex-1 text-xs font-bold text-gray-700 bg-white border border-gray-300 px-3 py-2.5 rounded-xl shadow-sm hover:bg-gray-50 transition">
                🔄 Cetak Ulang
            </button>
        </div>

        {{-- AREA SCANNER --}}
        <div class="bg-white p-3 rounded-3xl shadow-sm border border-gray-100 mb-6 relative">
            <div id="loadingIndicator" class="hidden absolute inset-0 bg-white/90 z-20 flex items-center justify-center rounded-3xl backdrop-blur-sm">
                <div class="text-center">
                    <svg class="animate-spin w-10 h-10 text-blue-600 mx-auto mb-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <p class="text-sm font-bold text-gray-600">Memproses...</p>
                </div>
            </div>
            
            <div id="reader" class="rounded-2xl overflow-hidden bg-gray-900 aspect-square"></div>
            <p class="text-center text-[10px] text-gray-400 mt-3 font-bold uppercase tracking-widest">Arahkan Kamera ke QR Code</p>
        </div>

        {{-- PENCARIAN MANUAL --}}
        <div class="bg-white p-3 rounded-2xl shadow-sm mb-6 border border-gray-100">
            <input type="text" id="searchManualInput" placeholder="Cari nama santri manual..." autocomplete="off" class="w-full text-base font-bold p-3 outline-none border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500">
            <div id="searchDropdown" class="hidden mt-2 border-t border-gray-100 pt-2 max-h-48 overflow-y-auto">
                <ul id="searchList" class="space-y-1"></ul>
            </div>
        </div>

        {{-- HASIL STATUS --}}
        <div id="statusResult" class="hidden p-4 rounded-2xl text-center shadow-lg font-black text-lg transition-all duration-300"></div>

    </div>

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
                const device = await navigator.bluetooth.requestDevice({
                    filters: [{ services: ['000018f0-0000-1000-8000-00805f9b34fb'] }]
                });

                const server = await device.gatt.connect();
                const service = await server.getPrimaryService('000018f0-0000-1000-8000-00805f9b34fb');
                printCharacteristic = await service.getCharacteristic('00002af1-0000-1000-8000-00805f9b34fb');

                const btn = document.getElementById('connectBtn');
                btn.classList.replace('bg-blue-600', 'bg-green-600');
                btn.innerHTML = "✅ Printer Terhubung";
                alert('Printer Berhasil Terhubung!');

            } catch (error) {
                console.error(error);
                alert('Gagal menghubungkan printer. Pastikan Bluetooth nyala & pilih printer yang benar.');
            }
        });

        // ==========================================================
        // 2. LOGIKA CETAK 2 STRUK + DELAY (SAMA PERSIS DENGAN DESKTOP)
        // ==========================================================
        async function printFullSequence(data) {
            if (!printCharacteristic) {
                alert('⚠️ Check-in Berhasil, tapi Printer belum terhubung.');
                return;
            }

            try {
                // TAHAP 1: CETAK STRUK ANTRIAN (UNTUK WALI)
                await printQueueTicket(data);

                // TAHAP 2: JEDA 3 DETIK (UTK SOBEK KERTAS)
                await new Promise(r => setTimeout(r, 3000)); 

                // TAHAP 3: CETAK STRUK LOGIN (UNTUK SANTRI)
                await printStudentTicket(data);

            } catch (error) {
                alert('❌ Error saat nge-print: ' + error.message);
            }
        }

        // --- FUNGSI CETAK STRUK 1: ANTRIAN UMUM ---
        async function printQueueTicket(data) {
            const encoder = new TextEncoder();
            
            const ESC = '\u001B';
            const GS = '\u001D';
            const center = ESC + 'a' + '\u0001';
            const left = ESC + 'a' + '\u0000';
            const boldOn = ESC + 'E' + '\u0001';
            const boldOff = ESC + 'E' + '\u0000';
            const doubleSize = GS + '!' + '\u0011'; 
            const normalSize = GS + '!' + '\u0000';

            let text = '';
            
            text += center + boldOn + "BUKTI REGISTRASI\n" + boldOff;
            text += "PSB PONDOK PESANTREN\n";
            text += "--------------------------------\n";
            
            text += left + "Waktu   : " + data.waktu + "\n";
            text += "No Reg  : " + data.no_daftar + "\n";
            text += "Nama    : " + data.nama.substring(0, 20) + "\n"; 
            text += "Jenjang : " + data.jenjang + "\n";
            text += "--------------------------------\n";
            
            text += boldOn + "R. Santri: " + (data.r_santri || '-') + "\n";
            text += "R. Wali  : " + (data.r_wali || '-') + boldOff + "\n";
            text += "--------------------------------\n";
            
            text += center + "NOMOR ANTRIAN ANDA\n";
            text += doubleSize + boldOn + data.antrian + "\n" + normalSize + boldOff;
            
            text += "--------------------------------\n";
            text += "Simpan struk ini untuk\n";
            text += "pemanggilan wali santri.\n\n";

            await printCharacteristic.writeValue(encoder.encode(text));

            const urlWali = `{{ url('/cek-pendaftaran') }}/${data.no_daftar}`;
            await printQRCode(urlWali);

            await printCharacteristic.writeValue(encoder.encode("\n\n\n"));
        }

        // --- FUNGSI CETAK STRUK 2: TIKET SANTRI ---
        async function printStudentTicket(data) {
            const encoder = new TextEncoder();
            const ESC = '\u001B';
            const center = ESC + 'a' + '\u0001';
            const left = ESC + 'a' + '\u0000';
            const boldOn = ESC + 'E' + '\u0001';
            const boldOff = ESC + 'E' + '\u0000';

            const linkLogin = `{{ route('interview.santri.login') }}?no_daftar=${data.no_daftar}`; 

            let text = '';

            text += center + boldOn + "TIKET MASUK TES\n" + boldOff;
            text += "--------------------------------\n";
            
            text += left;
            text += "Nama   : " + data.nama.substring(0, 20) + "\n";
            text += "Jenjang: " + (data.jenjang || '-') + "\n";
            text += "--------------------------------\n";
            
            text += center + "Scan QR di bawah ini untuk\n";
            text += "membuka Halaman Ujian:\n\n";

            await printCharacteristic.writeValue(encoder.encode(text));
            await printQRCode(linkLogin);

            text = "\n";
            text += left + "Lalu masukkan No. Registrasi:\n";
            text += center + boldOn + "\n" + data.no_daftar + "\n\n" + boldOff; 
            text += "SEMOGA SUKSES!\n\n\n\n"; 

            await printCharacteristic.writeValue(encoder.encode(text));
        }

        // --- HELPER: GENERATE NATIVE ESC/POS QR CODE ---
        async function printQRCode(dataString) {
            const storeLen = dataString.length + 3;
            const pL = storeLen % 256;
            const pH = Math.floor(storeLen / 256);

            let cmdModel = new Uint8Array([29, 40, 107, 4, 0, 49, 65, 50, 0]);
            await printCharacteristic.writeValue(cmdModel);

            let cmdSize = new Uint8Array([29, 40, 107, 3, 0, 49, 67, 8]); 
            await printCharacteristic.writeValue(cmdSize);

            let cmdErr = new Uint8Array([29, 40, 107, 3, 0, 49, 69, 48]);
            await printCharacteristic.writeValue(cmdErr);

            let cmdStoreHeader = new Uint8Array([29, 40, 107, pL, pH, 49, 80, 48]);
            let dataBytes = new TextEncoder().encode(dataString);
            
            let cmdStoreFull = new Uint8Array(cmdStoreHeader.length + dataBytes.length);
            cmdStoreFull.set(cmdStoreHeader);
            cmdStoreFull.set(dataBytes, cmdStoreHeader.length);
            await printCharacteristic.writeValue(cmdStoreFull);

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
        // 3. LOGIKA SCANNER & PROSES
        // ==========================================================
        function onScanSuccess(decodedText, decodedResult) {
            if (isProcessing) return;
            isProcessing = true;

            let finalCode = decodedText;
            if (decodedText.includes('no_daftar=')) {
                finalCode = new URL(decodedText).searchParams.get('no_daftar');
            } else if (decodedText.includes('/cek-pendaftaran/')) {
                finalCode = decodedText.split('/').pop();
            }

            processAttendance(finalCode);
        }

        function processAttendance(code) {
            document.getElementById('loadingIndicator').classList.remove('hidden');
            const statusDiv = document.getElementById('statusResult');
            statusDiv.classList.add('hidden');

            fetch("{{ route('admin.attendance.process') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ code: code })
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('loadingIndicator').classList.add('hidden');

                if (data.status === 'error') {
                    statusDiv.innerHTML = "❌ " + data.message;
                    statusDiv.className = "p-4 rounded-2xl text-center shadow-lg font-black text-lg bg-red-100 text-red-700";
                    alert(data.message);
                } else {
                    lastData = data.data;

                    if (data.status === 'success') {
                        statusDiv.innerHTML = "✅ BERHASIL CHECK-IN<br><span class='text-sm font-medium'>" + data.data.nama + "</span>";
                        statusDiv.className = "p-4 rounded-2xl text-center shadow-lg font-black text-lg bg-green-100 text-green-700";
                        
                        // AUTO PRINT
                        printFullSequence(data.data);

                    } else if (data.status === 'warning') {
                        statusDiv.innerHTML = "⚠️ SUDAH CHECK-IN<br><span class='text-sm font-medium'>" + data.data.nama + "</span>";
                        statusDiv.className = "p-4 rounded-2xl text-center shadow-lg font-black text-lg bg-yellow-100 text-yellow-700";
                    }
                }

                statusDiv.classList.remove('hidden');
                setTimeout(() => { statusDiv.classList.add('hidden'); isProcessing = false; }, 3000);
            })
            .catch(err => {
                isProcessing = false;
                document.getElementById('loadingIndicator').classList.add('hidden');
                alert('Server Error: ' + err);
            });
        }

        // Setup Scanner
        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", { fps: 10, qrbox: {width: 250, height: 250} }, false
        );
        html5QrcodeScanner.render(onScanSuccess, (error) => {});

        // ==========================================================
        // 4. PENCARIAN MANUAL
        // ==========================================================
        const searchInput = document.getElementById('searchManualInput');
        const searchDropdown = document.getElementById('searchDropdown');
        const searchList = document.getElementById('searchList');
        let searchTimeout = null;

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const keyword = this.value.trim();

            if (keyword.length < 2) {
                searchDropdown.classList.add('hidden');
                return;
            }

            searchTimeout = setTimeout(() => {
                fetch(`{{ route('admin.interview.attendance.search') }}?q=${keyword}`)
                    .then(res => res.json())
                    .then(data => {
                        searchList.innerHTML = '';
                        if (data.length === 0) {
                            searchList.innerHTML = `<li class="p-4 text-center text-sm text-gray-500">❌ Tidak ada santri yang cocok.</li>`;
                        } else {
                            data.forEach(item => {
                                const status = item.waktu_hadir 
                                    ? `<span class="text-[10px] bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-bold">Hadir</span>` 
                                    : `<span class="text-[10px] bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full font-bold">Belum</span>`;

                                const li = document.createElement('li');
                                li.className = "p-3 bg-white hover:bg-blue-50 cursor-pointer transition flex justify-between items-center border-b";
                                li.innerHTML = `
                                    <div>
                                        <div class="font-bold text-gray-800 text-sm">${item.nama_lengkap}</div>
                                        <div class="text-xs text-gray-500 font-mono mt-0.5">${item.no_daftar}</div>
                                    </div>
                                    <div class="flex flex-col items-end gap-1">
                                        ${status}
                                    </div>
                                `;
                                li.addEventListener('click', () => {
                                    if(isProcessing) return;
                                    searchInput.value = ''; 
                                    searchDropdown.classList.add('hidden'); 
                                    processAttendance(item.no_daftar); 
                                });
                                searchList.appendChild(li);
                            });
                        }
                        searchDropdown.classList.remove('hidden');
                    })
                    .catch(err => console.error(err));
            }, 400);
        });

        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
                searchDropdown.classList.add('hidden');
            }
        });
    </script>
</body>
</html>