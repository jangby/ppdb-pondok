<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterviewQuestion extends Model
{
    protected $guarded = [];

    // Cast kolom options agar otomatis jadi Array saat diambil dari DB
    protected $casts = [
        'options' => 'array',
        'is_active' => 'boolean',
    ];

    public function answers()
    {
        return $this->hasMany(InterviewAnswer::class);
    }
}