<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\StudentClass;
use Illuminate\Support\Carbon;
use Livewire\Component;

class AttendanceAnalytics extends Component
{
    public $selectedClass = null;
    public $selectedYear = null;
    public $selectedMonth = null;

    public function mount()
    {
        $this->selectedYear = now()->year;
        $this->selectedMonth = now()->month;
    }

    public function render()
    {
        $teacher = auth()->user();
        $isAdmin = $teacher && $teacher->role === 'admin';
        $assignedClassIds = $isAdmin
            ? StudentClass::query()->pluck('id')
            : ($teacher?->assignedClasses()->pluck('classes.id') ?? collect());

        // Get classes for filter
        $classes = $isAdmin
            ? StudentClass::query()->orderBy('name')->get()
            : ($teacher?->assignedClasses()->get() ?? collect());

        $effectiveClassIds = $this->selectedClass
            ? $assignedClassIds->intersect([(int) $this->selectedClass])->values()
            : $assignedClassIds;

        // Get analytics data
        $analyticsData = $this->getAnalyticsData($effectiveClassIds);

        // Get monthly trend
        $monthlyTrend = $this->getMonthlyTrend($effectiveClassIds);

        // Get class comparison
        $classComparison = $this->getClassComparison($effectiveClassIds);

        // Get student performance
        $studentPerformance = $this->getStudentPerformance($effectiveClassIds);

        $layout = $isAdmin ? 'layouts.admin' : 'layouts.teacher';

        return view('livewire.attendance-analytics', [
            'classes' => $classes,
            'analyticsData' => $analyticsData,
            'monthlyTrend' => $monthlyTrend,
            'classComparison' => $classComparison,
            'studentPerformance' => $studentPerformance,
        ])->layout($layout, ['title' => 'Attendance Analytics']);
    }

    private function getAnalyticsData($classIds)
    {
        $year = $this->selectedYear;
        $month = $this->selectedMonth;

        $startDate = Carbon::createFromDate($year, $month, 1);
        $endDate = $startDate->clone()->endOfMonth();

        $attendances = Attendance::query()
            ->whereBetween('date', [$startDate, $endDate])
            ->whereHas('student', fn($q) => $q->whereIn('class_id', $classIds))
            ->get();

        return [
            'total' => $attendances->count(),
            'present' => $attendances->where('status', 'present')->count(),
            'late' => $attendances->where('status', 'late')->count(),
            'sick' => $attendances->where('status', 'sick')->count(),
            'excused' => $attendances->where('status', 'excused')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'presentPercentage' => $attendances->count() > 0 ? round(($attendances->where('status', 'present')->count() / $attendances->count()) * 100, 1) : 0,
            'latePercentage' => $attendances->count() > 0 ? round(($attendances->where('status', 'late')->count() / $attendances->count()) * 100, 1) : 0,
            'absentPercentage' => $attendances->count() > 0 ? round(($attendances->where('status', 'absent')->count() / $attendances->count()) * 100, 1) : 0,
        ];
    }

    private function getMonthlyTrend($classIds)
    {
        $year = $this->selectedYear;
        $data = [];

        for ($month = 1; $month <= 12; $month++) {
            $startDate = Carbon::createFromDate($year, $month, 1);
            $endDate = $startDate->clone()->endOfMonth();

            $attendances = Attendance::query()
                ->whereBetween('date', [$startDate, $endDate])
                ->whereHas('student', fn($q) => $q->whereIn('class_id', $classIds))
                ->get();

            $data[] = [
                'month' => $startDate->format('M'),
                'present' => $attendances->where('status', 'present')->count(),
                'late' => $attendances->where('status', 'late')->count(),
                'absent' => $attendances->where('status', 'absent')->count(),
            ];
        }

        return $data;
    }

    private function getClassComparison($classIds)
    {
        $year = $this->selectedYear;
        $month = $this->selectedMonth;

        $startDate = Carbon::createFromDate($year, $month, 1);
        $endDate = $startDate->clone()->endOfMonth();

        $classes = collect();
        foreach ($classIds as $classId) {
            $class = StudentClass::find($classId);
            $attendances = Attendance::query()
                ->whereBetween('date', [$startDate, $endDate])
                ->whereHas('student', fn($q) => $q->where('class_id', $classId))
                ->get();

            $classes->push([
                'name' => $class->name,
                'total' => $attendances->count(),
                'present' => $attendances->where('status', 'present')->count(),
                'late' => $attendances->where('status', 'late')->count(),
                'absent' => $attendances->where('status', 'absent')->count(),
                'presentPercentage' => $attendances->count() > 0 ? round(($attendances->where('status', 'present')->count() / $attendances->count()) * 100, 1) : 0,
            ]);
        }

        return $classes;
    }

    private function getStudentPerformance($classIds)
    {
        $year = $this->selectedYear;
        $month = $this->selectedMonth;

        $startDate = Carbon::createFromDate($year, $month, 1);
        $endDate = $startDate->clone()->endOfMonth();

        $students = [];
        $attendances = Attendance::query()
            ->with(['student.user', 'student.class'])
            ->whereBetween('date', [$startDate, $endDate])
            ->whereHas('student', fn($q) => $q->whereIn('class_id', $classIds))
            ->get()
            ->groupBy('student_id');

        foreach ($attendances as $studentId => $records) {
            $record = $records->first();
            $students[] = [
                'name' => $record->student?->user?->name,
                'nisn' => $record->student?->nisn,
                'class' => $record->student?->class?->name,
                'total' => $records->count(),
                'present' => $records->where('status', 'present')->count(),
                'late' => $records->where('status', 'late')->count(),
                'absent' => $records->where('status', 'absent')->count(),
                'presentPercentage' => $records->count() > 0 ? round(($records->where('status', 'present')->count() / $records->count()) * 100, 1) : 0,
            ];
        }

        // Sort by present percentage
        usort($students, fn($a, $b) => $b['presentPercentage'] <=> $a['presentPercentage']);

        return array_slice($students, 0, 20); // Top 20 students
    }
}
