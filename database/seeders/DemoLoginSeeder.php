<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\StudentClass;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoLoginSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@scanhadir.com'],
            [
                'name' => 'Admin ScanHadir',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $teacher = User::query()->updateOrCreate(
            ['email' => 'guru@scanhadir.com'],
            [
                'name' => 'Guru Demo ScanHadir',
                'password' => Hash::make('guru123'),
                'role' => 'teacher',
                'email_verified_at' => now(),
            ]
        );

        $class = StudentClass::query()->firstOrCreate(
            ['name' => 'Demo Class'],
            [
                'level' => 'Demo',
                'major' => 'General',
            ]
        );

        $class->teachers()->syncWithoutDetaching([$teacher->id]);

        $studentUser = User::query()->updateOrCreate(
            ['email' => 'siswa@scanhadir.com'],
            [
                'name' => 'Siswa Demo ScanHadir',
                'password' => Hash::make('siswa123'),
                'role' => 'student',
                'email_verified_at' => now(),
            ]
        );

        Student::query()->updateOrCreate(
            ['user_id' => $studentUser->id],
            [
                'class_id' => $class->id,
                'nisn' => '24990000',
            ]
        );
    }
}
