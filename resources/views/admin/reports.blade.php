@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Laporan Presensi</h1>
            <p class="text-sm text-slate-500">Filter data presensi berdasarkan rentang tanggal, kelas, status, dan kata kunci.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.reports.export.excel', request()->query(), false) }}" download="laporan-presensi-{{ now()->format('Y-m-d') }}.xlsx" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">download</span>
                Export Excel
            </a>
            <a href="{{ route('admin.reports.export.pdf', request()->query(), false) }}" download="laporan-presensi-{{ now()->format('Y-m-d') }}.pdf" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">picture_as_pdf</span>
                Export PDF
            </a>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.reports') }}" class="bg-white rounded-xl border border-slate-100 p-4 grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-slate-500 mb-1">Cari Siswa / NISN</label>
            <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Nama atau NISN" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-200" />
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Tanggal Mulai</label>
            <input type="date" name="date_from" value="{{ $filters['date_from'] }}" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-200" />
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Tanggal Selesai</label>
            <input type="date" name="date_to" value="{{ $filters['date_to'] }}" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-200" />
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Kelas</label>
            <select name="class_id" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-200">
                <option value="0">Semua Kelas</option>
                @foreach($classOptions as $classOption)
                    <option value="{{ $classOption->id }}" @selected($filters['class_id'] === $classOption->id)>{{ $classOption->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Status</label>
            <select name="status" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-200">
                <option value="">Semua Status</option>
                @foreach($statusOptions as $value => $label)
                    <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-6 flex items-center gap-2 justify-end">
            <a href="{{ route('admin.reports') }}" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg text-sm font-semibold">Reset</a>
            <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-lg text-sm font-semibold">Terapkan Filter</button>
        </div>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-100">
            <p class="text-xs text-slate-500 uppercase">Total Siswa</p>
            <p class="text-2xl font-bold">{{ $summary['total_students'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-100">
            <p class="text-xs text-slate-500 uppercase">Total Presensi Hari Ini</p>
            <p class="text-2xl font-bold">{{ $summary['total_scanned'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-100">
            <p class="text-xs text-slate-500 uppercase">Hadir</p>
            <p class="text-2xl font-bold text-emerald-600">{{ $summary['hadir'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
                <tr>
                    <th class="text-left px-4 py-3">Tanggal</th>
                    <th class="text-left px-4 py-3">Nama</th>
                    <th class="text-left px-4 py-3">Kelas</th>
                    <th class="text-left px-4 py-3">Masuk</th>
                    <th class="text-left px-4 py-3">Pulang</th>
                    <th class="text-left px-4 py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($rows as $row)
                    @php
                        $status = (string) ($row->status ?? '');
                        $statusLabel = ucfirst($status ?: '-');
                        $statusClasses = match (strtolower($status)) {
                            'present', 'hadir' => 'bg-emerald-100 text-emerald-700',
                            'late', 'telat' => 'bg-amber-100 text-amber-700',
                            'sick', 'sakit' => 'bg-sky-100 text-sky-700',
                            'excused', 'izin' => 'bg-violet-100 text-violet-700',
                            'absent', 'alpa' => 'bg-rose-100 text-rose-700',
                            default => 'bg-slate-100 text-slate-600',
                        };
                    @endphp
                    <tr>
                        <td class="px-4 py-3">{{ $row->date }}</td>
                        <td class="px-4 py-3">{{ $row->student?->user?->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $row->student?->class?->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $row->check_in ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $row->check_out ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-500">Data presensi tidak ditemukan untuk filter yang dipilih.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="flex items-center justify-between">
        <p class="text-xs text-slate-500">Menampilkan {{ $rows->count() }} dari {{ $rows->total() }} data</p>
        <div>{{ $rows->onEachSide(1)->links() }}</div>
    </div>
</div>
@endsection
