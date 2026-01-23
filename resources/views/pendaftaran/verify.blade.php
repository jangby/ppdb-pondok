<x-guest-layout>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-2xl shadow-xl">
            <div class="text-center">
                <h2 class="text-2xl font-extrabold text-gray-900">Tahap 1: Verifikasi Berkas</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Silakan download surat perjanjian, tanda tangani, lalu upload kembali.
                </p>
            </div>

            {{-- Tombol Download Template --}}
            @if($template)
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 text-center">
                <p class="text-xs text-blue-600 mb-2 font-bold">Langkah 1: Download Template</p>
                <a href="{{ asset('storage/'.$template) }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Download Surat Perjanjian
                </a>
            </div>
            @endif

            <form class="mt-8 space-y-6" action="{{ route('pendaftaran.verify.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="rounded-md shadow-sm -space-y-px">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp Aktif</label>
                        <input name="no_wa" type="number" required class="appearance-none rounded-lg relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm" placeholder="Contoh: 08123456789">
                        <p class="text-xs text-gray-400 mt-1">Link pendaftaran akan dikirim ke nomor ini.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Upload Berkas Bertanda Tangan</label>
                        <input name="berkas" type="file" accept=".pdf,.jpg,.jpeg,.png" required class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                    </div>
                </div>

                <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-xl text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 shadow-lg">
                    Kirim Berkas
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>