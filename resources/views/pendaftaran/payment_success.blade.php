<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pembayaran Terkirim</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-3xl shadow-xl max-w-md w-full text-center border border-slate-100">
        <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <h2 class="text-2xl font-extrabold text-slate-800 mb-2">Bukti Terkirim!</h2>
        <p class="text-slate-500 text-sm leading-relaxed mb-8">
            Terima kasih. Admin kami akan memverifikasi pembayaran Anda. <br>
            Mohon tunggu notifikasi selanjutnya melalui <strong>WhatsApp</strong> untuk mendapatkan link pengisian biodata.
        </p>
        <a href="{{ url('/') }}" class="inline-block w-full py-3 bg-slate-900 text-white font-bold rounded-xl hover:bg-slate-800 transition">Kembali ke Beranda</a>
    </div>
</body>
</html>