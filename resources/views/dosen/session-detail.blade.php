@extends('layouts.teacher')

@section('content')
<div class="space-y-8">
    <!-- Breadcrumb & Header -->
    <div class="space-y-4">
        <nav class="flex items-center gap-2 text-xs font-medium text-slate-400">
            <a class="hover:text-primary transition-colors" href="{{ route('dosen-courses') }}">Mata Kuliah Saya</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-primary font-semibold">Detail Sesi</span>
        </nav>
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <h2 class="text-3xl font-headline font-bold text-on-surface tracking-tight">Detail Sesi Presensi</h2>
                <p class="text-slate-500 mt-2 max-w-xl text-sm leading-relaxed">{{ $subject->name }} ({{ $subject->code }}) • Kelas {{ $class->name }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dosen-schedule.detail.export.excel', ['subject_id' => $subject->id, 'class_id' => $class->id, 'date' => $selectedDate]) }}" class="px-4 py-2 bg-emerald-500 text-white rounded-xl text-sm font-bold hover:bg-emerald-600 transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">download</span>
                    Export Excel
                </a>
                <a href="{{ route('dosen-schedule.detail.export.pdf', ['subject_id' => $subject->id, 'class_id' => $class->id, 'date' => $selectedDate]) }}" class="px-4 py-2 bg-rose-500 text-white rounded-xl text-sm font-bold hover:bg-rose-600 transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">picture_as_pdf</span>
                    Export PDF
                </a>
            </div>
        </div>
    </div>

    <!-- Date Filter -->
    <form method="GET" action="{{ route('dosen-schedule.detail') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-white rounded-2xl border border-slate-100 shadow-sm">
        <div class="space-y-1.5">
            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500 ml-1">Tanggal</label>
            <input name="date" value="{{ $selectedDate }}" type="date" class="w-full bg-slate-50 border-none rounded-xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-primary/20 py-2.5 px-4">
        </div>
        <input type="hidden" name="subject_id" value="{{ $subject->id }}">
        <input type="hidden" name="class_id" value="{{ $class->id }}">
        <div class="flex items-end gap-2">
            <button type="submit" class="w-full py-2.5 px-4 bg-slate-900 text-white rounded-xl text-sm font-bold hover:opacity-90 transition-colors flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-lg">search</span>
                Tampilkan
            </button>
        </div>
    </form>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-black text-on-surface">{{ $summary['total_students'] }}</p>
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mt-1">Total Siswa</p>
        </div>
        <div class="bg-white rounded-2xl border border-emerald-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-black text-emerald-600">{{ $summary['hadir'] }}</p>
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mt-1">Hadir</p>
        </div>
        <div class="bg-white rounded-2xl border border-amber-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-black text-amber-600">{{ $summary['telat'] }}</p>
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mt-1">Telat</p>
        </div>
        <div class="bg-white rounded-2xl border border-blue-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-black text-blue-600">{{ $summary['sakit'] }}</p>
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mt-1">Sakit</p>
        </div>
        <div class="bg-white rounded-2xl border border-indigo-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-black text-indigo-600">{{ $summary['izin'] }}</p>
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mt-1">Izin</p>
        </div>
        <div class="bg-white rounded-2xl border border-rose-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-black text-rose-600">{{ $summary['alpa'] }}</p>
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mt-1">Alpa</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 text-center">
            <p class="text-2xl font-black text-slate-500">{{ $summary['pending'] }}</p>
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mt-1">Pending</p>
        </div>
    </div>

    <!-- Student Attendance Table -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">No</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">NISN</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Nama Siswa</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400 text-center">Status</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400 text-center">Metode</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400 text-center">Waktu Tap</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($studentRows as $index => $row)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 text-sm font-bold text-slate-400">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 text-sm font-mono font-bold text-slate-600">{{ $row['nisn'] }}</td>
                            <td class="px-6 py-4 text-sm font-bold text-on-surface">{{ $row['nama'] }}</td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $statusColors = [
                                        'Hadir' => 'bg-emerald-100 text-emerald-700',
                                        'Telat' => 'bg-amber-100 text-amber-700',
                                        'Sakit' => 'bg-blue-100 text-blue-700',
                                        'Izin' => 'bg-indigo-100 text-indigo-700',
                                        'Alpa' => 'bg-rose-100 text-rose-700',
                                        'Pending' => 'bg-slate-100 text-slate-500',
                                    ];
                                    $color = $statusColors[$row['status']] ?? 'bg-slate-100 text-slate-500';
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $color }}">
                                    {{ $row['status'] === 'Pending' ? 'Belum Absensi' : $row['status'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-slate-500">{{ $row['metode'] }}</td>
                            <td class="px-6 py-4 text-center text-sm font-mono font-bold text-slate-600">{{ $row['waktu_tap'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-slate-500">Tidak ada data siswa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
