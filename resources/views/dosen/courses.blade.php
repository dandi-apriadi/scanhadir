@extends('layouts.teacher')

@section('content')
<div class="space-y-8">
    @if (session('status'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
            {{ session('status') }}
        </div>
    @endif
    @if (session('error'))
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">
            {{ session('error') }}
        </div>
    @endif
    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
            {{ session('success') }}
        </div>
    @endif
    @if (session('info'))
        <div class="rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm font-semibold text-blue-700">
            {{ session('info') }}
        </div>
    @endif

    <!-- Breadcrumb & Header -->
    <div class="space-y-4">
        <nav class="flex items-center gap-2 text-xs font-medium text-slate-400">
            <a class="hover:text-primary transition-colors" href="{{ route('teacher.dashboard') }}">Dashboard</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-primary font-semibold">Mata Kuliah Saya</span>
        </nav>
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <h2 class="text-3xl font-headline font-bold text-on-surface tracking-tight">Mata Kuliah Saya</h2>
                <p class="text-slate-500 mt-2 max-w-xl text-sm leading-relaxed">Kelola sesi presensi untuk mata kuliah yang Anda ampu. Jadwal dikelompokkan berdasarkan semester akademik.</p>
            </div>
            @if($activeSession)
                <div class="flex items-center gap-2 px-6 py-3 bg-gradient-to-br from-emerald-500 to-emerald-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-emerald-500/20">
                    <span class="material-symbols-outlined text-lg animate-pulse">radio_button_checked</span>
                    SESI AKTIF: {{ $activeSession['mk_name'] ?? '-' }}
                    <span class="text-xs opacity-75 ml-2">({{ $activeSession['source'] ?? 'MANUAL' }})</span>
                </div>
            @else
                <div class="flex items-center gap-2 px-6 py-3 bg-slate-100 text-slate-500 rounded-xl font-bold text-sm">
                    <span class="material-symbols-outlined text-lg">radio_button_unchecked</span>
                    Tidak Ada Sesi Aktif
                </div>
            @endif
        </div>
    </div>

    <!-- Active Session Controls -->
    @if($activeSession)
        <div class="bg-white rounded-2xl border border-emerald-200 shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center">
                        <span class="material-symbols-outlined text-emerald-600 text-2xl">play_circle</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-on-surface">{{ $activeSession['mk_name'] ?? '-' }} ({{ $activeSession['mk_kode'] ?? '-' }})</h3>
                        <p class="text-sm text-slate-500">Kelas: {{ $activeSession['kelas_name'] ?? '-' }} • Mulai: {{ \Carbon\Carbon::parse($activeSession['started_at'])->format('H:i') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('dosen-schedule.detail', ['subject_id' => $activeSession['mata_kuliah_id'], 'class_id' => $activeSession['kelas_id']]) }}" class="px-4 py-2 bg-primary/10 text-primary rounded-xl text-sm font-bold hover:bg-primary/20 transition-colors flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">visibility</span>
                        Detail
                    </a>
                    <form method="POST" action="{{ route('dosen-schedule.stop') }}" onsubmit="return confirm('Tutup sesi presensi ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-rose-500 text-white rounded-xl text-sm font-bold hover:bg-rose-600 transition-colors flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">stop_circle</span>
                            Tutup Sesi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Schedules Grouped by Semester -->
    @forelse($groupedSchedules as $semesterGroup)
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">calendar_month</span>
                    {{ $semesterGroup['semester'] }}
                    <span class="text-sm font-normal text-slate-400">({{ $semesterGroup['total'] }} jadwal)</span>
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($semesterGroup['items'] as $jadwal)
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                        <div class="p-5 space-y-4">
                            <!-- Header -->
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h4 class="font-bold text-on-surface text-sm">{{ $jadwal->subject?->name ?? '-' }}</h4>
                                    <p class="text-xs text-slate-400 mt-1">{{ $jadwal->subject?->code ?? '-' }} • {{ $jadwal->class?->name ?? '-' }}</p>
                                </div>
                                <span class="px-2 py-1 bg-primary/10 text-primary rounded-lg text-[10px] font-bold uppercase">{{ $jadwal->day }}</span>
                            </div>

                            <!-- Time -->
                            <div class="flex items-center gap-2 text-sm text-slate-600">
                                <span class="material-symbols-outlined text-lg text-slate-400">schedule</span>
                                <span class="font-bold">{{ \Carbon\Carbon::parse($jadwal->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->end_time)->format('H:i') }}</span>
                            </div>

                            <!-- Teacher -->
                            <div class="flex items-center gap-2 text-xs text-slate-500">
                                <span class="material-symbols-outlined text-sm text-slate-400">person</span>
                                <span>{{ $jadwal->teacher?->name ?? '-' }}</span>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-2 pt-2 border-t border-slate-50">
                                @if($activeSession && $activeSession['mata_kuliah_id'] == $jadwal->subject_id && $activeSession['kelas_id'] == $jadwal->class_id)
                                    <a href="{{ route('dosen-schedule.detail', ['subject_id' => $jadwal->subject_id, 'class_id' => $jadwal->class_id]) }}" class="flex-1 py-2 bg-emerald-500 text-white rounded-xl text-xs font-bold hover:bg-emerald-600 transition-colors flex items-center justify-center gap-1">
                                        <span class="material-symbols-outlined text-sm">visibility</span>
                                        Lihat Detail
                                    </a>
                                @else
                                    <form method="POST" action="{{ route('dosen-schedule.store') }}" class="flex-1">
                                        @csrf
                                        <input type="hidden" name="subject_id" value="{{ $jadwal->subject_id }}">
                                        <input type="hidden" name="class_id" value="{{ $jadwal->class_id }}">
                                        <button type="submit" class="w-full py-2 bg-primary text-white rounded-xl text-xs font-bold hover:opacity-90 transition-colors flex items-center justify-center gap-1">
                                            <span class="material-symbols-outlined text-sm">play_circle</span>
                                            Mulai Sesi
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">
            <span class="material-symbols-outlined text-6xl text-slate-200 mb-4">event_busy</span>
            <h3 class="text-lg font-bold text-slate-400">Belum Ada Jadwal</h3>
            <p class="text-sm text-slate-300 mt-2">Anda belum memiliki jadwal yang ditetapkan.</p>
        </div>
    @endforelse
</div>
@endsection
