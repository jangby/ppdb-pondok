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
            <button id="btnConnect" onclick="connectPrinter()" class="flex-1 text-xs font-bold text-white bg-blue-600 px-3 py-2.5 rounded-xl shadow-md hover:bg-blue-700 transition">
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
            <input type="text" id="mobileSearch" placeholder="Cari nama santri manual..." autocomplete="off" class="w-full text-base font-bold p-3 outline-none border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500">
            <div id="searchDropdown" class="hidden mt-2 border-t border-gray-100 pt-2 max-h-48 overflow-y-auto">
                <ul id="searchList" class="space-y-1"></ul>
            </div>
        </div>

        {{-- HASIL STATUS --}}
        <div id="statusResult" class="hidden p-4 rounded-2xl text-center shadow-lg font-black text-lg transition-all duration-300"></div>

    </div>

    <script>
        let isProcessing = false;
        let printerCharacteristic = null;
        let lastData = null; // Menyimpan data terakhir untuk dicetak ulang

        // ==========================================================
        // 1. KONEKSI PRINTER
        // ==========================================================
        async function connectPrinter() {
            try {
                const device = await navigator.bluetooth.requestDevice({
                    filters: [{ services: ['000018f0-0000-1000-8000-00805f9b34fb'] }]
                });
                const server = await device.gatt.connect();
                const service = await server.getPrimaryService('000018f0-0000-1000-8000-00805f9b34fb');
                printerCharacteristic = await service.getCharacteristic('00002af1-0000-1000-8000-00805f9b34fb');
                
                const btn = document.getElementById('btnConnect');
                btn.classList.replace('bg-blue-600', 'bg-green-600');
                btn.classList.replace('hover:bg-blue-700', 'hover:bg-green-700');
                btn.innerHTML = "✅ Printer Konek";
                alert("Printer Berhasil Terhubung!");
            } catch (error) {
                alert("Gagal koneksi printer: " + error.message);
            }
        }

        // ==========================================================
        // 2. LOGIKA CETAK 2 STRUK + DELAY
        // ==========================================================
        async function printFullSequence(data) {
            if (!printerCharacteristic) {
                alert("⚠️ Data tersimpan, tapi Printer tidak terhubung. Hubungkan printer lalu klik Cetak Ulang.");
                return;
            }

            try {
                // TAHAP 1: STRUK ANTRIAN
                await printQueueTicket(data);

                // TAHAP 2: JEDA 3 DETIK (UTK SOBEK)
                await new Promise(r => setTimeout(r, 3000)); 

                // TAHAP 3: TIKET SANTRI
                await printStudentTicket(data);

            } catch (error) {
                alert("Error saat mencetak: " + error.message);
            }
        }

        // --- CETAK 1: ANTRIAN UMUM (WALI) ---
        async function printQueueTicket(data) {
            const encoder = new TextEncoder();
            const ESC = '\u001B', GS = '\u001D';
            const center = ESC + 'a' + '\u0001', left = ESC + 'a' + '\u0000';
            const boldOn = ESC + 'E' + '\u0001', boldOff = ESC + 'E' + '\u0000';
            const doubleSize = GS + '!' + '\u0011', normalSize = GS + '!' + '\u0000';

            let text = '';
            text += center + boldOn + "BUKTI REGISTRASI\n" + boldOff;
            text += "PSB PONDOK PESANTREN\n";
            text += "--------------------------------\n";
            
            text += left + "Waktu   : " + (data.waktu || '-') + "\n";
            text += "No Reg  : " + (data.no_daftar || '-') + "\n";
            text += "Nama    : " + (data.nama ? data.nama.substring(0, 20) : '-') + "\n"; 
            text += "Jenjang : " + (data.jenjang || '-') + "\n";
            text += "--------------------------------\n";
            
            text += boldOn + "R. Santri: " + (data.r_santri || '-') + "\n";
            text += "R. Wali  : " + (data.r_wali || '-') + boldOff + "\n";
            text += "--------------------------------\n";
            
            text += center + "NOMOR ANTRIAN ANDA\n";
            text += doubleSize + boldOn + (data.antrian || '-') + "\n" + normalSize + boldOff;
            text += "--------------------------------\n";
            text += "Simpan struk ini untuk\n";
            text += "pemanggilan wali santri.\n\n";

            await printerCharacteristic.writeValue(encoder.encode(text));

            // CETAK QR CODE KE HALAMAN CEK PENDAFTARAN WALI
            const urlWali = `{{ url('/cek-pendaftaran') }}/${data.no_daftar}`;
            await printQRCode(urlWali);

            await printerCharacteristic.writeValue(encoder.encode("\n\n\n"));
        }

        // --- CETAK 2: TIKET SANTRI (AUTO LOGIN) ---
        async function printStudentTicket(data) {
            const encoder = new TextEncoder();
            const ESC = '\u001B', center = ESC + 'a' + '\u0001', left = ESC + 'a' + '\u0000';
            const boldOn = ESC + 'E' + '\u0001', boldOff = ESC + 'E' + '\u0000';

            // Link Halaman Login Ujian Bawaan Auto-Login
            const linkLogin = `{{ route('interview.santri.login') }}?no_daftar=${data.no_daftar}`; 

            let text = '';
            text += center + boldOn + "TIKET MASUK TES\n" + boldOff;
            text += "--------------------------------\n";
            text += left + "Nama   : " + (data.nama ? data.nama.substring(0, 20) : '-') + "\n";
            text += "Jenjang: " + (data.jenjang || '-') + "\n";
            text += "--------------------------------\n";
            text += center + "Scan QR di bawah ini untuk\n";
            text += "membuka Halaman Ujian:\n\n";

            await printerCharacteristic.writeValue(encoder.encode(text));

            // CETAK QR CODE AUTO LOGIN
            await printQRCode(linkLogin);

            text = "\n" + left + "Lalu masukkan No. Registrasi:\n";
            text += center + boldOn + "\n" + data.no_daftar + "\n\n" + boldOff; 
            text += "SEMOGA SUKSES!\n\n\n\n"; 

            await printerCharacteristic.writeValue(encoder.encode(text));
        }

        // --- GENERATE NATIVE ESC/POS QR CODE ---
        async function printQRCode(dataString) {
            const storeLen = dataString.length + 3;
            const pL = storeLen % 256;
            const pH = Math.floor(storeLen / 256);

            let cmdModel = new Uint8Array([29, 40, 107, 4, 0, 49, 65, 50, 0]);
            let cmdSize = new Uint8Array([29, 40, 107, 3, 0, 49, 67, 8]); 
            let cmdErr = new Uint8Array([29, 40, 107, 3, 0, 49, 69, 48]);
            let cmdStoreHeader = new Uint8Array([29, 40, 107, pL, pH, 49, 80, 48]);
            let dataBytes = new TextEncoder().encode(dataString);
            
            let cmdStoreFull = new Uint8Array(cmdStoreHeader.length + dataBytes.length);
            cmdStoreFull.set(cmdStoreHeader);
            cmdStoreFull.set(dataBytes, cmdStoreHeader.length);
            let cmdPrint = new Uint8Array([29, 40, 107, 3, 0, 49, 81, 48]);

            await printerCharacteristic.writeValue(cmdModel);
            await printerCharacteristic.writeValue(cmdSize);
            await printerCharacteristic.writeValue(cmdErr);
            await printerCharacteristic.writeValue(cmdStoreFull);
            await printerCharacteristic.writeValue(cmdPrint);
        }

        function rePrintLast() {
            if(lastData) {
                printFullSequence(lastData);
            } else {
                alert("Belum ada data check-in hari ini.");
            }
        }

        // ==========================================================
        // 3. PROSES CHECK-IN
        // ==========================================================
        function processAttendance(code) {
            if (isProcessing) return;
            isProcessing = true;
            
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
            .then(res => res.json())
            .then(data => {
                document.getElementById('loadingIndicator').classList.add('hidden');
                
                // Set Pesan
                statusDiv.innerHTML = data.message;
                statusDiv.classList.remove('hidden', 'bg-green-100', 'text-green-700', 'bg-yellow-100', 'text-yellow-700', 'bg-red-100', 'text-red-700');
                
                if(data.status === 'success') {
                    statusDiv.classList.add('bg-green-100', 'text-green-700');
                    lastData = data.data; // Simpan untuk fungsi Reprint
                    printFullSequence(data.data); // CETAK KEDUA STRUK
                } else if(data.status === 'warning') {
                    statusDiv.classList.add('bg-yellow-100', 'text-yellow-700');
                    lastData = data.data; // Simpan untuk fungsi Reprint walau warning
                } else {
                    statusDiv.classList.add('bg-red-100', 'text-red-700');
                }

                setTimeout(() => { statusDiv.classList.add('hidden'); isProcessing = false; }, 4000);
            })
            .catch((err) => {
                document.getElementById('loadingIndicator').classList.add('hidden');
                statusDiv.innerHTML = "Gagal memproses (Server Error)";
                statusDiv.classList.remove('hidden', 'bg-green-100', 'text-green-700');
                statusDiv.classList.add('bg-red-100', 'text-red-700');
                isProcessing = false;
            });
        }

        // ==========================================================
        // 4. SCANNER KAMERA
        // ==========================================================
        function onScanSuccess(decodedText) {
            // Pembersihan URL dari QR Code Pintar
            let finalCode = decodedText;
            if (decodedText.includes('no_daftar=')) {
                finalCode = new URL(decodedText).searchParams.get('no_daftar');
            } else if (decodedText.includes('/cek-pendaftaran/')) {
                finalCode = decodedText.split('/').pop();
            }
            processAttendance(finalCode);
        }
        
        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", { fps: 10, qrbox: {width: 250, height: 250} }, false
        );
        html5QrcodeScanner.render(onScanSuccess, (error) => {});

        // ==========================================================
        // 5. PENCARIAN MANUAL
        // ==========================================================
        const searchInput = document.getElementById('mobileSearch');
        searchInput.addEventListener('input', function() {
            const keyword = this.value;
            if (keyword.length < 2) { document.getElementById('searchDropdown').classList.add('hidden'); return; }
            
            fetch(`{{ route('admin.interview.attendance.search') }}?q=${keyword}`)
                .then(res => res.json())
                .then(data => {
                    const list = document.getElementById('searchList');
                    list.innerHTML = '';
                    data.forEach(item => {
                        const li = document.createElement('li');
                        li.className = "p-3 bg-gray-50 rounded-xl font-bold text-sm active:bg-blue-100 border-b border-gray-100 cursor-pointer flex justify-between items-center";
                        li.innerHTML = `
                            <div>${item.nama_lengkap} <span class="text-gray-400 text-xs block">${item.no_daftar}</span></div>
                            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs">PILIH</span>
                        `;
                        li.onclick = () => { 
                            processAttendance(item.no_daftar); 
                            document.getElementById('searchDropdown').classList.add('hidden'); 
                            searchInput.value = ''; 
                        };
                        list.appendChild(li);
                    });
                    document.getElementById('searchDropdown').classList.remove('hidden');
                });
        });
    </script>
</body>
</html>