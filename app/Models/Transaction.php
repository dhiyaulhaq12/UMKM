<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'session_code',
        'type',
        'category',
        'amount',
        'transaction_date',
        'transaction_time',
        'note',
        'image',
        'quantity',
        'unit'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

