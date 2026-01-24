<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>Selesai</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-white min-h-screen flex items-center justify-center p-6 text-center">

    <div>
        <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 animate-bounce">
            <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        </div>
        
        <h1 class="text-2xl font-bold text-slate-900 mb-2">Terima Kasih!</h1>
        <p class="text-slate-500 mb-8 max-w-xs mx-auto leading-relaxed">
            Jawaban asesmen kamu sudah tersimpan. Silakan kembali ke panitia untuk tahapan selanjutnya.
        </p>

        <a href="{{ route('interview.santri.login') }}" class="px-6 py-3 bg-slate-100 text-slate-600 font-bold rounded-xl hover:bg-slate-200 transition text-sm">
            Kembali ke Halaman Utama
        </a>
    </div>

</body>
</html>