<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NISNFormat implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!preg_match('/^\d{13}$/', (string) $value)) {
            $fail('NISN harus terdiri dari 13 digit angka.');
        }
    }
}
