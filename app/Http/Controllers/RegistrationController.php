<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\CandidateAddress;
use App\Models\CandidateParent;
use App\Models\CandidateBill;
use App\Models\PaymentType;
use App\Models\Setting;
use App\Models\Verification; // Import Verification untuk cek token
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http; // Wajib untuk WAHA
use Illuminate\Support\Facades\Log;  // Wajib untuk Log

class RegistrationController extends Controller
{
    public function showForm($token)
    {
        // Cek Token Valid dan Approved
        $verify = Verification::where('token', $token)
                    ->where('status', 'approved')
                    ->first();

        if (!$verify) {
            abort(403, 'Link pendaftaran tidak valid atau sudah kadaluarsa.');
        }

        return view('pendaftaran.index', compact('token'));
    }

    public function store(Request $request)
    {
        // 1. CEK STATUS PENDAFTARAN (Buka/Tutup)
        $isClosed = Setting::where('key', 'status_ppdb')->value('value') == 'tutup';
        if ($isClosed) {
            return redirect()->route('home')->with('error', 'Mohon maaf, pendaftaran sudah ditutup.');
        }

        // 2. VALIDASI INPUT FORMULIR
        $request->validate([
            'token' => 'required', // Token dari hidden input
            'nama_lengkap' => 'required|string|max:255',
            'nisn' => 'required|numeric',
            'nik' => 'required|numeric',
            'jenjang' => 'required|in:SMP,SMK',
            'no_hp_ayah' => 'required|numeric',
            'alamat' => 'required|string',
            'desa' => 'required|string',
            'kecamatan' => 'required|string',
            'kabupaten' => 'required|string',
            'provinsi' => 'required|string',
        ]);

        // 3. AMBIL DATA VERIFIKASI (Untuk dapat path file perjanjian)
        $verifyData = Verification::where('token', $request->token)->first();
        
        // Security Check: Jika token dimanipulasi/hilang
        if (!$verifyData) {
            return back()->with('error', 'Token verifikasi tidak valid. Silakan ulangi proses dari awal.');
        }

        DB::beginTransaction();
        try {
            // BERSIHKAN FORMAT RUPIAH (Hapus titik, koma, Rp)
            $gajiAyah = preg_replace('/[^0-9]/', '', $request->penghasilan_ayah);
            $gajiIbu = preg_replace('/[^0-9]/', '', $request->penghasilan_ibu);

            // A. SIMPAN DATA SANTRI UTAMA
            $candidate = Candidate::create([
                'no_daftar' => 'REG-' . date('Y') . date('His'), // No. Registrasi Otomatis
                'nisn' => $request->nisn,
                'nik' => $request->nik,
                'no_kk' => $request->no_kk,
                'nama_lengkap' => $request->nama_lengkap,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'anak_ke' => $request->anak_ke ?? 1,
                'jumlah_saudara' => $request->jumlah_saudara ?? 0,
                'riwayat_penyakit' => $request->riwayat_penyakit,
                'jenjang' => $request->jenjang,
                'asal_sekolah' => $request->asal_sekolah,
                'tahun_masuk' => date('Y'),
                'jalur_pendaftaran' => 'Online',
                'status' => 'Baru',
                
                // PENTING: Simpan Path File Perjanjian dari Tabel Verifikasi ke Tabel Santri
                'file_perjanjian' => $verifyData->file_perjanjian, 
            ]);

            // B. SIMPAN ALAMAT
            CandidateAddress::create([
                'candidate_id' => $candidate->id,
                'alamat' => $request->alamat,
                'rt' => $request->rt,
                'rw' => $request->rw,
                'desa' => $request->desa,
                'kecamatan' => $request->kecamatan,
                'kabupaten' => $request->kabupaten,
                'provinsi' => $request->provinsi,
                'kode_pos' => $request->kode_pos,
            ]);

            // C. SIMPAN DATA ORANG TUA
            CandidateParent::create([
                'candidate_id' => $candidate->id,
                'nama_ayah' => $request->nama_ayah,
                'nik_ayah' => $request->nik_ayah,
                'pekerjaan_ayah' => $request->pekerjaan_ayah,
                'penghasilan_ayah' => (int) $gajiAyah,
                'no_hp_ayah' => $request->no_hp_ayah,
                
                'nama_ibu' => $request->nama_ibu,
                'nik_ibu' => $request->nik_ibu,
                'pekerjaan_ibu' => $request->pekerjaan_ibu,
                'penghasilan_ibu' => (int) $gajiIbu,
                'no_hp_ibu' => $request->no_hp_ibu,
            ]);

            // D. GENERATE TAGIHAN AWAL (Berdasarkan Jenjang)
            $biaya = PaymentType::where('jenjang', 'Semua')
                        ->orWhere('jenjang', $request->jenjang)
                        ->get();

            foreach ($biaya as $item) {
                CandidateBill::create([
                    'candidate_id' => $candidate->id,
                    'payment_type_id' => $item->id,
                    'nominal_tagihan' => $item->nominal,
                    'nominal_terbayar' => 0,
                    'status' => 'Belum Lunas',
                ]);
            }

            // Simpan Perubahan ke Database
            DB::commit();

            // ---------------------------------------------------------
            // F. KIRIM NOTIFIKASI WHATSAPP (LOGIKA BARU)
            // ---------------------------------------------------------
            try {
                Log::info("--- MULAI KIRIM WA SUKSES DAFTAR ---");

                // 1. Format Nomor HP (08 -> 628)
                $rawNo = $request->no_hp_ayah;
                $cleanNo = preg_replace('/[^0-9]/', '', $rawNo); // Hapus karakter selain angka
                
                if (substr($cleanNo, 0, 1) == '0') {
                    $cleanNo = '62' . substr($cleanNo, 1);
                } elseif (substr($cleanNo, 0, 2) != '62') {
                    // Jaga-jaga jika user input tanpa 0 (misal: 812345)
                    $cleanNo = '62' . $cleanNo;
                }
                
                $chatId = $cleanNo . '@c.us';
                Log::info("Tujuan WA: " . $chatId);

                // 2. Ambil Data Setting Sekolah
                $namaSekolah = Setting::where('key', 'nama_sekolah')->value('value') ?? 'Pondok Pesantren';
                $syaratJson = Setting::where('key', 'syarat_pendaftaran')->value('value');
                $syaratList = json_decode($syaratJson, true) ?? [];

                // 3. Susun List Persyaratan
                $listBerkas = "";
                if (!empty($syaratList)) {
                    foreach ($syaratList as $index => $item) {
                        $no = $index + 1;
                        $listBerkas .= "{$no}. {$item['nama']} ({$item['jumlah']} rangkap)\n";
                    }
                } else {
                    $listBerkas = "- (Silakan hubungi panitia untuk info berkas)\n";
                }

                // 4. Susun Pesan WA
                $pesanWA = "Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\n"
                         . "Yth. Bapak/Ibu Wali Santri,\n"
                         . "Alhamdulillah, pendaftaran calon santri baru atas nama:\n\n"
                         . "👤 Nama: *{$candidate->nama_lengkap}*\n"
                         . "🔖 No. Registrasi: *{$candidate->no_daftar}*\n"
                         . "🏫 Jenjang: *{$candidate->jenjang}*\n\n"
                         . "Telah berhasil kami terima di sistem database *{$namaSekolah}*.\n\n"
                         . "------------------------------------------------\n"
                         . "📋 *INFORMASI TAHAP SELANJUTNYA*\n"
                         . "------------------------------------------------\n"
                         . "Mohon simpan bukti pendaftaran ini. Selanjutnya, silakan datang ke sekretariat {$namaSekolah} untuk validasi fisik dan tes seleksi dengan membawa berkas sebagai berikut:\n\n"
                         . $listBerkas . "\n"
                         . "📍 *Alamat:* Sekretariat PPDB {$namaSekolah}\n\n"
                         . "Terima kasih atas kepercayaan Bapak/Ibu.\n"
                         . "Wassalamu'alaikum Warahmatullahi Wabarakatuh.";

                // 5. Kirim Request ke WAHA
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'X-Api-Key'    => env('WAHA_API_KEY', '0f0eb5d196b6459781f7d854aac5050e'),
                ])->post(env('WAHA_BASE_URL', 'http://72.61.208.130:3000') . '/api/sendText', [
                    'session' => 'default',
                    'chatId'  => $chatId,
                    'text'    => $pesanWA
                ]);

                // 6. Log Hasil Pengiriman
                if ($response->successful()) {
                    Log::info("WA Sukses Terkirim! Response: " . $response->body());
                } else {
                    Log::error("WA Gagal Terkirim! Status: " . $response->status() . " | Body: " . $response->body());
                }

            } catch (\Exception $waError) {
                // Tangkap error koneksi WA agar tidak menggagalkan pendaftaran
                Log::error("EXCEPTION WA Error: " . $waError->getMessage());
            }
            // ---------------------------------------------------------

            // Redirect ke Halaman Sukses
            return redirect()->route('pendaftaran.sukses', ['no_daftar' => $candidate->no_daftar]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Error Fatal Pendaftaran: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage())->withInput();
        }
    }

    public function sukses($no_daftar)
    {
        return view('pendaftaran.sukses', compact('no_daftar'));
    }
}