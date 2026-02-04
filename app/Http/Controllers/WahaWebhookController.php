<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Verification;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WahaWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // 1. Ambil Data
        $data = $request->all();
        
        // [DEBUG] Catat setiap request masuk ke storage/logs/laravel.log
        // Log::info('WAHA Webhook Hit:', $data); 

        // 2. Filter Event: Hanya terima pesan teks
        if (!isset($data['event']) || $data['event'] !== 'message') {
            return response()->json(['status' => 'ignored_event']);
        }

        $payload = $data['payload'] ?? [];

        // 3. Filter Pengirim: Jangan balas pesan dari diri sendiri (bot)
        if (!empty($payload['fromMe'])) {
            return response()->json(['status' => 'ignored_self']);
        }

        // 4. Ambil Nomor Pengirim
        $rawFrom = $payload['from'] ?? ''; // Format WA: 628123456@c.us
        $waNumber = explode('@', $rawFrom)[0]; // Ambil angkanya saja: 628123456
        
        // Buat versi "08..." juga untuk jaga-jaga jika di DB tersimpan pakai 0
        $localNumber = $waNumber;
        if (substr($waNumber, 0, 2) == '62') {
            $localNumber = '0' . substr($waNumber, 2); // Ubah 62812 jadi 0812
        }

        Log::info("[AUTO-REPLY] Cek Nomor: $waNumber atau $localNumber");

        // 5. Cek Database (Cek kedua format)
        $isRegistered = Verification::where(function($q) use ($waNumber, $localNumber) {
            $q->where('no_wa', $waNumber)
              ->orWhere('no_wa', $localNumber);
        })->exists();

        if ($isRegistered) {
            Log::info("[AUTO-REPLY] Nomor DITEMUKAN! Mengirim balasan...");
            $this->sendAutoReply($waNumber);
            return response()->json(['status' => 'replied']);
        } else {
            Log::info("[AUTO-REPLY] Nomor TIDAK DIKENAL. Tidak membalas.");
        }

        return response()->json(['status' => 'unknown_number']);
    }

    private function sendAutoReply($targetNumber)
    {
        // Ambil Nomor Admin
        $adminWa = Setting::getValue('whatsapp_admin', '-');
        
        // Format Nomor Admin (Hapus 0 depan, tambah 62)
        $displayAdmin = $adminWa;
        if (substr($displayAdmin, 0, 1) == '0') {
            $displayAdmin = '62' . substr($displayAdmin, 1);
        }

        $pesan = "🤖 *AUTO-REPLY SYSTEM*\n\n"
               . "Mohon maaf, ini adalah nomor *BOT OTOMATIS* yang hanya bertugas mengirim notifikasi sistem.\n\n"
               . "Admin/Manusia tidak memantau chat ini. Untuk pertanyaan atau konfirmasi, silakan hubungi Admin kami:\n\n"
               . "👉 *wa.me/{$displayAdmin}*\n\n"
               . "Terima kasih.";

        $this->sendWA($targetNumber, $pesan);
    }

    private function sendWA($number, $message)
    {
        $baseUrl = env('WAHA_BASE_URL', 'http://72.61.208.130:3000');
        $endpoint = $baseUrl . '/api/sendText';
        $apiKey = env('WAHA_API_KEY', '0f0eb5d196b6459781f7d854aac5050e'); 

        // Pastikan format nomor tujuan benar (akhiri dengan @c.us untuk WAHA)
        $chatId = preg_replace('/[^0-9]/', '', $number);
        if (substr($chatId, 0, 1) == '0') $chatId = '62' . substr($chatId, 1);
        $chatId .= '@c.us';

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Api-Key' => $apiKey
            ])->post($endpoint, [
                'session' => 'default',
                'chatId' => $chatId,
                'text' => $message
            ]);
            
            Log::info("[AUTO-REPLY] Hasil Kirim WA: " . $response->status());
        } catch (\Exception $e) {
            Log::error("[AUTO-REPLY] Gagal Kirim WA: " . $e->getMessage());
        }
    }
}