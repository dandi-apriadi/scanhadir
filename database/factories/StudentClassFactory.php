<?php

namespace Database\Factories;

use App\Models\StudentClass;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\StudentClass>
 */
class StudentClassFactory extends Factory
{
    protected $model = StudentClass::class;

    public function definition(): array
    {
        $classes = [
            ['name' => '7A', 'level' => 'VII', 'major' => 'SMP'],
            ['name' => '7B', 'level' => 'VII', 'major' => 'SMP'],
            ['name' => '8', 'level' => 'VIII', 'major' => 'SMP'],
            ['name' => '9A', 'level' => 'IX', 'major' => 'SMP'],
            ['name' => '9B', 'level' => 'IX', 'major' => 'SMP'],
        ];

        $class = $this->faker->randomElement($classes);

        return [
            'name' => $class['name'] . '-' . $this->faker->unique()->numerify('###'),
            'level' => $class['level'],
            'major' => $class['major'],
        ];
    }
}
