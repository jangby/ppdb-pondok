<?php

namespace App\Http\Controllers;

use App\Models\PaymentType;
use App\Models\Setting; // [BARU] Import Model Setting
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // [BARU] Import Rule Validation

class PaymentTypeController extends Controller
{
    public function index()
    {
        // 1. Ambil Data Pembayaran Terbaru
        $payments = PaymentType::latest()->get();

        // 2. Ambil Daftar Jenjang dari Setting (untuk dikirim ke View/Modal)
        $jenjangs = json_decode(Setting::getValue('list_jenjang'), true) ?? ['SMP', 'SMK'];

        return view('admin.payment_types.index', compact('payments', 'jenjangs'));
    }

    public function store(Request $request)
    {
        // Ambil daftar jenjang valid dari Setting + Tambah opsi "Semua"
        $jenjangList = json_decode(Setting::getValue('list_jenjang'), true) ?? [];
        $validJenjangs = array_merge(['Semua'], $jenjangList);

        $request->validate([
            'nama_pembayaran' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:0',
            // Validasi Dinamis: Hanya terima 'Semua' atau salah satu jenjang yang terdaftar
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