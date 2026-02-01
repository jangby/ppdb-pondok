<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateBill extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function payment_type()
    {
        return $this->belongsTo(PaymentType::class);
    }
    
    // --- TAMBAHKAN INI ---
    // Relasi ke Detail Transaksi (Agar bisa dihapus otomatis)
    public function transaction_details()
    {
        return $this->hasMany(TransactionDetail::class);
    }
    // ---------------------

    public function getSisaTagihanAttribute()
    {
        return $this->nominal_tagihan - $this->nominal_terbayar;
    }
}