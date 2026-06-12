<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\StudentClass;
use Illuminate\Support\Carbon;
use Livewire\Component;

class WaliKelasDashboard extends Component
{
    public ?int $selectedClassId = null;
    public ?int $selectedYear = null;
    public ?int $selectedMonth = null;

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user || ! $user->isTeacher()) {
            abort(403, 'Unauthorized');
        }

        $this->selectedYear = now()->year;
        $this->selectedMonth = now()->month;
        $this->selectedClassId = $this->homeroomClasses()->value('id');
    }

    /** Classes where the current user is the homeroom teacher (wali kelas). */
    private function homeroomClasses()
    {
        return StudentClass::where('homeroom_teacher_id', auth()->id())->orderBy('name');
    }

    public function render()
    {
        $homeroomClasses = $this->homeroomClasses()->get(['id', 'name', 'level', 'major']);

        // Guard: chosen class must belong to this wali kelas.
        if ($this->selectedClassId && ! $homeroomClasses->contains('id', $this->selectedClassId)) {
            $this->selectedClassId = $homeroomClasses->first()?->id;
        }

        $class = $this->selectedClassId
            ? StudentClass::with(['students.user', 'homeroomTeacher'])->find($this->selectedClassId)
            : null;

        $today = now()->toDateString();
        $startDate = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->toDateString();
        $endDate = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->endOfMonth()->toDateString();

        $students = collect();
        $todaySnapshot = ['hadir' => 0, 'telat' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0, 'belum' => 0];
        $monthRate = 0;

        if ($class) {
            $studentIds = $class->students->pluck('id');

            // All month attendance for this class, grouped per student.
            $monthAttendance = Attendance::query()
                ->whereIn('student_id', $studentIds)
                ->whereBetween('date', [$startDate, $endDate])
                ->get()
                ->groupBy('student_id');

            // Today's attendance, keyed per student.
            $todayAttendance = Attendance::query()
                ->whereIn('student_id', $studentIds)
                ->whereDate('date', $today)
                ->get()
                ->keyBy('student_id');

            $hadirAliases = Attendance::statusAliases('Hadir');
            $telatAliases = Attendance::statusAliases('Telat');
            $sakitAliases = Attendance::statusAliases('Sakit');
            $izinAliases = Attendance::statusAliases('Izin');
            $alpaAliases = Attendance::statusAliases('Alpa');

            $totalPresentDays = 0;
            $totalDays = 0;

            $students = $class->students->map(function ($student) use (
                $monthAttendance, $todayAttendance,
                $hadirAliases, $telatAliases, $sakitAliases, $izinAliases, $alpaAliases,
                &$totalPresentDays, &$totalDays
            ) {
                $records = $monthAttendance->get($student->id, collect());

                $hadir = $records->whereIn('status', $hadirAliases)->count();
                $telat = $records->whereIn('status', $telatAliases)->count();
                $sakit = $records->whereIn('status', $sakitAliases)->count();
                $izin = $records->whereIn('status', $izinAliases)->count();
                $alpa = $records->whereIn('status', $alpaAliases)->count();
                $total = $records->count();

                $totalPresentDays += $hadir;
                $totalDays += $total;

                $todayStatus = $todayAttendance->get($student->id)?->status;

                return [
                    'id' => $student->id,
                    'name' => $student->user?->name ?? '-',
                    'nisn' => $student->nisn,
                    'photo' => $student->photo_url,
                    'today_status' => Attendance::normalizeStatus($todayStatus),
                    'hadir' => $hadir,
                    'telat' => $telat,
                    'sakit' => $sakit,
                    'izin' => $izin,
                    'alpa' => $alpa,
                    'total' => $total,
                    'rate' => $total > 0 ? round(($hadir / $total) * 100, 1) : 0,
                ];
            })->sortBy('name')->values();

            // Today snapshot counts.
            foreach ($class->students as $student) {
                $status = Attendance::normalizeStatus($todayAttendance->get($student->id)?->status);
                match ($status) {
                    'Hadir' => $todaySnapshot['hadir']++,
                    'Telat' => $todaySnapshot['telat']++,
                    'Izin' => $todaySnapshot['izin']++,
                    'Sakit' => $todaySnapshot['sakit']++,
                    'Alpa' => $todaySnapshot['alpa']++,
                    default => $todaySnapshot['belum']++,
                };
            }

            $monthRate = $totalDays > 0 ? round(($totalPresentDays / $totalDays) * 100, 1) : 0;
        }

        // Students needing attention (most alpa, then telat) this month.
        $attention = $students
            ->filter(fn ($s) => $s['alpa'] > 0 || $s['telat'] > 0)
            ->sortByDesc(fn ($s) => $s['alpa'] * 10 + $s['telat'])
            ->take(5)
            ->values();

        return view('livewire.wali-kelas-dashboard', [
            'homeroomClasses' => $homeroomClasses,
            'class' => $class,
            'students' => $students,
            'todaySnapshot' => $todaySnapshot,
            'monthRate' => $monthRate,
            'attention' => $attention,
        ])->layout('layouts.teacher', ['title' => 'Kelas Saya (Wali Kelas)']);
    }
}
