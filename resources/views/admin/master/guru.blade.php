@extends('layouts.admin')

@section('content')
<div class="space-y-8">
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
        <div class="flex items-center gap-4 w-full md:w-auto">
            <div class="relative w-full md:w-72">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border-none rounded-lg text-sm focus:ring-2 focus:ring-primary/20 transition-all font-medium" placeholder="Cari Guru..." type="text"/>
            </div>
            <div class="relative min-w-[160px]">
                <select class="w-full appearance-none pl-4 pr-10 py-2.5 bg-slate-50 border-none rounded-lg text-sm font-bold text-slate-600 focus:ring-2 focus:ring-primary/20 transition-all cursor-pointer">
                    <option>Semua Mapel</option>
                    <option>Matematika</option>
                    <option>Bahasa Inggris</option>
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
            </div>
        </div>
        <button class="w-full md:w-auto px-6 py-2.5 bg-gradient-to-br from-primary to-primary-container text-white font-bold rounded-xl flex items-center justify-center gap-2 hover:opacity-90 transition-all transform hover:scale-[1.02] active:scale-95 shadow-lg shadow-primary/20">
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
                    <tr class="hover:bg-indigo-50/30 transition-colors group">
                        <td class="px-6 py-4 font-mono text-xs font-semibold text-slate-500">198501012010011001</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-indigo-50 flex items-center justify-center text-primary font-bold text-xs">BS</div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900">Budi Santoso, M.Pd</p>
                                    <p class="text-[10px] text-slate-400 font-medium">Joined Jan 2010</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 bg-surface-container-high text-primary rounded-full text-[10px] font-bold">Matematika</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-1.5 font-bold text-emerald-600 text-xs">
                                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                                AKTIF
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2 text-slate-400">
                                <button class="hover:text-primary transition-colors"><span class="material-symbols-outlined text-[20px]">edit</span></button>
                                <button class="hover:text-rose-500 transition-colors"><span class="material-symbols-outlined text-[20px]">delete</span></button>
                            </div>
                        </td>
                    </tr>
                    <tr class="hover:bg-indigo-50/30 transition-colors group">
                        <td class="px-6 py-4 font-mono text-xs font-semibold text-slate-500">197805202005012003</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-indigo-50 flex items-center justify-center text-primary font-bold text-xs">SA</div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900">Siti Aminah, S.Pd</p>
                                    <p class="text-[10px] text-slate-400 font-medium">Joined May 2005</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 bg-surface-container-high text-primary rounded-full text-[10px] font-bold">Bahasa Inggris</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-1.5 font-bold text-emerald-600 text-xs">
                                <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                                AKTIF
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2 text-slate-400">
                                <button class="hover:text-primary transition-colors"><span class="material-symbols-outlined text-[20px]">edit</span></button>
                                <button class="hover:text-rose-500 transition-colors"><span class="material-symbols-outlined text-[20px]">delete</span></button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Showing 2 of 45 Teachers</p>
            <div class="flex gap-1">
                <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-primary text-white font-bold text-xs shadow-sm">1</button>
                <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-400 hover:bg-white text-xs font-bold">2</button>
            </div>
        </div>
    </div>

    <!-- Stats Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-8">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-50 text-primary rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined">groups</span>
            </div>
            <div>
                <p class="text-2xl font-black text-slate-900 leading-none">45</p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Total Guru</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 flex items-center gap-4 text-emerald-600">
            <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined">verified</span>
            </div>
            <div>
                <p class="text-2xl font-black leading-none">42</p>
                <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest mt-1">Status Aktif</p>
            </div>
        </div>
    </div>
</div>
@endsection
