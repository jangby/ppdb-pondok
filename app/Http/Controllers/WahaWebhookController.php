<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Verification; // Cek nomor di tabel pendaftar
use App\Models\Setting;      // Ambil nomor admin
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WahaWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // 1. Ambil Data dari WAHA
        $data = $request->all();

        // Log untuk debug (Opsional, cek storage/logs/laravel.log)
        // Log::info('WAHA Webhook:', $data); 

        // 2. Pastikan ini Event Pesan Masuk (bukan status, dll)
        if (!isset($data['event']) || $data['event'] !== 'message') {
            return response()->json(['status' => 'ignored_event']);
        }

        $payload = $data['payload'] ?? [];
        
        // Cek pengirim (hindari infinite loop membalas pesan sendiri)
        if (!empty($payload['fromMe'])) {
            return response()->json(['status' => 'ignored_self']);
        }

        // 3. Ambil Nomor Pengirim
        $rawFrom = $payload['from'] ?? ''; // Format: 628123456@c.us
        $phoneNumber = explode('@', $rawFrom)[0]; // Ambil angkanya saja: 628123456

        // 4. Cek Apakah Nomor Terdaftar di Database?
        // Kita cek di tabel 'verifications' kolom 'no_wa'
        $isRegistered = Verification::where('no_wa', $phoneNumber)->exists();

        if ($isRegistered) {
            $this->sendAutoReply($phoneNumber);
            return response()->json(['status' => 'replied']);
        }

        return response()->json(['status' => 'unknown_number']);
    }

    private function sendAutoReply($targetNumber)
    {
        // Ambil Nomor Admin dari Database Setting
        $adminWa = Setting::getValue('whatsapp_admin', '-');
        
        // Bersihkan format nomor admin (jika user input 08..., ubah jadi 62...)
        if (substr($adminWa, 0, 1) == '0') {
            $adminWa = '62' . substr($adminWa, 1);
        }

        $pesan = "🤖 *AUTO-REPLY SYSTEM*\n\n"
               . "Mohon maaf, ini adalah nomor *BOT OTOMATIS* yang hanya bertugas mengirim notifikasi sistem.\n\n"
               . "Admin/Manusia tidak memantau chat ini. Untuk pertanyaan atau konfirmasi, silakan hubungi Admin kami:\n\n"
               . "👉 *wa.me/{$adminWa}*\n\n"
               . "Terima kasih.";

        // Kirim Pesan Balasan
        $this->sendWA($targetNumber, $pesan);
    }

    private function sendWA($number, $message)
    {
        $baseUrl = env('WAHA_BASE_URL', 'http://72.61.208.130:3000');
        $endpoint = $baseUrl . '/api/sendText';
        $apiKey = env('WAHA_API_KEY', '0f0eb5d196b6459781f7d854aac5050e'); 

        // Format Nomor Tujuan
        $chatId = preg_replace('/[^0-9]/', '', $number);
        if (substr($chatId, 0, 1) == '0') $chatId = '62' . substr($chatId, 1);
        $chatId .= '@c.us';

        try {
            Http::withHeaders(['Content-Type' => 'application/json', 'X-Api-Key' => $apiKey])
                ->post($endpoint, [
                    'session' => 'default',
                    'chatId' => $chatId,
                    'text' => $message
                ]);
        } catch (\Exception $e) {
            Log::error("Auto-Reply Gagal: " . $e->getMessage());
        }
    }
}