@extends('layouts.admin')

@section('content')
<div class="max-w-[1440px] mx-auto">
    <!-- Header Section -->
    <div class="mb-10 flex justify-between items-end">
        <div>
            <h2 class="text-3xl font-bold font-headline tracking-tight text-on-surface">Data Analytics</h2>
            <p class="text-slate-500 mt-1">Detailed statistical analysis of student attendance and performance.</p>
        </div>
        <div class="flex gap-3">
            <select class="px-5 py-2.5 bg-surface-container-lowest text-on-surface border border-outline-variant/15 rounded-xl text-sm font-semibold outline-none focus:ring-2 focus:ring-primary/20 transition-all">
                <option>Bulan Ini</option>
                <option>Minggu Ini</option>
                <option>Semester Ganjil</option>
            </select>
            <button class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-br from-primary to-primary-container text-white rounded-xl text-sm font-semibold shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all">
                <span class="material-symbols-outlined text-lg">description</span>
                Generate Report
            </button>
        </div>
    </div>

    <!-- Analytics Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Chart Area -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-outline-variant/15 p-8 relative overflow-hidden shadow-sm">
            <div class="flex justify-between items-center mb-10">
                <div>
                    <h3 class="text-xl font-bold font-headline text-on-surface">Trend Kehadiran Mingguan</h3>
                    <p class="text-sm text-slate-500">Senin, 12 Okt - Jumat, 16 Okt</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-primary"></span>
                        <span class="text-xs font-semibold text-slate-600">Hadir</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                        <span class="text-xs font-semibold text-slate-600">Terlambat</span>
                    </div>
                </div>
            </div>
            
            <!-- Simulated Chart Visualization -->
            <div class="h-80 w-full relative">
                <div class="absolute inset-0 flex items-end justify-between px-4 pb-12">
                    <!-- Chart Grid Lines -->
                    <div class="absolute inset-0 flex flex-col justify-between border-b border-slate-100 py-12 pointer-events-none">
                        <div class="w-full h-px bg-slate-100/50"></div>
                        <div class="w-full h-px bg-slate-100/50"></div>
                        <div class="w-full h-px bg-slate-100/50"></div>
                    </div>
                    
                    @php
                        $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
                        $heights = [92, 88, 95, 94.2, 91];
                        $late_heights = [15, 8, 12, 10, 20];
                    @endphp

                    @foreach($days as $index => $day)
                    <div class="relative w-16 h-64 bg-slate-100/50 rounded-t-lg group cursor-pointer overflow-hidden transition-all hover:bg-slate-200/50">
                        <div class="absolute bottom-0 w-full h-[{{ $heights[$index] }}%] bg-gradient-to-t from-primary to-primary-container rounded-t-lg transition-all group-hover:opacity-80"></div>
                        <div class="absolute bottom-0 w-full h-[{{ $late_heights[$index] }}%] bg-emerald-400/30"></div>
                        <p class="absolute -bottom-8 left-1/2 -translate-x-1/2 text-xs font-bold text-slate-400">{{ $day }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Distribution Chart Area -->
        <div class="bg-white rounded-2xl border border-outline-variant/15 p-8 shadow-sm">
            <h3 class="text-xl font-bold font-headline text-on-surface mb-8">Distribusi Status</h3>
            <div class="relative w-48 h-48 mx-auto mb-8">
                <svg class="w-full h-full transform -rotate-90">
                    <circle class="text-slate-100" cx="96" cy="96" fill="transparent" r="80" stroke="currentColor" stroke-width="12"></circle>
                    <circle class="text-primary" cx="96" cy="96" fill="transparent" r="80" stroke="currentColor" stroke-dasharray="502.4" stroke-dashoffset="50.2" stroke-width="12"></circle>
                    <circle class="text-emerald-500" cx="96" cy="96" fill="transparent" r="80" stroke="currentColor" stroke-dasharray="502.4" stroke-dashoffset="450.2" stroke-width="12"></circle>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-3xl font-black text-on-surface">94%</span>
                    <span class="text-[10px] text-slate-400 font-bold uppercase">Avg Rate</span>
                </div>
            </div>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 bg-primary rounded-full"></span>
                        <span class="text-sm font-medium text-slate-600">Tepat Waktu</span>
                    </div>
                    <span class="text-sm font-bold text-on-surface">88%</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 bg-emerald-500 rounded-full"></span>
                        <span class="text-sm font-medium text-slate-600">Terlambat</span>
                    </div>
                    <span class="text-sm font-bold text-on-surface">12%</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 bg-rose-500 rounded-full"></span>
                        <span class="text-sm font-medium text-slate-600">Alpa</span>
                    </div>
                    <span class="text-sm font-bold text-on-surface">2%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Insights -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-surface-container-low p-8 rounded-2xl border border-outline-variant/15">
            <h4 class="text-lg font-bold font-headline mb-4">Kelas dengan Kehadiran Tertinggi</h4>
            <div class="space-y-4">
                <div class="bg-white p-4 rounded-xl flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center text-primary font-bold">1</div>
                        <div>
                            <p class="text-sm font-bold">XII RPL 1</p>
                            <p class="text-xs text-slate-500">Wali Kelas: Bp. Suryono</p>
                        </div>
                    </div>
                    <span class="text-lg font-black text-emerald-600">99.2%</span>
                </div>
                <!-- More entries -->
            </div>
        </div>
        <div class="bg-surface-container-low p-8 rounded-2xl border border-outline-variant/15">
            <h4 class="text-lg font-bold font-headline mb-4">Efisiensi Sistem Scan QR</h4>
            <p class="text-sm text-slate-600 mb-6">Kecepatan rata-rata pemindaian per siswa meningkat 12% dari semester lalu.</p>
            <div class="flex gap-4">
                <div class="flex-1 bg-white p-4 rounded-xl shadow-sm text-center">
                    <p class="text-[10px] font-bold text-slate-400 uppercase">Avg Response Time</p>
                    <p class="text-2xl font-black text-primary">0.42s</p>
                </div>
                <div class="flex-1 bg-white p-4 rounded-xl shadow-sm text-center">
                    <p class="text-[10px] font-bold text-slate-400 uppercase">Max Concurrency</p>
                    <p class="text-2xl font-black text-primary">240/m</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
