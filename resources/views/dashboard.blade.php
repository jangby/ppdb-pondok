<x-app-layout>
    {{-- Header Slot --}}
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ __('Dashboard Overview') }}
            </h2>
            
            {{-- Status Indikator PPDB --}}
            <div class="flex items-center bg-white px-4 py-2 rounded-full shadow-sm border border-gray-100">
                <span class="text-sm font-medium text-gray-500 mr-2">Status PPDB:</span>
                @if($ppdbStatus == 'buka')
                    <span class="flex items-center text-green-600 font-bold text-sm">
                        <span class="w-3 h-3 bg-green-500 rounded-full animate-pulse mr-2"></span> DIBUKA
                    </span>
                @else
                    <span class="flex items-center text-red-600 font-bold text-sm">
                        <span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span> DITUTUP
                    </span>
                @endif
            </div>
        </div>
    </x-slot>

    {{-- Main Content --}}
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- 1. WELCOME BANNER --}}
            <div class="relative bg-gradient-to-r from-green-800 to-green-600 rounded-2xl p-6 md:p-10 shadow-xl overflow-hidden text-white flex flex-col md:flex-row items-center justify-between">
                <div class="relative z-10 max-w-lg">
                    <h3 class="text-3xl font-bold mb-2">Ahlan Wa Sahlan, {{ Auth::user()->name }}! 👋</h3>
                    <p class="text-green-100 mb-6 text-lg">
                        Selamat datang di Panel Admin PPDB Ponpes Assa'adah. Hari ini ada <span class="font-bold text-yellow-300">{{ count($terbaru) }} pendaftar baru</span> yang perlu dicek.
                    </p>
                    <a href="{{ route('admin.candidates.index') }}" class="inline-block px-6 py-3 bg-white text-green-800 font-bold rounded-lg shadow hover:bg-gray-100 transition transform hover:-translate-y-1">
                        Kelola Data Santri
                    </a>
                </div>
                {{-- Decorative Illustration (SVG) --}}
                <div class="absolute right-0 bottom-0 opacity-20 md:opacity-100 md:relative w-48 h-48 md:w-64 md:h-64 mr-[-20px] mb-[-20px] md:mr-0 md:mb-0">
                   <img src="https://cdn-icons-png.flaticon.com/512/3063/3063065.png" alt="Admin Illustration" class="w-full h-full object-contain filter drop-shadow-lg">
                </div>
            </div>

            {{-- 2. STATS CARDS --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-start justify-between hover:shadow-md transition">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Total Pendaftar</p>
                        <h4 class="text-3xl font-bold text-gray-800">{{ $totalSantri }}</h4>
                    </div>
                    <div class="p-3 bg-blue-50 text-blue-600 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-start justify-between hover:shadow-md transition">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Perlu Verifikasi</p>
                        <h4 class="text-3xl font-bold text-yellow-600">{{ $totalPending }}</h4>
                    </div>
                    <div class="p-3 bg-yellow-50 text-yellow-600 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-start justify-between hover:shadow-md transition">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Santri Diterima</p>
                        <h4 class="text-3xl font-bold text-green-600">{{ $totalLulus }}</h4>
                    </div>
                    <div class="p-3 bg-green-50 text-green-600 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-start justify-between hover:shadow-md transition">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Estimasi Masuk</p>
                        <h4 class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h4>
                    </div>
                    <div class="p-3 bg-purple-50 text-purple-600 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>

            {{-- 3. CHARTS SECTION --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 lg:col-span-2">
                    <h3 class="font-bold text-gray-800 mb-4">Tren Pendaftaran (7 Hari Terakhir)</h3>
                    <div id="chartPendaftaran"></div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4">Statistik Gender</h3>
                    <div id="chartGender" class="flex justify-center"></div>
                    <div class="mt-4 text-center text-sm text-gray-500">
                        <p>Total Laki-laki: <span class="font-bold text-gray-800">{{ $genderL }}</span></p>
                        <p>Total Perempuan: <span class="font-bold text-gray-800">{{ $genderP }}</span></p>
                    </div>
                </div>
            </div>

            {{-- 4. TABLE SECTION (Terbaru) --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="font-bold text-gray-800">Pendaftar Terbaru</h3>
                    <a href="{{ route('admin.candidates.index') }}" class="text-sm text-green-600 hover:text-green-700 font-medium">Lihat Semua &rarr;</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-gray-500 text-sm uppercase tracking-wider">
                                <th class="px-6 py-4 font-medium bg-white">Tanggal</th>
                                <th class="px-6 py-4 font-medium bg-white">Nama Santri</th>
                                <th class="px-6 py-4 font-medium bg-white">Jenjang</th>
                                <th class="px-6 py-4 font-medium bg-white">Asal Sekolah</th>
                                <th class="px-6 py-4 font-medium bg-white">Status</th>
                                <th class="px-6 py-4 font-medium bg-white text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm">
                            @forelse($terbaru as $item)
                            <tr class="border-t border-gray-50 hover:bg-gray-50 transition">
                                <td class="px-6 py-4">{{ $item->created_at->format('d M Y') }}</td>
                                <td class="px-6 py-4 font-semibold text-gray-900">{{ $item->nama_lengkap }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded text-xs font-bold 
                                        {{ $item->jenjang == 'SD' ? 'bg-yellow-100 text-yellow-700' : 
                                          ($item->jenjang == 'SMP' ? 'bg-blue-100 text-blue-700' : 
                                          'bg-gray-100 text-gray-700') }}">
                                        {{ $item->jenjang }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">{{ Str::limit($item->asal_sekolah, 20) }}</td>
                                <td class="px-6 py-4">
                                    @if($item->status_seleksi == 'Pending')
                                        <span class="px-3 py-1 bg-yellow-100 text-yellow-600 rounded-full text-xs font-bold">Pending</span>
                                    @elseif($item->status_seleksi == 'Lulus')
                                        <span class="px-3 py-1 bg-green-100 text-green-600 rounded-full text-xs font-bold">Diterima</span>
                                    @else
                                        <span class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-xs font-bold">Ditolak</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.candidates.show', $item->id) }}" class="text-indigo-600 hover:text-indigo-800 font-bold">Detail</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-400">Belum ada data pendaftar.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- SCRIPTS FOR CHARTS --}}
    {{-- Kita inject script ini di bawah --}}
    @push('scripts')
    {{-- Load ApexCharts dari CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        // 1. CHART PENDAFTARAN (Area Chart)
        var optionsArea = {
            series: [{
                name: 'Pendaftar Baru',
                data: @json($chartTotals) // Data dari Controller
            }],
            chart: {
                height: 300,
                type: 'area',
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif'
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            xaxis: {
                categories: @json($chartDates), // Tanggal dari Controller
                labels: { style: { colors: '#9ca3af', fontSize: '12px' } }
            },
            yaxis: {
                labels: { style: { colors: '#9ca3af', fontSize: '12px' } }
            },
            colors: ['#059669'], // Warna Hijau Green-600
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.2,
                    stops: [0, 90, 100]
                }
            },
            grid: { borderColor: '#f3f4f6' },
            tooltip: { theme: 'light' }
        };

        var chartArea = new ApexCharts(document.querySelector("#chartPendaftaran"), optionsArea);
        chartArea.render();

        // 2. CHART GENDER (Donut Chart)
        var optionsDonut = {
            series: [{{ $genderL }}, {{ $genderP }}], // Data [L, P]
            labels: ['Laki-laki', 'Perempuan'],
            chart: {
                type: 'donut',
                height: 280,
                fontFamily: 'Inter, sans-serif'
            },
            colors: ['#3b82f6', '#ec4899'], // Biru & Pink
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total',
                                fontSize: '14px',
                                color: '#6b7280'
                            }
                        }
                    }
                }
            },
            dataLabels: { enabled: false },
            legend: { position: 'bottom' },
            responsive: [{
                breakpoint: 480,
                options: { chart: { width: 200 } }
            }]
        };

        var chartDonut = new ApexCharts(document.querySelector("#chartGender"), optionsDonut);
        chartDonut.render();
    </script>
    @endpush

</x-app-layout>