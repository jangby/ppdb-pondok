<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator - Ponpes Assa'adah</title>
    
    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    
    {{-- Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">

    <div class="min-h-screen flex">
        
        <div class="hidden md:flex md:w-1/2 bg-green-900 relative justify-center items-center overflow-hidden">
            <div class="absolute inset-0 z-0">
                {{-- Ganti URL ini dengan foto kegiatan santri/masjid --}}
                <img src="https://images.unsplash.com/photo-1564959130747-897fb406b9dc?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Pesantren Background" class="w-full h-full object-cover opacity-40">
            </div>
            
            <div class="relative z-10 p-12 text-white max-w-lg">
                <div class="w-16 h-16 bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center mb-6 border border-white/20">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <h2 class="text-4xl font-bold mb-4">Sistem Administrasi <br> PPDB Online</h2>
                <p class="text-green-100 text-lg leading-relaxed">
                    "Barangsiapa yang menempuh suatu jalan untuk menuntut ilmu, maka Allah akan mudahkan baginya jalan menuju Surga."
                </p>
                <div class="mt-8 flex gap-2">
                    <div class="w-12 h-1 bg-yellow-500 rounded-full"></div>
                    <div class="w-4 h-1 bg-white/50 rounded-full"></div>
                    <div class="w-4 h-1 bg-white/50 rounded-full"></div>
                </div>
            </div>
        </div>

        <div class="w-full md:w-1/2 flex justify-center items-center bg-white p-8">
            <div class="w-full max-w-md">
                
                <div class="md:hidden flex justify-center mb-8">
                    <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center text-white font-bold">A</div>
                </div>

                <div class="mb-8">
                    <h3 class="text-2xl font-bold text-gray-900">Assalamu'alaikum, Admin!</h3>
                    <p class="text-gray-500 mt-2">Silakan masukkan akun Anda untuk mengelola data.</p>
                </div>

                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600 bg-green-50 p-3 rounded-lg border border-green-200">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 bg-red-50 p-3 rounded-lg border border-red-200">
                        <ul class="text-sm text-red-600 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <input id="email" type="email" name="email" :value="old('email')" required autofocus 
                                class="pl-10 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 py-3 transition ease-in-out duration-150 border" placeholder="admin@pesantren.com">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input id="password" type="password" name="password" required autocomplete="current-password"
                                class="pl-10 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 py-3 transition ease-in-out duration-150 border" placeholder="••••••••">
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50" name="remember">
                            <span class="ml-2 text-sm text-gray-600">Ingat Saya</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm text-green-600 hover:text-green-800 font-medium" href="{{ route('password.request') }}">
                                Lupa Password?
                            </a>
                        @endif
                    </div>

                    <div>
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 transform hover:-translate-y-0.5">
                            Masuk ke Dashboard
                        </button>
                    </div>
                </form>

                <div class="mt-8 text-center">
                    <a href="{{ route('home') }}" class="text-sm text-gray-400 hover:text-gray-600 flex items-center justify-center gap-1 transition">
                        &larr; Kembali ke Website Utama
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>