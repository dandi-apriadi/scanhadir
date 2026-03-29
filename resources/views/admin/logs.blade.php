@extends('layouts.admin')

@section('content')
<div class="space-y-8">
    <!-- Page Header & Breadcrumbs -->
    <div class="mb-8">
        <nav class="flex text-xs font-medium text-slate-400 mb-2 gap-2 items-center uppercase tracking-widest">
            <span>Dashboard</span>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span>Sistem Presensi</span>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-indigo-600">Log</span>
        </nav>
        <div class="flex justify-between items-end">
            <div>
                <h2 class="text-3xl font-bold font-headline tracking-tight text-on-surface">Log Presensi Harian</h2>
                <p class="text-slate-500 mt-1">Pantau dan kelola kehadiran siswa secara real-time.</p>
            </div>
            <a href="{{ route('admin.reports') }}" class="bg-gradient-to-br from-primary to-primary-container text-white px-6 py-3 rounded-xl font-bold flex items-center gap-2 shadow-lg shadow-primary/15 hover:scale-[1.02] transition-transform active:scale-95">
                <span class="material-symbols-outlined">download</span>
                Ekspor Laporan
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="grid grid-cols-12 gap-4 mb-8">
        <form method="GET" action="{{ route('admin.logs') }}" class="col-span-12 lg:col-span-8 bg-surface-container-low rounded-xl p-4 flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Tanggal Mulai</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">calendar_today</span>
                    <input name="date_from" class="w-full bg-surface-container-lowest border-none rounded-lg py-2 pl-10 pr-4 text-sm font-medium focus:ring-2 focus:ring-primary/10" type="date" value="{{ $dateFrom }}"/>
                </div>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Tanggal Akhir</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">event</span>
                    <input name="date_to" class="w-full bg-surface-container-lowest border-none rounded-lg py-2 pl-10 pr-4 text-sm font-medium focus:ring-2 focus:ring-primary/10" type="date" value="{{ $dateTo }}"/>
                </div>
            </div>
            <div class="w-full md:w-52">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Kelas</label>
                <select name="class_id" class="w-full bg-surface-container-lowest border-none rounded-lg py-2 px-3 text-sm font-medium focus:ring-2 focus:ring-primary/10 appearance-none">
                    <option value="">Semua Kelas</option>
                    @foreach($classOptions as $classOption)
                        <option value="{{ $classOption->id }}" @selected($classId === $classOption->id)>{{ $classOption->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full md:w-48">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Status</label>
                <select name="status" class="w-full bg-surface-container-lowest border-none rounded-lg py-2 px-3 text-sm font-medium focus:ring-2 focus:ring-primary/10 appearance-none">
                    <option value="">Semua Status</option>
                    <option value="present" @selected($status === 'present')>Hadir</option>
                    <option value="late" @selected($status === 'late')>Terlambat</option>
                    <option value="sick" @selected($status === 'sick')>Sakit</option>
                    <option value="excused" @selected($status === 'excused')>Izin</option>
                    <option value="absent" @selected($status === 'absent')>Alpa</option>
                </select>
            </div>
            <button type="submit" class="mt-5 h-10 px-4 flex items-center justify-center gap-1 rounded-lg bg-surface-container-highest text-primary hover:bg-primary hover:text-white transition-colors">
                <span class="material-symbols-outlined">filter_list</span>
                <span class="text-xs font-bold">Filter</span>
            </button>
        </form>
        <div class="col-span-12 lg:col-span-4 bg-primary-container rounded-xl p-4 text-white relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-[10px] font-bold opacity-70 uppercase tracking-widest mb-1">Kehadiran Hari Ini</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-4xl font-bold font-headline">{{ $todayRate }}%</span>
                    <span class="text-xs bg-white/20 px-2 py-0.5 rounded-full font-medium text-white">+2.4%</span>
                </div>
            </div>
            <div class="absolute -right-4 -bottom-4 opacity-10 scale-150 rotate-12 transition-transform group-hover:rotate-0">
                <span class="material-symbols-outlined text-9xl">analytics</span>
            </div>
        </div>
    </div>

    <!-- Data Table Section -->
    <div class="bg-surface-container-lowest rounded-2xl overflow-hidden shadow-sm border border-slate-100/50">
        <table class="w-full border-collapse text-left">
            <thead>
                <tr class="bg-slate-50/50 text-slate-500">
                    <th class="py-4 px-6 text-xs font-bold uppercase tracking-wider">Waktu</th>
                    <th class="py-4 px-6 text-xs font-bold uppercase tracking-wider">Nama Siswa</th>
                    <th class="py-4 px-6 text-xs font-bold uppercase tracking-wider">Kelas</th>
                    <th class="py-4 px-6 text-xs font-bold uppercase tracking-wider">Status</th>
                    <th class="py-4 px-6 text-xs font-bold uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($logs as $log)
                    @php
                        $statusLabel = [
                            'present' => 'Hadir',
                            'late' => 'Terlambat',
                            'sick' => 'Sakit',
                            'excused' => 'Izin',
                            'absent' => 'Alpa',
                        ][$log->status] ?? ucfirst((string) $log->status);

                        $statusClasses = [
                            'present' => 'bg-emerald-50 text-emerald-600 ring-emerald-600/10',
                            'late' => 'bg-amber-50 text-amber-600 ring-amber-600/10',
                            'sick' => 'bg-blue-50 text-blue-600 ring-blue-600/10',
                            'excused' => 'bg-indigo-50 text-indigo-600 ring-indigo-600/10',
                            'absent' => 'bg-rose-50 text-rose-600 ring-rose-600/10',
                        ][$log->status] ?? 'bg-slate-50 text-slate-600 ring-slate-600/10';

                        $studentName = $log->student?->user?->name ?? '-';
                        $initials = collect(explode(' ', trim($studentName)))->filter()->take(2)->map(fn($part) => strtoupper(substr($part, 0, 1)))->implode('');
                    @endphp
                    <tr class="group hover:bg-surface-container-low transition-colors duration-150">
                        <td class="py-4 px-6">
                            <span class="text-sm font-bold text-on-surface font-headline">{{ $log->check_in ?? '-' }}</span>
                            <p class="text-[10px] text-slate-400">{{ \Illuminate\Support\Carbon::parse($log->date)->format('d M Y') }}</p>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-primary font-bold text-xs">{{ $initials ?: 'SW' }}</div>
                                <span class="text-sm font-semibold text-on-surface">{{ $studentName }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-sm text-slate-500">{{ $log->student?->class?->name ?? '-' }}</td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold ring-1 ring-inset {{ $statusClasses }}">
                                <span class="w-1.5 h-1.5 rounded-full bg-current mr-1.5"></span>
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <span class="text-xs text-slate-400">{{ $log->check_out ? 'Check-out: ' . $log->check_out : '-' }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-10 px-6 text-center text-sm text-slate-500">Data log sesuai filter belum ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <!-- Pagination -->
        <div class="px-6 py-4 bg-slate-50/30 flex justify-between items-center">
            <p class="text-xs text-slate-500 font-medium">Menampilkan {{ $logs->count() }} dari {{ $logs->total() }} entri</p>
            <div>{{ $logs->onEachSide(1)->links() }}</div>
        </div>
    </div>
</div>
@endsection
