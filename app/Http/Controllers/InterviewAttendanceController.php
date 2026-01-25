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
        // Ambil antrian terakhir hari ini
        $today = Carbon::today();
        $lastQueue = Candidate::whereDate('waktu_hadir', $today)->max('nomor_antrian');
        
        // Berikan nomor selanjutnya
        $newQueue = $lastQueue ? $lastQueue + 1 : 1;

        // 3. UPDATE DATA (PERBAIKAN DISINI)
        $candidate->update([
            'waktu_hadir'   => now(),       // Update waktu datang terbaru
            'nomor_antrian' => $newQueue,   // Update nomor antrian baru
            
            // --- RESET STATUS PANGGILAN ---
            'waktu_panggil'  => null, // PENTING: Kosongkan agar bisa dipanggil lagi
            'dipanggil_oleh' => null  // Opsional: Reset nama pemanggil sebelumnya
        ]);

        // 4. KIRIM RESPONSE SUKSES
        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil! Cetak Antrian Baru.',
            'data' => [
                'nama'      => $candidate->nama_lengkap,
                'no_daftar' => $candidate->no_daftar,
                'jenjang'   => $candidate->jenjang,
                'antrian'   => $newQueue,
                'waktu'     => now()->format('d/m/Y H:i'),
                'is_new'    => true
            ]
        ]);
    }


    // =================================================================
    // 2. FITUR KIRIM WA (LINK KARTU TES)
    // =================================================================
    
    // Dipanggil dari Tombol Ungu di Tabel Santri
    public function sendQrToWa($id)
    {
        $candidate = Candidate::with('parent')->findOrFail($id);
        
        // Buat Link ke Halaman Kartu Publik
        $linkKartu = route('public.kartu_tes', $candidate->no_daftar);
        
        $message = "*UNDANGAN WAWANCARA & TES*\n\n" .
                   "Putra/i: *{$candidate->nama_lengkap}*\n" .
                   "No. Daftar: *{$candidate->no_daftar}*\n\n" .
                   "Silakan klik link di bawah ini untuk melihat *Kartu Tes / QR Code* Anda:\n" .
                   "👇👇👇\n" .
                   "$linkKartu\n" .
                   "👆👆👆\n\n" .
                   "Harap tunjukkan QR Code di dalam link tersebut kepada panitia saat tiba di lokasi.\n\n" .
                   "_Simpan pesan ini._";

        $this->sendWaLink($candidate, $message);

        return back()->with('success', 'Link Kartu Tes berhasil dikirim ke WA Wali.');
    }

    // Dipanggil dari Tombol Lonceng (Pengingat)
    public function sendReminder($id)
    {
        $candidate = Candidate::with('parent')->findOrFail($id);
        
        if ($candidate->waktu_hadir) {
            return back()->with('error', 'Santri sudah hadir, tidak perlu diingatkan.');
        }

        $message = "*PENGINGAT JADWAL WAWANCARA*\n\n" .
                   "Yth. Wali Santri *{$candidate->nama_lengkap}*,\n" .
                   "Kami menunggu kehadiran Anda di lokasi tes. Mohon segera melakukan registrasi ulang di meja panitia.\n\n" .
                   "Terima kasih.";

        $this->sendWaLink($candidate, $message);

        return back()->with('success', 'Pengingat berhasil dikirim.');
    }

    // Helper Kirim WA (Text Only / Link)
    private function sendWaLink($candidate, $messageText)
    {
        $rawNo = $candidate->parent->no_hp_ayah ?? $candidate->parent->no_hp_ibu;
        
        if (empty($rawNo)) return;

        $chatId = $this->formatNumber($rawNo);
        $baseUrl = rtrim(env('WAHA_BASE_URL', 'http://72.61.208.130:3000'), '/');
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