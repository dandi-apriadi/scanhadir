<?php

namespace Database\Seeders;

use App\Models\MataKuliahDosenAssignment;
use App\Models\Schedule;
use App\Models\SemesterAkademik;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\Subject;
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

        // 1. Create Users
        $this->command->info('👤 Creating users...');
        
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@scanhadir.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $dosen1 = User::create([
            'name' => 'Budi Santoso, M.Kom',
            'email' => 'budi@scanhadir.test',
            'password' => Hash::make('password'),
            'role' => 'dosen',
        ]);

        $dosen2 = User::create([
            'name' => 'Siti Rahayu, M.Pd',
            'email' => 'siti@scanhadir.test',
            'password' => Hash::make('password'),
            'role' => 'dosen',
        ]);

        $dosen3 = User::create([
            'name' => 'Ahmad Fauzi, M.T',
            'email' => 'ahmad@scanhadir.test',
            'password' => Hash::make('password'),
            'role' => 'dosen',
        ]);

        $this->command->info('   ✅ 1 Admin + 3 Dosen created');

        // 2. Get Active Semester
        $semester = SemesterAkademik::where('is_active', true)->first();
        if (!$semester) {
            $semester = SemesterAkademik::create([
                'nama_semester' => 'Semester Genap',
                'tahun_ajaran' => '2025/2026',
                'tanggal_mulai' => '2026-02-01',
                'tanggal_selesai' => '2026-07-31',
                'is_active' => true,
            ]);
        }
        $this->command->info("   📅 Using semester: {$semester->display_name}");

        // 3. Create Classes
        $this->command->info('🏫 Creating classes...');
        
        $classes = [
            ['name' => 'XII RPL 1', 'level' => 'XII', 'major' => 'Rekayasa Perangkat Lunak'],
            ['name' => 'XII RPL 2', 'level' => 'XII', 'major' => 'Rekayasa Perangkat Lunak'],
            ['name' => 'XII TKJ 1', 'level' => 'XII', 'major' => 'Teknik Komputer Jaringan'],
            ['name' => 'XI RPL 1', 'level' => 'XI', 'major' => 'Rekayasa Perangkat Lunak'],
        ];

        $classModels = [];
        foreach ($classes as $classData) {
            $classModels[$classData['name']] = StudentClass::create($classData);
        }
        $this->command->info('   ✅ 4 Kelas created');

        // 4. Create Subjects
        $this->command->info('📚 Creating subjects...');
        
        $subjects = [
            ['code' => 'PPLG-01', 'name' => 'Pemrograman Web', 'group' => 'Kejuruan', 'sks' => 4],
            ['code' => 'PPLG-02', 'name' => 'Basis Data', 'group' => 'Kejuruan', 'sks' => 3],
            ['code' => 'PPLG-03', 'name' => 'Pemrograman Mobile', 'group' => 'Kejuruan', 'sks' => 4],
            ['code' => 'TKJ-01', 'name' => 'Administrasi Server', 'group' => 'Kejuruan', 'sks' => 4],
            ['code' => 'TKJ-02', 'name' => 'Keamanan Jaringan', 'group' => 'Kejuruan', 'sks' => 3],
            ['code' => 'UMUM-01', 'name' => 'Bahasa Indonesia', 'group' => 'Umum', 'sks' => 2],
            ['code' => 'UMUM-02', 'name' => 'Matematika', 'group' => 'Umum', 'sks' => 3],
        ];

        $subjectModels = [];
        foreach ($subjects as $subjectData) {
            $subjectData['semester_akademik_id'] = $semester->id;
            $subjectModels[$subjectData['code']] = Subject::create($subjectData);
        }
        $this->command->info('   ✅ 7 Mata Pelajaran created');

        // 5. Create MataKuliahDosenAssignments
        $this->command->info('👨‍🏫 Creating dosen assignments...');
        
        $assignments = [
            ['subject_id' => $subjectModels['PPLG-01']->id, 'user_id' => $dosen1->id],
            ['subject_id' => $subjectModels['PPLG-02']->id, 'user_id' => $dosen1->id],
            ['subject_id' => $subjectModels['PPLG-03']->id, 'user_id' => $dosen2->id],
            ['subject_id' => $subjectModels['TKJ-01']->id, 'user_id' => $dosen3->id],
            ['subject_id' => $subjectModels['TKJ-02']->id, 'user_id' => $dosen3->id],
            ['subject_id' => $subjectModels['UMUM-01']->id, 'user_id' => $dosen2->id],
            ['subject_id' => $subjectModels['UMUM-02']->id, 'user_id' => $dosen1->id],
        ];

        foreach ($assignments as $assignment) {
            MataKuliahDosenAssignment::create($assignment);
        }
        $this->command->info('   ✅ 7 Penugasan Dosen created');

        // 6. Create Schedules
        $this->command->info('📅 Creating schedules...');
        
        $schedules = [
            // XII RPL 1 - Senin
            ['class_id' => $classModels['XII RPL 1']->id, 'subject_id' => $subjectModels['PPLG-01']->id, 'teacher_id' => $dosen1->id, 'day' => 'Senin', 'start_time' => '07:00:00', 'end_time' => '09:30:00', 'room' => 'Lab RPL 1', 'semester_akademik_id' => $semester->id],
            ['class_id' => $classModels['XII RPL 1']->id, 'subject_id' => $subjectModels['PPLG-02']->id, 'teacher_id' => $dosen1->id, 'day' => 'Senin', 'start_time' => '10:00:00', 'end_time' => '12:00:00', 'room' => 'Lab RPL 1', 'semester_akademik_id' => $semester->id],
            // XII RPL 1 - Selasa
            ['class_id' => $classModels['XII RPL 1']->id, 'subject_id' => $subjectModels['PPLG-03']->id, 'teacher_id' => $dosen2->id, 'day' => 'Selasa', 'start_time' => '07:00:00', 'end_time' => '09:30:00', 'room' => 'Lab Mobile', 'semester_akademik_id' => $semester->id],
            ['class_id' => $classModels['XII RPL 1']->id, 'subject_id' => $subjectModels['UMUM-01']->id, 'teacher_id' => $dosen2->id, 'day' => 'Selasa', 'start_time' => '10:00:00', 'end_time' => '11:30:00', 'room' => 'Ruang 301', 'semester_akademik_id' => $semester->id],
            // XII RPL 2 - Senin
            ['class_id' => $classModels['XII RPL 2']->id, 'subject_id' => $subjectModels['PPLG-01']->id, 'teacher_id' => $dosen1->id, 'day' => 'Senin', 'start_time' => '13:00:00', 'end_time' => '15:30:00', 'room' => 'Lab RPL 1', 'semester_akademik_id' => $semester->id],
            // XII TKJ 1 - Rabu
            ['class_id' => $classModels['XII TKJ 1']->id, 'subject_id' => $subjectModels['TKJ-01']->id, 'teacher_id' => $dosen3->id, 'day' => 'Rabu', 'start_time' => '07:00:00', 'end_time' => '09:30:00', 'room' => 'Lab Jaringan', 'semester_akademik_id' => $semester->id],
            ['class_id' => $classModels['XII TKJ 1']->id, 'subject_id' => $subjectModels['TKJ-02']->id, 'teacher_id' => $dosen3->id, 'day' => 'Rabu', 'start_time' => '10:00:00', 'end_time' => '12:00:00', 'room' => 'Lab Jaringan', 'semester_akademik_id' => $semester->id],
            // XI RPL 1 - Kamis
            ['class_id' => $classModels['XI RPL 1']->id, 'subject_id' => $subjectModels['PPLG-01']->id, 'teacher_id' => $dosen1->id, 'day' => 'Kamis', 'start_time' => '07:00:00', 'end_time' => '09:30:00', 'room' => 'Lab RPL 2', 'semester_akademik_id' => $semester->id],
            ['class_id' => $classModels['XI RPL 1']->id, 'subject_id' => $subjectModels['UMUM-02']->id, 'teacher_id' => $dosen1->id, 'day' => 'Kamis', 'start_time' => '10:00:00', 'end_time' => '11:30:00', 'room' => 'Ruang 201', 'semester_akademik_id' => $semester->id],
        ];

        foreach ($schedules as $scheduleData) {
            Schedule::create($scheduleData);
        }
        $this->command->info('   ✅ 9 Jadwal created');

        // 7. Create Students
        $this->command->info('🎓 Creating students...');
        
        $studentData = [
            // XII RPL 1
            ['nisn' => '2022001', 'name' => 'Andi Pratama', 'email' => 'andi@student.test', 'class' => 'XII RPL 1'],
            ['nisn' => '2022002', 'name' => 'Bella Safitri', 'email' => 'bella@student.test', 'class' => 'XII RPL 1'],
            ['nisn' => '2022003', 'name' => 'Cahya Dewi', 'email' => 'cahya@student.test', 'class' => 'XII RPL 1'],
            ['nisn' => '2022004', 'name' => 'Dimas Aditya', 'email' => 'dimas@student.test', 'class' => 'XII RPL 1'],
            ['nisn' => '2022005', 'name' => 'Eka Putri', 'email' => 'eka@student.test', 'class' => 'XII RPL 1'],
            // XII RPL 2
            ['nisn' => '2022006', 'name' => 'Fajar Nugroho', 'email' => 'fajar@student.test', 'class' => 'XII RPL 2'],
            ['nisn' => '2022007', 'name' => 'Gita Amelia', 'email' => 'gita@student.test', 'class' => 'XII RPL 2'],
            ['nisn' => '2022008', 'name' => 'Hendra Wijaya', 'email' => 'hendra@student.test', 'class' => 'XII RPL 2'],
            // XII TKJ 1
            ['nisn' => '2022009', 'name' => 'Irfan Hakim', 'email' => 'irfan@student.test', 'class' => 'XII TKJ 1'],
            ['nisn' => '2022010', 'name' => 'Jasmine Aulia', 'email' => 'jasmine@student.test', 'class' => 'XII TKJ 1'],
            ['nisn' => '2022011', 'name' => 'Kevin Susanto', 'email' => 'kevin@student.test', 'class' => 'XII TKJ 1'],
            // XI RPL 1
            ['nisn' => '2023001', 'name' => 'Lina Marlina', 'email' => 'lina@student.test', 'class' => 'XI RPL 1'],
            ['nisn' => '2023002', 'name' => 'Muhammad Rizki', 'email' => 'rizki@student.test', 'class' => 'XI RPL 1'],
            ['nisn' => '2023003', 'name' => 'Nadia Permata', 'email' => 'nadia@student.test', 'class' => 'XI RPL 1'],
        ];

        foreach ($studentData as $data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
                'role' => 'student',
            ]);

            Student::create([
                'user_id' => $user->id,
                'class_id' => $classModels[$data['class']]->id,
                'nisn' => $data['nisn'],
                'qr_code' => 'SH-' . strtoupper(\Illuminate\Support\Str::random(8)),
            ]);
        }
        $this->command->info('   ✅ 14 Siswa created');

        $this->command->info('');
        $this->command->info('✅ Seeding completed!');
        $this->command->info('');
        $this->command->info('🔐 Login Credentials:');
        $this->command->info('   Admin:  admin@scanhadir.test / password');
        $this->command->info('   Dosen:  budi@scanhadir.test / password');
        $this->command->info('           siti@scanhadir.test / password');
        $this->command->info('           ahmad@scanhadir.test / password');
        $this->command->info('   Siswa:  andi@student.test / password');
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
