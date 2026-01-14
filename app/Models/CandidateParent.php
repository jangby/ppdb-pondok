<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateParent extends Model
{
    use HasFactory;

    protected $table = 'candidate_parents'; // Pastikan nama tabel benar

    // TAMBAHKAN KOLOM BARU DI SINI
    protected $fillable = [
        'candidate_id',
        'nama_ayah',
        'nik_ayah',
        'thn_lahir_ayah',   // <--- Wajib ditambah
        'pendidikan_ayah',  // <--- Wajib ditambah
        'pekerjaan_ayah',
        'no_hp_ayah',
        'nama_ibu',
        'nik_ibu',
        'thn_lahir_ibu',    // <--- Wajib ditambah
        'pendidikan_ibu',   // <--- Wajib ditambah
        'pekerjaan_ibu',
        'no_hp_ibu',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}