@extends('layouts.admin')

@section('content')
<div class="space-y-8" x-data="{ editSchedule: { id: null, class_id: null, subject_id: null, teacher_id: null, day: '', start_time: '', end_time: '', room: '' }, showEdit: false }">
    @if (session('status'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
            {{ session('status') }}
        </div>
    @endif
    @if ($errors->has('schedule'))
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">
            {{ $errors->first('schedule') }}
        </div>
    @endif

    <!-- Breadcrumb & Header -->
    <div class="space-y-4">
        <nav class="flex items-center gap-2 text-xs font-medium text-slate-400">
            <a class="hover:text-primary transition-colors" href="{{ route('admin.dashboard') }}">Dashboard</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-slate-300">Master Data</span>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-primary font-semibold">Jadwal Pelajaran</span>
        </nav>
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <h2 class="text-3xl font-headline font-bold text-on-surface tracking-tight">Manajemen Jadwal Pelajaran</h2>
                <p class="text-slate-500 mt-2 max-w-xl text-sm leading-relaxed">Kelola dan atur jadwal mata pelajaran setiap kelas secara presisi untuk memastikan operasional sekolah berjalan lancar.</p>
            </div>
            <div class="flex items-center gap-2 px-6 py-3 bg-gradient-to-br from-primary to-primary-container text-white rounded-xl font-bold text-sm shadow-lg shadow-primary/20">
                <span class="material-symbols-outlined text-lg">event_available</span>
                VALIDASI BENTROK AKTIF
            </div>
        </div>
    </div>

    <!-- Toolbar / Filters -->
    <form method="GET" action="{{ route('admin.master.jadwal') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 p-4 bg-white rounded-2xl border border-slate-100 shadow-sm">
        <div class="space-y-1.5">
            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500 ml-1">Semester</label>
            <select name="semester_id" class="w-full bg-slate-50 border-none rounded-xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-primary/20 py-2.5 px-4">
                <option value="">Semua Semester</option>
                @foreach($semesterOptions as $sem)
                    <option value="{{ $sem->id }}" @selected($selectedSemesterId === (string) $sem->id)>{{ $sem->display_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="space-y-1.5">
            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500 ml-1">Pilih Kelas</label>
            <select name="class_id" class="w-full bg-slate-50 border-none rounded-xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-primary/20 py-2.5 px-4">
                <option value="">Semua Kelas</option>
                @foreach($classOptions as $option)
                    <option value="{{ $option->id }}" @selected($classId === $option->id)>{{ $option->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="space-y-1.5">
            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500 ml-1">Pilih Hari</label>
            <select name="day" class="w-full bg-slate-50 border-none rounded-xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-primary/20 py-2.5 px-4">
                <option value="">Semua Hari</option>
                @foreach($dayOptions as $dayOption)
                    <option value="{{ $dayOption }}" @selected($day === $dayOption)>{{ $dayOption }}</option>
                @endforeach
            </select>
        </div>
        <div class="space-y-1.5">
            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500 ml-1">Cari</label>
            <input name="q" value="{{ $q }}" class="w-full bg-slate-50 border-none rounded-xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-primary/20 py-2.5 px-4" placeholder="Mapel, guru, ruang, kelas..." type="text"/>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="w-full py-2.5 px-4 bg-slate-900 text-white rounded-xl text-sm font-bold hover:opacity-90 transition-colors flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-lg">search</span>
                Terapkan
            </button>
            <a href="{{ route('admin.master.jadwal') }}" class="w-full py-2.5 px-4 bg-primary/5 text-primary rounded-xl text-sm font-bold hover:bg-primary/10 transition-colors flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-lg">filter_alt_off</span>
                Reset
            </a>
        </div>
    </form>

    <form method="POST" action="{{ route('admin.master.jadwal.store') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 p-4 bg-white rounded-2xl border border-slate-100 shadow-sm">
        @csrf
        <div class="space-y-1.5">
            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500 ml-1">Semester</label>
            <select name="semester_akademik_id" class="w-full bg-slate-50 border-none rounded-xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-primary/20 py-2.5 px-4">
                <option value="">Pilih Semester</option>
                @foreach($semesterOptions as $sem)
                    <option value="{{ $sem->id }}" @selected((string) old('semester_akademik_id') === (string) $sem->id)>{{ $sem->display_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="space-y-1.5">
            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500 ml-1">Kelas</label>
            <select name="class_id" class="w-full bg-slate-50 border-none rounded-xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-primary/20 py-2.5 px-4" required>
                <option value="">Pilih Kelas</option>
                @foreach($classOptions as $option)
                    <option value="{{ $option->id }}" @selected((string) old('class_id') === (string) $option->id)>{{ $option->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="space-y-1.5">
            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500 ml-1">Mata Pelajaran</label>
            <select name="subject_id" class="w-full bg-slate-50 border-none rounded-xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-primary/20 py-2.5 px-4" required>
                <option value="">Pilih Mapel</option>
                @foreach($subjectOptions as $subjectOption)
                    <option value="{{ $subjectOption->id }}" @selected((string) old('subject_id') === (string) $subjectOption->id)>{{ $subjectOption->code }} - {{ $subjectOption->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="space-y-1.5">
            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500 ml-1">Guru</label>
            <select name="teacher_id" class="w-full bg-slate-50 border-none rounded-xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-primary/20 py-2.5 px-4" required>
                <option value="">Pilih Guru</option>
                @foreach($teacherOptions as $teacherOption)
                    <option value="{{ $teacherOption->id }}" @selected((string) old('teacher_id') === (string) $teacherOption->id)>{{ $teacherOption->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="space-y-1.5">
            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500 ml-1">Hari</label>
            <select name="day" class="w-full bg-slate-50 border-none rounded-xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-primary/20 py-2.5 px-4" required>
                <option value="">Pilih Hari</option>
                @foreach($dayOptions as $dayOption)
                    <option value="{{ $dayOption }}" @selected(old('day') === $dayOption)>{{ $dayOption }}</option>
                @endforeach
            </select>
        </div>
        <div class="space-y-1.5">
            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500 ml-1">Jam Mulai</label>
            <input name="start_time" value="{{ old('start_time') }}" type="time" class="w-full bg-slate-50 border-none rounded-xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-primary/20 py-2.5 px-4" required>
        </div>
        <div class="space-y-1.5">
            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500 ml-1">Jam Selesai</label>
            <input name="end_time" value="{{ old('end_time') }}" type="time" class="w-full bg-slate-50 border-none rounded-xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-primary/20 py-2.5 px-4" required>
        </div>
        <div class="space-y-1.5">
            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500 ml-1">Ruang</label>
            <input name="room" value="{{ old('room') }}" class="w-full bg-slate-50 border-none rounded-xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-primary/20 py-2.5 px-4" placeholder="Contoh: R-204">
        </div>
        <div class="flex items-end">
            <button type="submit" class="w-full py-2.5 px-4 bg-primary text-white rounded-xl text-sm font-bold hover:opacity-90 transition-colors flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-lg">add</span>
                Simpan Jadwal
            </button>
        </div>
        @if ($errors->any())
            <p class="md:col-span-4 text-xs font-semibold text-rose-600">{{ $errors->first() }}</p>
        @endif
    </form>

    <!-- Schedule Table -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-slate-100">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400">Semester</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400">Hari / Jam</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400">Mata Pelajaran</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400">Guru Pengampu</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 text-center">Ruang</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($schedules as $schedule)
                        @php($initials = collect(explode(' ', trim($schedule->teacher?->name ?? '')))->filter()->take(2)->map(fn($part) => strtoupper(substr($part, 0, 1)))->implode(''))
                        <tr class="hover:bg-indigo-50/30 transition-colors group">
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-indigo-50 text-primary rounded text-[10px] font-bold">{{ $schedule->semesterAkademik?->display_name ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-xs font-black text-on-surface">{{ strtoupper($schedule->day) }}</span>
                                    <span class="text-sm font-bold text-primary">{{ \Illuminate\Support\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Illuminate\Support\Carbon::parse($schedule->end_time)->format('H:i') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-primary">
                                        <span class="material-symbols-outlined text-lg">code</span>
                                    </div>
                                    <div>
                                        <span class="text-sm font-bold text-on-surface">{{ $schedule->subject?->name ?? '-' }}</span>
                                        <p class="text-[10px] text-slate-400 font-medium uppercase">{{ $schedule->subject?->code ?? '-' }} • {{ $schedule->class?->name ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-[10px]">{{ $initials ?: 'TG' }}</div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-on-surface">{{ $schedule->teacher?->name ?? '-' }}</span>
                                        <span class="text-[10px] text-slate-400 font-medium tracking-tighter uppercase">{{ $schedule->teacher?->email ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 bg-slate-100 rounded text-slate-500 text-[10px] font-bold uppercase">{{ $schedule->room ?: '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-right text-slate-400">
                                <div class="flex items-center justify-end gap-1">
                                    <button type="button" @click="editSchedule = { id: {{ $schedule->id }}, class_id: {{ $schedule->class_id }}, subject_id: {{ $schedule->subject_id }}, teacher_id: {{ $schedule->teacher_id }}, day: '{{ $schedule->day }}', start_time: '{{ \Illuminate\Support\Carbon::parse($schedule->start_time)->format('H:i') }}', end_time: '{{ \Illuminate\Support\Carbon::parse($schedule->end_time)->format('H:i') }}', room: '{{ $schedule->room }}' }; showEdit = true" class="p-2 hover:text-primary transition-colors"><span class="material-symbols-outlined text-lg">edit</span></button>
                                    <form method="POST" action="{{ route('admin.master.jadwal.destroy', $schedule) }}" onsubmit="return confirm('Hapus jadwal ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 hover:text-rose-500 transition-colors"><span class="material-symbols-outlined text-lg">delete</span></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-slate-500">Belum ada jadwal pelajaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $schedules->onEachSide(1)->links() }}
        </div>
    </div>

    <!-- Quick Insights -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-primary p-6 rounded-2xl text-white shadow-lg shadow-primary/20">
            <h3 class="text-xs font-bold uppercase tracking-widest opacity-80">Jam Mengajar</h3>
            <p class="text-3xl font-black mt-1">{{ $scheduleStats['weekly_hours'] }} <span class="text-sm font-medium opacity-60">Jam/Minggu</span></p>
            <div class="mt-4 flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-tighter">
                <span class="material-symbols-outlined text-sm">trending_up</span>
                KURIKULUM 2026 ACTIVE
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-50 text-primary rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined">meeting_room</span>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Ruang Terpakai</p>
                <p class="text-2xl font-black text-on-surface leading-tight">{{ $scheduleStats['occupied_rooms'] }} <span class="text-xs font-medium text-slate-400">Ruang Aktif</span></p>
            </div>
        </div>
    </div>

    <!-- Edit Schedule Modal -->
    <div x-show="showEdit" x-transition class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4" @click="showEdit = false" x-cloak>
        <div @click.stop class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full p-8 border border-slate-100">
            <h3 class="text-xl font-bold text-on-surface mb-6">Edit Jadwal Pelajaran</h3>
            <form method="POST" :action="showEdit ? `/admin/master/jadwal/${editSchedule.id}` : '#'">
                @csrf
                @method('PUT')
                <template x-if="showEdit">
                    <div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Kelas</label>
                                <select name="class_id" x-model.number="editSchedule.class_id" class="w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-sm font-bold text-slate-600 focus:ring-2 focus:ring-primary/20" required>
                                    <option value="">Pilih Kelas</option>
                                    @foreach($classOptions as $option)
                                        <option value="{{ $option->id }}">{{ $option->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Mata Pelajaran</label>
                                <select name="subject_id" x-model.number="editSchedule.subject_id" class="w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-sm font-bold text-slate-600 focus:ring-2 focus:ring-primary/20" required>
                                    <option value="">Pilih Mapel</option>
                                    @foreach($subjectOptions as $subjectOption)
                                        <option value="{{ $subjectOption->id }}">{{ $subjectOption->code }} - {{ $subjectOption->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Guru</label>
                                <select name="teacher_id" x-model.number="editSchedule.teacher_id" class="w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-sm font-bold text-slate-600 focus:ring-2 focus:ring-primary/20" required>
                                    <option value="">Pilih Guru</option>
                                    @foreach($teacherOptions as $teacherOption)
                                        <option value="{{ $teacherOption->id }}">{{ $teacherOption->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Hari</label>
                                <select name="day" x-model="editSchedule.day" class="w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-sm font-bold text-slate-600 focus:ring-2 focus:ring-primary/20" required>
                                    <option value="">Pilih Hari</option>
                                    @foreach($dayOptions as $dayOption)
                                        <option value="{{ $dayOption }}">{{ $dayOption }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Jam Mulai</label>
                                <input type="time" name="start_time" x-model="editSchedule.start_time" class="w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-sm font-bold text-slate-600 focus:ring-2 focus:ring-primary/20" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Jam Selesai</label>
                                <input type="time" name="end_time" x-model="editSchedule.end_time" class="w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-sm font-bold text-slate-600 focus:ring-2 focus:ring-primary/20" required>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-slate-700 mb-2">Ruang</label>
                                <input type="text" name="room" x-model="editSchedule.room" class="w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-sm font-bold text-slate-600 focus:ring-2 focus:ring-primary/20" placeholder="Contoh: R-204">
                            </div>
                        </div>
                        <div class="flex gap-3 mt-8">
                            <button type="button" @click="showEdit = false" class="flex-1 py-3 px-4 rounded-2xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 transition-all">Batal</button>
                            <button type="submit" class="flex-1 py-3 px-4 rounded-2xl bg-primary text-white font-bold hover:opacity-90 transition-all">Simpan</button>
                        </div>
                    </div>
                </template>
            </form>
        </div>
    </div>
</div>
@endsection
