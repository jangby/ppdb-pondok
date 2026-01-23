<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\CandidateAddress;
use App\Models\CandidateParent;
use App\Models\PaymentType;
use App\Models\CandidateBill;
use App\Models\Setting; // [BARU] Import Model Setting
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\CandidatesExport; 
use Maatwebsite\Excel\Facades\Excel; 

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

    // [PERBAIKAN DISINI] Filter Status Seleksi
    if ($request->has('status') && $request->status != 'Semua') {
        // Kita gunakan 'status_seleksi' BUKAN 'status'
        if ($request->status == 'Lulus') {
            // Jaga-jaga jika di database ada yang tertulis 'Lulus' atau 'Diterima'
            $query->whereIn('status_seleksi', ['Lulus', 'Diterima', 'Approved']); 
        } else {
            $query->where('status_seleksi', $request->status);
        }
    }

    // Ambil Data Pagination
    $candidates = $query->latest()->paginate(10)->withQueryString();

    // ... (kode KPI dan Jenjang tetap sama) ...
    
    // Copy ulang bagian KPI biar aman
    $kpi = [
        'total' => Candidate::count(),
        'laki' => Candidate::where('jenis_kelamin', 'L')->count(),
        'perempuan' => Candidate::where('jenis_kelamin', 'P')->count(),
        'pending' => Candidate::where('status_seleksi', 'Pending')->count(),
        'diterima' => Candidate::whereIn('status_seleksi', ['Lulus', 'Diterima'])->count(),
    ];

    $jenjangs = json_decode(\App\Models\Setting::getValue('list_jenjang'), true) ?? ['SMP', 'SMK'];

    return view('admin.candidates.index', compact('candidates', 'kpi', 'jenjangs'));
}

    // Method Baru untuk Export Excel
    public function export()
    {
        return Excel::download(new CandidatesExport, 'Data-Santri-' . date('Y-m-d') . '.xlsx');
    }

    // 1. Tampilkan Form Tambah Santri (Offline)
    public function create()
    {
        // [BARU] Kirim data jenjang ke form create juga
        $jenjangs = json_decode(Setting::getValue('list_jenjang'), true) ?? ['SMP', 'SMK'];
        return view('admin.candidates.create', compact('jenjangs'));
    }

    // 2. Proses Simpan Data Offline
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
                'asal_sekolah' => $request->asal_sekolah ?? '-', // Default strip jika kosong
                'tahun_masuk' => date('Y'),
                'jalur_pendaftaran' => 'Offline',
                'status_seleksi' => 'Lulus', // Offline biasanya langsung diterima
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
                'kabupaten' => $request->kabupaten,
                'provinsi' => $request->provinsi,
            ]);

            // C. Simpan Orang Tua
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
    
    public function updateStatus(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);
        $candidate->update(['status_seleksi' => $request->status_seleksi]);
        return back()->with('success', 'Status santri berhasil diperbarui.');
    }

    public function edit($id)
    {
        $candidate = Candidate::with(['address', 'parent'])->findOrFail($id);
        $jenjangs = json_decode(Setting::getValue('list_jenjang'), true) ?? ['SMP', 'SMK']; // [BARU]
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
            ]);

            // Update Alamat
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

            // Update Orang Tua
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