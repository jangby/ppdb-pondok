<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- ApexCharts CDN (Untuk Dashboard) --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>
<body class="font-sans antialiased bg-gray-50" x-data="{ sidebarOpen: false, sidebarExpanded: true }">
    
    <div class="flex h-screen overflow-hidden">
        
        {{-- INCLUDE SIDEBAR --}}
        @include('layouts.navigation')

        {{-- MAIN CONTENT WRAPPER --}}
        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">
            
            {{-- TOP BAR (Navbar Atas: Toggle & Profil) --}}
            <header class="bg-white shadow-sm sticky top-0 z-30">
                <div class="flex items-center justify-between px-6 py-3">
                    
                    {{-- Tombol Toggle Sidebar --}}
                    <div class="flex items-center gap-4">
                        <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 focus:outline-none lg:hidden">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        <button @click="sidebarExpanded = !sidebarExpanded" class="hidden lg:block text-gray-500 focus:outline-none hover:text-gray-700 transition">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                            </svg>
                        </button>
                    </div>

                    {{-- User Dropdown --}}
                    <div class="flex items-center">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                    <div>{{ Auth::user()->name }}</div>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.edit')">
                                    {{ __('Profile') }}
                                </x-dropdown-link>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')"
                                            onclick="event.preventDefault(); this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </div>
            </header>

            {{-- [PERBAIKAN] PAGE HEADER (Slot Header untuk Judul & Tombol Halaman) --}}
            @if (isset($header))
                <div class="bg-white border-b border-gray-200">
                    <div class="px-6 py-4">
                        {{ $header }}
                    </div>
                </div>
            @endif

            {{-- PAGE CONTENT (Slot Utama) --}}
            <main class="w-full grow p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

</body>
</html>