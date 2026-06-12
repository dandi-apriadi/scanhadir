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
                Masukkan email akun Anda. Kami akan mengirimkan link untuk membuat password baru.
            </p>
        </div>

        @if (session('status'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center gap-3">
                <span class="material-symbols-outlined text-emerald-500 text-[20px]" style="font-variation-settings:'FILL' 1;">check_circle</span>
                <p class="text-sm font-bold text-emerald-700">{{ session('status') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-2xl flex items-start gap-3">
                <span class="material-symbols-outlined text-rose-500 flex-shrink-0 mt-0.5 text-[20px]" style="font-variation-settings:'FILL' 1;">error</span>
                <p class="text-sm font-bold text-rose-700">{{ $errors->first() }}</p>
            </div>
        @endif

        <!-- Form Section -->
        <form class="space-y-8" action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="space-y-2">
                <label class="text-[0.75rem] font-bold font-label text-slate-400 uppercase tracking-widest px-1" for="email">
                    Email
                </label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <span class="material-symbols-outlined text-slate-300 group-focus-within:text-primary transition-colors">alternate_email</span>
                    </div>
                    <input class="block w-full pl-12 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-primary/20 focus:bg-white transition-all text-on-surface placeholder:text-slate-300 font-medium @error('email') border-rose-300 bg-rose-50 @enderror"
                           id="email"
                           name="email"
                           placeholder="nama@email.com"
                           type="email"
                           value="{{ old('email') }}"
                           required
                           autofocus>
                </div>
            </div>

            <!-- Primary Action -->
            <button class="w-full py-4 px-6 bg-gradient-to-r from-primary to-primary-container text-on-primary font-bold rounded-2xl flex items-center justify-center gap-3 shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all duration-200" type="submit">
                <span class="font-label uppercase tracking-widest text-sm">Kirim Link Reset</span>
                <span class="material-symbols-outlined text-xl">send</span>
            </button>

            <!-- Secondary Link -->
            <div class="pt-4 flex justify-center">
                <a class="inline-flex items-center gap-2 text-slate-400 hover:text-primary transition-colors font-bold group" href="{{ route('auth.login') }}">
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
