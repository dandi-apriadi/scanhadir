<?php

namespace App\Livewire;

use App\Models\Student;
use App\Rules\NISNFormat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class StudentLogin extends Component
{
    public $identifier = '';
    public $password = '';
    public $remember = false;
    public $isLoading = false;
    public $errorMessage = '';

    public function login()
    {
        $validator = Validator::make(
            ['identifier' => $this->identifier, 'password' => $this->password],
            [
                'identifier' => [
                    'required',
                    'string',
                    function (string $attribute, mixed $value, \Closure $fail): void {
                        $value = (string) $value;
                        $isEmail = filter_var($value, FILTER_VALIDATE_EMAIL) !== false;

                        if ($isEmail) {
                            return;
                        }

                        $nisnRule = new NISNFormat();
                        $nisnRule->validate($attribute, $value, $fail);
                    },
                ],
                'password' => ['required', 'string', 'min:6'],
            ],
            [
                'identifier.required' => 'Email atau NISN harus diisi.',
                'password.required' => 'Password harus diisi.',
                'password.min' => 'Password minimal 6 karakter.',
            ]
        );

        if ($validator->fails()) {
            $this->addError('identifier', $validator->errors()->first('identifier'));

            if ($validator->errors()->has('password')) {
                $this->addError('password', $validator->errors()->first('password'));
            }

            return;
        }

        $this->isLoading = true;
        $this->errorMessage = '';

        try {
            $loginEmail = $this->resolveEmailFromIdentifier($this->identifier);

            if ($loginEmail && Auth::attempt(['email' => $loginEmail, 'password' => $this->password], $this->remember)) {
                session()->regenerate();

                $user = Auth::user();
                
                // Redirect based on role
                return match($user->role) {
                    'admin' => redirect()->intended('/admin'),
                    'teacher' => redirect()->intended('/teacher'),
                    'student' => redirect()->intended('/dashboard'),
                    default => redirect()->intended('/dashboard'),
                };
            }

            // Invalid credentials
            $this->errorMessage = 'Email/NISN atau password salah.';
            $this->addError('identifier', $this->errorMessage);
        } catch (\Exception $e) {
            $this->errorMessage = 'Terjadi kesalahan saat login. Silakan coba lagi.';
            \Log::error('Login error: ' . $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    private function resolveEmailFromIdentifier(string $identifier): ?string
    {
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return $identifier;
        }

        return Student::query()
            ->where('nisn', $identifier)
            ->with('user:id,email')
            ->first()
            ?->user
            ?->email;
    }

    public function render()
    {
        return view('livewire.student-login')->layout('layouts.app');
    }
}

