<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\MataKuliahDosenAssignment;
use App\Models\Schedule;
use App\Models\SemesterAkademik;
use App\Models\StudentClass;
use Illuminate\Support\Facades\Cache;
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
    public $activeSession = null;
    public $activeSessionInfo = null;

    public function mount()
    {
        $user = auth()->user();

        if (!$user || !in_array($user->role, ['teacher', 'dosen'])) {
            abort(403, 'Unauthorized');
        }

        $this->selectedDate = now()->toDateString();
        $this->lastRefresh = now();
        $this->loadActiveSession();
    }

    /**
     * Load active session from cache
     */
    private function loadActiveSession()
    {
        $this->activeSession = Cache::get('active_attendance_session');
        
        if ($this->activeSession) {
            $schedule = Schedule::with(['subject', 'class', 'semesterAkademik'])
                ->find($this->activeSession['schedule_id'] ?? null);
            
            if ($schedule) {
                $this->activeSessionInfo = [
                    'subject_name' => $schedule->subject?->name ?? '-',
                    'subject_code' => $schedule->subject?->code ?? '-',
                    'class_name' => $schedule->class?->name ?? '-',
                    'semester' => $schedule->semesterAkademik?->display_name ?? '-',
                    'day' => $schedule->day,
                    'start_time' => Carbon::parse($schedule->start_time)->format('H:i'),
                    'end_time' => Carbon::parse($schedule->end_time)->format('H:i'),
                    'schedule_id' => $schedule->id,
                    'source' => $this->activeSession['source'] ?? 'manual',
                ];
            }
        }
    }

    public function refreshAttendance()
    {
        $this->lastRefresh = now();
        $this->loadActiveSession();
    }

    public function render()
    {
        $teacher = auth()->user();
        $isAdmin = $teacher && $teacher->role === 'admin';
        
        // Get assigned subject IDs via mata_kuliah_dosen_assignments or direct schedules
        $assignedSubjectIds = $isAdmin
            ? \App\Models\Subject::pluck('id')
            : MataKuliahDosenAssignment::where('user_id', $teacher?->id)->pluck('subject_id')
                ->merge(Schedule::where('teacher_id', $teacher?->id)->pluck('subject_id'))
                ->unique();
        
        // Get class IDs from schedules of assigned subjects or direct schedules
        $assignedClassIds = Schedule::whereIn('subject_id', $assignedSubjectIds)
            ->orWhere('teacher_id', $teacher?->id)
            ->pluck('class_id')
            ->merge($teacher?->assignedClasses()->pluck('classes.id') ?? collect())
            ->unique();
        
        $date = Carbon::parse($this->selectedDate)->toDateString();

        // Overall stats for today
        $stats = [
            'hadir' => 0,
            'telat' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alpa' => 0,
        ];

        if ($assignedClassIds->isNotEmpty()) {
            $attendanceQuery = Attendance::query()
                ->whereDate('date', $date)
                ->whereHas('student', fn ($query) => $query->whereIn('class_id', $assignedClassIds));

            $stats = [
                'hadir' => (clone $attendanceQuery)->whereIn('status', Attendance::statusAliases('Hadir'))->count(),
                'telat' => (clone $attendanceQuery)->whereIn('status', Attendance::statusAliases('Telat'))->count(),
                'sakit' => (clone $attendanceQuery)->whereIn('status', Attendance::statusAliases('Sakit'))->count(),
                'izin' => (clone $attendanceQuery)->whereIn('status', Attendance::statusAliases('Izin'))->count(),
                'alpa' => (clone $attendanceQuery)->whereIn('status', Attendance::statusAliases('Alpa'))->count(),
            ];
        }

        // Class details
        $classes = StudentClass::whereIn('id', $assignedClassIds)
            ->with('students.user')
            ->orderBy('name')
            ->get()
            ->map(function (StudentClass $class) use ($date) {
                $attendanceData = Attendance::query()
                    ->whereDate('date', $date)
                    ->whereHas('student', fn ($query) => $query->where('class_id', $class->id))
                    ->selectRaw('
                        COUNT(*) as total,
                        SUM(CASE WHEN status IN ("Hadir", "present") THEN 1 ELSE 0 END) as hadir,
                        SUM(CASE WHEN status IN ("Telat", "late") THEN 1 ELSE 0 END) as telat,
                        SUM(CASE WHEN status IN ("Alpa", "absent") THEN 1 ELSE 0 END) as alpa
                    ')
                    ->first();

                $total = $class->students->count();
                $percentage = $total > 0
                    ? round((($attendanceData->hadir ?? 0) / $total) * 100, 1)
                    : 0;

                return [
                    'id' => $class->id,
                    'name' => $class->name,
                    'total_students' => $total,
                    'hadir' => $attendanceData->hadir ?? 0,
                    'telat' => $attendanceData->telat ?? 0,
                    'alpa' => $attendanceData->alpa ?? 0,
                    'percentage' => $percentage,
                ];
            });

        // Recent attendance logs
        $recentLogs = Attendance::query()
            ->with(['student.user', 'student.class', 'schedule.subject'])
            ->whereDate('date', $date)
            ->whereHas('student', fn ($query) => $query->whereIn('class_id', $assignedClassIds))
            ->orderBy('check_in', 'desc')
            ->limit(15)
            ->get()
            ->map(fn ($att) => [
                'student_name' => $att->student?->user?->name,
                'class_name' => $att->student?->class?->name,
                'nisn' => $att->student?->nisn,
                'subject_name' => $att->schedule?->subject?->name ?? '-',
                'check_in' => $att->check_in,
                'status' => $att->status,
                'metode' => $att->metode_absensi ?? 'QR Code',
            ]);

        // Live scan statistics
        $totalTodayScans = Attendance::query()
            ->whereDate('date', $date)
            ->whereHas('student', fn ($query) => $query->whereIn('class_id', $assignedClassIds))
            ->count();

        // Latest scanned student
        $latestScannedStudent = Attendance::query()
            ->with(['student.user', 'student.class'])
            ->whereDate('date', $date)
            ->whereHas('student', fn ($query) => $query->whereIn('class_id', $assignedClassIds))
            ->orderBy('check_in', 'desc')
            ->first();

        $this->scanSessionActive = $totalTodayScans > 0;
        $this->scanCount = $totalTodayScans;
        $this->lastScanedStudent = $latestScannedStudent ? [
            'name' => $latestScannedStudent->student?->user?->name,
            'class' => $latestScannedStudent->student?->class?->name,
            'status' => $latestScannedStudent->status,
            'time' => $latestScannedStudent->check_in,
        ] : null;

        // Reload active session
        $this->loadActiveSession();

        return view('livewire.teacher-dashboard', [
            'stats' => $stats,
            'classes' => $classes,
            'recentLogs' => $recentLogs,
            'totalScans' => $totalTodayScans,
            'activeSessionInfo' => $this->activeSessionInfo,
        ]);
    }
}
