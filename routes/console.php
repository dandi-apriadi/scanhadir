<?php

use App\Models\Student;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('students:generate-qrcodes {--force : Regenerate existing QR image files}', function () {
    $students = Student::query()->whereNotNull('qr_code')->get();

    if ($students->isEmpty()) {
        $this->warn('No students found with QR code data.');
        return;
    }

    Storage::disk('local')->makeDirectory('qrcodes');

    $generated = 0;
    $skipped = 0;

    foreach ($students as $student) {
        $filename = "qrcodes/student-{$student->id}.svg";

        if (!$this->option('force') && Storage::disk('local')->exists($filename)) {
            $skipped++;
            continue;
        }

        $image = QrCode::format('svg')->size(400)->margin(1)->generate($student->qr_code);
        Storage::disk('local')->put($filename, $image);
        $generated++;
    }

    $this->info("QR generation complete. Generated: {$generated}, Skipped: {$skipped}");
})->purpose('Generate visual SVG QR codes for all students');
