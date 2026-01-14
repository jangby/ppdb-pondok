<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    // Siapa yang bayar?
    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    // Admin siapa yang input?
    public function admin() // menunjuk ke tabel users
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Detail alokasi dananya kemana aja?
    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }
}