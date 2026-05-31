<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\CandidateAddress;
use App\Models\CandidateParent;
use App\Models\CandidateBill;
use App\Models\PaymentType;
use App\Models\Setting;
use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class RegistrationController extends Controller
{
    public function showForm($token)
{
    // 1. Cari data verifikasi berdasarkan token dari WA
    $verification = \App\Models\Verification::where('token', $token)->firstOrFail();
    
    // [TAMBAHAN KEAMANAN] Cek apakah pembayaran sudah lunas sebelum mengizinkan isi biodata
    if ($verification->status_pembayaran != 'paid') {
        return redirect()->route('pendaftaran.payment', $token)
                         ->with('error', 'Silakan selesaikan pembayaran pendaftaran terlebih dahulu.');
    }

    // 2. CARI CANDIDATE HANYA BERDASARKAN FILE PERJANJIAN
    $candidateExists = \App\Models\Candidate::where('file_perjanjian', $verification->file_perjanjian)->first();

    // (BLOK PENCARIAN NOMOR HP SUDAH DIHAPUS DARI SINI UNTUK MENCEGAH BUG DOUBLE-SUBMIT)

    // 3. JIKA SUDAH ADA & SUDAH LENGKAP ISI BIODATA, REDIRECT KE HALAMAN SUKSES
    // Patokan lengkap: tempat lahir tidak kosong dan bukan '-'
    if ($candidateExists && !empty($candidateExists->tempat_lahir) && $candidateExists->tempat_lahir != '-') {
        return redirect()->route('pendaftaran.sukses', $candidateExists->no_daftar)
                         ->with('info', 'Anda sudah berhasil mengisi formulir pendaftaran sebelumnya. Data Anda aman!');
    }

    // Tampilkan form biodata
    return view('pendaftaran.index', compact('verification', 'candidateExists'));
}

    public function store(Request $request)
    {
        // 1. CEK STATUS PENDAFTARAN
        $isClosed = Setting::where('key', 'status_ppdb')->value('value') == 'tutup';
        if ($isClosed) {
            return redirect()->route('home')->with('error', 'Mohon maaf, pendaftaran sudah ditutup.');
        }

        $validJenjang = json_decode(Setting::getValue('list_jenjang'), true) ?? ['SMP', 'SMK'];

        // 2. VALIDASI INPUT FORMULIR (DIPERBAIKI)
        $request->validate([
            'token' => 'required',
            'nama_lengkap' => 'required|string|max:255',
            'nisn' => 'nullable|numeric|digits_between:10,12|unique:candidates,nisn', 
            'nik' => 'required|numeric|digits:16|unique:candidates,nik', 
            'jenjang' => ['required', Rule::in($validJenjang)],
            'no_hp_ayah' => 'required|numeric',
            'alamat' => 'required|string',
            'desa' => 'required|string',
            'kecamatan' => 'required|string',
            'kabupaten' => 'required|string',
            'provinsi' => 'required|string',
            'no_kk' => 'required|numeric', 
        ], [
            // PESAN ERROR BAHASA INDONESIA
            'nisn.unique' => 'NISN ini sudah terdaftar sebelumnya. Silakan cek kembali.',
            'nik.unique' => 'NIK ini sudah terdaftar di sistem kami.',
            'nisn.numeric' => 'NISN harus berupa angka tanpa karakter lain.',
            'nik.digits' => 'NIK harus 16 digit.',
            'numeric' => 'Kolom :attribute harus berupa angka tanpa spasi/strip.',
            'required' => 'Kolom :attribute wajib diisi.', // <-- PERBAIKAN: Menambahkan :attribute
        ], [
            // MENTRANSLATE NAMA KOLOM AGAR MUDAH DIBACA USER
            'nama_lengkap' => 'Nama Lengkap',
            'jenjang' => 'Jenjang Pendidikan',
            'no_hp_ayah' => 'Nomor HP / WA Ayah',
            'alamat' => 'Alamat Lengkap',
            'desa' => 'Desa / Kelurahan',
            'kecamatan' => 'Kecamatan',
            'kabupaten' => 'Kabupaten',
            'provinsi' => 'Provinsi',
            'no_kk' => 'Nomor Kartu Keluarga (KK)',
            'nik' => 'NIK',
            'nisn' => 'NISN'
        ]);

        // 3. AMBIL DATA VERIFIKASI
        $verifyData = Verification::where('token', $request->token)->first();
        
        if (!$verifyData) {
            return back()->with('error', 'Token verifikasi tidak valid. Silakan ulangi proses dari awal.');
        }

        DB::beginTransaction();
        try {
            $gajiAyah = preg_replace('/[^0-9]/', '', $request->penghasilan_ayah);
            $gajiIbu = preg_replace('/[^0-9]/', '', $request->penghasilan_ibu);

            // A. SIMPAN / UPDATE DATA SANTRI
            // 1. Kumpulkan semua data form ke dalam variabel array
            $dataSantri = [
                'nisn' => $request->nisn,
                'nik' => $request->nik,
                'no_kk' => $request->no_kk,
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
                'file_perjanjian' => $verifyData->file_perjanjian,
            ];

            // 2. Cari apakah data santri sudah ada (dibuat via admin sebelumnya)
            $candidate = Candidate::where('nama_lengkap', trim($request->nama_lengkap))->first();

            if ($candidate) {
                // JIKA DATA SUDAH ADA (UPDATE)
                $candidate->update($dataSantri);
                
                // Pastikan no_daftar sudah terisi, jika belum buatkan
                if (!$candidate->no_daftar) {
                    $candidate->no_daftar = 'REG-' . date('Y') . date('His');
                    $candidate->save();
                }
            } else {
                // JIKA PENDAFTAR MURNI BARU / ORANG TUA MENGISI SENDIRI (CREATE)
                $dataSantri['nama_lengkap'] = trim($request->nama_lengkap);
                
                // Masukkan no_daftar SEBELUM fungsi create/insert berjalan agar tidak terjadi Error 1364
                $dataSantri['no_daftar'] = 'REG-' . date('Y') . date('His');
                
                $candidate = Candidate::create($dataSantri);
            }

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

            // C. SIMPAN ORANG TUA
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

            // D. GENERATE TAGIHAN
            if ($candidate->wasRecentlyCreated) {
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
            }

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
                ])->post(env('WAHA_BASE_URL', 'http://72.61.208.130:3001') . '/api/sendText', [
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

            // ---------------------------------------------------------
            // G. KIRIM NOTIFIKASI WA KE ADMIN (TAMBAHAN BARU)
            // ---------------------------------------------------------
            try {
                Log::info("--- MULAI KIRIM WA NOTIFIKASI PENDAFTARAN KE ADMIN ---");
                
                // Ambil nomor admin dari .env (Sama seperti yang Anda seting sebelumnya)
                $adminWa = env('ADMIN_WA_NUMBER', '628xxxxxxxxxx'); 
                $adminChatId = $adminWa . '@c.us';

                // Susun Pesan untuk Admin
                $pesanAdmin = "🔔 *NOTIFIKASI PENDAFTARAN BARU*\n\n"
                            . "Assalamu'alaikum Admin,\n"
                            . "Alhamdulillah, ada calon santri baru yang baru saja menyelesaikan pengisian biodata:\n\n"
                            . "👤 Nama: *{$candidate->nama_lengkap}*\n"
                            . "🔖 No. Daftar: *{$candidate->no_daftar}*\n"
                            . "🏫 Jenjang: *{$candidate->jenjang}*\n"
                            . "📱 No. HP Ortu: *{$request->no_hp_ayah}*\n\n"
                            . "Mohon dicek di dashboard admin. Terima kasih.";

                // Kirim Request ke WAHA
                $responseAdmin = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'X-Api-Key'    => env('WAHA_API_KEY', '0f0eb5d196b6459781f7d854aac5050e'),
                ])->post(env('WAHA_BASE_URL', 'http://72.61.208.130:3001') . '/api/sendText', [
                    'session' => 'default',
                    'chatId'  => $adminChatId,
                    'text'    => $pesanAdmin
                ]);

                if ($responseAdmin->successful()) {
                    Log::info("WA Notifikasi Pendaftaran ke Admin Sukses Terkirim!");
                } else {
                    Log::error("WA Notifikasi Pendaftaran ke Admin Gagal! Status: " . $responseAdmin->status());
                }

            } catch (\Exception $eAdmin) {
                Log::error("EXCEPTION WA Admin (Pendaftaran) Error: " . $eAdmin->getMessage());
            }
            // ---------------------------------------------------------

            // Redirect ke Halaman Sukses
            return redirect()->route('pendaftaran.sukses', $candidate->no_daftar);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Error Fatal: " . $e->getMessage());
            // Tampilkan error ke user
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function sukses($no_daftar)
    {
        $candidate = \App\Models\Candidate::where('no_daftar', $no_daftar)->first();
        
        return view('pendaftaran.sukses', compact('no_daftar', 'candidate'));
    }

    // Tampilkan Form Edit Publik
    public function editPublic($no_daftar)
    {
        $candidate = Candidate::with(['address', 'parent'])->where('no_daftar', $no_daftar)->firstOrFail();

        // Cek apakah status sudah Lulus atau Siswa Aktif
        if (in_array($candidate->status, ['Lulus', 'Siswa Aktif'])) {
            return redirect()->route('public.finance.show', $no_daftar)
                ->with('error', 'Maaf, data tidak dapat diedit karena Anda sudah dinyatakan Lulus/Aktif.');
        }

        $validJenjang = json_decode(\App\Models\Setting::getValue('list_jenjang'), true) ?? ['SMP', 'SMK'];
        return view('pendaftaran.edit_public', compact('candidate', 'validJenjang'));
    }

    // Proses Update Data
    public function updatePublic(Request $request, $no_daftar)
    {
        $candidate = Candidate::where('no_daftar', $no_daftar)->firstOrFail();

        // Validasi input
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            
            // PERBAIKAN DI SINI:
            // Cek Unik, tapi abaikan (ignore) data milik santri ini sendiri
            'nisn' => [
                'nullable', 
                'numeric', 
                Rule::unique('candidates', 'nisn')->ignore($candidate->id)
            ],
            'nik' => [
                'nullable', 
                'numeric', 
                Rule::unique('candidates', 'nik')->ignore($candidate->id)
            ],
            
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required',
            'no_hp_ayah' => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {
            // 1. Update Data Utama
            $candidate->update($request->only([
                'nama_lengkap', 
                'nisn',  // Pastikan ini ada
                'nik',   // Pastikan ini ada
                'jenis_kelamin', 
                'tempat_lahir', 
                'tanggal_lahir', 
                'anak_ke', 
                'jumlah_saudara', 
                'riwayat_penyakit', 
                'asal_sekolah'
            ]));

            // 2. Update Alamat
            $candidate->address->update($request->only([
                'alamat', 'rt', 'rw', 'desa', 'kecamatan', 'kabupaten', 'provinsi', 'kode_pos'
            ]));

            // 3. Update Orang Tua
            $candidate->parent->update($request->only([
                'nama_ayah', 'nik_ayah', 'pekerjaan_ayah', 'no_hp_ayah',
                'nama_ibu', 'nik_ibu', 'pekerjaan_ibu', 'no_hp_ibu'
            ]));

            DB::commit();
            return redirect()->route('public.finance.show', $no_daftar)->with('success', 'Data Anda berhasil diperbarui.');
            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }
}