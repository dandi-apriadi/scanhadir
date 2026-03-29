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
        $totalUsers = 188;
        $studentCount = 150;
        $adminCount = (int) round($totalUsers * 0.15);
        $teacherCount = $totalUsers - $studentCount - $adminCount;

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

        User::factory()->count($adminCount)->admin()->withPassword('admin123')->create();
        $teachers = User::factory()->count($teacherCount)->teacher()->withPassword('guru123')->create();

        $studentsPerClass = intdiv($studentCount, $classes->count());
        $remainingStudents = $studentCount % $classes->count();

        $sequence = 2026000000000;

        foreach ($classes as $index => $class) {
            $count = $studentsPerClass + ($index < $remainingStudents ? 1 : 0);

            User::factory()
                ->count($count)
                ->student()
                ->withPassword('siswa123')
                ->create()
                ->each(function (User $user) use ($class, &$sequence) {
                    Student::factory()->create([
                        'user_id' => $user->id,
                        'class_id' => $class->id,
                        'nisn' => (string) $sequence++,
                    ]);
                });
        }

        if ($teachers->isNotEmpty()) {
            foreach ($classes as $index => $class) {
                $teacher = $teachers[$index % $teachers->count()];
                $class->teachers()->syncWithoutDetaching([$teacher->id]);
            }
        }

        $students = Student::query()->pluck('id');
        $dates = collect(range(0, 29))->map(fn (int $offset) => Carbon::today()->subDays($offset)->toDateString());

        foreach ($students as $studentId) {
            foreach ($dates as $date) {
                Attendance::factory()->create([
                    'student_id' => $studentId,
                    'date' => $date,
                ]);
            }
        }

        Holiday::factory()->count(10)->create();

        User::query()->updateOrCreate(
            ['email' => 'admin@scanhadir.com'],
            [
                'name' => 'Admin ScanHadir',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'guru1@sekolah.sch.id'],
            [
                'name' => 'Ibu Siti Nur Azizah, S.Pd',
                'password' => Hash::make('guru123'),
                'role' => 'teacher',
                'email_verified_at' => now(),
            ]
        );

        $sampleTeacher = User::query()->where('email', 'guru1@sekolah.sch.id')->first();

        if ($sampleTeacher !== null && $classes->isNotEmpty()) {
            $sampleAssignedClassIds = $classes->take(2)->pluck('id')->all();
            $sampleTeacher->assignedClasses()->syncWithoutDetaching($sampleAssignedClassIds);
        }

        $sampleStudentUser = User::query()->updateOrCreate(
            ['email' => 'rizki@sekolah.sch.id'],
            [
                'name' => 'Muhammad Rizki Pratama',
                'password' => Hash::make('siswa123'),
                'role' => 'student',
                'email_verified_at' => now(),
            ]
        );

        Student::query()->updateOrCreate(
            ['user_id' => $sampleStudentUser->id],
            [
                'class_id' => $classes->first()->id,
                'nisn' => '2026000999999',
            ]
        );

        // Output information for testing
        echo "\n✅ Database seeding completed!\n";
        echo "\n📋 Test Credentials:\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "\n👨‍💼 ADMIN:\n";
        echo "  Email: admin@scanhadir.com\n";
        echo "  Password: admin123\n";
        echo "\n👨‍🏫 TEACHER:\n";
        echo "  Email: guru1@sekolah.sch.id\n";
        echo "  Password: guru123\n";
        echo "\n👨‍🎓 STUDENT:\n";
        echo "  Email: rizki@sekolah.sch.id | Password: siswa123\n";
        echo "\n📊 Data Generated:\n";
        echo "  Users: " . User::count() . " (80% student, 15% admin, 5% teacher)\n";
        echo "  Students: " . Student::count() . "\n";
        echo "  Classes: " . StudentClass::count() . "\n";
        echo "  Attendances: " . Attendance::count() . " (30 hari)\n";
        echo "  Holidays: " . Holiday::count() . "\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    }
}

