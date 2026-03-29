<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\StudentClass;
use App\Services\AttendanceExportService;
use Illuminate\Support\Carbon;
use Livewire\Component;

class AttendanceReports extends Component
{
    public $dateFrom;
    public $dateTo;
    public $selectedClass = null;
    public $selectedStatus = null;
    public $searchStudent = '';
    public $sortBy = 'date';
    public $sortOrder = 'desc';

    protected $listeners = ['exportXLSX'];

    public function mount()
    {
        $this->dateFrom = now()->subMonth()->toDateString();
        $this->dateTo = now()->toDateString();
    }

    public function render()
    {
        $teacher = auth()->user();
        $assignedClassIds = $teacher?->assignedClasses()->pluck('classes.id') ?? collect();

        // Get classes for filter
        $classes = $teacher?->assignedClasses()->get() ?? collect();

        // Build query
        $query = Attendance::query()
            ->with(['student.user', 'student.class'])
            ->whereBetween('date', [$this->dateFrom, $this->dateTo])
            ->whereHas('student', fn($q) => $q->whereIn('class_id', $assignedClassIds));

        // Apply filters
        if ($this->selectedClass) {
            $query->whereHas('student', fn($q) => $q->where('class_id', $this->selectedClass));
        }

        if ($this->selectedStatus) {
            $query->where('status', $this->selectedStatus);
        }

        if ($this->searchStudent) {
            $query->whereHas('student.user', fn($q) => 
                $q->where('name', 'like', '%' . $this->searchStudent . '%')
                  ->orWhere('email', 'like', '%' . $this->searchStudent . '%')
            )->orWhereHas('student', fn($q) => 
                $q->where('nisn', 'like', '%' . $this->searchStudent . '%')
            );
        }

        // Apply sorting
        if ($this->sortBy === 'student') {
            $query->orderBy('student_id', $this->sortOrder);
        } else {
            $query->orderBy($this->sortBy, $this->sortOrder);
        }

        $reports = $query->paginate(50);

        // Get statistics
        $stats = $this->getStatistics($assignedClassIds);

        return view('livewire.attendance-reports', [
            'classes' => $classes,
            'reports' => $reports,
            'stats' => $stats,
            'statusOptions' => ['present' => 'Present', 'late' => 'Late', 'sick' => 'Sick', 'excused' => 'Excused', 'absent' => 'Absent'],
        ])->layout('layouts.teacher', ['title' => 'Attendance Reports']);
    }

    public function exportXLSX()
    {
        $teacher = auth()->user();
        $assignedClassIds = $teacher?->assignedClasses()->pluck('classes.id') ?? collect();

        $attendances = Attendance::query()
            ->with(['student.user', 'student.class'])
            ->whereBetween('date', [$this->dateFrom, $this->dateTo])
            ->whereHas('student', fn($q) => $q->whereIn('class_id', $assignedClassIds))
            ->when($this->selectedClass, fn($q) => $q->whereHas('student', fn($sq) => $sq->where('class_id', $this->selectedClass)))
            ->when($this->selectedStatus, fn($q) => $q->where('status', $this->selectedStatus))
            ->when($this->searchStudent, fn($q) => 
                $q->whereHas('student.user', fn($sq) => 
                    $sq->where('name', 'like', '%' . $this->searchStudent . '%')
                )
            )
            ->orderBy('date', 'asc')
            ->orderBy('student_id', 'asc')
            ->get();

        $exportService = new AttendanceExportService();
        $options = [
            'date_from' => $this->dateFrom,
            'date_to' => $this->dateTo,
            'class_name' => $this->selectedClass ? StudentClass::find($this->selectedClass)->name : 'All Classes',
        ];

        $filePath = $exportService->exportToXLSX($attendances, $options);
        $fileName = basename($filePath);

        return response()->download($filePath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    public function resetFilters()
    {
        $this->selectedClass = null;
        $this->selectedStatus = null;
        $this->searchStudent = '';
        $this->dateFrom = now()->subMonth()->toDateString();
        $this->dateTo = now()->toDateString();
    }

    private function getStatistics($classIds)
    {
        $attendances = Attendance::query()
            ->whereBetween('date', [$this->dateFrom, $this->dateTo])
            ->whereHas('student', fn($q) => $q->whereIn('class_id', $classIds))
            ->when($this->selectedClass, fn($q) => $q->whereHas('student', fn($sq) => $sq->where('class_id', $this->selectedClass)))
            ->get();

        return [
            'total' => $attendances->count(),
            'present' => $attendances->where('status', 'present')->count(),
            'late' => $attendances->where('status', 'late')->count(),
            'sick' => $attendances->where('status', 'sick')->count(),
            'excused' => $attendances->where('status', 'excused')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
        ];
    }
}
