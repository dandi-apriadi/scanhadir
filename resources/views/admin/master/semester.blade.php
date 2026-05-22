@extends('layouts.admin')

@section('content')
<div class="space-y-8" x-data="{ showCreateModal: false, editSemester: { id: null, nama_semester: '', tahun_ajaran: '', tanggal_mulai: '', tanggal_selesai: '', is_active: false } }">
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
            <span class="text-indigo-600">Semester Akademik</span>
        </nav>
        <div class="flex items-end justify-between">
            <div>
                <h2 class="text-3xl font-extrabold text-on-surface tracking-tight font-headline">Semester Akademik</h2>
                <p class="text-slate-500 mt-1 text-sm">Kelola periode semester akademik untuk penjadwalan mata pelajaran.</p>
            </div>
            <button type="button" @click="showCreateModal = true" class="bg-gradient-to-r from-primary to-primary-container text-white px-6 py-3 rounded-xl font-bold text-sm flex items-center gap-2 shadow-lg shadow-indigo-200 hover:scale-95 transition-transform duration-150">
                <span class="material-symbols-outlined">add</span>
                Tambah Semester
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center">
                <span class="material-symbols-outlined text-primary text-2xl">calendar_month</span>
            </div>
            <div>
                <p class="text-2xl font-black text-on-surface">{{ $semesterStats['total'] }}</p>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Semester</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-emerald-100 shadow-sm p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center">
                <span class="material-symbols-outlined text-emerald-600 text-2xl">check_circle</span>
            </div>
            <div>
                <p class="text-2xl font-black text-emerald-600">{{ $semesterStats['active'] }}</p>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Semester Aktif</p>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Semester</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tahun Ajaran</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Periode</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Status</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($semesters as $semester)
                        <tr class="hover:bg-indigo-50/30 transition-colors group">
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-3 font-bold text-on-surface">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-primary flex items-center justify-center">
                                        <span class="material-symbols-outlined text-sm">calendar_month</span>
                                    </div>
                                    <span>{{ $semester->nama_semester }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-sm font-bold text-slate-600">{{ $semester->tahun_ajaran }}</td>
                            <td class="px-6 py-5 text-sm text-slate-500">
                                {{ \Carbon\Carbon::parse($semester->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($semester->tanggal_selesai)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-5 text-center">
                                @if($semester->is_active)
                                    <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold">Aktif</span>
                                @else
                                    <span class="px-3 py-1 bg-slate-100 text-slate-500 rounded-full text-xs font-bold">Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-right">
                                <div class="flex items-center justify-end gap-1 text-slate-400">
                                    <button type="button" @click="editSemester = { id: {{ $semester->id }}, nama_semester: '{{ $semester->nama_semester }}', tahun_ajaran: '{{ $semester->tahun_ajaran }}', tanggal_mulai: '{{ $semester->tanggal_mulai->format('Y-m-d') }}', tanggal_selesai: '{{ $semester->tanggal_selesai->format('Y-m-d') }}', is_active: {{ $semester->is_active ? 'true' : 'false' }} }" class="p-2 hover:text-primary transition-colors"><span class="material-symbols-outlined text-[20px]">edit_note</span></button>
                                    <form method="POST" action="{{ route('admin.master.semester.destroy', $semester->id) }}" onsubmit="return confirm('Hapus data semester ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 hover:text-rose-500 transition-colors"><span class="material-symbols-outlined text-[20px]">delete</span></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-slate-500">Belum ada data semester.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 bg-slate-50/50 border-t border-slate-50 flex items-center justify-between">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Showing {{ $semesters->count() }} of {{ $semesters->total() }} Semesters</span>
            {{ $semesters->onEachSide(1)->links() }}
        </div>
    </div>

    <!-- Create Modal -->
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="showCreateModal = false">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 space-y-4">
            <h3 class="text-lg font-bold text-on-surface">Tambah Semester Baru</h3>
            <form method="POST" action="{{ route('admin.master.semester.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Nama Semester</label>
                    <input name="nama_semester" value="{{ old('nama_semester') }}" class="w-full bg-slate-50 border-none rounded-xl text-sm font-semibold focus:ring-2 focus:ring-primary/20 py-2.5 px-4" placeholder="Contoh: Semester Ganjil" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Tahun Ajaran</label>
                    <input name="tahun_ajaran" value="{{ old('tahun_ajaran') }}" class="w-full bg-slate-50 border-none rounded-xl text-sm font-semibold focus:ring-2 focus:ring-primary/20 py-2.5 px-4" placeholder="Contoh: 2025/2026" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Tanggal Mulai</label>
                        <input name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" type="date" class="w-full bg-slate-50 border-none rounded-xl text-sm font-semibold focus:ring-2 focus:ring-primary/20 py-2.5 px-4" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Tanggal Selesai</label>
                        <input name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" type="date" class="w-full bg-slate-50 border-none rounded-xl text-sm font-semibold focus:ring-2 focus:ring-primary/20 py-2.5 px-4" required>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input name="is_active" type="checkbox" value="1" id="is_active_create" class="rounded border-slate-300 text-primary focus:ring-primary">
                    <label for="is_active_create" class="text-sm font-semibold text-slate-600">Set sebagai semester aktif</label>
                </div>
                <div class="flex items-center justify-end gap-2 pt-4">
                    <button type="button" @click="showCreateModal = false" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-sm font-bold hover:bg-slate-200 transition-colors">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-xl text-sm font-bold hover:opacity-90 transition-colors">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div x-show="editSemester.id" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="editSemester = { id: null }">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 space-y-4">
            <h3 class="text-lg font-bold text-on-surface">Edit Semester</h3>
            <form :action="'/admin/master/semester/' + editSemester.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Nama Semester</label>
                    <input name="nama_semester" x-model="editSemester.nama_semester" class="w-full bg-slate-50 border-none rounded-xl text-sm font-semibold focus:ring-2 focus:ring-primary/20 py-2.5 px-4" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Tahun Ajaran</label>
                    <input name="tahun_ajaran" x-model="editSemester.tahun_ajaran" class="w-full bg-slate-50 border-none rounded-xl text-sm font-semibold focus:ring-2 focus:ring-primary/20 py-2.5 px-4" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Tanggal Mulai</label>
                        <input name="tanggal_mulai" x-model="editSemester.tanggal_mulai" type="date" class="w-full bg-slate-50 border-none rounded-xl text-sm font-semibold focus:ring-2 focus:ring-primary/20 py-2.5 px-4" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Tanggal Selesai</label>
                        <input name="tanggal_selesai" x-model="editSemester.tanggal_selesai" type="date" class="w-full bg-slate-50 border-none rounded-xl text-sm font-semibold focus:ring-2 focus:ring-primary/20 py-2.5 px-4" required>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input name="is_active" type="checkbox" value="1" x-model="editSemester.is_active" id="is_active_edit" class="rounded border-slate-300 text-primary focus:ring-primary">
                    <label for="is_active_edit" class="text-sm font-semibold text-slate-600">Set sebagai semester aktif</label>
                </div>
                <div class="flex items-center justify-end gap-2 pt-4">
                    <button type="button" @click="editSemester = { id: null }" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-sm font-bold hover:bg-slate-200 transition-colors">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-xl text-sm font-bold hover:opacity-90 transition-colors">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
