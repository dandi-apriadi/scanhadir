@extends('layouts.admin')

@section('content')
<div class="space-y-8">
    <!-- Page Header & Breadcrumbs -->
    <div class="mb-8">
        <nav class="flex text-xs font-medium text-slate-400 mb-2 gap-2 items-center uppercase tracking-widest">
            <span>Dashboard</span>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span>Sistem Presensi</span>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-indigo-600">Log</span>
        </nav>
        <div class="flex justify-between items-end">
            <div>
                <h2 class="text-3xl font-bold font-headline tracking-tight text-on-surface">Log Presensi Harian</h2>
                <p class="text-slate-500 mt-1">Pantau dan kelola kehadiran siswa secara real-time.</p>
            </div>
            <button class="bg-gradient-to-br from-primary to-primary-container text-white px-6 py-3 rounded-xl font-bold flex items-center gap-2 shadow-lg shadow-primary/15 hover:scale-[1.02] transition-transform active:scale-95">
                <span class="material-symbols-outlined">download</span>
                Ekspor Laporan
            </button>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="grid grid-cols-12 gap-4 mb-8">
        <div class="col-span-12 lg:col-span-8 bg-surface-container-low rounded-xl p-4 flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Rentang Tanggal</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">calendar_today</span>
                    <input class="w-full bg-surface-container-lowest border-none rounded-lg py-2 pl-10 pr-4 text-sm font-medium focus:ring-2 focus:ring-primary/10" type="text" value="Oct 12, 2023 - Oct 19, 2023"/>
                </div>
            </div>
            <div class="w-48">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Kelas</label>
                <select class="w-full bg-surface-container-lowest border-none rounded-lg py-2 px-3 text-sm font-medium focus:ring-2 focus:ring-primary/10 appearance-none">
                    <option>Semua Kelas</option>
                    <option>XII IPA 1</option>
                    <option>XII IPA 2</option>
                </select>
            </div>
            <button class="mt-5 h-10 w-10 flex items-center justify-center rounded-lg bg-surface-container-highest text-primary hover:bg-primary hover:text-white transition-colors">
                <span class="material-symbols-outlined">filter_list</span>
            </button>
        </div>
        <div class="col-span-12 lg:col-span-4 bg-primary-container rounded-xl p-4 text-white relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-[10px] font-bold opacity-70 uppercase tracking-widest mb-1">Kehadiran Hari Ini</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-4xl font-bold font-headline">94.2%</span>
                    <span class="text-xs bg-white/20 px-2 py-0.5 rounded-full font-medium text-white">+2.4%</span>
                </div>
            </div>
            <div class="absolute -right-4 -bottom-4 opacity-10 scale-150 rotate-12 transition-transform group-hover:rotate-0">
                <span class="material-symbols-outlined text-9xl">analytics</span>
            </div>
        </div>
    </div>

    <!-- Data Table Section -->
    <div class="bg-surface-container-lowest rounded-2xl overflow-hidden shadow-sm border border-slate-100/50">
        <table class="w-full border-collapse text-left">
            <thead>
                <tr class="bg-slate-50/50 text-slate-500">
                    <th class="py-4 px-6 text-xs font-bold uppercase tracking-wider">Waktu</th>
                    <th class="py-4 px-6 text-xs font-bold uppercase tracking-wider">Nama Siswa</th>
                    <th class="py-4 px-6 text-xs font-bold uppercase tracking-wider">Kelas</th>
                    <th class="py-4 px-6 text-xs font-bold uppercase tracking-wider">Status</th>
                    <th class="py-4 px-6 text-xs font-bold uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <tr class="group hover:bg-surface-container-low transition-colors duration-150">
                    <td class="py-4 px-6">
                        <span class="text-sm font-bold text-on-surface font-headline">07:12:45</span>
                        <p class="text-[10px] text-slate-400">19 Okt 2023</p>
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-primary font-bold text-xs">AF</div>
                            <span class="text-sm font-semibold text-on-surface">Ahmad Fauzi</span>
                        </div>
                    </td>
                    <td class="py-4 px-6 text-sm text-slate-500">XII IPA 1</td>
                    <td class="py-4 px-6">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 ring-1 ring-inset ring-emerald-600/10">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                            Hadir
                        </span>
                    </td>
                    <td class="py-4 px-6 text-right">
                        <button class="p-2 text-slate-400 hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-xl">more_vert</span>
                        </button>
                    </td>
                </tr>
                <tr class="group hover:bg-surface-container-low transition-colors duration-150">
                    <td class="py-4 px-6">
                        <span class="text-sm font-bold text-on-surface font-headline">07:45:12</span>
                        <p class="text-[10px] text-slate-400">19 Okt 2023</p>
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-primary font-bold text-xs">SA</div>
                            <span class="text-sm font-semibold text-on-surface">Siti Aminah</span>
                        </div>
                    </td>
                    <td class="py-4 px-6 text-sm text-slate-500">XII IPA 1</td>
                    <td class="py-4 px-6">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-600 ring-1 ring-inset ring-amber-600/10">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                            Terlambat
                        </span>
                    </td>
                    <td class="py-4 px-6 text-right">
                        <button class="p-2 text-slate-400 hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-xl">more_vert</span>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
        <!-- Pagination -->
        <div class="px-6 py-4 bg-slate-50/30 flex justify-between items-center">
            <p class="text-xs text-slate-500 font-medium">Menampilkan 1 - 2 dari 320 entri</p>
            <div class="flex gap-1">
                <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-400 hover:bg-white">
                    <span class="material-symbols-outlined text-lg">chevron_left</span>
                </button>
                <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-primary text-white font-bold text-xs">1</button>
                <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-white font-medium text-xs">2</button>
                <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-400 hover:bg-white">
                    <span class="material-symbols-outlined text-lg">chevron_right</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
