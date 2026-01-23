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
    ];
    
    // Atau jika ingin lebih simpel (mengizinkan semua kecuali id):
    // protected $guarded = ['id'];
}