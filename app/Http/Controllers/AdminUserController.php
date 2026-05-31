<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    // 1. Menampilkan daftar pengguna (Hanya admin dan pimpinan)
    public function index()
    {
        // Ambil data user, urutkan dari yang terbaru, kecualikan role 'santri' agar tidak kepenuhan
        $users = User::whereIn('role', ['admin', 'pimpinan'])->latest()->get();
        return view('admin.users.index', compact('users'));
    }

    // 2. Proses menyimpan data akun baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,pimpinan',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Enkripsi password
            'role' => $request->role,
        ]);

        return back()->with('success', 'Akun pengguna berhasil ditambahkan!');
    }

    // 3. Proses menghapus akun
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Mencegah admin menghapus dirinya sendiri
        if ($user->id == auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }

        $user->delete();
        return back()->with('success', 'Akun berhasil dihapus.');
    }
}