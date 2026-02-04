<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pembayaran Pendaftaran</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen py-8 px-4 flex items-center justify-center">

    <div class="max-w-lg w-full bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-100">
        
        {{-- Header --}}
        <div class="bg-blue-600 p-8 text-center text-white relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-full opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
            <div class="relative z-10">
                <span class="inline-block p-3 bg-white/20 rounded-full mb-4 backdrop-blur-sm">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                </span>
                <h1 class="text-2xl font-bold mb-1">Konfirmasi Pembayaran</h1>
                <p class="text-blue-100 text-sm">Lakukan pembayaran untuk melanjutkan pendaftaran.</p>
            </div>
        </div>

        <div class="p-8 space-y-8">
            
            {{-- Info Rekening --}}
            <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5">
                <h3 class="text-xs font-bold text-blue-500 uppercase tracking-widest mb-3">Rekening Tujuan</h3>
                <div class="text-slate-700 font-mono text-sm whitespace-pre-line leading-relaxed">{{ $rekening ?? 'Hubungi Admin untuk Info Rekening' }}</div>
            </div>

            <form action="{{ route('pendaftaran.payment.store', $data->token) }}" method="POST" enctype="multipart/form-data" class="space-y-6" x-data="{ selectedJenjang: '' }">
                @csrf
                
                {{-- Pilih Jenjang --}}
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Pilih Jenjang Sekolah</label>
                    <select name="jenjang" x-model="selectedJenjang" class="w-full bg-slate-50 border border-slate-200 text-slate-700 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-3.5" required>
                        <option value="" disabled selected>-- Pilih Jenjang --</option>
                        @foreach($biayaList as $item)
                            <option value="{{ $item['jenjang'] }}" data-nominal="{{ $item['formatted'] }}">
                                {{ $item['jenjang'] }} - {{ $item['formatted'] }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-500 mt-2">*Pilih jenjang untuk mengetahui nominal yang harus ditransfer.</p>
                </div>

                {{-- Alert Total Bayar (Muncul jika jenjang dipilih) --}}
                <div x-show="selectedJenjang" class="hidden bg-emerald-50 border border-emerald-100 rounded-xl p-4 text-center" :class="{'hidden': !selectedJenjang}">
                    <p class="text-xs text-emerald-600 font-bold uppercase">Total yang harus dibayar</p>
                    <p class="text-2xl font-extrabold text-emerald-700 mt-1" x-text="$el.parentElement.querySelector('option[value='+selectedJenjang+']').dataset.nominal"></p>
                </div>

                {{-- Upload Bukti --}}
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Upload Bukti Transfer</label>
                    <input type="file" name="bukti_transfer" accept="image/*" required class="block w-full text-sm text-slate-500 file:mr-4 file:py-3 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-slate-200 rounded-xl p-1">
                    <p class="text-[10px] text-slate-400 mt-2">*Format: JPG/PNG/Screenshot. Max 2MB.</p>
                </div>

                {{-- Tombol Kirim --}}
                <button type="submit" class="w-full py-4 bg-slate-900 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transition transform active:scale-95">
                    Kirim Bukti Pembayaran
                </button>
            </form>

            {{-- Status Jika Ditolak --}}
            @if($data->status_pembayaran == 'rejected')
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl">
                    <h4 class="font-bold text-red-700 text-sm">Pembayaran Ditolak</h4>
                    <p class="text-red-600 text-sm mt-1">{{ $data->catatan_pembayaran }}</p>
                    <p class="text-xs text-red-500 mt-2">Silakan upload ulang bukti pembayaran yang benar.</p>
                </div>
            @endif

        </div>
    </div>

</body>
</html>