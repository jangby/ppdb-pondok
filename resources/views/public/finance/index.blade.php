<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rincian Keuangan - {{ $candidate->nama_lengkap }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen pb-10">

    {{-- HEADER --}}
    <div class="bg-indigo-700 pt-8 pb-24 px-6 rounded-b-[2.5rem] shadow-xl relative overflow-hidden">
        <div class="relative z-10 text-center">
            <h1 class="text-white font-bold text-lg opacity-90">PORTAL KEUANGAN SANTRI</h1>
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

    {{-- CARD RINGKASAN (Float) --}}
    <div class="px-6 -mt-16 relative z-20">
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
            <div class="flex justify-between items-center mb-4">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Status Pembayaran</span>
                <span class="text-xs font-bold {{ $sisaTagihan <= 0 ? 'text-green-600' : 'text-orange-500' }}">
                    {{ $sisaTagihan <= 0 ? 'LUNAS' : 'BELUM LUNAS' }}
                </span>
            </div>
            
            <div class="space-y-1 text-center mb-6">
                <p class="text-sm text-gray-500">Sisa Kewajiban</p>
                <p class="text-3xl font-black text-gray-800">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</p>
            </div>

            {{-- Progress Bar --}}
            <div class="relative h-3 w-full bg-gray-100 rounded-full overflow-hidden">
                <div class="absolute top-0 left-0 h-full bg-gradient-to-r from-indigo-500 to-purple-500 transition-all duration-1000" style="width: {{ $persentase }}%"></div>
            </div>
            <div class="flex justify-between mt-2 text-xs font-medium text-gray-500">
                <span>Terbayar: Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</span>
                <span>{{ round($persentase) }}%</span>
            </div>
        </div>
    </div>

    {{-- LIST ITEM TAGIHAN --}}
    <div class="px-6 mt-8">
        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            Rincian Item Pembayaran
        </h3>
        
        <div class="space-y-4">
            @foreach($candidate->bills as $bill)
            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm relative overflow-hidden">
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="font-bold text-gray-800 text-sm">{{ $bill->payment_type->nama_pembayaran }}</h4>
                        <p class="text-xs text-gray-400 mt-1">Total: Rp {{ number_format($bill->nominal_tagihan, 0, ',', '.') }}</p>
                    </div>
                    <div class="text-right">
                        @if($bill->sisa_tagihan <= 0)
                            <span class="bg-green-100 text-green-700 text-[10px] font-bold px-2 py-1 rounded-full">LUNAS</span>
                        @else
                            <span class="text-xs font-bold text-red-500">- Rp {{ number_format($bill->sisa_tagihan, 0, ',', '.') }}</span>
                        @endif
                    </div>
                </div>
                
                {{-- Progress Mini --}}
                @php $itemPercent = ($bill->nominal_terbayar / $bill->nominal_tagihan) * 100; @endphp
                <div class="mt-3 w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                    <div class="h-full {{ $itemPercent >= 100 ? 'bg-green-500' : 'bg-orange-400' }}" style="width: {{ $itemPercent }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- RIWAYAT TRANSAKSI --}}
    <div class="px-6 mt-8">
        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Riwayat Setoran
        </h3>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            @forelse($candidate->transactions->sortByDesc('created_at') as $trx)
            <div class="p-4 border-b border-gray-50 last:border-0 flex justify-between items-center hover:bg-gray-50">
                <div>
                    <p class="text-xs font-bold text-gray-800">#{{ $trx->kode_transaksi }}</p>
                    <p class="text-[10px] text-gray-400">{{ $trx->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold text-green-600">+ Rp {{ number_format($trx->total_bayar, 0, ',', '.') }}</p>
                    <p class="text-[10px] text-gray-400">{{ $trx->admin->name ?? 'Admin' }}</p>
                </div>
            </div>
            @empty
            <div class="p-6 text-center text-gray-400 text-xs italic">Belum ada riwayat transaksi.</div>
            @endforelse
        </div>
    </div>

    <div class="mt-12 mb-6 text-center">
        <p class="text-[10px] text-gray-400">Dicetak otomatis oleh Sistem PPDB</p>
    </div>

</body>
</html>