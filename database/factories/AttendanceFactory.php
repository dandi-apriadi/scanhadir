<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition(): array
    {
        $status = $this->faker->randomElement([
            'present', 'present', 'present', 'late', 'sick', 'excused', 'absent',
        ]);

        $checkIn = null;
        $checkOut = null;

        if (in_array($status, ['present', 'late'], true)) {
            $checkIn = $status === 'late' ? $this->faker->time('H:i:s', '08:30:00') : $this->faker->time('H:i:s', '07:30:00');
            $checkOut = $this->faker->time('H:i:s', '17:30:00');
        }

        return [
            'student_id' => Student::query()->inRandomOrder()->value('id') ?? Student::factory(),
            'date' => $this->faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            'status' => $status,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'notes' => in_array($status, ['sick', 'excused', 'absent'], true) ? $this->faker->sentence() : null,
        ];
    }
}
