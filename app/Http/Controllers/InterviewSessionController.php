<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\InterviewSession;
use App\Models\Candidate;

class InterviewSessionController extends Controller
{
    /**
     * Menampilkan daftar sesi wawancara (Admin Dashboard)
     */
    public function index()
    {
        $sessions = InterviewSession::latest()->get();
        return view('admin.interview.sessions', compact('sessions'));
    }

    /**
     * Membuat sesi wawancara baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        InterviewSession::create([
            'title' => $request->title,
            'token' => Str::random(10), // Token unik untuk akses panitia
            'is_active' => true
        ]);

        return back()->with('success', 'Sesi wawancara berhasil dibuat.');
    }

    /**
     * Mengaktifkan/Nonaktifkan sesi
     */
    public function toggle($id)
    {
        $session = InterviewSession::findOrFail($id);
        $session->update(['is_active' => !$session->is_active]);
        
        return back()->with('success', 'Status sesi berhasil diperbarui.');
    }

    /**
     * MENANGANI SCAN QR DARI PANITIA
     * Menerima 'code' (no_daftar), mengembalikan URL redirect.
     */
    public function scanQr(Request $request)
    {
        // 1. Validasi Input
        $request->validate(['code' => 'required']);

        // 2. Cari Santri Berdasarkan No Daftar
        $candidate = Candidate::where('no_daftar', $request->code)->first();

        // Jika santri tidak ditemukan
        if (!$candidate) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data santri tidak ditemukan dengan nomor: ' . $request->code
            ], 404);
        }

        // 3. Ambil Token Sesi yang Sedang Aktif
        // Kita butuh token ini karena URL panitia formatnya: /panitia/{token}/form/{id}
        $activeSession = InterviewSession::where('is_active', true)->latest()->first();
        
        // Jika tidak ada sesi aktif, pakai default 'general' atau lempar error (tergantung kebutuhan)
        $token = $activeSession ? $activeSession->token : 'general';

        // 4. Generate URL Redirect
        // Nama route disesuaikan dengan web.php: prefix 'interview.' + name 'panitia.form'
        $url = route('interview.panitia.form', [
            'token' => $token,
            'candidate_id' => $candidate->id
        ]);

        // 5. Kirim Response JSON ke Javascript
        return response()->json([
            'status' => 'success',
            'redirect_url' => $url,
            'nama' => $candidate->nama_lengkap
        ]);
    }
}