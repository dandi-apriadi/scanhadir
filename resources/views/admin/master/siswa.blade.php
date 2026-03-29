@extends('layouts.admin')

@section('content')
<div class="space-y-8">
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
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
        <div>
            <h2 class="text-4xl font-bold tracking-tight text-on-surface mb-2 font-headline">Daftar Siswa</h2>
            <p class="text-slate-500 text-sm max-w-xl">Kelola data seluruh siswa, cetak kartu QR kehadiran, dan pantau status keaktifan dalam satu platform terpusat.</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="flex items-center gap-2 px-6 py-3.5 bg-gradient-to-br from-primary to-primary-container text-white rounded-2xl font-bold shadow-xl shadow-indigo-200/50 hover:shadow-indigo-300/50 active:scale-95 transition-all">
                <span class="material-symbols-outlined" style="font-variation-settings: 'wght' 600;">add</span>
                <span>Tambah Siswa</span>
            </button>
        </div>
    </div>

    <!-- Action Bar & Table Container -->
    <div class="bg-surface-container-lowest rounded-3xl p-2 shadow-sm border border-slate-100">
        <div class="p-6 flex flex-col md:flex-row gap-4 items-center justify-between border-b border-slate-50 mb-2">
            <div class="flex flex-col md:flex-row items-center gap-4 w-full md:w-auto">
                <div class="relative w-full md:w-80 group">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400">search</span>
                    <input class="w-full bg-slate-50 border-none rounded-2xl py-3 pl-12 pr-4 text-sm focus:ring-2 focus:ring-indigo-500/10 transition-all outline-none" placeholder="Cari nama atau NISN..." type="text"/>
                </div>
                <div class="relative w-full md:w-56">
                    <select class="w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-sm appearance-none focus:ring-2 focus:ring-indigo-500/10 outline-none cursor-pointer font-bold text-slate-600">
                        <option value="">Semua Kelas</option>
                        <option>XII RPL 1</option>
                        <option>XII RPL 2</option>
                    </select>
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400 pointer-events-none">expand_more</span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button class="p-3 text-slate-500 hover:bg-slate-50 rounded-xl transition-colors">
                    <span class="material-symbols-outlined">filter_list</span>
                </button>
                <button class="p-3 text-slate-500 hover:bg-slate-50 rounded-xl transition-colors">
                    <span class="material-symbols-outlined">file_download</span>
                </button>
            </div>
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
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-primary font-bold text-xs">AF</div>
                                <div>
                                    <p class="text-sm font-bold text-on-surface">Ahmad Fauzi</p>
                                    <p class="text-[10px] text-slate-400 uppercase tracking-tighter">ahmad.fauzi@school.id</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-mono text-slate-500 font-bold">0012345678</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 bg-indigo-50 rounded-lg text-xs font-bold text-primary">XII RPL 1</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="w-9 h-9 p-1 bg-white border border-slate-100 rounded-lg group-hover:scale-110 transition-transform cursor-pointer flex items-center justify-center shadow-sm">
                                <span class="material-symbols-outlined text-slate-300 text-xl">qr_code_2</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-1 text-slate-400">
                                <button class="p-2 hover:text-primary transition-colors"><span class="material-symbols-outlined text-[20px]">edit</span></button>
                                <button class="p-2 hover:text-primary transition-colors"><span class="material-symbols-outlined text-[20px]">download</span></button>
                                <button class="p-2 hover:text-rose-500 transition-colors"><span class="material-symbols-outlined text-[20px]">delete</span></button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="p-6 flex items-center justify-between border-t border-slate-50 bg-slate-50/30">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Menampilkan 1 dari 120 Siswa</p>
            <div class="flex items-center gap-1">
                <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-primary text-white text-xs font-bold shadow-md shadow-primary/20">1</button>
                <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-xs font-bold text-slate-400 hover:bg-white transition-all">2</button>
            </div>
        </div>
    </div>
</div>
@endsection
