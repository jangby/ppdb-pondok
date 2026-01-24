<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use Illuminate\Http\Request;
use App\Models\CandidateAddress;
use App\Models\CandidateParent;
use App\Models\PaymentType;
use App\Models\CandidateBill;
use App\Models\Setting; // [PENTING] Tambahkan Model Setting
use Illuminate\Support\Facades\DB;
use App\Exports\CandidatesExport; 
use Maatwebsite\Excel\Facades\Excel; 
use Illuminate\Support\Facades\Http; // [PENTING] Tambahkan Http untuk WA
use Illuminate\Support\Facades\Log;  // [PENTING] Tambahkan Log

class AdminCandidateController extends Controller
{
    public function index(Request $request)
    {
        // 1. FILTER & SEARCH
        $query = Candidate::query();

        // Filter Pencarian (Nama/No Daftar/NISN)
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->search . '%')
                  ->orWhere('no_daftar', 'like', '%' . $request->search . '%')
                  ->orWhere('nisn', 'like', '%' . $request->search . '%');
            });
        }

        // Filter Jenjang
        if ($request->has('jenjang') && $request->jenjang != 'Semua') {
            $query->where('jenjang', $request->jenjang);
        }

        // Filter Status Seleksi
        if ($request->has('status') && $request->status != 'Semua') {
            if ($request->status == 'Lulus') {
                $query->whereIn('status_seleksi', ['Lulus', 'Diterima', 'Approved']); 
            } else {
                $query->where('status_seleksi', $request->status);
            }
        }

        // Ambil Data Pagination
        $candidates = $query->latest()->paginate(10)->withQueryString();

        // KPI
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
    
    // [PERBAIKAN] Update Status + Kirim WA
    public function updateStatus(Request $request, $id)
    {
        $candidate = Candidate::with('parent')->findOrFail($id);
        
        $oldStatus = $candidate->status_seleksi;
        $newStatus = $request->status_seleksi;

        // Update Status di Database
        $candidate->update(['status_seleksi' => $newStatus]);

        // Cek jika status berubah jadi Lulus/Diterima
        if (in_array($newStatus, ['Lulus', 'Diterima']) && !in_array($oldStatus, ['Lulus', 'Diterima'])) {
            $this->sendWhatsAppNotification($candidate);
        }

        return back()->with('success', 'Status santri berhasil diperbarui dan notifikasi WA diproses.');
    }

    // Fungsi Privat untuk Kirim WA
    private function sendWhatsAppNotification($candidate)
    {
        try {
            Log::info("--- MULAI KIRIM WA LULUS SELEKSI ---");

            // 1. Format Nomor HP (08 -> 628)
            $rawNo = $candidate->parent->no_hp_ayah ?? $candidate->parent->no_hp_ibu;
            
            if (!$rawNo) {
                Log::warning("No HP Orang Tua tidak ditemukan untuk santri ID: " . $candidate->id);
                return;
            }

            $cleanNo = preg_replace('/[^0-9]/', '', $rawNo); 
            if (substr($cleanNo, 0, 1) == '0') {
                $cleanNo = '62' . substr($cleanNo, 1);
            } elseif (substr($cleanNo, 0, 2) != '62') {
                $cleanNo = '62' . $cleanNo;
            }
            
            $chatId = $cleanNo . '@c.us';

            // 2. Ambil Data Setting
            $namaSekolah = Setting::where('key', 'nama_sekolah')->value('value') ?? 'Pondok Pesantren';
            
            // 3. Susun Pesan WA Lulus
            $pesanWA = "Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\n"
                     . "Yth. Bapak/Ibu Wali Santri,\n"
                     . "Kami ucapkan *SELAMAT!* Berdasarkan hasil seleksi, calon santri:\n\n"
                     . "👤 Nama: *{$candidate->nama_lengkap}*\n"
                     . "📝 No. Daftar: *{$candidate->no_daftar}*\n"
                     . "🎓 Jenjang: *{$candidate->jenjang}*\n\n"
                     . "Dinyatakan *LULUS / DITERIMA* sebagai santri baru di *{$namaSekolah}*.\n\n"
                     . "------------------------------------------------\n"
                     . "ℹ️ *INFORMASI*\n"
                     . "------------------------------------------------\n"
                     . "Silakan lakukan pembayaran Pendaftaran atau hubungi panitia untuk informasi lebih lanjut.\n\n"
                     . "Terima kasih.\n"
                     . "Wassalamu'alaikum Warahmatullahi Wabarakatuh.";

            // 4. Kirim Request ke WAHA
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                // Pastikan ENV sesuai atau hardcode jika perlu untuk testing
                'X-Api-Key'    => env('WAHA_API_KEY', '0f0eb5d196b6459781f7d854aac5050e'), 
            ])->post(env('WAHA_BASE_URL', 'http://72.61.208.130:3000') . '/api/sendText', [
                'session' => 'default',
                'chatId'  => $chatId,
                'text'    => $pesanWA
            ]);

            // 5. Log Hasil
            if ($response->successful()) {
                Log::info("WA Lulus Terkirim ke {$chatId}");
            } else {
                Log::error("WA Gagal Terkirim! Status: " . $response->status() . " Body: " . $response->body());
            }

        } catch (\Exception $e) {
            Log::error("EXCEPTION WA Error (UpdateStatus): " . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $candidate = Candidate::with(['address', 'parent'])->findOrFail($id);
        $jenjangs = json_decode(Setting::getValue('list_jenjang'), true) ?? ['SMP', 'SMK']; 
        return view('admin.candidates.edit', compact('candidate', 'jenjangs'));
    }

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
            $candidate = Candidate::findOrFail($id);

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
            ]);

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
            return redirect()->route('admin.candidates.show', $id)->with('success', 'Data santri berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function printCard($id)
    {
        $candidate = Candidate::with(['address', 'parent'])->findOrFail($id);
        return view('admin.candidates.print_card', compact('candidate'));
    }
}