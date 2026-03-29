<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TeacherDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $teacher;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->teacher = User::factory()->create(['role' => 'teacher']);
    }

    /** @test */
    public function teacher_can_view_dashboard()
    {
        $response = $this->actingAs($this->teacher)->get('/teacher/dashboard');
        
        $response->assertStatus(200);
        $response->assertSeeLivewire('teacher-dashboard');
    }

    /** @test */
    public function teacher_dashboard_displays_assigned_classes()
    {
        $class1 = StudentClass::factory()->create();
        $class2 = StudentClass::factory()->create();
        
        // Assign classes to teacher
        $this->teacher->assignedClasses()->attach([$class1->id, $class2->id]);
        
        Livewire::actingAs($this->teacher)
            ->test('teacher-dashboard')
            ->assertSee($class1->name)
            ->assertSee($class2->name);
    }

    /** @test */
    public function teacher_dashboard_shows_only_assigned_classes()
    {
        $assignedClass = StudentClass::factory()->create();
        $unassignedClass = StudentClass::factory()->create();
        
        // Only assign one class
        $this->teacher->assignedClasses()->attach($assignedClass->id);
        
        Livewire::actingAs($this->teacher)
            ->test('teacher-dashboard')
            ->assertSee($assignedClass->name)
            ->assertDontSee($unassignedClass->name);
    }

    /** @test */
    public function teacher_dashboard_displays_todays_attendance_stats()
    {
        $class = StudentClass::factory()->create();
        $students = Student::factory(10)->create(['class_id' => $class->id]);
        
        $this->teacher->assignedClasses()->attach($class->id);
        
        // Create attendance for 6 present, 2 late, 2 absent
        for ($i = 0; $i < 6; $i++) {
            Attendance::factory()->create([
                'student_id' => $students[$i]->id,
                'date' => now()->toDateString(),
                'status' => 'present'
            ]);
        }
        
        for ($i = 6; $i < 8; $i++) {
            Attendance::factory()->create([
                'student_id' => $students[$i]->id,
                'date' => now()->toDateString(),
                'status' => 'late'
            ]);
        }
        
        for ($i = 8; $i < 10; $i++) {
            Attendance::factory()->create([
                'student_id' => $students[$i]->id,
                'date' => now()->toDateString(),
                'status' => 'absent'
            ]);
        }
        
        Livewire::actingAs($this->teacher)
            ->test('teacher-dashboard')
            ->assertSee('Hadir')
            ->assertSee('Terlambat')
            ->assertSee('Alpa')
            ->assertSee('6')
            ->assertSee('2');
    }

    /** @test */
    public function teacher_dashboard_shows_student_count_per_class()
    {
        $class1 = StudentClass::factory()->create();
        $class2 = StudentClass::factory()->create();
        
        Student::factory(15)->create(['class_id' => $class1->id]);
        Student::factory(20)->create(['class_id' => $class2->id]);
        
        $this->teacher->assignedClasses()->attach([$class1->id, $class2->id]);
        
        Livewire::actingAs($this->teacher)
            ->test('teacher-dashboard')
            ->assertSee('15')
            ->assertSee('20');
    }

    /** @test */
    public function teacher_dashboard_only_counts_assigned_classes_attendance()
    {
        $assignedClass = StudentClass::factory()->create();
        $unassignedClass = StudentClass::factory()->create();
        
        $assignedStudent = Student::factory()->create(['class_id' => $assignedClass->id]);
        $unassignedStudent = Student::factory()->create(['class_id' => $unassignedClass->id]);
        
        $this->teacher->assignedClasses()->attach($assignedClass->id);
        
        // Create attendance for both
        Attendance::factory()->create([
            'student_id' => $assignedStudent->id,
            'date' => now()->toDateString(),
            'status' => 'present'
        ]);
        
        Attendance::factory()->create([
            'student_id' => $unassignedStudent->id,
            'date' => now()->toDateString(),
            'status' => 'present'
        ]);
        
        Livewire::actingAs($this->teacher)
            ->test('teacher-dashboard')
            ->assertSee('1'); // Should only see 1 from assigned class
    }

    /** @test */
    public function teacher_dashboard_displays_attendance_percentage()
    {
        $class = StudentClass::factory()->create();
        $students = Student::factory(10)->create(['class_id' => $class->id]);
        
        $this->teacher->assignedClasses()->attach($class->id);
        
        // 8 present out of 10
        for ($i = 0; $i < 8; $i++) {
            Attendance::factory()->create([
                'student_id' => $students[$i]->id,
                'date' => now()->toDateString(),
                'status' => 'present'
            ]);
        }
        
        Livewire::actingAs($this->teacher)
            ->test('teacher-dashboard')
            ->assertSee('80'); // 8/10 = 80%
    }

    /** @test */
    public function teacher_without_assigned_classes_sees_empty_state()
    {
        Livewire::actingAs($this->teacher)
            ->test('teacher-dashboard')
            ->assertSee('Jadwal Mengajar');
    }

    /** @test */
    public function non_teacher_cannot_access_dashboard()
    {
        $student = User::factory()->create(['role' => 'student']);
        
        $response = $this->actingAs($student)->get('/teacher/dashboard');
        
        $response->assertStatus(403);
    }

    /** @test */
    public function teacher_dashboard_shows_class_statistics()
    {
        $class1 = StudentClass::factory()->create();
        $class2 = StudentClass::factory()->create();
        
        Student::factory(12)->create(['class_id' => $class1->id]);
        Student::factory(15)->create(['class_id' => $class2->id]);
        
        $this->teacher->assignedClasses()->attach([$class1->id, $class2->id]);
        
        Livewire::actingAs($this->teacher)
            ->test('teacher-dashboard')
            ->assertSee($class1->name)
            ->assertSee('12')
            ->assertSee($class2->name)
            ->assertSee('15');
    }

    /** @test */
    public function teacher_dashboard_handles_empty_attendance()
    {
        $class = StudentClass::factory()->create();
        Student::factory(5)->create(['class_id' => $class->id]);
        
        $this->teacher->assignedClasses()->attach($class->id);
        
        Livewire::actingAs($this->teacher)
            ->test('teacher-dashboard')
            ->assertSee('0'); // No attendance yet
    }

    /** @test */
    public function teacher_dashboard_updates_with_new_attendance()
    {
        $class = StudentClass::factory()->create();
        $student = Student::factory()->create(['class_id' => $class->id]);
        
        $this->teacher->assignedClasses()->attach($class->id);
        
        $component = Livewire::actingAs($this->teacher)
            ->test('teacher-dashboard');
        
        // Create attendance
        Attendance::factory()->create([
            'student_id' => $student->id,
            'date' => now()->toDateString(),
            'status' => 'present'
        ]);
        
        // Refresh
        $component->refresh()->assertSee('1');
    }
}
