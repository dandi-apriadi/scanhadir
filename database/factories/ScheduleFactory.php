<?php

namespace Database\Factories;

use App\Models\Schedule;
use App\Models\StudentClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Schedule>
 */
class ScheduleFactory extends Factory
{
    protected $model = Schedule::class;

    public function definition(): array
    {
        $startHour = $this->faker->numberBetween(7, 13);
        $startMinute = $this->faker->randomElement([0, 15, 30, 45]);
        $start = sprintf('%02d:%02d:00', $startHour, $startMinute);
        $endHour = min(17, $startHour + 2);
        $end = sprintf('%02d:%02d:00', $endHour, $startMinute);

        return [
            'class_id' => StudentClass::query()->inRandomOrder()->value('id') ?? StudentClass::factory(),
            'subject_id' => Subject::query()->inRandomOrder()->value('id') ?? Subject::factory(),
            'teacher_id' => User::query()->where('role', 'teacher')->inRandomOrder()->value('id') ?? User::factory()->teacher(),
            'day' => $this->faker->randomElement(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']),
            'start_time' => $start,
            'end_time' => $end,
            'room' => 'Lab ' . $this->faker->numberBetween(1, 10),
        ];
    }
}
