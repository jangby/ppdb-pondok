<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Dashboard Utama') }}
        </h2>
    </x-slot>

    {{-- Load ApexCharts --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            {{-- BAGIAN 1: STATISTIK UTAMA (Hero Cards) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                
                {{-- Card: Total Santri --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300 relative overflow-hidden group">
                    <div class="absolute right-0 top-0 h-24 w-24 bg-blue-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-blue-100 text-blue-600 rounded-xl">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Total Pendaftar</p>
                                <h3 class="text-2xl font-bold text-gray-800">{{ $totalSantri }}</h3>
                            </div>
                        </div>
                        <div class="mt-4 flex gap-2">
                            <span class="text-xs font-semibold text-green-700 bg-green-50 px-2 py-1 rounded-md border border-green-100">
                                {{ $santriLulus }} Lulus
                            </span>
                            <span class="text-xs font-semibold text-blue-700 bg-blue-50 px-2 py-1 rounded-md border border-blue-100">
                                {{ $santriBaru }} Baru
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Card: Pemasukan --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300 relative overflow-hidden group">
                    <div class="absolute right-0 top-0 h-24 w-24 bg-green-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-green-100 text-green-600 rounded-xl">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Pemasukan</p>
                                <h3 class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalPemasukan/1000000, 1, ',', '.') }}jt</h3>
                            </div>
                        </div>
                        <p class="mt-4 text-xs text-green-600 font-medium">+ Realtime dari tagihan</p>
                    </div>
                </div>

                {{-- Card: Pengeluaran --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300 relative overflow-hidden group">
                    <div class="absolute right-0 top-0 h-24 w-24 bg-red-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-red-100 text-red-600 rounded-xl">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Pengeluaran</p>
                                <h3 class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalPengeluaran/1000000, 1, ',', '.') }}jt</h3>
                            </div>
                        </div>
                        <p class="mt-4 text-xs text-red-500 font-medium">Total operasional</p>
                    </div>
                </div>

                {{-- Card: Sisa Saldo --}}
                <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-6 shadow-lg text-white relative overflow-hidden">
                    <div class="absolute right-0 top-0 h-32 w-32 bg-white opacity-5 rounded-full -mr-10 -mt-10"></div>
                    <div class="relative z-10">
                        <p class="text-sm font-medium text-gray-300">Sisa Saldo Kas</p>
                        <h3 class="text-3xl font-bold mt-2">Rp {{ number_format($saldoSaatIni, 0, ',', '.') }}</h3>
                        <div class="mt-4 flex items-center gap-2 text-xs text-gray-400">
                            <span class="w-2 h-2 rounded-full bg-green-400"></span>
                            Kondisi Keuangan Aman
                        </div>
                    </div>
                </div>
            </div>

            {{-- BAGIAN 2: GRAFIK & LIST (Layout Asimetris) --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- GRAFIK (Lebar 2 Kolom) --}}
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="font-bold text-gray-800 text-lg">Arus Kas Bulanan</h3>
                            <p class="text-sm text-gray-500">Perbandingan Pemasukan vs Pengeluaran Tahun {{ date('Y') }}</p>
                        </div>
                        <div class="flex gap-4 text-xs font-medium">
                            <div class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-emerald-500"></span> Masuk</div>
                            <div class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-rose-500"></span> Keluar</div>
                        </div>
                    </div>
                    <div id="financeChart" class="w-full h-80"></div>
                </div>

                {{-- LIST PENDAFTAR TERBARU (Lebar 1 Kolom) --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col h-full">
                    <div class="p-5 border-b border-gray-50 flex justify-between items-center">
                        <h3 class="font-bold text-gray-800">Pendaftar Terbaru</h3>
                        <a href="{{ route('admin.candidates.index') }}" class="text-xs font-bold text-blue-600 hover:bg-blue-50 px-2 py-1 rounded transition">Lihat Semua</a>
                    </div>
                    <div class="flex-1 overflow-y-auto pr-1">
                        <ul class="divide-y divide-gray-50">
                            @forelse($latestCandidates as $candidate)
                            <li class="p-4 hover:bg-gray-50 transition group cursor-default">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-100 to-blue-200 text-blue-700 flex items-center justify-center font-bold text-sm shadow-inner">
                                        {{ substr($candidate->nama_lengkap, 0, 1) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $candidate->nama_lengkap }}</p>
                                        <p class="text-xs text-gray-500">{{ $candidate->jenjang }} • {{ $candidate->created_at->diffForHumans() }}</p>
                                    </div>
                                    <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-full tracking-wide
                                        {{ $candidate->status == 'Lulus' ? 'bg-green-100 text-green-700' : 
                                          ($candidate->status == 'Baru' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600') }}">
                                        {{ $candidate->status ?? 'Baru' }}
                                    </span>
                                </div>
                            </li>
                            @empty
                            <li class="p-8 text-center text-gray-400 text-sm">Belum ada pendaftar.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            {{-- BAGIAN 3: TABEL PENGELUARAN --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-gray-800 text-lg">Pengeluaran Terakhir</h3>
                        <p class="text-xs text-gray-500 mt-1">5 Transaksi pengeluaran operasional terbaru.</p>
                    </div>
                    <a href="{{ route('admin.finance.index') }}" class="px-4 py-2 bg-gray-900 text-white text-xs font-bold rounded-lg hover:bg-gray-800 transition shadow-lg shadow-gray-200">
                        Kelola Keuangan
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50/50 text-gray-500 uppercase text-[10px] tracking-wider font-semibold">
                            <tr>
                                <th class="px-6 py-4 rounded-tl-lg">Tanggal</th>
                                <th class="px-6 py-4">Keperluan</th>
                                <th class="px-6 py-4 text-right">Nominal</th>
                                <th class="px-6 py-4 text-center rounded-tr-lg">Admin</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($latestExpenses as $expense)
                            <tr class="hover:bg-gray-50/80 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500 font-mono text-xs">
                                    {{ \Carbon\Carbon::parse($expense->tanggal)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    {{ $expense->judul_pengeluaran }}
                                </td>
                                <td class="px-6 py-4 text-right text-rose-600 font-bold font-mono">
                                    Rp {{ number_format($expense->total_keluar, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-gray-100 border border-gray-200 text-xs text-gray-600">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        {{ $expense->user->name ?? 'System' }}
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic">
                                    Belum ada data pengeluaran.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- SCRIPT CHART --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var options = {
                series: [{
                    name: 'Pemasukan',
                    data: @json($chartDataIncome)
                }, {
                    name: 'Pengeluaran',
                    data: @json($chartDataExpense)
                }],
                chart: {
                    type: 'area', 
                    height: 320,
                    fontFamily: 'Figtree, sans-serif',
                    toolbar: { show: false },
                    zoom: { enabled: false }
                },
                colors: ['#10B981', '#F43F5E'], // Emerald & Rose
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.4,
                        opacityTo: 0.05,
                        stops: [0, 90, 100]
                    }
                },
                dataLabels: { enabled: false },
                stroke: { 
                    curve: 'smooth', 
                    width: 3,
                    colors: ['#10B981', '#F43F5E']
                },
                xaxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: {
                        style: { colors: '#94a3b8', fontSize: '11px' }
                    }
                },
                yaxis: {
                    labels: {
                        style: { colors: '#94a3b8', fontSize: '11px' },
                        formatter: function (value) {
                            if(value >= 1000000) return (value/1000000).toFixed(1) + "jt";
                            if(value >= 1000) return (value/1000).toFixed(0) + "rb";
                            return value;
                        }
                    }
                },
                grid: {
                    borderColor: '#f1f5f9',
                    strokeDashArray: 4,
                },
                tooltip: {
                    theme: 'light',
                    y: {
                        formatter: function (val) {
                            return "Rp " + new Intl.NumberFormat('id-ID').format(val)
                        }
                    }
                },
                legend: { show: false } // Custom legend di header
            };

            var chart = new ApexCharts(document.querySelector("#financeChart"), options);
            chart.render();
        });
    </script>
</x-app-layout>