<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'user_name',
        'user_email',
        'rating',
        'comment',
        'ip_address',
        'is_approved',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
}