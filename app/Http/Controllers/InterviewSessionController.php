<?php

namespace App\Http\Controllers;

use App\Models\InterviewSession;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InterviewSessionController extends Controller
{
    // [PENTING] Ini adalah fungsi index yang sebelumnya hilang/error
    public function index()
    {
        // Mengambil semua sesi diurutkan dari yang terbaru
        $sessions = InterviewSession::latest()->get();
        
        // Menampilkan view
        return view('admin.interview.sessions', compact('sessions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        InterviewSession::create([
            'title' => $request->title,
            'token' => Str::random(10), // Membuat kode unik 10 karakter
            'is_active' => true
        ]);

        return back()->with('success', 'Sesi wawancara berhasil dibuat.');
    }

    public function toggle($id)
    {
        $session = InterviewSession::findOrFail($id);
        $session->update(['is_active' => !$session->is_active]);
        
        return back()->with('success', 'Status sesi berhasil diperbarui.');
    }
}