<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-indigo-600 rounded-lg shadow-lg text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            </div>
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Profil Akun Saya') }}
                </h2>
                <p class="text-xs text-gray-500">Kelola informasi profil, keamanan password, dan pengaturan akun.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- 1. UPDATE PROFILE INFO --}}
                <div class="p-6 sm:p-8 bg-white shadow-sm sm:rounded-2xl border border-gray-100 relative overflow-hidden group hover:shadow-md transition duration-300">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition">
                        <svg class="w-24 h-24 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0c0 .667.333 1 1 1v1m2-2c0 .667-.333 1-1 1v1"></path></svg>
                    </div>

                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800">Informasi Pribadi</h3>
                        </div>
                        
                        <div class="max-w-xl">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>
                </div>

                {{-- 2. UPDATE PASSWORD --}}
                <div class="p-6 sm:p-8 bg-white shadow-sm sm:rounded-2xl border border-gray-100 relative overflow-hidden group hover:shadow-md transition duration-300">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition">
                        <svg class="w-24 h-24 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>

                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800">Keamanan Password</h3>
                        </div>

                        <div class="max-w-xl">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>
                </div>

            </div>

            {{-- 3. DELETE ACCOUNT (Danger Zone) --}}
            <div class="p-6 sm:p-8 bg-red-50 shadow-sm sm:rounded-2xl border border-red-100 relative overflow-hidden">
                <div class="flex flex-col md:flex-row md:items-start gap-6">
                    <div class="p-3 bg-white text-red-500 rounded-full shadow-sm shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-red-800 mb-1">Zona Berbahaya</h3>
                        <p class="text-sm text-red-600 mb-4">Menghapus akun akan menghilangkan semua data secara permanen dan tidak dapat dikembalikan.</p>
                        
                        <div class="max-w-xl bg-white p-4 rounded-xl border border-red-200">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>