<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Dashboard Seleksi & Wawancara') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- 1. MENU OPERASIONAL HARI-H --}}
            <div>
                <h3 class="font-bold text-gray-800 text-lg mb-4 flex items-center gap-2">
                    <span class="bg-red-100 text-red-600 p-1.5 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </span>
                    Menu Operasional (Hari Pelaksanaan)
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    
                    {{-- Tombol Scanner --}}
                    <a href="{{ route('admin.attendance.index') }}" class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-indigo-500 hover:shadow-md transition flex items-center gap-4">
                        <div class="p-4 bg-indigo-100 text-indigo-600 rounded-xl group-hover:bg-indigo-600 group-hover:text-white transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">Meja Registrasi</h3>
                            <p class="text-[10px] text-gray-500">Scan QR & Cetak Antrian.</p>
                        </div>
                    </a>

                    {{-- Tombol Rekap --}}
                    <a href="{{ route('admin.attendance.recap') }}" class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-pink-500 hover:shadow-md transition flex items-center gap-4">
                        <div class="p-4 bg-pink-100 text-pink-600 rounded-xl group-hover:bg-pink-600 group-hover:text-white transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">Rekap Hadir</h3>
                            <p class="text-[10px] text-gray-500">Pantau kehadiran real-time.</p>
                        </div>
                    </a>

                    {{-- Tombol Loket Panggilan --}}
                    <a href="{{ route('public.queue.index') }}" target="_blank" class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-cyan-500 hover:shadow-md transition flex items-center gap-4">
                        <div class="p-4 bg-cyan-100 text-cyan-600 rounded-xl group-hover:bg-cyan-600 group-hover:text-white transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">Loket Panggil</h3>
                            <p class="text-[10px] text-gray-500">Audio pemanggil antrian.</p>
                        </div>
                    </a>

                    {{-- Tombol Salin Link --}}
                    <div onclick="copyLink('{{ route('public.queue.index') }}')" class="cursor-pointer group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-gray-500 hover:shadow-md transition flex items-center gap-4 active:scale-95">
                        <div class="p-4 bg-gray-100 text-gray-600 rounded-xl group-hover:bg-gray-800 group-hover:text-white transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">Salin Link</h3>
                            <p class="text-[10px] text-gray-500">Klik untuk copy link loket.</p>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- 2. MANAJEMEN DATA & PERSIAPAN --}}
            <div>
                <h3 class="font-bold text-gray-800 text-lg mb-4 flex items-center gap-2">
                    <span class="bg-blue-100 text-blue-600 p-1.5 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    </span>
                    Manajemen & Persiapan
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    
                    {{-- [BARU] MANAJEMEN RUANGAN TES --}}
                    <a href="{{ route('admin.test_rooms.index') }}" class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-yellow-500 hover:shadow-md transition flex items-center gap-4">
                        <div class="p-4 bg-yellow-100 text-yellow-600 rounded-xl group-hover:bg-yellow-600 group-hover:text-white transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">Ruang Tes</h3>
                            <p class="text-[10px] text-gray-500">Atur ruangan & kapasitas.</p>
                        </div>
                    </a>

                    {{-- Bank Soal --}}
                    <a href="{{ route('admin.interview.questions.index') }}" class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-purple-500 hover:shadow-md transition flex items-center gap-4">
                        <div class="p-4 bg-purple-100 text-purple-600 rounded-xl group-hover:bg-purple-600 group-hover:text-white transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">Bank Soal</h3>
                            <p class="text-[10px] text-gray-500">Atur pertanyaan tes.</p>
                        </div>
                    </a>

                    {{-- Sesi Panitia --}}
                    <a href="{{ route('admin.interview.sessions.index') }}" class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-blue-500 hover:shadow-md transition flex items-center gap-4">
                        <div class="p-4 bg-blue-100 text-blue-600 rounded-xl group-hover:bg-blue-600 group-hover:text-white transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">Sesi Panitia</h3>
                            <p class="text-[10px] text-gray-500">QR akses pewawancara.</p>
                        </div>
                    </a>

                    {{-- QR Santri --}}
                    <div class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-orange-500 hover:shadow-md transition flex items-center gap-4 cursor-pointer" 
                         onclick="window.open('https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ route('interview.santri.login') }}', '_blank')">
                        <div class="p-4 bg-orange-100 text-orange-600 rounded-xl group-hover:bg-orange-600 group-hover:text-white transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">QR Santri</h3>
                            <p class="text-[10px] text-gray-500">Cetak QR login siswa.</p>
                        </div>
                    </div>

                    {{-- Export Data --}}
                    <a href="{{ route('admin.interview.export') }}" class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-emerald-500 hover:shadow-md transition flex items-center gap-4">
                        <div class="p-4 bg-emerald-100 text-emerald-600 rounded-xl group-hover:bg-emerald-600 group-hover:text-white transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">Export Data</h3>
                            <p class="text-[10px] text-gray-500">Unduh rekap Excel.</p>
                        </div>
                    </a>
                </div>
            </div>

            {{-- 3. KPI & GRAFIK --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- Statistik Progress --}}
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 lg:col-span-2">
                    <h3 class="font-bold text-gray-800 mb-6">Progres Seleksi</h3>
                    
                    <div class="grid grid-cols-3 gap-4 mb-8">
                        <div class="text-center p-4 bg-gray-50 rounded-xl border border-gray-100">
                            <p class="text-3xl font-black text-gray-800">{{ $totalPeserta }}</p>
                            <p class="text-[10px] text-gray-500 font-bold uppercase mt-1 tracking-wider">Total Peserta</p>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-xl border border-green-100">
                            <p class="text-3xl font-black text-green-600">{{ $sudahWawancara }}</p>
                            <p class="text-[10px] text-green-600 font-bold uppercase mt-1 tracking-wider">Selesai</p>
                        </div>
                        <div class="text-center p-4 bg-red-50 rounded-xl border border-red-100">
                            <p class="text-3xl font-black text-red-600">{{ $belumWawancara }}</p>
                            <p class="text-[10px] text-red-600 font-bold uppercase mt-1 tracking-wider">Belum Tes</p>
                        </div>
                    </div>

                    {{-- Progress Bar --}}
                    <div>
                        <div class="flex justify-between text-sm font-bold text-gray-600 mb-2">
                            <span>Kelengkapan Data Masuk</span>
                            <span>{{ $progress }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-3 rounded-full transition-all duration-1000 shadow-sm" style="width: {{ $progress }}%"></div>
                        </div>
                    </div>
                </div>

                {{-- Grafik Analisis --}}
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-center">
                    @if(!empty($chartData))
                        <div class="mb-2">
                            <h3 class="font-bold text-gray-800 text-sm">Analisis Jawaban</h3>
                            <p class="text-[10px] text-gray-400 truncate">{{ $chartData['question'] }}</p>
                        </div>
                        <div id="chart-jawaban" class="w-full h-48"></div>
                    @else
                        <div class="text-center text-gray-400">
                            <p class="text-sm">Belum ada data grafik.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 4. TABEL MONITORING --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-gray-800">Monitoring Peserta Seleksi</h3>
                        <p class="text-xs text-gray-500 mt-1">Daftar calon santri yang berhak mengikuti tes & wawancara.</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-3">Nama Santri</th>
                                <th class="px-6 py-3">Jenjang</th>
                                <th class="px-6 py-3 text-center">Asesmen Santri</th>
                                <th class="px-6 py-3 text-center">Wawancara Wali</th>
                                <th class="px-6 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($candidates as $c)
                                @php
                                    $hasSantriAnswer = $c->interview_answers->contains(fn($a) => $a->question->target == 'Santri');
                                    $hasWaliAnswer = $c->interview_answers->contains(fn($a) => $a->question->target == 'Wali');
                                @endphp
                                <tr class="hover:bg-blue-50/50 transition duration-150">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-800">{{ $c->nama_lengkap }}</div>
                                        <div class="text-xs text-gray-400 mt-0.5">{{ $c->no_daftar }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-bold">{{ $c->jenjang }}</span>
                                    </td>
                                    
                                    {{-- Status Asesmen Santri --}}
                                    <td class="px-6 py-4 text-center">
                                        @if($hasSantriAnswer)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold border border-emerald-200">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                Selesai
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-slate-100 text-slate-400 text-[10px] font-bold">
                                                Belum
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Status Wawancara Wali --}}
                                    <td class="px-6 py-4 text-center">
                                        @if($hasWaliAnswer)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-purple-100 text-purple-700 text-[10px] font-bold border border-purple-200">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                Selesai
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-slate-100 text-slate-400 text-[10px] font-bold">
                                                Belum
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('admin.interview.result', $c->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-blue-200 text-blue-600 hover:bg-blue-600 hover:text-white text-xs font-bold rounded-lg transition shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                {{-- Pagination --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    {{ $candidates->links() }}
                </div>
            </div>

        </div>
    </div>

    {{-- Script ApexCharts & Copy Link --}}
    <script>
        function copyLink(url) {
            navigator.clipboard.writeText(url).then(() => {
                alert('Link Loket Panggilan berhasil disalin! Kirimkan ke Panitia Pemberkasan.');
            });
        }

        @if(!empty($chartData))
        document.addEventListener('DOMContentLoaded', function () {
            var options = {
                series: [{ name: 'Jumlah', data: @json($chartData['series']) }],
                chart: { type: 'bar', height: 200, toolbar: { show: false }, fontFamily: 'Figtree, sans-serif' },
                plotOptions: { bar: { borderRadius: 4, horizontal: true, distributed: true, barHeight: '70%' } },
                colors: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                dataLabels: { enabled: true, style: { fontSize: '10px' } },
                xaxis: { categories: @json($chartData['labels']) },
                grid: { show: false },
                legend: { show: false }
            };
            var chart = new ApexCharts(document.querySelector("#chart-jawaban"), options);
            chart.render();
        });
        @endif
    </script>
</x-app-layout>