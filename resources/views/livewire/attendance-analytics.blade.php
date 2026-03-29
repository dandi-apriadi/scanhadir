<div class="space-y-8 px-6 py-8 max-w-[1440px] mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-on-surface mb-2">Attendance Analytics</h1>
            <p class="text-slate-500">View detailed attendance statistics and trends</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/15 p-6 flex gap-4 items-end flex-wrap">
        <div>
            <label class="block text-xs text-slate-500 uppercase font-bold mb-2">Class</label>
            <select wire:model="selectedClass" class="px-4 py-2.5 bg-surface-container-low border border-outline-variant/15 rounded-lg text-sm font-semibold outline-none">
                <option value="">All Classes</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-slate-500 uppercase font-bold mb-2">Year</label>
            <select wire:model="selectedYear" class="px-4 py-2.5 bg-surface-container-low border border-outline-variant/15 rounded-lg text-sm font-semibold outline-none">
                @for ($year = now()->year - 2; $year <= now()->year + 1; $year++)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endfor
            </select>
        </div>
        <div>
            <label class="block text-xs text-slate-500 uppercase font-bold mb-2">Month</label>
            <select wire:model="selectedMonth" class="px-4 py-2.5 bg-surface-container-low border border-outline-variant/15 rounded-lg text-sm font-semibold outline-none">
                @for ($month = 1; $month <= 12; $month++)
                    <option value="{{ $month }}">{{ now()->setMonth($month)->format('F') }}</option>
                @endfor
            </select>
        </div>
    </div>

    <!-- Main Analytics Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        <!-- Key Metrics -->
        <div class="lg:col-span-5 grid grid-cols-1 md:grid-cols-6 gap-4">
            <div class="bg-gradient-to-br from-emerald-50 to-emerald-100/50 border border-emerald-200 rounded-2xl p-6">
                <p class="text-xs text-emerald-600 uppercase font-bold mb-2">Present</p>
                <p class="text-3xl font-bold text-emerald-700">{{ $analyticsData['present'] }}</p>
                <p class="text-xs text-emerald-600 mt-2">{{ $analyticsData['presentPercentage'] }}%</p>
            </div>
            <div class="bg-gradient-to-br from-amber-50 to-amber-100/50 border border-amber-200 rounded-2xl p-6">
                <p class="text-xs text-amber-600 uppercase font-bold mb-2">Late</p>
                <p class="text-3xl font-bold text-amber-700">{{ $analyticsData['late'] }}</p>
                <p class="text-xs text-amber-600 mt-2">{{ $analyticsData['latePercentage'] }}%</p>
            </div>
            <div class="bg-gradient-to-br from-rose-50 to-rose-100/50 border border-rose-200 rounded-2xl p-6">
                <p class="text-xs text-rose-600 uppercase font-bold mb-2">Absent</p>
                <p class="text-3xl font-bold text-rose-700">{{ $analyticsData['absent'] }}</p>
                <p class="text-xs text-rose-600 mt-2">{{ $analyticsData['absentPercentage'] }}%</p>
            </div>
            <div class="bg-gradient-to-br from-blue-50 to-blue-100/50 border border-blue-200 rounded-2xl p-6">
                <p class="text-xs text-blue-600 uppercase font-bold mb-2">Sick</p>
                <p class="text-3xl font-bold text-blue-700">{{ $analyticsData['sick'] }}</p>
            </div>
            <div class="bg-gradient-to-br from-purple-50 to-purple-100/50 border border-purple-200 rounded-2xl p-6">
                <p class="text-xs text-purple-600 uppercase font-bold mb-2">Excused</p>
                <p class="text-3xl font-bold text-purple-700">{{ $analyticsData['excused'] }}</p>
            </div>
            <div class="bg-gradient-to-br from-slate-50 to-slate-100/50 border border-slate-200 rounded-2xl p-6">
                <p class="text-xs text-slate-600 uppercase font-bold mb-2">Total</p>
                <p class="text-3xl font-bold text-slate-700">{{ $analyticsData['total'] }}</p>
            </div>
        </div>
    </div>

    <!-- Class Comparison -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Class Comparison Table -->
        <div class="lg:col-span-2 bg-surface-container-lowest rounded-2xl border border-outline-variant/15 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h3 class="text-xl font-bold text-on-surface">Attendance by Class</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase">Class</th>
                            <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase">Total</th>
                            <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase">Present</th>
                            <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase">Late</th>
                            <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase">Absent</th>
                            <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase text-right">Percentage</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($classComparison as $class)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-semibold text-on-surface">{{ $class['name'] }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ $class['total'] }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-bold rounded">{{ $class['present'] }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 bg-amber-50 text-amber-600 text-[10px] font-bold rounded">{{ $class['late'] }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 bg-rose-50 text-rose-600 text-[10px] font-bold rounded">{{ $class['absent'] }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center gap-2 justify-end">
                                        <div class="w-24 bg-slate-200 rounded-full h-2">
                                            <div class="bg-emerald-600 rounded-full h-2 transition-all" style="width: {{ $class['presentPercentage'] }}%"></div>
                                        </div>
                                        <span class="text-sm font-bold text-emerald-600 min-w-max">{{ $class['presentPercentage'] }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-slate-500">No data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top Performing Students -->
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h3 class="text-xl font-bold text-on-surface">Top Performing Students</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase">Student</th>
                        <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase">NISN</th>
                        <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase">Class</th>
                        <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase">Total</th>
                        <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase">Present</th>
                        <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase text-right">Attendance %</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($studentPerformance as $student)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-primary font-bold text-xs">
                                        {{ substr($student['name'], 0, 1) }}
                                    </div>
                                    <span class="text-sm font-semibold text-on-surface">{{ $student['name'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-500 text-sm">{{ $student['nisn'] }}</td>
                            <td class="px-6 py-4 text-slate-500 text-sm">{{ $student['class'] }}</td>
                            <td class="px-6 py-4 font-semibold">{{ $student['total'] }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-bold rounded">{{ $student['present'] }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-bold text-emerald-600">{{ $student['presentPercentage'] }}%</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-slate-500">No data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
