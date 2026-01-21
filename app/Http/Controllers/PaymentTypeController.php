<?php

namespace App\Http\Controllers;

use App\Models\PaymentType;
use Illuminate\Http\Request;

class PaymentTypeController extends Controller
{
    public function index()
    {
        // Mengambil data terbaru
        $payments = PaymentType::latest()->get();
        return view('admin.payment_types.index', compact('payments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pembayaran' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:0',
            'jenjang' => 'required|in:Semua,SD,SMP,SMA,SMK', // Sesuaikan dengan enum di migrasi Anda (tambahkan jika perlu)
        ]);

        PaymentType::create($request->all());

        return back()->with('success', 'Item pembayaran berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_pembayaran' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:0',
            'jenjang' => 'required|in:Semua,SD,SMP,SMA,SMK',
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