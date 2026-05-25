<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReportExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_export_attendance_to_excel(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $class = StudentClass::factory()->create();
        $student = Student::factory()->create(['class_id' => $class->id]);

        Attendance::create([
            'student_id' => $student->id,
            'date' => now()->toDateString(),
            'status' => 'Hadir',
            'check_in' => now()->toTimeString(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.reports.export.excel', [
                'date_from' => now()->toDateString(),
                'date_to' => now()->toDateString(),
            ]))
            ->assertOk()
            ->assertDownload('laporan-presensi-' . now()->toDateString() . '-to-' . now()->toDateString() . '.xlsx');
    }

    public function test_admin_can_export_attendance_to_pdf(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $class = StudentClass::factory()->create();
        $student = Student::factory()->create(['class_id' => $class->id]);

        Attendance::create([
            'student_id' => $student->id,
            'date' => now()->toDateString(),
            'status' => 'Hadir',
            'check_in' => now()->toTimeString(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.reports.export.pdf', [
                'date_from' => now()->toDateString(),
                'date_to' => now()->toDateString(),
            ]))
            ->assertOk()
            ->assertDownload('laporan-presensi-' . now()->toDateString() . '-to-' . now()->toDateString() . '.pdf');
    }

    public function test_teacher_cannot_access_admin_report_exports(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $this->actingAs($teacher)
            ->get(route('admin.reports.export.excel'))
            ->assertForbidden();

        $this->actingAs($teacher)
            ->get(route('admin.reports.export.pdf'))
            ->assertForbidden();
    }
}