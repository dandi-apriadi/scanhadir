<?php

namespace App\Providers;

use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\User;
use App\Policies\AttendancePolicy;
use App\Policies\HolidayPolicy;
use App\Policies\StudentClassPolicy;
use App\Policies\StudentPolicy;
use App\Policies\TeacherPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /**
         * Register Authorization Policies
         */
        Gate::policy(Student::class, StudentPolicy::class);
        Gate::policy(Attendance::class, AttendancePolicy::class);
        Gate::policy(Holiday::class, HolidayPolicy::class);
        Gate::policy(StudentClass::class, StudentClassPolicy::class);
        Gate::policy(User::class, TeacherPolicy::class);
    }
}
