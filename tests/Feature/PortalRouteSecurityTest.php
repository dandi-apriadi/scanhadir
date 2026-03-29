<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalRouteSecurityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function unauthenticated_user_is_blocked_from_student_dashboard()
    {
        $this->get('/student/dashboard')
            ->assertForbidden();
    }

    /** @test */
    public function unauthenticated_user_is_blocked_from_admin_dashboard()
    {
        $this->get('/admin/dashboard')
            ->assertForbidden();
    }

    /** @test */
    public function unauthenticated_user_is_blocked_from_teacher_dashboard()
    {
        $this->get('/teacher/dashboard')
            ->assertForbidden();
    }
}
