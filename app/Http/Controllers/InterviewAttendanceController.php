<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class InterviewAttendanceController extends Controller
{
    // =================================================================
    // 1. HALAMAN SCANNER & BLUETOOTH PRINTER
    // =================================================================
    public function index()
    {
        return view('admin.interview.attendance');
    }

    public function processScan(Request $request)
    {
        $noDaftar = $request->input('code'); 
        
        // 1. Cari santri
        $candidate = Candidate::where('no_daftar', $noDaftar)->first();

        if (!$candidate) {
            return response()->json(['status' => 'error', 'message' => 'Data santri tidak ditemukan!'], 404);
        }

        // 2. GENERATE NOMOR ANTRIAN BARU
        $today = \Carbon\Carbon::today();
        $lastQueue = Candidate::whereDate('waktu_hadir', $today)->max('nomor_antrian');
        $newQueue = $lastQueue ? $lastQueue + 1 : 1;

        // 3. UPDATE DATA
        $candidate->update([
            'waktu_hadir'   => now(),
            'nomor_antrian' => $newQueue,
            'waktu_panggil'  => null, // Reset status panggil
            'dipanggil_oleh' => null
        ]);

        // ========================================================
        // PERBAIKAN DISINI: LOAD RELASI RUANGAN
        // ========================================================
        // Kita paksa sistem untuk mengambil data santri_room dan wali_room
        $candidate->load(['santri_room', 'wali_room']); 

        // 4. KIRIM RESPONSE KE JAVASCRIPT
        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil! Cetak Antrian Baru.',
            'data' => [
                'nama'      => $candidate->nama_lengkap,
                'no_daftar' => $candidate->no_daftar,
                'jenjang'   => $candidate->jenjang,
                'antrian'   => $newQueue,
                'waktu'     => now()->format('d/m/Y H:i'),
                
                // Ambil Nama Ruangan (Gunakan operator null safe ? :)
                'r_santri'  => $candidate->santri_room ? $candidate->santri_room->nama_ruangan : '-',
                'r_wali'    => $candidate->wali_room   ? $candidate->wali_room->nama_ruangan   : '-',
            ]
        ]);
    }

    // =================================================================
    // 2. FITUR KIRIM WA (DIRECT LAMPIRAN KARTU TES)
    // =================================================================
    
    public function sendQrToWa($id)
    {
        $candidate = Candidate::with(['parent', 'santri_room', 'wali_room'])->findOrFail($id);
        
        $ruangSantri = $candidate->santri_room ? $candidate->santri_room->nama_ruangan : 'Cek di Papan Pengumuman';
        $ruangWali   = $candidate->wali_room   ? $candidate->wali_room->nama_ruangan   : 'Cek di Papan Pengumuman';

        // SULAP MENJADI GAMBAR QR CODE (HANYA BERISI NO DAFTAR)
        $qrImageUrl = "https://quickchart.io/qr?text=" . urlencode($candidate->no_daftar) . "&margin=2&size=500";
        
        $message = "🎓 *UNDANGAN WAWANCARA & TES*\n\n" .
                   "Santri/ah: *{$candidate->nama_lengkap}*\n" .
                   "No. Daftar: *{$candidate->no_daftar}*\n\n" .
                   "📍 *LOKASI TES:*\n" .
                   "👤 Santri: *{$ruangSantri}*\n" .
                   "👥 Wali: *{$ruangWali}*\n\n" .
                   "Berikut adalah *Gambar QR Code* Ananda.\n" .
                   "Silakan simpan gambar ini dan tunjukkan kepada panitia saat tiba di meja registrasi.\n\n" .
                   "_Simpan pesan ini._";

        $status = $this->sendWaDirectFile($candidate, $message, $qrImageUrl, 'QR_Kartu_'.$candidate->no_daftar.'.png');

        // ... (kode peringatan success/error di bawahnya tetap sama)
        if ($status === 'no_number') {
            return back()->with('error', '⛔ Gagal: Nomor WA pendaftar tidak ditemukan.');
        } elseif ($status === 'bot_error') {
            return back()->with('error', '⚠️ Gagal: Tidak dapat menghubungi Bot.');
        }

        return back()->with('success', '✅ Gambar QR Code & Info Ruangan berhasil dikirim.');
    }

    private function sendWaDirectFile($candidate, $message, $fileUrl, $fileName)
    {
        // A. Coba cari nomor WA dari relasi Candidate atau Parent
        $number = $candidate->no_wa ?? ($candidate->parent->no_wa ?? null); 
        
        // B. Jika tidak ketemu, kita bongkar dan lacak riwayat pendaftaran di tabel Verification
        if (!$number) {
            $verif = \App\Models\Verification::where('file_perjanjian', $candidate->file_perjanjian)->first();
            $number = $verif ? $verif->no_wa : null;
        }

        // Jika tetap tidak ketemu, teriakkan error "no_number"
        if (!$number) return 'no_number';

        $chatId = preg_replace('/[^0-9]/', '', $number);
        if (substr($chatId, 0, 1) == '0') $chatId = '62' . substr($chatId, 1);

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(5)->post('http://127.0.0.1:5000/api/send-message', [
                'no_wa'     => $chatId,
                'pesan'     => $message,
                'file_url'  => $fileUrl,
                'file_name' => $fileName
            ]);
            
            return $response->successful() ? 'success' : 'bot_error';
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Gagal konek ke Bot: " . $e->getMessage());
            return 'bot_error';
        }
    }

    // Dipanggil dari Tombol Lonceng (Pengingat)
    public function sendReminder($id)
    {
        $candidate = Candidate::with('parent')->findOrFail($id);
        
        if ($candidate->waktu_hadir) {
            return back()->with('error', '⛔ Santri sudah hadir, tidak perlu diingatkan.');
        }

        $message = "🔔 *PENGINGAT JADWAL WAWANCARA & TES*\n\n" .
                   "Yth. Bapak/Ibu Wali dari *{$candidate->nama_lengkap}*,\n\n" .
                   "Kami menginformasikan bahwa panitia saat ini sedang menunggu kehadiran Anda di lokasi tes. Mohon segera menuju meja registrasi kepanitiaan untuk melakukan lapor kedatangan.\n\n" .
                   "Terima kasih atas kerjasamanya. 🙏";

        // Eksekusi pengiriman via Bot Baileys (Parameter ke-3 dan ke-4 diisi null karena tidak ada lampiran file)
        $status = $this->sendWaDirectFile($candidate, $message, null, null);

        // Tampilkan pesan error / sukses yang informatif
        if ($status === 'no_number') {
            return back()->with('error', '⛔ Gagal: Nomor WA pendaftar tidak ditemukan di database.');
        } elseif ($status === 'bot_error') {
            return back()->with('error', '⚠️ Gagal: Tidak dapat menghubungi server Bot Baileys.');
        }

        return back()->with('success', '✅ Pesan pengingat (Reminder) berhasil dikirim langsung ke WhatsApp Wali Santri.');
    }
    
    // Helper Kirim WA (Text Only / Link)
    private function sendWaLink($candidate, $messageText)
    {
        $rawNo = $candidate->parent->no_hp_ayah ?? $candidate->parent->no_hp_ibu;
        
        if (empty($rawNo)) return;

        $chatId = $this->formatNumber($rawNo);
        $baseUrl = rtrim(env('WAHA_BASE_URL', 'http://72.61.208.130:3001'), '/');
        $apiKey  = env('WAHA_API_KEY', '0f0eb5d196b6459781f7d854aac5050e');

        try {
            Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Api-Key'    => $apiKey,
            ])->post($baseUrl . '/api/sendText', [
                'session' => 'default',
                'chatId'  => $chatId,
                'text'    => $messageText
            ]);
        } catch (\Exception $e) {
            Log::error("WA Error: " . $e->getMessage());
        }
    }

    private function formatNumber($number) {
        $cleanNo = preg_replace('/[^0-9]/', '', $number);
        if (substr($cleanNo, 0, 1) == '0') $cleanNo = '62' . substr($cleanNo, 1);
        elseif (substr($cleanNo, 0, 2) != '62') $cleanNo = '62' . $cleanNo;
        return $cleanNo . '@c.us';
    }


    // =================================================================
    // 3. FITUR REKAPITULASI & BLAST WA
    // =================================================================
    
    public function recap()
    {
        // Ambil semua data santri (Bisa difilter status 'Lulus' saja jika perlu)
        // Disini kita ambil semua yang statusnya bukan 'Ditolak'
        $candidates = Candidate::where('status_seleksi', '!=', 'Ditolak')->get();

        $total   = $candidates->count();
        $present = $candidates->whereNotNull('waktu_hadir');
        $absent  = $candidates->whereNull('waktu_hadir');

        return view('admin.interview.recap', [
            'total'   => $total,
            'present' => $present, // Collection santri hadir
            'absent'  => $absent   // Collection santri belum hadir
        ]);
    }

    public function massRemind()
    {
        // 1. Ambil santri yang BELUM hadir & status valid
        $absentCandidates = Candidate::where('status_seleksi', '!=', 'Ditolak')
                            ->whereNull('waktu_hadir')
                            ->with('parent') // Eager load orang tua
                            ->get();

        if ($absentCandidates->isEmpty()) {
            return back()->with('error', 'Semua santri sudah hadir. Tidak ada pesan yang dikirim.');
        }

        $countSuccess = 0;
        
        // 2. Loop dan Kirim WA
        foreach ($absentCandidates as $candidate) {
            
            $message = "Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\n" .
                   "Yth. Wali Santri *{$candidate->nama_lengkap}*,\n" .
                   "Kami informasikan bahwa sesi Tes & Wawancara PPDB sedang berlangsung.\n\n" .
                   "Saat ini status Ananda tercatat: *BELUM HADIR*.\n" .
                   "Mohon segera menuju meja registrasi ulang jika sudah tiba di lokasi.\n\n" .
                   "Abaikan pesan ini jika Anda sedang dalam antrian.\n" .
                   "Terima kasih.";

            // Kirim WA (Memakai fungsi helper yang sudah ada)
            $this->sendWaLink($candidate, $message);
            
            $countSuccess++;

            // PENTING: Jeda 2 detik agar tidak di-banned WA
            sleep(2); 
        }

        return back()->with('success', "Berhasil mengirim pengingat ke {$countSuccess} Wali Santri.");
    }
}