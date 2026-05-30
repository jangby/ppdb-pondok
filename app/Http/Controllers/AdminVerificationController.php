<?php

namespace App\Http\Controllers;

use App\Models\Verification;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Candidate;
use App\Models\CandidateBill;
use App\Models\PaymentType;
use Illuminate\Support\Facades\DB;

class AdminVerificationController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil Filter
        $filter = $request->query('status', 'pending');

        // 2. Query Utama untuk Tabel (Sesuai Filter)
        $query = Verification::latest();

        if ($filter == 'pending') {
            // PERBAIKAN: Tampilkan yang berkasnya pending, ATAU yang sedang menunggu wali bayar (unpaid), ATAU yang bukti bayarnya pending verifikasi
            $query->where(function($q) {
                $q->where('status', 'pending')
                  ->orWhere('status_pembayaran', 'unpaid')
                  ->orWhere('status_pembayaran', 'pending');
            })->where('status', '!=', 'rejected')->where('status_pembayaran', '!=', 'rejected');
            
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
        
        // Total antrian yang harus dikerjakan admin sekarang (tidak termasuk yang menunggu wali transfer)
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
            
            $data->update(['status' => 'approved']);
            $waSent = $this->sendPaymentNotification($data);
            $data->update(['wa_tahap1_sent' => $waSent]);

        return back()->with($waSent ? 'success' : 'warning', 
            $waSent ? 'Berkas DISETUJUI. Pesan tagihan telah dikirim.' : 'Berkas DISETUJUI, TETAPI WA GAGAL terkirim. Server WA bermasalah.'
        );
        }

        // ==========================================================
        // SKENARIO B: MENYETUJUI BUKTI PEMBAYARAN (Tahap 2)
        // ==========================================================
        if ($data->status == 'approved' && $data->status_pembayaran == 'pending') {
            
            $data->update(['status_pembayaran' => 'paid']);

            if ($data->candidate ?? false) {
                $candidate = $data->candidate;
                $candidate->update(['status_seleksi' => 'Lulus Administrasi']);

                if (!$candidate->santri_room_id) {
                    $targetSantri = \App\Models\TestRoom::where('jenis', 'Santri')
                                ->withCount('candidates_santri')
                                ->orderBy('candidates_santri_count', 'asc')
                                ->first();
                    if ($targetSantri) $candidate->update(['santri_room_id' => $targetSantri->id]);
                }

                if (!$candidate->wali_room_id) {
                    $targetWali = \App\Models\TestRoom::where('jenis', 'Wali')
                                ->withCount('candidates_wali')
                                ->orderBy('candidates_wali_count', 'asc')
                                ->first();
                    if ($targetWali) $candidate->update(['wali_room_id' => $targetWali->id]);
                }
            }

            $waSent = $this->sendBioLinkNotification($data);
            $data->update(['wa_tahap2_sent' => $waSent]);

        return back()->with($waSent ? 'success' : 'warning', 
            $waSent ? 'Pembayaran DITERIMA. Link biodata telah dikirim.' : 'Pembayaran DITERIMA, TETAPI WA GAGAL terkirim. Server WA bermasalah.'
        );

        }

        // ==========================================================
        // SKENARIO C: MENERIMA PEMBAYARAN CASH (DI PONDOK)
        // ==========================================================
        if ($data->status == 'approved' && $data->status_pembayaran == 'unpaid') {
            
            $data->update([
                'status_pembayaran' => 'paid',
                'catatan_pembayaran' => 'Pembayaran Cash Offline'
            ]);

            if ($data->candidate ?? false) {
                $candidate = $data->candidate;
                $candidate->update(['status_seleksi' => 'Lulus Administrasi']);

                if (!$candidate->santri_room_id) {
                    $targetSantri = \App\Models\TestRoom::where('jenis', 'Santri')
                                ->withCount('candidates_santri')
                                ->orderBy('candidates_santri_count', 'asc')
                                ->first();
                    if ($targetSantri) $candidate->update(['santri_room_id' => $targetSantri->id]);
                }

                if (!$candidate->wali_room_id) {
                    $targetWali = \App\Models\TestRoom::where('jenis', 'Wali')
                                ->withCount('candidates_wali')
                                ->orderBy('candidates_wali_count', 'asc')
                                ->first();
                    if ($targetWali) $candidate->update(['wali_room_id' => $targetWali->id]);
                }
            }

            $waSent = $this->sendBioLinkNotification($data);
            $data->update(['wa_tahap2_sent' => $waSent]);

        return back()->with($waSent ? 'success' : 'warning', 
            $waSent ? 'Pembayaran DITERIMA. Link biodata telah dikirim.' : 'Pembayaran DITERIMA, TETAPI WA GAGAL terkirim. Server WA bermasalah.'
        );
        }

        // Jika data tidak cocok dengan skenario manapun, kembalikan dengan pesan error
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

    public function resendWa($id, $tahap)
    {
        $data = Verification::findOrFail($id);

        if ($tahap == 1) {
            $waSent = $this->sendPaymentNotification($data);
            if ($waSent) {
                $data->update(['wa_tahap1_sent' => true]);
                return back()->with('success', 'Pesan WA Tagihan berhasil dikirim ulang!');
            }
        } elseif ($tahap == 2) {
            $waSent = $this->sendBioLinkNotification($data);
            if ($waSent) {
                $data->update(['wa_tahap2_sent' => true]);
                return back()->with('success', 'Pesan WA Link Biodata berhasil dikirim ulang!');
            }
        }

        return back()->with('error', 'Masih gagal mengirim WA. Pastikan server WAHA sedang menyala.');
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


        // PENTING: Tambahkan 'return' di depannya
        return $this->sendWA($data->no_wa, $pesan);
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

        // PENTING: Tambahkan 'return' di depannya
        return $this->sendWA($data->no_wa, $pesan);
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
        $baseUrl = env('WAHA_BASE_URL', 'http://72.61.208.130:3001');
        $endpoint = $baseUrl . '/api/sendText';
        $apiKey = env('WAHA_API_KEY', '0f0eb5d196b6459781f7d854aac5050e'); 

        $chatId = preg_replace('/[^0-9]/', '', $number);
        if (substr($chatId, 0, 1) == '0') $chatId = '62' . substr($chatId, 1);
        $chatId .= '@c.us';

        try {
            // [BARU] Simpan respons dari WAHA dan gunakan timeout agar tidak loading lama jika server mati
            $response = Http::timeout(10)->withHeaders(['Content-Type' => 'application/json', 'X-Api-Key' => $apiKey])
                ->post($endpoint, [
                    'session' => 'default',
                    'chatId' => $chatId,
                    'text' => $message
                ]);
            
            // [BARU] Kembalikan nilai true jika berhasil, false jika error
            return $response->successful();
        } catch (\Exception $e) {
            Log::error("WA Gagal: " . $e->getMessage());
            return false;
        }
    }

public function registerBasic(Request $request, $id)
{
    $request->validate([
        'nama_lengkap' => 'required|string|max:255',
        'jenjang' => 'required|string',
        'jenis_kelamin' => 'required|in:L,P',
    ]);

    $verification = \App\Models\Verification::findOrFail($id);

    // Cek apakah data santri dengan nama ini sudah terdaftar sebelumnya
    $exists = Candidate::where('nama_lengkap', $request->nama_lengkap)->first();
    if ($exists) {
        return back()->with('error', 'Santri dengan nama tersebut sudah terdaftar.');
    }

    DB::beginTransaction();
    try {
        // 1. Buat data Candidate awal (data parsial)
        $candidate = Candidate::create([
            'no_daftar' => 'REG-' . date('Y') . date('His'),
            'nama_lengkap' => $request->nama_lengkap,
            'jenjang' => $request->jenjang,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tahun_masuk' => date('Y'),
            'jalur_pendaftaran' => 'Online',
            'status' => 'Baru',
            'file_perjanjian' => $verification->file_perjanjian,
            
            // --- DATA SEMENTARA YANG DIPERBAIKI (PASTI UNIK) ---
            'tempat_lahir' => '-',
            'tanggal_lahir' => date('Y-m-d'),
            
            // Generate 16 digit unik: Tahun(4)Bulan(2)Tanggal(2)Jam(2)Menit(2)Detik(2) + 2 angka acak
            'nik' => date('YmdHis') . rand(10, 99), 
            'no_kk' => date('YmdHis') . rand(10, 99), 
            
            'asal_sekolah' => '-',
            'anak_ke' => 1,
            'jumlah_saudara' => 0,
        ]);

        // 2. Generate Tagihan otomatis berdasarkan jenjang yang dipilih admin
        $biaya = PaymentType::where('jenjang', 'Semua')
                            ->orWhere('jenjang', $request->jenjang)
                            ->get();

        foreach ($biaya as $item) {
            CandidateBill::firstOrCreate(
                [
                    'candidate_id' => $candidate->id,
                    'payment_type_id' => $item->id,
                ],
                [
                    'nominal_tagihan' => $item->nominal,
                    'nominal_terbayar' => 0,
                    'status' => 'Belum Lunas',
                ]
            );
        }

        DB::commit();
        return back()->with('success', 'Berhasil mendaftarkan data awal santri ' . $request->nama_lengkap);

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Gagal mendaftarkan santri: ' . $e->getMessage());
    }
}
}