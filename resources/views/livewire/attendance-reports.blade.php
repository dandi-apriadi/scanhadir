<div class="space-y-8 px-6 py-8 max-w-[1440px] mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-on-surface mb-2">Attendance Reports</h1>
            <p class="text-slate-500">Generate detailed reports with filters and XLSX export</p>
        </div>
        <button wire:click="exportXLSX" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg transition-colors flex items-center gap-2">
            <span class="material-symbols-outlined">download</span>
            Export to XLSX
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/15 p-6 space-y-4">
        <div class="flex gap-4 flex-wrap items-end">
            <div>
                <label class="block text-xs text-slate-500 uppercase font-bold mb-2">From Date</label>
                <input type="date" wire:model="dateFrom" class="px-4 py-2.5 bg-surface-container-low border border-outline-variant/15 rounded-lg text-sm font-semibold outline-none"/>
            </div>
            <div>
                <label class="block text-xs text-slate-500 uppercase font-bold mb-2">To Date</label>
                <input type="date" wire:model="dateTo" class="px-4 py-2.5 bg-surface-container-low border border-outline-variant/15 rounded-lg text-sm font-semibold outline-none"/>
            </div>
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
                <label class="block text-xs text-slate-500 uppercase font-bold mb-2">Status</label>
                <select wire:model="selectedStatus" class="px-4 py-2.5 bg-surface-container-low border border-outline-variant/15 rounded-lg text-sm font-semibold outline-none">
                    <option value="">All Status</option>
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs text-slate-500 uppercase font-bold mb-2">Search Student</label>
                <input type="text" wire:model="searchStudent" placeholder="Name, Email, or NISN" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/15 rounded-lg text-sm font-semibold outline-none"/>
            </div>
            <button wire:click="resetFilters" class="px-4 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold rounded-lg transition-colors">
                Reset
            </button>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100/50 border border-emerald-200 rounded-xl p-4">
            <p class="text-xs text-emerald-600 uppercase font-bold">Present</p>
            <p class="text-2xl font-bold text-emerald-700">{{ $stats['present'] }}</p>
        </div>
        <div class="bg-gradient-to-br from-amber-50 to-amber-100/50 border border-amber-200 rounded-xl p-4">
            <p class="text-xs text-amber-600 uppercase font-bold">Late</p>
            <p class="text-2xl font-bold text-amber-700">{{ $stats['late'] }}</p>
        </div>
        <div class="bg-gradient-to-br from-rose-50 to-rose-100/50 border border-rose-200 rounded-xl p-4">
            <p class="text-xs text-rose-600 uppercase font-bold">Absent</p>
            <p class="text-2xl font-bold text-rose-700">{{ $stats['absent'] }}</p>
        </div>
        <div class="bg-gradient-to-br from-blue-50 to-blue-100/50 border border-blue-200 rounded-xl p-4">
            <p class="text-xs text-blue-600 uppercase font-bold">Sick</p>
            <p class="text-2xl font-bold text-blue-700">{{ $stats['sick'] }}</p>
        </div>
        <div class="bg-gradient-to-br from-purple-50 to-purple-100/50 border border-purple-200 rounded-xl p-4">
            <p class="text-xs text-purple-600 uppercase font-bold">Excused</p>
            <p class="text-2xl font-bold text-purple-700">{{ $stats['excused'] }}</p>
        </div>
        <div class="bg-gradient-to-br from-slate-50 to-slate-100/50 border border-slate-200 rounded-xl p-4">
            <p class="text-xs text-slate-600 uppercase font-bold">Total</p>
            <p class="text-2xl font-bold text-slate-700">{{ $stats['total'] }}</p>
        </div>
    </div>

    <!-- Reports Table -->
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h3 class="text-xl font-bold text-on-surface">Attendance Records</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 border-b border-slate-100 sticky top-0">
                    <tr>
                        <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase cursor-pointer hover:bg-slate-100" wire:click="sortByField('date')">
                            Date
                            @if($sortBy === 'date')
                                <span class="material-symbols-outlined text-xs">{{ $sortOrder === 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                            @endif
                        </th>
                        <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase">Student</th>
                        <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase">NISN</th>
                        <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase">Class</th>
                        <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase">Check-in</th>
                        <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase">Check-out</th>
                        <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reports as $report)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium">{{ $report->date?->format('Y-m-d') ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-primary font-bold text-xs">
                                        {{ substr($report->student?->user?->name ?? 'N', 0, 1) }}
                                    </div>
                                    <span class="text-sm font-semibold text-on-surface">{{ $report->student?->user?->name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-500 text-sm">{{ $report->student?->nisn ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-slate-500 text-sm">{{ $report->student?->class?->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm font-medium">{{ $report->check_in ? $report->check_in->format('H:i:s') : '-' }}</td>
                            <td class="px-6 py-4 text-sm font-medium">{{ $report->check_out ? $report->check_out->format('H:i:s') : '-' }}</td>
                            <td class="px-6 py-4 text-right">
                                <span class="px-2 py-1 text-[10px] font-bold rounded 
                                    @if($report->status === 'present') bg-emerald-50 text-emerald-600
                                    @elseif($report->status === 'late') bg-amber-50 text-amber-600
                                    @elseif($report->status === 'sick') bg-blue-50 text-blue-600
                                    @elseif($report->status === 'excused') bg-purple-50 text-purple-600
                                    @else bg-rose-50 text-rose-600 @endif">
                                    {{ strtoupper($report->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-slate-500">No attendance records found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($reports->count() > 0)
            <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between">
                <p class="text-xs text-slate-500">Showing {{ $reports->firstItem() }} to {{ $reports->lastItem() }} of {{ $reports->total() }} records</p>
                <div class="flex gap-2">
                    @if($reports->onFirstPage())
                        <button disabled class="px-3 py-1 bg-slate-100 text-slate-400 rounded text-sm font-semibold cursor-not-allowed">← Previous</button>
                    @else
                        <button wire:click="previousPage" class="px-3 py-1 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded text-sm font-semibold">← Previous</button>
                    @endif
                    
                    @if($reports->hasMorePages())
                        <button wire:click="nextPage" class="px-3 py-1 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded text-sm font-semibold">Next →</button>
                    @else
                        <button disabled class="px-3 py-1 bg-slate-100 text-slate-400 rounded text-sm font-semibold cursor-not-allowed">Next →</button>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
