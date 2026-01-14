<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Data Pendaftar Santri Baru') }}
            </h2>
            <a href="{{ route('admin.candidates.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md font-bold text-sm hover:bg-indigo-700 transition shadow">
                + Tambah Santri Offline
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm font-light">
                            <thead class="border-b bg-gray-100 font-medium">
                                <tr>
                                    <th class="px-6 py-4">No. Daftar</th>
                                    <th class="px-6 py-4">Nama Lengkap</th>
                                    <th class="px-6 py-4">Jenjang</th>
                                    <th class="px-6 py-4">Status Seleksi</th>
                                    <th class="px-6 py-4">Tagihan</th>
                                    <th class="px-6 py-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($candidates as $candidate)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="whitespace-nowrap px-6 py-4 font-bold">{{ $candidate->no_daftar }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        {{ $candidate->nama_lengkap }} <br>
                                        <span class="text-xs text-gray-500">{{ $candidate->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="px-2 py-1 rounded text-white text-xs {{ $candidate->jenjang == 'SMK' ? 'bg-blue-500' : 'bg-green-500' }}">
                                            {{ $candidate->jenjang }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        @if($candidate->status_seleksi == 'Pending')
                                            <span class="text-yellow-600 font-bold">Menunggu</span>
                                        @elseif($candidate->status_seleksi == 'Diterima')
                                            <span class="text-green-600 font-bold">Diterima</span>
                                        @else
                                            <span class="text-red-600 font-bold">Ditolak</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        @php 
                                            $total = $candidate->bills->sum('nominal_tagihan');
                                            $bayar = $candidate->bills->sum('nominal_terbayar');
                                            $lunas = $total > 0 && $total == $bayar;
                                        @endphp

                                        @if($lunas)
                                            <span class="text-green-600 font-bold">LUNAS</span>
                                        @else
                                            <span class="text-red-500 text-xs">Belum Lunas</span><br>
                                            Rp {{ number_format($total - $bayar, 0, ',', '.') }}
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <a href="{{ route('admin.candidates.show', $candidate->id) }}" class="bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700 text-xs">
                                            Detail & Bayar
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $candidates->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>