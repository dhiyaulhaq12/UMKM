<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class LandingFeature extends Model
{
    protected $fillable = ['icon', 'title', 'description'];

    /**
     * Otomatis mengurutkan berdasarkan ID setiap kali data dipanggil
     */
    protected static function booted()
    {
        static::addGlobalScope('orderById', function (Builder $builder) {
            $builder->orderBy('id', 'asc');
        });
    }
}