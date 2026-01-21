<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseFundSource extends Model
{
    use HasFactory;

    // IZINKAN MASS ASSIGNMENT
    protected $guarded = ['id'];

    // Matikan timestamps karena di migrasi Anda (kemungkinan) tidak pakai created_at/updated_at default
    // Tapi jika di migrasi ada $table->timestamps(), hapus baris di bawah ini.
    // Berdasarkan migrasi yang Anda kirim, Anda pakai $table->timestamps(), jadi SAYA HAPUS public $timestamps = false;
    
    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class);
    }
}