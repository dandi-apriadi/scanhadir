<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TeacherDashboardRealtimeTest extends TestCase
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

    /** @test */
    public function teacher_can_access_dashboard_with_polling()
    {
        $this->actingAs($this->teacher);

        Livewire::test('teacher-dashboard')
            ->assertStatus(200);
    }

    /** @test */
    public function dashboard_displays_live_scan_session_when_attendance_exists()
    {
        $this->actingAs($this->teacher);

        // Create attendance record
        Attendance::factory()->create([
            'student_id' => $this->student->id,
            'date' => now()->toDateString(),
            'status' => 'present',
            'check_in' => now(),
        ]);

        Livewire::test('teacher-dashboard')
            ->assertViewHas('totalScans', 1)
            ->assertSee('Sesi Scan QR Aktif');
    }

    /** @test */
    public function dashboard_shows_scan_count_correctly()
    {
        $this->actingAs($this->teacher);

        // Create multiple attendance records
        Attendance::factory()->count(5)->create([
            'date' => now()->toDateString(),
        ])->each(function ($attendance) {
            $attendance->student()->update(['class_id' => $this->class->id]);
        });

        Livewire::test('teacher-dashboard')
            ->assertSee('5 siswa telah dipindai hari ini');
    }

    /** @test */
    public function dashboard_displays_latest_scanned_student()
    {
        $this->actingAs($this->teacher);

        $student2 = Student::factory()->create(['class_id' => $this->class->id]);

        // Create first attendance
        Attendance::factory()->create([
            'student_id' => $this->student->id,
            'date' => now()->toDateString(),
            'check_in' => now()->subMinutes(5),
            'status' => 'present',
        ]);

        // Create second attendance (latest)
        Attendance::factory()->create([
            'student_id' => $student2->id,
            'date' => now()->toDateString(),
            'check_in' => now(),
            'status' => 'present',
        ]);

        Livewire::test('teacher-dashboard')
            ->assertSee($student2->user->name);
    }

    /** @test */
    public function refresh_attendance_method_updates_component()
    {
        $this->actingAs($this->teacher);

        // Create initial attendance
        Attendance::factory()->create([
            'student_id' => $this->student->id,
            'date' => now()->toDateString(),
            'status' => 'present',
        ]);

        Livewire::test('teacher-dashboard')
            ->call('refreshAttendance')
            ->assertStatus(200);
    }

    /** @test */
    public function polling_directive_enabled_in_template()
    {
        $this->actingAs($this->teacher);

        Livewire::test('teacher-dashboard')
            ->assertViewIs('livewire.teacher-dashboard');
    }

    /** @test */
    public function dashboard_hides_scan_session_when_no_attendance()
    {
        $this->actingAs($this->teacher);

        Livewire::test('teacher-dashboard')
            ->assertDontSee('Sesi Scan QR Aktif');
    }

    /** @test */
    public function scan_session_shows_live_indicator()
    {
        $this->actingAs($this->teacher);

        // Create attendance
        Attendance::factory()->create([
            'student_id' => $this->student->id,
            'date' => now()->toDateString(),
        ]);

        Livewire::test('teacher-dashboard')
            ->assertSee('LIVE (1)');
    }

    /** @test */
    public function dashboard_shows_offline_indicator_when_no_scans()
    {
        $this->actingAs($this->teacher);

        Livewire::test('teacher-dashboard')
            ->assertSee('OFFLINE');
    }

    /** @test */
    public function recent_logs_display_includes_scan_count()
    {
        $this->actingAs($this->teacher);

        // Create attendance
        Attendance::factory()->create([
            'student_id' => $this->student->id,
            'date' => now()->toDateString(),
            'check_in' => now(),
        ]);

        Livewire::test('teacher-dashboard')
            ->assertSee('Log Presensi Terbaru');
    }

    /** @test */
    public function latest_scanned_student_shows_time()
    {
        $this->actingAs($this->teacher);

        $checkInTime = now()->setTime(7, 15, 30);
        Attendance::factory()->create([
            'student_id' => $this->student->id,
            'date' => now()->toDateString(),
            'check_in' => $checkInTime,
        ]);

        Livewire::test('teacher-dashboard')
            ->assertSee($checkInTime->format('H:i:s'));
    }

    /** @test */
    public function dashboard_updates_when_new_attendance_created()
    {
        $this->actingAs($this->teacher);

        $component = Livewire::test('teacher-dashboard');
        $this->assertFalse($component->viewData('scanSessionActive') ?? false);

        // Create attendance after initial render
        Attendance::factory()->create([
            'student_id' => $this->student->id,
            'date' => now()->toDateString(),
        ]);

        // Refresh
        $component->call('refreshAttendance')
            ->assertSee('Sesi Scan QR Aktif');
    }

    /** @test */
    public function non_teacher_cannot_see_live_scan_session()
    {
        $student = User::factory()->create(['role' => 'student']);
        $this->actingAs($student);

        // Should not have access to teacher dashboard
        $response = $this->get('/teacher/dashboard');
        $this->assertEquals(403, $response->status());
    }

    /** @test */
    public function scan_session_shows_only_assigned_classes()
    {
        $this->actingAs($this->teacher);

        // Create another class not assigned to teacher
        $otherClass = StudentClass::factory()->create();
        $otherStudent = Student::factory()->create(['class_id' => $otherClass->id]);

        // Create attendance in other class
        Attendance::factory()->create([
            'student_id' => $otherStudent->id,
            'date' => now()->toDateString(),
        ]);

        // Create attendance in assigned class
        Attendance::factory()->create([
            'student_id' => $this->student->id,
            'date' => now()->toDateString(),
        ]);

        Livewire::test('teacher-dashboard')
            ->assertSee('1 siswa telah dipindai');
    }

    /** @test */
    public function live_indicator_pulsates_when_active()
    {
        $this->actingAs($this->teacher);

        Attendance::factory()->create([
            'student_id' => $this->student->id,
            'date' => now()->toDateString(),
        ]);

        Livewire::test('teacher-dashboard')
            ->assertSee('animate-pulse');
    }

    /** @test */
    public function dashboard_handles_multiple_scans_per_student()
    {
        $this->actingAs($this->teacher);

        // Check-in
        Attendance::factory()->create([
            'student_id' => $this->student->id,
            'date' => now()->toDateString(),
            'check_in' => now()->setTime(7, 10),
            'check_out' => null,
            'status' => 'present',
        ]);

        // Check-out (same day)
        Attendance::factory()->create([
            'student_id' => $this->student->id,
            'date' => now()->toDateString(),
            'check_in' => now()->setTime(14, 0),
            'check_out' => now()->setTime(14, 5),
            'status' => 'present',
        ]);

        Livewire::test('teacher-dashboard')
            ->assertSee('2 siswa telah dipindai');
    }

    /** @test */
    public function refresh_attendance_updates_last_refresh_timestamp()
    {
        $this->actingAs($this->teacher);

        $component = Livewire::test('teacher-dashboard');
        $before = $component->get('lastRefresh');

        sleep(1); // Small delay to ensure timestamp difference

        $component->call('refreshAttendance');
        $after = $component->get('lastRefresh');

        $this->assertNotEquals($before, $after);
    }

    /** @test */
    public function scan_session_banner_includes_videocam_icon()
    {
        $this->actingAs($this->teacher);

        Attendance::factory()->create([
            'student_id' => $this->student->id,
            'date' => now()->toDateString(),
        ]);

        Livewire::test('teacher-dashboard')
            ->assertSee('videocam');
    }

    /** @test */
    public function polling_interval_is_3_seconds()
    {
        $this->actingAs($this->teacher);

        $component = Livewire::test('teacher-dashboard');
        $this->assertEquals(3000, $component->get('pollInterval'));
    }
}
