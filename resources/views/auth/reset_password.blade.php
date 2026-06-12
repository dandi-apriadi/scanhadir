@extends('layouts.guest')

@section('content')
<div class="relative z-10 w-full max-w-[480px]">
    <div class="bg-white border border-slate-100 rounded-2xl shadow-[0px_20px_40px_rgba(13,28,46,0.06)] p-8 md:p-12">
        <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mb-8">
            <span class="material-symbols-outlined text-primary text-3xl" style="font-variation-settings:'FILL' 1;">lock_reset</span>
        </div>

        <div class="space-y-2 mb-8">
            <h1 class="font-headline text-2xl md:text-3xl text-on-surface font-bold tracking-tight">Buat Password Baru</h1>
            <p class="text-slate-500 text-sm">Masukkan password baru untuk akun Anda.</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-2xl flex items-start gap-3">
                <span class="material-symbols-outlined text-rose-500 flex-shrink-0 mt-0.5 text-[20px]" style="font-variation-settings:'FILL' 1;">error</span>
                <div>
                    @foreach ($errors->all() as $error)
                        <p class="text-sm font-bold text-rose-700">{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        <form class="space-y-5" action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="space-y-1.5">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest ml-1">Email</label>
                <div class="relative group">
                    <span class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                        <span class="material-symbols-outlined text-slate-300 group-focus-within:text-primary transition-colors text-[20px]">alternate_email</span>
                    </span>
                    <input class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40 focus:bg-white transition-all @error('email') border-rose-300 bg-rose-50 @enderror"
                           type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus />
                </div>
            </div>

            <div class="space-y-1.5" x-data="{ show: false }">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest ml-1">Password Baru</label>
                <div class="relative group">
                    <span class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                        <span class="material-symbols-outlined text-slate-300 group-focus-within:text-primary transition-colors text-[20px]">lock</span>
                    </span>
                    <input class="w-full pl-11 pr-12 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40 focus:bg-white transition-all @error('password') border-rose-300 bg-rose-50 @enderror"
                           :type="show ? 'text' : 'password'" name="password" placeholder="Min. 8 karakter" required />
                    <button type="button" @click="show = !show"
                        class="absolute inset-y-0 right-4 flex items-center text-slate-300 hover:text-slate-500 transition-colors">
                        <span class="material-symbols-outlined text-[20px]" x-text="show ? 'visibility_off' : 'visibility'"></span>
                    </button>
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest ml-1">Konfirmasi Password</label>
                <div class="relative group">
                    <span class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                        <span class="material-symbols-outlined text-slate-300 group-focus-within:text-primary transition-colors text-[20px]">lock_clock</span>
                    </span>
                    <input class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40 focus:bg-white transition-all"
                           type="password" name="password_confirmation" placeholder="Ulangi password baru" required />
                </div>
            </div>

            <button type="submit"
                class="w-full py-3.5 px-6 bg-gradient-to-r from-primary to-primary-container text-white font-headline font-bold text-base rounded-2xl shadow-lg shadow-primary/25 hover:scale-[1.01] active:scale-[0.99] transition-all duration-200 mt-2">
                Simpan Password Baru
            </button>
        </form>

        <div class="mt-6 flex justify-center">
            <a class="inline-flex items-center gap-2 text-slate-400 hover:text-primary transition-colors font-bold group" href="{{ route('auth.login') }}">
                <span class="material-symbols-outlined text-xl group-hover:-translate-x-1 transition-transform">arrow_back</span>
                <span class="text-xs uppercase tracking-widest">Kembali ke Login</span>
            </a>
        </div>
    </div>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection
