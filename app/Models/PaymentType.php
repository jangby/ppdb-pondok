<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentType extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function bills()
    {
        return $this->hasMany(CandidateBill::class);
    }

    /**
     * The "booted" method of the model.
     * Logika Hapus Otomatis (Cascading Delete)
     */
    protected static function booted()
    {
        static::deleting(function ($paymentType) {
            // Ambil semua tagihan (bills) yang menggunakan jenis pembayaran ini
            // (Misal: Semua tagihan 'Uang Gedung' di seluruh santri)
            $bills = $paymentType->bills;

            foreach ($bills as $bill) {
                // 1. Hapus dulu riwayat pembayarannya (Detail Transaksi)
                // Jika tidak dihapus, akan error Foreign Key Constraint seperti sebelumnya
                $bill->transaction_details()->delete();

                // 2. Setelah bersih, baru hapus Tagihan Santrinya
                $bill->delete();
            }
            
            // 3. Terakhir, PaymentType itu sendiri akan terhapus otomatis setelah fungsi ini selesai
        });
    }
}