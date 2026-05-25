<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DevelopmentSeeder extends Seeder
{
    /**
     * Run the database seeds for development/testing.
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding development data...');

        $structure = app(SchoolTimetableSeeder::class)->seedStructure();
        $classModels = $structure['classes'];

        $this->command->info('👤 Creating admin...');
        User::query()->updateOrCreate(
            ['email' => 'admin@scanhadir.test'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        $this->command->info('🎓 Creating students...');

        $studentData = [
            ['nisn' => '2022001', 'name' => 'Andi Pratama', 'email' => 'andi@student.test', 'class' => '7A'],
            ['nisn' => '2022002', 'name' => 'Bella Safitri', 'email' => 'bella@student.test', 'class' => '7A'],
            ['nisn' => '2022003', 'name' => 'Cahya Dewi', 'email' => 'cahya@student.test', 'class' => '7B'],
            ['nisn' => '2022004', 'name' => 'Dimas Aditya', 'email' => 'dimas@student.test', 'class' => '7B'],
            ['nisn' => '2022005', 'name' => 'Eka Putri', 'email' => 'eka@student.test', 'class' => '8'],
            ['nisn' => '2022006', 'name' => 'Fajar Nugroho', 'email' => 'fajar@student.test', 'class' => '8'],
            ['nisn' => '2022007', 'name' => 'Gita Amelia', 'email' => 'gita@student.test', 'class' => '9A'],
            ['nisn' => '2022008', 'name' => 'Hendra Wijaya', 'email' => 'hendra@student.test', 'class' => '9A'],
            ['nisn' => '2022009', 'name' => 'Irfan Hakim', 'email' => 'irfan@student.test', 'class' => '9B'],
            ['nisn' => '2022010', 'name' => 'Jasmine Aulia', 'email' => 'jasmine@student.test', 'class' => '9B'],
        ];

        foreach ($studentData as $data) {
            $user = User::query()->updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'role' => 'student',
                ]
            );

            Student::query()->updateOrCreate(
                ['nisn' => $data['nisn']],
                [
                    'user_id' => $user->id,
                    'class_id' => $classModels[$data['class']]->id,
                    'qr_code' => 'SH-' . strtoupper(\Illuminate\Support\Str::random(8)),
                ]
            );
        }

        $this->command->info('   ✅ demo data created');
        $this->command->info('');
        $this->command->info('✅ Seeding completed!');
        $this->command->info('');
        $this->command->info('🔐 Login Credentials:');
        $this->command->info('   Admin:  admin@scanhadir.test / password');
        $this->command->info('   Student: andi@student.test / password');
        $this->command->info('');
        $this->command->info('🌐 Quick Links:');
        $this->command->info('   Admin Dashboard:  /admin/dashboard');
        $this->command->info('   Dosen Courses:    /dosen/mata-kuliah');
        $this->command->info('   Teacher Dashboard: /teacher/dashboard');
        $this->command->info('   Admin Scanner:    /admin/scanner');
        $this->command->info('   Master Semester:  /admin/master/semester');
        $this->command->info('   Master Jadwal:    /admin/master/jadwal');
        $this->command->info('   Master Mapel:     /admin/master/mapel');
    }
}
