<?php

namespace Tests\Unit;

use App\Models\Student;
use App\Models\StudentClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_generates_unique_qr_code_on_create(): void
    {
        $user = User::factory()->create();
        $class = StudentClass::factory()->create();

        $student1 = Student::factory()->create([
            'user_id' => $user->id,
            'class_id' => $class->id,
        ]);

        $student2 = Student::factory()->create([
            'class_id' => $class->id,
        ]);

        $this->assertNotNull($student1->qr_code);
        $this->assertNotNull($student2->qr_code);
        $this->assertNotEquals($student1->qr_code, $student2->qr_code);
        $this->assertTrue(str_starts_with($student1->qr_code, 'SH-'));
    }

    public function test_student_qr_code_is_set_if_not_provided(): void
    {
        $user = User::factory()->create();
        $class = StudentClass::factory()->create();

        $student = Student::factory()->create([
            'user_id' => $user->id,
            'class_id' => $class->id,
            'qr_code' => null,
        ]);

        $this->assertNotNull($student->qr_code);
        $this->assertTrue(str_starts_with($student->qr_code, 'SH-'));
    }

    public function test_student_qr_code_is_respected_if_provided(): void
    {
        $user = User::factory()->create();
        $class = StudentClass::factory()->create();

        $student = Student::factory()->create([
            'user_id' => $user->id,
            'class_id' => $class->id,
            'qr_code' => 'CUSTOM-QR-12345',
        ]);

        $this->assertEquals('CUSTOM-QR-12345', $student->qr_code);
    }

    public function test_student_has_relation_to_user(): void
    {
        $user = User::factory()->create();
        $class = StudentClass::factory()->create();
        $student = Student::factory()->create(['user_id' => $user->id, 'class_id' => $class->id]);

        $this->assertTrue($student->user()->exists());
        $this->assertEquals($user->id, $student->user->id);
    }

    public function test_student_has_relation_to_class(): void
    {
        $user = User::factory()->create();
        $class = StudentClass::factory()->create();
        $student = Student::factory()->create(['user_id' => $user->id, 'class_id' => $class->id]);

        $this->assertTrue($student->class()->exists());
        $this->assertEquals($class->id, $student->class->id);
    }

    public function test_student_has_many_attendances(): void
    {
        $user = User::factory()->create();
        $class = StudentClass::factory()->create();
        $student = Student::factory()->create(['user_id' => $user->id, 'class_id' => $class->id]);

        $student->attendances()->createMany([
            ['date' => now()->toDateString(), 'status' => 'present'],
            ['date' => now()->subDay()->toDateString(), 'status' => 'late'],
        ]);

        $this->assertCount(2, $student->attendances);
    }
}
