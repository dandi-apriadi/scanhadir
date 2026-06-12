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
        'photo_path',
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
        return $this->belongsToMany(StudentClass::class, 'class_teacher', 'teacher_id', 'class_id')->distinct();
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

    public function isTeacher(): bool
    {
        return in_array($this->role, ['teacher', 'dosen'], true);
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo_path) {
            return asset('storage/' . $this->photo_path);
        }
        return 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"%3E%3Crect fill="%23e0e7ff" width="100" height="100"/%3E%3Ctext x="50" y="50" font-size="40" fill="%234f46e5" text-anchor="middle" dy=".3em" font-weight="bold"%3E'.substr($this->name, 0, 1).'%3C/text%3E%3C/svg%3E';
    }

    public function homeroomClass()
    {
        return $this->hasOne(StudentClass::class, 'homeroom_teacher_id');
    }

    /** A teacher who is assigned as a homeroom teacher (wali kelas) of any class. */
    public function isWaliKelas(): bool
    {
        if (! $this->isTeacher()) {
            return false;
        }

        // Cache per-request so layout calls don't repeat the query.
        static $cache = [];
        return $cache[$this->id] ??= StudentClass::where('homeroom_teacher_id', $this->id)->exists();
    }

    /** Human-friendly role label: Admin / Wali Kelas / Guru / Dosen / Siswa. */
    public function getRoleLabelAttribute(): string
    {
        return match (true) {
            $this->isAdmin() => 'Admin',
            $this->isWaliKelas() => 'Wali Kelas',
            $this->role === 'dosen' => 'Dosen',
            $this->role === 'teacher' => 'Guru',
            $this->isStudent() => 'Siswa',
            default => ucfirst((string) $this->role),
        };
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
