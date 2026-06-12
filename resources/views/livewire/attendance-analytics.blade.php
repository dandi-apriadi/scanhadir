<div class="space-y-8 px-6 py-8 max-w-[1440px] mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-2">
        <div>
            <h1 class="text-3xl font-bold text-on-surface mb-2">Attendance Analytics</h1>
            <p class="text-slate-500">Grafik kehadiran, siswa terlambat, ranking, &amp; statistik kelas</p>
        </div>
        <div wire:loading class="flex items-center gap-2 text-primary text-sm font-semibold">
            <span class="material-symbols-outlined animate-spin">progress_activity</span> Memuat...
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/15 p-6 flex gap-4 items-end flex-wrap">
        <div>
            <label class="block text-xs text-slate-500 uppercase font-bold mb-2">Class</label>
            <select wire:model.live="selectedClass" class="px-4 py-2.5 bg-surface-container-low border border-outline-variant/15 rounded-lg text-sm font-semibold outline-none">
                <option value="">All Classes</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-slate-500 uppercase font-bold mb-2">Year</label>
            <select wire:model.live="selectedYear" class="px-4 py-2.5 bg-surface-container-low border border-outline-variant/15 rounded-lg text-sm font-semibold outline-none">
                @for ($year = now()->year - 2; $year <= now()->year + 1; $year++)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endfor
            </select>
        </div>
        <div>
            <label class="block text-xs text-slate-500 uppercase font-bold mb-2">Month</label>
            <select wire:model.live="selectedMonth" class="px-4 py-2.5 bg-surface-container-low border border-outline-variant/15 rounded-lg text-sm font-semibold outline-none">
                @for ($month = 1; $month <= 12; $month++)
                    <option value="{{ $month }}">{{ now()->setMonth($month)->format('F') }}</option>
                @endfor
            </select>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100/50 border border-emerald-200 rounded-2xl p-6">
            <p class="text-xs text-emerald-600 uppercase font-bold mb-2">Hadir</p>
            <p class="text-3xl font-bold text-emerald-700">{{ $analyticsData['present'] }}</p>
            <p class="text-xs text-emerald-600 mt-2">{{ $analyticsData['presentPercentage'] }}%</p>
        </div>
        <div class="bg-gradient-to-br from-amber-50 to-amber-100/50 border border-amber-200 rounded-2xl p-6">
            <p class="text-xs text-amber-600 uppercase font-bold mb-2">Telat</p>
            <p class="text-3xl font-bold text-amber-700">{{ $analyticsData['late'] }}</p>
            <p class="text-xs text-amber-600 mt-2">{{ $analyticsData['latePercentage'] }}%</p>
        </div>
        <div class="bg-gradient-to-br from-rose-50 to-rose-100/50 border border-rose-200 rounded-2xl p-6">
            <p class="text-xs text-rose-600 uppercase font-bold mb-2">Alpa</p>
            <p class="text-3xl font-bold text-rose-700">{{ $analyticsData['absent'] }}</p>
            <p class="text-xs text-rose-600 mt-2">{{ $analyticsData['absentPercentage'] }}%</p>
        </div>
        <div class="bg-gradient-to-br from-blue-50 to-blue-100/50 border border-blue-200 rounded-2xl p-6">
            <p class="text-xs text-blue-600 uppercase font-bold mb-2">Sakit</p>
            <p class="text-3xl font-bold text-blue-700">{{ $analyticsData['sick'] }}</p>
        </div>
        <div class="bg-gradient-to-br from-purple-50 to-purple-100/50 border border-purple-200 rounded-2xl p-6">
            <p class="text-xs text-purple-600 uppercase font-bold mb-2">Izin</p>
            <p class="text-3xl font-bold text-purple-700">{{ $analyticsData['excused'] }}</p>
        </div>
        <div class="bg-gradient-to-br from-slate-50 to-slate-100/50 border border-slate-200 rounded-2xl p-6">
            <p class="text-xs text-slate-600 uppercase font-bold mb-2">Total</p>
            <p class="text-3xl font-bold text-slate-700">{{ $analyticsData['total'] }}</p>
        </div>
    </div>

    @php
        $chartPayload = [
            'trend' => [
                'labels' => collect($monthlyTrend)->pluck('month')->values(),
                'present' => collect($monthlyTrend)->pluck('present')->values(),
                'late' => collect($monthlyTrend)->pluck('late')->values(),
                'absent' => collect($monthlyTrend)->pluck('absent')->values(),
            ],
            'distribution' => [
                'labels' => ['Hadir', 'Telat', 'Sakit', 'Izin', 'Alpa'],
                'data' => [
                    $analyticsData['present'], $analyticsData['late'],
                    $analyticsData['sick'], $analyticsData['excused'], $analyticsData['absent'],
                ],
            ],
            'classes' => [
                'labels' => collect($classComparison)->pluck('name')->values(),
                'present' => collect($classComparison)->pluck('presentPercentage')->values(),
            ],
        ];
    @endphp

    <!-- Hidden data node (Livewire morphs this on filter change) -->
    <div id="sh-analytics-data" data-payload='@json($chartPayload)' class="hidden"></div>

    <!-- Charts Row: Trend + Distribution -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-8 shadow-sm">
            <h3 class="text-xl font-bold text-on-surface mb-1">Grafik Kehadiran (Trend {{ $selectedYear }})</h3>
            <p class="text-sm text-slate-500 mb-6">Perbandingan Hadir, Telat &amp; Alpa per bulan</p>
            <div wire:ignore class="h-72"><canvas id="chartTrend"></canvas></div>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-8 shadow-sm">
            <h3 class="text-xl font-bold text-on-surface mb-1">Distribusi Status</h3>
            <p class="text-sm text-slate-500 mb-6">Bulan {{ now()->setMonth($selectedMonth)->format('F') }}</p>
            <div wire:ignore class="h-72 flex items-center justify-center"><canvas id="chartDist"></canvas></div>
        </div>
    </div>

    <!-- Late students + Class statistics chart -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Siswa Terlambat -->
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 overflow-hidden shadow-sm">
            <div class="p-6 border-b border-slate-100 flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-500">schedule</span>
                <h3 class="text-xl font-bold text-on-surface">Siswa Sering Terlambat</h3>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($lateStudents as $i => $student)
                    <div class="flex items-center gap-4 px-6 py-3 hover:bg-slate-50 transition-colors">
                        <span class="w-6 text-center font-black text-slate-300 text-sm">{{ $i + 1 }}</span>
                        @if($student['photo'])
                            <img src="{{ $student['photo'] }}" class="w-9 h-9 rounded-full object-cover ring-2 ring-amber-100" />
                        @else
                            <div class="w-9 h-9 rounded-full bg-amber-50 flex items-center justify-center text-amber-600 font-bold text-xs">{{ substr($student['name'] ?? '?', 0, 1) }}</div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-on-surface truncate">{{ $student['name'] }}</p>
                            <p class="text-[11px] text-slate-400">{{ $student['class'] }} · {{ $student['nisn'] }}</p>
                        </div>
                        <span class="px-3 py-1 bg-amber-50 text-amber-700 rounded-full text-xs font-bold whitespace-nowrap">{{ $student['lateCount'] }}x telat</span>
                    </div>
                @empty
                    <div class="px-6 py-10 text-center text-sm text-slate-400">Tidak ada keterlambatan pada periode ini 🎉</div>
                @endforelse
            </div>
        </div>

        <!-- Statistik Kelas chart -->
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-8 shadow-sm">
            <h3 class="text-xl font-bold text-on-surface mb-1">Statistik Kelas</h3>
            <p class="text-sm text-slate-500 mb-6">Persentase kehadiran per kelas (%)</p>
            <div wire:ignore class="h-72"><canvas id="chartClass"></canvas></div>
        </div>
    </div>

    <!-- Ranking Kehadiran -->
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 overflow-hidden shadow-sm">
        <div class="p-6 border-b border-slate-100 flex items-center gap-2">
            <span class="material-symbols-outlined text-emerald-500">trophy</span>
            <h3 class="text-xl font-bold text-on-surface">Ranking Kehadiran Siswa</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase">#</th>
                        <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase">Student</th>
                        <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase">NISN</th>
                        <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase">Class</th>
                        <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase">Hadir</th>
                        <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase">Telat</th>
                        <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase text-right">Attendance %</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($studentPerformance as $i => $student)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 font-black text-slate-300">{{ $i + 1 }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($student['photo'])
                                        <img src="{{ $student['photo'] }}" class="w-8 h-8 rounded-lg object-cover" />
                                    @else
                                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-primary font-bold text-xs">{{ substr($student['name'] ?? '?', 0, 1) }}</div>
                                    @endif
                                    <span class="text-sm font-semibold text-on-surface">{{ $student['name'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-500 text-sm">{{ $student['nisn'] }}</td>
                            <td class="px-6 py-4 text-slate-500 text-sm">{{ $student['class'] }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-bold rounded">{{ $student['present'] }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-amber-50 text-amber-600 text-[10px] font-bold rounded">{{ $student['late'] }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-bold text-emerald-600">{{ $student['presentPercentage'] }}%</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-slate-500">No data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @assets
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    @endassets

    @script
    <script>
        const shRenderCharts = () => {
            if (typeof Chart === 'undefined') return;
            const node = document.getElementById('sh-analytics-data');
            if (!node) return;
            let payload;
            try { payload = JSON.parse(node.dataset.payload); } catch (e) { return; }

            const draw = (id, config) => {
                const canvas = document.getElementById(id);
                if (!canvas) return;
                const existing = Chart.getChart(canvas);
                if (existing) existing.destroy();
                new Chart(canvas, config);
            };

            // Grafik Kehadiran (line trend)
            draw('chartTrend', {
                type: 'line',
                data: {
                    labels: payload.trend.labels,
                    datasets: [
                        { label: 'Hadir', data: payload.trend.present, borderColor: '#059669', backgroundColor: 'rgba(5,150,105,.1)', tension: .4, fill: true },
                        { label: 'Telat', data: payload.trend.late, borderColor: '#d97706', backgroundColor: 'rgba(217,119,6,.08)', tension: .4, fill: true },
                        { label: 'Alpa', data: payload.trend.absent, borderColor: '#e11d48', backgroundColor: 'rgba(225,29,72,.08)', tension: .4, fill: true },
                    ],
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true } } },
            });

            // Distribusi Status (doughnut)
            draw('chartDist', {
                type: 'doughnut',
                data: {
                    labels: payload.distribution.labels,
                    datasets: [{ data: payload.distribution.data, backgroundColor: ['#059669', '#d97706', '#2563eb', '#7c3aed', '#e11d48'], borderWidth: 0 }],
                },
                options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'bottom' } } },
            });

            // Statistik Kelas (bar)
            draw('chartClass', {
                type: 'bar',
                data: {
                    labels: payload.classes.labels,
                    datasets: [{ label: 'Kehadiran %', data: payload.classes.present, backgroundColor: '#4f46e5', borderRadius: 8 }],
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, max: 100 } } },
            });
        };

        shRenderCharts();
        Livewire.hook('commit', ({ succeed }) => {
            succeed(() => requestAnimationFrame(shRenderCharts));
        });
    </script>
    @endscript
</div>
