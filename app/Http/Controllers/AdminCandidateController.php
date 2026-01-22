<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use Illuminate\Http\Request;
use App\Models\CandidateAddress;
use App\Models\CandidateParent;
use App\Models\PaymentType;
use App\Models\CandidateBill;
use Illuminate\Support\Facades\DB;
use App\Exports\CandidatesExport; // Import Export Class
use Maatwebsite\Excel\Facades\Excel; // Import Facade Excel

class AdminCandidateController extends Controller
{
    public function index(Request $request)
    {
        // 1. FILTER & SEARCH
        $query = Candidate::query();

        if ($request->has('search') && $request->search != '') {
            $query->where('nama_lengkap', 'like', '%' . $request->search . '%')
                  ->orWhere('no_daftar', 'like', '%' . $request->search . '%');
        }

        if ($request->has('jenjang') && $request->jenjang != 'Semua') {
            $query->where('jenjang', $request->jenjang);
        }

        if ($request->has('status') && $request->status != 'Semua') {
            $query->where('status', $request->status);
        }

        // Ambil Data Pagination
        $candidates = $query->latest()->paginate(10);

        // 2. DATA KPI (STATISTIK)
        $kpi = [
            'total' => Candidate::count(),
            'laki' => Candidate::where('jenis_kelamin', 'L')->count(),
            'perempuan' => Candidate::where('jenis_kelamin', 'P')->count(),
            'baru' => Candidate::where('status', 'Baru')->count(),
            'diterima' => Candidate::where('status', 'Lulus')->count(),
        ];

        return view('admin.candidates.index', compact('candidates', 'kpi'));
    }

    // Method Baru untuk Export Excel
    public function export()
    {
        return Excel::download(new CandidatesExport, 'Data-Santri-' . date('Y-m-d') . '.xlsx');
    }

    // 1. Tampilkan Form Tambah Santri (Offline)
    public function create()
    {
        return view('admin.candidates.create');
    }

    // 2. Proses Simpan Data Offline
    public function store(Request $request)
    {
        // Validasi
        $request->validate([
            'nama_lengkap' => 'required',
            'jenis_kelamin' => 'required',
            'jenjang' => 'required',
            // NISN/NIK boleh diisi nanti kalau lupa bawa berkas
            'nisn' => 'nullable|unique:candidates,nisn', 
            'nik' => 'nullable|unique:candidates,nik',
        ]);

        DB::beginTransaction();

        try {
            // A. Simpan Data Santri
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
                // 'asal_sekolah' => $request->asal_sekolah, // Hapus jika kolom tidak ada di DB
                
                // Field Sistem
                'tahun_masuk' => date('Y'),
                'jalur_pendaftaran' => 'Offline',
                'status_seleksi' => 'Pending',
            ]);

            // B. Simpan Alamat
            CandidateAddress::create([
                'candidate_id' => $candidate->id,
                'alamat' => $request->alamat,
                'rt' => $request->rt,
                'rw' => $request->rw,
                'desa' => $request->desa,
                'kecamatan' => $request->kecamatan,
                'kode_pos' => $request->kode_pos,
                'kabupaten' => $request->kabupaten, // Pastikan sudah migrasi kolom ini
                'provinsi' => $request->provinsi,   // Pastikan sudah migrasi kolom ini
            ]);

            // C. Simpan Orang Tua
            CandidateParent::create([
                'candidate_id' => $candidate->id,
                'nama_ayah' => $request->nama_ayah,
                'nik_ayah' => $request->nik_ayah,
                'thn_lahir_ayah' => $request->thn_lahir_ayah, 
                'pendidikan_ayah' => $request->pendidikan_ayah,
                'pekerjaan_ayah' => $request->pekerjaan_ayah,
                'penghasilan_ayah' => $request->penghasilan_ayah ?? 0, // TAMBAHKAN INI
                'no_hp_ayah' => $request->no_hp_ayah,
                
                'nama_ibu' => $request->nama_ibu,
                'nik_ibu' => $request->nik_ibu,
                'thn_lahir_ibu' => $request->thn_lahir_ibu,
                'pendidikan_ibu' => $request->pendidikan_ibu,
                'pekerjaan_ibu' => $request->pekerjaan_ibu,
                'penghasilan_ibu' => $request->penghasilan_ibu ?? 0, // TAMBAHKAN INI
                'no_hp_ibu' => $request->no_hp_ibu,
            ]);

            // D. Generate Tagihan
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

            return redirect()->route('admin.candidates.show', $candidate->id)
                             ->with('success', 'Pendaftaran Offline Berhasil!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $candidate = Candidate::with(['address', 'parent', 'bills.payment_type', 'transactions'])
                        ->findOrFail($id);

        return view('admin.candidates.show', compact('candidate'));
    }
    
    // Update Status Kelulusan
    public function updateStatus(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);
        
        $candidate->update([
            'status_seleksi' => $request->status_seleksi
        ]);

        return back()->with('success', 'Status santri berhasil diperbarui.');
    }


    // 3. Tampilkan Halaman Edit
    public function edit($id)
    {
        $candidate = Candidate::with(['address', 'parent'])->findOrFail($id);
        return view('admin.candidates.edit', compact('candidate'));
    }

    // 4. Proses Update Data
    public function update(Request $request, $id)
    {
        // Validasi
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

            // A. Update Data Pribadi
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
                // 'asal_sekolah' => $request->asal_sekolah, // Pastikan ada migrasi untuk ini jika ingin dipakai
            ]);

            // B. Update Alamat (Sekarang KABUPATEN & PROVINSI dimasukkan)
            $candidate->address()->update([
                'alamat' => $request->alamat,
                'rt' => $request->rt,
                'rw' => $request->rw,
                'desa' => $request->desa,
                'kecamatan' => $request->kecamatan,
                'kabupaten' => $request->kabupaten, // SUDAH ADA DI DB
                'provinsi' => $request->provinsi,   // SUDAH ADA DI DB
                'kode_pos' => $request->kode_pos,
            ]);

            // C. Update Orang Tua
            $candidate->parent()->update([
                'nama_ayah' => $request->nama_ayah,
                'nik_ayah' => $request->nik_ayah,
                'thn_lahir_ayah' => $request->thn_lahir_ayah,
                'pendidikan_ayah' => $request->pendidikan_ayah,
                'pekerjaan_ayah' => $request->pekerjaan_ayah,
                'penghasilan_ayah' => $request->penghasilan_ayah ?? 0,
                'no_hp_ayah' => $request->no_hp_ayah,
                
                'nama_ibu' => $request->nama_ibu,
                'nik_ibu' => $request->nik_ibu,
                'thn_lahir_ibu' => $request->thn_lahir_ibu,
                'pendidikan_ibu' => $request->pendidikan_ibu,
                'pekerjaan_ibu' => $request->pekerjaan_ibu,
                'penghasilan_ibu' => $request->penghasilan_ibu ?? 0,
                'no_hp_ibu' => $request->no_hp_ibu,
            ]);

            DB::commit();

            return redirect()->route('admin.candidates.show', $id)
                             ->with('success', 'Data santri berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    // Menampilkan Kartu Diterima
public function printCard($id)
{
    $candidate = Candidate::with(['address', 'parent'])->findOrFail($id);
    
    // Pastikan hanya yang statusnya "Lulus" atau "Diterima" yang bisa cetak
    // if ($candidate->status_seleksi != 'Lulus') {
    //    return back()->with('error', 'Santri belum dinyatakan lulus.');
    // }

    return view('admin.candidates.print_card', compact('candidate'));
}
}

