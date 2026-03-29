<?php

namespace App\Http\Requests;

use App\Rules\NISNFormat;
use App\Rules\QRCodeUnique;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $studentId = $this->route('student')?->id ?? $this->route('record')?->id;

        return [
            'user_id' => ['required', 'exists:users,id'],
            'class_id' => ['required', 'exists:classes,id'],
            'nisn' => [
                'required',
                'string',
                new NISNFormat(),
                Rule::unique('students', 'nisn')->ignore($studentId),
            ],
            'qr_code' => ['nullable', 'string', new QRCodeUnique($studentId)],
            'email' => ['nullable', 'email', Rule::unique('users', 'email')->ignore($this->input('user_id'))],
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
