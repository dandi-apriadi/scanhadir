<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Holiday extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'type',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Check if a specific date is a holiday
    public static function isHoliday($date)
    {
        return static::where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->exists();
    }

    // Scope for active holidays (current and future)
    public function scopeActive(Builder $query)
    {
        return $query->where('end_date', '>=', now()->toDateString());
    }

    // Scope for past holidays
    public function scopePast(Builder $query)
    {
        return $query->where('end_date', '<', now()->toDateString());
    }
}
