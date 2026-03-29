@extends('layouts.student')

@section('content')
<div class="space-y-8">
    @if (session('status'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-6 uppercase tracking-widest">
        <a class="hover:text-primary transition-colors" href="{{ route('student.dashboard') }}">Home</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-indigo-600">Pengajuan Izin</span>
    </nav>

    <!-- Form Section -->
    <section class="bg-surface-container-lowest rounded-3xl p-10 shadow-sm border border-indigo-50/20 mb-10">
        <div class="mb-10">
            <h2 class="font-headline text-3xl font-bold text-on-surface mb-2">Pengajuan Izin & Sakit</h2>
            <p class="text-slate-500 font-body">Ajukan ketidakhadiran resmi Anda di sini. Pastikan data yang diisi sudah benar.</p>
        </div>

        <form method="POST" action="{{ route('student.izin.store') }}">
            @csrf
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Left Column -->
            <div class="space-y-8">
                <div class="space-y-3">
                    <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider">Jenis Pengajuan</label>
                    <div class="relative">
                        <select name="type" class="w-full bg-surface-container-low border-none rounded-xl py-4 px-5 text-on-surface font-medium focus:ring-2 ring-indigo-500/20 appearance-none cursor-pointer" required>
                            <option value="">Pilih Jenis Pengajuan</option>
                            <option value="excused" @selected(old('type') === 'excused')>Izin</option>
                            <option value="sick" @selected(old('type') === 'sick')>Sakit</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                    </div>
                </div>
                <div class="space-y-3">
                    <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider">Rentang Tanggal</label>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="relative">
                            <input name="date_from" value="{{ old('date_from') }}" class="w-full bg-surface-container-low border-none rounded-xl py-4 px-5 pl-12 text-on-surface font-medium focus:ring-2 ring-indigo-500/20" type="date" required/>
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-indigo-500">calendar_today</span>
                        </div>
                        <div class="relative">
                            <input name="date_to" value="{{ old('date_to') }}" class="w-full bg-surface-container-low border-none rounded-xl py-4 px-5 pl-12 text-on-surface font-medium focus:ring-2 ring-indigo-500/20" type="date" required/>
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-indigo-500">calendar_today</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Right Column -->
            <div class="space-y-8">
                <div class="space-y-3">
                    <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider">Alasan / Keterangan</label>
                    <textarea name="reason" class="w-full bg-surface-container-low border-none rounded-xl py-4 px-5 text-on-surface font-medium focus:ring-2 ring-indigo-500/20 resize-none" placeholder="Tuliskan alasan ketidakhadiran Anda secara detail..." rows="4" required>{{ old('reason') }}</textarea>
                </div>
                <div class="space-y-3">
                    <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider">Unggah Bukti</label>
                    <div class="border-2 border-dashed border-indigo-100 rounded-xl p-8 flex flex-col items-center justify-center bg-indigo-50/30 hover:bg-indigo-50/50 transition-colors cursor-pointer group">
                        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm text-indigo-500 mb-3 group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined">cloud_upload</span>
                        </div>
                        <p class="text-sm font-semibold text-on-surface">Klik untuk unggah atau seret file</p>
                        <p class="text-xs text-slate-400 mt-1">PDF, JPG, PNG (Maks. 5MB)</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-12 flex justify-end">
            <button type="submit" class="bg-gradient-to-br from-primary to-primary-container text-white px-10 py-4 rounded-xl font-bold tracking-tight shadow-xl shadow-indigo-200/50 hover:shadow-indigo-300/60 active:scale-95 transition-all flex items-center gap-3">
                Kirim Pengajuan
                <span class="material-symbols-outlined text-xl">send</span>
            </button>
        </div>
        </form>
    </section>

    <!-- Table Section -->
    <section class="space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="font-headline text-2xl font-bold text-on-surface">Riwayat Pengajuan</h3>
        </div>
        <div class="overflow-hidden rounded-3xl border border-indigo-50/20 bg-surface-container-lowest">
            <table class="w-full text-left">
                <thead class="bg-surface-container-low">
                    <tr>
                        <th class="px-8 py-5 text-xs font-bold text-slate-500 uppercase tracking-widest">Tanggal</th>
                        <th class="px-8 py-5 text-xs font-bold text-slate-500 uppercase tracking-widest">Jenis</th>
                        <th class="px-8 py-5 text-xs font-bold text-slate-500 uppercase tracking-widest">Alasan</th>
                        <th class="px-8 py-5 text-xs font-bold text-slate-500 uppercase tracking-widest">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-indigo-50/20">
                    @forelse($leaveHistory as $history)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-8 py-6">
                                <div class="flex flex-col">
                                    <span class="font-bold text-on-surface">{{ \Illuminate\Support\Carbon::parse($history->date)->format('d M Y') }}</span>
                                    <span class="text-[10px] text-slate-400 font-semibold">1 Hari</span>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-bold rounded-full uppercase">{{ $history->status === 'sick' ? 'Sakit' : 'Izin' }}</span>
                            </td>
                            <td class="px-8 py-6">
                                <p class="text-sm text-slate-600 truncate max-w-[320px]">{{ $history->notes ?? '-' }}</p>
                            </td>
                            <td class="px-8 py-6">
                                @if(($history->approval_status ?? 'pending') === 'approved')
                                    <span class="flex items-center gap-1.5 text-emerald-600 text-xs font-bold">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        Disetujui
                                    </span>
                                @elseif(($history->approval_status ?? 'pending') === 'rejected')
                                    <span class="flex items-center gap-1.5 text-rose-600 text-xs font-bold">
                                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                        Ditolak
                                    </span>
                                @else
                                    <span class="flex items-center gap-1.5 text-amber-600 text-xs font-bold">
                                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                        Menunggu Persetujuan
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-8 py-8 text-center text-sm text-slate-500">Belum ada riwayat izin/sakit.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
