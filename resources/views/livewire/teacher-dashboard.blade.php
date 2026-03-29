<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Teacher Dashboard</h1>
        <p class="text-sm text-slate-500">Ringkasan kelas dan kehadiran hari ini.</p>
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
