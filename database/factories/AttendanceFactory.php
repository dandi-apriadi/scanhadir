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
            'Hadir', 'Hadir', 'Hadir', 'Telat', 'Sakit', 'Izin', 'Alpa',
        ]);

        $checkIn = null;
        $checkOut = null;

        if (in_array($status, ['Hadir', 'Telat'], true)) {
            $checkIn = $status === 'Telat' ? $this->faker->time('H:i:s', '08:30:00') : $this->faker->time('H:i:s', '07:30:00');
            $checkOut = $this->faker->time('H:i:s', '17:30:00');
        }

        return [
            'student_id' => Student::query()->inRandomOrder()->value('id') ?? Student::factory(),
            'schedule_id' => null,
            'date' => $this->faker->unique()->dateTimeBetween('-365 days', 'now')->format('Y-m-d'),
            'status' => $status,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'notes' => in_array($status, ['Sakit', 'Izin', 'Alpa'], true) ? $this->faker->sentence() : null,
        ];
    }
}
