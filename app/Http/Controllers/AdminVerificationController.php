<?php

namespace App\Http\Controllers;

use App\Models\Verification;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdminVerificationController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil Filter
        $filter = $request->query('status', 'pending');

        // 2. Query Utama untuk Tabel (Sesuai Filter)
        $query = Verification::latest();

        if ($filter == 'pending') {
            $query->where(function($q) {
                $q->where('status', 'pending')
                  ->orWhere('status_pembayaran', 'pending');
            });
        } elseif ($filter == 'approved') {
            $query->where('status', 'approved')
                  ->where('status_pembayaran', 'paid');
        } elseif ($filter == 'rejected') {
            $query->where(function($q) {
                $q->where('status', 'rejected')
                  ->orWhere('status_pembayaran', 'rejected');
            });
        }
        
        $verifications = $query->paginate(10);

        // 3. HITUNG KPI / STATISTIK (Global, tidak terpengaruh filter)
        $stats = [
            'berkas_pending' => Verification::where('status', 'pending')->count(),
            'bayar_pending'  => Verification::where('status_pembayaran', 'pending')->count(),
            'selesai'        => Verification::where('status', 'approved')->where('status_pembayaran', 'paid')->count(),
            'ditolak'        => Verification::where('status', 'rejected')->orWhere('status_pembayaran', 'rejected')->count(),
        ];
        
        // Total antrian yang harus dikerjakan admin sekarang
        $stats['total_antrian'] = $stats['berkas_pending'] + $stats['bayar_pending'];

        return view('admin.verifications.index', compact('verifications', 'filter', 'stats'));
    }

    public function approve($id)
    {
        $data = Verification::findOrFail($id);
        
        // ==========================================================
        // SKENARIO A: MENYETUJUI BERKAS PERJANJIAN (Tahap 1)
        // ==========================================================
        if ($data->status == 'pending') {
            
            // 1. Update Status Berkas
            $data->update(['status' => 'approved']);

            // 2. Kirim WA: Instruksi Pembayaran (LENGKAP DENGAN NOMINAL & REKENING)
            $this->sendPaymentNotification($data);

            return back()->with('success', 'Berkas Perjanjian DISETUJUI. Pesan tagihan lengkap telah dikirim ke WA Wali.');
        }

        // ==========================================================
        // SKENARIO B: MENYETUJUI BUKTI PEMBAYARAN (Tahap 2)
        // ==========================================================
        if ($data->status == 'approved' && $data->status_pembayaran == 'pending') {
            
            // 1. Update Status Pembayaran
            $data->update(['status_pembayaran' => 'paid']);

            // 2. [AUTO] Update Status Santri & Assign Ruangan Tes
            if ($data->candidate) {
                $candidate = $data->candidate;
                $candidate->update(['status_seleksi' => 'Lulus Administrasi']);

                // Auto Assign Ruangan Santri
                if (!$candidate->santri_room_id) {
                    $targetSantri = \App\Models\TestRoom::where('jenis', 'Santri')
                                ->withCount('candidates_santri')
                                ->orderBy('candidates_santri_count', 'asc')
                                ->first();
                    if ($targetSantri) $candidate->update(['santri_room_id' => $targetSantri->id]);
                }

                // Auto Assign Ruangan Wali
                if (!$candidate->wali_room_id) {
                    $targetWali = \App\Models\TestRoom::where('jenis', 'Wali')
                                ->withCount('candidates_wali')
                                ->orderBy('candidates_wali_count', 'asc')
                                ->first();
                    if ($targetWali) $candidate->update(['wali_room_id' => $targetWali->id]);
                }
            }

            // 3. Kirim WA: Link Pengisian Biodata
            $this->sendBioLinkNotification($data);

            return back()->with('success', 'Pembayaran DITERIMA (Lunas). Link pengisian biodata telah dikirim ke WA Wali.');
        }

        return back()->with('error', 'Status data tidak valid untuk disetujui.');
    }

    public function reject(Request $request, $id)
    {
        $data = Verification::findOrFail($id);
        $alasan = $request->input('alasan', 'Dokumen tidak sesuai.');

        // JIKA MENOLAK BERKAS PERJANJIAN
        if ($data->status == 'pending') {
            $data->update(['status' => 'rejected']);
            // Opsional: Kirim WA Info Ditolak
            return back()->with('success', 'Berkas Perjanjian DITOLAK.');
        }

        // JIKA MENOLAK BUKTI PEMBAYARAN
        if ($data->status_pembayaran == 'pending') {
            $data->update([
                'status_pembayaran' => 'rejected',
                'catatan_pembayaran' => $alasan
            ]);
            
            // Kirim WA Notifikasi Ditolak agar upload ulang
            $this->sendPaymentRejectedNotification($data, $alasan);

            return back()->with('success', 'Bukti Pembayaran DITOLAK. Notifikasi dikirim ke WA.');
        }

        return back();
    }

    // =========================================================================
    // PRIVATE HELPER: WA NOTIFICATIONS (WAHA)
    // =========================================================================

    private function sendPaymentNotification($data)
    {
        $linkBayar = route('pendaftaran.payment', ['token' => $data->token]);
        $namaSekolah = Setting::getValue('nama_sekolah', 'Pondok Pesantren');
        
        // [BARU] Ambil Info Rekening
        $rekening = Setting::getValue('info_rekening', 'Hubungi Admin');
        
        // [BARU] Ambil Daftar Biaya & Format Teksnya
        $biayaRaw = json_decode(Setting::getValue('biaya_pendaftaran', '[]'), true);
        $listBiaya = "";
        
        if (!empty($biayaRaw)) {
            foreach ($biayaRaw as $jenjang => $nominal) {
                // Contoh: - SMP: Rp 100.000
                $formatted = number_format($nominal, 0, ',', '.');
                $listBiaya .= "• {$jenjang}: Rp {$formatted}\n";
            }
        } else {
            $listBiaya = "Hubungi Admin untuk info biaya.";
        }

        // [MODIFIKASI] Susun Pesan WA dengan Info Biaya & Rekening
        $pesan = "Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\n"
               . "Berkas Perjanjian Anda telah *DISETUJUI* oleh Admin {$namaSekolah}. 笨\n\n"
               . "Langkah selanjutnya, mohon lakukan *PEMBAYARAN PENDAFTARAN*.\n\n"
               . "*Rincian Biaya:*\n"
               . "{$listBiaya}\n"
               . "*Rekening Tujuan:*\n"
               . "{$rekening}\n\n"
               . "Setelah transfer, silakan *UPLOAD BUKTI TRANSFER* melalui link berikut:\n"
               . "{$linkBayar}\n\n"
               . "Mohon segera diselesaikan agar bisa lanjut ke pengisian biodata. Terima kasih.";

        $this->sendWA($data->no_wa, $pesan);
    }

    private function sendBioLinkNotification($data)
    {
        $linkForm = route('pendaftaran.form', ['token' => $data->token]);
        $linkGrup = Setting::getValue('link_grup_wa_pondok');
        $namaSekolah = Setting::getValue('nama_sekolah', 'Pondok Pesantren');
        
        $pesan = "ALHAMDULILLAH!\n"
               . "Pembayaran pendaftaran Anda telah *DITERIMA & TERVERIFIKASI*.\n\n"
               . "Silakan lengkapi *FORMULIR BIODATA SANTRI* melalui link rahasia berikut:\n"
               . "{$linkForm}\n\n"
               . "_(Mohon data diisi dengan teliti dan lengkap)_\n\n";

        $pesan .= "Terima kasih - Panitia PPDB {$namaSekolah}";

        $this->sendWA($data->no_wa, $pesan);
    }

    private function sendPaymentRejectedNotification($data, $alasan)
    {
        $linkBayar = route('pendaftaran.payment', ['token' => $data->token]);
        $namaSekolah = Setting::getValue('nama_sekolah', 'Pondok Pesantren');

        $pesan = "Mohon Maaf\n"
               . "Bukti Pembayaran Anda *DITOLAK* oleh Admin {$namaSekolah}.\n\n"
               . "Alasan: _{$alasan}_\n\n"
               . "Silakan upload ulang bukti pembayaran yang benar melalui link berikut:\n"
               . "{$linkBayar}";

        $this->sendWA($data->no_wa, $pesan);
    }

    private function sendWA($number, $message)
    {
        $baseUrl = env('WAHA_BASE_URL', 'http://72.61.208.130:3000');
        $endpoint = $baseUrl . '/api/sendText';
        $apiKey = env('WAHA_API_KEY', '0f0eb5d196b6459781f7d854aac5050e'); 

        // Format Nomor
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
            Log::error("WA Gagal: " . $e->getMessage());
        }
    }
}