<?php

/**
 * Data Migration Script: Link Existing Attendance to Schedules
 * 
 * This script migrates existing attendance records to link them with schedules
 * based on date, class_id, and day of week.
 * 
 * Usage: php scripts/migrate_attendance_to_schedules.php
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Attendance;
use App\Models\Schedule;
use Carbon\Carbon;

echo "🔄 Starting attendance to schedule migration...\n\n";

// Day name mapping
$dayMap = [
    1 => 'Senin',
    2 => 'Selasa',
    3 => 'Rabu',
    4 => 'Kamis',
    5 => 'Jumat',
    6 => 'Sabtu',
    7 => 'Minggu',
];

// Get all attendance records without schedule_id
$attendances = Attendance::whereNull('schedule_id')
    ->with(['student'])
    ->get();

$total = $attendances->count();
$updated = 0;
$skipped = 0;
$errors = 0;

echo "Found {$total} attendance records to migrate.\n\n";

foreach ($attendances as $index => $attendance) {
    if (($index + 1) % 100 === 0) {
        echo "Processing {$index}/{$total}...\n";
    }

    try {
        $student = $attendance->student;
        if (!$student || !$student->class_id) {
            $skipped++;
            continue;
        }

        // Get day name from attendance date
        $date = Carbon::parse($attendance->date);
        $dayName = $dayMap[$date->dayOfWeekIso] ?? null;

        if (!$dayName) {
            $skipped++;
            continue;
        }

        // Find matching schedule for this class on this day
        $schedule = Schedule::where('class_id', $student->class_id)
            ->where('day', $dayName)
            ->orderBy('start_time')
            ->first();

        if ($schedule) {
            $attendance->update(['schedule_id' => $schedule->id]);
            $updated++;
        } else {
            $skipped++;
        }
    } catch (\Exception $e) {
        $errors++;
        echo "  ❌ Error on attendance ID {$attendance->id}: {$e->getMessage()}\n";
    }
}

echo "\n✅ Migration Complete!\n";
echo "   Updated: {$updated}\n";
echo "   Skipped: {$skipped}\n";
echo "   Errors: {$errors}\n";
echo "   Total: {$total}\n";
