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
        $user = auth()->user();

        if (!$user || $user->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

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
            'hadir' => (clone $query)->where('status', 'Hadir')->count(),
            'telat' => (clone $query)->where('status', 'Telat')->count(),
            'sakit' => (clone $query)->where('status', 'Sakit')->count(),
            'izin' => (clone $query)->where('status', 'Izin')->count(),
            'alpa' => (clone $query)->where('status', 'Alpa')->count(),
            'absent' => (clone $query)->where('status', 'Alpa')->count(),
        ];

        $attendancePercentage = $stats['total_students'] > 0
            ? round(($stats['hadir'] / $stats['total_students']) * 100, 1)
            : 0;

        // Get recent attendance logs
        $recentLogs = Attendance::query()
            ->with(['student.user', 'student.class', 'schedule.subject'])
            ->whereDate('date', $date)
            ->orderBy('check_in', 'desc')
            ->limit(10)
            ->get()
            ->map(fn ($att) => [
                'student_name' => $att->student?->user?->name,
                'class_name' => $att->student?->class?->name,
                'subject_name' => $att->schedule?->subject?->name ?? '-',
                'check_in' => $att->check_in,
                'status' => $att->status,
                'metode' => $att->metode_absensi ?? 'QR Code',
            ]);

        // Late students (for alerts)
        $lateStudents = Attendance::query()
            ->with(['student.user', 'student.class'])
            ->whereDate('date', $date)
            ->where('status', 'Telat')
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
                        SUM(CASE WHEN status = "Hadir" THEN 1 ELSE 0 END) as hadir,
                        SUM(CASE WHEN status = "Telat" THEN 1 ELSE 0 END) as telat,
                        SUM(CASE WHEN status = "Alpa" THEN 1 ELSE 0 END) as alpa
                    ')
                    ->first();

                $percentage = $class->students_count > 0
                    ? round(($attendanceInfo->hadir / $class->students_count) * 100, 1)
                    : 0;

                return [
                    'id' => $class->id,
                    'name' => $class->name,
                    'total_students' => $class->students_count,
                    'attendance_total' => $attendanceInfo->total ?? 0,
                    'hadir' => $attendanceInfo->hadir ?? 0,
                    'telat' => $attendanceInfo->telat ?? 0,
                    'alpa' => $attendanceInfo->alpa ?? 0,
                    'percentage' => $percentage,
                ];
            });

        // System stats
        $totalUsers = User::count();
        $totalStudents = Student::count();
        $totalTeachers = User::whereIn('role', ['teacher', 'dosen'])->count();
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
        ]);
    }
}
