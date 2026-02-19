<?php

namespace App\Http\Controllers;

use App\Models\Verification;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VerificationController extends Controller
{
    /**
     * TAHAP 1: FORM UPLOAD BERKAS PERJANJIAN
     */
    public function showUploadForm()
    {
        // 1. Cek Apakah Pendaftaran Buka
        if (!Setting::isOpen()) {
            return redirect()->route('home')->with('error', 'Pendaftaran Tutup');
        }

        // 2. Cek Apakah Verifikasi Wajib?
        $wajibVerifikasi = Setting::getValue('verification_active', '1'); // Default 1 (Wajib)

        if ($wajibVerifikasi == '0') {
            // -- LOGIKA BYPASS (LEWATI VERIFIKASI & BAYAR) --
            // Jika mode cepat aktif, sistem menganggap user sudah setuju & sudah bayar (atau gratis)
            
            $autoToken = Str::random(60);

            Verification::create([
                'no_wa'             => '000000000000',      // Nomor dummy
                'file_perjanjian'   => 'skipped_by_system', // Penanda dilewati
                'token'             => $autoToken,
                'status'            => 'approved',          // Auto Lolos Berkas
                'status_pembayaran' => 'paid'               // Auto Lunas (Bypass)
            ]);

            // Langsung lempar ke halaman Form Biodata
            return redirect()->route('pendaftaran.form', ['token' => $autoToken]);
        }
        
        // 3. Jika Wajib Verifikasi, Tampilkan Form Upload Biasa
        $template = Setting::getValue('template_perjanjian');
        return view('pendaftaran.verify', compact('template'));
    }

    /**
     * PROSES SIMPAN BERKAS (TAHAP 1)
     */
    public function store(Request $request)
    {
        $request->validate([
            'no_wa' => 'required|numeric|digits_between:10,15',
            'berkas' => 'required|mimes:pdf,jpg,jpeg,png|max:2048', // Max 2MB
        ]);

        // Format No WA (628xxx)
        $wa = $request->no_wa;
        if (substr($wa, 0, 1) == '0') {
            $wa = '62' . substr($wa, 1);
        }

        // Simpan File
        $path = $request->file('berkas')->store('verifikasi_files', 'public');

        // Simpan ke Database
        Verification::create([
            'no_wa'             => $wa,
            'file_perjanjian'   => $path,
            'token'             => Str::random(60), // Token Unik Panjang
            'status'            => 'pending',       // Menunggu Cek Admin
            'status_pembayaran' => 'unpaid'         // Belum Bayar
        ]);

        try {
            Log::info("--- MULAI KIRIM WA NOTIFIKASI KE ADMIN ---");

            // 1. Tentukan Nomor WA Admin
            // Anda bisa mengambilnya dari tabel Setting atau file .env.
            // Contoh pakai .env: ADMIN_WA_NUMBER=6281234567890 (Pastikan depannya 62)
            $adminWa = env('ADMIN_WA_NUMBER', '6285136468097'); // Ganti default-nya dengan no Admin Anda jika belum ada di .env
            $chatId = $adminWa . '@c.us';

            // 2. Susun Pesan
            $pesanWA = "🔔 *NOTIFIKASI PPDB BARU*\n\n"
                     . "Assalamu'alaikum Admin,\n"
                     . "Ada calon wali santri yang baru saja mengunggah *Surat Perjanjian*.\n\n"
                     . "📱 No. WA Wali: *" . $wa . "*\n\n"
                     . "Mohon segera login ke halaman Admin untuk memverifikasi berkas tersebut.\n"
                     . "Terima kasih.";

            // 3. Kirim via WAHA
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Api-Key'    => env('WAHA_API_KEY', '0f0eb5d196b6459781f7d854aac5050e'),
            ])->post(env('WAHA_BASE_URL', 'http://72.61.208.130:3003') . '/api/sendText', [
                'session' => 'default',
                'chatId'  => $chatId,
                'text'    => $pesanWA
            ]);

            if ($response->successful()) {
                Log::info("WA Notifikasi Admin Sukses Terkirim!");
            } else {
                Log::error("WA Notifikasi Admin Gagal Terkirim! Status: " . $response->status());
            }

        } catch (\Exception $e) {
            // Tangkap error agar proses upload pendaftar tidak gagal walau WA error
            Log::error("EXCEPTION WA Admin Error: " . $e->getMessage());
        }
        // ---------------------------------------------------------

        return redirect()->route('pendaftaran.verify.success');
    }

    public function showSuccess()
    {
        return view('pendaftaran.verify_success');
    }

    /**
     * TAHAP 1.5: HALAMAN PEMBAYARAN (BARU)
     * Diakses melalui Link WA setelah berkas disetujui Admin.
     */
    public function showPaymentForm($token)
    {
        // Cari data berdasarkan token
        $data = Verification::where('token', $token)->firstOrFail();

        // 1. Validasi: Berkas Perjanjian harus sudah APPROVED
        if ($data->status != 'approved') {
            return redirect()->route('home')->with('error', 'Berkas perjanjian Anda belum disetujui atau sedang diperiksa admin.');
        }

        // 2. Validasi: Jika sudah LUNAS (Paid), langsung ke Form Biodata
        if ($data->status_pembayaran == 'paid') {
            return redirect()->route('pendaftaran.form', ['token' => $token]);
        }

        // 3. Ambil Info Rekening & Biaya dari Setting
        $rekening = Setting::getValue('info_rekening');
        
        // Ambil data biaya (JSON) dan format untuk View
        $biayaRaw = json_decode(Setting::getValue('biaya_pendaftaran', '[]'), true);
        $biayaList = [];
        
        if (is_array($biayaRaw)) {
            foreach ($biayaRaw as $jenjang => $nominal) {
                $biayaList[] = [
                    'jenjang'   => $jenjang,
                    'nominal'   => $nominal,
                    'formatted' => 'Rp ' . number_format($nominal, 0, ',', '.')
                ];
            }
        }

        return view('pendaftaran.payment', compact('data', 'rekening', 'biayaList'));
    }

    /**
     * PROSES UPLOAD BUKTI BAYAR
     */
    public function storePayment(Request $request, $token)
    {
        $data = Verification::where('token', $token)->firstOrFail();

        $request->validate([
            'jenjang'        => 'required',
            'bukti_transfer' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
        ]);

        // Simpan File Bukti Transfer
        $path = $request->file('bukti_transfer')->store('bukti_transfer', 'public');

        // Update Data Verifikasi
        $data->update([
            'jenjang'           => $request->jenjang,
            'bukti_transfer'    => $path,
            'status_pembayaran' => 'pending' // Ubah jadi pending agar masuk antrian cek admin
        ]);

        return redirect()->route('pendaftaran.payment.success');
    }

    public function showPaymentSuccess()
    {
        return view('pendaftaran.payment_success');
    }
}