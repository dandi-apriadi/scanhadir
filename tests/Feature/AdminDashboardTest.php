<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\Attendance;
use App\Models\Holiday;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    /** @test */
    public function admin_can_view_dashboard()
    {
        $response = $this->actingAs($this->admin)->get('/admin/dashboard');
        
        $response->assertStatus(200);
        $response->assertSeeText('Dashboard Admin');
        $response->assertSeeLivewire('admin-dashboard');
    }

    /** @test */
    public function admin_dashboard_displays_total_students()
    {
        $class = StudentClass::factory()->create();
        Student::factory(5)->create(['class_id' => $class->id]);
        
        Livewire::actingAs($this->admin)
            ->test('admin-dashboard')
            ->assertSee('Total Siswa')
            ->assertSee('5');
    }

    /** @test */
    public function admin_dashboard_shows_todays_attendance_stats()
    {
        $class = StudentClass::factory()->create();
        $students = Student::factory(10)->create(['class_id' => $class->id]);
        
        // Create attendance records for today
        foreach ($students->slice(0, 5) as $student) {
            Attendance::factory()->create([
                'student_id' => $student->id,
                'date' => now()->toDateString(),
                'status' => 'Hadir',
                'check_in' => now()->setHour(7)
            ]);
        }
        
        foreach ($students->slice(5, 3) as $student) {
            Attendance::factory()->create([
                'student_id' => $student->id,
                'date' => now()->toDateString(),
                'status' => 'Telat',
                'check_in' => now()->setHour(8)
            ]);
        }
        
        Livewire::actingAs($this->admin)
            ->test('admin-dashboard')
            ->assertSee('Hadir')
            ->assertSee('Terlambat')
            ->assertSee('5')
            ->assertSee('3');
    }

    /** @test */
    public function admin_dashboard_displays_class_statistics()
    {
        $class1 = StudentClass::factory()->create();
        $class2 = StudentClass::factory()->create();
        
        Student::factory(15)->create(['class_id' => $class1->id]);
        Student::factory(20)->create(['class_id' => $class2->id]);
        
        Livewire::actingAs($this->admin)
            ->test('admin-dashboard')
            ->assertSee($class1->name)
            ->assertSee($class2->name)
            ->assertSee('15')
            ->assertSee('20');
    }

    /** @test */
    public function admin_can_filter_by_date()
    {
        $class = StudentClass::factory()->create();
        $student = Student::factory()->create(['class_id' => $class->id]);
        
        Attendance::factory()->create([
            'student_id' => $student->id,
            'date' => now()->subDays(5)->toDateString(),
            'status' => 'present'
        ]);
        
        Livewire::actingAs($this->admin)
            ->test('admin-dashboard')
            ->set('selectedDate', now()->subDays(5)->toDateString())
            ->assertSee('1'); // Should filter to 1 record
    }

    /** @test */
    public function admin_can_filter_by_class()
    {
        $class1 = StudentClass::factory()->create();
        $class2 = StudentClass::factory()->create();
        
        $student1 = Student::factory()->create(['class_id' => $class1->id]);
        Student::factory()->create(['class_id' => $class2->id]);
        
        Attendance::factory()->create([
            'student_id' => $student1->id,
            'date' => now()->toDateString(),
            'status' => 'present'
        ]);
        
        Livewire::actingAs($this->admin)
            ->test('admin-dashboard')
            ->set('selectedClass', $class1->id)
            ->assertSee($class1->name);
    }

    /** @test */
    public function non_admin_cannot_view_dashboard()
    {
        $student = User::factory()->create(['role' => 'student']);
        
        $response = $this->actingAs($student)->get('/admin/dashboard');
        
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_dashboard_handles_empty_data()
    {
        Livewire::actingAs($this->admin)
            ->test('admin-dashboard')
            ->assertSee('Dashboard Admin') // Should render without error
            ->assertDontSee(null);
    }

    /** @test */
    public function admin_dashboard_updates_when_attendance_added()
    {
        $class = StudentClass::factory()->create();
        $student = Student::factory()->create(['class_id' => $class->id]);
        
        $component = Livewire::actingAs($this->admin)
            ->test('admin-dashboard');
        
        // Create attendance
        Attendance::factory()->create([
            'student_id' => $student->id,
            'date' => now()->toDateString(),
            'status' => 'present'
        ]);
        
        // Refresh component
        $component->refresh()->assertSee('1');
    }
}
