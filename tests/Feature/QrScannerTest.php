<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QrScannerTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_scan_student_attendance()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $class = StudentClass::factory()->create();
        $student = Student::factory()->create([
            'class_id' => $class->id,
            'nisn' => 'TEST-12345',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.attendance.scan'), [
            'nisn' => 'TEST-12345',
        ]);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'student_name',
                'student_nisn',
                'class_name',
                'check_in',
                'status',
                'timestamp',
            ],
        ]);

        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.student_nisn', 'TEST-12345');

        $attendance = Attendance::query()
            ->where('student_id', $student->id)
            ->whereDate('date', now()->toDateString())
            ->first();

        $this->assertNotNull($attendance);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function scanner_returns_error_for_invalid_nisn()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->postJson(route('admin.attendance.scan'), [
            'nisn' => 'INVALID-NISN',
        ]);

        $response->assertJsonPath('success', false);
        $response->assertStatus(404);
        $this->assertFalse(
            Attendance::query()->whereDate('date', now()->toDateString())->exists()
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function scanner_updates_checkout_on_second_scan()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $class = StudentClass::factory()->create();
        $student = Student::factory()->create([
            'class_id' => $class->id,
            'nisn' => 'TEST-67890',
        ]);

        // First scan (check_in)
        $response1 = $this->actingAs($admin)->postJson(route('admin.attendance.scan'), [
            'nisn' => 'TEST-67890',
        ]);

        $response1->assertJsonPath('success', true);

        $firstAttendance = Attendance::query()
            ->where('student_id', $student->id)
            ->whereDate('date', now()->toDateString())
            ->first();

        $this->assertNotNull($firstAttendance);
        $this->assertNotNull($firstAttendance->check_in);
        $this->assertNull($firstAttendance->check_out);

        // Second scan (check_out)
        $response2 = $this->actingAs($admin)->postJson(route('admin.attendance.scan'), [
            'nisn' => 'TEST-67890',
        ]);

        $response2->assertJsonPath('success', true);

        $updatedAttendance = Attendance::query()
            ->where('student_id', $student->id)
            ->whereDate('date', now()->toDateString())
            ->first();

        $this->assertNotNull($updatedAttendance);
        $this->assertNotNull($updatedAttendance->check_out);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function scanner_sets_status_based_on_time()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $class = StudentClass::factory()->create();
        $student = Student::factory()->create([
            'class_id' => $class->id,
            'nisn' => 'TEST-STATUS',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.attendance.scan'), [
            'nisn' => 'TEST-STATUS',
        ]);

        $response->assertJsonPath('success', true);
        // Status should be 'Hadir' or 'Telat' depending on current time
        $status = $response->json('data.status');
        $this->assertTrue(in_array($status, ['Hadir', 'Telat']), "Status should be 'Hadir' or 'Telat' but got '$status'");
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function scanner_requires_authentication()
    {
        $response = $this->postJson(route('admin.attendance.scan'), [
            'nisn' => 'TEST-12345',
        ]);

        $response->assertStatus(401);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function scanner_requires_admin_role()
    {
        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)->postJson(route('admin.attendance.scan'), [
            'nisn' => 'TEST-12345',
        ]);

        $response->assertStatus(403);
    }
}
