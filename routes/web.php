<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Livewire\TeacherDashboard;
use App\Models\Student;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

Route::redirect('/admin', '/admin/dashboard');
Route::redirect('/teacher', '/teacher/dashboard');
Route::redirect('/dashboard', '/student/dashboard');

Route::get('/student/{student}/qrcode', function (Student $student) {
    $filename = "qrcodes/student-{$student->id}.svg";

    if (!Storage::disk('local')->exists($filename)) {
        Storage::disk('local')->makeDirectory('qrcodes');
        $qrImage = QrCode::format('svg')->size(400)->margin(1)->generate($student->qr_code);
        Storage::disk('local')->put($filename, $qrImage);
    }

    $path = storage_path('app/' . $filename);

    if (request()->boolean('download')) {
        return response()->download($path, "qr-{$student->nisn}.svg", ['Content-Type' => 'image/svg+xml']);
    }

    return response()->file($path, ['Content-Type' => 'image/svg+xml']);
})->name('students.qrcode');

// Guest Routes
Route::get('/', [DashboardController::class, 'landing'])->name('landing');

// Authentication Routes
Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'processLogin'])->name('login.process');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Legacy routes - redirect to new login
Route::redirect('/login/student', '/auth/login');
Route::redirect('/login/admin', '/auth/login');

Route::get('/forgot-password', [DashboardController::class, 'forgotPassword'])->name('password.request');

// Student Routes
Route::prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'studentDashboard'])->name('dashboard');
    Route::get('/izin', [DashboardController::class, 'studentIzin'])->name('izin');
    Route::get('/profil', [DashboardController::class, 'studentProfil'])->name('profil');
    Route::get('/manual-attendance', [DashboardController::class, 'studentManual'])->name('manual');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');
    Route::get('/analytics', [DashboardController::class, 'adminAnalytics'])->name('analytics');
    Route::get('/logs', [DashboardController::class, 'adminLogs'])->name('logs');
    Route::get('/izin-approval', [DashboardController::class, 'adminIzinApproval'])->name('izin_approval');
    Route::get('/settings', [DashboardController::class, 'adminSettings'])->name('settings');
    Route::get('/scanner', [DashboardController::class, 'adminScanner'])->name('scanner');
    Route::get('/report-pdf', [DashboardController::class, 'adminReportPdf'])->name('report_pdf');
    Route::get('/reports', [DashboardController::class, 'adminReports'])->name('reports');
    Route::get('/reports/export/csv', [DashboardController::class, 'exportAttendanceCsv'])->name('reports.export.csv');
    Route::get('/reports/export/pdf', [DashboardController::class, 'exportAttendancePdf'])->name('reports.export.pdf');
    
    // Admin Master Data
    Route::prefix('master')->name('master.')->group(function () {
        Route::get('/guru', [DashboardController::class, 'masterGuru'])->name('guru');
        Route::get('/siswa', [DashboardController::class, 'masterSiswa'])->name('siswa');
        Route::get('/kelas', [DashboardController::class, 'masterKelas'])->name('kelas');
        Route::get('/jadwal', [DashboardController::class, 'masterJadwal'])->name('jadwal');
        Route::get('/mapel', [DashboardController::class, 'masterMapel'])->name('mapel');
    });
});

// Teacher Routes
Route::prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', TeacherDashboard::class)->name('dashboard');
});
