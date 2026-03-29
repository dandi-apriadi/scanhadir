@extends('layouts.teacher')

@section('content')
<div class="max-w-7xl mx-auto space-y-10">
    <!-- Welcome Banner/Hero Segment -->
    <section class="relative overflow-hidden rounded-[2.5rem] bg-gradient-to-br from-primary to-primary-container p-12 text-white shadow-2xl shadow-indigo-900/20 group">
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-10">
            <div class="space-y-4">
                <span class="px-4 py-1.5 bg-white/20 backdrop-blur-md rounded-full text-[10px] font-black uppercase tracking-[0.2em] border border-white/10">
                    Sesi Aktif: {{ date('H:i') }} WIB
                </span>
                <h2 class="text-4xl md:text-5xl font-extrabold font-headline leading-tight">Selamat Pagi,<br>Budi Santoso, S.Pd!</h2>
                <p class="text-indigo-100 text-lg font-medium opacity-90 max-w-xl">Hari ini Anda memiliki <span class="text-white font-bold underline decoration-white/30 underline-offset-4">4 sesi mengajar</span>. Pastikan perangkat scanner siap digunakan!</p>
                <div class="pt-6 flex flex-wrap gap-3">
                    <button class="bg-white text-primary px-8 py-3.5 rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-indigo-900/20 hover:scale-105 transition-all active:scale-95 flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">qr_code_scanner</span>
                        Mulai Absensi
                    </button>
                    <button class="bg-white/10 backdrop-blur-md text-white border border-white/20 px-8 py-3.5 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-white/20 transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">info</span>
                        Panduan Guru
                    </button>
                </div>
            </div>
            <div class="hidden lg:block relative">
                <div class="w-56 h-56 bg-white/10 rounded-[3rem] rotate-12 flex items-center justify-center backdrop-blur-3xl animate-pulse-slow ring-1 ring-white/20">
                    <span class="material-symbols-outlined text-9xl opacity-30 group-hover:scale-110 transition-transform duration-700">auto_awesome</span>
                </div>
            </div>
        </div>
        <!-- Background Elements -->
        <div class="absolute -right-20 -bottom-20 w-96 h-96 bg-white/5 rounded-full blur-[100px] animate-pulse"></div>
        <div class="absolute -left-20 -top-20 w-80 h-80 bg-indigo-400/10 rounded-full blur-[80px]"></div>
    </section>

    <!-- Statistik Overview -->
    <section class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-slate-50 flex items-center justify-between group hover:shadow-2xl hover:shadow-indigo-900/5 transition-all duration-500">
            <div class="space-y-2">
                <p class="text-slate-400 font-black uppercase tracking-[0.2em] text-[10px] mb-2">Total Kelas Hari Ini</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-6xl font-black font-headline text-primary tracking-tighter">4</h3>
                    <span class="text-sm font-bold text-slate-400 uppercase tracking-widest">Kelas</span>
                </div>
                <p class="text-[11px] text-slate-400 font-bold uppercase tracking-widest bg-slate-50 px-3 py-1 rounded-lg inline-block">Sisa 2 sesi mengajar lagi</p>
            </div>
            <div class="w-20 h-20 bg-indigo-50 rounded-3xl flex items-center justify-center text-primary group-hover:scale-110 group-hover:bg-primary group-hover:text-white transition-all duration-500">
                <span class="material-symbols-outlined text-4xl">school</span>
            </div>
        </div>
        <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-slate-50 flex items-center justify-between group hover:shadow-2xl hover:shadow-indigo-900/5 transition-all duration-500">
            <div class="space-y-2">
                <p class="text-slate-400 font-black uppercase tracking-[0.2em] text-[10px] mb-2">Rata-rata Kehadiran</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-6xl font-black font-headline text-secondary tracking-tighter">96%</h3>
                    <span class="text-sm font-bold text-slate-400 uppercase tracking-widest">Nasional</span>
                </div>
                <div class="flex items-center gap-1.5 text-emerald-600 font-black text-[10px] uppercase tracking-widest bg-emerald-50 px-3 py-1 rounded-lg inline-block">
                    <span class="material-symbols-outlined text-[10px]">trending_up</span>
                    +2% vs MINGGU LALU
                </div>
            </div>
            <div class="w-20 h-20 bg-slate-50 rounded-3xl flex items-center justify-center text-slate-400 group-hover:scale-110 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-500">
                <span class="material-symbols-outlined text-4xl">analytics</span>
            </div>
        </div>
    </section>

    <!-- Jadwal & Riwayat Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Jadwal Hari Ini -->
        <div class="lg:col-span-2 space-y-8 text-indigo-700 font-bold">
            <div class="flex justify-between items-end px-2">
                <div>
                    <h2 class="text-3xl font-black font-headline text-slate-800">Jadwal Mengajar</h2>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-1">Hari Ini • {{ date('d F Y') }}</p>
                </div>
                <button class="text-primary font-black text-[10px] uppercase tracking-widest hover:underline decoration-2 underline-offset-8">
                    Lihat Kalender
                </button>
            </div>
            
            <div class="flex gap-8 overflow-x-auto pb-6 -mx-2 px-2 scrollbar-hide">
                <!-- Card 1: Completed -->
                <div class="min-w-[340px] bg-white rounded-[2rem] p-8 shadow-sm border border-slate-50 flex flex-col justify-between hover:shadow-xl hover:shadow-indigo-900/5 transition-all group">
                    <div class="flex justify-between items-start mb-8">
                        <span class="bg-indigo-50 text-indigo-600 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest">Selesai</span>
                        <div class="flex items-baseline gap-1 text-slate-400">
                            <span class="material-symbols-outlined text-sm">schedule</span>
                            <span class="text-[10px] font-black uppercase tracking-widest">08:00 - 09:30</span>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-slate-400 font-black text-[10px] uppercase tracking-[0.2em] mb-1">XII RPL 1</h4>
                        <p class="text-2xl font-black font-headline text-slate-800 leading-tight">Pemrograman Web & Mobile</p>
                    </div>
                    <div class="mt-8 pt-6 border-t border-slate-50 flex items-center justify-between">
                        <div class="flex -space-x-3">
                            @for($i=0; $i<3; $i++)
                            <img src="https://api.dicebear.com/7.x/avataaars/svg?seed={{ $i }}" class="w-8 h-8 rounded-full border-2 border-white bg-slate-100">
                            @endfor
                            <div class="w-8 h-8 rounded-full bg-indigo-50 border-2 border-white flex items-center justify-center text-[8px] font-black text-indigo-600">+32</div>
                        </div>
                        <button class="text-slate-300 hover:text-primary transition-colors">
                            <span class="material-symbols-outlined">more_vert</span>
                        </button>
                    </div>
                </div>

                <!-- Card 2: Current -->
                <div class="min-w-[340px] bg-white rounded-[2rem] p-8 shadow-2xl shadow-indigo-900/10 ring-2 ring-primary relative overflow-hidden group">
                    <div class="flex justify-between items-start mb-8">
                        <span class="bg-primary text-white px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest animate-pulse">Berjalan</span>
                        <div class="flex items-baseline gap-1 text-primary">
                            <span class="material-symbols-outlined text-sm">schedule</span>
                            <span class="text-[10px] font-black uppercase tracking-widest">10:00 - 11:30</span>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-primary font-black text-[10px] uppercase tracking-[0.2em] mb-1 opacity-60">XI TKJ 2</h4>
                        <p class="text-2xl font-black font-headline text-slate-800 leading-tight">Keamanan Jaringan Berbasis IoT</p>
                    </div>
                    <div class="mt-8">
                        <button class="w-full bg-primary text-white py-4 rounded-xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-indigo-900/30 hover:scale-[1.02] active:scale-95 transition-all">
                            Buka QR Scanner
                        </button>
                    </div>
                    <div class="absolute -right-4 -top-4 w-20 h-20 bg-indigo-50 rounded-full blur-2xl opacity-50"></div>
                </div>

                <!-- Card 3: Upcoming -->
                <div class="min-w-[340px] bg-white rounded-[2rem] p-8 shadow-sm border border-slate-50 opacity-60 grayscale hover:opacity-100 hover:grayscale-0 transition-all">
                    <div class="flex justify-between items-start mb-8">
                        <span class="bg-slate-100 text-slate-400 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest">Upcoming</span>
                        <div class="flex items-baseline gap-1 text-slate-400">
                            <span class="material-symbols-outlined text-sm">schedule</span>
                            <span class="text-[10px] font-black uppercase tracking-widest">13:00 - 14:30</span>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-slate-400 font-black text-[10px] uppercase tracking-[0.2em] mb-1">X MM 3</h4>
                        <p class="text-2xl font-black font-headline text-slate-800 leading-tight">Dasar-Dasar Desain Grafis</p>
                    </div>
                    <div class="mt-8 pt-6 border-t border-slate-50 flex items-center justify-between">
                        <div class="flex -space-x-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 border-2 border-white flex items-center justify-center text-[8px] font-black text-slate-400">+28</div>
                        </div>
                        <span class="material-symbols-outlined text-slate-200">lock_clock</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Akses Cepat & Insight -->
        <div class="space-y-8">
            <h2 class="text-xl font-black font-headline text-slate-800 px-2 uppercase tracking-widest">Akses Cepat</h2>
            <div class="grid grid-cols-1 gap-4">
                <button class="w-full bg-white p-6 rounded-[2rem] shadow-sm border border-slate-50 flex items-center gap-6 hover:translate-y-[-6px] transition-all hover:shadow-2xl hover:shadow-indigo-900/5 group">
                    <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-all duration-300 shadow-sm">
                        <span class="material-symbols-outlined text-3xl">edit_note</span>
                    </div>
                    <div class="text-left">
                        <h4 class="font-black text-slate-800 uppercase text-xs tracking-widest">Presensi Manual</h4>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Input kehadiran via list</p>
                    </div>
                </button>
                <button class="w-full bg-white p-6 rounded-[2rem] shadow-sm border border-slate-50 flex items-center gap-6 hover:translate-y-[-6px] transition-all hover:shadow-2xl hover:shadow-indigo-900/5 group relative overflow-hidden">
                    <div class="w-16 h-16 bg-orange-50 rounded-2xl flex items-center justify-center text-orange-600 group-hover:bg-orange-600 group-hover:text-white transition-all duration-300 shadow-sm">
                        <span class="material-symbols-outlined text-3xl">assignment_late</span>
                    </div>
                    <div class="text-left">
                        <h4 class="font-black text-slate-800 uppercase text-xs tracking-widest">Pengajuan Izin</h4>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">5 Permohonan Baru</p>
                    </div>
                    <div class="absolute top-4 right-4 w-2 h-2 bg-error rounded-full animate-ping"></div>
                </button>
                <button class="w-full py-6 border-2 border-dashed border-indigo-100 rounded-[2rem] flex items-center justify-center gap-2 text-indigo-300 font-black text-[10px] uppercase tracking-[0.2em] hover:bg-indigo-50 transition-colors">
                    <span class="material-symbols-outlined text-sm">settings_suggest</span>
                    Kustomisasi Widget
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Floating Action Button for Scanners -->
<div class="fixed bottom-10 right-10 z-[60]">
    <button class="bg-primary hover:bg-indigo-700 text-white w-20 h-20 rounded-[2rem] flex items-center justify-center shadow-[0_20px_50px_rgba(47,27,200,0.4)] group transition-all duration-500 hover:rotate-12 active:scale-90">
        <span class="material-symbols-outlined text-4xl" style="font-variation-settings: 'FILL' 1;">qr_code_scanner</span>
        <div class="absolute right-24 bg-indigo-950 text-white px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] opacity-0 group-hover:opacity-100 transition-all pointer-events-none whitespace-nowrap translate-x-4 group-hover:translate-x-0">
            Scan Kehadiran Sekarang
        </div>
    </button>
</div>
@endsection
