<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\StudentClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataPagesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_master_guru_page_shows_real_teacher_data()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'name' => 'Budi Real Data',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.master.guru'));

        $response->assertOk();
        $response->assertSee($teacher->name);
    }

    /** @test */
    public function admin_master_siswa_page_shows_real_student_data()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $class = StudentClass::factory()->create(['name' => 'XII RPL REAL']);
        $studentUser = User::factory()->create([
            'role' => 'student',
            'name' => 'Siswa Real Data',
        ]);
        $student = Student::factory()->create([
            'user_id' => $studentUser->id,
            'class_id' => $class->id,
            'nisn' => '9988776655',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.master.siswa'));

        $response->assertOk();
        $response->assertSee($studentUser->name);
        $response->assertSee($student->nisn);
        $response->assertSee($class->name);
    }

    /** @test */
    public function admin_master_kelas_page_shows_real_class_data()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $class = StudentClass::factory()->create(['name' => 'XI TKJ REAL', 'level' => 'XI']);

        $response = $this->actingAs($admin)->get(route('admin.master.kelas'));

        $response->assertOk();
        $response->assertSee($class->name);
    }
}
