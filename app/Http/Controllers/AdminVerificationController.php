<?php

namespace App\Http\Controllers;

use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log; // <--- BARIS INI WAJIB ADA

class AdminVerificationController extends Controller
{
    public function index()
    {
        $verifications = Verification::where('status', 'pending')->latest()->get();
        return view('admin.verifications.index', compact('verifications'));
    }

    public function approve($id)
    {
        // 1. LOG AWAL
        Log::info("----------------------------------------------------");
        Log::info("[WAHA DEBUG] Memulai proses approval untuk ID: " . $id);

        $data = Verification::findOrFail($id);
        
        // Update Status
        $data->update(['status' => 'approved']);
        Log::info("[WAHA DEBUG] Status data diubah menjadi 'approved'.");

        // Generate Link
        $link = route('pendaftaran.form', ['token' => $data->token]);
        
        // Siapkan Data WA
        $baseUrl = env('WAHA_BASE_URL', 'http://72.61.208.130:3000');
        $endpoint = $baseUrl . '/api/sendText';
        $apiKey = env('WAHA_API_KEY', '0f0eb5d196b6459781f7d854aac5050e');
        
        // Pastikan format nomor WA benar (hapus 0 di depan/ + di depan, pastikan ada @c.us)
        // Jika data di DB sudah format 628xxx, langsung tambah @c.us
        $chatId = $data->no_wa . '@c.us'; 

        $pesan = "Halo! Berkas pendaftaran Anda telah *DITERIMA* ✅\n\n"
               . "Silakan klik link berikut untuk mengisi biodata santri:\n"
               . "👉 {$link}\n\n"
               . "_Link ini bersifat RAHASIA dan hanya untuk Anda._";

        // LOG DATA REQUEST
        Log::info("[WAHA DEBUG] Menyiapkan Request ke WAHA:");
        Log::info("URL: " . $endpoint);
        Log::info("Chat ID: " . $chatId);
        Log::info("API Key (Cek ada/tidak): " . ($apiKey ? 'ADA' : 'TIDAK ADA'));
        
        try {
            // KIRIM REQUEST
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Api-Key'    => $apiKey,
            ])->post($endpoint, [
                'session' => 'default', // Pastikan nama session di dashboard WAHA adalah 'default'
                'chatId'  => $chatId,
                'text'    => $pesan
            ]);

            // LOG RESPONSE DARI WAHA
            Log::info("[WAHA DEBUG] Response Status Code: " . $response->status());
            Log::info("[WAHA DEBUG] Response Body: " . $response->body());

            if ($response->successful()) {
                Log::info("[WAHA DEBUG] SUKSES! Pesan terkirim.");
                return back()->with('success', 'Berkas disetujui & Link dikirim ke WA!');
            } else {
                Log::error("[WAHA DEBUG] GAGAL! WAHA menolak request.");
                return back()->with('error', 'Approved, tapi WA Gagal. Cek file storage/logs/laravel.log untuk detailnya.');
            }

        } catch (\Exception $e) {
            // LOG ERROR KONEKSI (Misal WAHA mati)
            Log::error("[WAHA DEBUG] EXCEPTION / ERROR KONEKSI: " . $e->getMessage());
            return back()->with('error', 'Approved, tapi Gagal connect ke WAHA. Cek file storage/logs/laravel.log.');
        }
    }

    public function reject($id)
    {
        $data = Verification::findOrFail($id);
        $data->update(['status' => 'rejected']);
        
        // Opsional: Log reject juga
        Log::info("[WAHA DEBUG] Berkas ID {$id} DITOLAK.");

        return back()->with('success', 'Berkas ditolak.');
    }
}