@extends('layouts.guest')

@section('content')
<main class="min-h-screen flex flex-col bg-indigo-950">
    <nav class="w-full max-w-7xl mx-auto flex justify-between items-center px-8 py-6">
        <a href="{{ route('landing') }}" class="flex items-center gap-2">
            <span class="material-symbols-outlined text-white text-3xl">admin_panel_settings</span>
            <span class="text-2xl font-bold tracking-tight text-white font-headline">ScanHadir</span>
        </a>
    </nav>

    <div class="flex-grow flex items-center justify-center px-6 py-12">
        <div class="bg-white/10 backdrop-blur-xl w-full max-w-[500px] rounded-2xl p-10 md:p-12 shadow-2xl transition-all duration-300 border border-white/20">
            <div class="text-center mb-10">
                <h1 class="font-headline text-3xl font-bold text-white mb-3 tracking-tight">Admin Console</h1>
                <p class="font-body text-indigo-200 text-sm">Masuk ke pusat kendali manajemen kehadiran.</p>
            </div>

            <form action="{{ route('admin.dashboard') }}" class="space-y-6" method="GET">
                <div class="space-y-2">
                    <label class="font-label text-xs font-semibold text-indigo-300 uppercase tracking-wider ml-1">Nama Pengguna Admin</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-indigo-400">admin_panel_settings</span>
                        </div>
                        <input class="w-full pl-12 pr-4 py-4 bg-white/5 border border-white/10 rounded-xl focus:ring-2 focus:ring-primary-container focus:bg-white/10 transition-all outline-none text-white placeholder:text-indigo-400/60 font-medium" placeholder="admin_username" type="text" required/>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="font-label text-xs font-semibold text-indigo-300 uppercase tracking-wider ml-1">Kata Sandi</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-indigo-400">lock_open</span>
                        </div>
                        <input class="w-full pl-12 pr-12 py-4 bg-white/5 border border-white/10 rounded-xl focus:ring-2 focus:ring-primary-container focus:bg-white/10 transition-all outline-none text-white placeholder:text-indigo-400/60 font-medium" placeholder="••••••••" type="password" required/>
                    </div>
                </div>

                <button class="w-full py-4 px-6 bg-primary-container text-white font-headline font-bold text-lg rounded-xl shadow-lg shadow-black/40 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200" type="submit">
                    Masuk Console
                </button>
            </form>

            <div class="mt-8 text-center">
                <p class="text-sm text-indigo-300">Bukan Admin? <a href="{{ route('login.student') }}" class="text-white font-bold hover:underline">Masuk Portal Siswa</a></p>
            </div>
        </div>
    </div>

    <footer class="text-center pb-10">
        <p class="text-xs font-body text-indigo-400">© {{ date('Y') }} ScanHadir. High Fidelity Admin Dashboard.</p>
    </footer>
</main>
@endsection
