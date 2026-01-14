<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseFundSource extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    // Diambil dari pos anggaran apa?
    public function payment_type()
    {
        return $this->belongsTo(PaymentType::class);
    }
}