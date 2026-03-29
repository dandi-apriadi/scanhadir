<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\StudentClass;
use Livewire\Component;
use Illuminate\Support\Carbon;

class TeacherDashboard extends Component
{
    public $selectedDate;
    public $selectedClass = null;
    public $pollInterval = 3000; // 3 seconds
    public $lastRefresh;
    public $scanSessionActive = false;
    public $scanCount = 0;
    public $lastScanedStudent = null;

    public function mount()
    {
        $user = auth()->user();

        if (!$user || $user->role !== 'teacher') {
            abort(403, 'Unauthorized');
        }

        $this->selectedDate = now()->toDateString();
        $this->lastRefresh = now();
    }

    public function refreshAttendance()
    {
        // This method is called periodically via Livewire polling
        // It updates the attendance data in real-time
        $this->lastRefresh = now();
    }

    public function render()
    {
        $teacher = auth()->user();
        $assignedClassIds = $teacher?->assignedClasses()->pluck('classes.id') ?? collect();
        $date = Carbon::parse($this->selectedDate)->toDateString();

        // Overall stats for today
        $stats = [
            'present' => 0,
            'late' => 0,
            'sick' => 0,
            'excused' => 0,
            'absent' => 0,
        ];

        if ($assignedClassIds->isNotEmpty()) {
            $attendanceQuery = Attendance::query()
                ->whereDate('date', $date)
                ->whereHas('student', fn ($query) => $query->whereIn('class_id', $assignedClassIds));

            $stats = [
                'present' => (clone $attendanceQuery)->where('status', 'present')->count(),
                'late' => (clone $attendanceQuery)->where('status', 'late')->count(),
                'sick' => (clone $attendanceQuery)->where('status', 'sick')->count(),
                'excused' => (clone $attendanceQuery)->where('status', 'excused')->count(),
                'absent' => (clone $attendanceQuery)->where('status', 'absent')->count(),
            ];
        }

        // Class details
        $classes = $teacher?->assignedClasses()
            ->with('students.user')
            ->orderBy('name')
            ->get()
            ->map(function (StudentClass $class) use ($date) {
                $attendanceData = Attendance::query()
                    ->whereDate('date', $date)
                    ->whereHas('student', fn ($query) => $query->where('class_id', $class->id))
                    ->selectRaw('
                        COUNT(*) as total,
                        SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present,
                        SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) as late,
                        SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent
                    ')
                    ->first();

                $total = $class->students->count();
                $percentage = $total > 0
                    ? round(($attendanceData->present / $total) * 100, 1)
                    : 0;

                return [
                    'id' => $class->id,
                    'name' => $class->name,
                    'total_students' => $total,
                    'present' => $attendanceData->present ?? 0,
                    'late' => $attendanceData->late ?? 0,
                    'absent' => $attendanceData->absent ?? 0,
                    'percentage' => $percentage,
                ];
            }) ?? collect();

        // Recent attendance logs
        $recentLogs = Attendance::query()
            ->with(['student.user', 'student.class'])
            ->whereDate('date', $date)
            ->whereHas('student', fn ($query) => $query->whereIn('class_id', $assignedClassIds))
            ->orderBy('check_in', 'desc')
            ->limit(15)
            ->get()
            ->map(fn ($att) => [
                'student_name' => $att->student?->user?->name,
                'class_name' => $att->student?->class?->name,
                'nisn' => $att->student?->nisn,
                'check_in' => $att->check_in,
                'status' => $att->status,
            ]);

        // Live scan statistics (count of today's scans)
        $totalTodayScans = Attendance::query()
            ->whereDate('date', $date)
            ->whereHas('student', fn ($query) => $query->whereIn('class_id', $assignedClassIds))
            ->count();

        // Latest scanned student (for live feed)
        $latestScannedStudent = Attendance::query()
            ->with(['student.user', 'student.class'])
            ->whereDate('date', $date)
            ->whereHas('student', fn ($query) => $query->whereIn('class_id', $assignedClassIds))
            ->orderBy('check_in', 'desc')
            ->first();

        // Scan session stats (checks if scanning is active)
        $this->scanSessionActive = $totalTodayScans > 0;
        $this->scanCount = $totalTodayScans;
        $this->lastScanedStudent = $latestScannedStudent ? [
            'name' => $latestScannedStudent->student?->user?->name,
            'class' => $latestScannedStudent->student?->class?->name,
            'status' => $latestScannedStudent->status,
            'time' => $latestScannedStudent->check_in,
        ] : null;

        // Late students alert
        $lateStudents = Attendance::query()
            ->with(['student.user', 'student.class'])
            ->whereDate('date', $date)
            ->where('status', 'late')
            ->whereHas('student', fn ($query) => $query->whereIn('class_id', $assignedClassIds))
            ->limit(5)
            ->get();

        // Total stats
        $totalAssignedClasses = $assignedClassIds->count();
        $totalStudents = $teacher?->assignedClasses()
            ->withCount('students')
            ->get()
            ->sum('students_count') ?? 0;

        return view('livewire.teacher-dashboard', [
            'date' => $date,
            'stats' => $stats,
            'classes' => $classes,
            'recentLogs' => $recentLogs,
            'lateStudents' => $lateStudents,
            'totalAssignedClasses' => $totalAssignedClasses,
            'totalStudents' => $totalStudents,
            'totalScans' => $totalTodayScans,
            'latestScannedStudent' => $latestScannedStudent,
        ]);
    }
}
