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
use Illuminate\Support\Facades\Http; // [WAJIB]
use Illuminate\Support\Facades\Log;  // [WAJIB]

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
            'jenis_kelamin' => 'required',
            'jenjang' => 'required',
            'nisn' => 'nullable|unique:candidates,nisn', 
            'nik' => 'nullable|unique:candidates,nik',
        ]);

        DB::beginTransaction();

        try {
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
                'status_seleksi' => 'Lulus', 
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
            return redirect()->route('admin.candidates.show', $candidate->id)->with('success', 'Pendaftaran Offline Berhasil!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $candidate = Candidate::with(['address', 'parent', 'bills.payment_type', 'transactions'])->findOrFail($id);
        return view('admin.candidates.show', compact('candidate'));
    }
    
    // --- [1] LOGIKA UPDATE STATUS (TOMBOL KHUSUS) ---
    public function updateStatus(Request $request, $id)
    {
        $candidate = Candidate::with('parent')->findOrFail($id);
        
        $oldStatus = $candidate->status_seleksi; // Status Lama
        $newStatus = $request->status_seleksi;   // Status Baru dari Input

        // Update Database
        $candidate->update(['status_seleksi' => $newStatus]);

        // CEK PERUBAHAN STATUS -> KIRIM WA
        $this->checkAndSendWA($candidate, $oldStatus, $newStatus);

        return back()->with('success', 'Status santri diperbarui. (Cek log jika WA tidak masuk)');
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
            // Ambil Data Lama (Termasuk Parent untuk No HP)
            $candidate = Candidate::with('parent')->findOrFail($id);
            
            // Simpan status lama sebelum di-update
            $oldStatus = $candidate->status_seleksi;

            // Update Data Pribadi
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
                // Pastikan input status_seleksi ada di form edit Anda
                // Jika tidak ada di form edit, baris ini tidak akan mengubah status
                'status_seleksi' => $request->status_seleksi ?? $candidate->status_seleksi, 
            ]);

            // Ambil status baru setelah update
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

            // CEK PERUBAHAN STATUS -> KIRIM WA
            // Kita panggil helper yang sama
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
        // Debugging Log (Cek di storage/logs/laravel.log)
        Log::info("Cek Kirim WA: Old={$oldStatus}, New={$newStatus}");

        // Normalisasi string (biar 'lulus' dan 'Lulus' dianggap sama)
        $old = strtolower($oldStatus);
        $new = strtolower($newStatus);

        // Jika status baru adalah 'lulus'/'diterima' DAN status lama BUKAN 'lulus'/'diterima'
        // Artinya baru saja diluluskan
        if (in_array($new, ['lulus', 'diterima', 'approved']) && !in_array($old, ['lulus', 'diterima', 'approved'])) {
            $this->sendWhatsAppNotification($candidate);
        }
    }

    // --- FUNGSI KIRIM API ---
    private function sendWhatsAppNotification($candidate)
    {
        try {
            // A. Ambil Nomor HP (Prioritas Ayah, lalu Ibu)
            $rawNo = $candidate->parent->no_hp_ayah ?? $candidate->parent->no_hp_ibu;
            
            if (empty($rawNo)) {
                Log::warning("Gagal Kirim WA: No HP Kosong untuk ID: " . $candidate->id);
                return;
            }

            // B. Format Nomor (628...)
            $cleanNo = preg_replace('/[^0-9]/', '', $rawNo); 
            if (substr($cleanNo, 0, 1) == '0') {
                $cleanNo = '62' . substr($cleanNo, 1);
            } elseif (substr($cleanNo, 0, 2) != '62') {
                $cleanNo = '62' . $cleanNo;
            }
            $chatId = $cleanNo . '@c.us';

            // C. Ambil Nama Sekolah
            $namaSekolah = Setting::where('key', 'nama_sekolah')->value('value') ?? 'Pondok Pesantren';
            
            // D. Pesan WA
            $pesanWA = "Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\n"
                     . "Yth. Bapak/Ibu Wali Santri,\n"
                     . "Kami ucapkan *SELAMAT!* Berdasarkan hasil verifikasi/seleksi, calon santri:\n\n"
                     . "👤 Nama: *{$candidate->nama_lengkap}*\n"
                     . "📝 No. Daftar: *{$candidate->no_daftar}*\n"
                     . "🎓 Jenjang: *{$candidate->jenjang}*\n\n"
                     . "Dinyatakan *LULUS / DITERIMA* sebagai santri baru di *{$namaSekolah}*.\n\n"
                     . "------------------------------------------------\n"
                     . "ℹ️ *INFORMASI SELANJUTNYA*\n"
                     . "------------------------------------------------\n"
                     . "Silakan melakukan pendaftaran atau hubungi panitia untuk informasi lebih lanjut.\n\n"
                     . "Terima kasih.\n"
                     . "Wassalamu'alaikum Warahmatullahi Wabarakatuh.";

            Log::info("Mencoba kirim WA ke: " . $chatId);

            // E. Kirim API
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Api-Key'    => env('WAHA_API_KEY', '0f0eb5d196b6459781f7d854aac5050e'), 
            ])->post(env('WAHA_BASE_URL', 'http://72.61.208.130:3000') . '/api/sendText', [
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
        
        // Tangkap jenis surat dari URL (?type=administrasi atau ?type=tes)
        // Defaultnya 'tes' (Lulus Akhir) jika tidak ada parameter
        $jenisSurat = $request->query('type', 'tes'); 

        return view('admin.candidates.print_card', compact('candidate', 'settings', 'jenisSurat'));
    }
}