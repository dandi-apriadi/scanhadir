@extends('layouts.admin')

@section('content')
<div class="space-y-8" x-data="{ showCreateModal: {{ $errors->any() && !$editTeacher ? 'true' : 'false' }}, showEditModal: {{ $editTeacher ? 'true' : 'false' }} }">
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
    <div class="mb-8">
        <nav class="flex text-xs font-semibold text-slate-400 uppercase tracking-widest gap-2 mb-2">
            <a class="hover:text-indigo-600" href="{{ route('admin.dashboard') }}">Dashboard</a>
            <span>/</span>
            <span class="text-slate-300">Master Data</span>
            <span>/</span>
            <span class="text-indigo-600">Guru</span>
        </nav>
        <h2 class="font-headline text-3xl font-bold text-slate-900 tracking-tight">Manajemen Data Guru</h2>
        <p class="text-slate-500 mt-1 text-sm">Kelola informasi tenaga pendidik dan staf sekolah.</p>
    </div>

    <!-- High-End Toolbar -->
    <div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-6 p-4 bg-surface-container-lowest rounded-xl shadow-sm border border-slate-100">
        <form method="GET" action="{{ route('admin.master.guru') }}" class="flex items-center gap-4 w-full md:w-auto">
            <div class="relative w-full md:w-72">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input name="q" value="{{ $q }}" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border-none rounded-lg text-sm focus:ring-2 focus:ring-primary/20 transition-all font-medium" placeholder="Cari guru (nama/email)..." type="text"/>
            </div>
            <button type="submit" class="px-4 py-2.5 bg-slate-900 text-white rounded-lg text-sm font-bold hover:opacity-90 transition-all">Cari</button>
        </form>
        <button type="button" @click="showCreateModal = true" class="w-full md:w-auto px-6 py-2.5 bg-gradient-to-br from-primary to-primary-container text-white font-bold rounded-xl flex items-center justify-center gap-2 hover:opacity-90 transition-all transform hover:scale-[1.02] active:scale-95 shadow-lg shadow-primary/20">
            <span class="material-symbols-outlined">add_circle</span>
            <span>Tambah Guru</span>
        </button>
    </div>

    <!-- Data Table -->
    <div class="bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm border border-slate-100">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">NIP / ID</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Nama Lengkap</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Mata Pelajaran</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($teachers as $teacher)
                        @php($initials = collect(explode(' ', trim($teacher->name)))->filter()->take(2)->map(fn($part) => strtoupper(substr($part, 0, 1)))->implode(''))
                        <tr class="hover:bg-indigo-50/30 transition-colors group">
                            <td class="px-6 py-4 font-mono text-xs font-semibold text-slate-500">{{ str_pad((string) $teacher->id, 12, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-indigo-50 flex items-center justify-center text-primary font-bold text-xs">{{ $initials ?: 'TG' }}</div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-900">{{ $teacher->name }}</p>
                                        <p class="text-[10px] text-slate-400 font-medium">{{ $teacher->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-surface-container-high text-primary rounded-full text-[10px] font-bold">{{ $teacher->assigned_classes_count }} Kelas</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1.5 font-bold text-emerald-600 text-xs">
                                    <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                                    AKTIF
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2 text-slate-400">
                                    <a href="{{ route('admin.master.guru', array_merge(request()->query(), ['edit' => $teacher->id])) }}" class="hover:text-primary transition-colors"><span class="material-symbols-outlined text-[20px]">edit</span></a>
                                    <form method="POST" action="{{ route('admin.master.guru.destroy', $teacher->id) }}" onsubmit="return confirm('Hapus data guru ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="hover:text-rose-500 transition-colors"><span class="material-symbols-outlined text-[20px]">delete</span></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-slate-500">Belum ada data guru.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Showing {{ $teachers->count() }} of {{ $teachers->total() }} Teachers</p>
            <div>{{ $teachers->onEachSide(1)->links() }}</div>
        </div>
    </div>

    <!-- Stats Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-8">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-50 text-primary rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined">groups</span>
            </div>
            <div>
                <p class="text-2xl font-black text-slate-900 leading-none">{{ $teacherStats['total'] }}</p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Total Guru</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 flex items-center gap-4 text-emerald-600">
            <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined">verified</span>
            </div>
            <div>
                <p class="text-2xl font-black leading-none">{{ $teacherStats['active'] }}</p>
                <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest mt-1">Status Aktif</p>
            </div>
        </div>
    </div>

    <div x-show="showCreateModal" x-transition class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4" @click.self="showCreateModal = false">
        <div class="w-full max-w-2xl rounded-2xl bg-white border border-slate-100 shadow-2xl p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold text-slate-900">Tambah Guru</h3>
                <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-slate-700"><span class="material-symbols-outlined">close</span></button>
            </div>
            <form method="POST" action="{{ route('admin.master.guru.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Nama</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2.5 rounded-lg bg-slate-50 border-none text-sm focus:ring-2 focus:ring-primary/20" placeholder="Nama guru" />
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-2.5 rounded-lg bg-slate-50 border-none text-sm focus:ring-2 focus:ring-primary/20" placeholder="guru@sekolah.sch.id" />
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Password</label>
                    <input type="password" name="password" required class="w-full px-4 py-2.5 rounded-lg bg-slate-50 border-none text-sm focus:ring-2 focus:ring-primary/20" placeholder="Minimal 8 karakter" />
                </div>
                <div class="md:col-span-2 flex justify-end gap-2 mt-2">
                    <button type="button" @click="showCreateModal = false" class="px-4 py-2.5 rounded-lg bg-slate-100 text-slate-700 text-sm font-bold">Batal</button>
                    <button type="submit" class="px-4 py-2.5 bg-slate-900 text-white rounded-lg text-sm font-bold">Simpan Guru</button>
                </div>
            </form>
        </div>
    </div>

    @if ($editTeacher)
        <div x-show="showEditModal" x-transition class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4" @click.self="window.location='{{ route('admin.master.guru', request()->except('edit')) }}'">
            <div class="w-full max-w-2xl rounded-2xl bg-white border border-slate-100 shadow-2xl p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-slate-900">Edit Guru</h3>
                    <a href="{{ route('admin.master.guru', request()->except('edit')) }}" class="text-slate-400 hover:text-slate-700"><span class="material-symbols-outlined">close</span></a>
                </div>
                <form method="POST" action="{{ route('admin.master.guru.update', $editTeacher->id) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Nama</label>
                        <input type="text" name="name" value="{{ old('name', $editTeacher->name) }}" required class="w-full px-4 py-2.5 rounded-lg bg-slate-50 border-none text-sm focus:ring-2 focus:ring-primary/20" />
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email', $editTeacher->email) }}" required class="w-full px-4 py-2.5 rounded-lg bg-slate-50 border-none text-sm focus:ring-2 focus:ring-primary/20" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Password Baru</label>
                        <input type="password" name="password" class="w-full px-4 py-2.5 rounded-lg bg-slate-50 border-none text-sm focus:ring-2 focus:ring-primary/20" placeholder="Kosongkan jika tidak diubah" />
                    </div>
                    <div class="md:col-span-2 flex justify-end gap-2 mt-2">
                        <a href="{{ route('admin.master.guru', request()->except('edit')) }}" class="px-4 py-2.5 rounded-lg bg-slate-100 text-slate-700 text-sm font-bold">Batal</a>
                        <button type="submit" class="px-4 py-2.5 bg-primary text-white rounded-lg text-sm font-bold">Update Guru</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
