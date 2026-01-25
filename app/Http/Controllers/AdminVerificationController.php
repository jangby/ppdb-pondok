<?php

namespace App\Http\Controllers;

use App\Models\Verification;
use App\Models\Setting; // [WAJIB] Tambahkan Model Setting
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

        // -------------------------------------------------------------
        // [LOGIKA BARU] PERSIAPAN DATA WA
        // -------------------------------------------------------------

        // A. Link Isi Biodata (Formulir)
        $linkForm = route('pendaftaran.form', ['token' => $data->token]);

        // B. Link Grup WA Pondok (Dari Pengaturan)
        $linkGrup = Setting::where('key', 'link_grup_wa_pondok')->value('value');
        
        // C. Nama Sekolah
        $namaSekolah = Setting::where('key', 'nama_sekolah')->value('value') ?? 'Pondok Pesantren';

        // -------------------------------------------------------------
        // [UPDATE] SUSUN PESAN WA
        // -------------------------------------------------------------
        
        $pesan = "Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\n"
               . "Halo! Kami informasikan bahwa berkas verifikasi Anda telah *DITERIMA / DISETUJUI* ✅\n\n"
               . "Langkah selanjutnya adalah mengisi Formulir Biodata Santri melalui link berikut:\n"
               . "👉 {$linkForm}\n\n"
               . "_(Link tersebut bersifat RAHASIA, mohon tidak dibagikan ke orang lain)_\n\n";

        // Jika Admin sudah mengisi Link Grup di Pengaturan, tampilkan disini
        if (!empty($linkGrup)) {
            $pesan .= "--------------------------------\n"
                    . "📢 *INFORMASI PONDOK*\n"
                    . "--------------------------------\n"
                    . "Agar tidak ketinggalan informasi terbaru seputar PPDB dan Pondok, silakan bergabung ke Grup WhatsApp Resmi kami:\n"
                    . "🔗 {$linkGrup}\n\n";
        }

        $pesan .= "Terima kasih.\n"
                . "Panitia PPDB {$namaSekolah}";


        // -------------------------------------------------------------
        // KONFIGURASI PENGIRIMAN (WAHA)
        // -------------------------------------------------------------
        $baseUrl = env('WAHA_BASE_URL', 'http://72.61.208.130:3000');
        $endpoint = $baseUrl . '/api/sendText';
        $apiKey = env('WAHA_API_KEY', '0f0eb5d196b6459781f7d854aac5050e');
        
        // Format Nomor HP
        $chatId = $data->no_wa;
        // Bersihkan karakter selain angka
        $chatId = preg_replace('/[^0-9]/', '', $chatId);
        // Ubah 08xxx jadi 628xxx
        if (substr($chatId, 0, 1) == '0') {
            $chatId = '62' . substr($chatId, 1);
        }
        $chatId .= '@c.us';

        // LOG DATA REQUEST
        Log::info("[WAHA DEBUG] Menyiapkan Request ke WAHA:");
        Log::info("Chat ID: " . $chatId);
        
        try {
            // KIRIM REQUEST
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Api-Key'    => $apiKey,
            ])->post($endpoint, [
                'session' => 'default',
                'chatId'  => $chatId,
                'text'    => $pesan
            ]);

            // LOG RESPONSE DARI WAHA
            Log::info("[WAHA DEBUG] Response Body: " . $response->body());

            if ($response->successful()) {
                Log::info("[WAHA DEBUG] SUKSES! Pesan terkirim.");
                return back()->with('success', 'Berkas disetujui & Link (Form + Grup WA) dikirim!');
            } else {
                Log::error("[WAHA DEBUG] GAGAL! WAHA menolak request.");
                return back()->with('error', 'Approved, tapi WA Gagal terkirim.');
            }

        } catch (\Exception $e) {
            Log::error("[WAHA DEBUG] EXCEPTION: " . $e->getMessage());
            return back()->with('error', 'Approved, tapi Gagal connect ke WAHA.');
        }
    }

    public function reject($id)
    {
        $data = Verification::findOrFail($id);
        $data->update(['status' => 'rejected']);
        
        // Jika ingin kirim WA notifikasi ditolak, bisa tambahkan logic serupa disini
        Log::info("[WAHA DEBUG] Berkas ID {$id} DITOLAK.");

        return back()->with('success', 'Berkas ditolak.');
    }
}