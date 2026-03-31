@extends('layouts.admin')

@section('content')
<div class="space-y-8" x-data="{ showCreateModal: {{ $errors->any() && !$editClass ? 'true' : 'false' }}, showEditModal: {{ $editClass ? 'true' : 'false' }} }">
    @if (session('status'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Breadcrumbs & Header -->
    <div class="mb-10">
        <nav class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-2 tracking-wide uppercase">
            <a class="hover:text-indigo-600 transition-colors" href="{{ route('admin.dashboard') }}">Dashboard</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-slate-300">Data Master</span>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-indigo-600">Kelas</span>
        </nav>
        <div class="flex items-end justify-between">
            <div>
                <h2 class="text-3xl font-extrabold text-on-surface tracking-tight font-headline">Daftar Kelas</h2>
                <p class="text-slate-500 mt-1 text-sm">Kelola data ruang kelas dan wali kelas untuk sistem absensi.</p>
            </div>
            <button type="button" @click="showCreateModal = true" class="bg-gradient-to-r from-primary to-primary-container text-white px-6 py-3 rounded-xl font-bold text-sm flex items-center gap-2 shadow-lg shadow-indigo-200 hover:scale-95 transition-transform duration-150">
                <span class="material-symbols-outlined">add</span>
                Tambah Kelas
            </button>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-12 gap-8">
        <!-- Table Section -->
        <div class="col-span-12 lg:col-span-8 bg-surface-container-lowest rounded-2xl shadow-sm overflow-hidden flex flex-col border border-slate-100">
            <!-- Table Toolbar -->
            <div class="p-6 bg-white border-b border-slate-50 flex items-center justify-between">
                <form method="GET" action="{{ route('admin.master.kelas') }}" class="relative w-full max-w-xs">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                    <input name="q" value="{{ $q }}" class="w-full bg-slate-50 border-none rounded-xl py-2.5 pl-11 pr-24 text-sm focus:ring-2 focus:ring-primary/20 transition-all font-medium" placeholder="Cari Kelas..." type="text"/>
                    <button type="submit" class="absolute right-1.5 top-1/2 -translate-y-1/2 px-3 py-1.5 rounded-lg bg-slate-900 text-white text-xs font-bold">Cari</button>
                </form>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Nama Kelas</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Wali Kelas</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Siswa</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($classes as $class)
                            <tr class="hover:bg-indigo-50/30 transition-colors group">
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-3 font-bold text-on-surface">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-50 text-primary flex items-center justify-center font-black text-[10px]">{{ $class->level }}</div>
                                        <span>{{ $class->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-sm font-medium text-slate-600">{{ optional($class->teachers->first())->name ?? '-' }}</td>
                                <td class="px-6 py-5 text-center text-sm font-bold text-on-surface">
                                    <a href="{{ route('admin.master.siswa', ['class_id' => $class->id]) }}" class="inline-flex items-center gap-1 rounded-lg bg-indigo-50 px-2.5 py-1 text-primary hover:bg-indigo-100 transition-colors">
                                        <span>{{ $class->students_count }}</span>
                                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                                    </a>
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <div class="flex items-center justify-end gap-1 text-slate-400">
                                        <a href="{{ route('admin.master.siswa', ['class_id' => $class->id]) }}" class="p-2 hover:text-primary transition-colors" title="Lihat daftar siswa"><span class="material-symbols-outlined text-[20px]">groups</span></a>
                                        <a href="{{ route('admin.master.kelas', array_merge(request()->query(), ['edit' => $class->id])) }}" class="p-2 hover:text-primary transition-colors"><span class="material-symbols-outlined text-[20px]">edit_note</span></a>
                                        <form method="POST" action="{{ route('admin.master.kelas.destroy', $class->id) }}" onsubmit="return confirm('Hapus data kelas ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 hover:text-rose-500 transition-colors"><span class="material-symbols-outlined text-[20px]">delete</span></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-sm text-slate-500">Belum ada data kelas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="p-4 bg-slate-50/50 border-t border-slate-50 flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Showing {{ $classes->count() }} of {{ $classes->total() }} Classes</span>
                <div>{{ $classes->onEachSide(1)->links() }}</div>
            </div>
        </div>

        <!-- Sidebar Stats -->
        <div class="col-span-12 lg:col-span-4 space-y-6">
            <div class="bg-indigo-600 rounded-2xl p-6 text-white relative overflow-hidden shadow-lg shadow-indigo-100">
                <div class="relative z-10">
                    <span class="material-symbols-outlined mb-2 text-indigo-200">school</span>
                    <h3 class="text-sm font-bold uppercase tracking-widest opacity-80">Total Kelas</h3>
                    <p class="text-4xl font-black mt-1">{{ $classStats['total'] }}</p>
                    <p class="text-[10px] font-medium text-indigo-200 mt-4 italic uppercase">Update semester ganjil {{ date('Y', strtotime('-1 year')) }}/{{ date('Y') }}</p>
                </div>
                <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                <h3 class="font-bold text-on-surface text-sm mb-6 uppercase tracking-widest text-slate-400">Distribusi Tingkatan</h3>
                <div class="space-y-5">
                    <div>
                        <div class="flex justify-between text-[11px] font-bold text-slate-500 mb-2">
                            <span>KELAS XII</span>
                            <span class="text-primary">{{ $classStats['levels']['XII'] }} KELAS</span>
                        </div>
                        <div class="h-1.5 bg-slate-50 rounded-full overflow-hidden">
                            <div class="h-full bg-primary rounded-full" style="width: {{ $classStats['total'] > 0 ? round(($classStats['levels']['XII'] / $classStats['total']) * 100, 1) : 0 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-[11px] font-bold text-slate-500 mb-2">
                            <span>KELAS XI</span>
                            <span class="text-primary">{{ $classStats['levels']['XI'] }} KELAS</span>
                        </div>
                        <div class="h-1.5 bg-slate-50 rounded-full overflow-hidden">
                            <div class="h-full bg-primary rounded-full" style="width: {{ $classStats['total'] > 0 ? round(($classStats['levels']['XI'] / $classStats['total']) * 100, 1) : 0 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-[11px] font-bold text-slate-500 mb-2">
                            <span>KELAS X</span>
                            <span class="text-primary">{{ $classStats['levels']['X'] }} KELAS</span>
                        </div>
                        <div class="h-1.5 bg-slate-50 rounded-full overflow-hidden">
                            <div class="h-full bg-primary rounded-full" style="width: {{ $classStats['total'] > 0 ? round(($classStats['levels']['X'] / $classStats['total']) * 100, 1) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div x-show="showCreateModal" x-transition class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4" @click.self="showCreateModal = false">
        <div class="w-full max-w-2xl rounded-2xl bg-white border border-slate-100 shadow-2xl p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold text-slate-900">Tambah Kelas</h3>
                <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-slate-700"><span class="material-symbols-outlined">close</span></button>
            </div>
            <form method="POST" action="{{ route('admin.master.kelas.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @csrf
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Nama Kelas</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full bg-slate-50 border-none rounded-xl py-2.5 px-4 text-sm focus:ring-2 focus:ring-primary/20" placeholder="Contoh: XI RPL 1" />
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Tingkat</label>
                    <select name="level" required class="w-full bg-slate-50 border-none rounded-xl py-2.5 px-4 text-sm focus:ring-2 focus:ring-primary/20">
                        <option value="">Pilih</option>
                        <option value="X" @selected(old('level') === 'X')>X</option>
                        <option value="XI" @selected(old('level') === 'XI')>XI</option>
                        <option value="XII" @selected(old('level') === 'XII')>XII</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Jurusan</label>
                    <input type="text" name="major" value="{{ old('major') }}" required class="w-full bg-slate-50 border-none rounded-xl py-2.5 px-4 text-sm focus:ring-2 focus:ring-primary/20" placeholder="Contoh: RPL" />
                </div>
                <div class="md:col-span-3 flex justify-end gap-2 mt-2">
                    <button type="button" @click="showCreateModal = false" class="px-4 py-2.5 rounded-lg bg-slate-100 text-slate-700 text-sm font-bold">Batal</button>
                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-bold">Simpan Kelas</button>
                </div>
            </form>
        </div>
    </div>

    @if ($editClass)
        <div x-show="showEditModal" x-transition class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4" @click.self="window.location='{{ route('admin.master.kelas', request()->except('edit')) }}'">
            <div class="w-full max-w-2xl rounded-2xl bg-white border border-slate-100 shadow-2xl p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-slate-900">Edit Kelas</h3>
                    <a href="{{ route('admin.master.kelas', request()->except('edit')) }}" class="text-slate-400 hover:text-slate-700"><span class="material-symbols-outlined">close</span></a>
                </div>
                <form method="POST" action="{{ route('admin.master.kelas.update', $editClass->id) }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Nama Kelas</label>
                        <input type="text" name="name" value="{{ old('name', $editClass->name) }}" required class="w-full bg-slate-50 border-none rounded-xl py-2.5 px-4 text-sm focus:ring-2 focus:ring-primary/20" />
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Tingkat</label>
                        <select name="level" required class="w-full bg-slate-50 border-none rounded-xl py-2.5 px-4 text-sm focus:ring-2 focus:ring-primary/20">
                            <option value="X" @selected(old('level', $editClass->level) === 'X')>X</option>
                            <option value="XI" @selected(old('level', $editClass->level) === 'XI')>XI</option>
                            <option value="XII" @selected(old('level', $editClass->level) === 'XII')>XII</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Jurusan</label>
                        <input type="text" name="major" value="{{ old('major', $editClass->major) }}" required class="w-full bg-slate-50 border-none rounded-xl py-2.5 px-4 text-sm focus:ring-2 focus:ring-primary/20" />
                    </div>
                    <div class="md:col-span-3 flex justify-end gap-2 mt-2">
                        <a href="{{ route('admin.master.kelas', request()->except('edit')) }}" class="px-4 py-2.5 rounded-lg bg-slate-100 text-slate-700 text-sm font-bold">Batal</a>
                        <button type="submit" class="px-4 py-2.5 rounded-xl bg-primary text-white text-sm font-bold">Update Kelas</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
