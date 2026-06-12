<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\MataKuliahDosenAssignment;
use App\Models\Schedule;
use App\Models\SemesterAkademik;
use App\Models\StudentClass;
use Illuminate\Support\Carbon;
use Livewire\Component;

class AttendanceAnalytics extends Component
{
    public $selectedClass = null;
    public $selectedSubject = null;
    public $selectedSemester = null;
    public $selectedYear = null;
    public $selectedMonth = null;

    public function mount()
    {
        $this->selectedYear = now()->year;
        $this->selectedMonth = now()->month;
        $this->selectedSemester = SemesterAkademik::where('is_active', true)->value('id');
    }

    public function render()
    {
        $teacher = auth()->user();
        $isAdmin = $teacher && $teacher->role === 'admin';
        
        // Get assigned subject IDs
        $assignedSubjectIds = $isAdmin
            ? \App\Models\Subject::pluck('id')
            : MataKuliahDosenAssignment::where('user_id', $teacher?->id)->pluck('subject_id');
        
        // Get class IDs from schedules
        $assignedClassIds = Schedule::whereIn('subject_id', $assignedSubjectIds)
            ->pluck('class_id')
            ->unique();

        // Apply semester filter
        if ($this->selectedSemester) {
            $assignedClassIds = Schedule::where('semester_akademik_id', $this->selectedSemester)
                ->whereIn('subject_id', $assignedSubjectIds)
                ->pluck('class_id')
                ->unique();
        }

        // Get classes for filter
        $classes = $isAdmin
            ? StudentClass::query()->orderBy('name')->get()
            : StudentClass::whereIn('id', $assignedClassIds)->orderBy('name')->get();

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

        // Get late students ("Siswa Terlambat")
        $lateStudents = $this->getLateStudents($effectiveClassIds);

        // Get semesters for filter
        $semesters = SemesterAkademik::orderByDesc('is_active')->orderByDesc('tanggal_mulai')->get();

        $layout = $isAdmin ? 'layouts.admin' : 'layouts.teacher';

        return view('livewire.attendance-analytics', [
            'classes' => $classes,
            'semesters' => $semesters,
            'analyticsData' => $analyticsData,
            'monthlyTrend' => $monthlyTrend,
            'classComparison' => $classComparison,
            'studentPerformance' => $studentPerformance,
            'lateStudents' => $lateStudents,
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

        $total = $attendances->count();
        $present = $attendances->whereIn('status', Attendance::statusAliases('Hadir'))->count();
        $late = $attendances->whereIn('status', Attendance::statusAliases('Telat'))->count();
        $sick = $attendances->whereIn('status', Attendance::statusAliases('Sakit'))->count();
        $excused = $attendances->whereIn('status', Attendance::statusAliases('Izin'))->count();
        $absent = $attendances->whereIn('status', Attendance::statusAliases('Alpa'))->count();

        return [
            'total' => $total,
            'present' => $present,
            'late' => $late,
            'sick' => $sick,
            'excused' => $excused,
            'absent' => $absent,
            'presentPercentage' => $total > 0 ? round(($present / $total) * 100, 1) : 0,
            'latePercentage' => $total > 0 ? round(($late / $total) * 100, 1) : 0,
            'absentPercentage' => $total > 0 ? round(($absent / $total) * 100, 1) : 0,
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
                'present' => $attendances->whereIn('status', Attendance::statusAliases('Hadir'))->count(),
                'late' => $attendances->whereIn('status', Attendance::statusAliases('Telat'))->count(),
                'absent' => $attendances->whereIn('status', Attendance::statusAliases('Alpa'))->count(),
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
                'present' => $attendances->whereIn('status', Attendance::statusAliases('Hadir'))->count(),
                'late' => $attendances->whereIn('status', Attendance::statusAliases('Telat'))->count(),
                'absent' => $attendances->whereIn('status', Attendance::statusAliases('Alpa'))->count(),
                'presentPercentage' => $attendances->count() > 0 ? round(($attendances->whereIn('status', Attendance::statusAliases('Hadir'))->count() / $attendances->count()) * 100, 1) : 0,
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
                'photo' => $record->student?->photo_url,
                'total' => $records->count(),
                'present' => $records->whereIn('status', Attendance::statusAliases('Hadir'))->count(),
                'late' => $records->whereIn('status', Attendance::statusAliases('Telat'))->count(),
                'absent' => $records->whereIn('status', Attendance::statusAliases('Alpa'))->count(),
                'presentPercentage' => $records->count() > 0 ? round(($records->whereIn('status', Attendance::statusAliases('Hadir'))->count() / $records->count()) * 100, 1) : 0,
            ];
        }

        // Sort by present percentage
        usort($students, fn($a, $b) => $b['presentPercentage'] <=> $a['presentPercentage']);

        return array_slice($students, 0, 20); // Top 20 students
    }

    /**
     * Students with the most lateness in the selected month — "Siswa Terlambat".
     */
    private function getLateStudents($classIds)
    {
        $startDate = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1);
        $endDate = $startDate->clone()->endOfMonth();

        $grouped = Attendance::query()
            ->with(['student.user', 'student.class'])
            ->whereIn('status', Attendance::statusAliases('Telat'))
            ->whereBetween('date', [$startDate, $endDate])
            ->whereHas('student', fn($q) => $q->whereIn('class_id', $classIds))
            ->get()
            ->groupBy('student_id');

        $students = [];
        foreach ($grouped as $records) {
            $record = $records->first();
            $students[] = [
                'name' => $record->student?->user?->name,
                'nisn' => $record->student?->nisn,
                'class' => $record->student?->class?->name,
                'photo' => $record->student?->photo_url,
                'lateCount' => $records->count(),
            ];
        }

        usort($students, fn($a, $b) => $b['lateCount'] <=> $a['lateCount']);

        return array_slice($students, 0, 10);
    }
}
