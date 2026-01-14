<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateBill extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    // Milik siapa tagihan ini?
    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    // Tagihan jenis apa ini? (Gedung/Seragam)
    public function payment_type()
    {
        return $this->belongsTo(PaymentType::class);
    }
    
    // Helper untuk menghitung sisa tagihan secara kodingan
    public function getSisaTagihanAttribute()
    {
        return $this->nominal_tagihan - $this->nominal_terbayar;
    }
}