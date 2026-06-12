@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex mesh-gradient-bg">

    {{-- LEFT PANEL — Branding --}}
    <div class="hidden lg:flex lg:w-1/2 xl:w-[55%] flex-col justify-between p-12 relative overflow-hidden">
        {{-- decorative blobs --}}
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-primary/10 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-0 right-0 w-80 h-80 bg-primary-container/10 rounded-full blur-[100px] pointer-events-none"></div>

        {{-- Logo --}}
        <div class="relative z-10 flex items-center gap-3">
            <div class="w-11 h-11 rounded-2xl bg-primary flex items-center justify-center shadow-lg shadow-primary/30">
                <span class="material-symbols-outlined text-white" style="font-variation-settings:'FILL' 1;">qr_code_scanner</span>
            </div>
            <span class="text-2xl font-extrabold text-indigo-900 font-headline tracking-tight">ScanHadir</span>
        </div>

        {{-- Main copy --}}
        <div class="relative z-10 space-y-6">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-primary/10 border border-primary/20 rounded-full text-primary text-xs font-bold uppercase tracking-widest">
                <span class="material-symbols-outlined text-[14px]">verified</span>
                Sistem Presensi Digital
            </div>
            <h1 class="text-5xl xl:text-6xl font-extrabold text-indigo-900 font-headline tracking-tight leading-[1.05]">
                Satu Portal,<br>Semua Akses.
            </h1>
            <p class="text-lg text-slate-500 font-body max-w-md leading-relaxed">
                Masuk sebagai <strong class="text-indigo-700">Admin</strong>, <strong class="text-indigo-700">Guru</strong>, atau <strong class="text-indigo-700">Siswa</strong> —
                sistem mengarahkan Anda secara otomatis sesuai peran.
            </p>

            {{-- Feature highlights --}}
            <div class="grid grid-cols-2 gap-4 pt-4">
                <div class="flex items-start gap-3 p-4 bg-white/60 backdrop-blur-sm border border-white/80 rounded-2xl shadow-sm">
                    <span class="material-symbols-outlined text-primary mt-0.5" style="font-variation-settings:'FILL' 1;">qr_code_2</span>
                    <div>
                        <p class="text-sm font-bold text-slate-800">QR Absensi</p>
                        <p class="text-xs text-slate-400 mt-0.5">Scan cepat & akurat</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-4 bg-white/60 backdrop-blur-sm border border-white/80 rounded-2xl shadow-sm">
                    <span class="material-symbols-outlined text-emerald-600 mt-0.5" style="font-variation-settings:'FILL' 1;">analytics</span>
                    <div>
                        <p class="text-sm font-bold text-slate-800">Laporan Real-time</p>
                        <p class="text-xs text-slate-400 mt-0.5">Grafik & rekap lengkap</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-4 bg-white/60 backdrop-blur-sm border border-white/80 rounded-2xl shadow-sm">
                    <span class="material-symbols-outlined text-amber-600 mt-0.5" style="font-variation-settings:'FILL' 1;">supervisor_account</span>
                    <div>
                        <p class="text-sm font-bold text-slate-800">Wali Kelas</p>
                        <p class="text-xs text-slate-400 mt-0.5">Monitor kelas perwalian</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-4 bg-white/60 backdrop-blur-sm border border-white/80 rounded-2xl shadow-sm">
                    <span class="material-symbols-outlined text-rose-500 mt-0.5" style="font-variation-settings:'FILL' 1;">notifications_active</span>
                    <div>
                        <p class="text-sm font-bold text-slate-800">Notifikasi Izin</p>
                        <p class="text-xs text-slate-400 mt-0.5">Approval langsung</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <p class="relative z-10 text-xs text-slate-400 font-body">© {{ date('Y') }} ScanHadir · SMK / SMA Digital</p>
    </div>

    {{-- RIGHT PANEL — Login Form --}}
    <div class="w-full lg:w-1/2 xl:w-[45%] flex items-center justify-center p-6 lg:p-12">
        <div class="w-full max-w-[440px]">

            {{-- Mobile logo --}}
            <div class="flex items-center gap-3 mb-8 lg:hidden">
                <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center shadow-lg shadow-primary/30">
                    <span class="material-symbols-outlined text-white text-xl" style="font-variation-settings:'FILL' 1;">qr_code_scanner</span>
                </div>
                <span class="text-xl font-extrabold text-indigo-900 font-headline">ScanHadir</span>
            </div>

            <div class="bg-white/70 backdrop-blur-xl border border-white/80 rounded-3xl shadow-xl shadow-indigo-100/50 p-8 md:p-10">
                <div class="mb-8">
                    <h2 class="text-2xl font-extrabold text-indigo-900 font-headline tracking-tight">Masuk ke Portal</h2>
                    <p class="text-sm text-slate-400 mt-1">Gunakan akun yang diberikan oleh admin sekolah.</p>
                </div>

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-2xl flex items-start gap-3">
                        <span class="material-symbols-outlined text-rose-500 flex-shrink-0 mt-0.5 text-[20px]" style="font-variation-settings:'FILL' 1;">error</span>
                        <div>
                            <p class="text-sm font-bold text-rose-700 mb-1">Gagal Masuk</p>
                            @foreach ($errors->all() as $error)
                                <p class="text-xs text-rose-500">{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if (session('status'))
                    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center gap-3">
                        <span class="material-symbols-outlined text-emerald-500 text-[20px]" style="font-variation-settings:'FILL' 1;">check_circle</span>
                        <p class="text-sm font-bold text-emerald-700">{{ session('status') }}</p>
                    </div>
                @endif

                <form action="{{ route('auth.login.process') }}" method="POST" class="space-y-5">
                    @csrf

                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest ml-1">Email</label>
                        <div class="relative group">
                            <span class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                                <span class="material-symbols-outlined text-slate-300 group-focus-within:text-primary transition-colors text-[20px]">alternate_email</span>
                            </span>
                            <input
                                class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm text-slate-800 placeholder:text-slate-300 font-medium outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40 focus:bg-white transition-all @error('email') border-rose-300 bg-rose-50 @enderror"
                                placeholder="nama@email.com"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus />
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest ml-1">Kata Sandi</label>
                        <div class="relative group" x-data="{ show: false }">
                            <span class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                                <span class="material-symbols-outlined text-slate-300 group-focus-within:text-primary transition-colors text-[20px]">lock</span>
                            </span>
                            <input
                                class="w-full pl-11 pr-12 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm text-slate-800 placeholder:text-slate-300 font-medium outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40 focus:bg-white transition-all @error('password') border-rose-300 bg-rose-50 @enderror"
                                placeholder="••••••••"
                                :type="show ? 'text' : 'password'"
                                name="password"
                                required />
                            <button type="button" @click="show = !show"
                                class="absolute inset-y-0 right-4 flex items-center text-slate-300 hover:text-slate-500 transition-colors">
                                <span class="material-symbols-outlined text-[20px]" x-text="show ? 'visibility_off' : 'visibility'"></span>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary/30 cursor-pointer" type="checkbox" name="remember" />
                            <span class="text-sm text-slate-500">Ingat saya</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="text-sm font-semibold text-primary hover:underline">Lupa password?</a>
                    </div>

                    <button type="submit"
                        class="w-full py-3.5 px-6 bg-gradient-to-r from-primary to-primary-container text-white font-headline font-bold text-base rounded-2xl shadow-lg shadow-primary/25 hover:shadow-xl hover:shadow-primary/30 hover:scale-[1.01] active:scale-[0.99] transition-all duration-200">
                        Masuk Sekarang
                        <span class="material-symbols-outlined text-[18px] align-middle ml-1">arrow_forward</span>
                    </button>
                </form>

                {{-- Role indicator --}}
                <div class="mt-6 flex items-center gap-2 justify-center">
                    <span class="flex items-center gap-1.5 px-3 py-1 bg-slate-50 border border-slate-200 rounded-full text-[11px] font-bold text-slate-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block"></span> Admin
                    </span>
                    <span class="flex items-center gap-1.5 px-3 py-1 bg-slate-50 border border-slate-200 rounded-full text-[11px] font-bold text-slate-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 inline-block"></span> Guru
                    </span>
                    <span class="flex items-center gap-1.5 px-3 py-1 bg-slate-50 border border-slate-200 rounded-full text-[11px] font-bold text-slate-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 inline-block"></span> Siswa
                    </span>
                </div>
            </div>

            {{-- Demo credentials --}}
            <div class="mt-4 p-5 bg-white/50 backdrop-blur-sm border border-white/70 rounded-2xl">
                <div class="flex items-center gap-2 mb-3">
                    <span class="material-symbols-outlined text-slate-400 text-[16px]">terminal</span>
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Demo Credentials</p>
                </div>
                <div class="space-y-2 font-mono text-[11px]">
                    <div class="flex justify-between items-center bg-white/60 rounded-xl px-3 py-2">
                        <span class="font-bold text-rose-500">Admin</span>
                        <span class="text-slate-500">admin@scanhadir.com / admin123</span>
                    </div>
                    <div class="flex justify-between items-center bg-white/60 rounded-xl px-3 py-2">
                        <span class="font-bold text-indigo-500">Guru</span>
                        <span class="text-slate-500">guru@scanhadir.com / guru123</span>
                    </div>
                    <div class="flex justify-between items-center bg-white/60 rounded-xl px-3 py-2">
                        <span class="font-bold text-amber-500">Siswa</span>
                        <span class="text-slate-500">siswa@scanhadir.com / siswa123</span>
                    </div>
                </div>
            </div>

            <p class="text-center text-[11px] text-slate-400 mt-6">© {{ date('Y') }} ScanHadir · Sistem Presensi Digital</p>
        </div>
    </div>

</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection
