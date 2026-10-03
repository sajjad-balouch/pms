<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HousingScheme extends Model
{
    use HasFactory;

    // Agar aapka table name alag hai toh yahan specify karein:
    // protected $table = 'housing_schemes';

    protected $guarded = [];
}