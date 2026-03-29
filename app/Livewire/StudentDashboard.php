<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\Student;
use Livewire\Component;
use Illuminate\Support\Carbon;

class StudentDashboard extends Component
{
    public $student;

    public function mount()
    {
        $user = auth()->user();
        
        if (!$user || $user->role !== 'student') {
            abort(403, 'Unauthorized');
        }

        $this->student = Student::with('class', 'user')->where('user_id', $user->id)->first();
        
        if (!$this->student) {
            abort(403, 'Student record not found');
        }
    }

    public function render()
    {
        // Attendance this month
        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();

        $monthAttendance = Attendance::query()
            ->where('student_id', $this->student->id)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN status = "sick" THEN 1 ELSE 0 END) as sick,
                SUM(CASE WHEN status = "excused" THEN 1 ELSE 0 END) as excused,
                SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent
            ')
            ->first();

        $workdaysCount = max(1, $monthAttendance->total ?? 1);

        $attendanceStats = [
            'total_days' => $workdaysCount,
            'present' => $monthAttendance->present ?? 0,
            'late' => $monthAttendance->late ?? 0,
            'sick' => $monthAttendance->sick ?? 0,
            'excused' => $monthAttendance->excused ?? 0,
            'absent' => $monthAttendance->absent ?? 0,
        ];

        $attendancePercentage = $workdaysCount > 0
            ? round(($attendanceStats['present'] / $workdaysCount) * 100, 1)
            : 0;

        // Today's status
        $todayAttendance = Attendance::query()
            ->where('student_id', $this->student->id)
            ->whereDate('date', now()->toDateString())
            ->first();

        $todayStatus = $todayAttendance?->status ?? 'not_recorded';
        $todayTime = $todayAttendance?->check_in;

        // Recent attendance history (last 10 records)
        $recentAttendance = Attendance::query()
            ->where('student_id', $this->student->id)
            ->orderBy('date', 'desc')
            ->with('student.class')
            ->limit(10)
            ->get()
            ->map(fn ($att) => [
                'date' => Carbon::parse($att->date)->format('d M Y'),
                'day' => Carbon::parse($att->date)->locale('id')->translatedFormat('l'),
                'status' => $att->status,
                'check_in' => $att->check_in,
                'check_out' => $att->check_out ?? '-',
                'notes' => $att->notes ?? '-',
            ])
            ->reverse()
            ->values();

        return view('livewire.student-dashboard', [
            'student' => $this->student,
            'attendanceStats' => $attendanceStats,
            'attendancePercentage' => $attendancePercentage,
            'todayStatus' => $todayStatus,
            'todayTime' => $todayTime,
            'recentAttendance' => $recentAttendance,
        ])->layout('layouts.student', ['title' => 'Student Dashboard']);
    }
}
