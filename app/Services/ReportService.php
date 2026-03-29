<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ReportService
{
    /**
     * Get daily attendance summary with optional class filter
     */
    public function getDailyAttendanceSummary(string $date, ?int $classId = null): array
    {
        $query = Attendance::query()->whereDate('date', $date);

        // Filter by class if provided
        if ($classId) {
            $query->whereHas('student', fn ($q) => $q->where('class_id', $classId));
        }

        $records = $query;
        $studentQuery = Student::query();

        if ($classId) {
            $studentQuery->where('class_id', $classId);
        }

        return [
            'date' => $date,
            'total_students' => $studentQuery->count(),
            'total_scanned' => (clone $records)->count(),
            'present' => (clone $records)->where('status', 'present')->count(),
            'late' => (clone $records)->where('status', 'late')->count(),
            'sick' => (clone $records)->where('status', 'sick')->count(),
            'excused' => (clone $records)->where('status', 'excused')->count(),
            'absent' => (clone $records)->where('status', 'absent')->count(),
        ];
    }

    /**
     * Get student attendance history with date range
     */
    public function getStudentAttendanceHistory(int $studentId, array $range = []): Collection
    {
        $start = Carbon::parse($range['start'] ?? now()->subDays(30)->toDateString())->toDateString();
        $end = Carbon::parse($range['end'] ?? now()->toDateString())->toDateString();

        return Attendance::query()
            ->where('student_id', $studentId)
            ->whereBetween('date', [$start, $end])
            ->orderBy('date')
            ->get();
    }

    /**
     * Get class attendance stats for a month with optional student filter
     */
    public function getClassAttendanceStats(int $classId, string $month, ?int $studentId = null): array
    {
        $monthStart = Carbon::parse($month . '-01')->startOfMonth()->toDateString();
        $monthEnd = Carbon::parse($month . '-01')->endOfMonth()->toDateString();

        $query = Attendance::query()
            ->whereHas('student', fn ($q) => $q->where('class_id', $classId))
            ->whereBetween('date', [$monthStart, $monthEnd]);

        // Filter by specific student if provided
        if ($studentId) {
            $query->where('student_id', $studentId);
        }

        $records = $query;

        return [
            'class_id' => $classId,
            'student_id' => $studentId,
            'month' => $month,
            'present' => (clone $records)->where('status', 'present')->count(),
            'late' => (clone $records)->where('status', 'late')->count(),
            'sick' => (clone $records)->where('status', 'sick')->count(),
            'excused' => (clone $records)->where('status', 'excused')->count(),
            'absent' => (clone $records)->where('status', 'absent')->count(),
        ];
    }

    /**
     * Get attendance rows for export with optional filters
     */
    public function getAttendanceRowsForDate(
        string $date,
        ?int $classId = null,
        ?int $studentId = null
    ): Collection {
        $query = Attendance::query()
            ->with(['student.user', 'student.class'])
            ->whereDate('date', $date);

        // Filter by class if provided
        if ($classId) {
            $query->whereHas('student', fn ($q) => $q->where('class_id', $classId));
        }

        // Filter by student if provided
        if ($studentId) {
            $query->where('student_id', $studentId);
        }

        return $query
            ->orderBy('check_in')
            ->get()
            ->map(function (Attendance $attendance) {
                return [
                    'date' => $attendance->date,
                    'student_name' => $attendance->student?->user?->name,
                    'class_name' => $attendance->student?->class?->name,
                    'nisn' => $attendance->student?->nisn,
                    'check_in' => $attendance->check_in,
                    'check_out' => $attendance->check_out,
                    'status' => $attendance->status,
                    'notes' => $attendance->notes ?? '-',
                ];
            });
    }

    /**
     * Get attendance summary for a teacher's assigned classes
     */
    public function getTeacherAttendanceSummary(int $teacherId, string $date): array
    {
        $teacher = User::find($teacherId);
        if (!$teacher) {
            return [];
        }

        $assignedClassIds = $teacher->assignedClasses()->pluck('classes.id')->toArray();

        $records = Attendance::query()
            ->whereHas('student', fn ($q) => $q->whereIn('class_id', $assignedClassIds))
            ->whereDate('date', $date);

        $totalStudents = Student::whereIn('class_id', $assignedClassIds)->count();

        return [
            'teacher_id' => $teacherId,
            'teacher_name' => $teacher->name,
            'date' => $date,
            'assigned_classes' => count($assignedClassIds),
            'total_students' => $totalStudents,
            'total_scanned' => (clone $records)->count(),
            'present' => (clone $records)->where('status', 'present')->count(),
            'late' => (clone $records)->where('status', 'late')->count(),
            'sick' => (clone $records)->where('status', 'sick')->count(),
            'excused' => (clone $records)->where('status', 'excused')->count(),
            'absent' => (clone $records)->where('status', 'absent')->count(),
        ];
    }

    /**
     * Get attendance rows for teacher's assigned classes
     */
    public function getTeacherAttendanceRows(
        int $teacherId,
        string $date,
        ?int $classId = null
    ): Collection {
        $teacher = User::find($teacherId);
        if (!$teacher) {
            return collect();
        }

        $assignedClassIds = $teacher->assignedClasses()->pluck('classes.id')->toArray();

        $query = Attendance::query()
            ->with(['student.user', 'student.class'])
            ->whereDate('date', $date)
            ->whereHas('student', fn ($q) => $q->whereIn('class_id', $assignedClassIds));

        // Filter by specific class if provided
        if ($classId) {
            $query->whereHas('student', fn ($q) => $q->where('class_id', $classId));
        }

        return $query
            ->orderBy('check_in')
            ->get()
            ->map(function (Attendance $attendance) {
                return [
                    'date' => $attendance->date,
                    'student_name' => $attendance->student?->user?->name,
                    'class_name' => $attendance->student?->class?->name,
                    'nisn' => $attendance->student?->nisn,
                    'check_in' => $attendance->check_in,
                    'check_out' => $attendance->check_out,
                    'status' => $attendance->status,
                ];
            });
    }

    /**
     * Check if date is a holiday (exclude from statistics if needed)
     */
    public function isHolidayDate(string $date): bool
    {
        return Holiday::isHoliday($date);
    }

    /**
     * Get date range stats excluding holidays
     */
    public function getAttendanceStatsForRange(
        string $startDate,
        string $endDate,
        ?int $classId = null,
        ?int $studentId = null
    ): array {
        $start = Carbon::parse($startDate)->toDateString();
        $end = Carbon::parse($endDate)->toDateString();

        $query = Attendance::query()->whereBetween('date', [$start, $end]);

        if ($classId) {
            $query->whereHas('student', fn ($q) => $q->where('class_id', $classId));
        }

        if ($studentId) {
            $query->where('student_id', $studentId);
        }

        $records = $query->get();

        // Separate by holiday status
        $workdays = 0;
        $currentDate = Carbon::parse($start);
        while ($currentDate->lte(Carbon::parse($end))) {
            if (!Holiday::isHoliday($currentDate->toDateString())) {
                $workdays++;
            }
            $currentDate->addDay();
        }

        return [
            'start_date' => $start,
            'end_date' => $end,
            'total_workdays' => $workdays,
            'total_records' => $records->count(),
            'present' => $records->where('status', 'present')->count(),
            'late' => $records->where('status', 'late')->count(),
            'sick' => $records->where('status', 'sick')->count(),
            'excused' => $records->where('status', 'excused')->count(),
            'absent' => $records->where('status', 'absent')->count(),
            'attendance_percentage' => $workdays > 0
                ? round(($records->where('status', 'present')->count() / $workdays) * 100, 2)
                : 0,
        ];
    }
}
