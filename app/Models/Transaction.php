<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'category',
        'amount',
        'transaction_date',
        'note',
        'image'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

