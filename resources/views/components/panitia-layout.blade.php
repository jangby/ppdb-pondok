<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - Area Panitia</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="font-sans antialiased bg-gray-100">
        <div class="min-h-screen">
            
            {{-- NAVBAR SEDERHANA KHUSUS PANITIA --}}
            <nav class="bg-white border-b border-gray-100 shadow-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <div class="shrink-0 flex items-center">
                                <span class="font-bold text-indigo-600 text-xl tracking-tight">
                                    PPDB PANITIA
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <div class="px-3 py-1 text-xs font-bold text-indigo-600 bg-indigo-50 rounded-full border border-indigo-100">
                                Mode Pewawancara
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            {{-- HEADER --}}
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            {{-- KONTEN UTAMA --}}
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>