<?php

namespace Database\Factories;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Subject>
 */
class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    public function definition(): array
    {
        $subjects = [
            ['code' => 'IPA', 'name' => 'Ilmu Pengetahuan Alam'],
            ['code' => 'MAT', 'name' => 'Matematika'],
            ['code' => 'PANCASILA', 'name' => 'Pendidikan Pancasila'],
            ['code' => 'BIND', 'name' => 'Bahasa Indonesia'],
            ['code' => 'BING', 'name' => 'Bahasa Inggris'],
            ['code' => 'SBP', 'name' => 'Seni Budaya / Prakarya'],
            ['code' => 'PJOK', 'name' => 'Pendidikan Jasmani Olahraga'],
            ['code' => 'IPS', 'name' => 'Ilmu Pengetahuan Sosial'],
            ['code' => 'INF', 'name' => 'Informatika'],
            ['code' => 'KKA', 'name' => 'KKA'],
            ['code' => 'MULOK', 'name' => 'Muatan Lokal'],
            ['code' => 'PAKBP', 'name' => 'PAK dan BP'],
        ];

        $subject = $this->faker->randomElement($subjects);

        return [
            'code' => $subject['code'] . '-' . $this->faker->unique()->numerify('###'),
            'name' => $subject['name'],
            'group' => 'Umum',
            'semester_akademik_id' => \App\Models\SemesterAkademik::query()->inRandomOrder()->value('id') ?? \App\Models\SemesterAkademik::factory(),
        ];
    }
}
