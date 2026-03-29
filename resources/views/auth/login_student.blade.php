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
            <div class="text-center mb-10">
                <h1 class="font-headline text-3xl font-bold text-on-background mb-3 tracking-tight">Student Portal</h1>
                <p class="font-body text-on-surface-variant text-sm">Masuk untuk mencatat kehadiran presisi Anda.</p>
            </div>

            <form action="{{ route('student.dashboard') }}" class="space-y-6" method="GET">
                <div class="space-y-2">
                    <label class="font-label text-xs font-semibold text-on-surface-variant uppercase tracking-wider ml-1">Email Mahasiswa</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-outline group-focus-within:text-primary transition-colors">alternate_email</span>
                        </div>
                        <input class="w-full pl-12 pr-4 py-4 bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary/20 focus:bg-surface-container-highest transition-all outline-none text-on-surface placeholder:text-outline/60 font-medium" placeholder="nama@kampus.ac.id" type="email" required/>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="font-label text-xs font-semibold text-on-surface-variant uppercase tracking-wider ml-1">Kata Sandi</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-outline group-focus-within:text-primary transition-colors">lock_open</span>
                        </div>
                        <input class="w-full pl-12 pr-12 py-4 bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary/20 focus:bg-surface-container-highest transition-all outline-none text-on-surface placeholder:text-outline/60 font-medium" placeholder="••••••••" type="password" required/>
                    </div>
                </div>

                <div class="flex items-center justify-between pb-2">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input class="w-5 h-5 rounded border-outline-variant text-primary focus:ring-primary/30 transition-all cursor-pointer" type="checkbox"/>
                        <span class="text-sm text-on-surface-variant group-hover:text-on-surface transition-colors">Ingat saya</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm text-primary font-semibold hover:underline">Lupa Password?</a>
                </div>

                <button class="w-full py-4 px-6 bg-gradient-to-r from-primary to-primary-container text-white font-headline font-bold text-lg rounded-xl shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200" type="submit">
                    Masuk Sekarang
                </button>
            </form>

            <div class="mt-8 text-center">
                <p class="text-sm text-on-surface-variant">Bukan Siswa? <a href="{{ route('admin.dashboard') }}" class="text-primary font-bold hover:underline">Masuk Console Admin</a></p>
            </div>

            <!-- Demo Credentials (Dev Only) -->
            <div class="mt-8 p-6 bg-white/5 border border-white/10 rounded-2xl backdrop-blur-md">
                <div class="flex items-center gap-2 mb-3">
                    <span class="material-symbols-outlined text-primary text-sm">terminal</span>
                    <p class="font-headline text-xs font-bold text-on-surface uppercase tracking-widest">Siswa Demo</p>
                </div>
                <div class="flex justify-between items-center text-on-surface-variant bg-surface-container-highest/30 p-3 rounded-lg font-mono text-xs">
                    <span class="font-semibold text-primary">ID:</span>
                    <span>siswa@scanhadir.com / siswa123</span>
                </div>
            </div>

        </div>
    </div>

    <footer class="text-center pb-10">
        <p class="text-xs font-body text-slate-400">© {{ date('Y') }} ScanHadir. Precision Attendance.</p>
    </footer>
</main>
@endsection
