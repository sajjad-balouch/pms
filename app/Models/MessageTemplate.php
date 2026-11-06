<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Admin ka badla hua paigham; jo key yahan na ho us ka default App\Services\Messaging\Templates mein hai */
class MessageTemplate extends Model
{
    protected $fillable = ['key', 'body', 'meta_template', 'meta_language'];
}
