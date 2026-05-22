<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Active Semester
        $semester = \App\Models\SemesterAkademik::factory()->create([
            'nama_semester' => 'Ganjil',
            'tahun_ajaran' => '2025/2026',
            'is_active' => true,
        ]);

        // 2. Create Subjects
        $subjects = \App\Models\Subject::factory()->count(5)->create([
            'semester_akademik_id' => $semester->id,
        ]);

        // 3. Create Classes
        $classesData = [
            ['name' => 'X-A', 'level' => 'X', 'major' => 'RPL'],
            ['name' => 'X-B', 'level' => 'X', 'major' => 'TKJ'],
            ['name' => 'XI-A', 'level' => 'XI', 'major' => 'RPL'],
            ['name' => 'XI-B', 'level' => 'XI', 'major' => 'TKJ'],
            ['name' => 'XII-A', 'level' => 'XII', 'major' => 'RPL'],
        ];

        $classes = collect($classesData)->map(function (array $classData) {
            return StudentClass::query()->firstOrCreate(['name' => $classData['name']], $classData);
        });

        // 4. Create Users
        // Admin
        User::query()->updateOrCreate(
            ['email' => 'admin@scanhadir.com'],
            [
                'name' => 'Admin ScanHadir',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Teachers
        $teacher1 = User::query()->updateOrCreate(
            ['email' => 'guru1@sekolah.sch.id'],
            [
                'name' => 'Ibu Siti Nur Azizah, S.Pd',
                'password' => Hash::make('guru123'),
                'role' => 'teacher',
                'email_verified_at' => now(),
            ]
        );

        $teacher2 = User::query()->updateOrCreate(
            ['email' => 'guru@scanhadir.com'],
            [
                'name' => 'Bapak Budi Santoso, M.Pd',
                'password' => Hash::make('guru123'),
                'role' => 'teacher',
                'email_verified_at' => now(),
            ]
        );

        $teachers = collect([$teacher1, $teacher2]);

        // 5. Create Schedules
        $schedules = collect();
        foreach ($classes as $class) {
            foreach ($subjects->random(2) as $subject) {
                $schedules->push(\App\Models\Schedule::factory()->create([
                    'class_id' => $class->id,
                    'subject_id' => $subject->id,
                    'teacher_id' => $teachers->random()->id,
                    'semester_akademik_id' => $semester->id,
                ]));
            }
        }

        // 6. Create Students and Attendance
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
                'password' => Hash::make('siswa123'),
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
        echo "Teacher: guru1@sekolah.sch.id / guru123\n";
        echo "Student: rizki@sekolah.sch.id / siswa123\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    }
}

