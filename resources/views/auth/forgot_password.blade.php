@extends('layouts.guest')

@section('content')
<div class="relative z-10 w-full max-w-[480px]">
    <div class="bg-white border border-slate-100 rounded-2xl shadow-[0px_20px_40px_rgba(13,28,46,0.06)] p-8 md:p-12">
        <!-- Icon Header -->
        <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-8">
            <span class="material-symbols-outlined text-primary text-3xl">lock_reset</span>
        </div>
        
        <!-- Text Content -->
        <div class="space-y-3 mb-10">
            <h1 class="font-headline text-3xl md:text-4xl text-on-surface font-bold tracking-tight">
                Lupa Kata Sandi?
            </h1>
            <p class="text-slate-500 text-sm leading-relaxed">
                Masukkan Email atau NISN Anda untuk menerima instruksi pemulihan kata sandi.
            </p>
        </div>

        <!-- Form Section -->
        <form class="space-y-8" action="#" method="POST">
            @csrf
            <div class="space-y-2">
                <label class="text-[0.75rem] font-bold font-label text-slate-400 uppercase tracking-widest px-1" for="recovery_id">
                    Email / NISN
                </label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <span class="material-symbols-outlined text-slate-300 group-focus-within:text-primary transition-colors">mail</span>
                    </div>
                    <input class="block w-full pl-12 pr-4 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-primary/20 focus:bg-white transition-all text-on-surface placeholder:text-slate-300 font-medium" 
                           id="recovery_id" 
                           name="recovery_id" 
                           placeholder="nama@email.com atau 12345678" 
                           type="text">
                </div>
            </div>

            <!-- Primary Action -->
            <button class="w-full py-4 px-6 bg-gradient-to-r from-primary to-primary-container text-on-primary font-bold rounded-2xl flex items-center justify-center gap-3 shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all duration-200" type="submit">
                <span class="font-label uppercase tracking-widest text-sm">Kirim Instruksi</span>
                <span class="material-symbols-outlined text-xl">arrow_forward</span>
            </button>

            <!-- Secondary Link -->
            <div class="pt-4 flex justify-center">
                <a class="inline-flex items-center gap-2 text-slate-400 hover:text-primary transition-colors font-bold group" href="{{ route('login.student') }}">
                    <span class="material-symbols-outlined text-xl group-hover:-translate-x-1 transition-transform">arrow_back</span>
                    <span class="text-xs uppercase tracking-widest">Kembali ke Login</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Visual Support -->
    <div class="mt-8 text-center text-slate-300">
        <p class="text-[10px] font-black uppercase tracking-[0.2em]">
            Sistem Kehadiran Digital • Terenkripsi
        </p>
    </div>
</div>
@endsection
