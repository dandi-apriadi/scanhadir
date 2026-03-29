<?php

namespace Tests\Feature;

use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class Phase10ProductionDeploymentTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function production_prepare_command_supports_dry_run(): void
    {
        $this->artisan('app:prepare-production --dry-run --force')
            ->expectsOutputToContain('Preparing application for production...')
            ->expectsOutputToContain('[dry-run] php artisan route:cache')
            ->expectsOutputToContain('Production preparation complete.')
            ->assertExitCode(0);
    }

    #[Test]
    public function qrcode_endpoint_generates_svg_file_via_controller_route(): void
    {
        $student = Student::factory()->create();

        $response = $this->get(route('students.qrcode', $student));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/svg+xml');
        $this->assertTrue(Storage::disk('local')->exists("qrcodes/student-{$student->id}.svg"));
    }
}
