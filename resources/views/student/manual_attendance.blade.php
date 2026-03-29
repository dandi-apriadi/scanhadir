@extends('layouts.student')

@section('content')
<div class="space-y-8">
    @if (session('status'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Header Section -->
    <section class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-2">
                <a href="{{ route('student.dashboard') }}" class="hover:text-indigo-600 cursor-pointer">Dashboard</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-indigo-600">Manual Entry</span>
            </nav>
            <h1 class="text-3xl font-bold text-on-background tracking-tight">Presensi Manual</h1>
            <p class="text-sm text-slate-500 mt-1">Lakukan pencatatan kehadiran siswa secara manual untuk kasus khusus.</p>
        </div>
        <div class="bg-indigo-50 px-4 py-2 rounded-xl flex items-center gap-3 border border-indigo-100/50">
            <span class="material-symbols-outlined text-indigo-600">info</span>
            <span class="text-xs font-medium text-indigo-700">Pastikan data yang diinput sudah sesuai dengan bukti fisik.</span>
        </div>
    </section>

    <!-- Bento Layout Content -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Search Student Module (Left Column) -->
        <div class="lg:col-span-5 flex flex-col gap-6">
            <div class="bg-surface-container-low rounded-xl p-6 relative overflow-hidden">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Cari Nama Siswa / NISN</label>
                <div class="relative">
                    <input class="w-full bg-surface-container-lowest border-none ring-1 ring-outline-variant/20 rounded-xl px-4 py-3 focus:ring-primary focus:ring-offset-0 transition-all text-sm font-medium" placeholder="Ketik NISN atau Nama..." type="text" value="{{ $student_name }}" readonly/>
                </div>
            </div>
            <!-- Selected Student Display -->
            <div class="bg-surface-container-highest rounded-xl p-8 flex flex-col items-center text-center gap-4 relative overflow-hidden group">
                <div class="relative">
                    <img alt="Student preview" class="w-24 h-24 rounded-2xl object-cover ring-4 ring-white shadow-lg" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDXlLwOeOVe1zP4wmCSIX50RAmUHP7v4fQHocUPNZdY7oXGTGWDPz2JF3In0KqPsW64TrnSyUSmcZS9Clpkik0PbaWJZ33m2hU3MvthipH1s2JVPfEgbyfYGcINArLmx0NtvT9laJSwm4TlEeHAm0ryhnfx4T09PCc58ZOHr2k1nIRVaLb34ADnoAuDB_cAJfhQ3J5bfxmJU5FNYJ3fGVHP99qszRckuTK_gh183GYy413aBjeI8tL308p0oanuid1FrKUJ0bguz8wS"/>
                    <div class="absolute -bottom-2 -right-2 bg-indigo-600 text-white p-1 rounded-lg">
                        <span class="material-symbols-outlined text-sm">verified_user</span>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-on-surface tracking-tight">{{ $student_name }}</h3>
                    <p class="text-sm font-semibold text-primary/80">{{ $class }}</p>
                    <p class="text-xs text-slate-400 mt-1 uppercase tracking-tighter">Siswa Aktif • {{ date('Y', strtotime('-1 year')) }}/{{ date('Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Form Entry Module (Right Column) -->
        <div class="lg:col-span-7">
            <form method="POST" action="{{ route('student.manual.store') }}" class="bg-surface-container-lowest rounded-2xl p-8 shadow-sm ring-1 ring-slate-100 flex flex-col gap-8">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Status Kehadiran</label>
                    <div class="flex flex-wrap gap-4">
                        <label class="flex-1 min-w-[120px] relative group cursor-pointer">
                            <input class="peer sr-only" name="status" type="radio" value="present" {{ old('status', 'present') === 'present' ? 'checked' : '' }}/>
                            <div class="flex flex-col items-center gap-2 p-4 rounded-xl border-2 border-slate-100 peer-checked:border-primary peer-checked:bg-primary/10 transition-all hover:bg-slate-50">
                                <span class="material-symbols-outlined text-slate-400 peer-checked:text-primary">check_circle</span>
                                <span class="text-xs font-bold text-slate-600 peer-checked:text-primary">Hadir</span>
                            </div>
                        </label>
                        <label class="flex-1 min-w-[120px] relative group cursor-pointer">
                            <input class="peer sr-only" name="status" type="radio" value="late" {{ old('status') === 'late' ? 'checked' : '' }}/>
                            <div class="flex flex-col items-center gap-2 p-4 rounded-xl border-2 border-slate-100 peer-checked:border-secondary peer-checked:bg-secondary/10 transition-all hover:bg-slate-50">
                                <span class="material-symbols-outlined text-slate-400 peer-checked:text-secondary">history</span>
                                <span class="text-xs font-bold text-slate-600 peer-checked:text-secondary">Terlambat</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex flex-col gap-2">
                        <input type="hidden" name="date" value="{{ old('date', now()->toDateString()) }}">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Waktu Presensi</label>
                        <div class="relative">
                            <input name="check_in" class="w-full bg-slate-50 border-none ring-1 ring-slate-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-primary" type="time" value="{{ old('check_in', now()->format('H:i')) }}" required/>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Keterangan Tambahan</label>
                    <textarea name="notes" class="w-full bg-slate-50 border-none ring-1 ring-slate-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-primary transition-all" placeholder="Contoh: Lupa membawa kartu, Masalah transportasi, dsb." rows="3">{{ old('notes') }}</textarea>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                    <button class="px-6 py-3 text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors uppercase tracking-widest">
                        Batal
                    </button>
                    <button type="submit" class="bg-primary text-white px-10 py-4 rounded-xl font-bold text-sm tracking-wide shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center gap-3">
                        <span class="material-symbols-outlined text-lg">save</span>
                        SIMPAN PRESENSI
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
