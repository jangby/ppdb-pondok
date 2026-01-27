<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestRoom extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }

    public function candidates_santri()
    {
        return $this->hasMany(Candidate::class, 'santri_room_id');
    }

    public function candidates_wali()
    {
        return $this->hasMany(Candidate::class, 'wali_room_id');
    }
}