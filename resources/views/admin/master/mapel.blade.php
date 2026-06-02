@extends('layouts.admin')

@section('content')
<div class="p-8 max-w-7xl mx-auto" x-data="{ editSubject: null }">
    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-2 text-xs font-black text-slate-400 mb-6 tracking-[0.2em] uppercase">
        <a class="hover:text-primary transition-colors" href="{{ route('admin.dashboard') }}">Dashboard</a>
        <span class="material-symbols-outlined text-[10px]" data-icon="chevron_right">chevron_right</span>
        <span class="text-slate-400">Master Data</span>
        <span class="material-symbols-outlined text-[10px]" data-icon="chevron_right">chevron_right</span>
        <span class="text-indigo-600">Mata Pelajaran</span>
    </nav>

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
        <div class="space-y-1">
            <h2 class="font-headline text-3xl font-extrabold tracking-tight text-on-surface">Mata Pelajaran</h2>
            <p class="text-slate-400 font-medium text-sm">Kelola data mata pelajaran dan kurikulum pendidikan.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="bg-indigo-50/50 p-1 rounded-2xl flex items-center gap-1">
                <button class="px-4 py-2 bg-white rounded-xl shadow-sm text-primary text-[10px] font-black uppercase tracking-widest flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">list</span>
                    Table
                </button>
                <button class="px-4 py-2 text-slate-400 text-[10px] font-black uppercase tracking-widest flex items-center gap-2 hover:text-slate-600 cursor-pointer transition-colors">
                    <span class="material-symbols-outlined text-sm">grid_view</span>
                    Grid
                </button>
            </div>
        </div>
    </div>

    @if (session('status'))
        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
            {{ session('status') }}
        </div>
    @endif
    @if ($errors->has('mapel'))
        <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">
            {{ $errors->first('mapel') }}
        </div>
    @endif

    <!-- Management Section Card -->
    <section class="bg-white rounded-3xl p-8 shadow-[0_20px_40px_rgba(13,28,46,0.03)] border border-slate-100/50">
        <!-- Toolbar -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
            <form method="GET" action="{{ route('admin.master.mapel') }}" class="flex flex-col sm:flex-row gap-3 flex-1">
                <div class="relative flex-1 max-w-md">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-300">
                        <span class="material-symbols-outlined text-xl">search</span>
                    </span>
                    <input name="q" value="{{ $q }}" class="w-full bg-slate-50 border-none rounded-2xl py-3 pl-11 pr-4 text-sm font-medium focus:ring-2 focus:ring-primary/20 transition-all placeholder:text-slate-300" placeholder="Cari kode atau nama mapel..." type="text">
                </div>
                <div class="relative group">
                    <select name="group" class="appearance-none bg-slate-50 border-none rounded-2xl py-3 pl-4 pr-10 text-sm font-bold text-slate-600 focus:ring-2 focus:ring-primary/20 transition-all min-w-[180px]">
                        <option value="">Semua Kelompok</option>
                        @foreach($groupOptions as $groupOption)
                            <option value="{{ $groupOption }}" @selected($group === $groupOption)>{{ $groupOption }}</option>
                        @endforeach
                    </select>
                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                        <span class="material-symbols-outlined text-lg">expand_more</span>
                    </span>
                </div>
                <div class="relative group">
                    <select name="semester_id" class="appearance-none bg-slate-50 border-none rounded-2xl py-3 pl-4 pr-10 text-sm font-bold text-slate-600 focus:ring-2 focus:ring-primary/20 transition-all min-w-[200px]">
                        <option value="">Semua Semester</option>
                        @foreach($semesterList as $sem)
                            <option value="{{ $sem->id }}" @selected($selectedSemesterId === (string) $sem->id)>{{ $sem->display_name }}</option>
                        @endforeach
                    </select>
                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                        <span class="material-symbols-outlined text-lg">expand_more</span>
                    </span>
                </div>
                <button type="submit" class="bg-slate-900 hover:opacity-90 text-white font-black text-xs uppercase tracking-widest py-3.5 px-6 rounded-2xl flex items-center justify-center gap-2 transition-all">Cari</button>
            </form>
            <div class="bg-indigo-50 text-indigo-600 font-black text-xs uppercase tracking-widest py-3.5 px-6 rounded-2xl flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-lg">database</span>
                Data Sinkron DB
            </div>
        </div>

        <form method="POST" action="{{ route('admin.master.mapel.store') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3 mb-8">
            @csrf
            <input name="code" value="{{ old('code') }}" class="bg-slate-50 border-none rounded-2xl py-3 px-4 text-sm font-semibold focus:ring-2 focus:ring-primary/20" placeholder="Kode (contoh: PPLG-01)" required>
            <input name="name" value="{{ old('name') }}" class="bg-slate-50 border-none rounded-2xl py-3 px-4 text-sm font-semibold focus:ring-2 focus:ring-primary/20" placeholder="Nama mata pelajaran" required>
            <select name="group" class="appearance-none bg-slate-50 border-none rounded-2xl py-3 px-4 text-sm font-bold text-slate-600 focus:ring-2 focus:ring-primary/20" required>
                <option value="">Pilih kelompok</option>
                @foreach($groupOptions as $groupOption)
                    <option value="{{ $groupOption }}" @selected(old('group') === $groupOption)>{{ $groupOption }}</option>
                @endforeach
            </select>
            <select name="semester_akademik_id" class="appearance-none bg-slate-50 border-none rounded-2xl py-3 px-4 text-sm font-bold text-slate-600 focus:ring-2 focus:ring-primary/20">
                <option value="">Pilih Semester</option>
                @foreach($semesterList as $sem)
                    <option value="{{ $sem->id }}" @selected((string) old('semester_akademik_id') === (string) $sem->id)>{{ $sem->display_name }}</option>
                @endforeach
            </select>
            <input name="sks" value="{{ old('sks', 3) }}" type="number" min="1" max="10" class="bg-slate-50 border-none rounded-2xl py-3 px-4 text-sm font-semibold focus:ring-2 focus:ring-primary/20" placeholder="SKS">
            <div class="md:col-span-5 flex justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs uppercase tracking-widest py-3.5 px-6 rounded-2xl flex items-center justify-center gap-2 shadow-lg shadow-indigo-600/20 active:scale-95 transition-all">
                    <span class="material-symbols-outlined text-lg">add_circle</span>
                    Tambah Mapel
                </button>
            </div>
            @if ($errors->any())
                <p class="md:col-span-5 text-xs font-semibold text-rose-600">{{ $errors->first() }}</p>
            @endif
        </form>

        <!-- Modern Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-separate border-spacing-y-2">
                <thead>
                    <tr class="text-slate-300 font-bold text-[10px] uppercase tracking-[0.2em]">
                        <th class="px-6 py-4 text-center w-16 uppercase">No</th>
                        <th class="px-6 py-4 uppercase">Kode</th>
                        <th class="px-6 py-4 uppercase">Nama Mata Pelajaran</th>
                        <th class="px-6 py-4 uppercase">Semester</th>
                        <th class="px-6 py-4 uppercase text-center">SKS</th>
                        <th class="px-6 py-4 uppercase">Kelompok</th>
                        <th class="px-6 py-4 text-right uppercase">Kontrol</th>
                    </tr>
                </thead>
                <tbody class="text-sm font-semibold">
                    @forelse($subjects as $subject)
                    <tr class="group hover:bg-indigo-50/40 transition-all duration-300">
                        <td class="px-6 py-4 text-center text-slate-400 bg-slate-50/50 group-hover:bg-transparent rounded-l-2xl font-mono text-xs">{{ (($subjects->currentPage() - 1) * $subjects->perPage()) + $loop->iteration }}</td>
                        <td class="px-6 py-4 font-mono font-bold text-indigo-700">{{ $subject->code }}</td>
                        <td class="px-6 py-4 text-on-surface">{{ $subject->name }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-indigo-50 text-primary rounded text-[10px] font-bold">{{ $subject->semesterAkademik?->display_name ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded text-xs font-bold">{{ $subject->sks ?? 3 }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1.5 {{ $subject->group === 'Kejuruan' ? 'bg-indigo-100 text-indigo-600' : 'bg-slate-100 text-slate-500' }} rounded-lg text-[10px] font-black uppercase tracking-widest ring-1 ring-inset {{ $subject->group === 'Kejuruan' ? 'ring-indigo-200' : 'ring-slate-200' }}">
                                {{ $subject->group }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right rounded-r-2xl">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button type="button" @click="editSubject = {{ json_encode($subject->only(['id', 'code', 'name', 'group', 'sks', 'semester_akademik_id'])) }}" class="w-9 h-9 flex items-center justify-center rounded-xl bg-white text-slate-400 hover:text-indigo-600 shadow-sm ring-1 ring-slate-100 transition-all active:scale-90" title="Edit mapel">
                                    <span class="material-symbols-outlined text-lg">edit</span>
                                </button>
                                <form method="POST" action="{{ route('admin.master.mapel.destroy', $subject) }}" onsubmit="return confirm('Hapus mapel ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-9 h-9 flex items-center justify-center rounded-xl bg-white text-slate-400 hover:text-rose-600 shadow-sm ring-1 ring-slate-100 transition-all active:scale-90">
                                        <span class="material-symbols-outlined text-lg">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-sm text-slate-500">Belum ada data mata pelajaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer / Pagination -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-8 pt-6 border-t border-slate-50">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest italic">Menampilkan {{ $subjects->count() }} dari {{ $subjects->total() }} data mata pelajaran</p>
            <div>{{ $subjects->onEachSide(1)->links() }}</div>
        </div>
    </section>

    <!-- Edit Modal -->
    <div x-show="editSubject" x-transition class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4" @click="editSubject = null">
        <div @click.stop class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-8 border border-slate-100">
            <h3 class="text-xl font-bold text-on-surface mb-6">Edit Mata Pelajaran</h3>
            <form method="POST" x-show="editSubject" :action="editSubject ? `/admin/master/mapel/${editSubject.id}` : '#'">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Kode</label>
                        <input type="text" name="code" x-model="editSubject.code" class="w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-sm font-semibold focus:ring-2 focus:ring-primary/20" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nama Mata Pelajaran</label>
                        <input type="text" name="name" x-model="editSubject.name" class="w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-sm font-semibold focus:ring-2 focus:ring-primary/20" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Kelompok</label>
                        <select name="group" x-model="editSubject.group" class="appearance-none w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-sm font-bold text-slate-600 focus:ring-2 focus:ring-primary/20" required>
                            <option value="Kejuruan">Kejuruan</option>
                            <option value="Umum">Umum</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Semester</label>
                        <select name="semester_akademik_id" x-model.number="editSubject.semester_akademik_id" class="appearance-none w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-sm font-bold text-slate-600 focus:ring-2 focus:ring-primary/20">
                            <option value="">Pilih Semester</option>
                            @foreach($semesterList as $sem)
                                <option value="{{ $sem->id }}">{{ $sem->display_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">SKS</label>
                        <input type="number" name="sks" x-model.number="editSubject.sks" min="1" max="10" class="w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-sm font-semibold focus:ring-2 focus:ring-primary/20" required>
                    </div>
                </div>
                <div class="flex gap-3 mt-8">
                    <button type="button" @click="editSubject = null" class="flex-1 py-3 px-4 rounded-2xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 transition-all">Batal</button>
                    <button type="submit" class="flex-1 py-3 px-4 rounded-2xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition-all">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bottom Stats / Bento Insight -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-10">
        <div class="bg-indigo-600 rounded-[2rem] p-8 text-white overflow-hidden relative group shadow-xl shadow-indigo-200">
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-widest opacity-80 mb-2">Total Mapel</p>
                <h4 class="text-4xl font-black headline-font">{{ $subjectStats['total'] }}</h4>
            </div>
            <span class="material-symbols-outlined absolute -bottom-6 -right-6 text-9xl opacity-10 group-hover:scale-110 transition-transform duration-700">menu_book</span>
        </div>
        <div class="bg-white rounded-[2rem] p-8 border border-slate-100 shadow-sm flex items-center justify-between group">
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Kelompok Kejuruan</p>
                <h4 class="text-3xl font-black headline-font text-indigo-700">{{ $subjectStats['kejuruan'] }} <span class="text-[10px] font-bold text-slate-400 ml-1 uppercase opacity-60">Mapel</span></h4>
            </div>
            <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 transition-transform group-hover:bg-indigo-100">
                <span class="material-symbols-outlined text-3xl">engineering</span>
            </div>
        </div>
        <div class="bg-white rounded-[2rem] p-8 border border-slate-100 shadow-sm flex items-center justify-between group">
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Kelompok Umum</p>
                <h4 class="text-3xl font-black headline-font text-slate-700">{{ $subjectStats['umum'] }} <span class="text-[10px] font-bold text-slate-400 ml-1 uppercase opacity-60">Mapel</span></h4>
            </div>
            <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 transition-transform group-hover:bg-slate-100 group-hover:text-slate-600">
                <span class="material-symbols-outlined text-3xl">public</span>
            </div>
        </div>
    </div>
</div>
@endsection
