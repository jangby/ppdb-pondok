<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class QueueController extends Controller
{
    // 1. Tampilan Mobile (Publik)
    public function publicIndex()
    {
        return view('public.queue_mobile');
    }

    // 2. Logic Panggil (Bisa diakses tanpa login)
    public function callNext()
    {
        $candidate = DB::transaction(function () {
            
            // Cari antrian: Hadir hari ini, punya nomor, belum dipanggil
            $nextCandidate = Candidate::whereDate('waktu_hadir', Carbon::today())
                ->whereNotNull('nomor_antrian')
                ->whereNull('waktu_panggil')
                ->orderBy('nomor_antrian', 'asc')
                ->lockForUpdate() // Kunci agar tidak bentrok antar loket
                ->first();

            if ($nextCandidate) {
                $nextCandidate->update([
                    'waktu_panggil' => now(),
                    // 'dipanggil_oleh' => null // Kita kosongkan karena ini akses publik
                ]);
            }

            return $nextCandidate;
        });

        if (!$candidate) {
            return response()->json([
                'status' => 'empty',
                'message' => 'Antrian habis! Belum ada santri baru.'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'nama' => $candidate->nama_lengkap,
                'no_daftar' => $candidate->no_daftar,
                'antrian' => $candidate->nomor_antrian,
                'jenjang' => $candidate->jenjang
            ]
        ]);
    }

    // Tambahkan ini agar rute lama tidak error, tapi langsung melempar ke halaman baru
    public function index()
    {
        return redirect()->route('public.queue.index');
    }


    // 3. Halaman Monitor (Tampilan Layar TV / HP Orang Tua)
    public function publicMonitor()
    {
        return view('public.queue_monitor');
    }

    // 4. API untuk Cek Status Terkini (Dipanggil oleh Javascript per detik)
    public function getCurrentStatus()
    {
        // Ambil santri yang PALING BARU dipanggil hari ini
        $current = Candidate::whereDate('waktu_hadir', Carbon::today())
            ->whereNotNull('waktu_panggil')
            ->orderBy('waktu_panggil', 'desc') // Urutkan dari yang terakhir dipanggil
            ->first();

        if (!$current) {
            return response()->json([
                'status' => 'waiting',
                'data' => [
                    'antrian' => '--',
                    'nama' => 'Belum ada panggilan',
                    'no_daftar' => '-'
                ]
            ]);
        }

        return response()->json([
            'status' => 'active',
            'data' => [
                'antrian' => $current->nomor_antrian,
                'nama' => $current->nama_lengkap,
                'no_daftar' => $current->no_daftar
            ]
        ]);
    }
}