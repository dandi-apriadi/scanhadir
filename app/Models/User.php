<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function mataKuliahAssignments()
    {
        return $this->hasMany(MataKuliahDosenAssignment::class, 'user_id');
    }

    public function assignedSubjects()
    {
        return $this->hasManyThrough(Subject::class, MataKuliahDosenAssignment::class, 'user_id', 'subject_id');
    }

    public function teachingSchedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'teacher_id');
    }

    public function assignedClasses(): BelongsToMany
    {
        return $this->belongsToMany(StudentClass::class, 'schedules', 'teacher_id', 'class_id')->distinct();
    }

    public function getAssignedClassesCountAttribute(): int
    {
        return $this->teaching_schedules_count ?? $this->teachingSchedules()->count();
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isDosen(): bool
    {
        return $this->role === 'dosen';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
