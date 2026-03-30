<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\Subject;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\AttendanceExportService;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
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
            ->whereIn('status', ['sick', 'excused'])
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
            ->whereIn('status', ['sick', 'excused'])
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
        abort_unless(in_array($attendance->status, ['sick', 'excused'], true), 404);

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
        abort_unless(in_array($attendance->status, ['sick', 'excused'], true), 404);

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

    public function adminReports(ReportService $reportService)
    {
        $date = now()->toDateString();

        return view('admin.reports', [
            'summary' => $reportService->getDailyAttendanceSummary($date),
            'rows' => $reportService->getAttendanceRowsForDate($date),
        ]);
    }

    public function exportAttendanceExcel(AttendanceExportService $exportService)
    {
        $date = now()->toDateString();
        $attendances = Attendance::with(['student.user', 'student.class'])
            ->whereDate('date', $date)
            ->get();

        $options = [
            'date_from' => $date,
            'date_to' => $date,
            'class_name' => 'All Classes',
        ];

        $filePath = $exportService->exportToXLSX($attendances, $options);
        $downloadName = 'laporan-presensi-' . now()->format('Y-m-d') . '.xlsx';

        return response()->download($filePath, $downloadName)->deleteFileAfterSend(true);
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

        $downloadName = 'laporan-presensi-' . now()->format('Y-m-d') . '.pdf';

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

    public function masterGuru(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $editId = (int) $request->query('edit', 0);

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
            'editTeacher' => $editId > 0
                ? User::query()->where('role', 'teacher')->find($editId)
                : null,
            'teacherStats' => [
                'total' => User::where('role', 'teacher')->count(),
                'active' => User::where('role', 'teacher')->count(),
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
            'role' => 'teacher',
        ]);

        return redirect()->route('admin.master.guru')->with('status', 'Guru berhasil ditambahkan.');
    }

    public function updateGuru(Request $request, User $teacher)
    {
        abort_unless($teacher->role === 'teacher', 404);

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
        abort_unless($teacher->role === 'teacher', 404);

        if ($teacher->teachingSchedules()->exists()) {
            return redirect()->route('admin.master.guru')
                ->withErrors(['guru' => 'Guru tidak dapat dihapus karena masih terhubung ke jadwal pelajaran.']);
        }

        if ($teacher->assignedClasses()->exists()) {
            return redirect()->route('admin.master.guru')
                ->withErrors(['guru' => 'Guru tidak dapat dihapus karena masih terhubung sebagai wali/pengajar kelas.']);
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

        $scanValue = trim((string) $request->nisn);

        $student = Student::where('nisn', $scanValue)
            ->orWhere('qr_code', $scanValue)
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
        
        // Check if already scanned today
        $existingAttendance = Attendance::where('student_id', $student->id)
            ->where('date', $date)
            ->first();

        if ($existingAttendance) {
            // Update check_out if already have check_in
            $existingAttendance->update([
                'check_out' => $checkInTime,
                'approval_status' => $existingAttendance->approval_status ?? 'approved',
                'approved_by' => $existingAttendance->approved_by ?? auth()->id(),
                'approved_at' => $existingAttendance->approved_at ?? now(),
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
}
