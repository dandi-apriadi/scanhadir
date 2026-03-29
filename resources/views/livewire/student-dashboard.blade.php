<div class="space-y-8 px-6 py-8 max-w-[1440px] mx-auto">
    <!-- Hero Greeting -->
    <section class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
        <div>
            <h1 class="text-3xl font-bold font-headline text-on-background">Halo, {{ $student->user->name }} 👋</h1>
            <p class="text-slate-500 mt-1 font-medium">Selamat datang di sistem presensi digital sekolah.</p>
        </div>
        <div class="flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-700 rounded-full border border-emerald-100 shadow-sm">
            <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
            <span class="text-xs font-bold uppercase tracking-wider">Sistem Aktif</span>
        </div>
    </section>

    <!-- Metric Cards -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm flex items-center gap-5 hover:shadow-lg transition-all">
            <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center text-primary">
                <span class="material-symbols-outlined text-3xl">calendar_today</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Hadir Bulan Ini</p>
                <p class="text-2xl font-extrabold text-on-surface">{{ $attendanceStats['present'] }}<span class="text-slate-300 font-normal text-lg">/{{ $attendanceStats['total_days'] }}</span></p>
            </div>
        </div>

        <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm flex items-center gap-5 hover:shadow-lg transition-all">
            <div class="w-14 h-14 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600">
                <span class="material-symbols-outlined text-3xl">timer</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Terlambat</p>
                <p class="text-2xl font-extrabold text-on-surface">{{ $attendanceStats['late'] }} <span class="text-xs font-medium text-amber-500 bg-amber-50 px-2 py-0.5 rounded-md ml-1">Hari</span></p>
            </div>
        </div>

        <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm flex items-center gap-5 hover:shadow-lg transition-all">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                <span class="material-symbols-outlined text-3xl">check_circle</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Tingkat Kehadiran</p>
                <p class="text-2xl font-extrabold text-emerald-600">{{ $attendancePercentage }}%</p>
            </div>
        </div>
    </section>

    <!-- Main Content Area -->
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Attendance History -->
        <div class="lg:col-span-2 bg-surface-container-lowest rounded-2xl border border-outline-variant/15 overflow-hidden shadow-sm">
            <div class="p-6 border-b border-slate-100">
                <h3 class="text-xl font-bold font-headline text-on-surface">Riwayat Presensi Terbaru</h3>
            </div>

            @if($recentAttendance->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase">Tanggal</th>
                                <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase">Hari</th>
                                <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase">Masuk</th>
                                <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($recentAttendance as $record)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 font-semibold text-on-surface">{{ $record['date'] }}</td>
                                    <td class="px-6 py-4 text-slate-500 text-sm">{{ $record['day'] }}</td>
                                    <td class="px-6 py-4 font-medium text-on-surface">{{ $record['check_in'] ?? '-' }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="px-2 py-1 text-[10px] font-bold rounded @if($record['status'] === 'present') bg-emerald-50 text-emerald-600 @elseif($record['status'] === 'late') bg-amber-50 text-amber-600 @elseif($record['status'] === 'sick') bg-blue-50 text-blue-600 @elseif($record['status'] === 'excused') bg-purple-50 text-purple-600 @else bg-slate-50 text-slate-600 @endif">
                                            {{ strtoupper(str_replace('_', ' ', $record['status'])) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-8 text-center text-slate-500">
                    <p class="text-sm">Belum ada riwayat presensi</p>
                </div>
            @endif
        </div>

        <!-- Quick Info & QR Code -->
        <div class="space-y-6">
            <!-- QR Code Card -->
            <div class="bg-gradient-to-br from-primary to-primary-container p-8 rounded-2xl text-white shadow-lg">
                <p class="text-sm font-semibold opacity-90 mb-4">Kode QR Anda</p>
                <div class="bg-white p-4 rounded-lg flex items-center justify-center mb-4" style="aspect-ratio: 1;">
                    <span class="material-symbols-outlined text-6xl text-slate-300">qr_code_2</span>
                </div>
                <p class="text-xs opacity-75">Scan dengan alat presensi untuk pencatat kehadiran</p>
                <a href="{{ route('students.qrcode', $student->id) }}" class="mt-4 block w-full py-2 px-4 bg-white text-primary rounded-lg font-bold text-center text-sm hover:bg-slate-100 transition-colors">
                    Download QR Code
                </a>
            </div>

            <!-- Status Box -->
            <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/15">
                <h4 class="font-bold text-on-surface mb-4">Info Profil</h4>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-slate-500 text-xs">Nama Siswa</p>
                        <p class="font-semibold text-on-surface">{{ $student->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500 text-xs">NISN</p>
                        <p class="font-semibold text-on-surface">{{ $student->nisn }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500 text-xs">Kelas</p>
                        <p class="font-semibold text-on-surface">{{ $student->class?->name }}</p>
                    </div>
                    <div class="pt-3 border-t border-slate-200">
                        <p class="text-slate-500 text-xs">Status Hari Ini</p>
                        <div class="mt-2">
                            @if($todayStatus === 'present')
                                <span class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-xs font-bold">
                                    <span class="w-2 h-2 bg-emerald-600 rounded-full"></span>
                                    Hadir Tepat Waktu
                                </span>
                            @elseif($todayStatus === 'late')
                                <span class="inline-flex items-center gap-2 px-3 py-1 bg-amber-50 text-amber-600 rounded-full text-xs font-bold">
                                    <span class="w-2 h-2 bg-amber-600 rounded-full"></span>
                                    Terlambat
                                </span>
                            @elseif($todayStatus === 'not_recorded')
                                <span class="inline-flex items-center gap-2 px-3 py-1 bg-slate-50 text-slate-600 rounded-full text-xs font-bold">
                                    <span class="w-2 h-2 bg-slate-600 rounded-full"></span>
                                    Belum Tercatat
                                </span>
                            @else
                                <span class="inline-flex items-center gap-2 px-3 py-1 bg-slate-50 text-slate-600 rounded-full text-xs font-bold">
                                    {{ strtoupper(str_replace('_', ' ', $todayStatus)) }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Summary -->
            <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/15">
                <h4 class="font-bold text-on-surface mb-4">Ringkasan Bulan Ini</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Hadir:</span>
                        <span class="font-bold text-emerald-600">{{ $attendanceStats['present'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Terlambat:</span>
                        <span class="font-bold text-amber-600">{{ $attendanceStats['late'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Sakit:</span>
                        <span class="font-bold text-blue-600">{{ $attendanceStats['sick'] }}</span>
                    </div>
                    <div class="flex justify-between border-t border-slate-200 pt-2">
                        <span class="text-slate-500">Alpa:</span>
                        <span class="font-bold text-error">{{ $attendanceStats['absent'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
