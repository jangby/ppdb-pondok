<x-panitia-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            Dashboard Pewawancara
        </h2>
    </x-slot>

    <div class="py-12 px-4 max-w-7xl mx-auto space-y-6">

        <div class="bg-white p-6 rounded-2xl shadow-lg border border-indigo-100">
            
            {{-- 1. KONTROL KAMERA --}}
            <div class="mb-4 text-center">
                <h3 class="font-bold text-lg mb-2">Pilih Kamera & Mulai</h3>
                
                {{-- Dropdown Pilihan Kamera --}}
                <select id="cameraSelection" class="block w-full max-w-xs mx-auto mb-3 rounded-lg border-gray-300 text-sm">
                    <option value="" disabled selected>Sedang memuat daftar kamera...</option>
                </select>

                {{-- Tombol Start/Stop --}}
                <div class="flex justify-center gap-3">
                    <button id="btnStart" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700 transition" disabled>
                        Nyalakan Kamera
                    </button>
                    <button id="btnStop" class="bg-red-500 text-white px-6 py-2 rounded-lg font-bold hover:bg-red-600 transition hidden">
                        Matikan
                    </button>
                </div>
                <p id="errorMsg" class="text-red-500 text-xs mt-2 font-mono"></p>
            </div>

            {{-- 2. AREA KAMERA --}}
            <div class="max-w-md mx-auto bg-black rounded-lg overflow-hidden border-4 border-gray-300 relative min-h-[300px] flex items-center justify-center">
                <div id="reader" class="w-full h-full"></div>
                <p id="placeholderText" class="absolute text-gray-500 text-sm">Kamera belum aktif</p>
            </div>

            {{-- 3. LOADING --}}
            <div id="loadingIndicator" class="hidden mt-4 text-center">
                <span class="px-4 py-2 bg-indigo-100 text-indigo-700 rounded-full font-bold text-sm">
                    Memproses Data...
                </span>
            </div>
        </div>

        {{-- 4. INPUT MANUAL --}}
        <div class="max-w-md mx-auto bg-gray-50 p-6 rounded-2xl border border-gray-200 text-center">
            <p class="text-sm text-gray-500 mb-2">Input Manual:</p>
            <div class="flex gap-2">
                <input type="text" id="manualInput" placeholder="No. Registrasi" class="flex-1 rounded-lg border-gray-300 uppercase font-bold text-center">
                <button onclick="processCode(document.getElementById('manualInput').value)" class="bg-gray-800 text-white px-4 py-2 rounded-lg font-bold">Cari</button>
            </div>
        </div>

    </div>

    {{-- LIBRARY --}}
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let html5QrCode;
        let cameraId;
        
        // FUNGSI 1: CARI KAMERA SAAT HALAMAN DIMUAT
        document.addEventListener('DOMContentLoaded', () => {
            Html5Qrcode.getCameras().then(devices => {
                const select = document.getElementById('cameraSelection');
                const btnStart = document.getElementById('btnStart');

                if (devices && devices.length) {
                    select.innerHTML = ''; // Kosongkan loading msg
                    
                    devices.forEach(device => {
                        const option = document.createElement('option');
                        option.value = device.id;
                        option.text = device.label || `Kamera ${select.length + 1}`;
                        select.appendChild(option);
                    });

                    btnStart.disabled = false; // Aktifkan tombol
                    cameraId = devices[0].id; // Default kamera pertama
                } else {
                    select.innerHTML = '<option>Tidak ada kamera ditemukan</option>';
                    document.getElementById('errorMsg').innerText = "Browser tidak mendeteksi kamera.";
                }
            }).catch(err => {
                document.getElementById('errorMsg').innerText = "Error Izin Kamera: " + err;
                Swal.fire('Izin Ditolak', 'Mohon izinkan akses kamera di browser Anda (Klik ikon gembok di URL bar).', 'error');
            });
        });

        // FUNGSI 2: TOMBOL START KLIK
        document.getElementById('btnStart').addEventListener('click', () => {
            const selectedCameraId = document.getElementById('cameraSelection').value;
            if (!selectedCameraId) return;

            document.getElementById('placeholderText').style.display = 'none';
            document.getElementById('errorMsg').innerText = "";

            html5QrCode = new Html5Qrcode("reader");
            
            html5QrCode.start(
                selectedCameraId, 
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 }
                },
                (decodedText, decodedResult) => {
                    // SUKSES SCAN
                    processCode(decodedText);
                    stopCamera(); // Matikan kamera setelah sukses (opsional)
                },
                (errorMessage) => {
                    // Scanning... (abaikan error frame kosong)
                }
            ).then(() => {
                // Kamera Berhasil Nyala
                document.getElementById('btnStart').classList.add('hidden');
                document.getElementById('btnStop').classList.remove('hidden');
                document.getElementById('cameraSelection').disabled = true;
            }).catch(err => {
                // Kamera Gagal Nyala
                document.getElementById('errorMsg').innerText = "Gagal Start: " + err;
                document.getElementById('placeholderText').style.display = 'block';
                alert("Gagal menyalakan kamera. Pastikan kamera tidak dipakai aplikasi lain (Zoom/Meet).");
            });
        });

        // FUNGSI 3: TOMBOL STOP
        document.getElementById('btnStop').addEventListener('click', stopCamera);

        function stopCamera() {
            if(html5QrCode) {
                html5QrCode.stop().then(() => {
                    document.getElementById('btnStart').classList.remove('hidden');
                    document.getElementById('btnStop').classList.add('hidden');
                    document.getElementById('cameraSelection').disabled = false;
                    document.getElementById('placeholderText').style.display = 'block';
                    html5QrCode.clear();
                }).catch(err => console.log(err));
            }
        }

        // FUNGSI 4: PROSES KE SERVER
        function processCode(code) {
            if(!code) return;
            document.getElementById('loadingIndicator').classList.remove('hidden');

            fetch("{{ route('panitia.interview.scan') }}", {
                method: "POST",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                body: JSON.stringify({ code: code })
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    Swal.fire({
                        icon: 'success', title: 'Berhasil', text: data.nama,
                        timer: 1000, showConfirmButton: false
                    }).then(() => window.location.href = data.redirect_url);
                } else {
                    throw new Error(data.message);
                }
            })
            .catch(err => {
                Swal.fire('Gagal', err.message || 'Data tidak ditemukan', 'error');
                document.getElementById('loadingIndicator').classList.add('hidden');
            });
        }
    </script>
</x-panitia-layout>