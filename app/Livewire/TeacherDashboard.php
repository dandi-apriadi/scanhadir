<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\StudentClass;
use Livewire\Component;

class TeacherDashboard extends Component
{
    public function render()
    {
        $teacher = auth()->user();
        $assignedClassIds = $teacher?->assignedClasses()->pluck('classes.id') ?? collect();
        $today = now()->toDateString();

        $stats = [
            'present' => 0,
            'late' => 0,
            'absent' => 0,
        ];

        if ($assignedClassIds->isNotEmpty()) {
            $attendanceQuery = Attendance::query()
                ->whereDate('date', $today)
                ->whereHas('student', fn ($query) => $query->whereIn('class_id', $assignedClassIds));

            $stats = [
                'present' => (clone $attendanceQuery)->where('status', 'present')->count(),
                'late' => (clone $attendanceQuery)->where('status', 'late')->count(),
                'absent' => (clone $attendanceQuery)->where('status', 'absent')->count(),
            ];
        }

        $classes = $teacher?->assignedClasses()
            ->withCount('students')
            ->orderBy('name')
            ->get()
            ->map(function (StudentClass $class) use ($today) {
                $attendanceCount = Attendance::query()
                    ->whereDate('date', $today)
                    ->whereHas('student', fn ($query) => $query->where('class_id', $class->id))
                    ->count();

                return [
                    'name' => $class->name,
                    'students_count' => $class->students_count,
                    'attendance_count' => $attendanceCount,
                ];
            }) ?? collect();

        return view('livewire.teacher-dashboard', [
            'stats' => $stats,
            'classes' => $classes,
        ])->layout('layouts.teacher', ['title' => 'Teacher Dashboard']);
    }
}
