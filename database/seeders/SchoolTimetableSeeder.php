<?php

namespace Database\Seeders;

use App\Models\Schedule;
use App\Models\SemesterAkademik;
use App\Models\StudentClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class SchoolTimetableSeeder extends Seeder
{
    public function seedStructure(): array
    {
        $semester = SemesterAkademik::query()->updateOrCreate(
            [
                'nama_semester' => 'Semester Genap',
                'tahun_ajaran' => '2025/2026',
            ],
            [
                'tanggal_mulai' => '2026-02-01',
                'tanggal_selesai' => '2026-07-31',
                'is_active' => true,
            ]
        );

        $teachers = collect($this->teacherDefinitions())->mapWithKeys(function (array $teacherData, string $alias): array {
            $user = User::query()->updateOrCreate(
                ['email' => $teacherData['email']],
                [
                    'name' => $teacherData['name'],
                    'password' => Hash::make($teacherData['password']),
                    'role' => $teacherData['role'],
                    'email_verified_at' => now(),
                ]
            );

            return [$alias => $user];
        });

        $classes = collect($this->classDefinitions())->mapWithKeys(function (array $classData): array {
            $class = StudentClass::query()->updateOrCreate(
                ['name' => $classData['name']],
                [
                    'level' => $classData['level'],
                    'major' => $classData['major'],
                ]
            );

            return [$classData['name'] => $class];
        });

        $subjects = collect($this->subjectDefinitions())->mapWithKeys(function (array $subjectData) use ($semester): array {
            $subject = Subject::query()->updateOrCreate(
                ['code' => $subjectData['code']],
                [
                    'name' => $subjectData['name'],
                    'group' => $subjectData['group'],
                    'semester_akademik_id' => $semester->id,
                    'sks' => $subjectData['sks'],
                ]
            );

            return [$subjectData['code'] => $subject];
        });

        Schedule::query()
            ->where('semester_akademik_id', $semester->id)
            ->delete();

        foreach ($this->scheduleDefinitions() as $day => $classSchedules) {
            foreach ($classSchedules as $className => $rows) {
                $class = $classes->get($className);

                foreach ($rows as $row) {
                    $subject = $subjects->get($row['subject_code']);
                    $teacher = $teachers->get($this->teacherAliasFor($className, $row['subject_code']));

                    Schedule::query()->updateOrCreate(
                        [
                            'class_id' => $class->id,
                            'subject_id' => $subject->id,
                            'teacher_id' => $teacher->id,
                            'day' => $day,
                            'start_time' => Carbon::createFromFormat('H:i', $row['start'])->format('H:i:s'),
                            'end_time' => Carbon::createFromFormat('H:i', $row['end'])->format('H:i:s'),
                            'semester_akademik_id' => $semester->id,
                        ],
                        [
                            'room' => $row['room'] ?? $className,
                        ]
                    );
                }
            }
        }

        return [
            'semester' => $semester,
            'teachers' => $teachers,
            'classes' => $classes,
            'subjects' => $subjects,
        ];
    }

    public function run(): void
    {
        $this->seedStructure();
    }

    private function classDefinitions(): array
    {
        return [
            ['name' => '7A', 'level' => 'VII', 'major' => 'SMP'],
            ['name' => '7B', 'level' => 'VII', 'major' => 'SMP'],
            ['name' => '8', 'level' => 'VIII', 'major' => 'SMP'],
            ['name' => '9A', 'level' => 'IX', 'major' => 'SMP'],
            ['name' => '9B', 'level' => 'IX', 'major' => 'SMP'],
        ];
    }

    private function subjectDefinitions(): array
    {
        return [
            ['code' => 'IPA', 'name' => 'Ilmu Pengetahuan Alam', 'group' => 'Umum', 'sks' => 3],
            ['code' => 'MAT', 'name' => 'Matematika', 'group' => 'Umum', 'sks' => 4],
            ['code' => 'PANCASILA', 'name' => 'Pendidikan Pancasila', 'group' => 'Umum', 'sks' => 2],
            ['code' => 'BIND', 'name' => 'Bahasa Indonesia', 'group' => 'Umum', 'sks' => 4],
            ['code' => 'BING', 'name' => 'Bahasa Inggris', 'group' => 'Umum', 'sks' => 4],
            ['code' => 'SBP', 'name' => 'Seni Budaya / Prakarya', 'group' => 'Umum', 'sks' => 2],
            ['code' => 'PJOK', 'name' => 'Pendidikan Jasmani Olahraga', 'group' => 'Umum', 'sks' => 2],
            ['code' => 'IPS', 'name' => 'Ilmu Pengetahuan Sosial', 'group' => 'Umum', 'sks' => 3],
            ['code' => 'INF', 'name' => 'Informatika', 'group' => 'Umum', 'sks' => 2],
            ['code' => 'KKA', 'name' => 'KKA', 'group' => 'Umum', 'sks' => 2],
            ['code' => 'MULOK', 'name' => 'Muatan Lokal', 'group' => 'Umum', 'sks' => 2],
            ['code' => 'PAKBP', 'name' => 'PAK dan BP', 'group' => 'Umum', 'sks' => 2],
        ];
    }

    private function teacherDefinitions(): array
    {
        return [
            'laetitia' => ['name' => 'Laetitia Komalig, S.Pd', 'email' => 'laetitia@scanhadir.test', 'password' => 'guru123', 'role' => 'teacher'],
            'pingkan' => ['name' => 'Pingkan Mamauja, S.Pd', 'email' => 'pingkan@scanhadir.test', 'password' => 'guru123', 'role' => 'teacher'],
            'eke' => ['name' => 'Eke Nataria Lombogia, S.Teol', 'email' => 'eke@scanhadir.test', 'password' => 'guru123', 'role' => 'teacher'],
            'fian' => ['name' => 'Fian Mamauja, S.Pd.Gr, M.Pd', 'email' => 'fian@scanhadir.test', 'password' => 'guru123', 'role' => 'teacher'],
            'imakulata' => ['name' => 'Imakulata Pongoh, S.Pd', 'email' => 'imakulata@scanhadir.test', 'password' => 'guru123', 'role' => 'teacher'],
            'farly' => ['name' => 'Farly Gerung, S.Pd', 'email' => 'farly@scanhadir.test', 'password' => 'guru123', 'role' => 'teacher'],
            'jantje' => ['name' => 'Drs. Jantje Mangore, M.Pd', 'email' => 'jantje@scanhadir.test', 'password' => 'guru123', 'role' => 'teacher'],
            'sintha' => ['name' => 'Sintha Alkomar, S.Pd', 'email' => 'sintha@scanhadir.test', 'password' => 'guru123', 'role' => 'teacher'],
            'febry' => ['name' => 'Febry Pangemanan, S.Pd', 'email' => 'febry@scanhadir.test', 'password' => 'guru123', 'role' => 'teacher'],
            'majesty' => ['name' => 'Majesty Mamauja', 'email' => 'majesty@scanhadir.test', 'password' => 'guru123', 'role' => 'teacher'],
            'rivanti' => ['name' => 'Rivanti Gracia Kawung, SE', 'email' => 'rivanti@scanhadir.test', 'password' => 'guru123', 'role' => 'teacher'],
            'ifke' => ['name' => 'Ifke Pusung, S.Pd', 'email' => 'ifke@scanhadir.test', 'password' => 'guru123', 'role' => 'teacher'],
        ];
    }

    private function scheduleDefinitions(): array
    {
        return [
            'Senin' => [
                '7A' => [
                    ['start' => '08:10', 'end' => '09:30', 'subject_code' => 'IPA'],
                    ['start' => '09:55', 'end' => '11:55', 'subject_code' => 'MAT'],
                    ['start' => '12:10', 'end' => '13:30', 'subject_code' => 'PANCASILA'],
                ],
                '7B' => [
                    ['start' => '08:10', 'end' => '09:30', 'subject_code' => 'MAT'],
                    ['start' => '09:55', 'end' => '11:55', 'subject_code' => 'PANCASILA'],
                    ['start' => '12:10', 'end' => '13:30', 'subject_code' => 'BIND'],
                ],
                '8' => [
                    ['start' => '08:10', 'end' => '10:35', 'subject_code' => 'MAT'],
                    ['start' => '10:35', 'end' => '11:55', 'subject_code' => 'BING'],
                    ['start' => '12:10', 'end' => '13:30', 'subject_code' => 'IPA'],
                ],
                '9A' => [
                    ['start' => '08:10', 'end' => '10:35', 'subject_code' => 'PJOK'],
                    ['start' => '10:35', 'end' => '11:55', 'subject_code' => 'SBP'],
                    ['start' => '12:10', 'end' => '13:30', 'subject_code' => 'PAKBP'],
                ],
                '9B' => [
                    ['start' => '08:10', 'end' => '10:35', 'subject_code' => 'PAKBP'],
                    ['start' => '10:35', 'end' => '11:55', 'subject_code' => 'PJOK'],
                    ['start' => '12:10', 'end' => '13:30', 'subject_code' => 'IPS'],
                ],
            ],
            'Selasa' => [
                '7A' => [
                    ['start' => '07:30', 'end' => '08:50', 'subject_code' => 'BING'],
                    ['start' => '08:50', 'end' => '09:30', 'subject_code' => 'MULOK'],
                    ['start' => '09:55', 'end' => '11:15', 'subject_code' => 'INF'],
                    ['start' => '11:15', 'end' => '11:55', 'subject_code' => 'IPS'],
                    ['start' => '12:10', 'end' => '13:30', 'subject_code' => 'MAT'],
                ],
                '7B' => [
                    ['start' => '07:30', 'end' => '09:30', 'subject_code' => 'IPA'],
                    ['start' => '09:55', 'end' => '11:55', 'subject_code' => 'MAT'],
                    ['start' => '12:10', 'end' => '13:30', 'subject_code' => 'INF'],
                ],
                '8' => [
                    ['start' => '07:30', 'end' => '09:30', 'subject_code' => 'BIND'],
                    ['start' => '09:55', 'end' => '11:55', 'subject_code' => 'PAKBP'],
                    ['start' => '12:10', 'end' => '13:30', 'subject_code' => 'IPS'],
                ],
                '9A' => [
                    ['start' => '07:30', 'end' => '08:50', 'subject_code' => 'IPS'],
                    ['start' => '08:50', 'end' => '10:35', 'subject_code' => 'IPA'],
                    ['start' => '10:35', 'end' => '11:55', 'subject_code' => 'BING'],
                    ['start' => '12:10', 'end' => '13:30', 'subject_code' => 'BIND'],
                ],
                '9B' => [
                    ['start' => '07:30', 'end' => '08:10', 'subject_code' => 'MULOK'],
                    ['start' => '08:10', 'end' => '09:30', 'subject_code' => 'BING'],
                    ['start' => '09:55', 'end' => '11:55', 'subject_code' => 'BIND'],
                    ['start' => '12:10', 'end' => '13:30', 'subject_code' => 'SBP'],
                ],
            ],
            'Rabu' => [
                '7A' => [
                    ['start' => '07:30', 'end' => '09:30', 'subject_code' => 'IPA'],
                    ['start' => '09:55', 'end' => '10:35', 'subject_code' => 'KKA'],
                    ['start' => '10:35', 'end' => '11:55', 'subject_code' => 'BING'],
                    ['start' => '12:10', 'end' => '13:30', 'subject_code' => 'BIND'],
                ],
                '7B' => [
                    ['start' => '07:30', 'end' => '08:50', 'subject_code' => 'IPS'],
                    ['start' => '08:50', 'end' => '09:30', 'subject_code' => 'KKA'],
                    ['start' => '09:55', 'end' => '11:55', 'subject_code' => 'BIND'],
                    ['start' => '12:10', 'end' => '13:30', 'subject_code' => 'BING'],
                ],
                '8' => [
                    ['start' => '07:30', 'end' => '09:30', 'subject_code' => 'MAT'],
                    ['start' => '09:55', 'end' => '11:55', 'subject_code' => 'SBP'],
                    ['start' => '12:10', 'end' => '13:30', 'subject_code' => 'IPS'],
                ],
                '9A' => [
                    ['start' => '07:30', 'end' => '08:50', 'subject_code' => 'BING'],
                    ['start' => '08:50', 'end' => '11:15', 'subject_code' => 'PANCASILA'],
                    ['start' => '11:15', 'end' => '13:30', 'subject_code' => 'IPA'],
                ],
                '9B' => [
                    ['start' => '07:30', 'end' => '08:50', 'subject_code' => 'BIND'],
                    ['start' => '08:50', 'end' => '09:30', 'subject_code' => 'IPA'],
                    ['start' => '09:55', 'end' => '11:15', 'subject_code' => 'BING'],
                    ['start' => '11:15', 'end' => '13:30', 'subject_code' => 'PANCASILA'],
                ],
            ],
            'Kamis' => [
                '7A' => [
                    ['start' => '07:30', 'end' => '09:30', 'subject_code' => 'BIND'],
                    ['start' => '09:55', 'end' => '11:55', 'subject_code' => 'IPS'],
                    ['start' => '12:10', 'end' => '13:30', 'subject_code' => 'SBP'],
                ],
                '7B' => [
                    ['start' => '07:30', 'end' => '09:30', 'subject_code' => 'PAKBP'],
                    ['start' => '09:55', 'end' => '11:15', 'subject_code' => 'BING'],
                    ['start' => '11:15', 'end' => '11:55', 'subject_code' => 'MULOK'],
                    ['start' => '12:10', 'end' => '13:30', 'subject_code' => 'IPS'],
                ],
                '8' => [
                    ['start' => '07:30', 'end' => '09:30', 'subject_code' => 'PJOK'],
                    ['start' => '09:55', 'end' => '10:35', 'subject_code' => 'MULOK'],
                    ['start' => '10:35', 'end' => '11:55', 'subject_code' => 'IPA'],
                    ['start' => '12:10', 'end' => '13:30', 'subject_code' => 'BING'],
                ],
                '9A' => [
                    ['start' => '07:30', 'end' => '08:50', 'subject_code' => 'MAT'],
                    ['start' => '08:50', 'end' => '09:30', 'subject_code' => 'MULOK'],
                    ['start' => '09:55', 'end' => '11:15', 'subject_code' => 'INF'],
                    ['start' => '11:15', 'end' => '13:30', 'subject_code' => 'BIND'],
                ],
                '9B' => [
                    ['start' => '07:30', 'end' => '09:30', 'subject_code' => 'IPA'],
                    ['start' => '09:55', 'end' => '11:55', 'subject_code' => 'MAT'],
                    ['start' => '12:10', 'end' => '13:30', 'subject_code' => 'INF'],
                ],
            ],
            'Jumat' => [
                '7A' => [
                    ['start' => '07:30', 'end' => '09:30', 'subject_code' => 'PAKBP'],
                    ['start' => '09:55', 'end' => '11:55', 'subject_code' => 'PJOK'],
                ],
                '7B' => [
                    ['start' => '07:30', 'end' => '09:30', 'subject_code' => 'PJOK'],
                    ['start' => '09:55', 'end' => '11:15', 'subject_code' => 'IPA'],
                    ['start' => '11:15', 'end' => '11:55', 'subject_code' => 'BIND'],
                ],
                '8' => [
                    ['start' => '07:30', 'end' => '08:50', 'subject_code' => 'INF'],
                    ['start' => '08:50', 'end' => '11:55', 'subject_code' => 'PANCASILA'],
                ],
                '9A' => [
                    ['start' => '07:30', 'end' => '08:50', 'subject_code' => 'IPS'],
                    ['start' => '08:50', 'end' => '11:55', 'subject_code' => 'MAT'],
                ],
                '9B' => [
                    ['start' => '07:30', 'end' => '08:50', 'subject_code' => 'MAT'],
                    ['start' => '08:50', 'end' => '09:30', 'subject_code' => 'BIND'],
                    ['start' => '09:55', 'end' => '11:55', 'subject_code' => 'IPS'],
                ],
            ],
        ];
    }

    private function teacherAliasFor(string $className, string $subjectCode): string
    {
        return match ($subjectCode) {
            'IPA' => in_array($className, ['7A', '7B'], true) ? 'jantje' : 'majesty',
            'MAT' => $className === '8' ? 'farly' : 'febry',
            'PANCASILA' => 'sintha',
            'BIND' => $className === '8' ? 'rivanti' : 'laetitia',
            'BING' => in_array($className, ['7A', '7B'], true) ? 'fian' : 'pingkan',
            'SBP' => 'imakulata',
            'PJOK' => in_array($className, ['7A', '7B'], true) ? 'pingkan' : 'fian',
            'IPS' => $className === '8' ? 'rivanti' : 'ifke',
            'INF' => 'farly',
            'KKA' => 'farly',
            'MULOK' => 'imakulata',
            'PAKBP' => 'eke',
            default => 'farly',
        };
    }
}