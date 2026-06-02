<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login request with role validation
     */
    public function processLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'role' => ['required', 'in:admin,teacher,dosen,student'],
        ], [
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password harus diisi',
            'role.required' => 'Role harus dipilih',
            'role.in' => 'Role tidak valid',
        ]);


        // Attempt authentication
        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
            $user = Auth::user();

            if (!$this->roleMatchesLoginSelection($user->role, $credentials['role'])) {
                Auth::logout();

                throw ValidationException::withMessages([
                    'email' => 'Email atau password tidak valid.',
                ]);
            }

            // Remember me functionality
            if ($request->filled('remember')) {
                $request->session()->put('auth.remember', true);
            }

            // Redirect based on role
            return $this->redirectByRole($user);
        }

        throw ValidationException::withMessages([
            'email' => 'Email atau password tidak valid.',
        ]);
    }

    /**
     * Redirect user based on their role
     */
    private function redirectByRole($user)
    {
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'dosen', 'teacher' => redirect()->route('teacher.dashboard'),
            'student' => redirect()->route('student.dashboard'),
            default => redirect()->route('landing'),
        };
    }

    private function roleMatchesLoginSelection(string $actualRole, string $selectedRole): bool
    {
        if ($actualRole === $selectedRole) {
            return true;
        }

        return in_array($actualRole, ['teacher', 'dosen'], true)
            && in_array($selectedRole, ['teacher', 'dosen'], true);
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.login')->with('status', 'Anda telah berhasil logout.');
    }
}
