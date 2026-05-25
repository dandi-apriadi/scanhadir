<?php

namespace Tests\Feature;

use App\Models\Schedule;
use App\Models\StudentClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterCrudAndScheduleConflictTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_create_and_delete_subject()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $createResponse = $this->actingAs($admin)->post(route('admin.master.mapel.store'), [
            'code' => 'MP-900',
            'name' => 'Pemrograman Lanjut',
            'group' => 'Kejuruan',
        ]);

        $createResponse->assertRedirect(route('admin.master.mapel'));
        $this->assertDatabaseHas('subjects', [
            'code' => 'MP-900',
            'name' => 'Pemrograman Lanjut',
        ]);

        $subject = Subject::query()->where('code', 'MP-900')->firstOrFail();

        $deleteResponse = $this->actingAs($admin)->delete(route('admin.master.mapel.destroy', $subject));

        $deleteResponse->assertRedirect(route('admin.master.mapel'));
        $this->assertDatabaseMissing('subjects', ['id' => $subject->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_cannot_create_schedule_when_teacher_or_class_conflicts()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->teacher()->create();
        $class = StudentClass::factory()->create();
        $subjectA = Subject::factory()->create();
        $subjectB = Subject::factory()->create();

        Schedule::factory()->create([
            'teacher_id' => $teacher->id,
            'class_id' => $class->id,
            'subject_id' => $subjectA->id,
            'day' => 'Senin',
            'start_time' => '07:00:00',
            'end_time' => '08:00:00',
        ]);

        $response = $this->actingAs($admin)
            ->from(route('admin.master.jadwal'))
            ->post(route('admin.master.jadwal.store'), [
                'teacher_id' => $teacher->id,
                'class_id' => $class->id,
                'subject_id' => $subjectB->id,
                'day' => 'Senin',
                'start_time' => '07:30',
                'end_time' => '08:30',
                'room' => 'R-1',
            ]);

        $response->assertRedirect(route('admin.master.jadwal'));
        $response->assertSessionHasErrors('schedule');
        $this->assertDatabaseCount('schedules', 1);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_update_subject()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $subject = Subject::factory()->create([
            'code' => 'MP-100',
            'name' => 'Original Name',
            'group' => 'Kejuruan',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.master.mapel.update', $subject), [
            'code' => 'MP-100',
            'name' => 'Updated Name',
            'group' => 'Umum',
        ]);

        $response->assertRedirect(route('admin.master.mapel'));
        $this->assertDatabaseHas('subjects', [
            'id' => $subject->id,
            'code' => 'MP-100',
            'name' => 'Updated Name',
            'group' => 'Umum',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_update_schedule()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->teacher()->create();
        $class = StudentClass::factory()->create();
        $subjectA = Subject::factory()->create();
        $subjectB = Subject::factory()->create();

        $schedule = Schedule::factory()->create([
            'teacher_id' => $teacher->id,
            'class_id' => $class->id,
            'subject_id' => $subjectA->id,
            'day' => 'Senin',
            'start_time' => '07:00:00',
            'end_time' => '08:00:00',
            'room' => 'R-101',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.master.jadwal.update', $schedule), [
            'teacher_id' => $teacher->id,
            'class_id' => $class->id,
            'subject_id' => $subjectB->id,
            'day' => 'Selasa',
            'start_time' => '09:00',
            'end_time' => '10:00',
            'room' => 'R-204',
        ]);

        $response->assertRedirect(route('admin.master.jadwal'));
        $this->assertDatabaseHas('schedules', [
            'id' => $schedule->id,
            'subject_id' => $subjectB->id,
            'day' => 'Selasa',
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'room' => 'R-204',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_cannot_update_schedule_with_conflict()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->teacher()->create();
        $class = StudentClass::factory()->create();
        $subjectA = Subject::factory()->create();
        $subjectB = Subject::factory()->create();

        Schedule::factory()->create([
            'teacher_id' => $teacher->id,
            'class_id' => $class->id,
            'subject_id' => $subjectA->id,
            'day' => 'Senin',
            'start_time' => '07:00:00',
            'end_time' => '08:00:00',
        ]);

        $schedule = Schedule::factory()->create([
            'teacher_id' => $teacher->id,
            'class_id' => $class->id,
            'subject_id' => $subjectB->id,
            'day' => 'Senin',
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
        ]);

        $response = $this->actingAs($admin)
            ->from(route('admin.master.jadwal'))
            ->put(route('admin.master.jadwal.update', $schedule), [
                'teacher_id' => $teacher->id,
                'class_id' => $class->id,
                'subject_id' => $subjectB->id,
                'day' => 'Senin',
                'start_time' => '07:30',
                'end_time' => '08:30',
                'room' => '',
            ]);

        $response->assertRedirect(route('admin.master.jadwal'));
        $response->assertSessionHasErrors('schedule');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function schedule_validation_requires_teacher_role()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $nonTeacher = User::factory()->create(['role' => 'student']);
        $class = StudentClass::factory()->create();
        $subject = Subject::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.master.jadwal.store'), [
            'teacher_id' => $nonTeacher->id,
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'day' => 'Senin',
            'start_time' => '09:00',
            'end_time' => '10:00',
            'room' => 'R-1',
        ]);

        $response->assertSessionHasErrors('teacher_id');
    }
}
