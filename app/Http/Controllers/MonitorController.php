<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use Illuminate\Http\Request;

class MonitorController extends Controller
{
    public function index()
    {
        // Ambil semua data santri beserta alamatnya
        $candidates = Candidate::with('address')->latest()->get();

        // Kelompokkan berdasarkan jenjang (SMP, SMK, dll)
        $dataPerJenjang = $candidates->groupBy('jenjang');

        // Hitung KPI Otomatis
        $kpi = [];
        foreach ($dataPerJenjang as $jenjang => $dataSantri) {
            $kpi[$jenjang] = [
                'total' => $dataSantri->count(),
                'laki_laki' => $dataSantri->where('jenis_kelamin', 'L')->count(),
                'perempuan' => $dataSantri->where('jenis_kelamin', 'P')->count(),
            ];
        }

        return view('monitor.index', compact('kpi', 'dataPerJenjang'));
    }

    public function show($id)
    {
        // Ambil detail santri (bersama alamat dan data orang tua)
        $santri = Candidate::with(['address', 'parent'])->findOrFail($id);
        return view('monitor.show', compact('santri'));
    }
}