<?php

namespace Tests\Feature;

use App\Livewire\AttendanceScanner;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StudentVerificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function student_verification_modal_shows_on_scan()
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $class = StudentClass::factory()->create();
        $student = Student::factory()->create([
            'class_id' => $class->id,
            'qr_code' => 'TEST-QR-12345',
        ]);

        Livewire::actingAs($teacher)
            ->test(AttendanceScanner::class)
            ->call('processScan', 'TEST-QR-12345')
            ->assertSet('awaitingConfirmation', true)
            ->assertNotNull('pendingStudent');
    }

    /** @test */
    public function pending_student_details_contains_name()
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $class = StudentClass::factory()->create();
        $student = Student::factory()->create([
            'class_id' => $class->id,
            'qr_code' => 'TEST-QR-67890',
            'nisn' => 'NISN-2024-001',
        ]);

        Livewire::actingAs($teacher)
            ->test(AttendanceScanner::class)
            ->call('processScan', 'TEST-QR-67890')
            ->assertViewHas('pendingStudentDetails', function ($details) {
                return $details['name'] === $student->user->name &&
                       $details['class'] === $class->name &&
                       $details['nisn'] === 'NISN-2024-001';
            });
    }

    /** @test */
    public function confirm_student_processes_attendance()
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $class = StudentClass::factory()->create();
        $student = Student::factory()->create([
            'class_id' => $class->id,
            'qr_code' => 'TEST-QR-CONFIRM',
        ]);

        $component = Livewire::actingAs($teacher)
            ->test(AttendanceScanner::class);

        $component->call('processScan', 'TEST-QR-CONFIRM')
            ->assertSet('awaitingConfirmation', true)
            ->call('confirmStudent')
            ->assertSet('awaitingConfirmation', false);

        // Verify attendance was created
        $this->assertDatabaseHas('attendances', [
            'student_id' => $student->id,
            'date' => now()->toDateString(),
        ]);
    }

    /** @test */
    public function cancel_student_resets_state_without_recording()
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $class = StudentClass::factory()->create();
        $student = Student::factory()->create([
            'class_id' => $class->id,
            'qr_code' => 'TEST-QR-CANCEL',
        ]);

        Livewire::actingAs($teacher)
            ->test(AttendanceScanner::class)
            ->call('processScan', 'TEST-QR-CANCEL')
            ->assertSet('awaitingConfirmation', true)
            ->call('cancelStudent')
            ->assertSet('awaitingConfirmation', false);

        // Verify attendance was NOT created
        $this->assertDatabaseMissing('attendances', [
            'student_id' => $student->id,
            'date' => now()->toDateString(),
        ]);
    }

    /** @test */
    public function confirmation_creates_correct_attendance_status()
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $class = StudentClass::factory()->create();
        $student = Student::factory()->create([
            'class_id' => $class->id,
            'qr_code' => 'TEST-QR-STATUS',
        ]);

        Livewire::actingAs($teacher)
            ->test(AttendanceScanner::class)
            ->call('processScan', 'TEST-QR-STATUS')
            ->call('confirmStudent');

        // Status should be 'present' or 'late' depending on current time
        $attendance = Attendance::where('student_id', $student->id)->first();
        $this->assertNotNull($attendance);
        $this->assertTrue(in_array($attendance->status, ['present', 'late']));
    }

    /** @test */
    public function invalid_qr_code_does_not_show_verification()
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        Livewire::actingAs($teacher)
            ->test(AttendanceScanner::class)
            ->call('processScan', 'INVALID-QR-CODE')
            ->assertSet('awaitingConfirmation', false)
            ->assertSet('status', 'error');
    }

    /** @test */
    public function student_with_photo_includes_photo_url_in_verification()
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $class = StudentClass::factory()->create();
        $student = Student::factory()->create([
            'class_id' => $class->id,
            'qr_code' => 'TEST-QR-PHOTO',
            'photo_path' => 'students/123-photo.jpg',
        ]);

        Livewire::actingAs($teacher)
            ->test(AttendanceScanner::class)
            ->call('processScan', 'TEST-QR-PHOTO')
            ->assertViewHas('pendingStudentDetails', function ($details) {
                return $details['has_photo'] === true && !empty($details['photo_url']);
            });
    }

    /** @test */
    public function scan_only_without_confirmation_does_not_create_attendance()
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $class = StudentClass::factory()->create();
        $student = Student::factory()->create([
            'class_id' => $class->id,
            'qr_code' => 'TEST-QR-NO-CONFIRM',
        ]);

        Livewire::actingAs($teacher)
            ->test(AttendanceScanner::class)
            ->call('processScan', 'TEST-QR-NO-CONFIRM')
            ->assertSet('awaitingConfirmation', true);

        // Verify attendance was NOT created yet (waiting for confirmation)
        $this->assertDatabaseMissing('attendances', [
            'student_id' => $student->id,
            'date' => now()->toDateString(),
        ]);
    }
}
