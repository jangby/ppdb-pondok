<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Verification;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\WebhookLog;

class WahaWebhookController extends Controller
{
    public function handle(Request $request)
{
    // 1. [DEBUG DATABASE] TANGKAP SEMUA DATA KE DB
    // Ini akan menyimpan apapun yang dikirim WAHA, berhasil atau gagal diproses
    $logEntry = WebhookLog::create([
        'payload' => $request->all(),
        'status'  => 'received'
    ]);

    // Ambil Data
    $data = $request->all();
    
    // Log standar (bisa dihapus nanti kalau database sudah jalan)
    Log::info('WAHA Webhook Hit:', $data); 

    // 2. Filter Event: Hanya terima pesan teks
    if (!isset($data['event']) || $data['event'] !== 'message') {
        $logEntry->update(['status' => 'ignored_event']); // Update status di DB
        return response()->json(['status' => 'ignored_event']);
    }

    $payload = $data['payload'] ?? [];

    // 3. Filter Pengirim: Jangan balas pesan dari diri sendiri (bot)
    if (!empty($payload['fromMe'])) {
        $logEntry->update(['status' => 'ignored_self']); // Update status di DB
        return response()->json(['status' => 'ignored_self']);
    }

    // 4. Ambil Nomor Pengirim
    $rawFrom = $payload['from'] ?? ''; 
    $waNumber = explode('@', $rawFrom)[0]; 
    
    $localNumber = $waNumber;
    if (substr($waNumber, 0, 2) == '62') {
        $localNumber = '0' . substr($waNumber, 2); 
    }

    // 5. Cek Database
    $verification = Verification::where(function($q) use ($waNumber, $localNumber) {
        $q->where('no_wa', $waNumber)
          ->orWhere('no_wa', $localNumber);
    })->first();

    if ($verification) {
        // Kirim balasan
        $this->sendAutoReply($waNumber);
        
        $logEntry->update(['status' => 'replied_success']); // Update status SUKSES
        return response()->json(['status' => 'replied']);
    } 

    $logEntry->update(['status' => 'unknown_number']); // Update status GAGAL
    return response()->json(['status' => 'unknown_number']);
}

    private function sendAutoReply($targetNumber)
    {
        // Ambil Nomor Admin dari Database Setting
        // Pastikan Anda sudah menjalankan seeder Setting atau isi tabel settings
        $adminWa = Setting::getValue('whatsapp_admin', '-');
        
        // Format Nomor Admin (Hapus 0 depan, tambah 62 untuk link wa.me)
        $displayAdmin = $adminWa;
        if (substr($displayAdmin, 0, 1) == '0') {
            $displayAdmin = '62' . substr($displayAdmin, 1);
        }

        $pesan = "🤖 *AUTO-REPLY SYSTEM*\n\n"
               . "Mohon maaf, ini adalah nomor *BOT OTOMATIS* yang hanya bertugas mengirim notifikasi sistem.\n\n"
               . "Admin/Manusia tidak memantau chat ini. Untuk pertanyaan atau konfirmasi, silakan hubungi Admin kami:\n\n"
               . "👉 *wa.me/{$displayAdmin}*\n\n"
               . "Terima kasih.";

        return $this->sendWA($targetNumber, $pesan);
    }

    private function sendWA($number, $message)
    {
        // Konfigurasi WAHA
        $baseUrl = env('WAHA_BASE_URL', 'http://72.61.208.130:3000');
        $apiKey = env('WAHA_API_KEY', '0f0eb5d196b6459781f7d854aac5050e'); 
        $sessionName = env('WAHA_SESSION_NAME', 'default'); // Sesuaikan dengan nama session di dashboard WAHA

        $endpoint = $baseUrl . '/api/sendText';

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
            
            // Logging hasil response
            if ($response->successful()) {
                Log::info("[AUTO-REPLY] Sukses Kirim WA ke $number");
            } else {
                Log::error("[AUTO-REPLY] Gagal Kirim WA ke $number. Status: " . $response->status() . " Body: " . $response->body());
            }

            return $response->json();

        } catch (\Exception $e) {
            Log::error("[AUTO-REPLY] Exception Error: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}