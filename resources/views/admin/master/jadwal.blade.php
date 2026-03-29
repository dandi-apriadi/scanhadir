@extends('layouts.admin')

@section('content')
<div class="space-y-8">
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
            <button class="flex items-center gap-2 px-6 py-3 bg-gradient-to-br from-primary to-primary-container text-white rounded-xl font-bold text-sm shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all">
                <span class="material-symbols-outlined text-lg">add</span>
                TAMBAH JADWAL
            </button>
        </div>
    </div>

    <!-- Toolbar / Filters -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4 bg-white rounded-2xl border border-slate-100 shadow-sm">
        <div class="space-y-1.5">
            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500 ml-1">Pilih Kelas</label>
            <select class="w-full bg-slate-50 border-none rounded-xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-primary/20 py-2.5 px-4">
                <option>Semua Kelas</option>
                <option>XII RPL 1</option>
                <option>XII RPL 2</option>
            </select>
        </div>
        <div class="space-y-1.5">
            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500 ml-1">Pilih Hari</label>
            <select class="w-full bg-slate-50 border-none rounded-xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-primary/20 py-2.5 px-4">
                <option>Semua Hari</option>
                <option>Senin</option>
                <option>Selasa</option>
                <option>Rabu</option>
            </select>
        </div>
        <div class="space-y-1.5">
            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500 ml-1">Urutkan</label>
            <select class="w-full bg-slate-50 border-none rounded-xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-primary/20 py-2.5 px-4">
                <option>Waktu Terdekat</option>
                <option>Mata Pelajaran A-Z</option>
            </select>
        </div>
        <div class="flex items-end">
            <button class="w-full py-2.5 px-4 bg-primary/5 text-primary rounded-xl text-sm font-bold hover:bg-primary/10 transition-colors flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-lg">filter_alt</span>
                Reset Filter
            </button>
        </div>
    </div>

    <!-- Schedule Table -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-slate-100">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400">Hari / Jam</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400">Mata Pelajaran</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400">Guru Pengampu</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 text-center">Ruang</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <tr class="hover:bg-indigo-50/30 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-xs font-black text-on-surface">SENIN</span>
                                <span class="text-sm font-bold text-primary">07:15 - 08:45</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-primary">
                                    <span class="material-symbols-outlined text-lg">code</span>
                                </div>
                                <span class="text-sm font-bold text-on-surface">Pemrograman Web</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-[10px]">BS</div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-on-surface">Budi Santoso, M.Pd</span>
                                    <span class="text-[10px] text-slate-400 font-medium tracking-tighter uppercase">NIP. 19820301...</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2.5 py-1 bg-slate-100 rounded text-slate-500 text-[10px] font-bold uppercase">Lab 01</span>
                        </td>
                        <td class="px-6 py-4 text-right text-slate-400">
                            <div class="flex items-center justify-end gap-1">
                                <button class="p-2 hover:text-primary transition-colors"><span class="material-symbols-outlined text-lg">edit</span></button>
                                <button class="p-2 hover:text-rose-500 transition-colors"><span class="material-symbols-outlined text-lg">delete</span></button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Insights -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-primary p-6 rounded-2xl text-white shadow-lg shadow-primary/20">
            <h3 class="text-xs font-bold uppercase tracking-widest opacity-80">Jam Mengajar</h3>
            <p class="text-3xl font-black mt-1">38 <span class="text-sm font-medium opacity-60">Jam/Minggu</span></p>
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
                <p class="text-2xl font-black text-on-surface leading-tight">12 <span class="text-xs font-medium text-slate-400">/ 15 Lab</span></p>
            </div>
        </div>
    </div>
</div>
@endsection
