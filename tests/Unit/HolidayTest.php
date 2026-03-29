<?php

namespace Tests\Unit;

use App\Models\Holiday;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class HolidayTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_holiday_returns_true_for_holiday_date(): void
    {
        $startDate = now()->toDateString();
        $endDate = now()->addDays(2)->toDateString();

        Holiday::create([
            'name' => 'Test Holiday',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'type' => 'school',
        ]);

        $this->assertTrue(Holiday::isHoliday($startDate));
        $this->assertTrue(Holiday::isHoliday(now()->addDay()->toDateString()));
        $this->assertTrue(Holiday::isHoliday($endDate));
    }

    public function test_is_holiday_returns_false_for_non_holiday_date(): void
    {
        Holiday::create([
            'name' => 'Test Holiday',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'type' => 'school',
        ]);

        $beforeHoliday = now()->subDay()->toDateString();
        $afterHoliday = now()->addDays(3)->toDateString();

        $this->assertFalse(Holiday::isHoliday($beforeHoliday));
        $this->assertFalse(Holiday::isHoliday($afterHoliday));
    }

    public function test_is_holiday_with_no_holidays_defined(): void
    {
        $this->assertFalse(Holiday::isHoliday(now()->toDateString()));
    }

    public function test_holiday_has_proper_fillable_attributes(): void
    {
        $startDate = now()->toDateString();
        $endDate = now()->addDays(5)->toDateString();

        $data = [
            'name' => 'Lebaran',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'type' => 'national',
            'description' => 'Hari Raya Idul Fitri',
        ];

        $holiday = Holiday::create($data);

        $this->assertEquals('Lebaran', $holiday->name);
        $this->assertEquals($startDate, $holiday->start_date->toDateString());
        $this->assertEquals($endDate, $holiday->end_date->toDateString());
        $this->assertEquals('national', $holiday->type);
        $this->assertEquals('Hari Raya Idul Fitri', $holiday->description);
    }

    public function test_holiday_multiple_overlapping_ranges(): void
    {
        Holiday::create([
            'name' => 'Holiday 1',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'type' => 'school',
        ]);

        Holiday::create([
            'name' => 'Holiday 2',
            'start_date' => now()->addDays(1)->toDateString(),
            'end_date' => now()->addDays(4)->toDateString(),
            'type' => 'national',
        ]);

        // Check overlapping date
        $this->assertTrue(Holiday::isHoliday(now()->addDay()->toDateString()));
    }
}
