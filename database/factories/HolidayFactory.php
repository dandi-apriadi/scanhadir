<?php

namespace Database\Factories;

use App\Models\Holiday;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Holiday>
 */
class HolidayFactory extends Factory
{
    protected $model = Holiday::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-6 months', '+6 months');
        $days = $this->faker->numberBetween(1, 5);

        return [
            'name' => 'Libur ' . $this->faker->unique()->words(2, true),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $startDate->modify('+' . ($days - 1) . ' days')->format('Y-m-d'),
            'type' => $this->faker->randomElement(['national', 'school', 'exam_break']),
            'description' => $this->faker->sentence(),
        ];
    }
}
