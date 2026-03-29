@extends('layouts.student')

@section('content')
<div class="space-y-8">
    <!-- Hero Greeting -->
    <section class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
        <div>
            <h1 class="text-3xl font-bold font-headline text-on-background">Halo, {{ $student_name }} 👋</h1>
            <p class="text-slate-500 mt-1 font-medium">Selamat datang kembali di sistem presensi digital sekolah.</p>
        </div>
        <div class="flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-700 rounded-full border border-emerald-100 shadow-sm">
            <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
            <span class="text-xs font-bold uppercase tracking-wider font-label">System Active</span>
        </div>
    </section>

    <!-- Metric Cards -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-surface-container-lowest p-6 rounded-[32px] shadow-sm flex items-center gap-5 group hover:shadow-xl hover:shadow-slate-200/40 transition-all duration-300">
            <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center text-primary transition-transform group-hover:scale-110">
                <span class="material-symbols-outlined text-3xl">calendar_today</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest font-label mb-1">Hadir Bulan Ini</p>
                <p class="text-2xl font-extrabold text-on-surface">{{ $attendance_this_month }}<span class="text-slate-300 font-normal text-lg">/{{ $total_days }}</span></p>
            </div>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-[32px] shadow-sm flex items-center gap-5 group hover:shadow-xl hover:shadow-slate-200/40 transition-all duration-300">
            <div class="w-14 h-14 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600 transition-transform group-hover:scale-110">
                <span class="material-symbols-outlined text-3xl">timer</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest font-label mb-1">Terlambat</p>
                <p class="text-2xl font-extrabold text-on-surface">{{ $late_count }} <span class="text-xs font-medium text-amber-500 bg-amber-50 px-2 py-0.5 rounded-md ml-1">Sesi</span></p>
            </div>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-[32px] shadow-sm flex items-center gap-5 group hover:shadow-xl hover:shadow-slate-200/40 transition-all duration-300">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 transition-transform group-hover:scale-110">
                <span class="material-symbols-outlined text-3xl">check_circle</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest font-label mb-1">Status Hari Ini</p>
                <span class="inline-flex items-center px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full uppercase tracking-tighter">{{ $today_status }}</span>
            </div>
        </div>
    </section>

    <!-- Central Content: QR Card & History -->
    <section class="grid grid-cols-1 lg:grid-cols-5 gap-8">
        <!-- Digital MyQR Card -->
        <div class="lg:col-span-2 relative group overflow-hidden bg-gradient-to-br from-primary to-primary-container p-8 rounded-[40px] shadow-2xl shadow-indigo-200">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
            <div class="relative z-10 space-y-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-white font-headline font-bold text-xl">Kartu Digital MyQR</h3>
                        <p class="text-indigo-100/70 text-sm mt-1">Gunakan ini untuk akses pintu & kelas.</p>
                    </div>
                    <span class="material-symbols-outlined text-white/50">contactless</span>
                </div>
                <div class="glass-card rounded-[32px] p-8 flex flex-col items-center justify-center border border-white/30 shadow-inner">
                    <div class="bg-white p-4 rounded-3xl shadow-lg mb-6">
                        <img alt="Student QR Code" class="w-32 h-32" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAS0JAVO5rC9CQx4N4MvXwKonGxEsZGfAdjgd8nqOb2vuqztPL7YLkV_sJmDLe9tAMiJ0p2aS-genWjl_kQ9-Ygw8Ws4Byuc7wlL6oa5EuHvCVfUOMkjQBoAYf_JoLY5LPE8ymql1WbM9dn39YDry9HxaJvJSPQYpMfDCysZTmenu3myPCd3Ea4GUN1iOcNtlebUS7TLOjc23epzcnG-JDO_CbCjkQxEvBBBWmtawkr98uMTiCrE6J66adD80vAWx6PXY880kXjIVn7"/>
                    </div>
                    <div class="text-center">
                        <p class="text-primary font-bold text-lg font-headline tracking-wide">{{ $nisn }}</p>
                        <p class="text-slate-500 text-[10px] uppercase font-bold tracking-[0.2em]">NISN - {{ $student_name }}</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <button class="flex-1 bg-white text-primary font-bold py-4 rounded-2xl flex items-center justify-center gap-2 hover:bg-indigo-50 transition-colors shadow-lg">
                        <span class="material-symbols-outlined text-lg">download</span>
                        Download QR
                    </button>
                    <button class="w-14 h-14 bg-white/10 text-white rounded-2xl flex items-center justify-center backdrop-blur-md hover:bg-white/20 transition-all">
                        <span class="material-symbols-outlined">share</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- History Table -->
        <div class="lg:col-span-3 bg-white rounded-[40px] p-8 shadow-sm border-0 flex flex-col">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h3 class="text-xl font-bold font-headline text-on-surface">Riwayat Kehadiran Terbaru</h3>
                    <p class="text-slate-400 text-sm">Monitoring log aktivitas harian anda.</p>
                </div>
                <a href="{{ route('student.izin') }}" class="text-primary text-sm font-bold flex items-center gap-1 hover:underline">
                    Lihat Semua
                    <span class="material-symbols-outlined text-lg">chevron_right</span>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left border-b border-slate-50">
                            <th class="pb-4 text-[10px] uppercase tracking-widest font-bold text-slate-400 px-2">Tanggal</th>
                            <th class="pb-4 text-[10px] uppercase tracking-widest font-bold text-slate-400 px-2">Mata Pelajaran</th>
                            <th class="pb-4 text-[10px] uppercase tracking-widest font-bold text-slate-400 px-2 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50/50">
                        @foreach($recent_history as $history)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="py-5 px-2">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-on-surface">{{ $history['date'] }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $history['day'] }}</span>
                                </div>
                            </td>
                            <td class="py-5 px-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-{{ $history['color'] }}-50 text-{{ $history['color'] }}-500 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-lg">{{ $history['icon'] }}</span>
                                    </div>
                                    <span class="text-sm font-medium text-slate-600">{{ $history['subject'] }}</span>
                                </div>
                            </td>
                            <td class="py-5 px-2 text-right">
                                <span class="px-3 py-1 bg-{{ $history['status'] == 'Present' ? 'emerald' : ($history['status'] == 'Late' ? 'amber' : 'red') }}-100 text-{{ $history['status'] == 'Present' ? 'emerald' : ($history['status'] == 'Late' ? 'amber' : 'red') }}-700 text-[10px] font-bold rounded-full uppercase">{{ $history['status'] }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection
