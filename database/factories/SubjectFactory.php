<?php

namespace Database\Factories;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subject>
 */
class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    public function definition(): array
    {
        $groups = ['Kejuruan', 'Umum'];

        return [
            'code' => 'MP-' . $this->faker->unique()->numerify('###'),
            'name' => $this->faker->unique()->words(2, true),
            'group' => $this->faker->randomElement($groups),
            'semester_akademik_id' => \App\Models\SemesterAkademik::query()->inRandomOrder()->value('id') ?? \App\Models\SemesterAkademik::factory(),
        ];
    }
}
