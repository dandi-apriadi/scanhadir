<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AttendanceScannerTest extends TestCase
{
    use RefreshDatabase;

    protected StudentClass $class;
    protected Student $student;
    protected User $teacher;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->class = StudentClass::factory()->create();
        $this->student = Student::factory()->create(['class_id' => $this->class->id]);
        $this->teacher = User::factory()->create(['role' => 'teacher']);
        $this->teacher->assignedClasses()->attach($this->class->id);
    }

    /** @test */
    public function teacher_can_access_scanner()
    {
        $response = $this->actingAs($this->teacher)->get('/admin/scanner');
        
        $response->assertStatus(200);
        $response->assertSeeLivewire('attendance-scanner');
    }

    /** @test */
    public function scanner_accepts_valid_qr_code()
    {
        Livewire::test('attendance-scanner')
            ->call('processScan', $this->student->qr_code)
            ->assertSet('status', 'success');
    }

    /** @test */
    public function scanner_creates_attendance_record_on_valid_scan()
    {
        $this->assertDatabaseMissing('attendances', [
            'student_id' => $this->student->id,
            'date' => now()->toDateString(),
        ]);

        Livewire::test('attendance-scanner')
            ->call('processScan', $this->student->qr_code)
            ->call('confirmStudent');

        $this->assertDatabaseHas('attendances', [
            'student_id' => $this->student->id,
            'date' => now()->toDateString(),
            'check_in' => true, // Has check_in value
        ]);
    }

    /** @test */
    public function scanner_rejects_invalid_qr_code()
    {
        Livewire::test('attendance-scanner')
            ->call('processScan', 'INVALID_CODE_12345')
            ->assertSet('status', 'error');
    }

    /** @test */
    public function scanner_prevents_scanning_on_holidays()
    {
        // Create a holiday for today
        $today = now()->toDateString();
        Holiday::factory()->create([
            'start_date' => $today,
            'end_date' => $today,
            'type' => 'holiday'
        ]);

        Livewire::test('attendance-scanner')
            ->call('processScan', $this->student->qr_code)
            ->assertSet('status', 'error')
            ->assertSee('Hari libur');
    }

    /** @test */
    public function scanner_detects_check_in_on_first_scan()
    {
        Livewire::test('attendance-scanner')
            ->call('processScan', $this->student->qr_code)
            ->assertSet('status', 'success')
            ->assertSee('Absen Masuk');
    }

    /** @test */
    public function scanner_detects_check_out_on_second_scan()
    {
        // First scan (check-in)
        Livewire::test('attendance-scanner')
            ->call('processScan', $this->student->qr_code)
            ->call('confirmStudent');

        // Second scan (check-out)
        Livewire::test('attendance-scanner')
            ->call('processScan', $this->student->qr_code)
            ->call('confirmStudent')
            ->assertSet('status', 'success')
            ->assertSee('Absen Pulang');
    }

    /** @test */
    public function scanner_records_check_in_time()
    {
        Livewire::test('attendance-scanner')
            ->call('processScan', $this->student->qr_code)
            ->call('confirmStudent');

        $attendance = Attendance::where('student_id', $this->student->id)
            ->where('date', now()->toDateString())
            ->first();

        $this->assertNotNull($attendance->check_in);
        $this->assertNull($attendance->check_out); // No check-out yet
    }

    /** @test */
    public function scanner_records_check_out_time()
    {
        // First scan
        Livewire::test('attendance-scanner')
            ->call('processScan', $this->student->qr_code)
            ->call('confirmStudent');

        // Second scan
        Livewire::test('attendance-scanner')
            ->call('processScan', $this->student->qr_code)
            ->call('confirmStudent');

        $attendance = Attendance::where('student_id', $this->student->id)
            ->where('date', now()->toDateString())
            ->first();

        $this->assertNotNull($attendance->check_in);
        $this->assertNotNull($attendance->check_out);
    }

    /** @test */
    public function scanner_marks_attendance_as_late_after_threshold()
    {
        // Mock time to be after 07:30 (late threshold)
        Carbon::setTestNow(now()->setHour(7)->setMinute(45));

        Livewire::test('attendance-scanner')
            ->call('processScan', $this->student->qr_code)
            ->call('confirmStudent');

        $attendance = Attendance::where('student_id', $this->student->id)
            ->where('date', now()->toDateString())
            ->first();

        $this->assertEquals('late', $attendance->status);
    }

    /** @test */
    public function scanner_marks_attendance_as_present_before_threshold()
    {
        // Mock time to be before 07:30 (on time)
        Carbon::setTestNow(now()->setHour(7)->setMinute(15));

        Livewire::test('attendance-scanner')
            ->call('processScan', $this->student->qr_code)
            ->call('confirmStudent');

        $attendance = Attendance::where('student_id', $this->student->id)
            ->where('date', now()->toDateString())
            ->first();

        $this->assertEquals('present', $attendance->status);
    }

    /** @test */
    public function scanner_increments_scan_counter()
    {
        Livewire::test('attendance-scanner')
            ->call('processScan', $this->student->qr_code)
            ->call('confirmStudent')
            ->assertSet('scanCount', 1)
            ->call('processScan', $this->student->qr_code)
            ->call('confirmStudent')
            ->assertSet('scanCount', 2);
    }

    /** @test */
    public function scanner_fires_success_event_on_valid_scan()
    {
        Livewire::test('attendance-scanner')
            ->call('processScan', $this->student->qr_code)
            ->call('confirmStudent')
            ->assertDispatched('scan-success');
    }

    /** @test */
    public function scanner_fires_error_event_on_invalid_scan()
    {
        Livewire::test('attendance-scanner')
            ->call('processScan', 'INVALID')
            ->assertDispatched('scan-failed');
    }

    /** @test */
    public function scanner_shows_student_name_in_success()
    {
        Livewire::test('attendance-scanner')
            ->call('processScan', $this->student->qr_code)
            ->call('confirmStudent')
            ->assertDispatched('scan-success', function ($event) {
                return $event['name'] === $this->student->user->name;
            });
    }

    /** @test */
    public function scanner_shows_class_name_in_success()
    {
        Livewire::test('attendance-scanner')
            ->call('processScan', $this->student->qr_code)
            ->call('confirmStudent')
            ->assertDispatched('scan-success', function ($event) {
                return $event['class'] === $this->class->name;
            });
    }

    /** @test */
    public function scanner_cannot_scan_deleted_student()
    {
        $qrCode = $this->student->qr_code;
        $this->student->delete();

        Livewire::test('attendance-scanner')
            ->call('processScan', $qrCode)
            ->assertSet('status', 'error');
    }

    /** @test */
    public function scanner_handles_empty_qr_code()
    {
        Livewire::test('attendance-scanner')
            ->call('processScan', '')
            ->assertSet('status', 'error');
    }

    /** @test */
    public function scanner_handles_null_qr_code()
    {
        Livewire::test('attendance-scanner')
            ->call('processScan', null)
            ->assertSet('status', 'error');
    }

    /** @test */
    public function multiple_students_can_scan()
    {
        $student2 = Student::factory()->create(['class_id' => $this->class->id]);

        Livewire::test('attendance-scanner')
            ->call('processScan', $this->student->qr_code)
            ->call('confirmStudent');

        Livewire::test('attendance-scanner')
            ->call('processScan', $student2->qr_code)
            ->call('confirmStudent');

        $this->assertDatabaseHas('attendances', [
            'student_id' => $this->student->id,
        ]);

        $this->assertDatabaseHas('attendances', [
            'student_id' => $student2->id,
        ]);
    }

    /** @test */
    public function scanner_only_allows_one_attendance_per_day_per_student()
    {
        // First scan
        Livewire::test('attendance-scanner')
            ->call('processScan', $this->student->qr_code)
            ->call('confirmStudent');

        $firstAttendance = Attendance::where('student_id', $this->student->id)
            ->where('date', now()->toDateString())
            ->count();

        // Second scan for same student same day
        Livewire::test('attendance-scanner')
            ->call('processScan', $this->student->qr_code)
            ->call('confirmStudent');

        $secondAttendance = Attendance::where('student_id', $this->student->id)
            ->where('date', now()->toDateString())
            ->count();

        // Should still be 1 (same record)
        $this->assertEquals(1, $secondAttendance);
        $this->assertEquals(1, $firstAttendance);
    }

    /** @test */
    public function scanner_different_days_create_different_records()
    {
        // Scan today
        Livewire::test('attendance-scanner')
            ->call('processScan', $this->student->qr_code)
            ->call('confirmStudent');

        // Scan yesterday
        Carbon::setTestNow(now()->subDay());
        $yesterday = now()->toDateString();

        Livewire::test('attendance-scanner')
            ->call('processScan', $this->student->qr_code)
            ->call('confirmStudent');

        Carbon::setTestNow(null); // Reset

        $attendances = Attendance::where('student_id', $this->student->id)->count();

        $this->assertEquals(2, $attendances);
    }

    /** @test */
    public function scanner_shows_stats()
    {
        Livewire::test('attendance-scanner')
            ->call('processScan', $this->student->qr_code)
            ->call('confirmStudent')
            ->call('processScan', $this->student->qr_code)
            ->call('confirmStudent')
            ->assertSet('scanCount', 2)
            ->assertSet('status', 'success');
    }

    /** @test */
    public function non_teacher_cannot_access_scanner()
    {
        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)->get('/admin/scanner');

        $response->assertStatus(403);
    }

    /** @test */
    public function unauthenticated_user_redirected_to_login()
    {
        $response = $this->get('/admin/scanner');

        $response->assertRedirect('/auth/login');
    }
}
