<?php

namespace Tests\Unit;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    private function createTestStudent()
    {
        $user = User::factory()->create();
        $class = StudentClass::factory()->create();
        return Student::factory()->create(['user_id' => $user->id, 'class_id' => $class->id]);
    }

    public function test_attendance_can_record_check_in(): void
    {
        $student = $this->createTestStudent();
        $today = now()->toDateString();
        $checkInTime = now()->toTimeString();

        $attendance = Attendance::create([
            'student_id' => $student->id,
            'date' => $today,
            'status' => 'present',
            'check_in' => $checkInTime,
        ]);

        $this->assertNotNull($attendance->id);
        $this->assertEquals($student->id, $attendance->student_id);
        $this->assertEquals('present', $attendance->status);
        $this->assertNotNull($attendance->check_in);
    }

    public function test_attendance_can_record_check_out(): void
    {
        $student = $this->createTestStudent();
        $today = now()->toDateString();

        $attendance = Attendance::create([
            'student_id' => $student->id,
            'date' => $today,
            'status' => 'present',
            'check_in' => now()->subHours(8)->toTimeString(),
            'check_out' => now()->toTimeString(),
        ]);

        $this->assertNotNull($attendance->check_out);
    }

    public function test_multiple_students_can_attend_same_day(): void
    {
        $student1 = $this->createTestStudent();
        $student2 = $this->createTestStudent();
        $today = now()->toDateString();

        $attendance1 = Attendance::create([
            'student_id' => $student1->id,
            'date' => $today,
            'status' => 'present',
        ]);

        $attendance2 = Attendance::create([
            'student_id' => $student2->id,
            'date' => $today,
            'status' => 'late',
        ]);

        $this->assertNotNull($attendance1->id);
        $this->assertNotNull($attendance2->id);
        $this->assertNotEquals($attendance1->id, $attendance2->id);
    }

    public function test_attendance_status_values_are_valid(): void
    {
        $student = $this->createTestStudent();

        $statuses = ['present', 'late', 'sick', 'excused', 'absent'];

        foreach ($statuses as $i => $status) {
            $date = now()->subDays($i)->toDateString();
            $attendance = Attendance::create([
                'student_id' => $student->id,
                'date' => $date,
                'status' => $status,
            ]);

            $this->assertEquals($status, $attendance->status);
        }
    }

    public function test_attendance_has_relation_to_student(): void
    {
        $student = $this->createTestStudent();
        $today = now()->toDateString();

        $attendance = Attendance::create([
            'student_id' => $student->id,
            'date' => $today,
            'status' => 'present',
        ]);

        $this->assertTrue($attendance->student()->exists());
        $this->assertEquals($student->id, $attendance->student->id);
    }

    public function test_same_student_can_have_multiple_attendances_different_dates(): void
    {
        $student = $this->createTestStudent();

        for ($i = 0; $i < 3; $i++) {
            Attendance::create([
                'student_id' => $student->id,
                'date' => now()->subDays($i)->toDateString(),
                'status' => 'present',
            ]);
        }

        $this->assertCount(3, $student->attendances);
    }

    public function test_attendance_fillable_fields(): void
    {
        $student = $this->createTestStudent();

        $data = [
            'student_id' => $student->id,
            'date' => now()->toDateString(),
            'check_in' => '07:30:00',
            'check_out' => '14:30:00',
            'status' => 'present',
            'notes' => 'Attendance recorded successfully',
        ];

        $attendance = Attendance::create($data);

        $this->assertEquals('07:30:00', $attendance->check_in);
        $this->assertEquals('14:30:00', $attendance->check_out);
        $this->assertEquals('Attendance recorded successfully', $attendance->notes);
    }
}
