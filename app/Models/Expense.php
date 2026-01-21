<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    // IZINKAN MASS ASSIGNMENT
    // Artinya: Semua kolom selain 'id' boleh diisi oleh Controller
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fundSources()
    {
        return $this->hasMany(ExpenseFundSource::class, 'expense_id');
    }
}