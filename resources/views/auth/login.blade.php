@extends('layouts.guest')

@section('content')
<main class="min-h-screen flex flex-col mesh-gradient-bg">
    <nav class="w-full max-w-7xl mx-auto flex justify-between items-center px-8 py-6">
        <a href="{{ route('landing') }}" class="flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-3xl">qr_code_2</span>
            <span class="text-2xl font-bold tracking-tight text-indigo-700 font-headline">ScanHadir</span>
        </a>
    </nav>

    <div class="flex-grow flex items-center justify-center px-6 py-12">
        <div class="glass-card w-full max-w-[500px] rounded-xl p-10 md:p-12 shadow-2xl transition-all duration-300">
            <div class="text-center mb-8">
                <h1 class="font-headline text-3xl font-bold text-on-background mb-3 tracking-tight">Masuk ScanHadir</h1>
                <p class="font-body text-on-surface-variant text-sm">Sistem Pencatatan Kehadiran Presisi</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 p-4 bg-error/10 border border-error/30 rounded-lg">
                    <p class="text-sm font-medium text-error mb-2">Gagal Masuk</p>
                    <ul class="text-xs text-error space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('auth.login.process') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Role Selection Tabs -->
                <div class="grid grid-cols-3 gap-2 p-1 bg-surface-container-low rounded-lg mb-8">
                    <label class="relative cursor-pointer">
                        <input type="radio" name="role" value="student" class="sr-only peer" checked/>
                        <div class="peer-checked:bg-primary peer-checked:text-white peer-checked:shadow-lg px-3 py-2 rounded-md text-center text-sm font-semibold transition-all duration-200 text-on-surface-variant hover:bg-surface-container-highest">
                            <span class="material-symbols-outlined text-lg leading-none">school</span>
                            <span class="block text-xs mt-1">Siswa</span>
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="role" value="teacher" class="sr-only peer"/>
                        <div class="peer-checked:bg-primary peer-checked:text-white peer-checked:shadow-lg px-3 py-2 rounded-md text-center text-sm font-semibold transition-all duration-200 text-on-surface-variant hover:bg-surface-container-highest">
                            <span class="material-symbols-outlined text-lg leading-none">person_check</span>
                            <span class="block text-xs mt-1">Guru</span>
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="role" value="admin" class="sr-only peer"/>
                        <div class="peer-checked:bg-primary peer-checked:text-white peer-checked:shadow-lg px-3 py-2 rounded-md text-center text-sm font-semibold transition-all duration-200 text-on-surface-variant hover:bg-surface-container-highest">
                            <span class="material-symbols-outlined text-lg leading-none">admin_panel_settings</span>
                            <span class="block text-xs mt-1">Admin</span>
                        </div>
                    </label>
                </div>

                <!-- Email Field -->
                <div class="space-y-2">
                    <label class="font-label text-xs font-semibold text-on-surface-variant uppercase tracking-wider ml-1">Email</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-outline group-focus-within:text-primary transition-colors">alternate_email</span>
                        </div>
                        <input class="w-full pl-12 pr-4 py-4 bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary/20 focus:bg-surface-container-highest transition-all outline-none text-on-surface placeholder:text-outline/60 font-medium @error('email') ring-2 ring-error @enderror" 
                               placeholder="nama@email.com" 
                               type="email" 
                               name="email" 
                               value="{{ old('email') }}"
                               required
                               autofocus/>
                    </div>
                    @error('email')
                        <p class="text-xs text-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="space-y-2">
                    <label class="font-label text-xs font-semibold text-on-surface-variant uppercase tracking-wider ml-1">Kata Sandi</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-outline group-focus-within:text-primary transition-colors">lock_open</span>
                        </div>
                        <input class="w-full pl-12 pr-12 py-4 bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary/20 focus:bg-surface-container-highest transition-all outline-none text-on-surface placeholder:text-outline/60 font-medium @error('password') ring-2 ring-error @enderror" 
                               placeholder="••••••••" 
                               type="password" 
                               name="password"
                               required/>
                    </div>
                    @error('password')
                        <p class="text-xs text-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember & Forgot Password -->
                <div class="flex items-center justify-between pb-2">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input class="w-5 h-5 rounded border-outline-variant text-primary focus:ring-primary/30 transition-all cursor-pointer" type="checkbox" name="remember"/>
                        <span class="text-sm text-on-surface-variant group-hover:text-on-surface transition-colors">Ingat saya</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm text-primary font-semibold hover:underline">Lupa Password?</a>
                </div>

                <!-- Submit Button -->
                <button class="w-full py-4 px-6 bg-gradient-to-r from-primary to-primary-container text-white font-headline font-bold text-lg rounded-xl shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200" type="submit">
                    Masuk Sekarang
                </button>
            </form>

            <!-- Demo Credentials -->
            <div class="mt-8 p-4 bg-surface-container-low rounded-lg text-xs text-on-surface-variant">
                <p class="font-semibold mb-2">💡 Akun Demo:</p>
                <ul class="space-y-1 font-mono">
                    <li><strong>Siswa:</strong> siswa@sekolah.sch.id / siswa123</li>
                    <li><strong>Guru:</strong> guru@sekolah.sch.id / guru123</li>
                    <li><strong>Admin:</strong> admin@sekolah.sch.id / admin123</li>
                </ul>
            </div>
        </div>
    </div>

    <footer class="text-center pb-10">
        <p class="text-xs font-body text-slate-400">© {{ date('Y') }} ScanHadir. Precision Attendance.</p>
    </footer>
</main>
@endsection
