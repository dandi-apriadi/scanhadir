<?php

namespace Database\Factories;

use App\Models\SemesterAkademik;
use Illuminate\Database\Eloquent\Factories\Factory;

class SemesterAkademikFactory extends Factory
{
    protected $model = SemesterAkademik::class;

    public function definition(): array
    {
        $year = date('Y');
        return [
            'nama_semester' => $this->faker->randomElement(['Ganjil', 'Genap']),
            'tahun_ajaran' => "$year/" . ($year + 1),
            'tanggal_mulai' => now()->startOfYear(),
            'tanggal_selesai' => now()->endOfYear(),
            'is_active' => true,
        ];
    }
}
