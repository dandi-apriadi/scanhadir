<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders(): void
    {
        $response = $this->get('/auth/login');
        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    public function test_admin_can_login(): void
    {
        $admin = User::factory()->admin()->withPassword('admin123')->create([
            'email' => 'admin@test.com',
        ]);

        $response = $this->post('/auth/login', [
            'email' => 'admin@test.com',
            'password' => 'admin123',
            'role' => 'admin',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($admin);
    }

    public function test_teacher_can_login(): void
    {
        $teacher = User::factory()->teacher()->withPassword('guru123')->create([
            'email' => 'guru@test.com',
        ]);

        $response = $this->post('/auth/login', [
            'email' => 'guru@test.com',
            'password' => 'guru123',
            'role' => 'teacher',
        ]);

        $response->assertRedirect('/teacher/dashboard');
        $this->assertAuthenticatedAs($teacher);
    }

    public function test_student_can_login(): void
    {
        $student = User::factory()->student()->withPassword('siswa123')->create([
            'email' => 'siswa@test.com',
        ]);

        $response = $this->post('/auth/login', [
            'email' => 'siswa@test.com',
            'password' => 'siswa123',
            'role' => 'student',
        ]);

        $response->assertRedirect('/student/dashboard');
        $this->assertAuthenticatedAs($student);
    }

    public function test_login_fails_with_invalid_password(): void
    {
        User::factory()->admin()->withPassword('admin123')->create([
            'email' => 'admin@test.com',
        ]);

        $response = $this->post('/auth/login', [
            'email' => 'admin@test.com',
            'password' => 'wrongpassword',
            'role' => 'admin',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_fails_with_mismatched_role(): void
    {
        User::factory()->admin()->withPassword('admin123')->create([
            'email' => 'admin@test.com',
        ]);

        $response = $this->post('/auth/login', [
            'email' => 'admin@test.com',
            'password' => 'admin123',
            'role' => 'student', // Wrong role
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_fails_with_missing_fields(): void
    {
        $response = $this->post('/auth/login', [
            'email' => '',
            'password' => '',
            'role' => '',
        ]);

        $response->assertSessionHasErrors(['email', 'password', 'role']);
    }

    public function test_can_logout(): void
    {
        $user = User::factory()->admin()->create();
        
        $this->actingAs($user);
        
        $response = $this->post('/auth/logout');
        
        $response->assertRedirect('/auth/login');
        $this->assertGuest();
    }
}
