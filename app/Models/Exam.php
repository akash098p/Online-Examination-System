<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'title',
        'subject',
        'department',
        'description',
        'duration_minutes',
        'total_marks',
        'pass_percentage',
        'start_time',
        'end_time',
        'status',
        'semester',
        'negative_enabled',
        'negative_marking',
        'proctoring_enabled',
        'require_camera',
        'require_microphone',
        'detect_no_face',
        'detect_multiple_faces',
        'detect_talking',
        'max_warnings',
        'pre_exam_countdown_seconds',
    ];

    // ✅ FIX: Proper datetime casting
    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
        'pass_percentage' => 'float',
        'negative_enabled' => 'boolean',
        'negative_marking' => 'float',
        'proctoring_enabled' => 'boolean',
        'require_camera' => 'boolean',
        'require_microphone' => 'boolean',
        'detect_no_face' => 'boolean',
        'detect_multiple_faces' => 'boolean',
        'detect_talking' => 'boolean',
        'max_warnings' => 'integer',
        'pre_exam_countdown_seconds' => 'integer',
    ];

    protected function normalizeAcademicField($value): array
    {
        if (empty($value)) {
            return [];
        }

        if (is_array($value)) {
            return array_values($value);
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return array_values($decoded);
            }

            if (str_contains($value, ',')) {
                return array_values(array_filter(array_map('trim', explode(',', $value)), fn ($item) => $item !== ''));
            }

            return [$value];
        }

        return (array) $value;
    }

    public function getDepartmentAttribute($value)
    {
        return $this->normalizeAcademicField($value);
    }

    public function getSemesterAttribute($value)
    {
        return $this->normalizeAcademicField($value);
    }

    protected function encodeAcademicField($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if (is_array($value)) {
            $encoded = array_values(array_filter($value, fn ($item) => $item !== null && $item !== ''));
            if (count($encoded) === 0) {
                return null;
            }

            if (count($encoded) === 1) {
                return (string) $encoded[0];
            }

            return implode(',', $encoded);
        }

        return $value;
    }

    protected static function normalizeAcademicFilterValues($values): array
    {
        if (! is_array($values)) {
            $values = [$values];
        }

        return array_values(array_unique(array_filter(array_map(
            fn ($value) => is_string($value) ? trim($value) : $value,
            $values
        ), fn ($value) => $value !== null && $value !== '')));
    }

    public static function applyAcademicFieldFilter($query, string $field, $values, bool $includeUnassigned = false)
    {
        $values = self::normalizeAcademicFilterValues($values);

        if (count($values) === 0) {
            return $query;
        }

        return $query->where(function ($fieldQuery) use ($field, $values, $includeUnassigned) {
            $started = false;

            if ($includeUnassigned) {
                $fieldQuery->whereNull($field)->orWhere($field, '');
                $started = true;
            }

            foreach ($values as $value) {
                $method = $started ? 'orWhere' : 'where';

                $fieldQuery->{$method}(function ($valueQuery) use ($field, $value) {
                    $valueQuery->where($field, $value)
                        ->orWhere($field, 'like', $value . ',%')
                        ->orWhere($field, 'like', $value . ', %')
                        ->orWhere($field, 'like', '%,' . $value . ',%')
                        ->orWhere($field, 'like', '%, ' . $value . ',%')
                        ->orWhere($field, 'like', '%,' . $value)
                        ->orWhere($field, 'like', '%, ' . $value)
                        ->orWhereJsonContains($field, $value);
                });

                $started = true;
            }
        });
    }

    public function setDepartmentAttribute($value)
    {
        $this->attributes['department'] = $this->encodeAcademicField($value);
    }

    public function setSemesterAttribute($value)
    {
        $this->attributes['semester'] = $this->encodeAcademicField($value);
    }

    // Relationships
    public function attempts()
    {
        return $this->hasMany(\App\Models\ExamAttempt::class);
    }

    // Exam belongs to a creator (admin/teacher)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Exam has many questions
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    // Exam belongs to many students
    public function students()
    {
        return $this->belongsToMany(User::class, 'exam_user');
    }

    // Exam has many results
    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function violations()
    {
        return $this->hasMany(Violation::class);
    }

    public function aiGeneratedQuestions()
    {
        return $this->hasMany(AiGeneratedQuestion::class)->orderByDesc('id');
    }
}
