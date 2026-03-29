@extends('layouts.admin')

@section('content')
<div class="max-w-[1440px] mx-auto">
    <!-- Header Section -->
    <div class="mb-10 flex justify-between items-end">
        <div>
            <h2 class="text-3xl font-bold font-headline tracking-tight text-on-surface">System Overview</h2>
            <p class="text-slate-500 mt-1">Real-time attendance insights and system performance.</p>
        </div>
        <div class="flex gap-3">
            <button class="flex items-center gap-2 px-5 py-2.5 bg-surface-container-lowest text-on-surface border border-outline-variant/15 rounded-xl text-sm font-semibold hover:bg-surface-container-low transition-all">
                <span class="material-symbols-outlined text-lg">calendar_today</span>
                Hari Ini
            </button>
            <a href="{{ route('admin.report_pdf') }}" class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-br from-primary to-primary-container text-white rounded-xl text-sm font-semibold shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all">
                <span class="material-symbols-outlined text-lg">download</span>
                Ekspor Laporan
            </a>
        </div>
    </div>

    <!-- Bento Grid Widgets -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Kehadiran Hari Ini -->
        <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/15 hover:border-primary/20 transition-all group">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-primary/10 rounded-xl text-primary group-hover:bg-primary group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined">how_to_reg</span>
                </div>
                <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg flex items-center gap-1">
                    <span class="material-symbols-outlined text-xs">trending_up</span>
                    2.5%
                </span>
            </div>
            <p class="text-sm font-medium text-slate-500 mb-1">Kehadiran Hari Ini</p>
            <h3 class="text-3xl font-bold font-headline">94.2%</h3>
            <p class="text-[10px] text-slate-400 mt-2">dibandingkan kemarin</p>
        </div>
        <!-- Total Siswa -->
        <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/15 hover:border-primary/20 transition-all group">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-secondary/10 rounded-xl text-secondary group-hover:bg-secondary group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined">group</span>
                </div>
                <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded-lg">Stabil</span>
            </div>
            <p class="text-sm font-medium text-slate-500 mb-1">Total Siswa Aktif</p>
            <h3 class="text-3xl font-bold font-headline">1,240</h3>
            <p class="text-[10px] text-slate-400 mt-2">Terdaftar di sistem</p>
        </div>
        <!-- Siswa Terlambat -->
        <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/15 hover:border-amber-400/20 transition-all group">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-amber-100 rounded-xl text-amber-600 group-hover:bg-amber-500 group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined">alarm</span>
                </div>
                <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded-lg">Warning</span>
            </div>
            <p class="text-sm font-medium text-slate-500 mb-1">Siswa Terlambat</p>
            <h3 class="text-3xl font-bold font-headline text-amber-600">12</h3>
            <p class="text-[10px] text-slate-400 mt-2">Butuh tindak lanjut</p>
        </div>
        <!-- Siswa Alpa -->
        <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/15 hover:border-error/20 transition-all group">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-rose-100 rounded-xl text-error group-hover:bg-error group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined">person_off</span>
                </div>
                <span class="text-xs font-bold text-error bg-rose-50 px-2 py-1 rounded-lg">Critical</span>
            </div>
            <p class="text-sm font-medium text-slate-500 mb-1">Siswa Alpa</p>
            <h3 class="text-3xl font-bold font-headline text-error">2</h3>
            <p class="text-[10px] text-slate-400 mt-2">Ketidakhadiran tanpa izin</p>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Live Activity Log -->
        <div class="lg:col-span-2 bg-surface-container-lowest rounded-2xl border border-outline-variant/15 flex flex-col overflow-hidden shadow-sm">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-xl font-bold font-headline text-on-surface">Log Presensi Real-time</h3>
                <span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-bold rounded-full animate-pulse">LIVE UPDATE</span>
            </div>
            <div class="p-0 overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Siswa</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Kelas</th>
                            <th class="px-6 py-4 text-[10px) font-bold text-slate-400 uppercase tracking-widest">Waktu</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-primary font-bold text-xs">AF</div>
                                    <span class="text-sm font-bold text-on-surface">Ahmad Fauzi</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500">XII RPL 1</td>
                            <td class="px-6 py-4 text-sm font-medium text-on-surface">06:42:15 WIB</td>
                            <td class="px-6 py-4 text-right">
                                <span class="px-2 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-bold rounded">TEPAT WAKTU</span>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-primary font-bold text-xs">SA</div>
                                    <span class="text-sm font-bold text-on-surface">Siti Aminah</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500">XII RPL 1</td>
                            <td class="px-6 py-4 text-sm font-medium text-on-surface">06:45:10 WIB</td>
                            <td class="px-6 py-4 text-right">
                                <span class="px-2 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-bold rounded">TEPAT WAKTU</span>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-primary font-bold text-xs">BS</div>
                                    <span class="text-sm font-bold text-on-surface">Budi Santoso</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500">XII RPL 2</td>
                            <td class="px-6 py-4 text-sm font-medium text-on-surface">07:15:33 WIB</td>
                            <td class="px-6 py-4 text-right">
                                <span class="px-2 py-1 bg-amber-50 text-amber-600 text-[10px] font-bold rounded">TERLAMBAT</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="p-4 bg-slate-50 border-t border-slate-100 text-center">
                <a href="{{ route('admin.logs') }}" class="text-primary text-xs font-bold uppercase tracking-wider hover:underline">Lihat Seluruh Log</a>
            </div>
        </div>

        <!-- System Health / Quick Info -->
        <div class="space-y-6">
            <div class="bg-indigo-700 p-8 rounded-2xl text-white relative overflow-hidden shadow-lg shadow-indigo-200">
                <div class="relative z-10">
                    <span class="material-symbols-outlined text-4xl mb-4">settings_remote</span>
                    <h4 class="text-xl font-bold font-headline mb-2">Gate Status</h4>
                    <p class="text-indigo-200 text-sm">Semua gate presensi (8 pintu) saat ini beroperasi normal tanpa kendala.</p>
                    <div class="mt-6 flex items-center gap-2">
                        <span class="w-2 h-2 bg-emerald-400 rounded-full"></span>
                        <span class="text-xs font-bold">Latency: 45ms</span>
                    </div>
                </div>
                <!-- Background Decoration -->
                <div class="absolute bottom-0 right-0 w-32 h-32 bg-primary-container rounded-full blur-2xl -mb-10 -mr-10"></div>
            </div>

            <div class="bg-surface-container-low p-6 rounded-2xl border border-outline-variant/15">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Izin & Sakit Tertunda</h4>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-white border border-slate-100 flex items-center justify-center text-slate-400">
                                <span class="material-symbols-outlined text-sm">edit_calendar</span>
                            </div>
                            <div>
                                <p class="text-xs font-bold font-headline">5 Pengajuan</p>
                                <p class="text-[10px] text-slate-500">Menunggu verifikasi admin</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.izin_approval') }}" class="p-2 bg-primary text-white rounded-lg hover:scale-105 transition-transform shadow-md">
                            <span class="material-symbols-outlined text-sm">chevron_right</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
