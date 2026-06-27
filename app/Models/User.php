<?php

namespace App\Models;

use App\Notifications\CustomResetPassword;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'college_name',
        'department',
        'registration_no',
        'semester',
        'phone',
        'sex',
        'date_of_birth',
        'profile_photo',
        'profile_photo_public_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Cast attributes.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'deleted_at' => 'datetime',
            'date_of_birth' => 'date',
        ];
    }

    /* ====================================================
       RELATIONSHIPS FOR ONLINE EXAMINATION SYSTEM
       ==================================================== */

    // Exams created by this user (Admin/Teacher)
    public function createdExams()
    {
        return $this->hasMany(Exam::class, 'created_by');
    }

    // Exams assigned to this user (Student)
    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'exam_user');
    }

    // All responses submitted by this user
    public function responses()
    {
        return $this->hasMany(Response::class);
    }

    // All results of this user
    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function violations()
    {
        return $this->hasMany(Violation::class);
    }

    public function profilePhotoUrl()
    {
        if ($this->profile_photo) {
            if (str_starts_with($this->profile_photo, 'http://') || str_starts_with($this->profile_photo, 'https://')) {
                return $this->profile_photo;
            }

            return asset('storage/'.$this->profile_photo);
        }

        if ($this->sex === 'female') {
            return asset('images/default-female.png');
        }

        if ($this->sex === 'male') {
            return asset('images/default-male.png');
        }

        return $this->generatedDefaultAvatarUrl();
    }

    protected function generatedDefaultAvatarUrl(): string
    {
        $name = trim((string) $this->name);
        $parts = preg_split('/\s+/', $name, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $initials = collect($parts)
            ->take(2)
            ->map(fn (string $part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');

        if ($initials === '') {
            $initials = 'S';
        }

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 120" role="img" aria-label="Default avatar">
  <defs>
    <linearGradient id="avatarBg" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#0f172a" />
      <stop offset="100%" stop-color="#1e3a8a" />
    </linearGradient>
    <linearGradient id="avatarAccent" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#22d3ee" />
      <stop offset="100%" stop-color="#38bdf8" />
    </linearGradient>
  </defs>
  <rect width="120" height="120" rx="28" fill="url(#avatarBg)" />
  <circle cx="60" cy="45" r="22" fill="url(#avatarAccent)" opacity="0.95" />
  <path d="M26 101c5-18 19-28 34-28s29 10 34 28" fill="url(#avatarAccent)" opacity="0.9" />
  <text x="60" y="68" text-anchor="middle" font-family="Arial, sans-serif" font-size="28" font-weight="700" fill="#e0f2fe">{$initials}</text>
</svg>
SVG;

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new CustomResetPassword($token));
    }

    public function profileChangeRequests()
    {
        return $this->hasMany(StudentProfileChangeRequest::class);
    }

    public function pendingProfileChangeRequest()
    {
        return $this->hasOne(StudentProfileChangeRequest::class)->where('status', 'pending')->latestOfMany();
    }

}
