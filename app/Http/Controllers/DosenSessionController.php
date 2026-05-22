<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\MataKuliahDosenAssignment;
use App\Models\Schedule;
use App\Models\SemesterAkademik;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\Subject;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DosenSessionController extends Controller
{
    /**
     * Redirect to courses page with info message.
     */
    public function create(Request $request): RedirectResponse
    {
        return redirect()->route('dosen-courses')
            ->with('info', 'Sesi presensi dibuka langsung dari jadwal pada halaman Mata Kuliah Saya.');
    }

    /**
     * Display the dosen's courses with grouped schedules per semester.
     */
    public function courses(Request $request): View
    {
        $user = $request->user();
        $assignedSubjectIds = $this->assignedSubjectIds((int) ($user?->id ?? 0));

        $activeSession = Cache::get('active_attendance_session');

        // Auto-close: only close if the specific schedule's time has passed on its scheduled day
        if ($activeSession) {
            $now = now();
            $scheduleId = $activeSession['schedule_id'] ?? null;

            if ($scheduleId) {
                $schedule = Schedule::find($scheduleId);

                if ($schedule) {
                    $currentDayNames = $this->dayNames($now);
                    $endTime = Carbon::parse($schedule->end_time);

                    $isScheduledDay = in_array($schedule->day, $currentDayNames);
                    $isPastEndTime = $now->gt($endTime);

                    if ($isScheduledDay && $isPastEndTime) {
                        Cache::forget('active_attendance_session');
                        $activeSession = null;
                    }
                } else {
                    Cache::forget('active_attendance_session');
                    $activeSession = null;
                }
            } else {
                // Legacy session without schedule_id
                $schedule = Schedule::query()
                    ->where('subject_id', $activeSession['subject_id'])
                    ->where('class_id', $activeSession['class_id'])
                    ->first();

                if ($schedule) {
                    $currentDayNames = $this->dayNames($now);
                    $endTime = Carbon::parse($schedule->end_time);
                    $isScheduledDay = in_array($schedule->day, $currentDayNames);
                    $isPastEndTime = $now->gt($endTime);

                    if ($isScheduledDay && $isPastEndTime) {
                        Cache::forget('active_attendance_session');
                        $activeSession = null;
                    }
                }
            }
        }

        // Auto-open: if no active session but a schedule is currently in its time window, open it
        if (! $activeSession) {
            $now = now();
            $currentTime = $now->format('H:i:s');
            $dayNames = $this->dayNames($now);

            $autoOpenSchedule = Schedule::query()
                ->with(['semesterAkademik', 'class', 'subject'])
                ->whereIn('day', $dayNames)
                ->where('start_time', '<=', $currentTime)
                ->where('end_time', '>=', $currentTime)
                ->when($user?->role !== 'admin', function ($builder) use ($user): void {
                    $subjectIds = $this->assignedSubjectIds((int) ($user?->id ?? 0));
                    if ($subjectIds === []) {
                        $builder->whereRaw('1 = 0');
                    } else {
                        $builder->whereIn('subject_id', $subjectIds);
                    }
                })
                ->orderBy('start_time')
                ->first();

            if ($autoOpenSchedule) {
                $activeSession = [
                    'subject_id' => $autoOpenSchedule->subject_id,
                    'class_id' => $autoOpenSchedule->class_id,
                    'schedule_id' => $autoOpenSchedule->id,
                    'started_at' => now()->toDateTimeString(),
                    'user_id' => $user?->id,
                    'source' => 'auto_schedule',
                ];

                Cache::put('active_attendance_session', $activeSession, now()->addHours(3));
            }
        }

        // Get all schedules grouped by semester
        $query = Schedule::with(['semesterAkademik', 'class', 'subject'])
            ->when($user?->role !== 'admin', function ($builder) use ($user): void {
                $subjectIds = $this->assignedSubjectIds((int) ($user?->id ?? 0));
                if ($subjectIds === []) {
                    $builder->whereRaw('1 = 0');
                } else {
                    $builder->whereIn('subject_id', $subjectIds);
                }
            })
            ->orderByDesc('semester_akademik_id')
            ->orderBy('subject_id')
            ->orderBy('class_id')
            ->orderBy('day')
            ->orderBy('start_time');

        $groupedSchedules = $query->get()
            ->groupBy(function (Schedule $schedule): string {
                return $schedule->semesterAkademik?->display_name ?? 'Belum ditentukan';
            })
            ->map(function ($items, string $semesterLabel): array {
                return [
                    'semester' => $semesterLabel,
                    'total' => $items->count(),
                    'items' => $items->values(),
                ];
            })
            ->values();

        return view('dosen.courses', [
            'groupedSchedules' => $groupedSchedules,
            'todayDate' => now()->toDateString(),
            'assignedSubjectIds' => $assignedSubjectIds,
            'activeSession' => $activeSession,
        ]);
    }

    /**
     * Start an attendance session for a specific schedule.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:classes,id',
        ]);

        $user = $request->user();

        if ($user?->role !== 'admin') {
            $isOwner = MataKuliahDosenAssignment::query()
                ->where('subject_id', (int) $data['subject_id'])
                ->where('user_id', (int) $user?->id)
                ->exists();

            if (! $isOwner) {
                return redirect()->route('dosen-courses')
                    ->with('error', 'Sesi hanya bisa dibuka untuk mata kuliah yang Anda ampu.');
            }
        }

        $scheduleQuery = Schedule::query()
            ->where('subject_id', $data['subject_id'])
            ->where('class_id', $data['class_id']);

        $hasAssignedSchedule = $scheduleQuery->exists();
        if (! $hasAssignedSchedule) {
            return redirect()->route('dosen-courses')
                ->with('error', 'Sesi hanya bisa dibuka untuk jadwal yang ditetapkan kepada dosen.');
        }

        // Find the specific schedule for today's day
        $now = now();
        $dayNames = $this->dayNames($now);
        $currentTime = $now->format('H:i:s');

        $targetSchedule = Schedule::query()
            ->where('subject_id', $data['subject_id'])
            ->where('class_id', $data['class_id'])
            ->whereIn('day', $dayNames)
            ->where('start_time', '<=', $currentTime)
            ->where('end_time', '>=', $currentTime)
            ->first();

        // If no matching schedule for today, find any schedule for this subject/class
        if (! $targetSchedule) {
            $targetSchedule = $scheduleQuery->first();
        }

        Cache::put('active_attendance_session', [
            'subject_id' => $data['subject_id'],
            'class_id' => $data['class_id'],
            'schedule_id' => $targetSchedule?->id,
            'started_at' => now()->toDateTimeString(),
            'user_id' => $request->user()?->id,
            'source' => 'schedule',
        ], now()->addHours(3));

        return redirect()->route('monitoring')->with('success', 'Sesi presensi jadwal berhasil diaktifkan.');
    }

    /**
     * Stop the active attendance session.
     */
    public function destroy(): RedirectResponse
    {
        Cache::forget('active_attendance_session');
        return redirect()->route('dosen-courses')->with('success', 'Sesi presensi telah ditutup.');
    }

    /**
     * Show session detail for today.
     */
    public function detail(): View|RedirectResponse
    {
        return $this->detailByDate(now()->toDateString());
    }

    /**
     * Show session detail by filter (date, subject, class).
     */
    public function detailByFilter(Request $request): View|RedirectResponse
    {
        $selectedDate = $this->normalizeDate((string) $request->query('date', ''));
        $subjectId = $request->query('subject_id');
        $classId = $request->query('class_id');

        return $this->detailByDate($selectedDate, $subjectId, $classId);
    }

    /**
     * Export session detail to Excel.
     */
    public function exportExcel(Request $request): StreamedResponse|RedirectResponse
    {
        $selectedDate = $this->normalizeDate((string) $request->query('date', ''));
        $subjectId = $request->query('subject_id');
        $classId = $request->query('class_id');
        
        $detailData = $this->buildDetailData($selectedDate, $subjectId, $classId);

        if (isset($detailData['redirect'])) {
            return $detailData['redirect'];
        }

        $fileDate = str_replace('-', '', $selectedDate);
        $filename = "detail_sesi_{$fileDate}.xlsx";

        return response()->streamDownload(function () use ($detailData): void {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'Tanggal');
            $sheet->setCellValue('B1', $detailData['selectedDate']);
            $sheet->setCellValue('A2', 'Mata Kuliah');
            $sheet->setCellValue('B2', $detailData['subject']->name . ' (' . $detailData['subject']->code . ')');
            $sheet->setCellValue('A3', 'Kelas');
            $sheet->setCellValue('B3', $detailData['class']->name);

            $headerRow = 5;
            $sheet->fromArray(['NISN', 'Nama', 'Status', 'Metode', 'Waktu Tap'], null, "A{$headerRow}");

            $rowIndex = $headerRow + 1;
            foreach ($detailData['studentRows'] as $row) {
                $sheet->fromArray([
                    $row['nisn'],
                    $row['nama'],
                    $row['status'] === 'Pending' ? 'Belum Absensi' : $row['status'],
                    $row['metode'],
                    $row['waktu_tap'],
                ], null, "A{$rowIndex}");

                $rowIndex++;
            }

            foreach (['A', 'B', 'C', 'D', 'E'] as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /**
     * Export session detail to PDF.
     */
    public function exportPdf(Request $request): Response|RedirectResponse
    {
        $selectedDate = $this->normalizeDate((string) $request->query('date', ''));
        $subjectId = $request->query('subject_id');
        $classId = $request->query('class_id');

        $detailData = $this->buildDetailData($selectedDate, $subjectId, $classId);

        if (isset($detailData['redirect'])) {
            return $detailData['redirect'];
        }

        $fileDate = str_replace('-', '', $selectedDate);
        $filename = "detail_sesi_{$fileDate}.pdf";

        return Pdf::loadView('dosen.session-detail-pdf', $detailData)
            ->setPaper('a4', 'portrait')
            ->download($filename);
    }

    /**
     * Build detail data for a specific date, subject, and class.
     */
    private function detailByDate(string $selectedDate, $subjectId = null, $classId = null): View|RedirectResponse
    {
        $detailData = $this->buildDetailData($selectedDate, $subjectId, $classId);

        if (isset($detailData['redirect'])) {
            return $detailData['redirect'];
        }

        return view('dosen.session-detail', $detailData);
    }

    /**
     * Build the data array for session detail view/export.
     */
    private function buildDetailData(string $selectedDate, $subjectId = null, $classId = null): array
    {
        $activeSession = Cache::get('active_attendance_session');
        $currentUser = request()->user();

        // Prioritize parameters, fallback to cache
        $finalSubjectId = $subjectId ?? ($activeSession['subject_id'] ?? null);
        $finalClassId = $classId ?? ($activeSession['class_id'] ?? null);

        if (! $finalSubjectId || ! $finalClassId) {
            return [
                'redirect' => redirect()->route('dosen-courses')->with('error', 'Buka sesi dari jadwal yang tersedia di Mata Kuliah Saya.'),
            ];
        }

        $subject = Subject::find($finalSubjectId);
        $class = StudentClass::find($finalClassId);

        if (! $subject || ! $class) {
            return [
                'redirect' => redirect()->route('dosen-courses')->with('error', 'Data mata kuliah atau kelas tidak ditemukan.'),
            ];
        }

        $date = Carbon::parse($selectedDate);
        $dayNames = $this->dayNames($date);

        // Find schedule by subject and class
        $scheduleQuery = Schedule::query()
            ->where('class_id', $class->id)
            ->where('subject_id', $subject->id);

        if ($currentUser?->role !== 'admin') {
            $isOwner = MataKuliahDosenAssignment::query()
                ->where('subject_id', $subject->id)
                ->where('user_id', (int) $currentUser?->id)
                ->exists();

            if (! $isOwner) {
                return [
                    'redirect' => redirect()->route('dosen-courses')->with('error', 'Anda tidak memiliki akses ke mata kuliah ini.'),
                ];
            }
        }

        // Get all schedule IDs for this subject and class
        $scheduleIds = $scheduleQuery->pluck('id');

        if ($scheduleIds->isEmpty()) {
            return [
                'redirect' => redirect()->route('dosen-courses')->with('error', 'Jadwal tidak ditemukan untuk mata kuliah/kelas ini.'),
            ];
        }

        $students = Student::query()
            ->where('class_id', $class->id)
            ->orderBy('nisn')
            ->get(['id', 'nisn', 'user_id']);

        $attendanceRows = collect();
        if ($scheduleIds->isNotEmpty()) {
            $attendanceRows = Attendance::query()
                ->whereDate('date', $selectedDate)
                ->whereIn('schedule_id', $scheduleIds)
                ->orderByDesc('created_at')
                ->get(['id', 'student_id', 'status', 'metode_absensi', 'check_in', 'created_at']);
        }

        $latestAttendanceByStudent = $attendanceRows
            ->unique('student_id')
            ->keyBy('student_id');

        $studentRows = $students->map(function (Student $student) use ($latestAttendanceByStudent): array {
            $attendance = $latestAttendanceByStudent->get($student->id);
            $status = $attendance?->status ?? 'Pending';

            return [
                'nisn' => $student->nisn,
                'nama' => $student->user?->name ?? '-',
                'status' => $status,
                'metode' => $attendance?->metode_absensi ?? '-',
                'waktu_tap' => $this->formatTapTime($status, $attendance?->check_in),
                'is_pending' => ! $attendance,
            ];
        })->values();

        $summary = [
            'total_students' => $students->count(),
            'hadir' => $studentRows->where('status', 'Hadir')->count(),
            'telat' => $studentRows->where('status', 'Telat')->count(),
            'sakit' => $studentRows->where('status', 'Sakit')->count(),
            'izin' => $studentRows->where('status', 'Izin')->count(),
            'alpa' => $studentRows->where('status', 'Alpa')->count(),
            'pending' => $studentRows->where('status', 'Pending')->count(),
        ];

        return [
            'activeSession' => $activeSession,
            'subject' => $subject,
            'class' => $class,
            'selectedDate' => $selectedDate,
            'summary' => $summary,
            'studentRows' => $studentRows,
        ];
    }

    /**
     * Get day names in both Indonesian and English for the given date.
     */
    private function dayNames(Carbon $date): array
    {
        $dayMapId = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];

        $dayMapEn = [
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday',
        ];

        return [
            $dayMapId[$date->dayOfWeekIso],
            $dayMapEn[$date->dayOfWeekIso],
        ];
    }

    /**
     * Normalize a date string to Y-m-d format.
     */
    private function normalizeDate(string $date): string
    {
        try {
            return Carbon::parse($date)->toDateString();
        } catch (\Throwable) {
            return now()->toDateString();
        }
    }

    /**
     * Get the subject IDs assigned to a specific user.
     */
    private function assignedSubjectIds(int $userId): array
    {
        if ($userId <= 0) {
            return [];
        }

        return MataKuliahDosenAssignment::query()
            ->where('user_id', $userId)
            ->pluck('subject_id')
            ->merge(Schedule::where('teacher_id', $userId)->pluck('subject_id'))
            ->unique()
            ->map(static fn ($id) => (int) $id)
            ->values()
            ->all();
    }

    /**
     * Format tap time for display.
     */
    private function formatTapTime(string $status, mixed $checkIn): string
    {
        if (! $checkIn) {
            return '-';
        }

        $time = substr((string) $checkIn, 0, 8);
        if (strtolower($status) === 'alpa' && $time === '00:00:00') {
            return '-';
        }

        return $time;
    }
}
