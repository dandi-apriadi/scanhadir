@php
    $statusStyles = [
        'Hadir' => 'bg-emerald-50 text-emerald-700',
        'Telat' => 'bg-amber-50 text-amber-700',
        'Izin'  => 'bg-blue-50 text-blue-700',
        'Sakit' => 'bg-purple-50 text-purple-700',
        'Alpa'  => 'bg-rose-50 text-rose-700',
    ];
@endphp
<div class="space-y-8 max-w-[1440px] mx-auto">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.2em] text-emerald-600 mb-1">
                <span class="material-symbols-outlined text-[16px]">supervisor_account</span> Wali Kelas
            </div>
            <h1 class="text-3xl font-extrabold text-indigo-900 font-headline tracking-tight">
                {{ $class?->name ?? 'Belum ada kelas perwalian' }}
            </h1>
            @if($class)
                <p class="text-slate-500 mt-1 text-sm">{{ $class->level }} · {{ $class->major }} · {{ $students->count() }} siswa</p>
            @endif
        </div>
        <div class="flex items-end gap-3">
            @if($homeroomClasses->count() > 1)
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Kelas</label>
                    <select wire:model.live="selectedClassId" class="px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-700 outline-none focus:ring-2 focus:ring-primary/20">
                        @foreach($homeroomClasses as $hc)
                            <option value="{{ $hc->id }}">{{ $hc->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Bulan</label>
                <select wire:model.live="selectedMonth" class="px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-700 outline-none focus:ring-2 focus:ring-primary/20">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}">{{ now()->setMonth($m)->translatedFormat('F') }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Tahun</label>
                <select wire:model.live="selectedYear" class="px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-700 outline-none focus:ring-2 focus:ring-primary/20">
                    @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </div>

    @if(! $class)
        <div class="bg-white rounded-2xl border border-slate-100 p-12 text-center shadow-sm">
            <span class="material-symbols-outlined text-5xl text-slate-300">school</span>
            <h3 class="text-lg font-bold text-slate-700 mt-4">Anda belum ditetapkan sebagai wali kelas</h3>
            <p class="text-slate-500 text-sm mt-1">Hubungi admin untuk menetapkan Anda sebagai wali kelas pada menu Data Kelas.</p>
        </div>
    @else
        <!-- Snapshot Hari Ini -->
        <div>
            <h2 class="text-sm font-bold uppercase tracking-widest text-slate-400 mb-3">Absensi Hari Ini · {{ now()->translatedFormat('l, d M Y') }}</h2>
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5">
                    <p class="text-3xl font-black text-emerald-700">{{ $todaySnapshot['hadir'] }}</p>
                    <p class="text-[11px] font-bold text-emerald-600 uppercase mt-1">Hadir</p>
                </div>
                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
                    <p class="text-3xl font-black text-amber-700">{{ $todaySnapshot['telat'] }}</p>
                    <p class="text-[11px] font-bold text-amber-600 uppercase mt-1">Telat</p>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5">
                    <p class="text-3xl font-black text-blue-700">{{ $todaySnapshot['izin'] }}</p>
                    <p class="text-[11px] font-bold text-blue-600 uppercase mt-1">Izin</p>
                </div>
                <div class="bg-purple-50 border border-purple-200 rounded-2xl p-5">
                    <p class="text-3xl font-black text-purple-700">{{ $todaySnapshot['sakit'] }}</p>
                    <p class="text-[11px] font-bold text-purple-600 uppercase mt-1">Sakit</p>
                </div>
                <div class="bg-rose-50 border border-rose-200 rounded-2xl p-5">
                    <p class="text-3xl font-black text-rose-700">{{ $todaySnapshot['alpa'] }}</p>
                    <p class="text-[11px] font-bold text-rose-600 uppercase mt-1">Alpa</p>
                </div>
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5">
                    <p class="text-3xl font-black text-slate-500">{{ $todaySnapshot['belum'] }}</p>
                    <p class="text-[11px] font-bold text-slate-400 uppercase mt-1">Belum Absen</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Rekap bulan -->
            <div class="bg-gradient-to-br from-primary to-primary-container rounded-2xl p-8 text-white shadow-lg shadow-primary/20 flex flex-col justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-indigo-200">Rata-rata Kehadiran</p>
                    <p class="text-[11px] text-indigo-200/80">{{ now()->setMonth($selectedMonth)->translatedFormat('F') }} {{ $selectedYear }}</p>
                </div>
                <p class="text-6xl font-black mt-6">{{ $monthRate }}<span class="text-2xl">%</span></p>
                <div class="mt-4 h-2 bg-white/20 rounded-full overflow-hidden">
                    <div class="h-full bg-white rounded-full transition-all" style="width: {{ $monthRate }}%"></div>
                </div>
            </div>

            <!-- Siswa perlu perhatian -->
            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-50 flex items-center gap-2">
                    <span class="material-symbols-outlined text-rose-500">warning</span>
                    <h3 class="font-bold text-slate-800">Perlu Perhatian (Alpa / Telat Terbanyak)</h3>
                </div>
                <div class="divide-y divide-slate-50">
                    @forelse($attention as $s)
                        <div class="flex items-center gap-3 px-5 py-3">
                            @if($s['photo'])
                                <img src="{{ $s['photo'] }}" class="w-9 h-9 rounded-full object-cover ring-2 ring-rose-100" />
                            @else
                                <div class="w-9 h-9 rounded-full bg-rose-50 flex items-center justify-center text-rose-600 font-bold text-xs">{{ substr($s['name'], 0, 1) }}</div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-slate-800 truncate">{{ $s['name'] }}</p>
                                <p class="text-[11px] text-slate-400">NISN {{ $s['nisn'] }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($s['alpa'] > 0)<span class="px-2.5 py-1 bg-rose-50 text-rose-700 rounded-full text-[11px] font-bold">{{ $s['alpa'] }}x Alpa</span>@endif
                                @if($s['telat'] > 0)<span class="px-2.5 py-1 bg-amber-50 text-amber-700 rounded-full text-[11px] font-bold">{{ $s['telat'] }}x Telat</span>@endif
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-10 text-center text-sm text-slate-400">Tidak ada siswa bermasalah bulan ini 🎉</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Roster siswa -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-50 flex items-center justify-between">
                <h3 class="font-bold text-slate-800">Daftar Siswa & Rekap Bulan Ini</h3>
                <span class="text-[11px] font-bold text-slate-400 uppercase">{{ $students->count() }} Siswa</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 border-b border-slate-100 text-[10px] uppercase tracking-widest text-slate-400">
                        <tr>
                            <th class="px-5 py-3 font-bold">Siswa</th>
                            <th class="px-5 py-3 font-bold text-center">Hari Ini</th>
                            <th class="px-5 py-3 font-bold text-center">Hadir</th>
                            <th class="px-5 py-3 font-bold text-center">Telat</th>
                            <th class="px-5 py-3 font-bold text-center">Sakit</th>
                            <th class="px-5 py-3 font-bold text-center">Izin</th>
                            <th class="px-5 py-3 font-bold text-center">Alpa</th>
                            <th class="px-5 py-3 font-bold text-right">Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($students as $s)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        @if($s['photo'])
                                            <img src="{{ $s['photo'] }}" class="w-9 h-9 rounded-full object-cover ring-2 ring-indigo-100" />
                                        @else
                                            <div class="w-9 h-9 rounded-full bg-indigo-50 flex items-center justify-center text-primary font-bold text-xs">{{ substr($s['name'], 0, 1) }}</div>
                                        @endif
                                        <div>
                                            <p class="font-bold text-slate-800 leading-tight">{{ $s['name'] }}</p>
                                            <p class="text-[10px] text-slate-400">{{ $s['nisn'] }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    @if($s['today_status'])
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $statusStyles[$s['today_status']] ?? 'bg-slate-100 text-slate-500' }}">{{ $s['today_status'] }}</span>
                                    @else
                                        <span class="text-[10px] font-semibold text-slate-300">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-center font-bold text-emerald-600">{{ $s['hadir'] }}</td>
                                <td class="px-5 py-3 text-center font-bold text-amber-600">{{ $s['telat'] }}</td>
                                <td class="px-5 py-3 text-center font-bold text-purple-600">{{ $s['sakit'] }}</td>
                                <td class="px-5 py-3 text-center font-bold text-blue-600">{{ $s['izin'] }}</td>
                                <td class="px-5 py-3 text-center font-bold text-rose-600">{{ $s['alpa'] }}</td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-2 justify-end">
                                        <div class="w-20 bg-slate-100 rounded-full h-1.5">
                                            <div class="h-full rounded-full {{ $s['rate'] >= 75 ? 'bg-emerald-500' : ($s['rate'] >= 50 ? 'bg-amber-500' : 'bg-rose-500') }}" style="width: {{ $s['rate'] }}%"></div>
                                        </div>
                                        <span class="text-xs font-bold {{ $s['rate'] >= 75 ? 'text-emerald-600' : ($s['rate'] >= 50 ? 'text-amber-600' : 'text-rose-600') }} min-w-[42px] text-right">{{ $s['rate'] }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-5 py-10 text-center text-sm text-slate-400">Belum ada siswa di kelas ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
