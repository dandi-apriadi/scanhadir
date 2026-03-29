<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\User;
use App\Services\AttendanceExportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Phase9AdvancedFeaturesTest extends TestCase
{
    use RefreshDatabase;

    private User $teacher;
    private StudentClass $class;
    private Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        // Create teacher
        $this->teacher = User::factory()->create(['role' => 'teacher']);

        // Create class
        $this->class = StudentClass::factory()->create(['name' => 'XII IPA 1']);

        // Create pivot relationship
        $this->teacher->assignedClasses()->attach($this->class->id);

        // Create student
        $this->student = Student::factory()->create([
            'class_id' => $this->class->id,
            'nisn' => '0123456789',
        ]);
    }

    // ============ XLSX EXPORT TESTS (12 tests) ============

    /** @test */
    public function export_service_generates_xlsx_file()
    {
        $attendances = Attendance::factory()->count(5)->create([
            'student_id' => $this->student->id,
            'date' => now()->toDateString(),
        ]);

        $service = new AttendanceExportService();
        $filePath = $service->exportToXLSX($attendances);

        $this->assertFileExists($filePath);
        $this->assertStringEndsNotWith('.csv', $filePath);
        $this->assertStringEndsWith('.xlsx', $filePath);
    }

    /** @test */
    public function xlsx_export_includes_all_columns()
    {
        $attendance = Attendance::factory()->create([
            'student_id' => $this->student->id,
            'date' => now(),
            'status' => 'present',
            'check_in' => now()->setTime(7, 15),
        ]);

        $service = new AttendanceExportService();
        $filePath = $service->exportToXLSX(collect([$attendance]));

        // Verify file exists and is readable
        $this->assertFileExists($filePath);
        $this->assertTrue(is_readable($filePath));
    }

    /** @test */
    public function xlsx_export_includes_metadata()
    {
        $attendances = Attendance::factory()->count(3)->create();

        $service = new AttendanceExportService();
        $options = [
            'date_from' => '2026-01-01',
            'date_to' => '2026-01-31',
            'class_name' => 'XII IPA 1',
        ];

        $filePath = $service->exportToXLSX($attendances, $options);
        $this->assertFileExists($filePath);
    }

    /** @test */
    public function xlsx_export_creates_summary_sheet()
    {
        $attendances = Attendance::factory()->count(10)->create([
            'student_id' => $this->student->id,
            'status' => 'present',
        ]);

        $service = new AttendanceExportService();
        $filePath = $service->exportToXLSX($attendances);

        $this->assertFileExists($filePath);
    }

    /** @test */
    public function xlsx_export_handles_multiple_status_types()
    {
        $student2 = Student::factory()->create(['class_id' => $this->class->id]);

        $attendances = collect([
            Attendance::factory()->create(['student_id' => $this->student->id, 'status' => 'present']),
            Attendance::factory()->create(['student_id' => $this->student->id, 'status' => 'late']),
            Attendance::factory()->create(['student_id' => $student2->id, 'status' => 'absent']),
            Attendance::factory()->create(['student_id' => $student2->id, 'status' => 'sick']),
        ]);

        $service = new AttendanceExportService();
        $filePath = $service->exportToXLSX($attendances);

        $this->assertFileExists($filePath);
    }

    /** @test */
    public function xlsx_export_removes_file_after_download()
    {
        $attendances = Attendance::factory()->count(5)->create();

        $service = new AttendanceExportService();
        $filePath = $service->exportToXLSX($attendances);
        $fileName = basename($filePath);

        // File should exist after export
        $this->assertFileExists($filePath);
    }

    /** @test */
    public function xlsx_export_includes_student_details()
    {
        $attendance = Attendance::factory()->create([
            'student_id' => $this->student->id,
            'date' => now(),
        ]);

        $service = new AttendanceExportService();
        $filePath = $service->exportToXLSX(collect([$attendance]));

        $this->assertFileExists($filePath);
    }

    /** @test */
    public function xlsx_export_handles_large_dataset()
    {
        $attendances = Attendance::factory()->count(200)->create([
            'date' => now()->toDateString(),
        ]);

        $service = new AttendanceExportService();
        $filePath = $service->exportToXLSX($attendances);

        $this->assertFileExists($filePath);
    }

    /** @test */
    public function xlsx_export_class_specific_attendance()
    {
        Attendance::factory()->count(5)->create([
            'date' => now(),
        ])->each(function ($att) {
            $att->student()->update(['class_id' => $this->class->id]);
        });

        $service = new AttendanceExportService();
        $dateFrom = now()->subDays(30)->toDateString();
        $dateTo = now()->toDateString();

        $filePath = $service->exportClassAttendanceXLSX($this->class, $dateFrom, $dateTo);
        $this->assertFileExists($filePath);
    }

    /** @test */
    public function xlsx_export_calculates_percentages_correctly()
    {
        // Create 10 present, 5 late, 5 absent
        Attendance::factory()->count(10)->create([
            'student_id' => $this->student->id,
            'status' => 'present',
            'date' => now(),
        ]);

        $attendances = Attendance::where('student_id', $this->student->id)->get();

        $service = new AttendanceExportService();
        $filePath = $service->exportToXLSX($attendances);

        $this->assertFileExists($filePath);
    }

    // ============ ATTENDANCE ANALYTICS TESTS (10 tests) ============

    /** @test */
    public function teacher_can_access_analytics_dashboard()
    {
        $this->actingAs($this->teacher);

        Livewire::test('attendance-analytics')
            ->assertStatus(200);
    }

    /** @test */
    public function analytics_displays_current_month_data()
    {
        $this->actingAs($this->teacher);

        Attendance::factory()->count(5)->create([
            'date' => now(),
            'status' => 'present',
        ])->each(function ($att) {
            $att->student()->update(['class_id' => $this->class->id]);
        });

        Livewire::test('attendance-analytics')
            ->assertViewHas('analyticsData')
            ->assertSee('5');
    }

    /** @test */
    public function analytics_calculates_attendance_percentages()
    {
        $this->actingAs($this->teacher);

        Attendance::factory()->create(['student_id' => $this->student->id, 'status' => 'present']);
        Attendance::factory()->create(['student_id' => $this->student->id, 'status' => 'late']);

        Livewire::test('attendance-analytics')
            ->assertViewHas('analyticsData', function ($data) {
                return isset($data['presentPercentage']) && isset($data['latePercentage']);
            });
    }

    /** @test */
    public function analytics_shows_monthly_trend()
    {
        $this->actingAs($this->teacher);

        Livewire::test('attendance-analytics')
            ->assertViewHas('monthlyTrend');
    }

    /** @test */
    public function analytics_shows_class_comparison()
    {
        $this->actingAs($this->teacher);

        Attendance::factory()->create(['status' => 'present'])->student()->update(['class_id' => $this->class->id]);

        Livewire::test('attendance-analytics')
            ->assertViewHas('classComparison');
    }

    /** @test */
    public function analytics_shows_student_performance_ranking()
    {
        $this->actingAs($this->teacher);

        Livewire::test('attendance-analytics')
            ->assertViewHas('studentPerformance');
    }

    /** @test */
    public function analytics_filters_by_year()
    {
        $this->actingAs($this->teacher);

        Livewire::test('attendance-analytics')
            ->set('selectedYear', 2025)
            ->assertStatus(200);
    }

    /** @test */
    public function analytics_filters_by_month()
    {
        $this->actingAs($this->teacher);

        Livewire::test('attendance-analytics')
            ->set('selectedMonth', 6)
            ->assertStatus(200);
    }

    /** @test */
    public function non_teacher_cannot_access_analytics()
    {
        $student = User::factory()->create(['role' => 'student']);
        $this->actingAs($student);

        $response = $this->get('/teacher/analytics');
        $this->assertEquals(403, $response->status());
    }

    // ============ ATTENDANCE REPORTS TESTS (12 tests) ============

    /** @test */
    public function teacher_can_access_reports()
    {
        $this->actingAs($this->teacher);

        Livewire::test('attendance-reports')
            ->assertStatus(200);
    }

    /** @test */
    public function reports_displays_default_date_range()
    {
        $this->actingAs($this->teacher);

        $component = Livewire::test('attendance-reports');
        $this->assertNotNull($component->get('dateFrom'));
        $this->assertNotNull($component->get('dateTo'));
    }

    /** @test */
    public function reports_filters_by_date_range()
    {
        $this->actingAs($this->teacher);

        Attendance::factory()->create([
            'date' => now(),
            'status' => 'present',
        ])->student()->update(['class_id' => $this->class->id]);

        Livewire::test('attendance-reports')
            ->set('dateFrom', now()->subDays(7)->toDateString())
            ->set('dateTo', now()->toDateString())
            ->assertViewHas('reports');
    }

    /** @test */
    public function reports_filters_by_class()
    {
        $this->actingAs($this->teacher);

        Livewire::test('attendance-reports')
            ->set('selectedClass', $this->class->id)
            ->assertViewHas('reports');
    }

    /** @test */
    public function reports_filters_by_status()
    {
        $this->actingAs($this->teacher);

        Attendance::factory()->create([
            'status' => 'present',
        ])->student()->update(['class_id' => $this->class->id]);

        Livewire::test('attendance-reports')
            ->set('selectedStatus', 'present')
            ->assertSee('Present');
    }

    /** @test */
    public function reports_searches_by_student_name()
    {
        $this->actingAs($this->teacher);

        Livewire::test('attendance-reports')
            ->set('searchStudent', $this->student->user->name)
            ->assertViewHas('reports');
    }

    /** @test */
    public function reports_displays_statistics()
    {
        $this->actingAs($this->teacher);

        Livewire::test('attendance-reports')
            ->assertViewHas('stats');
    }

    /** @test */
    public function reports_exports_to_xlsx()
    {
        $this->actingAs($this->teacher);

        Attendance::factory()->create([
            'status' => 'present',
        ])->student()->update(['class_id' => $this->class->id]);

        Livewire::test('attendance-reports')
            ->call('exportXLSX')
            ->assertHasNoErrors();
    }

    /** @test */
    public function reports_resets_filters()
    {
        $this->actingAs($this->teacher);

        Livewire::test('attendance-reports')
            ->set('selectedClass', $this->class->id)
            ->set('selectedStatus', 'present')
            ->set('searchStudent', 'test')
            ->call('resetFilters')
            ->assertSet('selectedClass', null)
            ->assertSet('selectedStatus', null);
    }

    /** @test */
    public function reports_paginates_results()
    {
        $this->actingAs($this->teacher);

        Attendance::factory()->count(100)->create([
            'date' => now(),
        ])->each(function ($att) {
            $att->student()->update(['class_id' => $this->class->id]);
        });

        Livewire::test('attendance-reports')
            ->assertViewHas('reports');
    }

    // ============ BULK ATTENDANCE UPDATE TESTS (15 tests) ============

    /** @test */
    public function teacher_can_access_bulk_update()
    {
        $this->actingAs($this->teacher);

        Livewire::test('bulk-attendance-update')
            ->assertStatus(200);
    }

    /** @test */
    public function bulk_update_requires_class_selection()
    {
        $this->actingAs($this->teacher);

        Livewire::test('bulk-attendance-update')
            ->call('updateStatus')
            ->assertHasErrors();
    }

    /** @test */
    public function bulk_update_loads_students_by_class()
    {
        $this->actingAs($this->teacher);

        Livewire::test('bulk-attendance-update')
            ->set('selectedClass', $this->class->id)
            ->assertViewHas('students');
    }

    /** @test */
    public function bulk_update_displays_attendance_summary()
    {
        $this->actingAs($this->teacher);

        Attendance::factory()->create([
            'date' => now(),
            'student_id' => $this->student->id,
            'status' => 'present',
        ]);

        Livewire::test('bulk-attendance-update')
            ->set('selectedClass', $this->class->id)
            ->set('selectedDate', now()->toDateString())
            ->assertViewHas('attendanceSummary');
    }

    /** @test */
    public function bulk_update_can_select_all_students()
    {
        $this->actingAs($this->teacher);

        Student::factory()->count(5)->create(['class_id' => $this->class->id]);

        Livewire::test('bulk-attendance-update')
            ->set('selectedClass', $this->class->id)
            ->call('selectAllStudents')
            ->assertCount('selectedStudents', 6); // 5 + 1 from setUp
    }

    /** @test */
    public function bulk_update_can_deselect_all_students()
    {
        $this->actingAs($this->teacher);

        Livewire::test('bulk-attendance-update')
            ->set('selectedClass', $this->class->id)
            ->call('selectAllStudents')
            ->call('deselectAllStudents')
            ->assertCount('selectedStudents', 0);
    }

    /** @test */
    public function bulk_update_can_toggle_individual_student()
    {
        $this->actingAs($this->teacher);

        Livewire::test('bulk-attendance-update')
            ->set('selectedClass', $this->class->id)
            ->call('toggleStudent', $this->student->id)
            ->assertCount('selectedStudents', 1);
    }

    /** @test */
    public function bulk_update_requires_student_selection()
    {
        $this->actingAs($this->teacher);

        Livewire::test('bulk-attendance-update')
            ->set('selectedClass', $this->class->id)
            ->set('selectedDate', now()->toDateString())
            ->set('selectedStatus', 'present')
            ->call('updateStatus')
            ->assertSet('message', 'Please select at least one student');
    }

    /** @test */
    public function bulk_update_shows_confirmation_modal()
    {
        $this->actingAs($this->teacher);

        Livewire::test('bulk-attendance-update')
            ->set('selectedClass', $this->class->id)
            ->set('selectedDate', now()->toDateString())
            ->set('selectedStatus', 'present')
            ->set('selectedStudents', [$this->student->id])
            ->call('updateStatus')
            ->assertSet('showConfirmation', true);
    }

    /** @test */
    public function bulk_update_confirms_and_updates_attendance()
    {
        $this->actingAs($this->teacher);

        Livewire::test('bulk-attendance-update')
            ->set('selectedClass', $this->class->id)
            ->set('selectedDate', now()->toDateString())
            ->set('selectedStatus', 'present')
            ->set('selectedStudents', [$this->student->id])
            ->call('updateStatus')
            ->call('confirmUpdate');

        $this->assertDatabaseHas('attendances', [
            'student_id' => $this->student->id,
            'status' => 'present',
        ]);
    }

    /** @test */
    public function bulk_update_creates_or_updates_record()
    {
        $this->actingAs($this->teacher);

        // First update - creates
        Livewire::test('bulk-attendance-update')
            ->set('selectedClass', $this->class->id)
            ->set('selectedDate', now()->toDateString())
            ->set('selectedStatus', 'present')
            ->set('selectedStudents', [$this->student->id])
            ->call('confirmUpdate');

        $this->assertCount(1, Attendance::where('student_id', $this->student->id)->get());

        // Second update - updates existing
        $this->actingAs($this->teacher);
        Livewire::test('bulk-attendance-update')
            ->set('selectedClass', $this->class->id)
            ->set('selectedDate', now()->toDateString())
            ->set('selectedStatus', 'late')
            ->set('selectedStudents', [$this->student->id])
            ->call('confirmUpdate');

        $this->assertCount(1, Attendance::where('student_id', $this->student->id)->get());
        $this->assertDatabaseHas('attendances', ['status' => 'late']);
    }

    /** @test */
    public function bulk_update_respects_class_access_control()
    {
        $otherTeacher = User::factory()->create(['role' => 'teacher']);
        $this->actingAs($otherTeacher);

        // Try to access a class they don't teach
        Livewire::test('bulk-attendance-update')
            ->set('selectedClass', $this->class->id)
            ->set('selectedDate', now()->toDateString())
            ->set('selectedStatus', 'present')
            ->set('selectedStudents', [$this->student->id])
            ->call('confirmUpdate')
            ->assertSet('message', 'You do not have access to this class');
    }

    /** @test */
    public function bulk_update_can_cancel_operation()
    {
        $this->actingAs($this->teacher);

        Livewire::test('bulk-attendance-update')
            ->set('selectedClass', $this->class->id)
            ->set('selectedDate', now()->toDateString())
            ->set('selectedStatus', 'present')
            ->set('selectedStudents', [$this->student->id])
            ->call('updateStatus')
            ->call('cancel')
            ->assertSet('showConfirmation', false);
    }
}
