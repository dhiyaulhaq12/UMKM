<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomCategory extends Model
{
    protected $fillable = ['user_id', 'type', 'name', 'default_price', 'unit'];
}
