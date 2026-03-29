<?php

namespace Tests\Feature\Policies;

use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationPoliciesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_all_resources(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create(['role' => 'teacher']);
        $class = StudentClass::create(['name' => 'X-RPL-1', 'level' => 'X', 'major' => 'RPL']);
        $student = Student::create([
            'user_id' => $teacher->id,
            'class_id' => $class->id,
            'nisn' => '1234567890',
            'qr_code' => 'SH-TEST123',
        ]);
        $attendance = Attendance::create([
            'student_id' => $student->id,
            'date' => now()->toDateString(),
            'status' => 'present',
        ]);
        $holiday = Holiday::create([
            'name' => 'Test Holiday',
            'start_date' => now()->toDateString(),
            'end_date' => now()->toDateString(),
            'type' => 'national',
        ]);

        $this->assertTrue($admin->can('viewAny', Student::class));
        $this->assertTrue($admin->can('create', Student::class));
        $this->assertTrue($admin->can('update', $student));
        $this->assertTrue($admin->can('delete', $student));

        $this->assertTrue($admin->can('viewAny', Attendance::class));
        $this->assertTrue($admin->can('create', Attendance::class));
        $this->assertTrue($admin->can('update', $attendance));
        $this->assertTrue($admin->can('delete', $attendance));

        $this->assertTrue($admin->can('viewAny', Holiday::class));
        $this->assertTrue($admin->can('create', Holiday::class));
        $this->assertTrue($admin->can('update', $holiday));
        $this->assertTrue($admin->can('delete', $holiday));

        $this->assertTrue($admin->can('viewAny', StudentClass::class));
        $this->assertTrue($admin->can('create', StudentClass::class));
        $this->assertTrue($admin->can('update', $class));
        $this->assertTrue($admin->can('delete', $class));
    }

    public function test_teacher_has_limited_access(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $otherUser = User::factory()->create(['role' => 'student']);
        $class = StudentClass::create(['name' => 'XI-RPL-2', 'level' => 'XI', 'major' => 'RPL']);
        $student = Student::create([
            'user_id' => $otherUser->id,
            'class_id' => $class->id,
            'nisn' => '2234567890',
            'qr_code' => 'SH-TEST456',
        ]);
        $attendance = Attendance::create([
            'student_id' => $student->id,
            'date' => now()->toDateString(),
            'status' => 'present',
        ]);
        $holiday = Holiday::create([
            'name' => 'Holiday Teacher Test',
            'start_date' => now()->toDateString(),
            'end_date' => now()->toDateString(),
            'type' => 'school',
        ]);

        $this->assertTrue($teacher->can('viewAny', Student::class));
        $this->assertTrue($teacher->can('create', Student::class));
        $this->assertTrue($teacher->can('update', $student));
        $this->assertFalse($teacher->can('delete', $student));

        $this->assertTrue($teacher->can('viewAny', Attendance::class));
        $this->assertTrue($teacher->can('create', Attendance::class));
        $this->assertTrue($teacher->can('update', $attendance));
        $this->assertFalse($teacher->can('delete', $attendance));

        $this->assertTrue($teacher->can('viewAny', Holiday::class));
        $this->assertFalse($teacher->can('create', Holiday::class));
        $this->assertFalse($teacher->can('update', $holiday));
        $this->assertFalse($teacher->can('delete', $holiday));

        $this->assertTrue($teacher->can('viewAny', StudentClass::class));
        $this->assertFalse($teacher->can('create', StudentClass::class));
        $this->assertFalse($teacher->can('update', $class));
        $this->assertFalse($teacher->can('delete', $class));
    }
}
