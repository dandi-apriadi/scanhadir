<?php

namespace App\Http\Controllers;

use App\Exports\AttendanceExport;
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
        return view('student.dashboard');
    }

    public function studentIzin() {
        return view('student.izin');
    }

    public function studentProfil() {
        return view('student.profil');
    }

    public function studentManual() {
        return view('student.manual_attendance');
    }

    public function adminDashboard() {
        return view('admin.dashboard');
    }

    public function adminAnalytics() {
        return view('admin.analytics');
    }

    public function adminLogs() {
        return view('admin.logs');
    }

    public function adminIzinApproval() {
        return view('admin.izin_approval');
    }

    public function adminSettings() {
        return view('admin.settings');
    }

    public function adminScanner() {
        return view('admin.scanner');
    }

    public function adminReportPdf() {
        return view('admin.report_pdf');
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

    public function masterGuru() {
        return view('admin.master.guru');
    }

    public function masterSiswa() {
        return view('admin.master.siswa');
    }

    public function masterKelas() {
        return view('admin.master.kelas');
    }

    public function masterJadwal() {
        return view('admin.master.jadwal');
    }

    public function masterMapel() {
        $data = [
            'mapels' => [
                ['kode' => 'MP-001', 'nama' => 'Pemrograman Web', 'kelompok' => 'Kejuruan'],
                ['kode' => 'MP-002', 'nama' => 'Basis Data', 'kelompok' => 'Kejuruan'],
                ['kode' => 'MP-003', 'nama' => 'Matematika', 'kelompok' => 'Umum'],
                ['kode' => 'MP-004', 'nama' => 'Bahasa Inggris', 'kelompok' => 'Umum'],
            ]
        ];
        return view('admin.master.mapel', $data);
    }

    public function teacherDashboard() {
        return view('teacher.dashboard');
    }
}
