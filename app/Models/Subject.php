<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    protected $table = 'subjects';

    protected $fillable = [
        'code',
        'name',
        'group',
        'semester_akademik_id',
        'sks',
    ];

    public function semesterAkademik(): BelongsTo
    {
        return $this->belongsTo(SemesterAkademik::class, 'semester_akademik_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function dosenAssignment()
    {
        return $this->hasOne(MataKuliahDosenAssignment::class, 'subject_id');
    }
}
