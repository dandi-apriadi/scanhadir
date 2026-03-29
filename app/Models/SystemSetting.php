<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_name',
        'npsn',
        'school_address',
        'attendance_start_time',
        'late_tolerance_minutes',
        'active_days',
    ];

    protected $casts = [
        'active_days' => 'array',
    ];
}
