<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TownExpense extends Model
{
    protected $guarded = [];

    public function town()
    {
        return $this->belongsTo(Town::class);
    }
}
