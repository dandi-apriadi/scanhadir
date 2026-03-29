<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Carbon;

class DateRangeValid implements ValidationRule, DataAwareRule
{
    private array $data = [];

    public function __construct(private readonly string $startField = 'start_date')
    {
    }

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $startDate = $this->data[$this->startField] ?? null;

        if (empty($startDate) || empty($value)) {
            return;
        }

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($value);

        if ($end->lt($start)) {
            $fail('Tanggal akhir harus sama atau setelah tanggal mulai.');
        }
    }
}
