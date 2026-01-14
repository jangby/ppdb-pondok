<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ $candidate->nama_lengkap }}
                </h2>
                <p class="text-sm text-gray-500">No. Daftar: <span class="font-mono text-gray-700 font-bold">{{ $candidate->no_daftar }}</span> | Tanggal Daftar: {{ $candidate->created_at->format('d M Y') }}</p>
            </div>
            <a href="{{ route('admin.candidates.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition ease-in-out duration-150">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @php
                $totalTagihan = $candidate->bills->sum('nominal_tagihan');
                $totalTerbayar = $candidate->bills->sum('nominal_terbayar');
                $sisaTagihan = $totalTagihan - $totalTerbayar;
                $persenBayar = $totalTagihan > 0 ? ($totalTerbayar / $totalTagihan) * 100 : 0;
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl p-5 border-b-4 border-blue-500">
                    <div class="text-gray-500 text-xs uppercase font-bold tracking-wider">Total Tagihan</div>
                    <div class="text-2xl font-bold text-gray-800 mt-1">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl p-5 border-b-4 border-green-500">
                    <div class="text-gray-500 text-xs uppercase font-bold tracking-wider">Sudah Dibayar</div>
                    <div class="text-2xl font-bold text-green-600 mt-1">Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</div>
                    <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                        <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ $persenBayar }}%"></div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl p-5 border-b-4 border-red-500">
                    <div class="text-gray-500 text-xs uppercase font-bold tracking-wider">Sisa Kekurangan</div>
                    <div class="text-2xl font-bold text-red-600 mt-1">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="space-y-6">
                    
                    <div class="bg-white shadow-md rounded-xl overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
                            <h3 class="font-bold text-gray-800">Status & Aksi</h3>
                        </div>
                        <div class="p-6">
                            <form action="{{ route('admin.candidates.updateStatus', $candidate->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <label class="block text-sm font-medium text-gray-700 mb-2">Update Status Seleksi</label>
                                <div class="flex gap-2">
                                    <select name="status_seleksi" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="Pending" {{ $candidate->status_seleksi == 'Pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="Diterima" {{ $candidate->status_seleksi == 'Diterima' ? 'selected' : '' }}>Diterima</option>
                                        <option value="Ditolak" {{ $candidate->status_seleksi == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                                        <option value="Cadangan" {{ $candidate->status_seleksi == 'Cadangan' ? 'selected' : '' }}>Cadangan</option>
                                    </select>
                                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition">
                                        Simpan
                                    </button>
                                </div>
                            </form>

                            <div class="mt-6 pt-6 border-t border-dashed border-gray-200">
                                @if($candidate->status_seleksi == 'Diterima')
                                    <a href="{{ route('admin.candidates.print', $candidate->id) }}" target="_blank" class="w-full flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:border-green-700 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
    </svg>
    Cetak Kartu Diterima
</a>
                                @else
                                    <div class="text-center p-3 bg-gray-100 rounded text-gray-500 text-xs">
                                        Tombol cetak kartu hanya muncul jika status <b>Diterima</b>.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="bg-white shadow-md rounded-xl overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
    <div class="flex items-center gap-2">
        <h3 class="font-bold text-gray-800">Biodata Santri</h3>
        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">{{ $candidate->jenjang }}</span>
    </div>
    <a href="{{ route('admin.candidates.edit', $candidate->id) }}" class="text-gray-500 hover:text-indigo-600 transition" title="Edit Data Lengkap">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
    </a>
</div>
                        </div>
                        <div class="p-6 text-sm">
                            <dl class="space-y-3">
                                <div class="grid grid-cols-3 gap-2">
                                    <dt class="font-medium text-gray-500">NISN</dt>
                                    <dd class="col-span-2 text-gray-900">{{ $candidate->nisn ?? '-' }}</dd>
                                </div>
                                <div class="grid grid-cols-3 gap-2">
                                    <dt class="font-medium text-gray-500">NIK</dt>
                                    <dd class="col-span-2 text-gray-900">{{ $candidate->nik ?? '-' }}</dd>
                                </div>
                                <div class="grid grid-cols-3 gap-2">
                                    <dt class="font-medium text-gray-500">TTL</dt>
                                    <dd class="col-span-2 text-gray-900">{{ $candidate->tempat_lahir }}, {{ \Carbon\Carbon::parse($candidate->tanggal_lahir)->format('d-m-Y') }}</dd>
                                </div>
                                <div class="grid grid-cols-3 gap-2">
                                    <dt class="font-medium text-gray-500">Gender</dt>
                                    <dd class="col-span-2 text-gray-900">{{ $candidate->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</dd>
                                </div>
                                <div class="grid grid-cols-3 gap-2">
                                    <dt class="font-medium text-gray-500">Alamat</dt>
                                    <dd class="col-span-2 text-gray-900">
                                        {{ $candidate->address->alamat ?? '' }}
                                        RT {{ $candidate->address->rt ?? '-' }}/RW {{ $candidate->address->rw ?? '-' }}
                                        Desa {{ $candidate->address->desa ?? '-' }}
                                        Kec. {{ $candidate->address->kecamatan ?? '-' }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <div class="bg-white shadow-md rounded-xl overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
                            <h3 class="font-bold text-gray-800">Data Orang Tua</h3>
                        </div>
                        <div class="p-6 text-sm">
                            <dl class="space-y-3">
                                <div>
                                    <dt class="font-medium text-gray-500">Nama Ayah</dt>
                                    <dd class="text-gray-900 font-semibold">{{ $candidate->parent->nama_ayah ?? '-' }}</dd>
                                    <dd class="text-xs text-gray-500">{{ $candidate->parent->no_hp_ayah ?? '-' }}</dd>
                                </div>
                                <div class="border-t pt-2">
                                    <dt class="font-medium text-gray-500">Nama Ibu</dt>
                                    <dd class="text-gray-900 font-semibold">{{ $candidate->parent->nama_ibu ?? '-' }}</dd>
                                    <dd class="text-xs text-gray-500">{{ $candidate->parent->no_hp_ibu ?? '-' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                </div>

                <div class="lg:col-span-2 space-y-6">

                    <div class="bg-white shadow-md rounded-xl overflow-hidden border border-gray-100">
                        <div class="bg-gradient-to-r from-gray-800 to-gray-700 px-6 py-4 flex justify-between items-center">
                            <h3 class="font-bold text-white flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                Kasir Pembayaran
                            </h3>
                            <span class="text-xs text-gray-300 bg-gray-600 px-2 py-1 rounded">Input Manual</span>
                        </div>
                        
                        <div class="p-6">
                            <form action="{{ route('admin.transactions.store', $candidate->id) }}" method="POST">
                                @csrf
                                <div class="overflow-x-auto rounded-lg border border-gray-200">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item Pembayaran</th>
                                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Tagihan</th>
                                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Sisa Hutang</th>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">Bayar (Rp)</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($candidate->bills as $bill)
                                            <tr class="{{ $bill->sisa_tagihan == 0 ? 'bg-green-50' : '' }}">
                                                <td class="px-4 py-3 text-sm text-gray-900 font-medium">
                                                    {{ $bill->payment_type->nama_pembayaran }}
                                                    @if($bill->sisa_tagihan == 0)
                                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Lunas</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-500 text-right">
                                                    {{ number_format($bill->nominal_tagihan, 0, ',', '.') }}
                                                </td>
                                                <td class="px-4 py-3 text-sm font-bold text-right {{ $bill->sisa_tagihan > 0 ? 'text-red-600' : 'text-green-600' }}">
                                                    {{ number_format($bill->sisa_tagihan, 0, ',', '.') }}
                                                </td>
                                                <td class="px-4 py-3">
                                                    @if($bill->sisa_tagihan > 0)
                                                        <div class="relative rounded-md shadow-sm">
                                                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                                <span class="text-gray-500 sm:text-sm">Rp</span>
                                                            </div>
                                                            <input type="number" 
                                                                   name="payments[{{ $bill->id }}]" 
                                                                   class="block w-full rounded-md border-gray-300 pl-10 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                                                                   placeholder="0"
                                                                   min="0"
                                                                   max="{{ $bill->sisa_tagihan }}">
                                                        </div>
                                                    @else
                                                        <div class="text-center text-xs text-green-600 font-bold">- Selesai -</div>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-5 flex justify-end items-center gap-4">
                                    <div class="text-xs text-gray-500 italic">Pastikan uang fisik sudah diterima sebelum klik simpan.</div>
                                    <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-6 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                        Proses Pembayaran
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="bg-white shadow-md rounded-xl overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
                            <h3 class="font-bold text-gray-800">Riwayat Transaksi</h3>
                        </div>
                        <div class="p-6">
                            @if($candidate->transactions->count() > 0)
                                <div class="space-y-4">
                                    @foreach($candidate->transactions as $trx)
                                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-4 border rounded-lg hover:bg-gray-50 transition border-gray-200">
                                        
                                        <div class="mb-2 sm:mb-0">
                                            <div class="flex items-center gap-2">
                                                <span class="font-mono font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded text-sm">{{ $trx->kode_transaksi }}</span>
                                                <span class="text-xs text-gray-400">{{ $trx->created_at->format('d M Y, H:i') }}</span>
                                            </div>
                                            <div class="mt-2 text-sm text-gray-600">
                                                <span class="font-medium">Rincian:</span>
                                                @foreach($trx->details as $detail)
                                                    <span class="inline-block bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded mr-1 mb-1">
                                                        {{ $detail->bill->payment_type->nama_pembayaran }} 
                                                        ({{ number_format($detail->nominal) }})
                                                    </span>
                                                @endforeach
                                            </div>
                                            <div class="mt-1 text-xs text-gray-400">
                                                Diterima oleh: {{ $trx->admin->name ?? 'System' }}
                                            </div>
                                        </div>

                                        <div class="text-right flex flex-col items-end gap-2">
                                            <div class="font-bold text-lg text-gray-800">
                                                Rp {{ number_format($trx->total_bayar, 0, ',', '.') }}
                                            </div>
                                            <a href="{{ route('admin.transactions.print', $trx->id) }}" target="_blank" class="inline-flex items-center text-xs font-bold text-blue-600 hover:text-blue-800 border border-blue-200 bg-blue-50 px-3 py-1.5 rounded hover:bg-blue-100 transition">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                                Cetak Struk (Thermal)
                                            </a>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8 text-gray-400">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                    Belum ada data transaksi pembayaran.
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>