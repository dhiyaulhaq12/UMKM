<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = ['user_id', 'name', 'value', 'purchase_date', 'image'];
}
