<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\Schedule;
use App\Models\StudentClass;
use App\Services\AttendanceExportService;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class AttendanceReports extends Component
{
    use WithPagination;

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
        $isAdmin = $teacher && $teacher->role === 'admin';

        $assignedClassIds = Schedule::query()
            ->when(! $isAdmin, fn ($query) => $query->where('teacher_id', $teacher?->id))
            ->pluck('class_id')
            ->unique();

        // Get classes for filter
        $classes = $isAdmin
            ? StudentClass::query()->orderBy('name')->get()
            : StudentClass::whereIn('id', $assignedClassIds)->orderBy('name')->get();

        // Build query
        $query = Attendance::query()
            ->with(['student.user', 'student.class', 'schedule.subject'])
            ->whereBetween('date', [$this->dateFrom, $this->dateTo])
            ->whereHas('schedule', function ($scheduleQuery) use ($isAdmin, $teacher, $assignedClassIds): void {
                if ($isAdmin) {
                    if ($assignedClassIds->isNotEmpty()) {
                        $scheduleQuery->whereIn('class_id', $assignedClassIds);
                    }

                    return;
                }

                $scheduleQuery->where('teacher_id', $teacher?->id);
            });

        // Apply filters
        if ($this->selectedClass) {
            $query->whereHas('student', fn($q) => $q->where('class_id', $this->selectedClass));
        }

        if ($this->selectedStatus) {
            $query->where('status', $this->selectedStatus);
        }

        if ($this->searchStudent) {
            $search = $this->searchStudent;
            $query->where(function ($inner) use ($search) {
                $inner->whereHas('student.user', fn ($q) =>
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                )->orWhereHas('student', fn ($q) =>
                    $q->where('nisn', 'like', '%' . $search . '%')
                );
            });
        }

        // Apply sorting
        if ($this->sortBy === 'student') {
            $query->orderBy('student_id', $this->sortOrder);
        } else {
            $query->orderBy($this->sortBy, $this->sortOrder);
        }

        $reports = $query->paginate(20);

        // Get statistics
        $stats = $this->getStatistics($assignedClassIds);

        return view('livewire.attendance-reports', [
            'classes' => $classes,
            'reports' => $reports,
            'stats' => $stats,
            'statusOptions' => ['Hadir' => 'Hadir', 'Telat' => 'Telat', 'Sakit' => 'Sakit', 'Izin' => 'Izin', 'Alpa' => 'Alpa'],
        ])->layout('layouts.teacher', ['title' => 'Attendance Reports']);
    }

    public function exportXLSX()
    {
        $teacher = auth()->user();
        $isAdmin = $teacher && $teacher->role === 'admin';

        $assignedClassIds = Schedule::query()
            ->when(! $isAdmin, fn ($query) => $query->where('teacher_id', $teacher?->id))
            ->pluck('class_id')
            ->unique();

        $attendances = Attendance::query()
            ->with(['student.user', 'student.class', 'schedule.subject'])
            ->whereBetween('date', [$this->dateFrom, $this->dateTo])
            ->whereHas('schedule', function ($query) use ($isAdmin, $teacher): void {
                if (! $isAdmin) {
                    $query->where('teacher_id', $teacher?->id);
                }
            })
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
        $this->resetPage();
    }

    public function sortByField(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortOrder = $this->sortOrder === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortOrder = 'asc';
        }

        $this->resetPage();
    }

    private function getStatistics($classIds)
    {
        if ($classIds->isEmpty()) {
            return [
                'total' => 0,
                'present' => 0,
                'late' => 0,
                'sick' => 0,
                'excused' => 0,
                'absent' => 0,
            ];
        }

        $attendances = Attendance::query()
            ->whereBetween('date', [$this->dateFrom, $this->dateTo])
            ->when($classIds->isNotEmpty(), fn ($query) => $query->whereHas('schedule', fn ($scheduleQuery) => $scheduleQuery->whereIn('class_id', $classIds)))
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
