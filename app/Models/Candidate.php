<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

    // Izinkan semua kolom diisi (mass assignment)
    protected $guarded = ['id'];

    // Relasi ke User (Akun Login)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi 1 Santri punya 1 Alamat
    public function address()
    {
        return $this->hasOne(CandidateAddress::class);
    }

    // Relasi 1 Santri punya 1 Data Orang Tua
    public function parent()
    {
        return $this->hasOne(CandidateParent::class);
    }

    // Relasi 1 Santri punya Banyak Tagihan
    public function bills()
    {
        return $this->hasMany(CandidateBill::class);
    }

    // Relasi 1 Santri punya Banyak Riwayat Transaksi (Kwitansi)
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}