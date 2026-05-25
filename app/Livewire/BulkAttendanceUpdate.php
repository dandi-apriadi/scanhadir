<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\Schedule;
use App\Models\StudentClass;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Component;

class BulkAttendanceUpdate extends Component
{
    public ?int $selectedClass = null;
    public ?string $selectedDate = null;
    public ?string $selectedStatus = null;
    public ?string $bulkAction = null;
    public array $selectedStudents = [];
    public bool $showConfirmation = false;
    public string $message = '';

    protected $listeners = ['confirmUpdate'];

    public function mount()
    {
        $this->selectedDate = now()->toDateString();
    }

    public function render()
    {
        $teacher = auth()->user();
        $isAdmin = $teacher && $teacher->role === 'admin';

        $assignedClassIds = Schedule::query()
            ->when(! $isAdmin, fn ($query) => $query->where('teacher_id', $teacher?->id))
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

        $selectedSchedule = $this->resolveSelectedSchedule($teacher, $isAdmin);

        // Get attendance records for selected date and class
        $attendances = collect();
        $attendanceSummary = [];
        if ($selectedSchedule) {
            $attendances = Attendance::query()
                ->with(['student.user', 'student.class', 'schedule.subject'])
                ->where('schedule_id', $selectedSchedule->id)
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

    public function selectAllStudents(): void
    {
        $teacher = auth()->user();
        $isAdmin = $teacher && $teacher->role === 'admin';

        $assignedClassIds = Schedule::query()
            ->when(! $isAdmin, fn ($query) => $query->where('teacher_id', $teacher?->id))
            ->pluck('class_id')
            ->unique();

        if ($this->selectedClass && $assignedClassIds->contains($this->selectedClass)) {
            $class = StudentClass::find($this->selectedClass);
            $this->selectedStudents = $class->students()->pluck('id')->toArray();
        }
    }

    public function deselectAllStudents(): void
    {
        $this->selectedStudents = [];
    }

    public function toggleStudent(int $studentId): void
    {
        if (in_array($studentId, $this->selectedStudents)) {
            $this->selectedStudents = array_diff($this->selectedStudents, [$studentId]);
        } else {
            $this->selectedStudents[] = $studentId;
        }
    }

    public function updateStatus(): void
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

    public function confirmUpdate(): void
    {
        $teacher = auth()->user();
        $assignedClassIds = Schedule::query()
            ->when($teacher && $teacher->role !== 'admin', fn ($query) => $query->where('teacher_id', $teacher->id))
            ->pluck('class_id')
            ->unique();

        $selectedSchedule = $this->resolveSelectedSchedule($teacher, $teacher && $teacher->role === 'admin');

        if (! $selectedSchedule) {
            $this->message = 'Tidak ada jadwal yang cocok untuk kelas dan tanggal ini';
            return;
        }

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
                    'schedule_id' => $selectedSchedule->id,
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

    public function cancel(): void
    {
        $this->showConfirmation = false;
    }

    private function resolveSelectedSchedule(?User $teacher, bool $isAdmin): ?Schedule
    {
        if (! $this->selectedClass || ! $this->selectedDate) {
            return null;
        }

        $dayNames = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];

        $dayName = $dayNames[Carbon::parse($this->selectedDate)->dayOfWeekIso] ?? null;

        if (! $dayName) {
            return null;
        }

        return Schedule::query()
            ->where('class_id', $this->selectedClass)
            ->where('day', $dayName)
            ->when(! $isAdmin, fn ($query) => $query->where('teacher_id', $teacher?->id))
            ->orderBy('start_time')
            ->first();
    }
}
