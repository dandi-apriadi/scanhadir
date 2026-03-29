<?php

namespace Database\Factories;

use App\Models\StudentClass;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StudentClass>
 */
class StudentClassFactory extends Factory
{
    protected $model = StudentClass::class;

    public function definition(): array
    {
        $levels = ['X', 'XI', 'XII'];
        $majors = ['RPL', 'TKJ', 'MM'];
        $level = $this->faker->randomElement($levels);
        $major = $this->faker->randomElement($majors);

        return [
            'name' => $level . '-' . $major . '-' . $this->faker->numberBetween(1, 3),
            'level' => $level,
            'major' => $major,
        ];
    }
}
