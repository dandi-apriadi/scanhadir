<?php

namespace App\Http\Requests;

use App\Rules\NISNFormat;
use App\Rules\QRCodeUnique;
use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'class_id' => ['required', 'exists:classes,id'],
            'nisn' => ['required', 'string', new NISNFormat(), 'unique:students,nisn'],
            'qr_code' => ['nullable', 'string', new QRCodeUnique()],
            'email' => ['nullable', 'email', 'unique:users,email'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'User siswa wajib dipilih.',
            'class_id.required' => 'Kelas wajib dipilih.',
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.unique' => 'NISN sudah terdaftar.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
        ];
    }
}
