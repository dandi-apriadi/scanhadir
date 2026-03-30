<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterGuruSiswaKelasCrudTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_create_update_and_delete_teacher(): void
    {
        $admin = User::factory()->admin()->create();

        $create = $this->actingAs($admin)->post(route('admin.master.guru.store'), [
            'name' => 'Guru Test',
            'email' => 'guru.test@example.com',
            'password' => 'password123',
        ]);

        $create->assertRedirect(route('admin.master.guru'));

        $teacher = User::query()->where('email', 'guru.test@example.com')->firstOrFail();

        $update = $this->actingAs($admin)->put(route('admin.master.guru.update', $teacher), [
            'name' => 'Guru Test Updated',
            'email' => 'guru.test@example.com',
            'password' => '',
        ]);

        $update->assertRedirect(route('admin.master.guru'));
        $this->assertDatabaseHas('users', [
            'id' => $teacher->id,
            'name' => 'Guru Test Updated',
            'role' => 'teacher',
        ]);

        $delete = $this->actingAs($admin)->delete(route('admin.master.guru.destroy', $teacher));

        $delete->assertRedirect(route('admin.master.guru'));
        $this->assertDatabaseMissing('users', ['id' => $teacher->id]);
    }

    /** @test */
    public function admin_cannot_delete_teacher_when_still_attached_to_class(): void
    {
        $admin = User::factory()->admin()->create();
        $teacher = User::factory()->teacher()->create();
        $class = StudentClass::factory()->create();

        $teacher->assignedClasses()->attach($class->id);

        $response = $this->actingAs($admin)->delete(route('admin.master.guru.destroy', $teacher));

        $response->assertRedirect(route('admin.master.guru'));
        $response->assertSessionHasErrors('guru');
        $this->assertDatabaseHas('users', ['id' => $teacher->id]);
    }

    /** @test */
    public function admin_can_create_update_and_delete_student_with_linked_user(): void
    {
        $admin = User::factory()->admin()->create();
        $classA = StudentClass::factory()->create(['name' => 'X-RPL-1']);
        $classB = StudentClass::factory()->create(['name' => 'X-RPL-2']);

        $create = $this->actingAs($admin)->post(route('admin.master.siswa.store'), [
            'name' => 'Siswa Test',
            'email' => 'siswa.test@example.com',
            'password' => 'password123',
            'nisn' => '1234567890123',
            'class_id' => $classA->id,
        ]);

        $create->assertRedirect(route('admin.master.siswa'));

        $student = Student::query()->where('nisn', '1234567890123')->firstOrFail();

        $update = $this->actingAs($admin)->put(route('admin.master.siswa.update', $student), [
            'name' => 'Siswa Test Updated',
            'email' => 'siswa.test@example.com',
            'password' => '',
            'nisn' => '1234567890123',
            'class_id' => $classB->id,
        ]);

        $update->assertRedirect(route('admin.master.siswa'));

        $student->refresh();
        $this->assertSame($classB->id, $student->class_id);
        $this->assertDatabaseHas('users', [
            'id' => $student->user_id,
            'name' => 'Siswa Test Updated',
            'role' => 'student',
        ]);

        $delete = $this->actingAs($admin)->delete(route('admin.master.siswa.destroy', $student));

        $delete->assertRedirect(route('admin.master.siswa'));
        $this->assertDatabaseMissing('students', ['id' => $student->id]);
        $this->assertDatabaseMissing('users', ['id' => $student->user_id]);
    }

    /** @test */
    public function admin_cannot_delete_student_when_has_attendance_history(): void
    {
        $admin = User::factory()->admin()->create();
        $student = Student::factory()->create();

        Attendance::factory()->create([
            'student_id' => $student->id,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.master.siswa.destroy', $student));

        $response->assertRedirect(route('admin.master.siswa'));
        $response->assertSessionHasErrors('siswa');
        $this->assertDatabaseHas('students', ['id' => $student->id]);
    }

    /** @test */
    public function admin_can_create_update_and_delete_class_and_export_students_excel(): void
    {
        $admin = User::factory()->admin()->create();

        $create = $this->actingAs($admin)->post(route('admin.master.kelas.store'), [
            'name' => 'XI-TKJ-9',
            'level' => 'XI',
            'major' => 'TKJ',
        ]);

        $create->assertRedirect(route('admin.master.kelas'));

        $class = StudentClass::query()->where('name', 'XI-TKJ-9')->firstOrFail();

        $update = $this->actingAs($admin)->put(route('admin.master.kelas.update', $class), [
            'name' => 'XI-TKJ-10',
            'level' => 'XI',
            'major' => 'TKJ',
        ]);

        $update->assertRedirect(route('admin.master.kelas'));
        $this->assertDatabaseHas('classes', [
            'id' => $class->id,
            'name' => 'XI-TKJ-10',
        ]);

        $studentUser = User::factory()->student()->create([
            'name' => 'Export Siswa',
            'email' => 'export.siswa@example.com',
        ]);

        Student::factory()->create([
            'user_id' => $studentUser->id,
            'class_id' => $class->id,
            'nisn' => '3344556677889',
            'qr_code' => 'SH-EXPORT01',
        ]);

        $export = $this->actingAs($admin)->get(route('admin.master.siswa.export', [
            'class_id' => $class->id,
            'has_qr' => '1',
            'sort' => 'name_asc',
        ]));

        $export->assertOk();
        $export->assertHeader('content-type', 'application/vnd.ms-excel; charset=UTF-8');

        $delete = $this->actingAs($admin)->delete(route('admin.master.kelas.destroy', $class));

        $delete->assertRedirect(route('admin.master.kelas'));
        $delete->assertSessionHasErrors('kelas');

        Student::query()->where('class_id', $class->id)->delete();

        $deleteAfterCleanup = $this->actingAs($admin)->delete(route('admin.master.kelas.destroy', $class));

        $deleteAfterCleanup->assertRedirect(route('admin.master.kelas'));
        $this->assertDatabaseMissing('classes', ['id' => $class->id]);
    }
}
