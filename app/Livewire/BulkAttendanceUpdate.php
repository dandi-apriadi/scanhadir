<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\MataKuliahDosenAssignment;
use App\Models\Schedule;
use App\Models\StudentClass;
use Illuminate\Support\Carbon;
use Livewire\Component;

class BulkAttendanceUpdate extends Component
{
    public $selectedClass = null;
    public $selectedDate = null;
    public $selectedStatus = null;
    public $bulkAction = null;
    public $selectedStudents = [];
    public $showConfirmation = false;
    public $message = '';

    protected $listeners = ['confirmUpdate'];

    public function mount()
    {
        $this->selectedDate = now()->toDateString();
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

        // Get classes
        $classes = $isAdmin
            ? StudentClass::query()->orderBy('name')->get()
            : StudentClass::whereIn('id', $assignedClassIds)->orderBy('name')->get();

        // Get students for selected class
        $students = collect();
        if ($this->selectedClass) {
            $class = StudentClass::find($this->selectedClass);
            if ($class && $assignedClassIds->contains($class->id)) {
                $students = $class->students()->with('user')->get();
            }
        }

        // Get attendance records for selected date and class
        $attendances = collect();
        $attendanceSummary = [];
        if ($this->selectedClass && $this->selectedDate) {
            $attendances = Attendance::query()
                ->with(['student.user', 'student.class', 'schedule.subject'])
                ->where('date', $this->selectedDate)
                ->whereHas('student', fn($q) => $q->where('class_id', $this->selectedClass))
                ->get()
                ->keyBy('student_id');

            $attendanceSummary = [
                'hadir' => $attendances->where('status', 'Hadir')->count(),
                'telat' => $attendances->where('status', 'Telat')->count(),
                'sakit' => $attendances->where('status', 'Sakit')->count(),
                'izin' => $attendances->where('status', 'Izin')->count(),
                'alpa' => $attendances->where('status', 'Alpa')->count(),
            ];
        }

        return view('livewire.bulk-attendance-update', [
            'classes' => $classes,
            'students' => $students,
            'attendances' => $attendances,
            'attendanceSummary' => $attendanceSummary,
            'statusOptions' => ['Hadir' => 'Hadir', 'Telat' => 'Telat', 'Sakit' => 'Sakit', 'Izin' => 'Izin', 'Alpa' => 'Alpa'],
        ])->layout('layouts.teacher', ['title' => 'Bulk Attendance Update']);
    }

    public function selectAllStudents()
    {
        $teacher = auth()->user();
        $isAdmin = $teacher && $teacher->role === 'admin';
        
        $assignedSubjectIds = $isAdmin
            ? \App\Models\Subject::pluck('id')
            : MataKuliahDosenAssignment::where('user_id', $teacher?->id)->pluck('subject_id');
        
        $assignedClassIds = Schedule::whereIn('subject_id', $assignedSubjectIds)
            ->pluck('class_id')
            ->unique();

        if ($this->selectedClass && $assignedClassIds->contains($this->selectedClass)) {
            $class = StudentClass::find($this->selectedClass);
            $this->selectedStudents = $class->students()->pluck('id')->toArray();
        }
    }

    public function deselectAllStudents()
    {
        $this->selectedStudents = [];
    }

    public function toggleStudent($studentId)
    {
        if (in_array($studentId, $this->selectedStudents)) {
            $this->selectedStudents = array_diff($this->selectedStudents, [$studentId]);
        } else {
            $this->selectedStudents[] = $studentId;
        }
    }

    public function updateStatus()
    {
        if (!$this->selectedStatus || !$this->selectedDate || !$this->selectedClass) {
            $this->message = 'Please select class, date, and status';
            return;
        }

        if (empty($this->selectedStudents)) {
            $this->message = 'Please select at least one student';
            return;
        }

        $this->showConfirmation = true;
    }

    public function confirmUpdate()
    {
        $teacher = auth()->user();
        $assignedClassIds = $teacher?->assignedClasses()->pluck('classes.id') ?? collect();

        // Verify teacher has access to this class
        if (!$assignedClassIds->contains($this->selectedClass)) {
            $this->message = 'You do not have access to this class';
            return;
        }

        $updatedCount = 0;

        foreach ($this->selectedStudents as $studentId) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'date' => $this->selectedDate,
                ],
                [
                    'status' => $this->selectedStatus,
                    'check_in' => now()->setTime(7, 0, 0), // Default check-in time
                ]
            );
            $updatedCount++;
        }

        $this->showConfirmation = false;
        $this->message = "Successfully updated $updatedCount student(s)";
        $this->selectedStudents = [];
        $this->selectedStatus = null;
        
        // Dispatch event to refresh view
        $this->dispatch('studentUpdated');
    }

    public function cancel()
    {
        $this->showConfirmation = false;
    }
}
