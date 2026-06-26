<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Manajemen Ruangan Tes</h2>
    </x-slot>

    <div class="py-12 px-4 max-w-7xl mx-auto space-y-6">
        
        {{-- Flash Message --}}
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(session('warning'))
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded shadow-sm">
                {{ session('warning') }}
            </div>
        @endif

        {{-- Form Tambah Ruangan --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Tambah Ruangan Baru
            </h3>
            <form action="{{ route('admin.test_rooms.store') }}" method="POST">
                @csrf
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-grow">
                        <input type="text" name="nama_ruangan" placeholder="Nama Ruangan (Cth: Kelas 1A, Lab Komputer)" class="rounded-lg border-gray-300 w-full focus:ring-blue-500 focus:border-blue-500" required>
                    </div>

                    {{-- UPDATE: Pilihan Jenis diperbarui --}}
                    <div class="w-full md:w-56">
                        <select name="jenis" class="rounded-lg border-gray-300 w-full focus:ring-blue-500 focus:border-blue-500 cursor-pointer" required>
                            <option value="">-- Pilih Peruntukan --</option>
                            <option value="Santri">Calon Santri (Campur)</option>
                            <option value="Wali">Wali Santri</option>
                        </select>
                    </div>

                    <div class="w-full md:w-64">
                        <input type="text" name="lokasi" placeholder="Lokasi (Cth: Gedung B Lt. 2)" class="rounded-lg border-gray-300 w-full focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="w-full md:w-32">
                        <input type="number" name="kapasitas" placeholder="Kapasitas" class="rounded-lg border-gray-300 w-full focus:ring-blue-500 focus:border-blue-500 text-center" value="20" min="1" required>
                    </div>

                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition shadow-sm whitespace-nowrap">
                        Simpan
                    </button>
                </div>
            </form>
        </div>

        {{-- Tombol Distribusi --}}
        <div class="flex justify-between items-center">
            <p class="text-sm text-gray-500">
                Total Ruangan: <span class="font-bold text-gray-800">{{ $rooms->count() }}</span>
            </p>
            <form action="{{ route('admin.test_rooms.distribute') }}" method="POST" onsubmit="return confirm('Yakin ingin membagikan ruangan? Santri (Putra & Putri) akan masuk ke ruang Santri, dan Orangtua ke ruang Wali.');">
                @csrf
                <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-purple-700 transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                    Distribusi Otomatis
                </button>
            </form>
        </div>

        {{-- Tabel Daftar Ruangan --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 uppercase font-bold border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 w-10">No</th>
                            <th class="px-6 py-3">Nama Ruangan</th>
                            <th class="px-6 py-3">Peruntukan</th>
                            <th class="px-6 py-3">Lokasi</th>
                            <th class="px-6 py-3 text-center">Kapasitas</th>
                            <th class="px-6 py-3 text-center">Terisi</th>
                            <th class="px-6 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($rooms as $index => $room)
                        <tr class="hover:bg-blue-50/30 transition duration-150">
                            <td class="px-6 py-4 text-center text-gray-400">{{ $index + 1 }}</td>
                            
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800 text-base">{{ $room->nama_ruangan }}</div>
                            </td>

                            {{-- UPDATE: Tampilan Label --}}
                            <td class="px-6 py-4">
                                @if($room->jenis == 'Santri')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700 border border-blue-200">
                                        Calon Santri
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-700 border border-purple-200">
                                        Wali Santri
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-gray-600">{{ $room->lokasi ?? '-' }}</td>
                            <td class="px-6 py-4 text-center font-mono text-gray-500">{{ $room->kapasitas }}</td>

                            <td class="px-6 py-4 text-center">
                                @php
                                    $terisi = (str_contains($room->jenis, 'Santri')) ? $room->candidates_santri_count : $room->candidates_wali_count;
                                    $persen = ($room->kapasitas > 0) ? round(($terisi / $room->kapasitas) * 100) : 0;
                                    $color = $persen >= 100 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700';
                                @endphp
                                <span class="{{ $color }} px-3 py-1 rounded-full font-bold text-xs border border-opacity-20 shadow-sm">
                                    {{ $terisi }} Orang
                                </span>
                            </td>

                            <td class="px-6 py-4 flex justify-center gap-3">
                                <a href="{{ route('admin.test_rooms.print', $room->id) }}" target="_blank" class="text-gray-500 hover:text-blue-600 transition" title="Cetak">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                </a>
                                <form action="{{ route('admin.test_rooms.destroy', $room->id) }}" method="POST" onsubmit="return confirm('Hapus ruangan ini?');">
                                    @csrf @method('DELETE')
                                    <button class="text-gray-400 hover:text-red-600 transition" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">Belum ada ruangan tes.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>