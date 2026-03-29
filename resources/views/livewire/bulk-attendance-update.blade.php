<div class="space-y-8 px-6 py-8 max-w-[1440px] mx-auto">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold text-on-surface mb-2">Bulk Attendance Update</h1>
        <p class="text-slate-500">Update attendance status for multiple students at once</p>
    </div>

    <!-- Success/Error Message -->
    @if($message)
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-700 text-sm font-semibold">
            {{ $message }}
        </div>
    @endif

    <!-- Selection Panel -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left: Filters and Selection -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Select Class -->
            <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/15 p-6">
                <h3 class="text-xl font-bold text-on-surface mb-4">1. Select Class</h3>
                <select wire:model="selectedClass" class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/15 rounded-lg text-sm font-semibold outline-none">
                    <option value="">-- Choose a Class --</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Select Date -->
            @if($selectedClass)
                <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/15 p-6">
                    <h3 class="text-xl font-bold text-on-surface mb-4">2. Select Date</h3>
                    <input type="date" wire:model="selectedDate" class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/15 rounded-lg text-sm font-semibold outline-none"/>
                </div>
            @endif

            <!-- Select Status -->
            @if($selectedClass && $selectedDate)
                <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/15 p-6">
                    <h3 class="text-xl font-bold text-on-surface mb-4">3. Select Status to Update</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($statusOptions as $value => $label)
                            <button 
                                wire:click="$set('selectedStatus', '{{ $value }}')"
                                class="px-4 py-3 text-sm font-semibold rounded-lg transition-colors border-2 
                                    @if($selectedStatus === $value)
                                        border-primary bg-primary/10 text-primary
                                    @else
                                        border-outline-variant/15 bg-surface-container-low text-on-surface hover:bg-surface-container-high
                                    @endif">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Student Selection -->
            @if($selectedClass && $selectedDate && $selectedStatus)
                <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/15 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold text-on-surface">4. Select Students ({{ count($selectedStudents) }} selected)</h3>
                        <div class="flex gap-2">
                            <button wire:click="selectAllStudents" class="px-3 py-1 text-xs bg-emerald-100 text-emerald-700 font-bold rounded">Select All</button>
                            <button wire:click="deselectAllStudents" class="px-3 py-1 text-xs bg-slate-200 text-slate-700 font-bold rounded">Deselect All</button>
                        </div>
                    </div>

                    @if($students->isNotEmpty())
                        <div class="space-y-2 max-h-96 overflow-y-auto">
                            @foreach($students as $student)
                                <div class="flex items-center gap-3 p-3 hover:bg-slate-50 rounded-lg">
                                    <input 
                                        type="checkbox" 
                                        wire:click="toggleStudent({{ $student->id }})"
                                        {{ in_array($student->id, $selectedStudents) ? 'checked' : '' }}
                                        class="w-4 h-4 rounded border-outline-variant/15 text-primary cursor-pointer">
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-on-surface">{{ $student->user->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $student->nisn }}</p>
                                    </div>
                                    @if(isset($attendances[$student->id]))
                                        <span class="px-2 py-1 text-[10px] font-bold rounded
                                            @if($attendances[$student->id]->status === 'present') bg-emerald-50 text-emerald-600
                                            @elseif($attendances[$student->id]->status === 'late') bg-amber-50 text-amber-600
                                            @else bg-slate-50 text-slate-600 @endif">
                                            {{ strtoupper($attendances[$student->id]->status) }}
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-[10px] font-bold rounded bg-slate-50 text-slate-600">NO DATA</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-slate-500 py-4">No students found in this class</p>
                    @endif
                </div>

                <!-- Summary and Action -->
                <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 rounded-xl p-6">
                    <h4 class="text-lg font-bold text-emerald-900 mb-4">Update Summary</h4>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <p class="text-xs text-emerald-700 uppercase font-bold">Students Selected</p>
                            <p class="text-2xl font-bold text-emerald-700">{{ count($selectedStudents) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-emerald-700 uppercase font-bold">New Status</p>
                            <p class="text-lg font-bold text-emerald-700 capitalize">{{ $selectedStatus }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-emerald-700 uppercase font-bold">Date</p>
                            <p class="text-sm font-bold text-emerald-700">{{ $selectedDate ? \Carbon\Carbon::parse($selectedDate)->format('M d, Y') : '-' }}</p>
                        </div>
                    </div>
                    <button 
                        wire:click="updateStatus"
                        {{ count($selectedStudents) === 0 ? 'disabled' : '' }}
                        class="w-full px-6 py-3 bg-emerald-600 hover:bg-emerald-700 
                            {{ count($selectedStudents) === 0 ? 'opacity-50 cursor-not-allowed' : '' }}
                            text-white font-bold rounded-lg transition-colors">
                        Proceed to Update
                    </button>
                </div>
            @endif
        </div>

        <!-- Right: Attendance Summary -->
        @if($selectedClass && $selectedDate)
            <div class="space-y-6">
                <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/15 p-6 sticky top-8">
                    <h3 class="text-lg font-bold text-on-surface mb-4">Attendance Summary</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                            <span class="text-sm text-slate-600">Present</span>
                            <span class="text-lg font-bold text-emerald-600">{{ $attendanceSummary['present'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                            <span class="text-sm text-slate-600">Late</span>
                            <span class="text-lg font-bold text-amber-600">{{ $attendanceSummary['late'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                            <span class="text-sm text-slate-600">Sick</span>
                            <span class="text-lg font-bold text-blue-600">{{ $attendanceSummary['sick'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                            <span class="text-sm text-slate-600">Excused</span>
                            <span class="text-lg font-bold text-purple-600">{{ $attendanceSummary['excused'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-600">Absent</span>
                            <span class="text-lg font-bold text-rose-600">{{ $attendanceSummary['absent'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Confirmation Modal -->
    @if($showConfirmation)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-8 space-y-6">
                <div class="text-center">
                    <div class="w-16 h-16 rounded-full bg-amber-50 flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-amber-600 text-3xl">warning</span>
                    </div>
                    <h3 class="text-xl font-bold text-on-surface">Confirm Update</h3>
                </div>

                <div class="bg-slate-50 rounded-lg p-4 space-y-2">
                    <p class="text-sm"><span class="font-semibold text-slate-700">Students:</span> {{ count($selectedStudents) }}</p>
                    <p class="text-sm"><span class="font-semibold text-slate-700">New Status:</span> <span class="capitalize font-bold text-emerald-600">{{ $selectedStatus }}</span></p>
                    <p class="text-sm"><span class="font-semibold text-slate-700">Date:</span> {{ \Carbon\Carbon::parse($selectedDate)->format('M d, Y') }}</p>
                </div>

                <p class="text-sm text-slate-600">This action cannot be undone. Proceed with caution.</p>

                <div class="flex gap-3">
                    <button 
                        wire:click="cancel"
                        class="flex-1 px-4 py-3 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button 
                        wire:click="confirmUpdate"
                        class="flex-1 px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg transition-colors">
                        Confirm Update
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
