@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Laporan Presensi</h1>
            <p class="text-sm text-slate-500">Daily summary dan export data ke CSV/PDF.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.reports.export.csv') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold">Export CSV</a>
            <a href="{{ route('admin.reports.export.pdf') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold">Export PDF</a>
        </div>
    </div>

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
            <p class="text-2xl font-bold text-emerald-600">{{ $summary['present'] }}</p>
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
                @foreach($rows as $row)
                    <tr>
                        <td class="px-4 py-3">{{ $row['date'] }}</td>
                        <td class="px-4 py-3">{{ $row['student_name'] }}</td>
                        <td class="px-4 py-3">{{ $row['class_name'] }}</td>
                        <td class="px-4 py-3">{{ $row['check_in'] ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $row['check_out'] ?? '-' }}</td>
                        <td class="px-4 py-3">{{ ucfirst($row['status']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
