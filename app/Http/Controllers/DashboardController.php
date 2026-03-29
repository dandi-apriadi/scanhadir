<?php

namespace App\Http\Controllers;

use App\Exports\AttendanceExport;
use App\Models\Attendance;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\Subject;
use App\Models\User;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function landing() {
        return view('landing');
    }

    public function loginStudent() {
        return view('auth.login_student');
    }

    public function loginAdmin() {
        return view('auth.login_admin');
    }

    public function forgotPassword() {
        return view('auth.forgot_password');
    }

    public function studentDashboard() {
        return view('student.dashboard', ['title' => 'Ringkasan Aktivitas']);
    }

    public function studentIzin()
    {
        $student = $this->getAuthenticatedStudent();

        $leaveHistory = Attendance::query()
            ->where('student_id', $student->id)
            ->whereIn('status', ['sick', 'excused'])
            ->orderByDesc('date')
            ->limit(20)
            ->get();

        return view('student.izin', [
            'title' => 'Riwayat & Izin',
            'student_name' => $student->user?->name,
            'class' => $student->class?->name,
            'leaveHistory' => $leaveHistory,
        ]);
    }

    public function studentProfil()
    {
        $student = $this->getAuthenticatedStudent();

        return view('student.profil', [
            'title' => 'Identitas Siswa',
            'student_id' => $student->id,
            'student_name' => $student->user?->name,
            'class' => $student->class?->name,
            'nisn' => $student->nisn,
        ]);
    }

    public function studentManual()
    {
        $student = $this->getAuthenticatedStudent();

        return view('student.manual_attendance', [
            'title' => 'Absensi Manual',
            'student_name' => $student->user?->name,
            'class' => $student->class?->name,
            'nisn' => $student->nisn,
        ]);
    }

    public function storeStudentIzin(Request $request)
    {
        $student = $this->getAuthenticatedStudent();

        $validated = $request->validate([
            'type' => ['required', 'in:sick,excused'],
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date', 'after_or_equal:date_from'],
            'reason' => ['required', 'string', 'max:2000'],
        ]);

        $from = \Carbon\Carbon::parse($validated['date_from'])->startOfDay();
        $to = \Carbon\Carbon::parse($validated['date_to'])->startOfDay();

        for ($date = $from->copy(); $date->lte($to); $date->addDay()) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'date' => $date->toDateString(),
                ],
                [
                    'status' => $validated['type'],
                    'notes' => $validated['reason'],
                    'check_in' => null,
                    'check_out' => null,
                ]
            );
        }

        return redirect()
            ->route('student.izin')
            ->with('status', 'Pengajuan izin berhasil disimpan.');
    }

    public function storeStudentManual(Request $request)
    {
        $student = $this->getAuthenticatedStudent();

        $validated = $request->validate([
            'status' => ['required', 'in:present,late'],
            'date' => ['required', 'date'],
            'check_in' => ['required', 'date_format:H:i'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        Attendance::updateOrCreate(
            [
                'student_id' => $student->id,
                'date' => $validated['date'],
            ],
            [
                'status' => $validated['status'],
                'check_in' => $validated['check_in'] . ':00',
                'notes' => $validated['notes'] ?: null,
            ]
        );

        return redirect()
            ->route('student.manual')
            ->with('status', 'Absensi manual berhasil disimpan.');
    }

    public function adminDashboard() {
        return view('admin.dashboard', ['title' => 'Ringkasan Sistem']);
    }

    public function adminAnalytics() {
        return view('admin.analytics', ['title' => 'Analisis Statistik']);
    }

    public function adminLogs(Request $request)
    {
        $dateFrom = trim((string) $request->query('date_from', now()->toDateString()));
        $dateTo = trim((string) $request->query('date_to', $dateFrom));
        $classId = (int) $request->query('class_id', 0);
        $status = trim((string) $request->query('status', ''));

        $allowedStatuses = ['present', 'late', 'sick', 'excused', 'absent'];

        $logs = Attendance::query()
            ->with(['student.user:id,name', 'student.class:id,name'])
            ->when($dateFrom !== '', fn ($query) => $query->whereDate('date', '>=', $dateFrom))
            ->when($dateTo !== '', fn ($query) => $query->whereDate('date', '<=', $dateTo))
            ->when($classId > 0, fn ($query) => $query->whereHas('student', fn ($studentQuery) => $studentQuery->where('class_id', $classId)))
            ->when(in_array($status, $allowedStatuses, true), fn ($query) => $query->where('status', $status))
            ->orderByDesc('date')
            ->orderByDesc('check_in')
            ->paginate(15)
            ->withQueryString();

        $todayAttendance = Attendance::query()->whereDate('date', now()->toDateString());
        $todayTotalStudents = Student::count();
        $todayPresent = (clone $todayAttendance)->whereIn('status', ['present', 'late'])->count();
        $todayRate = $todayTotalStudents > 0 ? round(($todayPresent / $todayTotalStudents) * 100, 1) : 0;

        return view('admin.logs', [
            'title' => 'Log Kehadiran',
            'logs' => $logs,
            'classOptions' => StudentClass::query()->orderBy('name')->get(['id', 'name']),
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'classId' => $classId,
            'status' => $status,
            'todayRate' => $todayRate,
        ]);
    }

    public function adminIzinApproval() {
        return view('admin.izin_approval', ['title' => 'Persetujuan Izin']);
    }

    public function adminSettings() {
        return view('admin.settings', ['title' => 'Pengaturan']);
    }

    public function adminScanner() {
        return view('admin.scanner', ['title' => 'Terminal Scanner']);
    }

    public function adminReportPdf() {
        return view('admin.report_pdf', ['title' => 'Cetak Laporan']);
    }

    public function adminReports(ReportService $reportService)
    {
        $date = now()->toDateString();

        return view('admin.reports', [
            'summary' => $reportService->getDailyAttendanceSummary($date),
            'rows' => $reportService->getAttendanceRowsForDate($date),
        ]);
    }

    public function exportAttendanceCsv(ReportService $reportService)
    {
        $rows = $reportService->getAttendanceRowsForDate(now()->toDateString());
        $export = new AttendanceExport($rows);

        return response($export->toCsvString(), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="attendance-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    public function exportAttendancePdf(ReportService $reportService)
    {
        $rows = $reportService->getAttendanceRowsForDate(now()->toDateString());

        $pdf = Pdf::loadView('reports.attendance', [
            'attendances' => $rows->map(fn (array $row) => (object) [
                'date' => $row['date'],
                'check_in' => $row['check_in'],
                'check_out' => $row['check_out'],
                'status' => $row['status'],
                'student' => (object) [
                    'user' => (object) ['name' => $row['student_name'] ?? '-'],
                    'class' => (object) ['name' => $row['class_name'] ?? '-'],
                ],
            ]),
        ]);

        return response()->streamDownload(
            static fn () => print($pdf->output()),
            'attendance-' . now()->format('Y-m-d') . '.pdf'
        );
    }

    public function masterGuru(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $teachers = User::query()
            ->where('role', 'teacher')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->withCount('assignedClasses')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.master.guru', [
            'title' => 'Manajemen Guru',
            'teachers' => $teachers,
            'q' => $q,
            'teacherStats' => [
                'total' => User::where('role', 'teacher')->count(),
                'active' => User::where('role', 'teacher')->count(),
            ],
        ]);
    }

    public function masterSiswa(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $classId = (int) $request->query('class_id', 0);

        $students = Student::query()
            ->with(['user:id,name,email', 'class:id,name'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('nisn', 'like', "%{$q}%")
                        ->orWhereHas('user', function ($userQuery) use ($q) {
                            $userQuery->where('name', 'like', "%{$q}%")
                                ->orWhere('email', 'like', "%{$q}%");
                        });
                });
            })
            ->when($classId > 0, fn ($query) => $query->where('class_id', $classId))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.master.siswa', [
            'title' => 'Manajemen Siswa',
            'students' => $students,
            'classOptions' => StudentClass::query()->orderBy('name')->get(['id', 'name']),
            'q' => $q,
            'classId' => $classId,
            'studentStats' => [
                'total' => Student::count(),
            ],
        ]);
    }

    public function masterKelas(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $classes = StudentClass::query()
            ->withCount('students')
            ->with(['teachers' => function ($query) {
                $query->select('users.id', 'users.name');
            }])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('name', 'like', "%{$q}%")
                        ->orWhere('level', 'like', "%{$q}%")
                        ->orWhere('major', 'like', "%{$q}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $levelDistribution = StudentClass::query()
            ->selectRaw('level, COUNT(*) as total')
            ->groupBy('level')
            ->pluck('total', 'level');

        return view('admin.master.kelas', [
            'title' => 'Manajemen Kelas',
            'classes' => $classes,
            'q' => $q,
            'classStats' => [
                'total' => StudentClass::count(),
                'levels' => [
                    'X' => (int) ($levelDistribution['X'] ?? 0),
                    'XI' => (int) ($levelDistribution['XI'] ?? 0),
                    'XII' => (int) ($levelDistribution['XII'] ?? 0),
                ],
            ],
        ]);
    }

    public function masterJadwal(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $classId = (int) $request->query('class_id', 0);
        $day = trim((string) $request->query('day', ''));
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        $schedules = Schedule::query()
            ->with([
                'class:id,name',
                'subject:id,name,code',
                'teacher:id,name,email',
            ])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('room', 'like', "%{$q}%")
                        ->orWhereHas('subject', function ($subjectQuery) use ($q) {
                            $subjectQuery->where('name', 'like', "%{$q}%")
                                ->orWhere('code', 'like', "%{$q}%");
                        })
                        ->orWhereHas('teacher', fn ($teacherQuery) => $teacherQuery->where('name', 'like', "%{$q}%"))
                        ->orWhereHas('class', fn ($classQuery) => $classQuery->where('name', 'like', "%{$q}%"));
                });
            })
            ->when($classId > 0, fn ($query) => $query->where('class_id', $classId))
            ->when(in_array($day, $days, true), fn ($query) => $query->where('day', $day))
            ->orderByRaw("CASE day WHEN 'Senin' THEN 1 WHEN 'Selasa' THEN 2 WHEN 'Rabu' THEN 3 WHEN 'Kamis' THEN 4 WHEN 'Jumat' THEN 5 ELSE 99 END")
            ->orderBy('start_time')
            ->paginate(12)
            ->withQueryString();

        $weeklyMinutes = Schedule::all()->sum(function ($s) {
            $start = \Carbon\Carbon::parse($s->start_time);
            $end = \Carbon\Carbon::parse($s->end_time);
            return $start->diffInMinutes($end);
        });

        return view('admin.master.jadwal', [
            'title' => 'Jadwal Pelajaran',
            'schedules' => $schedules,
            'classOptions' => StudentClass::query()->orderBy('name')->get(['id', 'name']),
            'subjectOptions' => Subject::query()->orderBy('name')->get(['id', 'name', 'code']),
            'teacherOptions' => User::query()->where('role', 'teacher')->orderBy('name')->get(['id', 'name']),
            'dayOptions' => $days,
            'q' => $q,
            'classId' => $classId,
            'day' => $day,
            'scheduleStats' => [
                'weekly_hours' => round($weeklyMinutes / 60, 1),
                'occupied_rooms' => (int) Schedule::query()->whereNotNull('room')->distinct('room')->count('room'),
            ],
        ]);
    }

    public function storeJadwal(Request $request)
    {
        $validated = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'teacher_id' => ['required', 'exists:users,id', 'in:' . User::query()->where('role', 'teacher')->pluck('id')->implode(',')],
            'day' => ['required', 'in:Senin,Selasa,Rabu,Kamis,Jumat'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'room' => ['nullable', 'string', 'max:64'],
        ]);

        if ($this->hasScheduleConflict((int) $validated['class_id'], (int) $validated['teacher_id'], $validated['day'], $validated['start_time'], $validated['end_time'])) {
            return back()
                ->withErrors(['schedule' => 'Jadwal bentrok terdeteksi untuk kelas atau guru di rentang waktu tersebut.'])
                ->withInput();
        }

        Schedule::create([
            'class_id' => (int) $validated['class_id'],
            'subject_id' => (int) $validated['subject_id'],
            'teacher_id' => (int) $validated['teacher_id'],
            'day' => $validated['day'],
            'start_time' => $validated['start_time'] . ':00',
            'end_time' => $validated['end_time'] . ':00',
            'room' => $validated['room'] ?: null,
        ]);

        return redirect()->route('admin.master.jadwal')->with('status', 'Jadwal berhasil ditambahkan.');
    }

    public function updateJadwal(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'teacher_id' => ['required', 'exists:users,id', 'in:' . User::query()->where('role', 'teacher')->pluck('id')->implode(',')],
            'day' => ['required', 'in:Senin,Selasa,Rabu,Kamis,Jumat'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'room' => ['nullable', 'string', 'max:64'],
        ]);

        if ($this->hasScheduleConflict(
            (int) $validated['class_id'],
            (int) $validated['teacher_id'],
            $validated['day'],
            $validated['start_time'],
            $validated['end_time'],
            $schedule->id
        )) {
            return back()
                ->withErrors(['schedule' => 'Jadwal bentrok terdeteksi untuk kelas atau guru di rentang waktu tersebut.'])
                ->withInput();
        }

        $schedule->update([
            'class_id' => (int) $validated['class_id'],
            'subject_id' => (int) $validated['subject_id'],
            'teacher_id' => (int) $validated['teacher_id'],
            'day' => $validated['day'],
            'start_time' => $validated['start_time'] . ':00',
            'end_time' => $validated['end_time'] . ':00',
            'room' => $validated['room'] ?: null,
        ]);

        return redirect()->route('admin.master.jadwal')->with('status', 'Jadwal berhasil diperbarui.');
    }

    public function destroyJadwal(Schedule $schedule)
    {
        $schedule->delete();

        return redirect()->route('admin.master.jadwal')->with('status', 'Jadwal berhasil dihapus.');
    }

    public function masterMapel(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $group = trim((string) $request->query('group', ''));
        $groups = ['Kejuruan', 'Umum'];

        $subjects = Subject::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('code', 'like', "%{$q}%")
                        ->orWhere('name', 'like', "%{$q}%");
                });
            })
            ->when(in_array($group, $groups, true), fn ($query) => $query->where('group', $group))
            ->orderBy('code')
            ->paginate(10)
            ->withQueryString();

        return view('admin.master.mapel', [
            'title' => 'Mata Pelajaran',
            'subjects' => $subjects,
            'groupOptions' => $groups,
            'q' => $q,
            'group' => $group,
            'subjectStats' => [
                'total' => Subject::count(),
                'kejuruan' => Subject::where('group', 'Kejuruan')->count(),
                'umum' => Subject::where('group', 'Umum')->count(),
            ],
        ]);
    }

    public function storeMapel(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:32', 'unique:subjects,code'],
            'name' => ['required', 'string', 'max:255'],
            'group' => ['required', 'in:Kejuruan,Umum'],
        ]);

        Subject::create($validated);

        return redirect()->route('admin.master.mapel')->with('status', 'Mapel berhasil ditambahkan.');
    }

    public function updateMapel(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:32', 'unique:subjects,code,' . $subject->id],
            'name' => ['required', 'string', 'max:255'],
            'group' => ['required', 'in:Kejuruan,Umum'],
        ]);

        $subject->update($validated);

        return redirect()->route('admin.master.mapel')->with('status', 'Mapel berhasil diperbarui.');
    }

    public function destroyMapel(Subject $subject)
    {
        if ($subject->schedules()->exists()) {
            return redirect()->route('admin.master.mapel')
                ->withErrors(['mapel' => 'Mapel tidak dapat dihapus karena masih dipakai pada jadwal.']);
        }

        $subject->delete();

        return redirect()->route('admin.master.mapel')->with('status', 'Mapel berhasil dihapus.');
    }

    private function hasScheduleConflict(int $classId, int $teacherId, string $day, string $startTime, string $endTime, ?int $excludeScheduleId = null): bool
    {
        $hasClassConflict = Schedule::query()
            ->where('class_id', $classId)
            ->where('day', $day)
            ->where('start_time', '<', $endTime . ':00')
            ->where('end_time', '>', $startTime . ':00')
            ->when($excludeScheduleId, fn ($q) => $q->where('id', '!=', $excludeScheduleId))
            ->exists();

        if ($hasClassConflict) {
            return true;
        }

        return Schedule::query()
            ->where('teacher_id', $teacherId)
            ->where('day', $day)
            ->where('start_time', '<', $endTime . ':00')
            ->where('end_time', '>', $startTime . ':00')
            ->when($excludeScheduleId, fn ($q) => $q->where('id', '!=', $excludeScheduleId))
            ->exists();
    }

    public function teacherDashboard() {
        return view('teacher.dashboard', ['title' => 'Dashboard Guru']);
    }

    public function scanAttendance(Request $request)
    {
        $request->validate([
            'nisn' => 'required|string|max:20',
        ]);

        $student = Student::where('nisn', $request->nisn)
            ->with(['user', 'class'])
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa dengan NISN ' . $request->nisn . ' tidak ditemukan.',
            ], 404);
        }

        $date = now()->toDateString();
        $checkInTime = now()->format('H:i:s');
        
        // Check if already scanned today
        $existingAttendance = Attendance::where('student_id', $student->id)
            ->whereDate('date', $date)
            ->first();

        if ($existingAttendance) {
            // Update check_out if already have check_in
            $existingAttendance->update([
                'check_out' => $checkInTime,
            ]);
            $attendance = $existingAttendance;
        } else {
            // Determine status based on check_in time
            $hour = (int) now()->format('H');
            $minute = (int) now()->format('i');
            $status = 'present';
            
            // Example: If after 07:30, mark as late
            if ($hour > 7 || ($hour == 7 && $minute > 30)) {
                $status = 'late';
            }

            $attendance = Attendance::create([
                'student_id' => $student->id,
                'date' => $date,
                'check_in' => $checkInTime,
                'status' => $status,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kehadiran berhasil tercatat',
            'data' => [
                'student_name' => $student->user->name,
                'student_nisn' => $student->nisn,
                'class_name' => $student->class?->name,
                'check_in' => $attendance->check_in,
                'check_out' => $attendance->check_out,
                'status' => $attendance->status,
                'timestamp' => now()->format('H:i:s'),
            ],
        ]);
    }

    private function getAuthenticatedStudent(): Student
    {
        $user = auth()->user();

        abort_unless($user && $user->role === 'student', 403, 'Unauthorized');

        $student = Student::query()
            ->with(['user', 'class'])
            ->where('user_id', $user->id)
            ->first();

        abort_unless($student, 404, 'Data siswa tidak ditemukan.');

        return $student;
    }
}
