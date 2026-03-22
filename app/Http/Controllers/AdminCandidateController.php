<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use Illuminate\Http\Request;
use App\Models\CandidateAddress;
use App\Models\CandidateParent;
use App\Models\PaymentType;
use App\Models\CandidateBill;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use App\Exports\CandidatesExport; 
use Maatwebsite\Excel\Facades\Excel; 
use Illuminate\Support\Facades\Http; 
use Illuminate\Support\Facades\Log;  

class AdminCandidateController extends Controller
{
    public function index(Request $request)
    {
        $query = Candidate::query();

        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->search . '%')
                  ->orWhere('no_daftar', 'like', '%' . $request->search . '%')
                  ->orWhere('nisn', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('jenjang') && $request->jenjang != 'Semua') {
            $query->where('jenjang', $request->jenjang);
        }

        if ($request->has('status') && $request->status != 'Semua') {
            if ($request->status == 'Lulus') {
                $query->whereIn('status_seleksi', ['Lulus', 'Diterima', 'Approved']); 
            } else {
                $query->where('status_seleksi', $request->status);
            }
        }

        // Hapus with('dormitory') jika belum perlu ditampilkan di tabel ini
        $candidates = $query->latest()->paginate(10)->withQueryString();

        $kpi = [
            'total' => Candidate::count(),
            'laki' => Candidate::where('jenis_kelamin', 'L')->count(),
            'perempuan' => Candidate::where('jenis_kelamin', 'P')->count(),
            'pending' => Candidate::where('status_seleksi', 'Pending')->count(),
            'diterima' => Candidate::whereIn('status_seleksi', ['Lulus', 'Diterima'])->count(),
        ];

        $jenjangs = json_decode(Setting::getValue('list_jenjang'), true) ?? ['SMP', 'SMK'];

        return view('admin.candidates.index', compact('candidates', 'kpi', 'jenjangs'));
    }

    public function export()
    {
        return Excel::download(new CandidatesExport, 'Data-Santri-' . date('Y-m-d') . '.xlsx');
    }

    public function create()
    {
        $jenjangs = json_decode(Setting::getValue('list_jenjang'), true) ?? ['SMP', 'SMK'];
        return view('admin.candidates.create', compact('jenjangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required',
            'jenis_kelamin' => 'required|in:L,P',
            'jenjang' => 'required',
            'nisn' => 'nullable|unique:candidates,nisn', 
            'nik' => 'nullable|unique:candidates,nik',
        ]);

        DB::beginTransaction();

        try {
            // [KOREKSI] SAYA HAPUS LOGIKA AUTO ASRAMA DISINI
            // Karena penetapan asrama baru dilakukan setelah interview

            $candidate = Candidate::create([
                'no_daftar' => 'OFF-' . date('Y') . date('His'),
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
                'asal_sekolah' => $request->asal_sekolah ?? '-', 
                'tahun_masuk' => date('Y'),
                'jalur_pendaftaran' => 'Offline',
                'status_seleksi' => 'Lulus', // Lulus Administrasi/Daftar
            ]);

            CandidateAddress::create([
                'candidate_id' => $candidate->id,
                'alamat' => $request->alamat,
                'rt' => $request->rt,
                'rw' => $request->rw,
                'desa' => $request->desa,
                'kecamatan' => $request->kecamatan,
                'kode_pos' => $request->kode_pos,
                'kabupaten' => $request->kabupaten,
                'provinsi' => $request->provinsi,
            ]);

            CandidateParent::create([
                'candidate_id' => $candidate->id,
                'nama_ayah' => $request->nama_ayah,
                'nik_ayah' => $request->nik_ayah,
                'pekerjaan_ayah' => $request->pekerjaan_ayah,
                'penghasilan_ayah' => $request->penghasilan_ayah ?? 0,
                'no_hp_ayah' => $request->no_hp_ayah,
                'nama_ibu' => $request->nama_ibu,
                'nik_ibu' => $request->nik_ibu,
                'pekerjaan_ibu' => $request->pekerjaan_ibu,
                'penghasilan_ibu' => $request->penghasilan_ibu ?? 0,
                'no_hp_ibu' => $request->no_hp_ibu,
            ]);

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

            DB::commit();

            $candidate->load('parent');
            
            // Kirim WA Notifikasi (Lulus Administrasi)
            $this->checkAndSendWA($candidate, 'Pending', 'Lulus');

            return redirect()->route('admin.candidates.show', $candidate->id)->with('success', 'Pendaftaran Offline Berhasil!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        // Tetap load dormitory jaga-jaga kalau sudah diassign di tahap interview nanti
        $candidate = Candidate::with(['address', 'parent', 'bills.payment_type', 'transactions', 'dormitory'])->findOrFail($id);
        return view('admin.candidates.show', compact('candidate'));
    }
    
    // --- [1] LOGIKA UPDATE STATUS (TOMBOL KHUSUS) ---
    public function updateStatus(Request $request, $id)
    {
        $candidate = Candidate::with('parent')->findOrFail($id);
        
        $oldStatus = $candidate->status_seleksi; 
        $newStatus = $request->status_seleksi;   

        // [KOREKSI] HAPUS AUTO ASSIGN ASRAMA DI SINI
        // Karena Admin hanya meluluskan Administrasi. Asrama nanti via Interview.

        $candidate->status_seleksi = $newStatus;
        $candidate->save();

        $this->checkAndSendWA($candidate, $oldStatus, $newStatus);

        return back()->with('success', 'Status santri diperbarui.');
    }

    public function edit($id)
    {
        $candidate = Candidate::with(['address', 'parent'])->findOrFail($id);
        $jenjangs = json_decode(Setting::getValue('list_jenjang'), true) ?? ['SMP', 'SMK']; 
        return view('admin.candidates.edit', compact('candidate', 'jenjangs'));
    }

    // --- [2] LOGIKA UPDATE DATA (EDIT FORM LENGKAP) ---
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_lengkap' => 'required',
            'jenjang' => 'required',
            'nisn' => 'nullable|unique:candidates,nisn,'.$id, 
            'nik' => 'nullable|unique:candidates,nik,'.$id,
            'asal_sekolah' => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $candidate = Candidate::with('parent')->findOrFail($id);
            $oldStatus = $candidate->status_seleksi;

            $candidate->update([
                'nama_lengkap' => $request->nama_lengkap,
                'nisn' => $request->nisn,
                'nik' => $request->nik,
                'no_kk' => $request->no_kk,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'anak_ke' => $request->anak_ke,
                'jumlah_saudara' => $request->jumlah_saudara,
                'riwayat_penyakit' => $request->riwayat_penyakit,
                'jenjang' => $request->jenjang,
                'asal_sekolah' => $request->asal_sekolah,
                'status_seleksi' => $request->status_seleksi ?? $candidate->status_seleksi, 
            ]);

            $newStatus = $candidate->status_seleksi;

            $candidate->address()->update([
                'alamat' => $request->alamat,
                'rt' => $request->rt,
                'rw' => $request->rw,
                'desa' => $request->desa,
                'kecamatan' => $request->kecamatan,
                'kabupaten' => $request->kabupaten,
                'provinsi' => $request->provinsi,
                'kode_pos' => $request->kode_pos,
            ]);

            $candidate->parent()->update([
                'nama_ayah' => $request->nama_ayah,
                'nik_ayah' => $request->nik_ayah,
                'pekerjaan_ayah' => $request->pekerjaan_ayah,
                'penghasilan_ayah' => $request->penghasilan_ayah ?? 0,
                'no_hp_ayah' => $request->no_hp_ayah,
                'nama_ibu' => $request->nama_ibu,
                'nik_ibu' => $request->nik_ibu,
                'pekerjaan_ibu' => $request->pekerjaan_ibu,
                'penghasilan_ibu' => $request->penghasilan_ibu ?? 0,
                'no_hp_ibu' => $request->no_hp_ibu,
            ]);

            DB::commit();

            $this->checkAndSendWA($candidate, $oldStatus, $newStatus);

            return redirect()->route('admin.candidates.show', $id)->with('success', 'Data santri berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    // --- HELPER LOGIKA WA ---
    private function checkAndSendWA($candidate, $oldStatus, $newStatus)
    {
        Log::info("Cek Kirim WA: Old={$oldStatus}, New={$newStatus}");
        
        $old = strtolower($oldStatus);
        $new = strtolower($newStatus);

        if (in_array($new, ['lulus', 'diterima', 'approved']) && !in_array($old, ['lulus', 'diterima', 'approved'])) {
            $this->sendWhatsAppNotification($candidate);
        }
    }

    // --- FUNGSI KIRIM API ---
    private function sendWhatsAppNotification($candidate)
    {
        try {
            $rawNo = $candidate->parent->no_hp_ayah ?? $candidate->parent->no_hp_ibu;
            
            if (empty($rawNo)) {
                Log::warning("Gagal Kirim WA: No HP Kosong untuk ID: " . $candidate->id);
                return;
            }

            $cleanNo = preg_replace('/[^0-9]/', '', $rawNo); 
            if (substr($cleanNo, 0, 1) == '0') {
                $cleanNo = '62' . substr($cleanNo, 1);
            } elseif (substr($cleanNo, 0, 2) != '62') {
                $cleanNo = '62' . $cleanNo;
            }
            $chatId = $cleanNo . '@c.us';

            $namaSekolah = Setting::where('key', 'nama_sekolah')->value('value') ?? 'Pondok Pesantren';
            
            // [KOREKSI] HANYA AMBIL LINK GRUP PONDOK (UMUM)
            // TIDAK ADA LOGIKA ASRAMA DISINI
            $linkGrupPondok = Setting::where('key', 'link_grup_wa_pondok')->value('value');

            // Susun Info Tambahan
            $infoTambahan = "";
            
            // Info Grup Pondok (Jika ada)
            if (!empty($linkGrupPondok)) {
                $infoTambahan .= "🔗 Grup Info Pondok: {$linkGrupPondok}\n";
            }

            $pesanWA = "Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\n"
                     . "Yth. Bapak/Ibu Wali Santri,\n"
                     . "Kami ucapkan *SELAMAT!* Berdasarkan verifikasi data, calon santri:\n\n"
                     . "👤 Nama: *{$candidate->nama_lengkap}*\n"
                     . "📝 No. Daftar: *{$candidate->no_daftar}*\n"
                     . "🎓 Jenjang: *{$candidate->jenjang}*\n"
                     . "Dinyatakan *LULUS ADMINISTRASI* di *{$namaSekolah}*.\n\n"
                     . $infoTambahan . "\n"
                     . "------------------------------------------------\n"
                     . "ℹ️ *TAHAP SELANJUTNYA*\n"
                     . "------------------------------------------------\n"
                     . "Silakan mengikuti tahapan seleksi tes/interview sesuai jadwal yang ditentukan.\n\n"
                     . "Terima kasih.\n"
                     . "Wassalamu'alaikum Warahmatullahi Wabarakatuh.";

            Log::info("Mencoba kirim WA ke: " . $chatId);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Api-Key'    => env('WAHA_API_KEY', '0f0eb5d196b6459781f7d854aac5050e'), 
            ])->post(env('WAHA_BASE_URL', 'http://72.61.208.130:3003') . '/api/sendText', [
                'session' => 'default',
                'chatId'  => $chatId,
                'text'    => $pesanWA
            ]);

            if ($response->successful()) {
                Log::info("WA Sukses Terkirim! Response: " . $response->body());
            } else {
                Log::error("WA Gagal Terkirim API! Status: " . $response->status() . " Body: " . $response->body());
            }

        } catch (\Exception $e) {
            Log::error("EXCEPTION WA Error: " . $e->getMessage());
        }
    }

    public function printCard(Request $request, $id)
    {
        $candidate = Candidate::with(['address', 'parent'])->findOrFail($id);
        $settings = Setting::all()->pluck('value', 'key');
        
        $jenisSurat = $request->query('type', 'tes'); 

        return view('admin.candidates.print_card', compact('candidate', 'settings', 'jenisSurat'));
    }

    public function destroy($id)
    {
        try {
            $candidate = Candidate::findOrFail($id);
            
            // Hapus data (otomatis trigger 'booted' di Model untuk hapus relasi)
            $candidate->delete();

            // [PERBAIKAN DISINI]
            // Jangan gunakan back(), karena halaman detailnya sudah hilang.
            // Gunakan redirect ke route index (Halaman Tabel Data Santri)
            return redirect()->route('admin.candidates.index')
                ->with('success', 'Data santri berhasil dihapus permanen.');

        } catch (\Exception $e) {
            // Jika error, baru kita kembalikan ke halaman sebelumnya
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function sendBillNotification($id)
    {
        // 1. Ambil Data Santri
        $candidate = Candidate::with(['bills', 'parent'])->findOrFail($id);

        // 2. Hitung Sisa Tagihan
        $totalTagihan = $candidate->bills->sum('nominal_tagihan');
        $totalTerbayar = $candidate->bills->sum('nominal_terbayar');
        $sisaTagihan = $totalTagihan - $totalTerbayar;

        if ($sisaTagihan <= 0) {
            return back()->with('error', 'Tagihan santri ini sudah lunas.');
        }

        // 3. LOGIKA PENENTUAN NOMOR WA (Sesuai Request)
        // Cek tabel verification berdasarkan file_perjanjian yang sama
        $verification = \App\Models\Verification::where('file_perjanjian', $candidate->file_perjanjian)->first();
        
        $targetNo = null;
        $sumberNomor = '';

        if ($verification && !empty($verification->no_wa)) {
            $targetNo = $verification->no_wa;
            $sumberNomor = 'Data Verifikasi Awal';
        } elseif (!empty($candidate->parent->no_hp_ibu)) {
            $targetNo = $candidate->parent->no_hp_ibu;
            $sumberNomor = 'Data Ibu';
        } elseif (!empty($candidate->parent->no_hp_ayah)) {
            $targetNo = $candidate->parent->no_hp_ayah;
            $sumberNomor = 'Data Ayah';
        }

        // 4. VALIDASI & PEMBERSIHAN NOMOR (PENTING AGAR TIDAK ERROR 500)
        // Hapus semua karakter selain angka
        $cleanNo = preg_replace('/[^0-9]/', '', $targetNo);

        // Cek jika nomor terlalu pendek (kurang dari 10 digit itu tidak wajar)
        if (empty($cleanNo) || strlen($cleanNo) < 10) {
            return back()->with('error', "Gagal: Nomor WA tidak valid atau kosong. (Sumber: $sumberNomor)");
        }

        // Format ke 62 (Standar Internasional)
        if (substr($cleanNo, 0, 1) == '0') {
            $cleanNo = '62' . substr($cleanNo, 1);
        } elseif (substr($cleanNo, 0, 2) != '62') {
            $cleanNo = '62' . $cleanNo;
        }
        
        $chatId = $cleanNo . '@c.us';

        // 5. Ambil Pengaturan
        $namaSekolah = Setting::where('key', 'nama_sekolah')->value('value') ?? 'Pondok Pesantren';
        $waAdmin     = Setting::where('key', 'whatsapp_admin')->value('value') ?? '-';
        $noRekening  = Setting::where('key', 'info_rekening')->value('value') ?? '(Hubungi Admin)';
        
        // Nama Wali (Untuk sapaan)
        $namaWali = $candidate->parent->nama_ayah ?? 'Wali Santri';

        // 6. Susun Pesan (Gunakan Emoji Standar Saja)
        $pesan = "Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\n"
               . "Yth. Bapak/Ibu *{$namaWali}*,\n"
               . "Berikut informasikan tagihan pembayaran santri:\n\n"
               . "👤 Nama: *{$candidate->nama_lengkap}*\n"
               . "📝 No. Daftar: {$candidate->no_daftar}\n"
               . "💰 Sisa Tagihan: *Rp " . number_format($sisaTagihan, 0, ',', '.') . "*\n\n"
               . "Mohon pelunasan ditransfer ke:\n"
               . "🏦 *{$noRekening}*\n\n"
               . "Konfirmasi bukti bayar ke Admin:\n"
               . "📞 wa.me/{$waAdmin}\n\n"
               . "Terima kasih.\n"
               . "_{$namaSekolah}_";

        // 7. Kirim & Debugging
        try {
            // Log dulu sebelum kirim untuk cek di storage/logs/laravel.log
            Log::info("Mencoba kirim Tagihan WA ke: $chatId ($sumberNomor)");

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Api-Key'    => env('WAHA_API_KEY', '0f0eb5d196b6459781f7d854aac5050e'),
            ])->timeout(10)->post(env('WAHA_BASE_URL', 'http://72.61.208.130:3003') . '/api/sendText', [
                'session' => 'default',
                'chatId'  => $chatId,
                'text'    => $pesan
            ]);

            if ($response->successful()) {
                return back()->with('success', "Sukses mengirim tagihan ke nomor $sumberNomor ($cleanNo).");
            } else {
                // Log error detail dari WAHA
                Log::error("WAHA Error {$response->status()}: " . $response->body());
                return back()->with('error', "Gagal kirim WA (Status {$response->status()}). Cek Log untuk detail.");
            }
        } catch (\Exception $e) {
            Log::error("Koneksi WA Gagal: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem saat mengirim WA.');
        }
    }

    public function createLanjutan()
    {
        // Tampilkan form sederhana khusus santri lanjutan
        return view('admin.candidates.create_lanjutan');
    }

    public function storeLanjutan(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nis_lokal'    => 'required|string|max:50',
            'jenjang'      => 'required|string', 
            'jenis_kelamin'=> 'required|string'
        ]);

        // 1. Buat Data Kandidat 
        $candidate = Candidate::create([
            'jalur'             => 'lanjutan',
            'nis_lokal'         => $request->nis_lokal,
            'nama_lengkap'      => $request->nama_lengkap,
            'jenis_kelamin'     => $request->jenis_kelamin,
            'jenjang'           => $request->jenjang,
            'status_seleksi'    => 'Diterima', 
            'status'            => 'Lulus',    
            'no_daftar'         => 'LJT-' . date('Y') . date('His'),
            'tahun_masuk'       => date('Y'),
            'jalur_pendaftaran' => 'Offline',
            'tempat_lahir'      => '-', 
            'tanggal_lahir'     => date('Y-m-d'), 
            'anak_ke'           => 1, 
            'jumlah_saudara'    => 0, 
            'asal_sekolah'      => 'Internal Pondok', 
        ]);

        // ====================================================================
        // 2. Mengambil Tagihan Khusus "Lanjutan" & Tagihan "Semua"
        // ====================================================================
        
        $paymentTypes = PaymentType::where(function($query) use ($request) {
            // KONDISI 1: Ambil tagihan yang namanya ada kata "Lanjutan" DAN jenjangnya cocok (misal: SMA)
            $query->where('nama_pembayaran', 'LIKE', '%Lanjutan%')
                  ->where('jenjang', $request->jenjang);
                  
        })->orWhere(function($query) {
            // KONDISI 2: ATAU ambil tagihan yang jenjangnya diset "Semua" (apapun nama pembayarannya)
            $query->where('jenjang', 'Semua');
            
        })->get();

        // Looping untuk memasukkan semua tagihan yang ditemukan ke database
        foreach ($paymentTypes as $paymentType) {
            CandidateBill::create([
                'candidate_id'     => $candidate->id,
                'payment_type_id'  => $paymentType->id,
                'nominal_tagihan'  => $paymentType->nominal, 
                'nominal_terbayar' => 0, 
                'status'           => 'Belum Lunas'
            ]);
        }
        // ====================================================================

        return redirect()->route('admin.candidates.index')->with('success', 'Santri Lanjutan berhasil ditambahkan dan tagihan telah dibuat!');
    }
}