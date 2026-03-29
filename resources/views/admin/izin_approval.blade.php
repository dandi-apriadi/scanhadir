@extends('layouts.admin')

@section('content')
<div class="space-y-8">
    @if (session('status'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm text-slate-500">Kelola pengajuan izin dan sakit siswa secara terpusat.</p>
        </div>
        <form method="GET" class="grid w-full gap-3 sm:grid-cols-3 lg:w-auto">
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama, NISN, alasan" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm focus:border-primary focus:outline-none" />
            <select name="class_id" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm focus:border-primary focus:outline-none">
                <option value="0">Semua kelas</option>
                @foreach($classOptions as $classOption)
                    <option value="{{ $classOption->id }}" @selected($classId === $classOption->id)>{{ $classOption->name }}</option>
                @endforeach
            </select>
            <button class="rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white" type="submit">Terapkan Filter</button>
            <input type="hidden" name="approval" value="{{ $approval }}">
        </form>
    </div>

    <div class="flex flex-wrap items-center gap-3 border-b border-outline-variant/15 pb-2">
        @php
            $tabs = [
                'all' => 'Semua',
                'pending' => 'Pending',
                'approved' => 'Disetujui',
                'rejected' => 'Ditolak',
            ];
        @endphp
        @foreach($tabs as $key => $label)
            <a href="{{ route('admin.izin_approval', array_merge(request()->query(), ['approval' => $key])) }}"
               class="rounded-full px-4 py-2 text-sm font-semibold {{ $approval === $key ? 'bg-primary text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }} transition-colors">
                {{ $label }}
                <span class="ml-1 text-xs">({{ $counts[$key] ?? 0 }})</span>
            </a>
        @endforeach
    </div>

    <div class="overflow-hidden rounded-xl border border-outline-variant/10 bg-surface-container-lowest shadow-sm">
        <table class="w-full border-collapse text-left">
            <thead>
                <tr class="bg-slate-50">
                    <th class="px-5 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-500">No</th>
                    <th class="px-5 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-500">Siswa</th>
                    <th class="px-5 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-500">Jenis</th>
                    <th class="px-5 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-500">Tanggal</th>
                    <th class="px-5 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-500">Status</th>
                    <th class="px-5 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-500">Alasan</th>
                    <th class="px-5 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-500 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($submissions as $index => $submission)
                    <tr class="hover:bg-slate-50/80">
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $submissions->firstItem() + $index }}</td>
                        <td class="px-5 py-4">
                            <p class="text-sm font-bold text-on-surface">{{ $submission->student?->user?->name ?? '-' }}</p>
                            <p class="text-[11px] text-slate-500">{{ $submission->student?->class?->name ?? '-' }} | NISN {{ $submission->student?->nisn ?? '-' }}</p>
                        </td>
                        <td class="px-5 py-4">
                            <span class="rounded-full px-3 py-1 text-[10px] font-bold uppercase {{ $submission->status === 'sick' ? 'border border-amber-100 bg-amber-50 text-amber-700' : 'border border-sky-100 bg-sky-50 text-sky-700' }}">
                                {{ $submission->status === 'sick' ? 'Sakit' : 'Izin' }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ \Illuminate\Support\Carbon::parse($submission->date)->format('d M Y') }}</td>
                        <td class="px-5 py-4">
                            @if($submission->approval_status === 'approved')
                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">Disetujui</span>
                            @elseif($submission->approval_status === 'rejected')
                                <span class="rounded-full bg-rose-50 px-3 py-1 text-xs font-bold text-rose-700">Ditolak</span>
                            @else
                                <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700">Pending</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <p class="max-w-[260px] truncate text-sm text-slate-600" title="{{ $submission->notes ?? '-' }}">{{ $submission->notes ?? '-' }}</p>
                            @if($submission->rejection_reason)
                                <p class="mt-1 text-[11px] text-rose-600">Alasan tolak: {{ $submission->rejection_reason }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            @if($submission->approval_status === 'pending' || $submission->approval_status === null)
                                <div class="flex items-center justify-end gap-2">
                                    <form method="POST" action="{{ route('admin.izin_approval.approve', $submission) }}">
                                        @csrf
                                        <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">Setujui</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.izin_approval.reject', $submission) }}" class="flex items-center gap-2">
                                        @csrf
                                        <input type="text" name="rejection_reason" placeholder="Opsional" class="w-28 rounded-lg border border-slate-200 px-2 py-1 text-xs focus:border-primary focus:outline-none" />
                                        <button type="submit" class="rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-rose-700">Tolak</button>
                                    </form>
                                </div>
                            @else
                                <p class="text-right text-xs font-semibold text-slate-500">Selesai diproses</p>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-sm text-slate-500">Tidak ada data pengajuan untuk filter saat ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $submissions->links() }}
    </div>
</div>
@endsection
