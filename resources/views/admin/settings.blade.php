@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto">
    @if (session('status'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}" class="grid grid-cols-12 gap-8">
        @csrf
        @method('PUT')

        <div class="col-span-12 lg:col-span-3">
            <div class="sticky top-24 flex flex-col space-y-2">
                <button type="button" class="flex items-center gap-3 rounded-xl border-l-4 border-indigo-600 bg-white px-4 py-3.5 font-bold text-indigo-700 shadow-sm transition-all">
                    <span class="material-symbols-outlined text-xl">account_balance</span>
                    <span class="text-sm">Informasi Sekolah</span>
                </button>
                <button type="button" class="flex items-center gap-3 rounded-xl px-4 py-3.5 font-medium text-slate-500 transition-all hover:bg-surface-container-low">
                    <span class="material-symbols-outlined text-xl">tune</span>
                    <span class="text-sm">Konfigurasi Presensi</span>
                </button>
            </div>
        </div>

        <div class="col-span-12 space-y-8 lg:col-span-9">
            <section class="rounded-2xl border border-outline-variant/15 bg-surface-container-lowest p-8 shadow-sm">
                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-on-surface">Informasi Sekolah</h3>
                        <p class="text-sm text-slate-500">Atur identitas resmi sekolah untuk laporan presensi.</p>
                    </div>
                    <span class="material-symbols-outlined text-4xl text-indigo-200">domain</span>
                </div>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Nama Sekolah</label>
                        <input name="school_name" class="w-full rounded-xl border-none bg-slate-50 px-4 py-3 text-on-surface transition-all focus:ring-2 focus:ring-primary/20" type="text" value="{{ old('school_name', $settings->school_name) }}" required/>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-500">NPSN</label>
                        <input name="npsn" class="w-full rounded-xl border-none bg-slate-50 px-4 py-3 text-on-surface transition-all focus:ring-2 focus:ring-primary/20" type="text" value="{{ old('npsn', $settings->npsn) }}"/>
                    </div>
                    <div class="space-y-2 md:col-span-2">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Alamat Sekolah</label>
                        <textarea name="school_address" class="w-full rounded-xl border-none bg-slate-50 px-4 py-3 text-on-surface transition-all focus:ring-2 focus:ring-primary/20" rows="3">{{ old('school_address', $settings->school_address) }}</textarea>
                    </div>
                    <div class="space-y-4 md:col-span-2">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Logo Sekolah</label>
                        <div class="flex items-center gap-6 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 p-6">
                            <div class="relative flex h-24 w-24 cursor-pointer items-center justify-center overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm group">
                                <span class="material-symbols-outlined text-4xl text-slate-300 transition-transform group-hover:scale-110">image</span>
                                <div class="absolute inset-0 flex items-center justify-center bg-primary/40 opacity-0 transition-opacity group-hover:opacity-100">
                                    <span class="material-symbols-outlined text-white">edit</span>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <p class="text-sm font-semibold text-on-surface">Unggah Logo Baru</p>
                                <p class="max-w-xs text-xs text-slate-400">Format PNG, JPG atau SVG. Maksimal 2MB. Rekomendasi 512x512px.</p>
                                <button type="button" onclick="showToast('Upload logo akan diaktifkan pada iterasi berikutnya.')" class="mt-2 flex items-center gap-1 text-xs font-bold text-primary hover:underline">
                                    <span class="material-symbols-outlined text-sm">upload</span> Pilih File
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-outline-variant/15 bg-surface-container-lowest p-8 shadow-sm">
                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-on-surface">Konfigurasi Presensi</h3>
                        <p class="text-sm text-slate-500">Atur parameter waktu dan jadwal kerja sistem.</p>
                    </div>
                    <span class="material-symbols-outlined text-4xl text-indigo-200">schedule</span>
                </div>
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Batas Jam Masuk</label>
                            <input name="attendance_start_time" class="w-full rounded-xl border-none bg-slate-50 px-4 py-3 text-on-surface transition-all focus:ring-2 focus:ring-primary/20" type="time" value="{{ old('attendance_start_time', \Illuminate\Support\Carbon::parse($settings->attendance_start_time)->format('H:i')) }}"/>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Toleransi Keterlambatan</label>
                            <div class="flex items-center gap-3">
                                <input name="late_tolerance_minutes" class="w-24 rounded-xl border-none bg-slate-50 px-4 py-3 font-bold text-on-surface transition-all focus:ring-2 focus:ring-primary/20" type="number" min="0" max="180" value="{{ old('late_tolerance_minutes', $settings->late_tolerance_minutes) }}"/>
                                <span class="text-sm font-medium text-slate-600">Menit</span>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Hari Kerja Aktif</label>
                        @php
                            $activeDays = old('active_days', $settings->active_days ?? []);
                        @endphp
                        <div class="grid grid-cols-2 gap-3">
                            @foreach($dayOptions as $hari)
                                <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-transparent bg-slate-50 p-3 transition-all hover:border-primary/20">
                                    <input name="active_days[]" value="{{ $hari }}" @checked(in_array($hari, $activeDays, true)) class="h-5 w-5 rounded border-slate-300 text-primary focus:ring-primary" type="checkbox"/>
                                    <span class="text-sm font-medium text-on-surface">{{ $hari }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            <div class="flex items-center justify-between rounded-2xl border border-indigo-100/50 bg-indigo-50/50 p-6">
                <div class="flex items-center gap-3 text-indigo-700">
                    <span class="material-symbols-outlined">info</span>
                    <p class="text-xs font-medium italic">Perubahan akan diterapkan setelah disimpan.</p>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.settings') }}" class="rounded-xl px-6 py-2.5 text-sm font-bold uppercase tracking-widest text-slate-500 transition-all hover:bg-slate-200/50">Reset</a>
                    <button type="submit" class="rounded-xl bg-gradient-to-r from-primary to-primary-container px-8 py-3 text-sm font-bold uppercase tracking-widest text-white shadow-lg shadow-primary/20 transition-all hover:scale-[1.02] active:scale-95">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
