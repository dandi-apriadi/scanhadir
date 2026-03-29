<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\StudentClass;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReportServiceTest extends TestCase
{
    use RefreshDatabase;

    private ReportService $reportService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->reportService = new ReportService();
    }

    private function createTestStudent(): Student
    {
        $user = User::factory()->create();
        $class = StudentClass::factory()->create();

        return Student::factory()->create([
            'user_id' => $user->id,
            'class_id' => $class->id,
        ]);
    }

    public function test_daily_attendance_summary_without_filter(): void
    {
        $date = now()->toDateString();
        $student = $this->createTestStudent();

        Attendance::create([
            'student_id' => $student->id,
            'date' => $date,
            'status' => 'present',
        ]);

        $summary = $this->reportService->getDailyAttendanceSummary($date);

        $this->assertEquals($date, $summary['date']);
        $this->assertEquals(1, $summary['total_scanned']);
        $this->assertEquals(1, $summary['present']);
    }

    public function test_daily_attendance_summary_with_class_filter(): void
    {
        $date = now()->toDateString();
        $class1 = StudentClass::factory()->create();
        $class2 = StudentClass::factory()->create();

        $user1 = User::factory()->create();
        $student1 = Student::factory()->create([
            'user_id' => $user1->id,
            'class_id' => $class1->id,
        ]);

        $user2 = User::factory()->create();
        $student2 = Student::factory()->create([
            'user_id' => $user2->id,
            'class_id' => $class2->id,
        ]);

        Attendance::create([
            'student_id' => $student1->id,
            'date' => $date,
            'status' => 'present',
        ]);

        Attendance::create([
            'student_id' => $student2->id,
            'date' => $date,
            'status' => 'absent',
        ]);

        $summary = $this->reportService->getDailyAttendanceSummary($date, $class1->id);

        $this->assertEquals(1, $summary['total_scanned']);
        $this->assertEquals(1, $summary['present']);
        $this->assertEquals(0, $summary['absent']);
    }

    public function test_class_attendance_stats_with_student_filter(): void
    {
        $class = StudentClass::factory()->create();
        $month = now()->format('Y-m');

        $user1 = User::factory()->create();
        $student1 = Student::factory()->create([
            'user_id' => $user1->id,
            'class_id' => $class->id,
        ]);

        $user2 = User::factory()->create();
        $student2 = Student::factory()->create([
            'user_id' => $user2->id,
            'class_id' => $class->id,
        ]);

        Attendance::create([
            'student_id' => $student1->id,
            'date' => now()->toDateString(),
            'status' => 'present',
        ]);

        Attendance::create([
            'student_id' => $student2->id,
            'date' => now()->toDateString(),
            'status' => 'absent',
        ]);

        // Without filter: should count both
        $statsAll = $this->reportService->getClassAttendanceStats($class->id, $month);
        $this->assertEquals(1, $statsAll['present']);
        $this->assertEquals(1, $statsAll['absent']);

        // With student filter: should count only student1
        $statsStudent1 = $this->reportService->getClassAttendanceStats($class->id, $month, $student1->id);
        $this->assertEquals($student1->id, $statsStudent1['student_id']);
        $this->assertEquals(1, $statsStudent1['present']);
        $this->assertEquals(0, $statsStudent1['absent']);
    }

    public function test_attendance_rows_for_date_with_filters(): void
    {
        $date = now()->toDateString();
        $class = StudentClass::factory()->create();

        $user = User::factory()->create();
        $student = Student::factory()->create([
            'user_id' => $user->id,
            'class_id' => $class->id,
        ]);

        $attendance = Attendance::create([
            'student_id' => $student->id,
            'date' => $date,
            'status' => 'present',
            'check_in' => now()->toTimeString(),
        ]);

        $rows = $this->reportService->getAttendanceRowsForDate($date, $class->id, $student->id);

        $this->assertEquals(1, $rows->count());
        $this->assertEquals($student->user->name, $rows[0]['student_name']);
        $this->assertEquals($class->name, $rows[0]['class_name']);
        $this->assertEquals($student->nisn, $rows[0]['nisn']);
    }

    public function test_teacher_attendance_summary(): void
    {
        $class = StudentClass::factory()->create();
        $teacher = User::factory()->create(['role' => 'teacher']);
        $teacher->assignedClasses()->attach($class->id);

        $date = now()->toDateString();
        $user = User::factory()->create();
        $student = Student::factory()->create([
            'user_id' => $user->id,
            'class_id' => $class->id,
        ]);

        Attendance::create([
            'student_id' => $student->id,
            'date' => $date,
            'status' => 'present',
        ]);

        $summary = $this->reportService->getTeacherAttendanceSummary($teacher->id, $date);

        $this->assertEquals($teacher->id, $summary['teacher_id']);
        $this->assertEquals($teacher->name, $summary['teacher_name']);
        $this->assertEquals(1, $summary['assigned_classes']);
        $this->assertEquals(1, $summary['total_students']);
        $this->assertEquals(1, $summary['present']);
    }

    public function test_teacher_attendance_summary_empty_when_no_classes(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $date = now()->toDateString();

        $summary = $this->reportService->getTeacherAttendanceSummary($teacher->id, $date);

        $this->assertEquals(0, $summary['assigned_classes']);
        $this->assertEquals(0, $summary['total_students']);
    }

    public function test_teacher_attendance_rows_with_class_filter(): void
    {
        $class1 = StudentClass::factory()->create();
        $class2 = StudentClass::factory()->create();
        $teacher = User::factory()->create(['role' => 'teacher']);
        $teacher->assignedClasses()->attach([$class1->id, $class2->id]);

        $date = now()->toDateString();

        $user1 = User::factory()->create();
        $student1 = Student::factory()->create([
            'user_id' => $user1->id,
            'class_id' => $class1->id,
        ]);

        $user2 = User::factory()->create();
        $student2 = Student::factory()->create([
            'user_id' => $user2->id,
            'class_id' => $class2->id,
        ]);

        Attendance::create([
            'student_id' => $student1->id,
            'date' => $date,
            'status' => 'present',
        ]);

        Attendance::create([
            'student_id' => $student2->id,
            'date' => $date,
            'status' => 'absent',
        ]);

        // Without class filter: should return both
        $allRows = $this->reportService->getTeacherAttendanceRows($teacher->id, $date);
        $this->assertEquals(2, $allRows->count());

        // With class filter: should return only class1 attendances
        $class1Rows = $this->reportService->getTeacherAttendanceRows($teacher->id, $date, $class1->id);
        $this->assertEquals(1, $class1Rows->count());
        $this->assertEquals($class1->name, $class1Rows[0]['class_name']);
    }

    public function test_is_holiday_date(): void
    {
        $holidayDate = now()->toDateString();
        Holiday::create([
            'name' => 'Test Holiday',
            'start_date' => $holidayDate,
            'end_date' => $holidayDate,
            'type' => 'national',
        ]);

        $this->assertTrue($this->reportService->isHolidayDate($holidayDate));
        $this->assertFalse($this->reportService->isHolidayDate(now()->addDay()->toDateString()));
    }

    public function test_attendance_stats_for_range(): void
    {
        $class = StudentClass::factory()->create();
        $startDate = now()->subDays(5)->toDateString();
        $endDate = now()->toDateString();

        $user = User::factory()->create();
        $student = Student::factory()->create([
            'user_id' => $user->id,
            'class_id' => $class->id,
        ]);

        // Create attendance records
        for ($i = 0; $i < 3; $i++) {
            Attendance::create([
                'student_id' => $student->id,
                'date' => now()->subDays($i)->toDateString(),
                'status' => 'present',
            ]);
        }

        $stats = $this->reportService->getAttendanceStatsForRange($startDate, $endDate, $class->id);

        $this->assertEquals($startDate, $stats['start_date']);
        $this->assertEquals($endDate, $stats['end_date']);
        $this->assertGreaterThan(0, $stats['total_workdays']);
        $this->assertEquals(3, $stats['total_records']);
        $this->assertEquals(3, $stats['present']);
        $this->assertGreaterThan(0, $stats['attendance_percentage']);
    }

    public function test_attendance_stats_for_range_with_holidays(): void
    {
        $class = StudentClass::factory()->create();
        $holidayDate = now()->toDateString();
        $startDate = now()->subDays(5)->toDateString();
        $endDate = now()->toDateString();

        Holiday::create([
            'name' => 'Holiday',
            'start_date' => $holidayDate,
            'end_date' => $holidayDate,
            'type' => 'national',
        ]);

        $user = User::factory()->create();
        $student = Student::factory()->create([
            'user_id' => $user->id,
            'class_id' => $class->id,
        ]);

        Attendance::create([
            'student_id' => $student->id,
            'date' => now()->subDays(1)->toDateString(),
            'status' => 'present',
        ]);

        $stats = $this->reportService->getAttendanceStatsForRange($startDate, $endDate, $class->id);

        // Workdays should exclude the holiday
        $this->assertLessThan(6, $stats['total_workdays']);
        $this->assertEquals(1, $stats['total_records']);
    }

    public function test_student_attendance_history(): void
    {
        $student = $this->createTestStudent();
        $startDate = now()->subDays(10)->toDateString();

        Attendance::create([
            'student_id' => $student->id,
            'date' => now()->subDays(5)->toDateString(),
            'status' => 'present',
        ]);

        Attendance::create([
            'student_id' => $student->id,
            'date' => now()->toDateString(),
            'status' => 'absent',
        ]);

        $history = $this->reportService->getStudentAttendanceHistory($student->id, [
            'start' => $startDate,
            'end' => now()->toDateString(),
        ]);

        $this->assertEquals(2, $history->count());
        $this->assertEquals('present', $history[0]->status);
        $this->assertEquals('absent', $history[1]->status);
    }
}
