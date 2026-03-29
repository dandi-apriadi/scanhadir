<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SimulationDataSeeder extends Seeder
{
    public function run(): void
    {
        echo "🚀 Starting 2-Year Simulation Data Seeding...\n";

        // 1. Create Admins
        User::updateOrCreate(
            ['email' => 'admin@scanhadir.com'],
            [
                'name' => 'Admin Utama',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // 1.1 Create Demo Teacher
        $demoTeacher = User::updateOrCreate(
            ['email' => 'guru@scanhadir.com'],
            [
                'name' => 'Bpk. Demo Guru, S.Pd',
                'password' => Hash::make('guru123'),
                'role' => 'teacher',
                'email_verified_at' => now(),
            ]
        );


        // 2. Create Subjects
        $subjectsData = [
            ['code' => 'MAT-01', 'name' => 'Matematika Terapan', 'group' => 'Umum'],
            ['code' => 'BIN-01', 'name' => 'Bahasa Indonesia', 'group' => 'Umum'],
            ['code' => 'ENG-01', 'name' => 'English for Tech', 'group' => 'Umum'],
            ['code' => 'RPL-01', 'name' => 'Pemrograman Web', 'group' => 'Kejuruan'],
            ['code' => 'RPL-02', 'name' => 'Basis Data', 'group' => 'Kejuruan'],
            ['code' => 'RPL-03', 'name' => 'Mobile Development', 'group' => 'Kejuruan'],
            ['code' => 'TKJ-01', 'name' => 'Jaringan Komputer', 'group' => 'Kejuruan'],
            ['code' => 'TKJ-02', 'name' => 'Administrasi Server', 'group' => 'Kejuruan'],
            ['code' => 'AGM-01', 'name' => 'Pendidikan Agama', 'group' => 'Umum'],
            ['code' => 'PKN-01', 'name' => 'Pendidikan Kewarganegaraan', 'group' => 'Umum'],
            ['code' => 'ORK-01', 'name' => 'Olahraga', 'group' => 'Umum'],
            ['code' => 'KWU-01', 'name' => 'Kewirausahaan', 'group' => 'Kejuruan'],
        ];

        $subjects = collect($subjectsData)->map(fn($data) => Subject::create($data));
        echo "✅ Subjects created.\n";

        // 3. Create Teachers
        $teachers = User::factory()->count(12)->teacher()->withPassword('guru123')->create();
        echo "✅ Teachers created.\n";

        // 4. Create Classes
        $classesData = [
            ['name' => 'X-RPL', 'level' => 'X', 'major' => 'RPL'],
            ['name' => 'XI-RPL', 'level' => 'XI', 'major' => 'RPL'],
            ['name' => 'XII-RPL', 'level' => 'XII', 'major' => 'RPL'],
            ['name' => 'X-TKJ', 'level' => 'X', 'major' => 'TKJ'],
            ['name' => 'XI-TKJ', 'level' => 'XI', 'major' => 'TKJ'],
            ['name' => 'XII-TKJ', 'level' => 'XII', 'major' => 'TKJ'],
        ];

        $classes = collect($classesData)->map(fn($data) => StudentClass::create($data));
        echo "✅ Classes created.\n";

        // 5. Assign Teachers to Classes and Subjects (Schedules)
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        foreach ($classes as $classIndex => $class) {
            // Assign 2 teachers to this class permanently
            $assignedTeachers = $teachers->random(3);
            $class->teachers()->attach($assignedTeachers->pluck('id'));

            foreach ($days as $dayIndex => $day) {
                // 3 lessons per day
                for ($slot = 0; $slot < 3; $slot++) {
                    $startHour = 7 + ($slot * 2);
                    Schedule::create([
                        'class_id' => $class->id,
                        'subject_id' => $subjects->random()->id,
                        'teacher_id' => $teachers->random()->id,
                        'day' => $day,
                        'start_time' => sprintf('%02d:00:00', $startHour),
                        'end_time' => sprintf('%02d:30:00', $startHour + 1),
                        'room' => 'Ruang ' . ($classIndex + 1) . chr(65 + $slot),
                    ]);
                }
            }
        }
        echo "✅ Schedules created.\n";

        // 6. Create Students
        // 6.1 Create Demo Student
        $demoStudentUser = User::updateOrCreate(
            ['email' => 'siswa@scanhadir.com'],
            [
                'name' => 'Siswa Demo ScanHadir',
                'password' => Hash::make('siswa123'),
                'role' => 'student',
                'email_verified_at' => now(),
            ]
        );

        Student::updateOrCreate(
            ['user_id' => $demoStudentUser->id],
            [
                'class_id' => $classes->first()->id,
                'nisn' => '24990000',
            ]
        );

        $students = collect([Student::where('user_id', $demoStudentUser->id)->first()]);
        foreach ($classes as $class) {
            $studentUsers = User::factory()->count(20)->student()->withPassword('siswa123')->create();
            foreach ($studentUsers as $index => $user) {
                $students->push(Student::create([
                    'user_id' => $user->id,
                    'class_id' => $class->id,
                    'nisn' => '24' . str_pad($class->id, 2, '0', STR_PAD_LEFT) . str_pad($index, 4, '0', STR_PAD_LEFT),
                ]));
            }
        }
        echo "✅ " . $students->count() . " Students created.\n";

        // 7. Generate Attendance History (March 2024 to March 2026)
        $startDate = Carbon::now()->subYears(2);
        $endDate = Carbon::now();
        
        $attendanceBatch = [];
        $totalRecords = 0;

        echo "⏳ Simulating Attendance (this may take a minute)...\n";

        for ($date = clone $startDate; $date->lte($endDate); $date->addDay()) {
            // Skip weekends
            if ($date->isWeekend()) continue;

            // Random holiday chance (10 days per year roughly)
            if (rand(1, 40) === 1) continue;

            foreach ($students as $student) {
                $rand = rand(1, 100);
                $status = 'present';
                $checkIn = null;
                $checkOut = null;

                if ($rand <= 92) {
                    $status = 'present';
                    $checkIn = $date->copy()->setTime(rand(6, 7), rand(0, 15), rand(0, 59))->toTimeString();
                    $checkOut = $date->copy()->setTime(rand(14, 15), rand(0, 59), rand(0, 59))->toTimeString();
                } elseif ($rand <= 96) {
                    $status = 'late';
                    $checkIn = $date->copy()->setTime(7, rand(31, 59), rand(0, 59))->toTimeString();
                    $checkOut = $date->copy()->setTime(rand(14, 15), rand(0, 59), rand(0, 59))->toTimeString();
                } elseif ($rand <= 98) {
                    $status = rand(0, 1) ? 'sick' : 'excused';
                } else {
                    $status = 'absent';
                }

                $attendanceBatch[] = [
                    'student_id' => $student->id,
                    'date' => $date->toDateString(),
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'status' => $status,
                    'notes' => in_array($status, ['sick', 'excused']) ? 'Simulated reason' : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (count($attendanceBatch) >= 1000) {
                    DB::table('attendances')->insert($attendanceBatch);
                    $totalRecords += count($attendanceBatch);
                    $attendanceBatch = [];
                }
            }
        }

        if (count($attendanceBatch) > 0) {
            DB::table('attendances')->insert($attendanceBatch);
            $totalRecords += count($attendanceBatch);
        }

        echo "✅ Simulation Complete! Total Attendance Records: " . number_format($totalRecords) . "\n";
    }
}
