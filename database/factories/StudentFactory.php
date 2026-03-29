<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\StudentClass;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->student(),
            'class_id' => StudentClass::query()->inRandomOrder()->value('id') ?? StudentClass::factory(),
            'nisn' => $this->faker->unique()->numerify('#############'),
            'photo_path' => null,
        ];
    }
}
