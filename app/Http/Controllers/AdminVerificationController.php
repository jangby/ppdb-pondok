<?php

namespace App\Http\Controllers;

use App\Models\Verification;
use App\Models\Setting;
use App\Models\Candidate;
use App\Models\CandidateBill;
use App\Models\PaymentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AdminVerificationController extends Controller
{
    /**
     * Halaman Utama Antrian Verifikasi (Index)
     */
    public function index(Request $request)
    {
        // 1. Ambil Filter Status
        $filter = $request->query('status', 'pending');

        // 2. Query Utama untuk Tabel (Sesuai Filter)
        $query = Verification::latest();

        if ($filter == 'pending') {
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

        // 3. Hitung KPI / Statistik Global
        $stats = [
            'berkas_pending' => Verification::where('status', 'pending')->count(),
            'bayar_pending'  => Verification::where('status_pembayaran', 'pending')->count(),
            'selesai'        => Verification::where('status', 'approved')->where('status_pembayaran', 'paid')->count(),
            'ditolak'        => Verification::where('status', 'rejected')->orWhere('status_pembayaran', 'rejected')->count(),
        ];
        
        $stats['total_antrian'] = $stats['berkas_pending'] + $stats['bayar_pending'];

        return view('admin.verifications.index', compact('verifications', 'filter', 'stats'));
    }

    /**
     * Menyetujui Berkas atau Pembayaran (ACC)
     */
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
                $waSent ? 'Berkas DISETUJUI. Pesan tagihan otomatis telah dikirim via Bot.' : 'Berkas DISETUJUI, namun notifikasi WA GAGAL. Pastikan aplikasi Bot aktif.'
            );
        }

        // ==========================================================
        // SKENARIO B: MENYETUJUI BUKTI PEMBAYARAN (Tahap 2 via Upload)
        // ==========================================================
        if ($data->status == 'approved' && $data->status_pembayaran == 'pending') {
            $data->update(['status_pembayaran' => 'paid']);

            if ($data->candidate ?? false) {
                $candidate = $data->candidate;
                $candidate->update(['status_seleksi' => 'Lulus Administrasi']);

                // Alokasi Kamar/Ruang Ujian Santri secara adil (Acak seimbang)
                if (!$candidate->santri_room_id) {
                    $targetSantri = \App\Models\TestRoom::where('jenis', 'Santri')
                                ->withCount('candidates_santri')
                                ->orderBy('candidates_santri_count', 'asc')
                                ->first();
                    if ($targetSantri) $candidate->update(['santri_room_id' => $targetSantri->id]);
                }

                // Alokasi Kamar/Ruang Ujian Wali
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
                $waSent ? 'Pembayaran DITERIMA. Link formulir biodata telah terkirim.' : 'Pembayaran DITERIMA, tetapi WA GAGAL terkirim.'
            );
        }

        // ==========================================================
        // SKENARIO C: MENERIMA PEMBAYARAN CASH LANGSUNG (Offline)
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
                $waSent ? 'Pembayaran CASH Berhasil dicatat. Link pengisian data telah dikirim.' : 'Pembayaran CASH dicatat, WA GAGAL.'
            );
        }

        return back()->with('error', 'Status data tidak valid untuk disetujui.');
    }

    /**
     * Menolak Berkas / Bukti Transfer (Reject)
     */
    public function reject(Request $request, $id)
    {
        $data = Verification::findOrFail($id);
        $alasan = $request->input('alasan', 'Dokumen tidak sesuai.');

        // JIKA MENOLAK BERKAS PERJANJIAN
        if ($data->status == 'pending') {
            $data->update(['status' => 'rejected']);
            return back()->with('success', 'Berkas Surat Perjanjian pendaftar telah ditolak.');
        }

        // JIKA MENOLAK BUKTI PEMBAYARAN
        if ($data->status_pembayaran == 'pending') {
            $data->update([
                'status_pembayaran' => 'rejected',
                'catatan_pembayaran' => $alasan
            ]);
            
            // Kirim WA Notifikasi Ditolak agar upload ulang
            $this->sendPaymentRejectedNotification($data, $alasan);

            return back()->with('success', 'Bukti Pembayaran DITOLAK. Notifikasi panduan upload ulang dikirim ke WA.');
        }

        return back();
    }

    /**
     * Fitur Manual Kirim Ulang Pesan WhatsApp (Resend)
     */
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

        return back()->with('error', 'Masih gagal mengirim WA. Pastikan server Bot internal Anda menyala.');
    }

    // =========================================================================
    // PRIVATE HELPER: STRUKTUR STRINGS TEKS NOTIFIKASI PPDB
    // =========================================================================

    private function sendPaymentNotification($data)
    {
        $linkBayar = route('pendaftaran.payment', ['token' => $data->token]);
        $namaSekolah = Setting::getValue('nama_sekolah', 'Pondok Pesantren');
        $rekening = Setting::getValue('info_rekening', 'Hubungi Admin');
        
        $biayaRaw = json_decode(Setting::getValue('biaya_pendaftaran', '[]'), true);
        $listBiaya = "";
        
        if (!empty($biayaRaw)) {
            foreach ($biayaRaw as $jenjang => $nominal) {
                $listBiaya .= "• {$jenjang}: Rp " . number_format($nominal, 0, ',', '.') . "\n";
            }
        } else {
            $listBiaya = "Hubungi Admin untuk info biaya.\n";
        }

        $pesan = "Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\n"
               . "Berkas Perjanjian Anda telah *DISETUJUI* oleh Admin {$namaSekolah}. ✔️\n\n"
               . "Langkah selanjutnya, mohon lakukan *PEMBAYARAN PENDAFTARAN*.\n\n"
               . "*Rincian Biaya:*\n"
               . "{$listBiaya}\n"
               . "*Rekening Tujuan Transfer:*\n"
               . "{$rekening}\n\n"
               . "Setelah transfer, silakan *UPLOAD BUKTI REKENING* melalui link berikut:\n"
               . "{$linkBayar}\n\n"
               . "Mohon segera diselesaikan agar sistem dapat membuka formulir biodata rahasia santri. Syukron.";

        return $this->sendWA($data->no_wa, $pesan);
    }

    private function sendBioLinkNotification($data)
    {
        $linkForm = route('pendaftaran.form', ['token' => $data->token]);
        $namaSekolah = Setting::getValue('nama_sekolah', 'Pondok Pesantren');
        
        $pesan = "✨ *ALHAMDULILLAH! VERIFIKASI SELESAI* ✨\n\n"
               . "Pembayaran pendaftaran Anda telah *DITERIMA & SAH TERVERIFIKASI* oleh bendahara.\n\n"
               . "Silakan melengkapi *FORMULIR BIODATA UTAMA SANTRI* melalui link aman berikut:\n"
               . "{$linkForm}\n\n"
               . "_(Mohon data diisi dengan jujur, teliti, dan berkas lengkap)_\n\n"
               . "💡 *PILIHAN PRAKTIS:*\n"
               . "Jika Anda ingin mengisi biodata otomatis langsung lewat ruang chat WhatsApp ini, silakan salin (copy) dan kirim format perintah di bawah ini:\n\n"
               . "*.daftar {$data->token}*\n\n"
               . "Salam hangat - Panitia PPDB {$namaSekolah}";

        return $this->sendWA($data->no_wa, $pesan);
    }

    private function sendPaymentRejectedNotification($data, $alasan)
    {
        $linkBayar = route('pendaftaran.payment', ['token' => $data->token]);
        $namaSekolah = Setting::getValue('nama_sekolah', 'Pondok Pesantren');

        $pesan = "⚠️ *PEMBERITAHUAN REVISI TRANSFER* ⚠️\n\n"
               . "Mohon maaf, bukti pembayaran yang Anda kirim *DITOLAK* oleh Admin {$namaSekolah}.\n\n"
               . "*Alasan Penolakan:* _\"{$alasan}\"_\n\n"
               . "Silakan lakukan upload ulang dokumen bukti transfer yang valid/jelas melalui tautan resmi ini:\n"
               . "{$linkBayar}";

        return $this->sendWA($data->no_wa, $pesan);
    }

    /**
     * CORE TRANSMITTER: Mengirimkan Teks Pesan ke Server Bot Node.js (Port 5000)
     */
    private function sendWA($number, $message)
    {
        // TARGET WEBHOOK: Menuju port internal Bot Baileys Anda
        $endpoint = 'http://127.0.0.1:5000/api/send-message';

        // Bersihkan format nomor HP pendaftar
        $chatId = preg_replace('/[^0-9]/', '', $number);
        if (substr($chatId, 0, 1) == '0') {
            $chatId = '62' . substr($chatId, 1);
        }

        try {
            // Kirim paket data teks lengkap menggunakan Http Client Laravel (Timeout 7 detik)
            $response = Http::timeout(7)->post($endpoint, [
                'no_wa' => $chatId,
                'pesan' => $message
            ]);
            
            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Koneksi Webhook ke Bot WA Gagal: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mendaftarkan Data Kasar Awal Santri Baru (Manual Input)
     */
    public function registerBasic(Request $request, $id)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'jenjang' => 'required|string',
            'jenis_kelamin' => 'required|in:L,P',
        ]);

        $verification = Verification::findOrFail($id);

        $exists = Candidate::where('nama_lengkap', $request->nama_lengkap)->first();
        if ($exists) {
            return back()->with('error', 'Santri dengan nama tersebut sudah terdaftar.');
        }

        DB::beginTransaction();
        try {
            $candidate = Candidate::create([
                'no_daftar' => 'REG-' . date('Y') . date('His'),
                'nama_lengkap' => $request->nama_lengkap,
                'jenjang' => $request->jenjang,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tahun_masuk' => date('Y'),
                'jalur_pendaftaran' => 'Online',
                'status' => 'Baru',
                'file_perjanjian' => $verification->file_perjanjian,
                'tempat_lahir' => '-',
                'tanggal_lahir' => date('Y-m-d'),
                'nik' => date('YmdHis') . rand(10, 99), 
                'no_kk' => date('YmdHis') . rand(10, 99), 
                'asal_sekolah' => '-',
                'anak_ke' => 1,
                'jumlah_saudara' => 0,
            ]);

            // Pembuatan invoice tagihan otomatis berdasar jenjang pilihan admin
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

    /**
     * Menghapus Permanen Data Sampah / Testing (Cascading Clean)
     */
    public function destroy($id)
    {
        $data = Verification::findOrFail($id);
        
        $candidate = Candidate::where('file_perjanjian', $data->file_perjanjian)->first();
        if ($candidate) {
            $candidate->delete();
        }

        if (!empty($data->file_perjanjian)) {
            \Illuminate\Support\Facades\Storage::delete($data->file_perjanjian);
        }
        if (!empty($data->bukti_bayar)) {
            \Illuminate\Support\Facades\Storage::delete($data->bukti_bayar);
        }

        $data->delete();

        return back()->with('success', 'Data pendaftar sampah dan seluruh berkas terkait berhasil dibersihkan permanen!');
    }
}