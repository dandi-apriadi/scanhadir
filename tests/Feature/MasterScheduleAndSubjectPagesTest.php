<?php

namespace Tests\Feature;

use App\Models\Schedule;
use App\Models\StudentClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterScheduleAndSubjectPagesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_master_mapel_page_shows_real_subject_data()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $subject = Subject::factory()->create([
            'code' => 'MP-777',
            'name' => 'Algoritma Lanjut',
            'group' => 'Kejuruan',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.master.mapel'));

        $response->assertOk();
        $response->assertSee($subject->code);
        $response->assertSee($subject->name);
    }

    /** @test */
    public function admin_master_jadwal_page_shows_real_schedule_data()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->teacher()->create(['name' => 'Guru Jadwal Real']);
        $class = StudentClass::factory()->create(['name' => 'XI RPL REAL']);
        $subject = Subject::factory()->create(['name' => 'Basis Data Real']);

        $schedule = Schedule::factory()->create([
            'teacher_id' => $teacher->id,
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'day' => 'Senin',
            'room' => 'LAB-DB',
            'start_time' => '07:15:00',
            'end_time' => '08:45:00',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.master.jadwal'));

        $response->assertOk();
        $response->assertSee(strtoupper($schedule->day));
        $response->assertSee($teacher->name);
        $response->assertSee($class->name);
        $response->assertSee('LAB-DB');
    }
}
