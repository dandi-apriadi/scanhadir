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

    /** @test */
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

        $this->assertDatabaseHas('attendances', [
            'student_id' => $student->id,
            'date' => now()->toDateString(),
        ]);
    }

    /** @test */
    public function scanner_returns_error_for_invalid_nisn()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->postJson(route('admin.attendance.scan'), [
            'nisn' => 'INVALID-NISN',
        ]);

        $response->assertJsonPath('success', false);
        $response->assertStatus(404);
        $this->assertDatabaseMissing('attendances', [
            'date' => now()->toDateString(),
        ]);
    }

    /** @test */
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
        $this->assertDatabaseHas('attendances', [
            'student_id' => $student->id,
            'date' => now()->toDateString(),
            'check_in' => $response1->json('data.check_in'),
            'check_out' => null,
        ]);

        // Second scan (check_out)
        $response2 = $this->actingAs($admin)->postJson(route('admin.attendance.scan'), [
            'nisn' => 'TEST-67890',
        ]);

        $response2->assertJsonPath('success', true);
        $this->assertDatabaseHas('attendances', [
            'student_id' => $student->id,
            'date' => now()->toDateString(),
            'check_out' => $response2->json('data.check_out'),
        ]);
    }

    /** @test */
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
        // Status should be 'present' or 'late' depending on current time
        $status = $response->json('data.status');
        $this->assertTrue(in_array($status, ['present', 'late']), "Status should be 'present' or 'late' but got '$status'");
    }

    /** @test */
    public function scanner_requires_authentication()
    {
        $response = $this->postJson(route('admin.attendance.scan'), [
            'nisn' => 'TEST-12345',
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function scanner_requires_admin_role()
    {
        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)->postJson(route('admin.attendance.scan'), [
            'nisn' => 'TEST-12345',
        ]);

        $response->assertStatus(403);
    }
}
