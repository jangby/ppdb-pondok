<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    // Admin siapa yang belanja?
    public function admin()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Sumber dananya dari mana aja?
    public function fund_sources()
    {
        return $this->hasMany(ExpenseFundSource::class);
    }
}