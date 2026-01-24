<div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak
    class="fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-sm lg:hidden transition-opacity"></div>

<aside :class="sidebarExpanded ? 'w-64' : 'w-20'"
       class="fixed inset-y-0 left-0 z-50 flex flex-col bg-slate-900 text-white transition-all duration-300 transform lg:translate-x-0 lg:static lg:inset-0 shadow-xl"
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

    {{-- HEADER SIDEBAR --}}
    <div class="flex items-center justify-center h-16 bg-slate-800 shadow-md relative">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <div class="bg-blue-600 p-1.5 rounded-lg">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            <span class="font-bold text-lg tracking-wide transition-opacity duration-300"
                  :class="sidebarExpanded ? 'opacity-100' : 'opacity-0 hidden'">
                PPDB App
            </span>
        </a>
        <button @click="sidebarOpen = false" class="absolute right-4 text-gray-400 hover:text-white lg:hidden">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
    </div>

    {{-- MENU LIST --}}
    <div class="flex-1 overflow-y-auto py-4 custom-scrollbar">
        <nav class="space-y-1 px-3">
            
            {{-- DASHBOARD --}}
            <x-nav-link-sidebar :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="home">
                {{ __('Dashboard') }}
            </x-nav-link-sidebar>

            <div class="my-4 border-t border-slate-700 mx-2"></div>
            
            {{-- GROUP: MANAJEMEN UTAMA --}}
            <p class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2 transition-all duration-300"
               :class="sidebarExpanded ? 'block' : 'hidden'">
                Manajemen
            </p>

            <x-nav-link-sidebar :href="route('admin.verifications.index')" :active="request()->routeIs('admin.verifications.*')" icon="verify">
                {{ __('Verifikasi Berkas') }}
                
                {{-- Badge Notification --}}
                @php $pendingCount = \App\Models\Verification::where('status', 'pending')->count(); @endphp
                @if($pendingCount > 0)
                    <span class="ml-auto bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm"
                          :class="sidebarExpanded ? 'block' : 'hidden'">
                        {{ $pendingCount }}
                    </span>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"
                          :class="sidebarExpanded ? 'hidden' : 'block'"></span>
                @endif
            </x-nav-link-sidebar>

            <x-nav-link-sidebar :href="route('admin.candidates.index')" :active="request()->routeIs('admin.candidates.*')" icon="users">
                {{ __('Data Santri') }}
            </x-nav-link-sidebar>

            <x-nav-link-sidebar :href="route('admin.finance.index')" :active="request()->routeIs('admin.finance.*')" icon="cash">
                {{ __('Keuangan') }}
            </x-nav-link-sidebar>

            <div class="my-4 border-t border-slate-700 mx-2"></div>

            {{-- GROUP: SELEKSI & UJIAN (BARU) --}}
            <p class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2 transition-all duration-300"
               :class="sidebarExpanded ? 'block' : 'hidden'">
                Seleksi & Ujian
            </p>

            {{-- Menu Utama Wawancara (Dashboard Wawancara) --}}
            {{-- Pastikan route ini nanti kita buat --}}
            <x-nav-link-sidebar :href="url('admin/interview/dashboard')" :active="request()->is('admin/interview*')" icon="clipboard">
                {{ __('Seleksi Tes') }}
            </x-nav-link-sidebar>

            <div class="my-4 border-t border-slate-700 mx-2"></div>

            {{-- GROUP: PENGATURAN --}}
            <p class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2 transition-all duration-300"
               :class="sidebarExpanded ? 'block' : 'hidden'">
                Konfigurasi
            </p>

            <x-nav-link-sidebar :href="route('admin.settings.index')" :active="request()->routeIs('admin.settings.*')" icon="settings">
                {{ __('Pengaturan PPDB') }}
            </x-nav-link-sidebar>

            <x-nav-link-sidebar :href="route('admin.payment_types.index')" :active="request()->routeIs('admin.payment_types.*')" icon="tag">
                {{ __('Jenis Pembayaran') }}
            </x-nav-link-sidebar>

            <div class="my-4 border-t border-slate-700 mx-2"></div>

            <x-nav-link-sidebar :href="route('profile.edit')" :active="request()->routeIs('profile.edit')" icon="user-circle">
                {{ __('Akun Saya') }}
            </x-nav-link-sidebar>

        </nav>
    </div>

    {{-- FOOTER LOGOUT --}}
    <div class="p-4 bg-slate-900 border-t border-slate-800">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center gap-3 text-slate-400 hover:text-white transition w-full group p-2 rounded-lg hover:bg-slate-800">
                <svg class="w-5 h-5 flex-shrink-0 group-hover:text-red-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                <span :class="sidebarExpanded ? 'opacity-100' : 'opacity-0 hidden'" class="font-medium text-sm whitespace-nowrap transition-all duration-300">
                    Keluar Aplikasi
                </span>
            </button>
        </form>
    </div>
</aside>