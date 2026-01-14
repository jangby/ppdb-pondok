<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'alamat',
        'rt',
        'rw',
        'desa',
        'kecamatan',
        'kabupaten', // <--- Pastikan ini ada
        'provinsi',
        'kode_pos',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}