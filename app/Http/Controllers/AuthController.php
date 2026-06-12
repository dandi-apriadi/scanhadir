<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Handle login request. Role is optional for the current login form, but
     * still validated when clients submit it explicitly.
     */
    public function processLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'role' => ['nullable', 'in:admin,teacher,dosen,student'],
        ], [
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password harus diisi',
            'role.in' => 'Role tidak valid',
        ]);


        // Attempt authentication
        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
            $user = Auth::user();

            if (!empty($credentials['role']) && !$this->roleMatchesLoginSelection($user->role, $credentials['role'])) {
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

    public function showForgotPassword()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.forgot_password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(
            ['email' => ['required', 'email']],
            ['email.required' => 'Email harus diisi', 'email.email' => 'Format email tidak valid']
        );

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'Link reset password sudah dikirim ke email Anda.');
        }

        throw ValidationException::withMessages([
            'email' => __($status),
        ]);
    }

    public function showResetPassword(string $token)
    {
        return view('auth.reset_password', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ], [
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min'       => 'Password minimal 8 karakter.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(['password' => Hash::make($password)])
                     ->setRememberToken(Str::random(60));
                $user->save();
                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('auth.login')
                ->with('status', 'Password berhasil direset. Silakan login.');
        }

        throw ValidationException::withMessages([
            'email' => __($status),
        ]);
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
