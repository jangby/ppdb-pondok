<x-app-layout>
    <div class="bg-slate-50 min-h-screen pb-12 font-sans">
        
        <div class="bg-gradient-to-r from-blue-700 to-blue-900 pb-20 pt-8 px-6 rounded-b-[2.5rem] shadow-md">
            <div class="max-w-md mx-auto">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-extrabold text-white tracking-tight">Dashboard Eksekutif</h1>
                        <p class="text-blue-100 text-sm mt-1 opacity-90">Pantauan Pendaftaran Real-time</p>
                    </div>
                    <div class="bg-white/20 p-2 rounded-xl backdrop-blur-sm">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-md mx-auto -mt-12 px-4 relative z-10">
            
            <div class="grid grid-cols-2 gap-4 mb-8">
                @foreach($kpi as $jenjang => $stat)
                <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 relative overflow-hidden group hover:shadow-md transition-shadow">
                    
                    <div class="absolute -right-4 -top-4 w-16 h-16 bg-blue-50 rounded-full opacity-60"></div>
                    
                    <h3 class="text-[13px] font-bold text-slate-500 uppercase tracking-wider mb-1 relative z-10">
                        Total {{ $jenjang }}
                    </h3>
                    
                    <p class="text-3xl font-extrabold text-blue-700 relative z-10">
                        {{ $stat['total'] }} <span class="text-xs font-semibold text-slate-400 normal-case tracking-normal">Santri</span>
                    </p>
                    
                    <div class="mt-3 pt-3 border-t border-slate-100 flex justify-between text-xs font-bold relative z-10">
                        <span class="flex items-center text-blue-600 bg-blue-50 px-2 py-1.5 rounded-lg w-full mr-1 justify-center">
                            L : {{ $stat['laki_laki'] }}
                        </span>
                        <span class="flex items-center text-rose-500 bg-rose-50 px-2 py-1.5 rounded-lg w-full ml-1 justify-center">
                            P : {{ $stat['perempuan'] }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>

            @foreach($dataPerJenjang as $jenjang => $santris)
            <div class="mb-8">
                
                <div class="flex items-center justify-between mb-4 px-1">
                    <h2 class="text-base font-extrabold text-slate-800">Pendaftar {{ $jenjang }}</h2>
                    <span class="text-xs font-bold text-blue-700 bg-blue-100 px-3 py-1 rounded-full">
                        {{ $santris->count() }} Data
                    </span>
                </div>

                <div class="space-y-3">
                    @foreach($santris as $santri)
                    <a href="{{ route('monitor.santri.detail', $santri->id) }}" class="flex items-center bg-white rounded-2xl p-3 shadow-sm border border-slate-100 hover:shadow-md hover:border-blue-200 transition-all duration-200 active:scale-[0.98]">
                        
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 text-white flex-shrink-0 flex items-center justify-center font-bold text-lg shadow-sm">
                            {{ strtoupper(substr($santri->nama_lengkap, 0, 1)) }}
                        </div>
                        
                        <div class="ml-4 flex-1 overflow-hidden">
                            <h4 class="font-bold text-slate-900 text-[14px] truncate">{{ $santri->nama_lengkap }}</h4>
                            <p class="text-[12px] text-slate-500 mt-0.5 truncate font-medium">
                                {{ $santri->asal_sekolah ?? 'Belum input asal sekolah' }}
                            </p>
                            
                            <p class="text-[11px] text-slate-400 mt-1.5 flex items-center truncate">
                                <svg class="w-3 h-3 mr-1 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span class="truncate">{{ $santri->address->kecamatan ?? '-' }}, {{ $santri->address->kabupaten ?? '-' }}</span>
                            </p>
                        </div>
                        
                        <div class="ml-2 text-slate-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </div>
                        
                    </a>
                    @endforeach
                </div>
            </div>
            @endforeach
            
        </div>
    </div>
</x-app-layout>