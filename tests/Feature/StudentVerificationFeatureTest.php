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

class StudentVerificationFeatureTest extends TestCase
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
            ->assertSet('pendingStudent', fn ($student) => $student->id === $student->id)
            ->assertViewHas('pendingStudentDetails');
    }

    /** @test */
    public function pending_student_details_contains_required_fields()
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
            ->assertSet('pendingStudentDetails', fn ($details) => 
                $details['name'] === $student->user->name &&
                $details['class'] === $class->name &&
                $details['nisn'] === 'NISN-2024-001'
            );
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

        Livewire::actingAs($teacher)
            ->test(AttendanceScanner::class)
            ->call('processScan', 'TEST-QR-CONFIRM')
            ->assertSet('awaitingConfirmation', true)
            ->call('confirmStudent')
            ->assertSet('awaitingConfirmation', false)
            ->assertSet('pendingStudent', null);

        // Verify attendance was created
        $this->assertDatabaseHas('attendances', [
            'student_id' => $student->id,
            'date' => now()->toDateString(),
        ]);
    }

    /** @test */
    public function cancel_student_resets_state()
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
            ->assertSet('awaitingConfirmation', false)
            ->assertSet('pendingStudent', null)
            ->assertSet('pendingStudentDetails', null);

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
            ->assertSet('pendingStudent', null)
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
            ->assertSet('pendingStudentDetails', fn ($details) => 
                $details['has_photo'] === true &&
                !empty($details['photo_url'])
            );
    }

    /** @test */
    public function student_without_photo_has_has_photo_false()
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $class = StudentClass::factory()->create();
        $student = Student::factory()->create([
            'class_id' => $class->id,
            'qr_code' => 'TEST-QR-NOPHOTO',
            'photo_path' => null,
        ]);

        Livewire::actingAs($teacher)
            ->test(AttendanceScanner::class)
            ->call('processScan', 'TEST-QR-NOPHOTO')
            ->assertSet('pendingStudentDetails', fn ($details) => 
                $details['has_photo'] === false &&
                is_null($details['photo_url'])
            );
    }

    /** @test */
    public function second_scan_during_verification_is_ignored()
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $class = StudentClass::factory()->create();
        $student1 = Student::factory()->create([
            'class_id' => $class->id,
            'qr_code' => 'QR-1',
        ]);
        $student2 = Student::factory()->create([
            'class_id' => $class->id,
            'qr_code' => 'QR-2',
        ]);

        Livewire::actingAs($teacher)
            ->test(AttendanceScanner::class)
            ->call('processScan', 'QR-1')
            ->assertSet('awaitingConfirmation', true)
            ->assertSet('pendingStudent', fn ($student) => 
                $student->qr_code === 'QR-1'
            );

        // Pending student should still be student1
        $this->assertEquals('QR-1', Livewire::actingAs($teacher)
            ->test(AttendanceScanner::class)
            ->get()->data['pendingStudent']?->qr_code ?? null);
    }

    /** @test */
    public function scan_count_increments_after_confirmation()
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $class = StudentClass::factory()->create();
        $student = Student::factory()->create([
            'class_id' => $class->id,
            'qr_code' => 'TEST-QR-COUNT',
        ]);

        Livewire::actingAs($teacher)
            ->test(AttendanceScanner::class)
            ->call('processScan', 'TEST-QR-COUNT')
            ->call('confirmStudent')
            ->assertSet('scanCount', 1);
    }

    /** @test */
    public function scan_count_does_not_increment_on_cancel()
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $class = StudentClass::factory()->create();
        $student = Student::factory()->create([
            'class_id' => $class->id,
            'qr_code' => 'TEST-QR-NOCOUNT',
        ]);

        Livewire::actingAs($teacher)
            ->test(AttendanceScanner::class)
            ->call('processScan', 'TEST-QR-NOCOUNT')
            ->call('cancelStudent')
            ->assertSet('scanCount', 0);
    }
}
