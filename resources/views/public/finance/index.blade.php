<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Santri - {{ $candidate->nama_lengkap }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen pb-10">

    {{-- HEADER --}}
    <div class="bg-indigo-700 pt-8 pb-24 px-6 rounded-b-[2.5rem] shadow-xl relative overflow-hidden">
        <div class="relative z-10 text-center">
            <h1 class="text-white font-bold text-lg opacity-90">PORTAL SANTRI PSB</h1>
            <p class="text-indigo-200 text-xs">Pondok Pesantren Assa'adah</p>
            
            <div class="mt-6">
                <h2 class="text-2xl font-extrabold text-white">{{ $candidate->nama_lengkap }}</h2>
                <span class="inline-block mt-1 px-3 py-1 bg-white/20 text-white rounded-full text-xs font-mono tracking-wider backdrop-blur-sm">
                    {{ $candidate->no_daftar }}
                </span>
            </div>
        </div>
        {{-- Hiasan Background --}}
        <div class="absolute top-0 left-0 w-32 h-32 bg-white/10 rounded-full -ml-10 -mt-10 blur-2xl"></div>
        <div class="absolute bottom-0 right-0 w-40 h-40 bg-indigo-500/30 rounded-full -mr-10 -mb-10 blur-2xl"></div>
    </div>

    {{-- CARD STATUS & EDIT (Float) --}}
    <div class="px-6 -mt-16 relative z-20">
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Status Pendaftaran</span>
                    <div class="mt-1">
                        @php
                            // Menentukan warna berdasarkan status santri
                            $statusColor = match($candidate->status) {
                                'Baru' => 'bg-blue-100 text-blue-700',
                                'Lulus' => 'bg-green-100 text-green-700',
                                'Tidak Lulus' => 'bg-red-100 text-red-700',
                                default => 'bg-orange-100 text-orange-700',
                            };
                        @endphp
                        <span class="px-3 py-1 {{ $statusColor }} rounded-lg text-sm font-bold uppercase">
                            {{ $candidate->status }}
                        </span>
                    </div>
                </div>
                
                {{-- Tombol Edit: Hanya muncul jika belum Lulus/Siswa Aktif --}}
                @if(!in_array($candidate->status, ['Lulus', 'Siswa Aktif']))
                <a href="{{ route('pendaftaran.edit_public', $candidate->no_daftar) }}" class="flex items-center gap-1 text-xs font-bold text-indigo-600 border border-indigo-600 px-3 py-2 rounded-lg hover:bg-indigo-50 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    EDIT DATA
                </a>
                @endif
            </div>

            <div class="space-y-1 text-center mb-6 border-t pt-4">
                <p class="text-sm text-gray-500">Sisa Kewajiban Pembayaran</p>
                <p class="text-3xl font-black text-gray-800">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</p>
            </div>

            {{-- Progress Bar Keseluruhan --}}
            <div class="relative h-3 w-full bg-gray-100 rounded-full overflow-hidden">
                <div class="absolute top-0 left-0 h-full bg-indigo-500 transition-all duration-1000" style="width: {{ $persentase }}%"></div>
            </div>
            <div class="flex justify-between mt-2 text-xs font-medium text-gray-500">
                <span>Terbayar: Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</span>
                <span>{{ round($persentase) }}%</span>
            </div>
        </div>
    </div>

    {{-- Notifikasi Sukses --}}
    @if(session('success'))
    <div class="px-6 mt-4">
        <div class="bg-green-500 text-white p-3 rounded-xl text-sm font-bold shadow-lg flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    </div>
    @endif

    {{-- RIWAYAT TRANSAKSI (Dinaikkan ke Atas) --}}
    <div class="px-6 mt-8">
        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2 uppercase tracking-wider text-sm">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Riwayat Setoran Pembayaran
        </h3>
        
        <div class="space-y-3">
            @forelse($candidate->transactions->sortByDesc('created_at') as $trx)
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center hover:shadow-md transition">
                <div>
                    <p class="text-xs font-bold text-gray-800">#{{ $trx->kode_transaksi }}</p>
                    <p class="text-[10px] text-gray-400 uppercase">{{ $trx->created_at->translatedFormat('d M Y, H:i') }}</p>
                    <p class="text-sm font-bold text-green-600 mt-1">Rp {{ number_format($trx->total_bayar, 0, ',', '.') }}</p>
                </div>
                
                {{-- Tombol Unduh Struk (Direct Download PDF Thermal) --}}
                <a href="{{ route('public.pendaftaran.receipt', [$candidate->no_daftar, $trx->id]) }}" 
                   class="p-2.5 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-600 hover:text-white transition shadow-sm border border-indigo-100 flex items-center gap-2 text-[10px] font-bold">
                    STRUK
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                </a>
            </div>
            @empty
            <div class="p-8 text-center bg-white rounded-xl text-gray-400 text-xs italic border border-dashed border-gray-200">
                Belum ada riwayat pembayaran yang tercatat dalam sistem.
            </div>
            @endforelse
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="mt-12 mb-6 text-center">
        <p class="text-[10px] text-gray-400">Dicetak secara otomatis melalui Sistem PSB Terintegrasi</p>
        <div class="mt-4">
            <a href="/" class="text-[11px] bg-white px-4 py-2 rounded-full text-indigo-600 border border-indigo-100 font-bold shadow-sm hover:bg-indigo-50 transition inline-flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Beranda
            </a>
        </div>
    </div>

</body>
</html>