<div class="max-w-[1440px] mx-auto px-6 py-8">
    <!-- Header Section -->
    <div class="mb-10 flex justify-between items-end">
        <div>
            <h2 class="text-3xl font-bold font-headline tracking-tight text-on-surface">Dashboard Admin</h2>
            <p class="text-slate-500 mt-1">Real-time attendance insights and system overview.</p>
        </div>
        <div class="flex gap-3">
            <input type="date" wire:model="selectedDate" class="px-4 py-2.5 bg-surface-container-low border border-outline-variant/15 rounded-xl text-sm font-semibold hover:bg-surface-container-high transition-all outline-none"/>
            <a href="{{ route('admin.reports') }}" class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-br from-primary to-primary-container text-white rounded-xl text-sm font-semibold shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all">
                <span class="material-symbols-outlined text-lg">assessment</span>
                Laporan Lengkap
            </a>
        </div>
    </div>

    <!-- System Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/15 hover:border-primary/20 transition-all group">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-primary/10 rounded-xl text-primary group-hover:bg-primary group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined">groups</span>
                </div>
            </div>
            <p class="text-sm font-medium text-slate-500 mb-1">Total Siswa</p>
            <h3 class="text-3xl font-bold font-headline">{{ $totalStudents }}</h3>
            <p class="text-[10px] text-slate-400 mt-2">Terdaftar di sistem</p>
        </div>

        <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/15 hover:border-secondary/20 transition-all group">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-secondary/10 rounded-xl text-secondary group-hover:bg-secondary group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined">person_check</span>
                </div>
            </div>
            <p class="text-sm font-medium text-slate-500 mb-1">Total Guru</p>
            <h3 class="text-3xl font-bold font-headline">{{ $totalTeachers }}</h3>
            <p class="text-[10px] text-slate-400 mt-2">Pengajar aktif</p>
        </div>

        <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/15 hover:border-tertiary/20 transition-all group">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-tertiary/10 rounded-xl text-tertiary group-hover:bg-tertiary group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined">school</span>
                </div>
            </div>
            <p class="text-sm font-medium text-slate-500 mb-1">Total Kelas</p>
            <h3 class="text-3xl font-bold font-headline">{{ $totalClasses }}</h3>
            <p class="text-[10px] text-slate-400 mt-2">Jumlah rombel</p>
        </div>

        <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/15 hover:border-error/20 transition-all group">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-rose-100 rounded-xl text-error group-hover:bg-error group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined">check_circle</span>
                </div>
            </div>
            <p class="text-sm font-medium text-slate-500 mb-1">Kehadiran Hari Ini</p>
            <h3 class="text-3xl font-bold font-headline">{{ $attendancePercentage }}%</h3>
            <p class="text-[10px] text-slate-400 mt-2">{{ $stats['hadir'] }} tepat waktu</p>
        </div>
    </div>

    <!-- Daily Attendance Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/15">
            <p class="text-sm font-medium text-slate-500 mb-2">Hadir</p>
            <h3 class="text-2xl font-bold text-emerald-600">{{ $stats['hadir'] }}/{{ $stats['total_scanned'] }}</h3>
            <p class="text-[10px] text-slate-400 mt-2">dari {{ $stats['total_students'] }} siswa</p>
        </div>

        <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/15">
            <p class="text-sm font-medium text-slate-500 mb-2">Terlambat</p>
            <h3 class="text-2xl font-bold text-amber-600">{{ $stats['telat'] }}</h3>
            <p class="text-[10px] text-slate-400 mt-2">membutuhkan tindakan</p>
        </div>

        <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/15">
            <p class="text-sm font-medium text-slate-500 mb-2">Izin/Sakit</p>
            <h3 class="text-2xl font-bold text-blue-600">{{ ($stats['sakit'] ?? 0) + ($stats['izin'] ?? 0) }}</h3>
            <p class="text-[10px] text-slate-400 mt-2">dengan dokumen</p>
        </div>

        <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/15">
            <p class="text-sm font-medium text-slate-500 mb-2">Alpa</p>
            <h3 class="text-2xl font-bold text-error">{{ $stats['absent'] }}</h3>
            <p class="text-[10px] text-slate-400 mt-2">tanpa keterangan</p>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Recent Activity Log -->
        <div class="lg:col-span-2 bg-surface-container-lowest rounded-2xl border border-outline-variant/15 overflow-hidden shadow-sm">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-xl font-bold font-headline text-on-surface">Log Presensi Real-time</h3>
                <span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-bold rounded-full animate-pulse">LIVE</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Siswa</th>
                            <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Kelas</th>
                            <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Waktu Check-In</th>
                            <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentLogs as $log)
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
                                <td class="px-6 py-4 text-sm font-medium text-on-surface">{{ $log['check_in'] ?? '-' }}</td>
                                <td class="px-6 py-4 text-right">
                                    <span class="px-2 py-1 text-[10px] font-bold rounded @if($log['status'] === 'Hadir') bg-emerald-50 text-emerald-600 @elseif($log['status'] === 'Telat') bg-amber-50 text-amber-600 @else bg-slate-50 text-slate-600 @endif">
                                        {{ strtoupper(str_replace('_', ' ', $log['status'])) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-slate-500">
                                    Belum ada data presensi hari ini
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Late Students Alert & Summary -->
        <div class="space-y-6">
            <!-- Late Students -->
            <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/15 overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-amber-50">
                    <h4 class="text-lg font-bold text-amber-900 flex items-center gap-2">
                        <span class="material-symbols-outlined text-xl">alarm</span>
                        Siswa Terlambat
                    </h4>
                </div>
                <div class="p-6 space-y-3">
                    @forelse($lateStudents as $student)
                        <div class="p-3 bg-amber-50 rounded-lg border border-amber-200">
                            <p class="text-sm font-semibold text-on-surface">{{ $student->student->user->name }}</p>
                            <p class="text-xs text-slate-500">{{ $student->student->class->name }} • {{ $student->check_in }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 text-center py-4">Tidak ada siswa terlambat</p>
                    @endforelse
                </div>
            </div>

            <!-- Class Summary -->
            <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/15 overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h4 class="text-lg font-bold text-on-surface">Ringkasan Per Kelas</h4>
                </div>
                <div class="p-6 space-y-3 max-h-96 overflow-y-auto">
                    @forelse($classes as $class)
                        <div class="p-3 border border-outline-variant/15 rounded-lg hover:bg-surface-container-high transition-colors">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="text-sm font-semibold text-on-surface">{{ $class['name'] }}</p>
                                    <p class="text-xs text-slate-500">{{ $class['hadir'] }}/{{ $class['total_students'] }} hadir</p>
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
