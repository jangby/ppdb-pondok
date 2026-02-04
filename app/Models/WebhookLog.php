<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookLog extends Model
{
    protected $guarded = [];

    protected $casts = [
        'payload' => 'array', // Otomatis ubah JSON jadi Array saat diambil
    ];
}