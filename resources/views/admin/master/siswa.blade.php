@extends('layouts.admin')

@section('content')
<div class="space-y-8" x-data="{ showCreateModal: {{ $errors->any() && !$editStudent ? 'true' : 'false' }}, showEditModal: {{ $editStudent ? 'true' : 'false' }}, showAdvancedFilter: {{ ($level !== '' || $major !== '' || $hasQr !== '' || $sort !== 'newest') ? 'true' : 'false' }} }">
    <!-- Breadcrumbs & Header -->
    <nav class="mb-4">
        <ol class="flex items-center gap-2 text-xs font-semibold text-slate-400 uppercase tracking-widest">
            <li><a class="hover:text-primary" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><span class="material-symbols-outlined text-[14px]">chevron_right</span></li>
            <li class="text-slate-300">Data Master</li>
            <li><span class="material-symbols-outlined text-[14px]">chevron_right</span></li>
            <li class="text-primary font-bold">Siswa</li>
        </ol>
    </nav>
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

    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
        <div>
            <h2 class="text-4xl font-bold tracking-tight text-on-surface mb-2 font-headline">Daftar Siswa</h2>
            <p class="text-slate-500 text-sm max-w-xl">Kelola data seluruh siswa, cetak kartu QR kehadiran, dan pantau status keaktifan dalam satu platform terpusat.</p>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" @click="showCreateModal = true" class="flex items-center gap-2 px-6 py-3.5 bg-gradient-to-br from-primary to-primary-container text-white rounded-2xl font-bold shadow-xl shadow-indigo-200/50 hover:shadow-indigo-300/50 active:scale-95 transition-all">
                <span class="material-symbols-outlined" style="font-variation-settings: 'wght' 600;">add</span>
                <span>Tambah Siswa</span>
            </button>
        </div>
    </div>

    <!-- Action Bar & Table Container -->
    <div class="bg-surface-container-lowest rounded-3xl p-2 shadow-sm border border-slate-100">
        <div class="p-6 flex flex-col md:flex-row gap-4 items-center justify-between border-b border-slate-50 mb-2">
            <form method="GET" action="{{ route('admin.master.siswa') }}" class="flex flex-col md:flex-row items-center gap-4 w-full md:w-auto">
                <div class="relative w-full md:w-80 group">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400">search</span>
                    <input name="q" value="{{ $q }}" class="w-full bg-slate-50 border-none rounded-2xl py-3 pl-12 pr-4 text-sm focus:ring-2 focus:ring-indigo-500/10 transition-all outline-none" placeholder="Cari nama, email, atau NISN..." type="text"/>
                </div>
                <div class="relative w-full md:w-56">
                    <select name="class_id" class="w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-sm appearance-none focus:ring-2 focus:ring-indigo-500/10 outline-none cursor-pointer font-bold text-slate-600">
                        <option value="">Semua Kelas</option>
                        @foreach($classOptions as $option)
                            <option value="{{ $option->id }}" @selected($classId === $option->id)>{{ $option->name }}</option>
                        @endforeach
                    </select>
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400 pointer-events-none">expand_more</span>
                </div>
                <button type="submit" class="px-4 py-3 rounded-2xl bg-slate-900 text-white text-sm font-bold">Cari</button>
            </form>
            <div class="flex items-center gap-2">
                <button type="button" @click="showAdvancedFilter = !showAdvancedFilter" class="p-3 text-slate-500 hover:bg-slate-50 rounded-xl transition-colors" :class="showAdvancedFilter ? 'bg-slate-100 text-primary' : ''">
                    <span class="material-symbols-outlined">filter_list</span>
                </button>
                <a href="{{ route('admin.master.siswa.export', request()->query()) }}" class="p-3 text-slate-500 hover:bg-slate-50 rounded-xl transition-colors">
                    <span class="material-symbols-outlined">file_download</span>
                </a>
            </div>
        </div>

        <div x-show="showAdvancedFilter" x-transition class="px-6 pb-5 border-b border-slate-50 mb-2">
            <form method="GET" action="{{ route('admin.master.siswa') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
                <input type="hidden" name="q" value="{{ $q }}">
                <input type="hidden" name="class_id" value="{{ $classId }}">

                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Level</label>
                    <select name="level" class="w-full bg-slate-50 border-none rounded-2xl py-2.5 px-4 text-sm focus:ring-2 focus:ring-indigo-500/10">
                        <option value="">Semua</option>
                        @foreach($levelOptions as $levelOption)
                            <option value="{{ $levelOption }}" @selected($level === $levelOption)>{{ $levelOption }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Jurusan</label>
                    <select name="major" class="w-full bg-slate-50 border-none rounded-2xl py-2.5 px-4 text-sm focus:ring-2 focus:ring-indigo-500/10">
                        <option value="">Semua</option>
                        @foreach($majorOptions as $majorOption)
                            <option value="{{ $majorOption }}" @selected($major === $majorOption)>{{ $majorOption }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">QR Code</label>
                    <select name="has_qr" class="w-full bg-slate-50 border-none rounded-2xl py-2.5 px-4 text-sm focus:ring-2 focus:ring-indigo-500/10">
                        <option value="">Semua</option>
                        <option value="1" @selected($hasQr === '1')>Sudah Ada</option>
                        <option value="0" @selected($hasQr === '0')>Belum Ada</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Urutkan</label>
                    <select name="sort" class="w-full bg-slate-50 border-none rounded-2xl py-2.5 px-4 text-sm focus:ring-2 focus:ring-indigo-500/10">
                        <option value="newest" @selected($sort === 'newest')>Data Terbaru</option>
                        <option value="oldest" @selected($sort === 'oldest')>Data Terlama</option>
                        <option value="name_asc" @selected($sort === 'name_asc')>Nama A-Z</option>
                        <option value="name_desc" @selected($sort === 'name_desc')>Nama Z-A</option>
                        <option value="nisn_asc" @selected($sort === 'nisn_asc')>NISN Kecil-Besar</option>
                        <option value="nisn_desc" @selected($sort === 'nisn_desc')>NISN Besar-Kecil</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="px-4 py-2.5 rounded-2xl bg-slate-900 text-white text-sm font-bold">Terapkan</button>
                    <a href="{{ route('admin.master.siswa', request()->only(['q', 'class_id'])) }}" class="px-4 py-2.5 rounded-2xl bg-slate-100 text-slate-700 text-sm font-bold">Reset</a>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="text-left bg-slate-50/50">
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Nama Lengkap</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">NISN</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Kelas</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">QR Code</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($students as $student)
                        @php($initials = collect(explode(' ', trim($student->user?->name ?? '')))->filter()->take(2)->map(fn($part) => strtoupper(substr($part, 0, 1)))->implode(''))
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-primary font-bold text-xs">{{ $initials ?: 'SW' }}</div>
                                    <div>
                                        <p class="text-sm font-bold text-on-surface">{{ $student->user?->name ?? '-' }}</p>
                                        <p class="text-[10px] text-slate-400 uppercase tracking-tighter">{{ $student->user?->email ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-mono text-slate-500 font-bold">{{ $student->nisn }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 bg-indigo-50 rounded-lg text-xs font-bold text-primary">{{ $student->class?->name ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('students.qrcode', $student->id) }}?download=1" class="w-9 h-9 p-1 bg-white border border-slate-100 rounded-lg group-hover:scale-110 transition-transform cursor-pointer flex items-center justify-center shadow-sm">
                                    <span class="material-symbols-outlined text-slate-400 text-xl">download</span>
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-1 text-slate-400">
                                    <a href="{{ route('admin.master.siswa', array_merge(request()->query(), ['edit' => $student->id])) }}" class="p-2 hover:text-primary transition-colors"><span class="material-symbols-outlined text-[20px]">edit</span></a>
                                    <a href="{{ route('students.qrcode', $student->id) }}?download=1" class="p-2 hover:text-primary transition-colors"><span class="material-symbols-outlined text-[20px]">download</span></a>
                                    <form method="POST" action="{{ route('admin.master.siswa.destroy', $student->id) }}" onsubmit="return confirm('Hapus data siswa ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 hover:text-rose-500 transition-colors"><span class="material-symbols-outlined text-[20px]">delete</span></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-slate-500">Belum ada data siswa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="p-6 flex items-center justify-between border-t border-slate-50 bg-slate-50/30">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Menampilkan {{ $students->count() }} dari {{ $students->total() }} Siswa</p>
            <div>{{ $students->onEachSide(1)->links() }}</div>
        </div>
    </div>

    <div x-show="showCreateModal" x-transition class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4" @click.self="showCreateModal = false">
        <div class="w-full max-w-4xl rounded-2xl bg-white border border-slate-100 shadow-2xl p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold text-slate-900">Tambah Siswa</h3>
                <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-slate-700"><span class="material-symbols-outlined">close</span></button>
            </div>
            <form method="POST" action="{{ route('admin.master.siswa.store') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                @csrf
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Nama</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-sm focus:ring-2 focus:ring-indigo-500/10" />
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-sm focus:ring-2 focus:ring-indigo-500/10" />
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">NISN</label>
                    <input type="text" name="nisn" value="{{ old('nisn') }}" required class="w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-sm focus:ring-2 focus:ring-indigo-500/10" />
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Kelas</label>
                    <select name="class_id" required class="w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-sm focus:ring-2 focus:ring-indigo-500/10">
                        <option value="">Pilih Kelas</option>
                        @foreach($classOptions as $option)
                            <option value="{{ $option->id }}" @selected((int) old('class_id') === $option->id)>{{ $option->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Password</label>
                    <input type="password" name="password" required class="w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-sm focus:ring-2 focus:ring-indigo-500/10" placeholder="Minimal 8 karakter" />
                </div>
                <div class="md:col-span-5 flex justify-end gap-2 mt-2">
                    <button type="button" @click="showCreateModal = false" class="px-4 py-2.5 rounded-2xl bg-slate-100 text-slate-700 text-sm font-bold">Batal</button>
                    <button type="submit" class="px-6 py-3 rounded-2xl bg-slate-900 text-white text-sm font-bold">Simpan Siswa</button>
                </div>
            </form>
        </div>
    </div>

    @if ($editStudent)
        <div x-show="showEditModal" x-transition class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4" @click.self="window.location='{{ route('admin.master.siswa', request()->except('edit')) }}'">
            <div class="w-full max-w-4xl rounded-2xl bg-white border border-slate-100 shadow-2xl p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-slate-900">Edit Siswa</h3>
                    <a href="{{ route('admin.master.siswa', request()->except('edit')) }}" class="text-slate-400 hover:text-slate-700"><span class="material-symbols-outlined">close</span></a>
                </div>
                <form method="POST" action="{{ route('admin.master.siswa.update', $editStudent->id) }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Nama</label>
                        <input type="text" name="name" value="{{ old('name', $editStudent->user?->name) }}" required class="w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-sm focus:ring-2 focus:ring-indigo-500/10" />
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email', $editStudent->user?->email) }}" required class="w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-sm focus:ring-2 focus:ring-indigo-500/10" />
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">NISN</label>
                        <input type="text" name="nisn" value="{{ old('nisn', $editStudent->nisn) }}" required class="w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-sm focus:ring-2 focus:ring-indigo-500/10" />
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Kelas</label>
                        <select name="class_id" required class="w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-sm focus:ring-2 focus:ring-indigo-500/10">
                            @foreach($classOptions as $option)
                                <option value="{{ $option->id }}" @selected((int) old('class_id', $editStudent->class_id) === $option->id)>{{ $option->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Password Baru</label>
                        <input type="password" name="password" class="w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-sm focus:ring-2 focus:ring-indigo-500/10" placeholder="Kosongkan jika tidak diubah" />
                    </div>
                    <div class="md:col-span-5 flex justify-end gap-2 mt-2">
                        <a href="{{ route('admin.master.siswa', request()->except('edit')) }}" class="px-4 py-2.5 rounded-2xl bg-slate-100 text-slate-700 text-sm font-bold">Batal</a>
                        <button type="submit" class="px-6 py-3 rounded-2xl bg-primary text-white text-sm font-bold">Update Siswa</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
