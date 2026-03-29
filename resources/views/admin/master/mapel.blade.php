@extends('layouts.admin')

@section('content')
<div class="p-8 max-w-7xl mx-auto">
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

    <!-- Management Section Card -->
    <section class="bg-white rounded-3xl p-8 shadow-[0_20px_40px_rgba(13,28,46,0.03)] border border-slate-100/50">
        <!-- Toolbar -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-8">
            <div class="flex flex-col sm:flex-row gap-3 flex-1">
                <div class="relative flex-1 max-w-md">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-300">
                        <span class="material-symbols-outlined text-xl">search</span>
                    </span>
                    <input class="w-full bg-slate-50 border-none rounded-2xl py-3 pl-11 pr-4 text-sm font-medium focus:ring-2 focus:ring-primary/20 transition-all placeholder:text-slate-300" placeholder="Cari Mata Pelajaran..." type="text">
                </div>
                <div class="relative group">
                    <select class="appearance-none bg-slate-50 border-none rounded-2xl py-3 pl-4 pr-10 text-sm font-bold text-slate-600 focus:ring-2 focus:ring-primary/20 transition-all min-w-[180px]">
                        <option>Semua Kelompok</option>
                        <option>Kejuruan</option>
                        <option>Umum</option>
                    </select>
                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                        <span class="material-symbols-outlined text-lg">expand_more</span>
                    </span>
                </div>
            </div>
            <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs uppercase tracking-widest py-3.5 px-6 rounded-2xl flex items-center justify-center gap-2 shadow-lg shadow-indigo-600/20 active:scale-95 transition-all">
                <span class="material-symbols-outlined text-lg">add_circle</span>
                Tambah Mapel
            </button>
        </div>

        <!-- Modern Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-separate border-spacing-y-2">
                <thead>
                    <tr class="text-slate-300 font-bold text-[10px] uppercase tracking-[0.2em]">
                        <th class="px-6 py-4 text-center w-16 uppercase">No</th>
                        <th class="px-6 py-4 uppercase">Kode</th>
                        <th class="px-6 py-4 uppercase">Nama Mata Pelajaran</th>
                        <th class="px-6 py-4 uppercase">Kelompok</th>
                        <th class="px-6 py-4 text-right uppercase">Kontrol</th>
                    </tr>
                </thead>
                <tbody class="text-sm font-semibold">
                    @foreach($mapels as $index => $mapel)
                    <tr class="group hover:bg-indigo-50/40 transition-all duration-300">
                        <td class="px-6 py-4 text-center text-slate-400 bg-slate-50/50 group-hover:bg-transparent rounded-l-2xl font-mono text-xs">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 font-mono font-bold text-indigo-700">{{ $mapel['kode'] }}</td>
                        <td class="px-6 py-4 text-on-surface">{{ $mapel['nama'] }}</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1.5 {{ $mapel['kelompok'] == 'Kejuruan' ? 'bg-indigo-100 text-indigo-600' : 'bg-slate-100 text-slate-500' }} rounded-lg text-[10px] font-black uppercase tracking-widest ring-1 ring-inset {{ $mapel['kelompok'] == 'Kejuruan' ? 'ring-indigo-200' : 'ring-slate-200' }}">
                                {{ $mapel['kelompok'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right rounded-r-2xl">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button class="w-9 h-9 flex items-center justify-center rounded-xl bg-white text-slate-400 hover:text-indigo-600 shadow-sm ring-1 ring-slate-100 transition-all active:scale-90">
                                    <span class="material-symbols-outlined text-lg">edit</span>
                                </button>
                                <button class="w-9 h-9 flex items-center justify-center rounded-xl bg-white text-slate-400 hover:text-error shadow-sm ring-1 ring-slate-100 transition-all active:scale-90">
                                    <span class="material-symbols-outlined text-lg">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Footer / Pagination -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-8 pt-6 border-t border-slate-50">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest italic">Menampilkan 4 dari 24 data mata pelajaran</p>
            <div class="flex items-center gap-1.5">
                <button class="w-10 h-10 flex items-center justify-center rounded-xl text-slate-300 hover:bg-slate-100 transition-colors">
                    <span class="material-symbols-outlined">chevron_left</span>
                </button>
                <button class="w-10 h-10 flex items-center justify-center rounded-xl bg-primary text-white font-black text-xs shadow-lg shadow-primary/20">1</button>
                <button class="w-10 h-10 flex items-center justify-center rounded-xl text-slate-500 font-bold text-xs hover:bg-slate-100 transition-all">2</button>
                <button class="w-10 h-10 flex items-center justify-center rounded-xl text-slate-300 hover:bg-slate-100 transition-colors">
                    <span class="material-symbols-outlined">chevron_right</span>
                </button>
            </div>
        </div>
    </section>

    <!-- Bottom Stats / Bento Insight -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-10">
        <div class="bg-indigo-600 rounded-[2rem] p-8 text-white overflow-hidden relative group shadow-xl shadow-indigo-200">
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-widest opacity-80 mb-2">Total Mapel</p>
                <h4 class="text-4xl font-black headline-font">24</h4>
            </div>
            <span class="material-symbols-outlined absolute -bottom-6 -right-6 text-9xl opacity-10 group-hover:scale-110 transition-transform duration-700">menu_book</span>
        </div>
        <div class="bg-white rounded-[2rem] p-8 border border-slate-100 shadow-sm flex items-center justify-between group">
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Kelompok Kejuruan</p>
                <h4 class="text-3xl font-black headline-font text-indigo-700">14 <span class="text-[10px] font-bold text-slate-400 ml-1 uppercase opacity-60">Mapel</span></h4>
            </div>
            <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 transition-transform group-hover:bg-indigo-100">
                <span class="material-symbols-outlined text-3xl">engineering</span>
            </div>
        </div>
        <div class="bg-white rounded-[2rem] p-8 border border-slate-100 shadow-sm flex items-center justify-between group">
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Kelompok Umum</p>
                <h4 class="text-3xl font-black headline-font text-slate-700">10 <span class="text-[10px] font-bold text-slate-400 ml-1 uppercase opacity-60">Mapel</span></h4>
            </div>
            <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 transition-transform group-hover:bg-slate-100 group-hover:text-slate-600">
                <span class="material-symbols-outlined text-3xl">public</span>
            </div>
        </div>
    </div>
</div>
@endsection
