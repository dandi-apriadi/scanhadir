<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\MataKuliahDosenAssignment;
use App\Models\Schedule;
use App\Models\SemesterAkademik;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\Subject;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\AttendanceExportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
            ->whereIn('status', ['Sakit', 'Izin'])
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
            'type' => ['required', 'in:Sakit,Izin'],
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date', 'after_or_equal:date_from'],
            'reason' => ['required', 'string', 'max:2000'],
        ]);

        $from = \Carbon\Carbon::parse($validated['date_from'])->startOfDay();
        $to = \Carbon\Carbon::parse($validated['date_to'])->startOfDay();

        for ($date = $from->copy(); $date->lte($to); $date->addDay()) {
            foreach ($this->resolveSchedulesForStudentDate($student, $date->toDateString()) as $schedule) {
                Attendance::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'schedule_id' => $schedule->id,
                        'date' => $date->toDateString(),
                    ],
                    [
                        'status' => $validated['type'],
                        'approval_status' => 'pending',
                        'approved_by' => null,
                        'approved_at' => null,
                        'rejected_by' => null,
                        'rejected_at' => null,
                        'rejection_reason' => null,
                        'notes' => $validated['reason'],
                        'check_in' => null,
                        'check_out' => null,
                    ]
                );
            }
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

        $scheduleId = $this->resolveAttendanceScheduleId($student, $validated['date']);

        if (! $scheduleId) {
            return back()->withErrors(['date' => 'Jadwal tidak ditemukan untuk tanggal tersebut.'])->withInput();
        }

        Attendance::updateOrCreate(
            [
                'student_id' => $student->id,
                'schedule_id' => $scheduleId,
                'date' => $validated['date'],
            ],
            [
                'status' => $validated['status'],
                'approval_status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'rejected_by' => null,
                'rejected_at' => null,
                'rejection_reason' => null,
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

        $allowedStatuses = ['Hadir', 'Telat', 'Sakit', 'Izin', 'Alpa'];

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
        $todayPresent = (clone $todayAttendance)->whereIn('status', ['Hadir', 'Telat'])->count();
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

    public function adminIzinApproval(Request $request)
    {
        $approval = trim((string) $request->query('approval', 'all'));
        $classId = (int) $request->query('class_id', 0);
        $q = trim((string) $request->query('q', ''));

        $allowedApproval = ['all', 'pending', 'approved', 'rejected'];
        if (!in_array($approval, $allowedApproval, true)) {
            $approval = 'all';
        }

        $submissions = Attendance::query()
            ->with([
                'student.user:id,name',
                'student.class:id,name',
                'approvedBy:id,name',
                'rejectedBy:id,name',
            ])
            ->whereIn('status', ['Sakit', 'Izin'])
            ->when($approval !== 'all', fn ($query) => $query->where('approval_status', $approval))
            ->when($classId > 0, fn ($query) => $query->whereHas('student', fn ($studentQuery) => $studentQuery->where('class_id', $classId)))
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('notes', 'like', "%{$q}%")
                        ->orWhereHas('student.user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$q}%"))
                        ->orWhereHas('student', fn ($studentQuery) => $studentQuery->where('nisn', 'like', "%{$q}%"));
                });
            })
            ->orderByRaw("CASE approval_status WHEN 'pending' THEN 1 WHEN 'rejected' THEN 2 WHEN 'approved' THEN 3 ELSE 99 END")
            ->orderByDesc('date')
            ->paginate(12)
            ->withQueryString();

        $counts = Attendance::query()
            ->selectRaw("COALESCE(approval_status, 'pending') as approval_status, COUNT(*) as total")
            ->whereIn('status', ['Sakit', 'Izin'])
            ->groupBy('approval_status')
            ->pluck('total', 'approval_status');

        return view('admin.izin_approval', [
            'title' => 'Persetujuan Izin',
            'submissions' => $submissions,
            'classOptions' => StudentClass::query()->orderBy('name')->get(['id', 'name']),
            'approval' => $approval,
            'classId' => $classId,
            'q' => $q,
            'counts' => [
                'all' => (int) ($counts->sum()),
                'pending' => (int) ($counts['pending'] ?? 0),
                'approved' => (int) ($counts['approved'] ?? 0),
                'rejected' => (int) ($counts['rejected'] ?? 0),
            ],
        ]);
    }

    public function approveIzinAttendance(Attendance $attendance)
    {
        abort_unless(in_array($attendance->status, ['Sakit', 'Izin'], true), 404);

        $attendance->update([
            'approval_status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejected_by' => null,
            'rejected_at' => null,
            'rejection_reason' => null,
        ]);

        return redirect()->route('admin.izin_approval')->with('status', 'Pengajuan berhasil disetujui.');
    }

    public function rejectIzinAttendance(Request $request, Attendance $attendance)
    {
        abort_unless(in_array($attendance->status, ['Sakit', 'Izin'], true), 404);

        $validated = $request->validate([
            'rejection_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $attendance->update([
            'approval_status' => 'rejected',
            'approved_by' => null,
            'approved_at' => null,
            'rejected_by' => auth()->id(),
            'rejected_at' => now(),
            'rejection_reason' => $validated['rejection_reason'] ?: null,
        ]);

        return redirect()->route('admin.izin_approval')->with('status', 'Pengajuan berhasil ditolak.');
    }

    public function adminSettings()
    {
        $settings = SystemSetting::query()->firstOrCreate(
            ['id' => 1],
            [
                'school_name' => 'SMK Negeri 1 Bandung',
                'npsn' => null,
                'school_address' => null,
                'attendance_start_time' => '07:00:00',
                'late_tolerance_minutes' => 15,
                'active_days' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
            ]
        );

        return view('admin.settings', [
            'title' => 'Pengaturan',
            'settings' => $settings,
            'dayOptions' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
        ]);
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'school_name' => ['required', 'string', 'max:255'],
            'npsn' => ['nullable', 'string', 'max:32'],
            'school_address' => ['nullable', 'string', 'max:2000'],
            'attendance_start_time' => ['required', 'date_format:H:i'],
            'late_tolerance_minutes' => ['required', 'integer', 'min:0', 'max:180'],
            'active_days' => ['array'],
            'active_days.*' => ['in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu'],
        ]);

        $settings = SystemSetting::query()->firstOrCreate(['id' => 1]);
        $settings->update([
            'school_name' => $validated['school_name'],
            'npsn' => $validated['npsn'] ?? null,
            'school_address' => $validated['school_address'] ?? null,
            'attendance_start_time' => $validated['attendance_start_time'] . ':00',
            'late_tolerance_minutes' => (int) $validated['late_tolerance_minutes'],
            'active_days' => array_values(array_unique($validated['active_days'] ?? [])),
        ]);

        return redirect()->route('admin.settings')->with('status', 'Pengaturan berhasil disimpan.');
    }

    public function adminScanner() {
        return view('admin.scanner', ['title' => 'Terminal Scanner']);
    }

    public function adminReportPdf() {
        return view('admin.report_pdf', ['title' => 'Cetak Laporan']);
    }

    public function adminReports(Request $request)
    {
        $filters = $this->normalizeReportFilters($request);
        $rowsQuery = $this->buildAdminAttendanceReportQuery($filters);
        $summaryQuery = $this->buildAdminAttendanceReportQuery($filters);

        $rows = $rowsQuery
            ->orderByDesc('date')
            ->orderBy('check_in')
            ->paginate(20)
            ->withQueryString();

        $studentQuery = Student::query();
        if ($filters['class_id'] > 0) {
            $studentQuery->where('class_id', $filters['class_id']);
        }

        return view('admin.reports', [
            'title' => 'Laporan Presensi',
            'summary' => [
                'total_students' => $studentQuery->count(),
                'total_scanned' => (clone $summaryQuery)->count(),
                'hadir' => (clone $summaryQuery)->where('status', 'Hadir')->count(),
                'telat' => (clone $summaryQuery)->where('status', 'Telat')->count(),
                'sakit' => (clone $summaryQuery)->where('status', 'Sakit')->count(),
                'izin' => (clone $summaryQuery)->where('status', 'Izin')->count(),
                'alpa' => (clone $summaryQuery)->where('status', 'Alpa')->count(),
            ],
            'rows' => $rows,
            'classOptions' => StudentClass::query()->orderBy('name')->get(['id', 'name']),
            'statusOptions' => [
                'Hadir' => 'Hadir',
                'Telat' => 'Telat',
                'Sakit' => 'Sakit',
                'Izin' => 'Izin',
                'Alpa' => 'Alpa',
            ],
            'filters' => $filters,
        ]);
    }

    public function exportAttendanceExcel(Request $request, AttendanceExportService $exportService)
    {
        $filters = $this->normalizeReportFilters($request);

        $attendances = $this->buildAdminAttendanceReportQuery($filters)
            ->orderBy('date')
            ->orderBy('check_in')
            ->get();

        $selectedClass = $filters['class_id'] > 0
            ? StudentClass::query()->find($filters['class_id'])
            : null;

        $options = [
            'date_from' => $filters['date_from'],
            'date_to' => $filters['date_to'],
            'class_name' => $selectedClass?->name ?? 'All Classes',
        ];

        $filePath = $exportService->exportToXLSX($attendances, $options);
        $downloadName = 'laporan-presensi-' . $filters['date_from'] . '-to-' . $filters['date_to'] . '.xlsx';

        return response()->download($filePath, $downloadName)->deleteFileAfterSend(true);
    }

    public function exportAttendancePdf(Request $request)
    {
        $filters = $this->normalizeReportFilters($request);

        $attendances = $this->buildAdminAttendanceReportQuery($filters)
            ->orderBy('date')
            ->orderBy('check_in')
            ->get();

        $pdf = Pdf::loadView('reports.attendance', [
            'attendances' => $attendances,
        ]);

        $downloadName = 'laporan-presensi-' . $filters['date_from'] . '-to-' . $filters['date_to'] . '.pdf';

        return response()->streamDownload(
            fn () => print($pdf->output()),
            $downloadName,
            [
                'Content-Type' => 'application/pdf',
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]
        );
    }

    private function normalizeReportFilters(Request $request): array
    {
        $today = now()->toDateString();

        $dateFrom = (string) $request->query('date_from', $today);
        $dateTo = (string) $request->query('date_to', $today);

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom)) {
            $dateFrom = $today;
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo)) {
            $dateTo = $today;
        }

        if ($dateFrom > $dateTo) {
            [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
        }

        $classId = (int) $request->query('class_id', 0);
        if ($classId > 0 && !StudentClass::query()->whereKey($classId)->exists()) {
            $classId = 0;
        }

        $status = trim((string) $request->query('status', ''));
        $allowedStatus = ['Hadir', 'Telat', 'Sakit', 'Izin', 'Alpa'];
        if (!in_array($status, $allowedStatus, true)) {
            $status = '';
        }

        return [
            'q' => trim((string) $request->query('q', '')),
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'class_id' => $classId,
            'status' => $status,
        ];
    }

    private function buildAdminAttendanceReportQuery(array $filters)
    {
        return Attendance::query()
            ->with(['student.user:id,name', 'student.class:id,name'])
            ->whereBetween('date', [$filters['date_from'], $filters['date_to']])
            ->when($filters['class_id'] > 0, function ($query) use ($filters) {
                $query->whereHas('student', fn ($studentQuery) => $studentQuery->where('class_id', $filters['class_id']));
            })
            ->when($filters['status'] !== '', function ($query) use ($filters) {
                $query->where('status', $filters['status']);
            })
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                $q = $filters['q'];

                $query->whereHas('student', function ($studentQuery) use ($q) {
                    $studentQuery
                        ->where('nisn', 'like', "%{$q}%")
                        ->orWhereHas('user', function ($userQuery) use ($q) {
                            $userQuery->where('name', 'like', "%{$q}%");
                        });
                });
            });
    }

    public function masterGuru(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $editId = (int) $request->query('edit', 0);

        $teachers = User::query()
            ->where('role', 'teacher')
            ->orWhere('role', 'dosen')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->withCount('teachingSchedules')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.master.guru', [
            'title' => 'Manajemen Guru',
            'teachers' => $teachers,
            'q' => $q,
            'editTeacher' => $editId > 0
                ? User::query()->whereIn('role', ['teacher', 'dosen'])->find($editId)
                : null,
            'teacherStats' => [
                'total' => User::whereIn('role', ['teacher', 'dosen'])->count(),
                'active' => User::whereIn('role', ['teacher', 'dosen'])->count(),
            ],
        ]);
    }

    public function storeGuru(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'dosen',
        ]);

        return redirect()->route('admin.master.guru')->with('status', 'Guru berhasil ditambahkan.');
    }

    public function updateGuru(Request $request, User $teacher)
    {
        abort_unless(in_array($teacher->role, ['teacher', 'dosen'], true), 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $teacher->id],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $payload['password'] = Hash::make($validated['password']);
        }

        $teacher->update($payload);

        return redirect()->route('admin.master.guru')->with('status', 'Data guru berhasil diperbarui.');
    }

    public function destroyGuru(User $teacher)
    {
        abort_unless(in_array($teacher->role, ['teacher', 'dosen'], true), 404);

        if ($teacher->teachingSchedules()->exists()) {
            return redirect()->route('admin.master.guru')
                ->withErrors(['guru' => 'Guru tidak dapat dihapus karena masih terhubung ke jadwal pelajaran.']);
        }

        $teacher->delete();

        return redirect()->route('admin.master.guru')->with('status', 'Guru berhasil dihapus.');
    }

    public function masterSiswa(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $classId = (int) $request->query('class_id', 0);
        $level = trim((string) $request->query('level', ''));
        $major = trim((string) $request->query('major', ''));
        $hasQr = trim((string) $request->query('has_qr', ''));
        $sort = trim((string) $request->query('sort', 'newest'));
        $editId = (int) $request->query('edit', 0);

        $allowedSort = ['newest', 'oldest', 'name_asc', 'name_desc', 'nisn_asc', 'nisn_desc'];
        if (!in_array($sort, $allowedSort, true)) {
            $sort = 'newest';
        }

        $studentsQuery = Student::query()
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
            ->when($level !== '', fn ($query) => $query->whereHas('class', fn ($classQuery) => $classQuery->where('level', $level)))
            ->when($major !== '', fn ($query) => $query->whereHas('class', fn ($classQuery) => $classQuery->where('major', $major)))
            ->when($hasQr !== '', function ($query) use ($hasQr) {
                if ($hasQr === '1') {
                    $query->whereNotNull('qr_code');
                }

                if ($hasQr === '0') {
                    $query->whereNull('qr_code');
                }
            });

        match ($sort) {
            'oldest' => $studentsQuery->oldest('id'),
            'name_asc' => $studentsQuery->orderBy(
                User::query()->select('name')->whereColumn('users.id', 'students.user_id'),
                'asc'
            ),
            'name_desc' => $studentsQuery->orderBy(
                User::query()->select('name')->whereColumn('users.id', 'students.user_id'),
                'desc'
            ),
            'nisn_asc' => $studentsQuery->orderBy('nisn', 'asc'),
            'nisn_desc' => $studentsQuery->orderBy('nisn', 'desc'),
            default => $studentsQuery->latest('id'),
        };

        $students = $studentsQuery
            ->paginate(10)
            ->withQueryString();

        $classQuery = StudentClass::query();

        return view('admin.master.siswa', [
            'title' => 'Manajemen Siswa',
            'students' => $students,
            'classOptions' => (clone $classQuery)->orderBy('name')->get(['id', 'name']),
            'q' => $q,
            'classId' => $classId,
            'levelOptions' => (clone $classQuery)->select('level')->distinct()->orderBy('level')->pluck('level'),
            'majorOptions' => (clone $classQuery)->select('major')->distinct()->orderBy('major')->pluck('major'),
            'level' => $level,
            'major' => $major,
            'hasQr' => $hasQr,
            'sort' => $sort,
            'editStudent' => $editId > 0 ? Student::query()->with('user')->find($editId) : null,
            'studentStats' => [
                'total' => Student::count(),
            ],
        ]);
    }

    public function exportSiswa(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $classId = (int) $request->query('class_id', 0);
        $level = trim((string) $request->query('level', ''));
        $major = trim((string) $request->query('major', ''));
        $hasQr = trim((string) $request->query('has_qr', ''));
        $sort = trim((string) $request->query('sort', 'newest'));

        $allowedSort = ['newest', 'oldest', 'name_asc', 'name_desc', 'nisn_asc', 'nisn_desc'];
        if (!in_array($sort, $allowedSort, true)) {
            $sort = 'newest';
        }

        $studentsQuery = Student::query()
            ->with(['user:id,name,email', 'class:id,name,level,major'])
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
            ->when($level !== '', fn ($query) => $query->whereHas('class', fn ($classQuery) => $classQuery->where('level', $level)))
            ->when($major !== '', fn ($query) => $query->whereHas('class', fn ($classQuery) => $classQuery->where('major', $major)))
            ->when($hasQr !== '', function ($query) use ($hasQr) {
                if ($hasQr === '1') {
                    $query->whereNotNull('qr_code');
                }

                if ($hasQr === '0') {
                    $query->whereNull('qr_code');
                }
            });

        match ($sort) {
            'oldest' => $studentsQuery->oldest('id'),
            'name_asc' => $studentsQuery->orderBy(
                User::query()->select('name')->whereColumn('users.id', 'students.user_id'),
                'asc'
            ),
            'name_desc' => $studentsQuery->orderBy(
                User::query()->select('name')->whereColumn('users.id', 'students.user_id'),
                'desc'
            ),
            'nisn_asc' => $studentsQuery->orderBy('nisn', 'asc'),
            'nisn_desc' => $studentsQuery->orderBy('nisn', 'desc'),
            default => $studentsQuery->latest('id'),
        };

        $rows = $studentsQuery->get();
        $fileName = 'master-siswa-' . now()->format('Ymd-His') . '.xls';

        return response()->streamDownload(function () use ($rows) {
            echo "\xEF\xBB\xBF";

            $headers = ['Nama', 'Email', 'NISN', 'Kelas', 'Level', 'Jurusan', 'QR Code'];
            echo implode("\t", $headers) . "\n";

            foreach ($rows as $row) {
                $values = [
                    $row->user?->name ?? '-',
                    $row->user?->email ?? '-',
                    $row->nisn,
                    $row->class?->name ?? '-',
                    $row->class?->level ?? '-',
                    $row->class?->major ?? '-',
                    $row->qr_code ? 'Ya' : 'Tidak',
                ];

                $escaped = array_map(function ($value) {
                    return str_replace(["\t", "\r", "\n"], ' ', (string) $value);
                }, $values);

                echo implode("\t", $escaped) . "\n";
            }
        }, $fileName, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    public function storeSiswa(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'nisn' => ['required', 'string', 'max:32', 'unique:students,nisn'],
            'class_id' => ['required', 'exists:classes,id'],
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'student',
            ]);

            Student::create([
                'user_id' => $user->id,
                'class_id' => (int) $validated['class_id'],
                'nisn' => $validated['nisn'],
            ]);
        });

        return redirect()->route('admin.master.siswa')->with('status', 'Siswa berhasil ditambahkan.');
    }

    public function updateSiswa(Request $request, Student $student)
    {
        $student->load('user');

        abort_unless($student->user !== null && $student->user->role === 'student', 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $student->user->id],
            'password' => ['nullable', 'string', 'min:8'],
            'nisn' => ['required', 'string', 'max:32', 'unique:students,nisn,' . $student->id],
            'class_id' => ['required', 'exists:classes,id'],
        ]);

        DB::transaction(function () use ($validated, $student) {
            $userPayload = [
                'name' => $validated['name'],
                'email' => $validated['email'],
            ];

            if (!empty($validated['password'])) {
                $userPayload['password'] = Hash::make($validated['password']);
            }

            $student->user->update($userPayload);

            $student->update([
                'class_id' => (int) $validated['class_id'],
                'nisn' => $validated['nisn'],
            ]);
        });

        return redirect()->route('admin.master.siswa')->with('status', 'Data siswa berhasil diperbarui.');
    }

    public function destroySiswa(Student $student)
    {
        $student->load('user');

        if ($student->attendances()->exists()) {
            return redirect()->route('admin.master.siswa')
                ->withErrors(['siswa' => 'Siswa tidak dapat dihapus karena sudah memiliki riwayat absensi.']);
        }

        DB::transaction(function () use ($student) {
            $user = $student->user;
            $student->delete();

            if ($user && $user->role === 'student') {
                $user->delete();
            }
        });

        return redirect()->route('admin.master.siswa')->with('status', 'Siswa berhasil dihapus.');
    }

    public function masterKelas(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $editId = (int) $request->query('edit', 0);

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
            'editClass' => $editId > 0 ? StudentClass::query()->find($editId) : null,
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

    public function storeKelas(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:classes,name'],
            'level' => ['required', 'in:X,XI,XII'],
            'major' => ['required', 'string', 'max:100'],
        ]);

        StudentClass::create($validated);

        return redirect()->route('admin.master.kelas')->with('status', 'Kelas berhasil ditambahkan.');
    }

    public function updateKelas(Request $request, StudentClass $class)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:classes,name,' . $class->id],
            'level' => ['required', 'in:X,XI,XII'],
            'major' => ['required', 'string', 'max:100'],
        ]);

        $class->update($validated);

        return redirect()->route('admin.master.kelas')->with('status', 'Data kelas berhasil diperbarui.');
    }

    public function destroyKelas(StudentClass $class)
    {
        if ($class->students()->exists()) {
            return redirect()->route('admin.master.kelas')
                ->withErrors(['kelas' => 'Kelas tidak dapat dihapus karena masih memiliki data siswa.']);
        }

        if ($class->schedules()->exists()) {
            return redirect()->route('admin.master.kelas')
                ->withErrors(['kelas' => 'Kelas tidak dapat dihapus karena masih dipakai pada jadwal pelajaran.']);
        }

        $class->teachers()->detach();
        $class->delete();

        return redirect()->route('admin.master.kelas')->with('status', 'Kelas berhasil dihapus.');
    }

    public function masterJadwal(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $classId = (int) $request->query('class_id', 0);
        $day = trim((string) $request->query('day', ''));
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $selectedSemesterId = (string) $request->query('semester_id', '');
        $semesterList = SemesterAkademik::orderByDesc('is_active')->orderByDesc('tanggal_mulai')->get();

        $schedules = Schedule::query()
            ->with([
                'class:id,name',
                'subject:id,name,code',
                'teacher:id,name,email',
                'semesterAkademik',
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
            ->when($selectedSemesterId !== '', fn ($query) => $query->where('semester_akademik_id', (int) $selectedSemesterId))
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
            'teacherOptions' => User::query()->where('role', 'teacher')->orWhere('role', 'dosen')->orderBy('name')->get(['id', 'name']),
            'semesterOptions' => $semesterList,
            'selectedSemesterId' => $selectedSemesterId,
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
            'teacher_id' => ['required', 'exists:users,id', 'in:' . User::query()->whereIn('role', ['teacher', 'dosen'])->pluck('id')->implode(',')],
            'day' => ['required', 'in:Senin,Selasa,Rabu,Kamis,Jumat'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'room' => ['nullable', 'string', 'max:64'],
            'semester_akademik_id' => ['nullable', 'exists:semester_akademik,id'],
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
            'semester_akademik_id' => $validated['semester_akademik_id'] ?? null,
        ]);

        return redirect()->route('admin.master.jadwal')->with('status', 'Jadwal berhasil ditambahkan.');
    }

    public function updateJadwal(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'teacher_id' => ['required', 'exists:users,id', 'in:' . User::query()->whereIn('role', ['teacher', 'dosen'])->pluck('id')->implode(',')],
            'day' => ['required', 'in:Senin,Selasa,Rabu,Kamis,Jumat'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'room' => ['nullable', 'string', 'max:64'],
            'semester_akademik_id' => ['nullable', 'exists:semester_akademik,id'],
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
            'semester_akademik_id' => $validated['semester_akademik_id'] ?? null,
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
        $selectedSemesterId = (string) $request->query('semester_id', '');
        $semesterList = SemesterAkademik::orderByDesc('is_active')->orderByDesc('tanggal_mulai')->get();

        $subjects = Subject::query()
            ->with(['semesterAkademik'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('code', 'like', "%{$q}%")
                        ->orWhere('name', 'like', "%{$q}%");
                });
            })
            ->when(in_array($group, $groups, true), fn ($query) => $query->where('group', $group))
            ->when($selectedSemesterId !== '', fn ($query) => $query->where('semester_akademik_id', (int) $selectedSemesterId))
            ->orderBy('code')
            ->paginate(10)
            ->withQueryString();

        return view('admin.master.mapel', [
            'title' => 'Mata Pelajaran',
            'subjects' => $subjects,
            'groupOptions' => $groups,
            'semesterList' => $semesterList,
            'selectedSemesterId' => $selectedSemesterId,
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
            'semester_akademik_id' => ['nullable', 'exists:semester_akademik,id'],
            'sks' => ['nullable', 'integer', 'min:1', 'max:10'],
        ]);

        Subject::create([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'group' => $validated['group'],
            'semester_akademik_id' => $validated['semester_akademik_id'] ?? null,
            'sks' => $validated['sks'] ?? 3,
        ]);

        return redirect()->route('admin.master.mapel')->with('status', 'Mapel berhasil ditambahkan.');
    }

    public function updateMapel(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:32', 'unique:subjects,code,' . $subject->id],
            'name' => ['required', 'string', 'max:255'],
            'group' => ['required', 'in:Kejuruan,Umum'],
            'semester_akademik_id' => ['nullable', 'exists:semester_akademik,id'],
            'sks' => ['nullable', 'integer', 'min:1', 'max:10'],
        ]);

        $subject->update([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'group' => $validated['group'],
            'semester_akademik_id' => $validated['semester_akademik_id'] ?? null,
            'sks' => $validated['sks'] ?? 3,
        ]);

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
            'nisn' => 'required|string|max:255',
        ]);

        $scanValue = trim((string) $request->nisn);
        $scanCandidates = $this->extractScanCandidates($scanValue);

        $student = Student::where(function ($query) use ($scanCandidates) {
                $query->whereIn('nisn', $scanCandidates)
                    ->orWhereIn('qr_code', $scanCandidates)
                    ->orWhereIn(DB::raw('UPPER(qr_code)'), array_values(array_unique(array_map('strtoupper', $scanCandidates))));
            })
            ->with(['user', 'class'])
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Data siswa dengan kode ' . $scanValue . ' tidak ditemukan.',
            ], 404);
        }

        $date = now()->toDateString();
        $checkInTime = now()->format('H:i:s');
        $scheduleId = $this->resolveAttendanceScheduleId($student, $date);

        if (! $scheduleId) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal aktif tidak ditemukan untuk siswa ini.',
            ], 404);
        }

        // Calculate status based on system settings
        $settings = SystemSetting::query()->first();
        $status = 'Hadir';

        if ($settings) {
            $startTime = \Carbon\Carbon::parse($settings->attendance_start_time);
            $tolerance = (int) ($settings->late_tolerance_minutes ?? 0);
            $lateThreshold = $startTime->copy()->addMinutes($tolerance);

            $currentTime = now();
            // Since attendance is for today, we only compare the time part
            $currentHms = $currentTime->format('H:i:s');
            $thresholdHms = $lateThreshold->format('H:i:s');

            if ($currentHms > $thresholdHms) {
                $status = 'Telat';
            }
        } else {
            // Fallback to 07:30 if settings are missing
            $hour = (int) now()->format('H');
            $minute = (int) now()->format('i');
            if ($hour > 7 || ($hour == 7 && $minute > 30)) {
                $status = 'Telat';
            }
        }
        
        // Check if already scanned today
        $existingAttendance = Attendance::where('student_id', $student->id)
            ->where('schedule_id', $scheduleId)
            ->whereDate('date', $date)
            ->first();

        if ($existingAttendance) {
            // If historical data has no check-in yet, treat this as first check-in.
            if (empty($existingAttendance->check_in)) {
                $existingAttendance->update([
                    'check_in' => $checkInTime,
                    'status' => $status,
                    'approval_status' => $existingAttendance->approval_status ?? 'approved',
                    'approved_by' => $existingAttendance->approved_by ?? auth()->id(),
                    'approved_at' => $existingAttendance->approved_at ?? now(),
                ]);
            } elseif (empty($existingAttendance->check_out)) {
                // Second valid scan in the same day is interpreted as check-out.
                $existingAttendance->update([
                    'check_out' => $checkInTime,
                    'approval_status' => $existingAttendance->approval_status ?? 'approved',
                    'approved_by' => $existingAttendance->approved_by ?? auth()->id(),
                    'approved_at' => $existingAttendance->approved_at ?? now(),
                ]);
            }

            $attendance = $existingAttendance->fresh();
        } else {
            $attendance = Attendance::create([
                'student_id' => $student->id,
                'schedule_id' => $scheduleId,
                'date' => $date,
                'check_in' => $checkInTime,
                'status' => $status,
                'approval_status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
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

    private function extractScanCandidates(string $rawValue): array
    {
        $candidates = [];

        $appendCandidate = function (?string $value) use (&$candidates): void {
            $normalized = trim((string) $value);
            if ($normalized === '') {
                return;
            }

            $normalized = trim($normalized, " \t\n\r\0\x0B\"'");
            if ($normalized === '') {
                return;
            }

            if (!in_array($normalized, $candidates, true)) {
                $candidates[] = $normalized;
            }
        };

        $appendCandidate($rawValue);

        $decoded = rawurldecode($rawValue);
        $appendCandidate($decoded);

        if (preg_match('/(?:NISN|QR(?:_?CODE)?|CODE)\s*[:=]\s*([A-Za-z0-9\-]+)/i', $decoded, $labelMatch)) {
            $appendCandidate($labelMatch[1] ?? '');
        }

        if (preg_match('/SH-[A-Z0-9]{8}/i', $decoded, $qrMatch)) {
            $appendCandidate(strtoupper($qrMatch[0] ?? ''));
        }

        if (preg_match('/\b\d{8,20}\b/', $decoded, $nisnMatch)) {
            $appendCandidate($nisnMatch[0] ?? '');
        }

        if (filter_var($decoded, FILTER_VALIDATE_URL)) {
            $parsed = parse_url($decoded);

            if (is_array($parsed)) {
                if (!empty($parsed['query'])) {
                    parse_str($parsed['query'], $queryValues);
                    foreach (['nisn', 'code', 'qr', 'qr_code', 'value'] as $key) {
                        if (array_key_exists($key, $queryValues)) {
                            $rawQueryValue = $queryValues[$key];

                            if (is_array($rawQueryValue)) {
                                foreach ($rawQueryValue as $item) {
                                    $appendCandidate(is_scalar($item) ? (string) $item : '');
                                }
                            } else {
                                $appendCandidate(is_scalar($rawQueryValue) ? (string) $rawQueryValue : '');
                            }
                        }
                    }
                }

                if (!empty($parsed['path'])) {
                    foreach (explode('/', trim((string) $parsed['path'], '/')) as $segment) {
                        if (preg_match('/^(SH-[A-Z0-9]{8}|\d{8,20})$/i', $segment)) {
                            $appendCandidate($segment);
                        }
                    }
                }
            }
        }

        if (str_starts_with($decoded, '{') && str_ends_with($decoded, '}')) {
            $json = json_decode($decoded, true);
            if (is_array($json)) {
                foreach (['nisn', 'code', 'qr', 'qr_code', 'value'] as $key) {
                    if (array_key_exists($key, $json) && is_scalar($json[$key])) {
                        $appendCandidate((string) $json[$key]);
                    }
                }
            }
        }

        if (empty($candidates)) {
            $appendCandidate($rawValue);
        }

        return $candidates;
    }

    private function getAuthenticatedStudent(): Student
    {
        $user = auth()->user();

        abort_unless($user && $user->role === 'student', 403, 'Unauthorized');

        $student = Student::query()
            ->with(['user', 'class'])
            ->where('user_id', $user->id)
            ->first();

        abort_unless($student !== null, 404, 'Data siswa tidak ditemukan.');

        return $student;
    }

    private function resolveAttendanceScheduleId(Student $student, string $date): ?int
    {
        $activeSession = Cache::get('active_attendance_session');
        if (!empty($activeSession['schedule_id'])) {
            return (int) $activeSession['schedule_id'];
        }

        $schedules = $this->resolveSchedulesForStudentDate($student, $date);

        return $schedules->first()?->id;
    }

    private function resolveSchedulesForStudentDate(Student $student, string $date)
    {
        $dayNames = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];

        $dayName = $dayNames[\Illuminate\Support\Carbon::parse($date)->dayOfWeekIso] ?? null;

        if (! $dayName) {
            return collect();
        }

        return Schedule::query()
            ->where('class_id', $student->class_id)
            ->where('day', $dayName)
            ->orderBy('start_time')
            ->get();
    }

    // ==========================================
    // Semester Akademik Management
    // ==========================================

    public function masterSemester(Request $request)
    {
        $semesters = SemesterAkademik::query()
            ->orderByDesc('is_active')
            ->orderByDesc('tanggal_mulai')
            ->paginate(10);

        return view('admin.master.semester', [
            'title' => 'Semester Akademik',
            'semesters' => $semesters,
            'semesterStats' => [
                'total' => SemesterAkademik::count(),
                'active' => SemesterAkademik::where('is_active', true)->count(),
            ],
        ]);
    }

    public function storeSemester(Request $request)
    {
        $validated = $request->validate([
            'nama_semester' => ['required', 'string', 'max:255'],
            'tahun_ajaran' => ['required', 'string', 'max:32'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($validated) {
            if (!empty($validated['is_active'])) {
                SemesterAkademik::query()->update(['is_active' => false]);
            }

            SemesterAkademik::create([
                'nama_semester' => $validated['nama_semester'],
                'tahun_ajaran' => $validated['tahun_ajaran'],
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'],
                'is_active' => !empty($validated['is_active']),
            ]);
        });

        return redirect()->route('admin.master.semester')->with('status', 'Semester berhasil ditambahkan.');
    }

    public function updateSemester(Request $request, SemesterAkademik $semester)
    {
        $validated = $request->validate([
            'nama_semester' => ['required', 'string', 'max:255'],
            'tahun_ajaran' => ['required', 'string', 'max:32'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($validated, $semester) {
            if (!empty($validated['is_active'])) {
                SemesterAkademik::query()->where('id', '!=', $semester->id)->update(['is_active' => false]);
            }

            $semester->update([
                'nama_semester' => $validated['nama_semester'],
                'tahun_ajaran' => $validated['tahun_ajaran'],
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'],
                'is_active' => !empty($validated['is_active']),
            ]);
        });

        return redirect()->route('admin.master.semester')->with('status', 'Semester berhasil diperbarui.');
    }

    public function destroySemester(SemesterAkademik $semester)
    {
        if ($semester->jadwal()->exists()) {
            return redirect()->route('admin.master.semester')
                ->withErrors(['semester' => 'Semester tidak dapat dihapus karena masih memiliki jadwal.']);
        }

        if ($semester->mataKuliah()->exists()) {
            return redirect()->route('admin.master.semester')
                ->withErrors(['semester' => 'Semester tidak dapat dihapus karena masih memiliki mata pelajaran.']);
        }

        $semester->delete();

        return redirect()->route('admin.master.semester')->with('status', 'Semester berhasil dihapus.');
    }
}
