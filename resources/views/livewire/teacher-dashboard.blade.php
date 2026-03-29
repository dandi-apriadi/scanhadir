<div class="space-y-8 px-6 py-8 max-w-[1440px] mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-end gap-6 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-on-surface mb-2">Dashboard Guru</h1>
            <p class="text-slate-500">Ringkasan kehadiran siswa dan kelas Anda.</p>
        </div>
        <input type="date" wire:model="selectedDate" class="px-4 py-2.5 bg-surface-container-low border border-outline-variant/15 rounded-xl text-sm font-semibold outline-none"/>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/15">
            <p class="text-xs text-slate-500 uppercase font-bold mb-2">Kelas yang Diampu</p>
            <p class="text-3xl font-bold text-primary">{{ $totalAssignedClasses }}</p>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/15">
            <p class="text-xs text-slate-500 uppercase font-bold mb-2">Total Siswa</p>
            <p class="text-3xl font-bold text-secondary">{{ $totalStudents }}</p>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/15">
            <p class="text-xs text-slate-500 uppercase font-bold mb-2">Hadir</p>
            <p class="text-3xl font-bold text-emerald-600">{{ $stats['present'] }}</p>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/15">
            <p class="text-xs text-slate-500 uppercase font-bold mb-2">Terlambat</p>
            <p class="text-3xl font-bold text-amber-600">{{ $stats['late'] }}</p>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/15">
            <p class="text-xs text-slate-500 uppercase font-bold mb-2">Alpa</p>
            <p class="text-3xl font-bold text-error">{{ $stats['absent'] }}</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Recent Attendance Log -->
        <div class="lg:col-span-2 bg-surface-container-lowest rounded-2xl border border-outline-variant/15 overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-xl font-bold text-on-surface">Log Presensi Terbaru</h3>
                <span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-bold rounded-full animate-pulse">LIVE</span>
            </div>
            @if($recentLogs->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase">Siswa</th>
                                <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase">Kelas</th>
                                <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase">Waktu Check-In</th>
                                <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($recentLogs as $log)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-primary font-bold text-xs">
                                                {{ substr($log['student_name'], 0, 1) }}
                                            </div>
                                            <span class="text-sm font-semibold text-on-surface">{{ $log['student_name'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-500">{{ $log['class_name'] }}</td>
                                    <td class="px-6 py-4 text-sm font-medium">{{ $log['check_in'] ?? '-' }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="px-2 py-1 text-[10px] font-bold rounded @if($log['status'] === 'present') bg-emerald-50 text-emerald-600 @elseif($log['status'] === 'late') bg-amber-50 text-amber-600 @else bg-slate-50 text-slate-600 @endif">
                                            {{ strtoupper($log['status']) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-8 text-center text-slate-500">
                    <p class="text-sm">Belum ada data presensi</p>
                </div>
            @endif
        </div>

        <!-- Class Summary & Late Students -->
        <div class="space-y-6">
            <!-- Late Students Alert -->
            @if($lateStudents->isNotEmpty())
                <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/15 overflow-hidden">
                    <div class="p-6 border-b border-slate-100 bg-amber-50">
                        <h4 class="text-lg font-bold text-amber-900 flex items-center gap-2">
                            <span class="material-symbols-outlined">alarm</span>
                            Siswa Terlambat
                        </h4>
                    </div>
                    <div class="p-6 space-y-3 max-h-64 overflow-y-auto">
                        @foreach($lateStudents as $student)
                            <div class="p-3 bg-amber-50 rounded-lg border border-amber-200">
                                <p class="text-sm font-semibold text-on-surface">{{ $student->user->name }}</p>
                                <p class="text-xs text-slate-500">{{ $student->class->name }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Class Summary -->
            <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/15 overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h4 class="text-lg font-bold text-on-surface">Kehadiran Per Kelas</h4>
                </div>
                <div class="p-6 space-y-3 max-h-96 overflow-y-auto">
                    @forelse($classes as $class)
                        <div class="p-4 border border-outline-variant/15 rounded-lg hover:bg-surface-container-high transition-colors">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="text-sm font-semibold text-on-surface">{{ $class['name'] }}</p>
                                    <p class="text-xs text-slate-500">{{ $class['present'] }}/{{ $class['total_students'] }} hadir</p>
                                </div>
                                <span class="text-xs font-bold bg-primary/10 text-primary px-2 py-1 rounded">{{ $class['percentage'] }}%</span>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-2">
                                <div class="bg-primary rounded-full h-2 transition-all" style="width: {{ $class['percentage'] }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 text-center py-4">Belum ada data kelas</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-xl border border-slate-100">
            <p class="text-xs text-slate-500 uppercase">Hadir</p>
            <p class="text-2xl font-bold text-emerald-600">{{ $stats['present'] }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-slate-100">
            <p class="text-xs text-slate-500 uppercase">Terlambat</p>
            <p class="text-2xl font-bold text-amber-600">{{ $stats['late'] }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-slate-100">
            <p class="text-xs text-slate-500 uppercase">Alpa</p>
            <p class="text-2xl font-bold text-rose-600">{{ $stats['absent'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h2 class="font-semibold text-slate-800">Kelas dan Presensi Hari Ini</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
                    <tr>
                        <th class="text-left px-5 py-3">Kelas</th>
                        <th class="text-left px-5 py-3">Jumlah Siswa</th>
                        <th class="text-left px-5 py-3">Sudah Presensi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($classes as $class)
                    <tr>
                        <td class="px-5 py-3 font-medium text-slate-700">{{ $class['name'] }}</td>
                        <td class="px-5 py-3">{{ $class['students_count'] }}</td>
                        <td class="px-5 py-3">{{ $class['attendance_count'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-5 py-4 text-center text-slate-500">Belum ada kelas yang ditugaskan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
