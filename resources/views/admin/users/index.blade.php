<x-app-layout>
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Manajemen Pengguna</h2>
                <p class="text-sm text-gray-500">Kelola akun Admin dan Pimpinan Yayasan.</p>
            </div>
            
            <button onclick="document.getElementById('modalTambah').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Pengguna
            </button>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-4 mb-4 rounded-lg">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>- {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email / Username</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hak Akses (Role)</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($users as $user)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($user->role == 'admin')
                                <span class="bg-indigo-100 text-indigo-800 text-xs font-semibold px-2.5 py-0.5 rounded">Admin</span>
                            @else
                                <span class="bg-emerald-100 text-emerald-800 text-xs font-semibold px-2.5 py-0.5 rounded">Pimpinan</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            @if($user->id !== auth()->id())
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus akun ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md transition-colors">Hapus</button>
                            </form>
                            @else
                                <span class="text-gray-400 text-xs italic">Akun Anda</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="modalTambah" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/50 backdrop-blur-sm">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-xl font-bold text-gray-900">Tambah Akun Baru</h3>
                    <button onclick="document.getElementById('modalTambah').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" name="name" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email / Username</label>
                            <input type="text" name="email" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hak Akses (Role)</label>
                            <select name="role" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="pimpinan">Pimpinan Yayasan / Kepsek</option>
                                <option value="admin">Admin / Panitia</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <input type="password" name="password" required minlength="8" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Minimal 8 karakter.</p>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200">Batal</button>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Simpan Akun</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>