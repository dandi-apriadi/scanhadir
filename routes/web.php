<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentQrCodeController;
use App\Livewire\AttendanceAnalytics;
use App\Livewire\AttendanceReports;
use App\Livewire\BulkAttendanceUpdate;
use Illuminate\Support\Facades\Route;

Route::redirect('/admin', '/admin/dashboard');
Route::redirect('/teacher', '/teacher/dashboard');
Route::redirect('/dashboard', '/student/dashboard');

Route::get('/student/{student}/qrcode', [StudentQrCodeController::class, 'show'])->name('students.qrcode');

// Guest Routes
Route::get('/', [DashboardController::class, 'landing'])->name('landing');

// Authentication Routes
Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'processLogin'])->name('login.process');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Legacy routes - redirect to unified login
Route::redirect('/login/student', '/auth/login');
Route::redirect('/login/admin', '/auth/login');

Route::get('/forgot-password', [DashboardController::class, 'forgotPassword'])->name('password.request');

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
    });
});

// Teacher Routes
Route::prefix('teacher')->name('teacher.')->middleware(['auth', 'role:teacher'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'teacherDashboard'])->name('dashboard');
    Route::get('/analytics', AttendanceAnalytics::class)->name('analytics');
    Route::get('/reports', AttendanceReports::class)->name('reports');
    Route::get('/bulk-update', BulkAttendanceUpdate::class)->name('bulk-update');
});
