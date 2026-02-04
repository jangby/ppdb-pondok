<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    use HasFactory;

    // Izinkan kolom-kolom ini untuk diisi secara massal
    protected $fillable = [
        'no_wa',
        'file_perjanjian',
        'token',
        'status',
        'jenjang',         // [BARU] Jenjang Pilihan
        'bukti_transfer',  // [BARU] Path File
        'status_pembayaran', // [BARU] Status Uang
        'catatan_pembayaran'
    ];
    
    // Atau jika ingin lebih simpel (mengizinkan semua kecuali id):
    // protected $guarded = ['id'];
}