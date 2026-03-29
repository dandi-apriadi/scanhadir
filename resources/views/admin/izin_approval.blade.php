@extends('layouts.admin')

@section('content')
<div class="flex flex-1 overflow-hidden -m-8 h-[calc(100vh-64px)]">
    <!-- Main Content Area -->
    <div class="flex-1 p-8 overflow-y-auto">
        <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-800">
            Halaman persetujuan saat ini masih mode preview (read-only).
        </div>

        <!-- Breadcrumbs -->
        <nav class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-2 uppercase tracking-widest">
            <span>Dashboard</span>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-primary">Persetujuan</span>
        </nav>
        <h2 class="text-3xl font-bold font-headline text-on-surface mb-8 tracking-tight">Persetujuan Ketidakhadiran</h2>

        <!-- Tabs Section -->
        <div class="flex items-center gap-8 border-b border-outline-variant/15 mb-8 overflow-x-auto pb-1">
            <button type="button" onclick="alert('Filter tab approval belum diaktifkan.')" class="pb-4 text-sm font-semibold text-primary border-b-2 border-primary whitespace-nowrap">
                Semua
            </button>
            <button type="button" onclick="alert('Filter tab approval belum diaktifkan.')" class="pb-4 text-sm font-medium text-slate-500 hover:text-primary transition-colors flex items-center gap-2 whitespace-nowrap">
                Pending
                <span class="bg-primary-container text-white px-2 py-0.5 rounded-full text-[10px] font-bold">12</span>
            </button>
            <button type="button" onclick="alert('Filter tab approval belum diaktifkan.')" class="pb-4 text-sm font-medium text-slate-500 hover:text-primary transition-colors whitespace-nowrap">
                Disetujui
            </button>
            <button type="button" onclick="alert('Filter tab approval belum diaktifkan.')" class="pb-4 text-sm font-medium text-slate-500 hover:text-primary transition-colors whitespace-nowrap">
                Ditolak
            </button>
        </div>

        <!-- Table Container -->
        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/10 overflow-hidden shadow-sm">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Siswa</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Jenis</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr class="hover:bg-primary/5 transition-colors bg-primary/5 border-l-4 border-l-primary">
                        <td class="px-6 py-4 text-sm text-slate-600">01</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center text-primary font-bold text-xs">RR</div>
                                <div>
                                    <p class="text-sm font-bold text-on-surface">Rizki Ramadhan</p>
                                    <p class="text-[10px] text-slate-500">XII IPA 1</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 bg-amber-50 text-amber-700 text-[10px] font-bold rounded-full border border-amber-100 uppercase">Sakit</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">12 Mar 2024</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button type="button" onclick="alert('Aksi setujui belum diaktifkan pada mode preview.')" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg">
                                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                                </button>
                                <button type="button" onclick="alert('Aksi tolak belum diaktifkan pada mode preview.')" class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg">
                                    <span class="material-symbols-outlined text-[18px]">cancel</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <!-- More rows... -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right Side Preview Panel -->
    <aside class="w-96 bg-white border-l border-slate-100 p-6 overflow-y-auto hidden xl:block shadow-[-4px_0_12px_rgba(0,0,0,0.02)]">
        <h3 class="font-headline font-bold text-lg mb-8">Detail Pengajuan</h3>
        
        <div class="flex flex-col items-center mb-8 text-center">
            <div class="relative mb-4">
                <img alt="Detail Avatar" class="w-24 h-24 rounded-full object-cover ring-4 ring-primary/10" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDaD3d_yRLQmJhU9YgacSCCRcRG_8Y3uYfscHFoxdXF__2MXQqcs2oMbVvH60j9m2CtTLqxCtBWNIY9-_weuXdn9hKH96bWJmuGDDTAbEAi_uqXoqKaXkPru7p5Uxu8eIRM1ArWYfgqngWW-cVTffFLt-BqCGe_UTdaOcBmxo66YyZ05Q_KvVddNiehIWM3Ni91KgrSjDds-q8QqIvhMwz-_4ea0YA1N9YSt5PioCsRvoBLl9Dku1gEL9aNwS57I-XsEv2o-HiiPd8o"/>
                <span class="absolute bottom-1 right-1 bg-amber-500 border-2 border-white w-6 h-6 rounded-full flex items-center justify-center text-white">
                    <span class="material-symbols-outlined text-[14px]">medical_services</span>
                </span>
            </div>
            <h4 class="text-xl font-bold text-on-surface">Rizki Ramadhan</h4>
            <p class="text-sm font-medium text-slate-500">Kelas XII - IPA 1</p>
            <div class="mt-4 flex items-center gap-2">
                <span class="px-4 py-1.5 bg-amber-50 text-amber-700 text-xs font-bold rounded-full border border-amber-100 uppercase">Sakit</span>
                <span class="px-4 py-1.5 bg-slate-100 text-slate-600 text-xs font-bold rounded-full uppercase">Pending</span>
            </div>
        </div>

        <div class="space-y-6">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Alasan</p>
                <div class="bg-slate-50 p-4 rounded-xl text-sm leading-relaxed text-slate-600 border border-slate-100 italic">
                    "Demam tinggi disertai flu berat sejak malam hari. Surat dokter terlampir. Memerlukan istirahat selama 3 hari."
                </div>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Dokumen Pendukung</p>
                <div class="group relative aspect-[4/3] rounded-xl overflow-hidden cursor-pointer border-2 border-dashed border-slate-200 bg-slate-50 flex items-center justify-center hover:border-primary/40 transition-all">
                    <span class="material-symbols-outlined text-slate-300 text-4xl group-hover:text-primary/40 transition-colors">description</span>
                    <div class="absolute inset-0 bg-primary/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white">
                        <span class="material-symbols-outlined">zoom_in</span>
                    </div>
                </div>
                <p class="text-[10px] text-center mt-2 text-slate-400 italic">Surat_Dokter_Rizki.pdf</p>
            </div>
            
            <div class="pt-6 grid grid-cols-2 gap-3">
                <button type="button" onclick="alert('Aksi tolak belum diaktifkan pada mode preview.')" class="py-3 px-4 bg-rose-50 text-rose-600 border border-rose-100 rounded-xl font-bold text-xs uppercase hover:bg-rose-600 hover:text-white transition-all">
                    Tolak
                </button>
                <button type="button" onclick="alert('Aksi setujui belum diaktifkan pada mode preview.')" class="py-3 px-4 bg-primary text-white rounded-xl font-bold text-xs uppercase shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all">
                    Setujui
                </button>
            </div>
        </div>
    </aside>
</div>
@endsection
