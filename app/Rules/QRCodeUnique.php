<?php

namespace App\Rules;

use App\Models\Student;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class QRCodeUnique implements ValidationRule
{
    public function __construct(private readonly ?int $ignoreStudentId = null)
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $exists = Student::query()
            ->where('qr_code', (string) $value)
            ->when($this->ignoreStudentId, fn ($query) => $query->where('id', '!=', $this->ignoreStudentId))
            ->exists();

        if ($exists) {
            $fail('QR Code sudah digunakan oleh siswa lain.');
        }
    }
}
