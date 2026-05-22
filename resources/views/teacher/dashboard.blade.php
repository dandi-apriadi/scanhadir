@extends('layouts.teacher')

@section('content')
<div class="space-y-6">
    <!-- Quick Navigation -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('dosen-courses') }}" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 hover:shadow-md transition-shadow flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-primary text-2xl">menu_book</span>
            </div>
            <div>
                <h3 class="font-bold text-on-surface">Mata Kuliah Saya</h3>
                <p class="text-xs text-slate-400">Kelola sesi presensi per semester</p>
            </div>
        </a>
        <a href="{{ route('teacher.analytics') }}" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 hover:shadow-md transition-shadow flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center">
                <span class="material-symbols-outlined text-emerald-600 text-2xl">analytics</span>
            </div>
            <div>
                <h3 class="font-bold text-on-surface">Analitik Kehadiran</h3>
                <p class="text-xs text-slate-400">Statistik dan tren kehadiran</p>
            </div>
        </a>
        <a href="{{ route('teacher.reports') }}" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 hover:shadow-md transition-shadow flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center">
                <span class="material-symbols-outlined text-indigo-600 text-2xl">assessment</span>
            </div>
            <div>
                <h3 class="font-bold text-on-surface">Laporan</h3>
                <p class="text-xs text-slate-400">Export dan filter data kehadiran</p>
            </div>
        </a>
    </div>

    <!-- Livewire Dashboard -->
    <livewire:teacher-dashboard />
</div>
@endsection
