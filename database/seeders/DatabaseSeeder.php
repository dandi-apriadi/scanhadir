<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\Student;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $structure = app(SchoolTimetableSeeder::class)->seedStructure();
        $classes = $structure['classes'];

        // 1. Create shared demo login accounts shown on the login page.
        $this->call(DemoLoginSeeder::class);

        $schedules = \App\Models\Schedule::query()
            ->with(['class', 'subject'])
            ->orderBy('class_id')
            ->orderBy('day')
            ->orderBy('start_time')
            ->get();

        // 2. Create Students and Attendance
        $sequence = 2026000000001;
        $dates = collect(range(0, 10))->map(fn (int $offset) => Carbon::today()->subDays($offset)->toDateString());

        foreach ($classes as $class) {
            $studentUsers = User::factory()->count(5)->student()->withPassword('siswa123')->create();
            
            foreach ($studentUsers as $user) {
                $student = Student::factory()->create([
                    'user_id' => $user->id,
                    'class_id' => $class->id,
                    'nisn' => (string) $sequence++,
                ]);

                // Create attendance for this student in their class schedules
                $classSchedules = $schedules->where('class_id', $class->id);
                foreach ($classSchedules as $schedule) {
                    foreach ($dates as $date) {
                        Attendance::factory()->create([
                            'student_id' => $student->id,
                            'schedule_id' => $schedule->id,
                            'date' => $date,
                        ]);
                    }
                }
            }
        }

        // Specific Test Student
        $sampleStudentUser = User::query()->updateOrCreate(
            ['email' => 'rizki@sekolah.sch.id'],
            [
                'name' => 'Muhammad Rizki Pratama',
                'password' => bcrypt('siswa123'),
                'role' => 'student',
                'email_verified_at' => now(),
            ]
        );

        $sampleStudent = Student::query()->updateOrCreate(
            ['user_id' => $sampleStudentUser->id],
            [
                'class_id' => $classes->first()->id,
                'nisn' => '2026000999999',
            ]
        );

        // Add attendance for test student
        $classSchedules = $schedules->where('class_id', $sampleStudent->class_id);
        foreach ($classSchedules as $schedule) {
            foreach ($dates as $date) {
                Attendance::factory()->create([
                    'student_id' => $sampleStudent->id,
                    'schedule_id' => $schedule->id,
                    'date' => $date,
                ]);
            }
        }

        Holiday::factory()->count(5)->create();

        echo "\n✅ Database seeding completed successfully!\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "Admin: admin@scanhadir.com / admin123\n";
        echo "Guru: guru@scanhadir.com / guru123\n";
        echo "Siswa: siswa@scanhadir.com / siswa123\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    }
}
