@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="grid grid-cols-12 gap-8">
        <!-- Settings Tabs Navigation -->
        <div class="col-span-12 lg:col-span-3">
            <div class="flex flex-col space-y-2 sticky top-24">
                <button class="flex items-center gap-3 px-4 py-3.5 rounded-xl bg-white shadow-sm text-indigo-700 font-bold border-l-4 border-indigo-600 transition-all">
                    <span class="material-symbols-outlined text-xl">account_balance</span>
                    <span class="text-sm">Informasi Sekolah</span>
                </button>
                <button class="flex items-center gap-3 px-4 py-3.5 rounded-xl text-slate-500 hover:bg-surface-container-low font-medium transition-all">
                    <span class="material-symbols-outlined text-xl">tune</span>
                    <span class="text-sm">Konfigurasi Presensi</span>
                </button>
                <button class="flex items-center gap-3 px-4 py-3.5 rounded-xl text-slate-500 hover:bg-surface-container-low font-medium transition-all">
                    <span class="material-symbols-outlined text-xl">security</span>
                    <span class="text-sm">Akun & Keamanan</span>
                </button>
            </div>
        </div>

        <!-- Settings Content Area -->
        <div class="col-span-12 lg:col-span-9 space-y-8">
            <!-- Section: Informasi Sekolah -->
            <section class="bg-surface-container-lowest rounded-2xl p-8 border border-outline-variant/15 shadow-sm">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h3 class="text-xl font-bold text-on-surface">Informasi Sekolah</h3>
                        <p class="text-sm text-slate-500">Atur identitas resmi sekolah untuk laporan presensi.</p>
                    </div>
                    <span class="material-symbols-outlined text-indigo-200 text-4xl">domain</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Sekolah</label>
                        <input class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all" type="text" value="SMK Negeri 1 Bandung"/>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">NPSN</label>
                        <input class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all" type="text" value="20212345"/>
                    </div>
                    <div class="md:col-span-2 space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Alamat Sekolah</label>
                        <textarea class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all" rows="3">Jl. Wastukencana No.75, Tamansari, Kec. Bandung Wetan, Kota Bandung, Jawa Barat 40116</textarea>
                    </div>
                    <div class="md:col-span-2 space-y-4">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Logo Sekolah</label>
                        <div class="flex items-center gap-6 p-6 border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50/50">
                            <div class="relative w-24 h-24 rounded-2xl overflow-hidden bg-white shadow-sm border border-slate-100 flex items-center justify-center group cursor-pointer">
                                <span class="material-symbols-outlined text-slate-300 text-4xl group-hover:scale-110 transition-transform">image</span>
                                <div class="absolute inset-0 bg-primary/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <span class="material-symbols-outlined text-white">edit</span>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <p class="text-sm font-semibold text-on-surface">Unggah Logo Baru</p>
                                <p class="text-xs text-slate-400 max-w-xs">Format PNG, JPG atau SVG. Maksimal 2MB. Rekomendasi 512x512px.</p>
                                <button class="mt-2 text-xs font-bold text-primary hover:underline flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">upload</span> Pilih File
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section: Konfigurasi Presensi -->
            <section class="bg-surface-container-lowest rounded-2xl p-8 border border-outline-variant/15 shadow-sm">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h3 class="text-xl font-bold text-on-surface">Konfigurasi Presensi</h3>
                        <p class="text-sm text-slate-500">Atur parameter waktu dan jadwal kerja sistem.</p>
                    </div>
                    <span class="material-symbols-outlined text-indigo-200 text-4xl">schedule</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Batas Jam Masuk</label>
                            <div class="relative">
                                <input class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all" type="time" value="07:00"/>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Toleransi Keterlambatan</label>
                            <div class="flex items-center gap-3">
                                <input class="w-24 bg-slate-50 border-none rounded-xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all font-bold" type="number" value="15"/>
                                <span class="text-sm font-medium text-slate-600">Menit</span>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Hari Kerja Aktif</label>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                            <label class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-transparent hover:border-primary/20 cursor-pointer transition-all">
                                <input checked class="w-5 h-5 rounded border-slate-300 text-primary focus:ring-primary" type="checkbox"/>
                                <span class="text-sm font-medium text-on-surface">{{ $hari }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            <!-- Final Action Bar -->
            <div class="flex items-center justify-between p-6 bg-indigo-50/50 rounded-2xl border border-indigo-100/50">
                <div class="flex items-center gap-3 text-indigo-700">
                    <span class="material-symbols-outlined">info</span>
                    <p class="text-xs font-medium italic">Semua perubahan akan langsung diterapkan pada sistem.</p>
                </div>
                <div class="flex items-center gap-4">
                    <button class="px-6 py-2.5 rounded-xl text-slate-500 font-bold hover:bg-slate-200/50 transition-all text-sm uppercase tracking-widest">Batal</button>
                    <button class="px-8 py-3 rounded-xl bg-gradient-to-r from-primary to-primary-container text-white font-bold shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all text-sm uppercase tracking-widest">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
