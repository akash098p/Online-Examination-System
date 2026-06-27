<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'question_text',
        'topic',
        'question_type',
        'marks',
        'explanation',
    ];

    // Belongs to exam
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    // Has many options
    public function options()
    {
        return $this->hasMany(Option::class);
    }

    // Has many responses
    public function responses()
    {
        return $this->hasMany(Response::class);
    }
}
