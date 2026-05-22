@extends('layouts.admin')

@section('content')
<div class="space-y-8">
    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <!-- Breadcrumbs & Header -->
    <div class="mb-10">
        <nav class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-2 tracking-wide uppercase">
            <a class="hover:text-indigo-600 transition-colors" href="{{ route('admin.dashboard') }}">Dashboard</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-indigo-600">Koreksi Kehadiran</span>
        </nav>
        <div class="flex items-end justify-between">
            <div>
                <h2 class="text-3xl font-extrabold text-on-surface tracking-tight font-headline">Permintaan Koreksi Kehadiran</h2>
                <p class="text-slate-500 mt-1 text-sm">Kelola permohonan perubahan status kehadiran mahasiswa.</p>
            </div>
            <a href="{{ route('correction.create') }}" class="bg-gradient-to-r from-primary to-primary-container text-white px-6 py-3 rounded-xl font-bold text-sm flex items-center gap-2 shadow-lg shadow-indigo-200 hover:scale-95 transition-transform duration-150">
                <span class="material-symbols-outlined">add</span>
                Buat Koreksi
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center">
                <span class="material-symbols-outlined text-slate-500 text-2xl">edit_note</span>
            </div>
            <div>
                <p class="text-2xl font-black text-on-surface">{{ $summaryCounts['total'] ?? 0 }}</p>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-amber-100 shadow-sm p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                <span class="material-symbols-outlined text-amber-600 text-2xl">pending</span>
            </div>
            <div>
                <p class="text-2xl font-black text-amber-600">{{ $summaryCounts['pending'] ?? 0 }}</p>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pending</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-emerald-100 shadow-sm p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center">
                <span class="material-symbols-outlined text-emerald-600 text-2xl">check_circle</span>
            </div>
            <div>
                <p class="text-2xl font-black text-emerald-600">{{ $summaryCounts['approved'] ?? 0 }}</p>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Disetujui</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-rose-100 shadow-sm p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-rose-100 flex items-center justify-center">
                <span class="material-symbols-outlined text-rose-600 text-2xl">cancel</span>
            </div>
            <div>
                <p class="text-2xl font-black text-rose-600">{{ $summaryCounts['rejected'] ?? 0 }}</p>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Ditolak</p>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <form method="GET" action="{{ route('correction') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-white rounded-2xl border border-slate-100 shadow-sm">
        <div class="space-y-1.5">
            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500 ml-1">Status Approval</label>
            <select name="status" class="w-full bg-slate-50 border-none rounded-xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-primary/20 py-2.5 px-4">
                <option value="">Semua Status</option>
                @foreach($approvalStatusOptions as $key => $label)
                    <option value="{{ $key }}" @selected($selectedStatus === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="w-full py-2.5 px-4 bg-slate-900 text-white rounded-xl text-sm font-bold hover:opacity-90 transition-colors flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-lg">search</span>
                Terapkan
            </button>
            <a href="{{ route('correction') }}" class="w-full py-2.5 px-4 bg-primary/5 text-primary rounded-xl text-sm font-bold hover:bg-primary/10 transition-colors flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-lg">filter_alt_off</span>
                Reset
            </a>
        </div>
    </form>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tanggal</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Mahasiswa</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Jadwal</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Perubahan</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Status</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($corrections as $correction)
                        <tr class="hover:bg-indigo-50/30 transition-colors group">
                            <td class="px-6 py-5 text-sm font-bold text-slate-600">{{ \Carbon\Carbon::parse($correction->tanggal)->format('d M Y') }}</td>
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center text-primary font-bold text-[10px]">
                                        {{ strtoupper(substr($correction->student?->user?->name ?? 'M', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-on-surface">{{ $correction->student?->user?->name ?? '-' }}</p>
                                        <p class="text-[10px] text-slate-400">{{ $correction->student?->nisn ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-sm text-slate-600">
                                <p class="font-bold">{{ $correction->schedule?->subject?->name ?? '-' }}</p>
                                <p class="text-[10px] text-slate-400">{{ $correction->schedule?->class?->name ?? '-' }} • {{ $correction->schedule?->day ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-1 bg-rose-100 text-rose-700 rounded text-xs font-bold">{{ $correction->status_lama }}</span>
                                    <span class="material-symbols-outlined text-sm text-slate-400">arrow_forward</span>
                                    <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded text-xs font-bold">{{ $correction->status_baru }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-center">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-amber-100 text-amber-700',
                                        'approved' => 'bg-emerald-100 text-emerald-700',
                                        'rejected' => 'bg-rose-100 text-rose-700',
                                    ];
                                    $color = $statusColors[$correction->approval_status] ?? 'bg-slate-100 text-slate-500';
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $color }}">
                                    {{ $approvalStatusOptions[$correction->approval_status] ?? $correction->approval_status }}
                                </span>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <div class="flex items-center justify-end gap-1 text-slate-400">
                                    @if($correction->approval_status === 'pending')
                                        <form method="POST" action="{{ route('correction.approve', $correction->id) }}" onsubmit="return confirm('Setujui koreksi ini?')">
                                            @csrf
                                            <button type="submit" class="p-2 hover:text-emerald-500 transition-colors" title="Setujui"><span class="material-symbols-outlined text-[20px]">check_circle</span></button>
                                        </form>
                                        <form method="POST" action="{{ route('correction.reject', $correction->id) }}" onsubmit="return confirm('Tolak koreksi ini?')">
                                            @csrf
                                            <button type="submit" class="p-2 hover:text-rose-500 transition-colors" title="Tolak"><span class="material-symbols-outlined text-[20px]">cancel</span></button>
                                        </form>
                                    @endif
                                    <a href="{{ route('correction.edit', $correction->id) }}" class="p-2 hover:text-primary transition-colors"><span class="material-symbols-outlined text-[20px]">edit_note</span></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-slate-500">Belum ada permintaan koreksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 bg-slate-50/50 border-t border-slate-50 flex items-center justify-between">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Showing {{ $corrections->count() }} of {{ $corrections->total() }}</span>
            {{ $corrections->onEachSide(1)->links() }}
        </div>
    </div>
</div>
@endsection
