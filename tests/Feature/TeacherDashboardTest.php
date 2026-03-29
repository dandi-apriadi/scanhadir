<?php

namespace Tests\Feature;

use App\Livewire\TeacherDashboard;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TeacherDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_dashboard_shows_only_assigned_classes(): void
    {
        $teacher = User::factory()->teacher()->create();

        $assignedClass = StudentClass::factory()->create(['name' => 'X-RPL-1']);
        $otherClass = StudentClass::factory()->create(['name' => 'XI-TKJ-1']);

        $teacher->assignedClasses()->attach($assignedClass->id);

        $assignedStudentUser = User::factory()->student()->create();
        $assignedStudent = Student::factory()->create([
            'user_id' => $assignedStudentUser->id,
            'class_id' => $assignedClass->id,
        ]);

        $otherStudentUser = User::factory()->student()->create();
        $otherStudent = Student::factory()->create([
            'user_id' => $otherStudentUser->id,
            'class_id' => $otherClass->id,
        ]);

        Attendance::factory()->create([
            'student_id' => $assignedStudent->id,
            'date' => now()->toDateString(),
            'status' => 'present',
        ]);

        Attendance::factory()->create([
            'student_id' => $otherStudent->id,
            'date' => now()->toDateString(),
            'status' => 'present',
        ]);

        Livewire::actingAs($teacher)
            ->test(TeacherDashboard::class)
            ->assertSee($assignedClass->name)
            ->assertDontSee($otherClass->name);
    }

    public function test_teacher_dashboard_shows_empty_state_when_no_class_assignment(): void
    {
        $teacher = User::factory()->teacher()->create();

        Livewire::actingAs($teacher)
            ->test(TeacherDashboard::class)
            ->assertSee('Belum ada kelas yang ditugaskan.');
    }
}
