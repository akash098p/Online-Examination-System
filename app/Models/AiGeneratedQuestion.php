<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiGeneratedQuestion extends Model
{
    protected $fillable = [
        'exam_id',
        'question_id',
        'generated_by',
        'approved_by',
        'subject',
        'difficulty',
        'topic',
        'question',
        'options',
        'correct_answer',
        'explanation',
        'status',
        'model',
        'cache_key',
        'raw_payload',
    ];

    protected $casts = [
        'options' => 'array',
        'raw_payload' => 'array',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function questionModel()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}
