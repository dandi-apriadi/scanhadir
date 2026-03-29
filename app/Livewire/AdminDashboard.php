<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Carbon;

class AdminDashboard extends Component
{
    public $selectedDate;
    public $selectedClass = null;

    public function mount()
    {
        $this->selectedDate = now()->toDateString();
    }

    public function updatedSelectedDate()
    {
        // Reset when date changes
    }

    public function render()
    {
        $date = Carbon::parse($this->selectedDate)->toDateString();

        // Get statistics
        $query = Attendance::whereDate('date', $date);
        $classQuery = Student::query();

        if ($this->selectedClass) {
            $query->whereHas('student', fn ($q) => $q->where('class_id', $this->selectedClass));
            $classQuery->where('class_id', $this->selectedClass);
        }

        $stats = [
            'total_students' => $classQuery->count(),
            'total_scanned' => (clone $query)->count(),
            'present' => (clone $query)->where('status', 'present')->count(),
            'late' => (clone $query)->where('status', 'late')->count(),
            'sick' => (clone $query)->where('status', 'sick')->count(),
            'excused' => (clone $query)->where('status', 'excused')->count(),
            'absent' => (clone $query)->where('status', 'absent')->count(),
        ];

        $attendancePercentage = $stats['total_students'] > 0
            ? round(($stats['present'] / $stats['total_students']) * 100, 1)
            : 0;

        // Get recent attendance logs
        $recentLogs = Attendance::query()
            ->with(['student.user', 'student.class'])
            ->whereDate('date', $date)
            ->orderBy('check_in', 'desc')
            ->limit(10)
            ->get()
            ->map(fn ($att) => [
                'student_name' => $att->student?->user?->name,
                'class_name' => $att->student?->class?->name,
                'check_in' => $att->check_in,
                'status' => $att->status,
            ]);

        // Late students (for alerts)
        $lateStudents = Attendance::query()
            ->with(['student.user', 'student.class'])
            ->whereDate('date', $date)
            ->where('status', 'late')
            ->limit(5)
            ->get();

        // Classes overview
        $classes = StudentClass::query()
            ->withCount('students')
            ->orderBy('name')
            ->get()
            ->map(function (StudentClass $class) use ($date) {
                $attendanceInfo = Attendance::query()
                    ->whereDate('date', $date)
                    ->whereHas('student', fn ($q) => $q->where('class_id', $class->id))
                    ->selectRaw('
                        COUNT(*) as total,
                        SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present,
                        SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) as late,
                        SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent
                    ')
                    ->first();

                $percentage = $class->students_count > 0
                    ? round(($attendanceInfo->present / $class->students_count) * 100, 1)
                    : 0;

                return [
                    'id' => $class->id,
                    'name' => $class->name,
                    'total_students' => $class->students_count,
                    'attendance_total' => $attendanceInfo->total ?? 0,
                    'present' => $attendanceInfo->present ?? 0,
                    'late' => $attendanceInfo->late ?? 0,
                    'absent' => $attendanceInfo->absent ?? 0,
                    'percentage' => $percentage,
                ];
            });

        // System stats
        $totalUsers = User::count();
        $totalStudents = Student::count();
        $totalTeachers = User::where('role', 'teacher')->count();
        $totalClasses = StudentClass::count();

        return view('livewire.admin-dashboard', [
            'date' => $date,
            'stats' => $stats,
            'attendancePercentage' => $attendancePercentage,
            'recentLogs' => $recentLogs,
            'lateStudents' => $lateStudents,
            'classes' => $classes,
            'totalUsers' => $totalUsers,
            'totalStudents' => $totalStudents,
            'totalTeachers' => $totalTeachers,
            'totalClasses' => $totalClasses,
        ])->layout('layouts.admin', ['title' => 'Admin Dashboard']);
    }
}
