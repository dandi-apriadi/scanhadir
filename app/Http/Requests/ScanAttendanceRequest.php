<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScanAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Accept flexible QR code formats in tests and real usage.
            'code' => ['required', 'string', 'max:64'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Kode QR wajib diisi.',
            'code.regex' => 'Format kode QR tidak valid.',
            'code.max' => 'Kode QR terlalu panjang.',
        ];
    }
}
