<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold mb-4">Verifikasi Berkas Masuk</h2>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="p-4">No WA</th>
                            <th class="p-4">Waktu Upload</th>
                            <th class="p-4">Berkas</th>
                            <th class="p-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($verifications as $v)
                        <tr class="border-t">
                            <td class="p-4">{{ $v->no_wa }}</td>
                            <td class="p-4">{{ $v->created_at->diffForHumans() }}</td>
                            <td class="p-4">
                                <a href="{{ asset('storage/'.$v->file_perjanjian) }}" target="_blank" class="text-blue-600 underline">Lihat PDF</a>
                            </td>
                            <td class="p-4 flex gap-2">
                                <form action="{{ route('admin.verifications.approve', $v->id) }}" method="POST">
                                    @csrf
                                    <button class="bg-green-600 text-white px-4 py-1 rounded hover:bg-green-700">ACC & Kirim WA</button>
                                </form>
                                <form action="{{ route('admin.verifications.reject', $v->id) }}" method="POST">
                                    @csrf
                                    <button class="bg-red-600 text-white px-4 py-1 rounded hover:bg-red-700">Tolak</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>