<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentClass extends Model
{
    protected $table = 'classes';

    protected $fillable = [
        'name',
        'level',
        'major',
    ];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'class_id');
    }
}
