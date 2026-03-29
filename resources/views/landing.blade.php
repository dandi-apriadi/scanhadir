@extends('layouts.guest')

@section('content')
<!-- TopNavBar Implementation -->
<nav class="fixed top-0 w-full z-50 bg-white/60 backdrop-blur-xl shadow-sm">
    <div class="flex justify-between items-center max-w-7xl mx-auto px-8 py-4">
        <div class="text-2xl font-extrabold tracking-tighter text-indigo-700 font-headline">
            ScanHadir
        </div>
        <div class="hidden md:flex items-center space-x-8">
            <a class="text-sm font-semibold text-indigo-700 border-b-2 border-indigo-600 font-label" href="#">Home</a>
            <a class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-all duration-300 font-label" href="#">Features</a>
            <a class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-all duration-300 font-label" href="#">About</a>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('login.student') }}" class="text-indigo-600 font-bold text-sm hover:underline">Student Portal</a>
            <a href="{{ route('login.admin') }}" class="bg-gradient-to-br from-primary to-primary-container text-on-primary px-6 py-2.5 rounded-full font-bold text-sm hover:scale-95 transition-all duration-200 ease-in-out shadow-lg shadow-primary/20">
                Admin Console
            </a>
        </div>
    </div>
</nav>

<main class="pt-20">
    <!-- Hero Section -->
    <section class="relative min-h-[80vh] flex items-center overflow-hidden mesh-gradient-bg">
        <div class="max-w-7xl mx-auto px-8 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center relative z-10">
            <div class="space-y-8">
                <div class="inline-flex items-center px-4 py-1.5 rounded-full bg-primary/10 border border-primary/20 text-primary font-label text-xs font-bold tracking-widest uppercase">
                    Digital Pulse for Precision
                </div>
                <h1 class="text-7xl md:text-8xl font-bold font-headline tracking-tighter text-on-surface leading-[0.9]">
                    ScanHadir
                </h1>
                <p class="text-2xl md:text-3xl font-medium text-on-surface-variant font-body leading-relaxed max-w-xl">
                    Solusi Presensi QR Modern untuk ekosistem pendidikan cerdas.
                </p>
                <div class="flex flex-wrap gap-4 pt-4">
                    <a href="{{ route('login.student') }}" class="bg-primary text-on-primary px-8 py-4 rounded-xl font-bold text-lg hover:shadow-2xl hover:shadow-primary/40 transition-all">
                        Mulai Sekarang
                    </a>
                    <button class="flex items-center gap-2 px-8 py-4 rounded-xl font-bold text-lg text-primary hover:bg-surface-container transition-all">
                        <span class="material-symbols-outlined">play_circle</span>
                        Lihat Demo
                    </button>
                </div>
            </div>
            <div class="relative">
                <div class="absolute -top-20 -right-20 w-96 h-96 bg-primary/20 rounded-full blur-[100px]"></div>
                <div class="relative z-10 rounded-[2.5rem] overflow-hidden shadow-2xl shadow-primary/10 border border-white/50">
                    <img alt="Dashboard Analytics" class="w-full h-full object-cover" src="{{ asset('images/hero.png') }}"/>
                </div>
            </div>
        </div>
    </section>

    <!-- Feature Highlights -->
    <section class="py-32 bg-surface">
        <div class="max-w-7xl mx-auto px-8">
            <div class="mb-16">
                <h2 class="text-4xl font-bold font-headline text-on-surface mb-4">Efisiensi Tanpa Kompromi</h2>
                <p class="text-on-surface-variant text-lg max-w-2xl font-body">Dirancang untuk kecepatan dan akurasi tinggi dalam pencatatan kehadiran harian.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="group p-8 rounded-3xl bg-surface-container-low hover:bg-surface-container-high transition-all duration-500 border border-outline-variant/10">
                    <div class="w-14 h-14 rounded-2xl bg-primary-container flex items-center justify-center text-white mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-3xl">bolt</span>
                    </div>
                    <h3 class="text-xl font-bold font-headline mb-4 text-on-surface">Pemindaian Kilat</h3>
                    <p class="text-on-surface-variant leading-relaxed">Teknologi pemindaian QR generasi terbaru yang mampu mengenali kode dalam waktu kurang dari 0.5 detik.</p>
                </div>
                <div class="group p-8 rounded-3xl bg-surface-container-lowest shadow-sm hover:shadow-xl transition-all duration-500 border border-outline-variant/20">
                    <div class="w-14 h-14 rounded-2xl bg-secondary-container flex items-center justify-center text-white mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-3xl">shield_person</span>
                    </div>
                    <h3 class="text-xl font-bold font-headline mb-4 text-on-surface">Anti-Fraud</h3>
                    <p class="text-on-surface-variant leading-relaxed">Validasi berbasis lokasi dan enkripsi dinamis untuk memastikan data kehadiran yang valid dan tidak dapat dimanipulasi.</p>
                </div>
                <div class="group p-8 rounded-3xl bg-surface-container-low hover:bg-surface-container-high transition-all duration-500 border border-outline-variant/10">
                    <div class="w-14 h-14 rounded-2xl bg-tertiary-container flex items-center justify-center text-white mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-3xl">bar_chart_4_bars</span>
                    </div>
                    <h3 class="text-xl font-bold font-headline mb-4 text-on-surface">Laporan Otomatis</h3>
                    <p class="text-on-surface-variant leading-relaxed">Ekspor laporan kehadiran langsung ke berbagai format dengan analisis data mendalam yang dikirim secara otomatis.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-24 relative overflow-hidden">
        <div class="absolute inset-0 bg-primary/5 -skew-y-3"></div>
        <div class="max-w-7xl mx-auto px-8 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <div class="glass-card p-12 rounded-[2.5rem] border border-white/40 shadow-xl group hover:-translate-y-2 transition-all duration-500">
                    <div class="flex justify-between items-start mb-12">
                        <div>
                            <h4 class="text-sm font-bold text-primary font-label uppercase tracking-widest mb-2">Pihak Siswa</h4>
                            <h2 class="text-3xl font-bold font-headline text-on-surface">Akses Portal Siswa</h2>
                        </div>
                        <span class="material-symbols-outlined text-4xl text-primary/40 group-hover:text-primary transition-colors">school</span>
                    </div>
                    <p class="text-on-surface-variant mb-12 text-lg">Lihat riwayat kehadiran, jadwal pelajaran, dan terima notifikasi langsung di perangkat Anda.</p>
                    <a href="{{ route('login.student') }}" class="block w-full text-center bg-white text-primary border border-primary/20 py-4 rounded-xl font-bold text-lg group-hover:bg-primary group-hover:text-white transition-all">
                        Masuk Portal Siswa
                    </a>
                </div>
                <div class="bg-indigo-900 p-12 rounded-[2.5rem] shadow-2xl group hover:-translate-y-2 transition-all duration-500 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-primary-container/20 rounded-full blur-3xl"></div>
                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-12">
                            <div>
                                <h4 class="text-sm font-bold text-indigo-300 font-label uppercase tracking-widest mb-2">Administrasi</h4>
                                <h2 class="text-3xl font-bold font-headline text-white">Akses Console Admin</h2>
                            </div>
                            <span class="material-symbols-outlined text-4xl text-indigo-300 group-hover:rotate-12 transition-transform">admin_panel_settings</span>
                        </div>
                        <p class="text-indigo-200 mb-12 text-lg">Kelola data seluruh siswa, buat QR code unik, dan pantau statistik kehadiran secara real-time.</p>
                        <a href="{{ route('login.admin') }}" class="block w-full text-center bg-primary-container text-white py-4 rounded-xl font-bold text-lg hover:bg-primary transition-all shadow-lg shadow-black/20">
                            Masuk Console Admin
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<footer class="bg-slate-50 w-full rounded-t-3xl mt-20">
    <div class="max-w-7xl mx-auto px-8 py-12">
        <div class="flex flex-col md:flex-row justify-between gap-8">
            <div>
                <div class="text-xl font-bold text-slate-900 mb-4 font-headline">ScanHadir</div>
                <p class="text-slate-500 text-sm font-body max-w-md mb-6">
                    © {{ date('Y') }} ScanHadir. The Digital Pulse for Precision Attendance. Solusi terintegrasi untuk manajemen kehadiran masa depan.
                </p>
                <div class="flex gap-4">
                    <span class="material-symbols-outlined text-slate-400 cursor-pointer hover:text-primary transition-colors">public</span>
                    <span class="material-symbols-outlined text-slate-400 cursor-pointer hover:text-primary transition-colors">hub</span>
                    <span class="material-symbols-outlined text-slate-400 cursor-pointer hover:text-primary transition-colors">share</span>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-8">
                <div class="space-y-4">
                    <h5 class="text-xs font-semibold uppercase tracking-widest text-indigo-700 font-label">Product</h5>
                    <ul class="space-y-2">
                        <li><a class="text-sm text-slate-500 hover:text-indigo-500 transition-colors" href="#">Privacy Policy</a></li>
                        <li><a class="text-sm text-slate-500 hover:text-indigo-500 transition-colors" href="#">Terms of Service</a></li>
                    </ul>
                </div>
                <div class="space-y-4">
                    <h5 class="text-xs font-semibold uppercase tracking-widest text-indigo-700 font-label">Support</h5>
                    <ul class="space-y-2">
                        <li><a class="text-sm text-slate-500 hover:text-indigo-500 transition-colors" href="#">Support</a></li>
                        <li><a class="text-sm text-slate-500 hover:text-indigo-500 transition-colors" href="#">Contact</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>
@endsection
