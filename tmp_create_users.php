<?php

use App\Models\User;
use App\Models\Student;
use App\Models\StudentClass;
use Illuminate\Support\Facades\Hash;

// Admin is already there but let's be sure
User::updateOrCreate(
    ['email' => 'admin@scanhadir.com'],
    [
        'name' => 'Admin ScanHadir',
        'password' => Hash::make('admin123'),
        'role' => 'admin',
        'email_verified_at' => now(),
    ]
);

// Teacher
User::updateOrCreate(
    ['email' => 'guru1@sekolah.sch.id'],
    [
        'name' => 'Ibu Siti Nur Azizah, S.Pd',
        'password' => Hash::make('guru123'),
        'role' => 'teacher',
        'email_verified_at' => now(),
    ]
);

// Student
$studentUser = User::updateOrCreate(
    ['email' => 'rizki@sekolah.sch.id'],
    [
        'name' => 'Muhammad Rizki Pratama',
        'password' => Hash::make('siswa123'),
        'role' => 'student',
        'email_verified_at' => now(),
    ]
);

$class = StudentClass::first();
if ($class) {
    Student::updateOrCreate(
        ['user_id' => $studentUser->id],
        [
            'class_id' => $class->id,
            'nisn' => '2026000999999',
        ]
    );
}

echo "Users created successfully!\n";
