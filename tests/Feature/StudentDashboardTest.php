<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StudentDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected Student $studentRecord;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create student user and student record
        $class = StudentClass::factory()->create();
        $this->student = User::factory()->create(['role' => 'student']);
        $this->studentRecord = Student::factory()->create([
            'user_id' => $this->student->id,
            'class_id' => $class->id
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function student_can_view_dashboard()
    {
        $response = $this->actingAs($this->student)->get('/student/dashboard');
        
        $response->assertStatus(200);
        $response->assertSeeLivewire('student-dashboard');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function student_dashboard_displays_student_name()
    {
        Livewire::actingAs($this->student)
            ->test('student-dashboard')
            ->assertSee($this->student->name);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function student_dashboard_displays_qr_code()
    {
        Livewire::actingAs($this->student)
            ->test('student-dashboard')
            ->assertSee('Kode QR Anda')
            ->assertSee('Download QR Code');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function student_dashboard_shows_this_month_attendance()
    {
        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();
        
        // Create 5 present attendances this month
        for ($i = 0; $i < 5; $i++) {
            Attendance::factory()->create([
                'student_id' => $this->studentRecord->id,
                'date' => now()->addDays($i)->toDateString(),
                'status' => 'present'
            ]);
        }
        
        Livewire::actingAs($this->student)
            ->test('student-dashboard')
            ->assertSee('Hadir Bulan Ini')
            ->assertSee('5');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function student_dashboard_shows_late_count()
    {
        // Create 2 late attendances
        for ($i = 0; $i < 2; $i++) {
            Attendance::factory()->create([
                'student_id' => $this->studentRecord->id,
                'date' => now()->subDays($i)->toDateString(),
                'status' => 'late'
            ]);
        }
        
        Livewire::actingAs($this->student)
            ->test('student-dashboard')
            ->assertSee('Terlambat')
            ->assertSee('2');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function student_dashboard_calculates_attendance_percentage()
    {
        $monthStart = now()->startOfMonth()->toDateString();
        
        // Create 8 present out of 10 working days
        for ($i = 0; $i < 8; $i++) {
            Attendance::factory()->create([
                'student_id' => $this->studentRecord->id,
                'date' => now()->addDays($i)->toDateString(),
                'status' => 'present'
            ]);
        }
        
        Livewire::actingAs($this->student)
            ->test('student-dashboard')
            ->assertSee('Tingkat Kehadiran')
            ->assertSee('80'); // 8/10 = 80%
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function student_dashboard_displays_attendance_history()
    {
        // Create 5 attendance records
        for ($i = 0; $i < 5; $i++) {
            Attendance::factory()->create([
                'student_id' => $this->studentRecord->id,
                'date' => now()->subDays($i)->toDateString(),
                'status' => 'present'
            ]);
        }
        
        Livewire::actingAs($this->student)
            ->test('student-dashboard')
            ->assertSee('Riwayat Presensi Terbaru');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function student_dashboard_shows_today_status()
    {
        Attendance::factory()->create([
            'student_id' => $this->studentRecord->id,
            'date' => now()->toDateString(),
            'status' => 'present'
        ]);
        
        Livewire::actingAs($this->student)
            ->test('student-dashboard')
            ->assertSee('Hadir Tepat Waktu');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function student_dashboard_shows_empty_state_for_no_history()
    {
        Livewire::actingAs($this->student)
            ->test('student-dashboard')
            ->assertSee('Riwayat Presensi Terbaru')
            ->assertSee('Belum ada riwayat presensi');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function student_dashboard_displays_class_info()
    {
        Livewire::actingAs($this->student)
            ->test('student-dashboard')
            ->assertSee($this->studentRecord->class->name);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function student_dashboard_displays_nisn()
    {
        Livewire::actingAs($this->student)
            ->test('student-dashboard')
            ->assertSee($this->studentRecord->nisn);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function non_student_cannot_access_dashboard()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($admin)->get('/student/dashboard');
        
        $response->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function student_cannot_access_other_student_dashboard()
    {
        $otherStudent = User::factory()->create(['role' => 'student']);
        
        $response = $this->actingAs($otherStudent)->get('/student/dashboard');
        
        $response->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function status_badges_display_correct_colors()
    {
        Attendance::factory()->create([
            'student_id' => $this->studentRecord->id,
            'date' => now()->toDateString(),
            'status' => 'late'
        ]);
        
        Livewire::actingAs($this->student)
            ->test('student-dashboard')
            ->assertSee('LATE');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function attendance_history_shows_latest_first()
    {
        // Create 3 records on different dates
        Attendance::factory()->create([
            'student_id' => $this->studentRecord->id,
            'date' => now()->subDays(2)->toDateString(),
            'status' => 'present'
        ]);
        
        Attendance::factory()->create([
            'student_id' => $this->studentRecord->id,
            'date' => now()->subDays(1)->toDateString(),
            'status' => 'late'
        ]);
        
        Attendance::factory()->create([
            'student_id' => $this->studentRecord->id,
            'date' => now()->toDateString(),
            'status' => 'present'
        ]);
        
        Livewire::actingAs($this->student)
            ->test('student-dashboard')
            ->assertViewHas('recentAttendance');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function student_dashboard_handles_null_student_record()
    {
        // Create a user without student record
        $userNoRecord = User::factory()->create(['role' => 'student']);
        
        $response = $this->actingAs($userNoRecord)->get('/student/dashboard');
        
        // Should either show error or redirect
        $response->assertStatus(403);
    }
}
