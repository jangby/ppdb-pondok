<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use Illuminate\Http\Request;
use App\Models\CandidateAddress;
use App\Models\CandidateParent;
use App\Models\CandidateBill;
use App\Models\PaymentType;
use Illuminate\Support\Facades\DB;

class AdminCandidateController extends Controller
{
    public function index(Request $request)
{
    // Mulai Query
    $query = Candidate::query();

    // 1. Logika Search (Nama atau No Daftar)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('nama_lengkap', 'like', '%' . $search . '%')
              ->orWhere('no_daftar', 'like', '%' . $search . '%');
        });
    }

    // 2. Logika Filter Jenjang
    if ($request->filled('jenjang')) {
        $query->where('jenjang', $request->jenjang);
    }

    // 3. Logika Filter Status
    if ($request->filled('status')) {
        $query->where('status_seleksi', $request->status);
    }

    // Ambil data dengan pagination (tambahkan withQueryString agar filter tidak hilang saat ganti halaman)
    $candidates = $query->latest()->paginate(10)->withQueryString();

    return view('admin.candidates.index', compact('candidates'));
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
            // NISN/NIK boleh diisi nanti kalau lupa bawa berkas, tapi sebaiknya diisi
            'nisn' => 'nullable|unique:candidates,nisn', 
            'nik' => 'nullable|unique:candidates,nik',
        ]);

        DB::beginTransaction();

        try {
            // A. Simpan Data Santri
            $candidate = Candidate::create([
                'no_daftar' => 'OFF-' . date('Y') . date('His'), // Kode OFF untuk Offline
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
                
                // Field Sistem
                'tahun_masuk' => date('Y'),
                'jalur_pendaftaran' => 'Offline', // PENTING
                'status_seleksi' => 'Pending', // Default Pending, nanti admin bisa langsung terima di detail
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
                'kabupaten' => $request->kabupaten ?? '-',
                'provinsi' => $request->provinsi ?? '-',
            ]);

            // C. Simpan Orang Tua
            // Bagian C. Simpan Orang Tua di function store()
CandidateParent::create([
    'candidate_id' => $candidate->id,
    'nama_ayah' => $request->nama_ayah,
    'nik_ayah' => $request->nik_ayah,
    // Tambahkan ini agar tersimpan saat daftar baru
    'thn_lahir_ayah' => $request->thn_lahir_ayah, 
    'pendidikan_ayah' => $request->pendidikan_ayah,
    'pekerjaan_ayah' => $request->pekerjaan_ayah,
    'no_hp_ayah' => $request->no_hp_ayah,
    
    'nama_ibu' => $request->nama_ibu,
    'nik_ibu' => $request->nik_ibu,
    // Tambahkan ini juga
    'thn_lahir_ibu' => $request->thn_lahir_ibu,
    'pendidikan_ibu' => $request->pendidikan_ibu,
    'pekerjaan_ibu' => $request->pekerjaan_ibu,
    'no_hp_ibu' => $request->no_hp_ibu,
]);

            // D. Generate Tagihan (Copy logic dari online)
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

            // Redirect langsung ke halaman Detail agar Admin bisa langsung input pembayaran
            return redirect()->route('admin.candidates.show', $candidate->id)
                             ->with('success', 'Pendaftaran Offline Berhasil! Silakan cek kelengkapan & pembayaran.');

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
        // Validasi sederhana (Sesuaikan kebutuhan)
        $request->validate([
            'nama_lengkap' => 'required',
            'jenjang' => 'required',
            // Ignore ID saat cek unique agar tidak error validasi diri sendiri
            'nisn' => 'nullable|unique:candidates,nisn,'.$id, 
            'nik' => 'nullable|unique:candidates,nik,'.$id,
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
            ]);

            // B. Update Alamat
            $candidate->address()->update([
                'alamat' => $request->alamat,
                'rt' => $request->rt,
                'rw' => $request->rw,
                'desa' => $request->desa,
                'kecamatan' => $request->kecamatan,
                'kabupaten' => $request->kabupaten,
                'provinsi' => $request->provinsi, // Jika ada inputnya
                'kode_pos' => $request->kode_pos,
            ]);

            // C. Update Orang Tua
            $candidate->parent()->update([
                'nama_ayah' => $request->nama_ayah,
                'nik_ayah' => $request->nik_ayah,
                'thn_lahir_ayah' => $request->thn_lahir_ayah,
                'pendidikan_ayah' => $request->pendidikan_ayah,
                'pekerjaan_ayah' => $request->pekerjaan_ayah,
                'no_hp_ayah' => $request->no_hp_ayah,
                
                'nama_ibu' => $request->nama_ibu,
                'nik_ibu' => $request->nik_ibu,
                'thn_lahir_ibu' => $request->thn_lahir_ibu,
                'pendidikan_ibu' => $request->pendidikan_ibu,
                'pekerjaan_ibu' => $request->pekerjaan_ibu,
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

