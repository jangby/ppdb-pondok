<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Keuangan & Laporan Operasional') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- ALERT MESSAGES --}}
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-sm flex items-center gap-3 animate-fade-in-down">
                    <div class="bg-green-100 p-2 rounded-full text-green-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <p class="text-sm text-green-700 font-bold">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm flex items-center gap-3 animate-fade-in-down">
                    <div class="bg-red-100 p-2 rounded-full text-red-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <p class="text-sm text-red-700 font-bold">{{ session('error') }}</p>
                </div>
            @endif

            {{-- 1. FITUR UTAMA: REKAP SETORAN (CUT OFF) --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-indigo-100 relative">
                <div class="bg-gradient-to-r from-indigo-700 to-purple-800 px-8 py-6 flex flex-col md:flex-row justify-between items-start md:items-center relative overflow-hidden">
                    <div class="relative z-10">
                        <h3 class="font-bold text-white text-xl flex items-center gap-2">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Rekap & Setor Kas (Cut Off)
                        </h3>
                        <p class="text-indigo-200 text-sm mt-1">Pilih item untuk melihat sisa kas fisik, download laporan, dan setor ke Yayasan.</p>
                    </div>
                    <svg class="w-40 h-40 text-white opacity-5 absolute -right-10 -bottom-16 rotate-12 pointer-events-none" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                </div>
                
                <div class="p-8 bg-indigo-50/30">
                    <form action="{{ route('admin.finance.export_deposit') }}" method="POST">
                        @csrf
                        
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
                                <span class="bg-indigo-600 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-black shadow-md">1</span>
                                Pilih Sumber Dana yang akan disetor:
                            </label>
                            
                            {{-- LOGIKA PERHITUNGAN LENGKAP --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                                @foreach($paymentTypes as $item)
                                    @php
                                        // 1. Uang masuk dari pembayaran santri
                                        $totalMasuk = $item->bills->sum('nominal_terbayar');
                                        
                                        // 2. Uang yang sudah disetor via Cut-Off (Sistem)
                                        $totalDisetorSystem = $item->bills->sum('nominal_disetor');
                                        
                                        // 3. Uang yang terpakai manual (Beli ATK dll menggunakan dana ini)
                                        // PERBAIKAN: Cari berdasarkan STRING Judul karena tidak ada kolom payment_type_id
                                        $searchString = '(Sumber: ' . $item->nama_pembayaran . ')';
                                        $totalTerpakaiManual = \App\Models\Expense::where('judul_pengeluaran', 'LIKE', '%' . $searchString . '%')
                                                                ->sum('total_keluar');

                                        // 4. Rumus Saldo Fisik (Cash on Hand)
                                        // Saldo = Masuk - (Disetor + Terpakai)
                                        $siapSetor = $totalMasuk - $totalDisetorSystem - $totalTerpakaiManual;
                                        
                                        // Jika hasil negatif (misal input manual berlebih), set 0 agar tampilan tidak minus
                                        $displaySiapSetor = $siapSetor < 0 ? 0 : $siapSetor;
                                        
                                        $isAvailable = $displaySiapSetor > 0;
                                    @endphp

                                <label class="relative cursor-pointer group">
                                    <input type="checkbox" name="items[]" value="{{ $item->id }}" class="peer sr-only" {{ !$isAvailable ? 'disabled' : '' }}>
                                    
                                    <div class="relative p-5 rounded-2xl border-2 {{ $isAvailable ? 'border-white bg-white hover:border-indigo-300 cursor-pointer shadow-sm hover:shadow-md' : 'border-gray-100 bg-gray-50 opacity-70 cursor-not-allowed' }} peer-checked:border-indigo-600 peer-checked:bg-indigo-50 peer-checked:shadow-lg peer-checked:shadow-indigo-100 transition-all duration-200 h-full flex flex-col justify-between">
                                        
                                        {{-- Header Kartu --}}
                                        <div class="flex justify-between items-start mb-3 border-b border-gray-100 pb-2">
                                            <div>
                                                <span class="text-[10px] font-black uppercase tracking-wider {{ $isAvailable ? 'text-indigo-600 bg-indigo-50' : 'text-gray-400 bg-gray-100' }} px-2 py-1 rounded-md">
                                                    {{ $item->jenjang }}
                                                </span>
                                                <h4 class="font-bold text-gray-800 text-sm mt-1.5 leading-tight">{{ $item->nama_pembayaran }}</h4>
                                            </div>
                                            
                                            {{-- Check Icon --}}
                                            <div class="w-6 h-6 rounded-full border-2 border-gray-200 peer-checked:bg-indigo-600 peer-checked:border-indigo-600 flex items-center justify-center transition-colors shadow-sm">
                                                <svg class="w-4 h-4 text-white opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                        </div>

                                        {{-- Body Informasi Keuangan --}}
                                        <div class="space-y-1.5 text-xs">
                                            <div class="flex justify-between items-center text-gray-500">
                                                <span>Total Masuk</span>
                                                <span class="font-medium text-gray-700">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</span>
                                            </div>
                                            
                                            {{-- Info Pengurangan --}}
                                            <div class="flex justify-between items-center text-red-400">
                                                <span>Sudah Disetor</span>
                                                <span>- {{ number_format($totalDisetorSystem, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="flex justify-between items-center text-orange-400">
                                                <span>Terpakai (Manual)</span>
                                                <span>- {{ number_format($totalTerpakaiManual, 0, ',', '.') }}</span>
                                            </div>
                                            
                                            <div class="mt-2 pt-2 border-t border-dashed border-gray-200 flex justify-between items-center">
                                                <span class="font-bold {{ $isAvailable ? 'text-gray-600' : 'text-gray-400' }}">SISA KAS</span>
                                                <span class="font-black text-sm {{ $isAvailable ? 'text-green-600 bg-green-50 px-2 py-0.5 rounded' : 'text-gray-400' }}">
                                                    Rp {{ number_format($displaySiapSetor, 0, ',', '.') }}
                                                </span>
                                            </div>
                                        </div>

                                    </div>
                                    
                                    {{-- Highlight Border --}}
                                    <div class="absolute inset-0 border-2 border-transparent peer-checked:border-indigo-600 rounded-2xl pointer-events-none"></div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row justify-between items-center pt-6 border-t border-indigo-100 gap-4 mt-6">
                            <div class="text-xs text-gray-500 italic max-w-lg">
                                <span class="font-bold text-red-500">* Perhatian:</span> Saldo "SISA KAS" akan diambil, lalu sistem akan mereset status tagihan santri menjadi "Disetor". Pastikan uang fisik sesuai dengan nominal.
                            </div>
                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-8 py-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl shadow-lg shadow-green-200 transition transform hover:-translate-y-0.5 border-b-4 border-green-800 active:border-b-0 active:translate-y-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                <span>Proses & Download Excel</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                
                {{-- 2. FORM CATAT PENGELUARAN (MANUAL) --}}
                <div class="bg-white shadow-lg rounded-2xl overflow-hidden border border-gray-100 sticky top-24">
                    <div class="bg-gray-800 px-6 py-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        <h3 class="font-bold text-white">Catat Pengeluaran Lain</h3>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('admin.finance.store') }}" method="POST" class="space-y-5">
                            @csrf
                            
                            {{-- Input Sumber Dana --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Sumber Dana (Wajib Pilih)</label>
                                <select name="source_id" class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm">
                                    <option value="">-- Pilih Pos Anggaran --</option>
                                    @foreach($paymentTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->nama_pembayaran }} ({{ $type->jenjang }})</option>
                                    @endforeach
                                </select>
                                <p class="text-[10px] text-gray-400 mt-1">Uang diambil dari pos mana? (Penting untuk perhitungan saldo).</p>
                            </div>

                            {{-- Input Keterangan --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Keterangan / Judul</label>
                                <input type="text" name="keterangan" required class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm placeholder-gray-300" placeholder="Contoh: Beli ATK, Biaya Listrik">
                            </div>
                            
                            {{-- Input Nominal --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nominal (Rp)</label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="text-gray-500 sm:text-sm font-bold">Rp</span>
                                    </div>
                                    <input type="number" name="nominal" required class="block w-full rounded-xl border-gray-300 pl-10 focus:border-red-500 focus:ring-red-500 text-sm font-bold text-gray-800" placeholder="0">
                                </div>
                            </div>

                            {{-- Input Tanggal --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tanggal Transaksi</label>
                                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm">
                            </div>

                            <button type="submit" class="w-full py-3 bg-red-600 text-white font-bold rounded-xl text-sm hover:bg-red-700 transition shadow-md flex justify-center gap-2 border-b-4 border-red-800 active:border-b-0 active:translate-y-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                                Simpan Pengeluaran
                            </button>
                        </form>
                    </div>
                </div>

                {{-- 3. TABEL RIWAYAT --}}
                <div class="lg:col-span-2 bg-white shadow-lg rounded-2xl overflow-hidden border border-gray-100">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                            Riwayat Pengeluaran / Setoran
                        </h3>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Keterangan</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Total Keluar</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($expenses as $expense)
                                <tr class="hover:bg-gray-50 transition group">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($expense->tanggal)->format('d M Y') }}
                                        <div class="text-[10px] text-gray-400 mt-1">Admin: {{ $expense->user->name ?? 'System' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{-- Tampilkan Sumber Dana (Diambil dari Judul yang kita gabung di controller) --}}
                                        @if(str_contains($expense->judul_pengeluaran, '(Sumber:'))
                                            @php
                                                // Ekstrak teks sumber dana untuk styling
                                                preg_match('/\((Sumber:.*?)\)/', $expense->judul_pengeluaran, $matches);
                                                $sumberLabel = $matches[1] ?? 'Dana Khusus';
                                                $judulBersih = trim(str_replace('('.$sumberLabel.')', '', $expense->judul_pengeluaran));
                                            @endphp
                                            <div class="mb-1">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-orange-100 text-orange-800 border border-orange-200">
                                                    {{ $sumberLabel }}
                                                </span>
                                            </div>
                                            <div class="font-medium">{{ $judulBersih }}</div>
                                        @else
                                            <div class="font-medium">{{ $expense->judul_pengeluaran }}</div>
                                        @endif
                                        
                                        @if(str_contains($expense->judul_pengeluaran, '(Auto)'))
                                            <span class="mt-1 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-700 border border-indigo-200">
                                                SYSTEM CUT-OFF
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-red-600">
                                        Rp {{ number_format($expense->total_keluar, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <form action="{{ route('admin.finance.destroy', $expense->id) }}" method="POST" onsubmit="return confirm('Hapus data pengeluaran ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-gray-300 hover:text-red-600 transition p-2 hover:bg-red-50 rounded-full">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-400 text-sm italic">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            <span>Belum ada data pengeluaran tercatat.</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>