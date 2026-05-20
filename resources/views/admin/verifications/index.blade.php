<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- HEADER & JUDUL --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-black text-gray-800 tracking-tight">
                        Verifikasi Pendaftaran
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Kelola validasi berkas dan bukti pembayaran santri baru.</p>
                </div>
            </div>

            {{-- 1. KPI STATS CARDS (Statistik Ringkas) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                {{-- Card 1: Antrian Berkas --}}
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition">
                    <div class="absolute right-0 top-0 w-24 h-24 bg-amber-50 rounded-bl-full -mr-4 -mt-4 transition group-hover:bg-amber-100"></div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Cek Berkas</span>
                        </div>
                        <div class="text-3xl font-black text-gray-800">{{ $stats['berkas_pending'] }}</div>
                        <div class="text-xs text-amber-600 font-medium mt-1">Perlu Validasi</div>
                    </div>
                </div>

                {{-- Card 2: Antrian Pembayaran --}}
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition">
                    <div class="absolute right-0 top-0 w-24 h-24 bg-blue-50 rounded-bl-full -mr-4 -mt-4 transition group-hover:bg-blue-100"></div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Cek Transfer</span>
                        </div>
                        <div class="text-3xl font-black text-gray-800">{{ $stats['bayar_pending'] }}</div>
                        <div class="text-xs text-blue-600 font-medium mt-1">Perlu Verifikasi</div>
                    </div>
                </div>

                {{-- Card 3: Selesai --}}
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition">
                    <div class="absolute right-0 top-0 w-24 h-24 bg-emerald-50 rounded-bl-full -mr-4 -mt-4 transition group-hover:bg-emerald-100"></div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Lulus Berkas</span>
                        </div>
                        <div class="text-3xl font-black text-gray-800">{{ $stats['selesai'] }}</div>
                        <div class="text-xs text-emerald-600 font-medium mt-1">Total Diterima</div>
                    </div>
                </div>

                {{-- Card 4: Ditolak --}}
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition">
                    <div class="absolute right-0 top-0 w-24 h-24 bg-red-50 rounded-bl-full -mr-4 -mt-4 transition group-hover:bg-red-100"></div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Ditolak</span>
                        </div>
                        <div class="text-3xl font-black text-gray-800">{{ $stats['ditolak'] }}</div>
                        <div class="text-xs text-red-600 font-medium mt-1">Perlu Perbaikan</div>
                    </div>
                </div>
            </div>

            {{-- ALERT NOTIFIKASI --}}
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-r-lg shadow-sm flex items-center gap-3">
                    <div class="bg-green-100 p-1.5 rounded-full"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                    <span class="font-bold text-sm">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-r-lg shadow-sm flex items-center gap-3">
                    <div class="bg-red-100 p-1.5 rounded-full"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></div>
                    <span class="font-bold text-sm">{{ session('error') }}</span>
                </div>
            @endif

            {{-- 2. TAB & TABEL UTAMA --}}
            <div class="bg-white shadow-xl shadow-gray-100 sm:rounded-2xl border border-gray-100 overflow-hidden">
                
                {{-- Tab Header --}}
                <div class="border-b border-gray-100 bg-gray-50/50 px-6 py-4 flex flex-col md:flex-row justify-between items-center gap-4">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                        Daftar Antrian
                    </h3>

                    <div class="flex bg-gray-200/60 p-1 rounded-xl">
                        <a href="{{ route('admin.verifications.index', ['status' => 'pending']) }}" class="px-4 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-2 {{ $filter == 'pending' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                            <span>⏳ Menunggu</span>
                            @if($stats['total_antrian'] > 0)
                                <span class="px-1.5 py-0.5 rounded-md bg-indigo-100 text-indigo-700 text-[10px]">{{ $stats['total_antrian'] }}</span>
                            @endif
                        </a>
                        <a href="{{ route('admin.verifications.index', ['status' => 'approved']) }}" class="px-4 py-1.5 rounded-lg text-xs font-bold transition {{ $filter == 'approved' ? 'bg-white text-emerald-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">✅ Selesai</a>
                        <a href="{{ route('admin.verifications.index', ['status' => 'rejected']) }}" class="px-4 py-1.5 rounded-lg text-xs font-bold transition {{ $filter == 'rejected' ? 'bg-white text-red-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">❌ Ditolak</a>
                        <a href="{{ route('admin.verifications.index', ['status' => 'all']) }}" class="px-4 py-1.5 rounded-lg text-xs font-bold transition {{ $filter == 'all' ? 'bg-white text-gray-800 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">Semua</a>
                    </div>
                </div>

                {{-- Tabel Data --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Pendaftar</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Status Tahapan</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Lampiran</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($verifications as $v)
                            
                            {{-- LOGIKA CEK APAKAH SUDAH TERDAFTAR (Agar tombol Isi Nama otomatis hilang) --}}
@php
    $candidateData = null;
    
    // 1. Cek dari relasi (jika sudah dihubungkan)
    if ($v->candidate ?? false) {
        $candidateData = $v->candidate;
    } 
    // 2. Jika belum, cek pencocokan file_perjanjian yang di-copy saat submit "Isi Nama"
    elseif (!empty($v->file_perjanjian)) {
        $candidateData = \App\Models\Candidate::where('file_perjanjian', $v->file_perjanjian)->first();
    }
    
    // Tentukan status terdaftar & ambil nama dari tabel candidate
    $isRegistered = $candidateData ? true : false;
    $namaSantri = $candidateData ? $candidateData->nama_lengkap : '';
@endphp

                            <tr class="hover:bg-indigo-50/30 transition group">
                                {{-- KOLOM 1: Info Pendaftar --}}
                                <td class="px-6 py-4 align-top">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xs shadow-md">
                                            {{ substr($v->no_wa, -2) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-800 text-sm font-mono tracking-tight">{{ $v->no_wa }}</div>
                                            <div class="text-[10px] text-gray-400 mt-0.5 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                {{ $v->created_at->format('d M Y • H:i') }}
                                            </div>
                                            @if($v->jenjang)
                                                <span class="mt-2 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                                    {{ $v->jenjang }}
                                                </span>
                                            @endif
                                            @if($namaSantri)
                                                <div class="text-xs text-indigo-600 font-bold mt-1">👤 {{ $namaSantri }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- KOLOM 2: Status Progress --}}
                                <td class="px-6 py-4 align-top">
                                    <div class="flex flex-col gap-2">
                                        <div class="flex items-center justify-between w-40 text-xs p-1.5 rounded-lg border {{ $v->status == 'pending' ? 'bg-amber-50 border-amber-100' : ($v->status == 'approved' ? 'bg-emerald-50 border-emerald-100' : 'bg-red-50 border-red-100') }}">
                                            <span class="text-gray-500 font-medium text-[10px]">Berkas</span>
                                            @if($v->status == 'pending')
                                                <span class="font-bold text-amber-600 flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Cek</span>
                                            @elseif($v->status == 'approved')
                                                <span class="font-bold text-emerald-600">✔ OK</span>
                                            @else
                                                <span class="font-bold text-red-600">✘ Tolak</span>
                                            @endif
                                        </div>

                                        <div class="flex items-center justify-between w-40 text-xs p-1.5 rounded-lg border {{ $v->status_pembayaran == 'pending' && $v->bukti_transfer ? 'bg-blue-50 border-blue-100' : ($v->status_pembayaran == 'paid' ? 'bg-emerald-50 border-emerald-100' : 'bg-gray-50 border-gray-100') }}">
                                            <span class="text-gray-500 font-medium text-[10px]">Bayar</span>
                                            @if($v->status_pembayaran == 'pending' && $v->bukti_transfer)
                                                <span class="font-bold text-blue-600 flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span> Verif</span>
                                            @elseif($v->status_pembayaran == 'paid')
                                                <span class="font-bold text-emerald-600">✔ Lunas</span>
                                            @elseif($v->status_pembayaran == 'rejected')
                                                <span class="font-bold text-red-600">✘ Salah</span>
                                            @else
                                                <span class="font-bold text-gray-400">-</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- KOLOM 3: Berkas --}}
                                <td class="px-6 py-4 align-top">
                                    <div class="flex flex-col gap-2">
                                        <a href="{{ asset('storage/'.$v->file_perjanjian) }}" target="_blank" class="group/link flex items-center gap-3 p-2 rounded-lg border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition bg-white w-48">
                                            <div class="bg-indigo-100 p-1.5 rounded text-indigo-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg></div>
                                            <div class="flex-1"><div class="text-[10px] uppercase text-gray-400 font-bold">Dokumen</div><div class="text-xs font-bold text-gray-700 group-hover/link:text-indigo-700">Surat Perjanjian</div></div>
                                        </a>

                                        @if($v->bukti_transfer)
                                        <a href="{{ asset('storage/'.$v->bukti_transfer) }}" target="_blank" class="group/link flex items-center gap-3 p-2 rounded-lg border border-gray-200 hover:border-emerald-300 hover:bg-emerald-50 transition bg-white w-48">
                                            <div class="bg-emerald-100 p-1.5 rounded text-emerald-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                                            <div class="flex-1"><div class="text-[10px] uppercase text-gray-400 font-bold">Keuangan</div><div class="text-xs font-bold text-gray-700 group-hover/link:text-emerald-700">Bukti Transfer</div></div>
                                        </a>
                                        @endif
                                    </div>
                                </td>

                                {{-- KOLOM 4: Aksi --}}
                                <td class="px-6 py-4 align-top text-right">
                                    <div class="flex flex-col gap-2 items-end">
                                        
                                        {{-- TAHAP 1: JIKA BERKAS MASIH PENDING --}}
                                        @if($v->status == 'pending')
                                            <form action="{{ route('admin.verifications.approve', $v->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="w-28 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-lg text-xs font-bold shadow-md hover:shadow-lg transition flex items-center justify-center gap-1.5" onclick="return confirm('ACC Perjanjian?')">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    ACC Berkas
                                                </button>
                                            </form>
                                            <div x-data="{ open: false }" class="relative">
                                                <button @click="open = !open" type="button" class="w-28 bg-white border border-gray-300 text-gray-600 hover:bg-red-50 hover:text-red-600 hover:border-red-200 px-3 py-2 rounded-lg text-xs font-bold transition">Tolak</button>
                                                <div x-show="open" @click.outside="open = false" class="absolute right-0 mt-2 w-64 bg-white border border-gray-200 shadow-xl rounded-xl p-3 z-50 text-left">
                                                    <form action="{{ route('admin.verifications.reject', $v->id) }}" method="POST">
                                                        @csrf
                                                        <label class="block text-xs font-bold text-gray-700 mb-1">Alasan Tolak:</label>
                                                        <textarea name="alasan" rows="2" class="w-full text-xs border-gray-300 rounded-lg mb-2 focus:ring-red-500 focus:border-red-500"></textarea>
                                                        <button type="submit" class="w-full bg-red-600 text-white text-xs font-bold py-1.5 rounded hover:bg-red-700">Konfirmasi</button>
                                                    </form>
                                                </div>
                                            </div>

                                        {{-- TAHAP 1.5: JIKA BERKAS ACC, TAPI WALI BELUM BAYAR/UPLOAD --}}
                                        @elseif($v->status == 'approved' && $v->status_pembayaran == 'unpaid')
                                            
                                            {{-- CEK APAKAH SUDAH TERDAFTAR --}}
                                            @if(!$isRegistered)
                                                <button type="button" onclick="openRegisterModal('{{ $v->id }}', '{{ $namaSantri }}')" class="w-28 bg-amber-600 hover:bg-amber-700 text-white px-2 py-1.5 rounded-lg text-[10px] font-bold shadow-sm transition flex items-center justify-center gap-1 mb-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                    Isi Nama
                                                </button>
                                            @else
                                                <div class="w-28 bg-slate-100 border border-slate-200 text-slate-500 px-2 py-1.5 rounded-lg text-[10px] font-bold text-center shadow-sm mb-1 flex items-center justify-center gap-1 cursor-default" title="Nama anak sudah masuk ke database utama">
                                                    <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    Terdaftar
                                                </div>
                                            @endif

                                            @if(!$v->wa_tahap1_sent)
                                                <div class="w-28 bg-red-50 border border-red-200 p-1.5 rounded-lg shadow-sm text-center">
                                                    <span class="text-[9px] font-bold text-red-600 block mb-1">⚠️ WA Tagihan Gagal</span>
                                                    <form action="{{ route('admin.verifications.resend_wa', ['id' => $v->id, 'tahap' => 1]) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-1 rounded text-[9px] font-bold">Kirim Ulang WA</button>
                                                    </form>
                                                </div>
                                            @endif

                                            <div class="w-28 bg-amber-50 border border-amber-200 text-amber-600 px-2 py-1.5 rounded-lg text-center shadow-sm">
                                                <div class="text-[9px] font-bold uppercase">Menunggu Transfer</div>
                                            </div>
                                            <form action="{{ route('admin.verifications.approve', $v->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="w-28 bg-green-600 hover:bg-green-700 text-white px-2 py-1.5 rounded-lg text-[10px] font-bold shadow-sm transition flex items-center justify-center gap-1" onclick="return confirm('Terima Pembayaran CASH?')">
                                                    Terima Cash
                                                </button>
                                            </form>

                                        {{-- TAHAP 2: JIKA WALI SUDAH UPLOAD, MENUNGGU ACC ADMIN --}}
                                        @elseif($v->status == 'approved' && $v->status_pembayaran == 'pending')

                                            {{-- CEK APAKAH SUDAH TERDAFTAR --}}
                                            @if(!$isRegistered)
                                                <button type="button" onclick="openRegisterModal('{{ $v->id }}', '{{ $namaSantri }}')" class="w-28 bg-amber-600 hover:bg-amber-700 text-white px-2 py-1.5 rounded-lg text-[10px] font-bold shadow-sm transition flex items-center justify-center gap-1 mb-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                    Isi Nama
                                                </button>
                                            @else
                                                <div class="w-28 bg-slate-100 border border-slate-200 text-slate-500 px-2 py-1.5 rounded-lg text-[10px] font-bold text-center shadow-sm mb-1 flex items-center justify-center gap-1 cursor-default" title="Nama anak sudah masuk ke database utama">
                                                    <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    Terdaftar
                                                </div>
                                            @endif

                                            <form action="{{ route('admin.verifications.approve', $v->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="w-28 bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-2 rounded-lg text-xs font-bold shadow-md hover:shadow-lg transition flex items-center justify-center gap-1.5" onclick="return confirm('ACC Pembayaran?')">
                                                    Terima Bayar
                                                </button>
                                            </form>
                                            <div x-data="{ open: false }" class="relative">
                                                <button @click="open = !open" type="button" class="w-28 bg-white border border-gray-300 text-gray-600 hover:bg-red-50 hover:text-red-600 hover:border-red-200 px-3 py-2 rounded-lg text-xs font-bold transition">Tolak</button>
                                                <div x-show="open" @click.outside="open = false" class="absolute right-0 mt-2 w-64 bg-white border border-gray-200 shadow-xl rounded-xl p-3 z-50 text-left">
                                                    <form action="{{ route('admin.verifications.reject', $v->id) }}" method="POST">
                                                        @csrf
                                                        <label class="block text-xs font-bold text-gray-700 mb-1">Alasan Salah:</label>
                                                        <textarea name="alasan" rows="2" class="w-full text-xs border-gray-300 rounded-lg mb-2 focus:ring-red-500 focus:border-red-500" placeholder="Nominal tidak sesuai / Buram"></textarea>
                                                        <button type="submit" class="w-full bg-red-600 text-white text-xs font-bold py-1.5 rounded hover:bg-red-700">Tolak Bukti</button>
                                                    </form>
                                                </div>
                                            </div>

                                        {{-- SUDAH SELESAI --}}
                                        @elseif($v->status_pembayaran == 'paid')

                                            {{-- CEK APAKAH SUDAH TERDAFTAR --}}
                                            @if(!$isRegistered)
                                                <button type="button" onclick="openRegisterModal('{{ $v->id }}', '{{ $namaSantri }}')" class="w-28 bg-amber-600 hover:bg-amber-700 text-white px-2 py-1.5 rounded-lg text-[10px] font-bold shadow-sm transition flex items-center justify-center gap-1 mb-2">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                    Isi Nama
                                                </button>
                                            @else
                                                <div class="w-28 bg-slate-100 border border-slate-200 text-slate-500 px-2 py-1.5 rounded-lg text-[10px] font-bold text-center shadow-sm mb-2 flex items-center justify-center gap-1 cursor-default" title="Nama anak sudah masuk ke database utama">
                                                    <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    Terdaftar
                                                </div>
                                            @endif

                                            @if(!$v->wa_tahap2_sent)
                                                <div class="w-28 bg-red-50 border border-red-200 p-1.5 rounded-lg shadow-sm text-center mb-2">
                                                    <span class="text-[9px] font-bold text-red-600 block mb-1">⚠️ WA Biodata Gagal</span>
                                                    <form action="{{ route('admin.verifications.resend_wa', ['id' => $v->id, 'tahap' => 2]) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-1 rounded text-[9px] font-bold">Kirim Ulang WA</button>
                                                    </form>
                                                </div>
                                            @endif

                                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-50 text-green-700 rounded-full text-[10px] font-bold border border-green-200">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                Verified
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </div>
                                        <h3 class="text-gray-900 font-medium text-sm">Tidak ada data ditemukan</h3>
                                        <p class="text-gray-500 text-xs mt-1 max-w-xs">Data verifikasi dengan filter status ini belum tersedia.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $verifications->appends(['status' => $filter])->links() }}
                </div>
            </div>

        </div>
    </div>

    <div id="registerBasicModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 animate-fade-in">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-slate-800">Daftarkan Nama Santri</h3>
                <button type="button" onclick="closeRegisterModal()" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form id="registerBasicForm" method="POST" action="">
                @csrf
                <div class="space-y-4">
                    {{-- Input Nama --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" id="modal_nama_lengkap" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-emerald-500" required>
                    </div>

                    {{-- Input Jenjang (Sekarang Dinamis dari Database) --}}
                    @php
                        $listJenjangModal = json_decode(\App\Models\Setting::getValue('list_jenjang'), true) ?? ['SMP', 'SMK'];
                    @endphp
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Jenjang Pendidikan</label>
                        <select name="jenjang" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm p-2.5 outline-none focus:ring-2 focus:ring-emerald-500" required>
                            <option value="">-- Pilih Jenjang --</option>
                            @foreach($listJenjangModal as $j)
                                <option value="{{ $j }}">{{ $j }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Input Jenis Kelamin --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Jenis Kelamin</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-1.5 text-sm font-semibold text-slate-700 cursor-pointer">
                                <input type="radio" name="jenis_kelamin" value="L" required class="text-emerald-600 focus:ring-emerald-500"> Laki-laki
                            </label>
                            <label class="flex items-center gap-1.5 text-sm font-semibold text-slate-700 cursor-pointer">
                                <input type="radio" name="jenis_kelamin" value="P" class="text-emerald-600 focus:ring-emerald-500"> Perempuan
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" onclick="closeRegisterModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-bold rounded-xl transition">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition shadow-md">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRegisterModal(id, name) {
            const modal = document.getElementById('registerBasicModal');
            const form = document.getElementById('registerBasicForm');
            const inputNama = document.getElementById('modal_nama_lengkap');
            
            // Pasang URL Action Form secara dinamis
            form.action = `/admin/verifications/${id}/register-basic`;
            inputNama.value = name; // Isi otomatis dengan nama yang ada di verifikasi berkas
            
            modal.classList.remove('hidden');
        }

        function closeRegisterModal() {
            document.getElementById('registerBasicModal').classList.add('hidden');
        }
    </script>
</x-app-layout>