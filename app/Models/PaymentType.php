<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentType extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    // Relasi untuk melihat tagihan mana saja yang pakai jenis pembayaran ini
    public function bills()
    {
        return $this->hasMany(CandidateBill::class);
    }
}