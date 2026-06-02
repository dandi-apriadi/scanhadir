<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendances';

    protected $fillable = [
        'student_id',
        'schedule_id',
        'date',
        'check_in',
        'check_out',
        'status',
        'metode_absensi',
        'approval_status',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'string',
        'check_out' => 'string',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public static function normalizeStatus(?string $status): ?string
    {
        return match ($status) {
            'present' => 'Hadir',
            'late' => 'Telat',
            'sick' => 'Sakit',
            'excused' => 'Izin',
            'absent' => 'Alpa',
            default => $status,
        };
    }

    public static function statusAliases(string $status): array
    {
        $normalized = self::normalizeStatus($status);

        return match ($normalized) {
            'Hadir' => ['Hadir', 'present'],
            'Telat' => ['Telat', 'late'],
            'Sakit' => ['Sakit', 'sick'],
            'Izin' => ['Izin', 'excused'],
            'Alpa' => ['Alpa', 'absent'],
            default => [$status],
        };
    }

    public function setDateAttribute($value): void
    {
        $this->attributes['date'] = Carbon::parse($value)->toDateString();
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }
}
