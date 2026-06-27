<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiResultAnalysis extends Model
{
    protected $fillable = [
        'result_id',
        'user_id',
        'model',
        'prompt_hash',
        'analysis',
    ];

    protected $casts = [
        'analysis' => 'array',
    ];

    public function result()
    {
        return $this->belongsTo(Result::class);
    }
}
