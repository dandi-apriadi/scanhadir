@extends('layouts.admin')

@section('content')
<div class="space-y-8">
    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Breadcrumbs & Header -->
    <div class="mb-10">
        <nav class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-2 tracking-wide uppercase">
            <a class="hover:text-indigo-600 transition-colors" href="{{ route('correction') }}">Koreksi Kehadiran</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-indigo-600">{{ isset($correction) ? 'Edit Koreksi' : 'Buat Koreksi Baru' }}</span>
        </nav>
        <div>
            <h2 class="text-3xl font-extrabold text-on-surface tracking-tight font-headline">{{ isset($correction) ? 'Edit Permintaan Koreksi' : 'Buat Permintaan Koreksi Baru' }}</h2>
            <p class="text-slate-500 mt-1 text-sm">Ajukan perubahan status kehadiran untuk mahasiswa tertentu.</p>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8">
        <form method="POST" action="{{ isset($correction) ? route('correction.update', $correction->id) : route('correction.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @if(isset($correction))
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Mahasiswa -->
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500 ml-1">Mahasiswa</label>
                    <select name="student_id" class="w-full bg-slate-50 border-none rounded-xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-primary/20 py-2.5 px-4" required>
                        <option value="">Pilih Mahasiswa</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" @selected(isset($correction) && $correction->student_id == $student->id)>{{ $student->user?->name ?? '-' }} ({{ $student->nisn }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Jadwal -->
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500 ml-1">Jadwal</label>
                    <select name="schedule_id" class="w-full bg-slate-50 border-none rounded-xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-primary/20 py-2.5 px-4" required>
                        <option value="">Pilih Jadwal</option>
                        @foreach($schedules as $schedule)
                            <option value="{{ $schedule->id }}" @selected(isset($correction) && $correction->schedule_id == $schedule->id)>{{ $schedule->subject?->name ?? '-' }} - {{ $schedule->class?->name ?? '-' }} ({{ $schedule->day }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tanggal -->
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500 ml-1">Tanggal</label>
                    <input name="tanggal" value="{{ $correction->tanggal?->format('Y-m-d') ?? old('tanggal') }}" type="date" class="w-full bg-slate-50 border-none rounded-xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-primary/20 py-2.5 px-4" required>
                </div>

                <!-- Status Lama -->
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500 ml-1">Status Lama</label>
                    <select name="status_lama" class="w-full bg-slate-50 border-none rounded-xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-primary/20 py-2.5 px-4" required>
                        <option value="">Pilih Status</option>
                        @foreach($statusOptions as $key => $label)
                            <option value="{{ $key }}" @selected(isset($correction) && $correction->status_lama == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Baru -->
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500 ml-1">Status Baru</label>
                    <select name="status_baru" class="w-full bg-slate-50 border-none rounded-xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-primary/20 py-2.5 px-4" required>
                        <option value="">Pilih Status</option>
                        @foreach($statusOptions as $key => $label)
                            <option value="{{ $key }}" @selected(isset($correction) && $correction->status_baru == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Dokumen -->
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500 ml-1">Dokumen Pendukung (Opsional)</label>
                    <input name="dokumen" type="file" accept=".pdf,.jpg,.jpeg,.png" class="w-full bg-slate-50 border-none rounded-xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-primary/20 py-2.5 px-4">
                    @if(isset($correction) && $correction->dokumen)
                        <p class="text-xs text-slate-400 mt-1">File saat ini: {{ $correction->dokumen }}</p>
                    @endif
                </div>
            </div>

            <!-- Alasan -->
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500 ml-1">Alasan Koreksi</label>
                <textarea name="alasan" rows="4" class="w-full bg-slate-50 border-none rounded-xl text-sm font-semibold focus:ring-2 focus:ring-primary/20 py-2.5 px-4" placeholder="Jelaskan alasan perubahan status kehadiran..." required>{{ $correction->alasan ?? old('alasan') }}</textarea>
            </div>

            <!-- Submit -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('correction') }}" class="px-6 py-2.5 bg-slate-100 text-slate-600 rounded-xl text-sm font-bold hover:bg-slate-200 transition-colors">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-primary text-white rounded-xl text-sm font-bold hover:opacity-90 transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">save</span>
                    {{ isset($correction) ? 'Update Koreksi' : 'Ajukan Koreksi' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
