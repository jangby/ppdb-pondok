<?php

namespace App\Http\Controllers;

use App\Models\PaymentType;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentTypeController extends Controller
{
    public function index()
    {
        // 1. Ambil Data Pembayaran
        $payments = PaymentType::latest()->get();

        // 2. Ambil Daftar Jenjang dari Setting
        $jenjangs = json_decode(Setting::getValue('list_jenjang'), true) ?? ['SMP', 'SMK'];

        // 3. [BARU] Hitung Total Biaya Per Jenjang
        // Inisialisasi array total dengan 0
        $totalBiaya = array_fill_keys($jenjangs, 0);

        foreach ($payments as $p) {
            if ($p->jenjang == 'Semua') {
                // Jika jenisnya 'Semua', tambahkan ke semua jenjang
                foreach ($jenjangs as $j) {
                    $totalBiaya[$j] += $p->nominal;
                }
            } elseif (in_array($p->jenjang, $jenjangs)) {
                // Jika spesifik, tambahkan ke jenjang itu saja
                $totalBiaya[$p->jenjang] += $p->nominal;
            }
        }

        return view('admin.payment_types.index', compact('payments', 'jenjangs', 'totalBiaya'));
    }

    public function store(Request $request)
    {
        $jenjangList = json_decode(Setting::getValue('list_jenjang'), true) ?? [];
        $validJenjangs = array_merge(['Semua'], $jenjangList);

        $request->validate([
            'nama_pembayaran' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:0',
            'jenjang' => ['required', Rule::in($validJenjangs)], 
        ]);

        PaymentType::create($request->all());

        return back()->with('success', 'Item pembayaran berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $jenjangList = json_decode(Setting::getValue('list_jenjang'), true) ?? [];
        $validJenjangs = array_merge(['Semua'], $jenjangList);

        $request->validate([
            'nama_pembayaran' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:0',
            'jenjang' => ['required', Rule::in($validJenjangs)],
        ]);

        $payment = PaymentType::findOrFail($id);
        $payment->update($request->all());

        return back()->with('success', 'Item pembayaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $payment = PaymentType::findOrFail($id);
        $payment->delete();

        return back()->with('success', 'Item pembayaran berhasil dihapus.');
    }
}