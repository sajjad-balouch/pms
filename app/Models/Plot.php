<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plot extends Model
{
    use HasFactory;

    protected $fillable = [
        'town_id',
        'plot_number',
        'block_name',
        'type',
        'size',
        'total_price',
        'down_payment',
        'total_installments',
        'status',
    ];

    public function town()
    {
        return $this->belongsTo(Town::class);
    }

    public function installments()
    {
        return $this->hasMany(Installment::class);
    }


    // Jis User/Admin ne post kiya uski Relationship
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Direct owner nikalne ke liye 'hasOneThrough' (Town ke zariye User)
    public function owner()
    {
        return $this->hasOneThrough(
            User::class,
            Town::class,
            'id',       // Town table ki Primary Key
            'id',       // User table ki Primary Key
            'town_id',  // Plot table me Foreign Key
            'user_id'   // Town table me Foreign Key (Owner ki ID)
        );
    }
    
}