<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;

    /**
     * ✅ ALLOW ALL RESULT FIELDS TO BE SAVED
     */
    protected $fillable = [
        'exam_id',
        'user_id',

        // MARKS
        'total_marks',
        'obtained_marks',
        'percentage',
        'status',

        // COUNTERS (THIS WAS THE CORE BUG)
        'total_questions',
        'correct',
        'wrong',
        'not_attempted',

        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    /* ---------------- RELATIONS ---------------- */

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * ✅ Student responses for THIS exam & THIS user
     * (keeps your existing logic intact)
     */
    public function responses()
    {
        return $this->hasMany(\App\Models\Response::class, 'exam_id', 'exam_id')
                    ->where('user_id', $this->user_id);
    }

    public function aiAnalysis()
    {
        return $this->hasOne(AiResultAnalysis::class);
    }

    public function aiChatMessages()
    {
        return $this->hasMany(AiChatMessage::class)->orderBy('id');
    }
}
