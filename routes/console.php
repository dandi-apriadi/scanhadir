<?php

use App\Models\Student;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
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

Artisan::command('app:prepare-production
    {--with-migrate : Run migration with --force}
    {--with-seed : Run seeder with --force}
    {--force : Confirm execution outside production}
    {--dry-run : Print steps without executing}', function () {
    $isDryRun = (bool) $this->option('dry-run');
    $appEnv = app()->environment();

    if ($appEnv !== 'production' && !$this->option('force')) {
        $this->error('This command is intended for production. Use --force to run in non-production environments.');
        return 1;
    }

    $this->info('Preparing application for production...');
    $this->line("Environment: {$appEnv}");

    $cacheCommands = [
        'optimize:clear',
        'config:cache',
        'route:cache',
        'view:cache',
        'event:cache',
        'queue:restart',
    ];

    if (!File::isWritable(storage_path())) {
        $this->error('Storage directory is not writable.');
        return 1;
    }

    if (!File::isWritable(base_path('bootstrap/cache'))) {
        $this->error('bootstrap/cache directory is not writable.');
        return 1;
    }

    $run = function (string $command, array $parameters = []) use ($isDryRun) {
        if ($isDryRun) {
            $this->line("[dry-run] php artisan {$command}");
            return 0;
        }

        return Artisan::call($command, $parameters);
    };

    if ($this->option('with-migrate')) {
        $this->line('Running migrations...');
        $code = $run('migrate', ['--force' => true]);
        if ($code !== 0) {
            $this->error('Migration step failed.');
            return $code;
        }
    }

    if ($this->option('with-seed')) {
        $this->line('Running database seeders...');
        $code = $run('db:seed', ['--force' => true]);
        if ($code !== 0) {
            $this->error('Seeder step failed.');
            return $code;
        }
    }

    foreach ($cacheCommands as $command) {
        $this->line("Running {$command}...");
        $code = $run($command);

        if ($code !== 0) {
            $this->error("Command failed: {$command}");
            return $code;
        }
    }

    $this->info('Production preparation complete.');
    $this->line('Health endpoint: /up');

    return 0;
})->purpose('Prepare application for production deployment and caching');
