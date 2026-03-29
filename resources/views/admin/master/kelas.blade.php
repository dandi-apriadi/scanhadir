@extends('layouts.admin')

@section('content')
<div class="space-y-8">
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
            <button class="bg-gradient-to-r from-primary to-primary-container text-white px-6 py-3 rounded-xl font-bold text-sm flex items-center gap-2 shadow-lg shadow-indigo-200 hover:scale-95 transition-transform duration-150">
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
                <div class="relative w-full max-w-xs">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                    <input class="w-full bg-slate-50 border-none rounded-xl py-2.5 pl-11 pr-4 text-sm focus:ring-2 focus:ring-primary/20 transition-all font-medium" placeholder="Cari Kelas..." type="text"/>
                </div>
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
                        <tr class="hover:bg-indigo-50/30 transition-colors group">
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-3 font-bold text-on-surface">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-primary flex items-center justify-center font-black text-[10px]">XII</div>
                                    <span>XII RPL 1</span>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-sm font-medium text-slate-600">Budiman, S.Pd</td>
                            <td class="px-6 py-5 text-center text-sm font-bold text-on-surface">36</td>
                            <td class="px-6 py-5 text-right">
                                <div class="flex items-center justify-end gap-1 text-slate-400">
                                    <button class="p-2 hover:text-primary transition-colors"><span class="material-symbols-outlined text-[20px]">edit_note</span></button>
                                    <button class="p-2 hover:text-rose-500 transition-colors"><span class="material-symbols-outlined text-[20px]">delete</span></button>
                                </div>
                            </td>
                        </tr>
                        <tr class="hover:bg-indigo-50/30 transition-colors group">
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-3 font-bold text-on-surface">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-primary flex items-center justify-center font-black text-[10px]">XI</div>
                                    <span>XI TKJ 2</span>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-sm font-medium text-slate-600">Siti Aminah, M.Pd</td>
                            <td class="px-6 py-5 text-center text-sm font-bold text-on-surface">32</td>
                            <td class="px-6 py-5 text-right">
                                <div class="flex items-center justify-end gap-1 text-slate-400">
                                    <button class="p-2 hover:text-primary transition-colors"><span class="material-symbols-outlined text-[20px]">edit_note</span></button>
                                    <button class="p-2 hover:text-rose-500 transition-colors"><span class="material-symbols-outlined text-[20px]">delete</span></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="p-4 bg-slate-50/50 border-t border-slate-50 flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Showing 2 of 24 Classes</span>
                <div class="flex gap-1">
                    <button class="w-7 h-7 flex items-center justify-center rounded-lg bg-primary text-white text-[10px] font-bold shadow-sm">1</button>
                    <button class="w-7 h-7 flex items-center justify-center rounded-lg border border-slate-200 text-slate-400 text-[10px] font-bold hover:bg-white">2</button>
                </div>
            </div>
        </div>

        <!-- Sidebar Stats -->
        <div class="col-span-12 lg:col-span-4 space-y-6">
            <div class="bg-indigo-600 rounded-2xl p-6 text-white relative overflow-hidden shadow-lg shadow-indigo-100">
                <div class="relative z-10">
                    <span class="material-symbols-outlined mb-2 text-indigo-200">school</span>
                    <h3 class="text-sm font-bold uppercase tracking-widest opacity-80">Total Kelas</h3>
                    <p class="text-4xl font-black mt-1">24</p>
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
                            <span class="text-primary">8 KELAS</span>
                        </div>
                        <div class="h-1.5 bg-slate-50 rounded-full overflow-hidden">
                            <div class="h-full bg-primary rounded-full" style="width: 33%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-[11px] font-bold text-slate-500 mb-2">
                            <span>KELAS XI</span>
                            <span class="text-primary">9 KELAS</span>
                        </div>
                        <div class="h-1.5 bg-slate-50 rounded-full overflow-hidden">
                            <div class="h-full bg-primary rounded-full" style="width: 38%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-[11px] font-bold text-slate-500 mb-2">
                            <span>KELAS X</span>
                            <span class="text-primary">7 KELAS</span>
                        </div>
                        <div class="h-1.5 bg-slate-50 rounded-full overflow-hidden">
                            <div class="h-full bg-primary rounded-full" style="width: 29%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
