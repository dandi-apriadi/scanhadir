@extends('layouts.student')

@section('content')
<div class="space-y-8">
    <!-- Header & Breadcrumbs -->
    <div class="mb-8">
        <nav class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-2 uppercase tracking-wider">
            <a class="hover:text-primary transition-colors" href="{{ route('student.dashboard') }}">Dashboard</a>
            <span class="material-symbols-outlined text-xs">chevron_right</span>
            <span class="text-primary-container">Detail Profil</span>
        </nav>
        <h2 class="text-3xl font-extrabold text-on-surface tracking-tight">Detail Profil</h2>
    </div>

    <div class="grid grid-cols-12 gap-8">
        <!-- Left Column: Profile Card -->
        <div class="col-span-12 lg:col-span-4 space-y-6">
            <div class="bg-surface-container-lowest rounded-3xl p-8 border border-outline-variant/10 shadow-sm relative overflow-hidden">
                <div class="flex flex-col items-center text-center relative z-10">
                    <div class="relative mb-6">
                        <div class="w-40 h-40 rounded-full p-1 bg-gradient-to-tr from-primary to-secondary shadow-xl">
                            <img alt="{{ $student_name }}" class="w-full h-full rounded-full border-4 border-surface-container-lowest object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuD_xd_cM4evy4bhs1xuXiXHx8Nli5c-DGmDABrdyGD9JzNRof6JHG_yP7NUki5b_cUxn9TvtrLqaEVzJBEcLWUL_QFC4EMAlXYrMxusWUJK7WX4gQf8vjZyzLnekheufCGujvSH4ihCLCCeUHJlONGRP5lq6c5UUYYo_ty0q6aCP_stlRlAvaJmSyQtabaK-n-kvMTuh3yTTiN6-5tRSlK6oPhrVF2M9ekM782d1beuDFocHzqVFStqCP1wlKGTNezUsCchXLO58_wA"/>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-on-surface mb-1">{{ $student_name }}</h3>
                    <p class="text-indigo-600 font-semibold text-sm mb-4">Siswa - {{ $class }}</p>
                    <div class="inline-flex items-center gap-2 bg-surface-container-low px-4 py-1.5 rounded-full border border-primary/10 mb-8">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">NISN</span>
                        <span class="text-sm font-bold text-on-surface">{{ $nisn }}</span>
                    </div>
                    
                    <div class="w-full bg-surface-container rounded-2xl p-6 flex flex-col items-center border border-indigo-100/50">
                        <p class="text-[10px] font-bold text-indigo-700/60 uppercase tracking-widest mb-4">Student Digital ID</p>
                        <div class="bg-white p-4 rounded-xl shadow-inner mb-6">
                            <img alt="QR Code" class="w-32 h-32" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAS0JAVO5rC9CQx4N4MvXwKonGxEsZGfAdjgd8nqOb2vuqztPL7YLkV_sJmDLe9tAMiJ0p2aS-genWjl_kQ9-Ygw8Ws4Byuc7wlL6oa5EuHvCVfUOMkjQBoAYf_JoLY5LPE8ymql1WbM9dn39YDry9HxaJvJSPQYpMfDCysZTmenu3myPCd3Ea4GUN1iOcNtlebUS7TLOjc23epzcnG-JDO_CbCjkQxEvBBBWmtawkr98uMTiCrE6J66adD80vAWx6PXY880kXjIVn7"/>
                        </div>
                        <a href="{{ route('students.qrcode.download', ['nisn' => $nisn, 'filename' => 'qr-siswa-'.$nisn], false) }}" download="qr-siswa-{{ $nisn }}.png" class="w-full py-3 bg-gradient-to-r from-primary to-primary-container text-white font-bold text-xs uppercase tracking-widest rounded-xl hover:shadow-lg hover:shadow-primary/20 transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-lg">download</span>
                            Download QR
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Information -->
        <div class="col-span-12 lg:col-span-8 space-y-8">
            <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/10 shadow-sm overflow-hidden">
                <div class="px-8 py-5 border-b border-slate-50 flex items-center justify-between">
                    <h4 class="text-lg font-bold text-on-surface">Informasi Pribadi</h4>
                    <button type="button" onclick="showToast('Fitur edit profil belum diaktifkan.')" class="text-primary text-xs font-bold uppercase tracking-widest flex items-center gap-1 hover:bg-indigo-50 px-3 py-1.5 rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-sm">edit</span>
                        Edit Data
                    </button>
                </div>
                <div class="p-8 grid grid-cols-2 gap-y-8 gap-x-12">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Nama Lengkap</p>
                        <p class="text-sm font-semibold text-on-surface">{{ $student_name }} Saputra</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Tempat, Tanggal Lahir</p>
                        <p class="text-sm font-semibold text-on-surface">Jakarta, 12 Maret 2006</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Jenis Kelamin</p>
                        <p class="text-sm font-semibold text-on-surface">Laki-laki</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Agama</p>
                        <p class="text-sm font-semibold text-on-surface">Islam</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Alamat Lengkap</p>
                        <p class="text-sm font-semibold text-on-surface leading-relaxed">Jl. Kebon Jeruk No. 45, RT 002/004, Kel. Palmerah, Kec. Palmerah, Jakarta Barat, 11480</p>
                    </div>
                </div>
            </div>

            <!-- Stats Chart Mockup -->
            <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/10 shadow-sm overflow-hidden">
                <div class="px-8 py-5 border-b border-slate-50">
                    <h4 class="text-lg font-bold text-on-surface">Tren Kehadiran</h4>
                    <p class="text-xs text-slate-400 font-medium">Data 30 hari terakhir</p>
                </div>
                <div class="p-8">
                    <div class="h-32 flex items-end justify-between gap-1">
                        @for($i=0; $i<30; $i++)
                        <div class="flex-1 bg-primary rounded-t-sm h-[{{ rand(40, 100) }}%] opacity-{{ rand(20, 100) }}"></div>
                        @endfor
                    </div>
                    <div class="mt-4 flex justify-between text-[10px] font-bold text-slate-400 uppercase tracking-tighter">
                        <span>01 Mar</span>
                        <span>15 Mar</span>
                        <span>30 Mar</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
