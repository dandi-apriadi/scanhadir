<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CorrectionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DosenSessionController;
use App\Http\Controllers\StudentQrCodeController;
use App\Livewire\AttendanceAnalytics;
use App\Livewire\AttendanceReports;
use App\Livewire\BulkAttendanceUpdate;
use App\Livewire\WaliKelasDashboard;
use Illuminate\Support\Facades\Route;

Route::redirect('/admin', '/admin/dashboard');
Route::redirect('/teacher', '/teacher/dashboard');
Route::redirect('/dashboard', '/student/dashboard');

Route::get('/student/{student}/qrcode', [StudentQrCodeController::class, 'show'])->name('students.qrcode');
Route::get('/qr-download/{nisn}/{filename}.png', [StudentQrCodeController::class, 'downloadByNisn'])->name('students.qrcode.download');

// Root → login portal
Route::redirect('/', '/auth/login')->name('landing');

// Authentication Routes
Route::redirect('/login', '/auth/login');
Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'processLogin'])->name('login.process');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Legacy routes - redirect to unified login
Route::redirect('/login', '/auth/login');
Route::redirect('/login/student', '/auth/login');
Route::redirect('/login/admin', '/auth/login');

Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Student Routes
Route::prefix('student')->name('student.')->middleware(['auth', 'role:student'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'studentDashboard'])->name('dashboard');
    Route::get('/izin', [DashboardController::class, 'studentIzin'])->name('izin');
    Route::post('/izin', [DashboardController::class, 'storeStudentIzin'])->name('izin.store');
    Route::get('/profil', [DashboardController::class, 'studentProfil'])->name('profil');
    Route::get('/manual-attendance', [DashboardController::class, 'studentManual'])->name('manual');
    Route::post('/manual-attendance', [DashboardController::class, 'storeStudentManual'])->name('manual.store');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');
    Route::get('/search', [DashboardController::class, 'globalSearch'])->name('search');
    Route::get('/analytics', AttendanceAnalytics::class)->name('analytics');
    Route::get('/logs', [DashboardController::class, 'adminLogs'])->name('logs');
    Route::get('/izin-approval', [DashboardController::class, 'adminIzinApproval'])->name('izin_approval');
    Route::post('/izin-approval/{attendance}/approve', [DashboardController::class, 'approveIzinAttendance'])->name('izin_approval.approve');
    Route::post('/izin-approval/{attendance}/reject', [DashboardController::class, 'rejectIzinAttendance'])->name('izin_approval.reject');
    Route::get('/settings', [DashboardController::class, 'adminSettings'])->name('settings');
    Route::put('/settings', [DashboardController::class, 'updateSettings'])->name('settings.update');
    Route::get('/scanner', [DashboardController::class, 'adminScanner'])->name('scanner');
    Route::post('/attendance/scan', [DashboardController::class, 'scanAttendance'])->name('attendance.scan');
    Route::get('/report-pdf', [DashboardController::class, 'adminReportPdf'])->name('report_pdf');
    Route::get('/reports', [DashboardController::class, 'adminReports'])->name('reports');
    Route::get('/reports/export/excel', [DashboardController::class, 'exportAttendanceExcel'])->name('reports.export.excel');
    Route::get('/reports/export/pdf', [DashboardController::class, 'exportAttendancePdf'])->name('reports.export.pdf');
    
    // Admin Master Data
    Route::prefix('master')->name('master.')->group(function () {
        Route::get('/guru', [DashboardController::class, 'masterGuru'])->name('guru');
        Route::post('/guru', [DashboardController::class, 'storeGuru'])->name('guru.store');
        Route::put('/guru/{teacher}', [DashboardController::class, 'updateGuru'])->name('guru.update');
        Route::delete('/guru/{teacher}', [DashboardController::class, 'destroyGuru'])->name('guru.destroy');
        Route::get('/siswa', [DashboardController::class, 'masterSiswa'])->name('siswa');
        Route::get('/siswa/export', [DashboardController::class, 'exportSiswa'])->name('siswa.export');
        Route::post('/siswa', [DashboardController::class, 'storeSiswa'])->name('siswa.store');
        Route::put('/siswa/{student}', [DashboardController::class, 'updateSiswa'])->name('siswa.update');
        Route::delete('/siswa/{student}', [DashboardController::class, 'destroySiswa'])->name('siswa.destroy');
        Route::get('/kelas', [DashboardController::class, 'masterKelas'])->name('kelas');
        Route::post('/kelas', [DashboardController::class, 'storeKelas'])->name('kelas.store');
        Route::put('/kelas/{class}', [DashboardController::class, 'updateKelas'])->name('kelas.update');
        Route::delete('/kelas/{class}', [DashboardController::class, 'destroyKelas'])->name('kelas.destroy');
        Route::get('/jadwal', [DashboardController::class, 'masterJadwal'])->name('jadwal');
        Route::post('/jadwal', [DashboardController::class, 'storeJadwal'])->name('jadwal.store');
        Route::put('/jadwal/{schedule}', [DashboardController::class, 'updateJadwal'])->name('jadwal.update');
        Route::delete('/jadwal/{schedule}', [DashboardController::class, 'destroyJadwal'])->name('jadwal.destroy');
        Route::get('/mapel', [DashboardController::class, 'masterMapel'])->name('mapel');
        Route::post('/mapel', [DashboardController::class, 'storeMapel'])->name('mapel.store');
        Route::put('/mapel/{subject}', [DashboardController::class, 'updateMapel'])->name('mapel.update');
        Route::delete('/mapel/{subject}', [DashboardController::class, 'destroyMapel'])->name('mapel.destroy');
        Route::get('/semester', [DashboardController::class, 'masterSemester'])->name('semester');
        Route::post('/semester', [DashboardController::class, 'storeSemester'])->name('semester.store');
        Route::put('/semester/{semester}', [DashboardController::class, 'updateSemester'])->name('semester.update');
        Route::delete('/semester/{semester}', [DashboardController::class, 'destroySemester'])->name('semester.destroy');
    });
});

// Teacher/Dosen Routes
Route::prefix('teacher')->name('teacher.')->middleware(['auth', 'role:teacher,dosen'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'teacherDashboard'])->name('dashboard');
    Route::get('/wali-kelas', WaliKelasDashboard::class)->name('wali-kelas');
    Route::get('/analytics', AttendanceAnalytics::class)->name('analytics');
    Route::get('/reports', AttendanceReports::class)->name('reports');
    Route::get('/bulk-update', BulkAttendanceUpdate::class)->name('bulk-update');
});

// Dosen Session Routes (IOT-Attendance style)
Route::middleware(['auth', 'role:admin,dosen,teacher'])->group(function () {
    // Mata Kuliah Saya (Courses)
    Route::get('/dosen/mata-kuliah', [DosenSessionController::class, 'courses'])->name('dosen-courses');
    Route::get('/dosen/schedule/start', [DosenSessionController::class, 'create'])->name('dosen-schedule.start');
    Route::post('/dosen/schedule/start', [DosenSessionController::class, 'store'])->name('dosen-schedule.store');
    Route::delete('/dosen/schedule/stop', [DosenSessionController::class, 'destroy'])->name('dosen-schedule.stop');
    Route::get('/dosen/schedule/detail', [DosenSessionController::class, 'detailByFilter'])->name('dosen-schedule.detail');
    Route::get('/dosen/schedule/detail/export/excel', [DosenSessionController::class, 'exportExcel'])->name('dosen-schedule.detail.export.excel');
    Route::get('/dosen/schedule/detail/export/pdf', [DosenSessionController::class, 'exportPdf'])->name('dosen-schedule.detail.export.pdf');
    
    // Monitoring
    Route::get('/monitoring/live', [DashboardController::class, 'adminScanner'])->name('monitoring');
    
    // Correction (optional)
    Route::get('/correction', [CorrectionController::class, 'index'])->name('correction');
    Route::get('/correction/create', [CorrectionController::class, 'create'])->name('correction.create');
    Route::post('/correction', [CorrectionController::class, 'store'])->name('correction.store');
    Route::get('/correction/{correction}/edit', [CorrectionController::class, 'edit'])->name('correction.edit');
    Route::put('/correction/{correction}', [CorrectionController::class, 'update'])->name('correction.update');
    Route::post('/correction/{correction}/approve', [CorrectionController::class, 'approve'])->name('correction.approve');
    Route::post('/correction/{correction}/reject', [CorrectionController::class, 'reject'])->name('correction.reject');
});
