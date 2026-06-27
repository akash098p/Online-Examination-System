<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiChatMessage extends Model
{
    protected $fillable = [
        'result_id',
        'user_id',
        'role',
        'message',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function result()
    {
        return $this->belongsTo(Result::class);
    }
}
