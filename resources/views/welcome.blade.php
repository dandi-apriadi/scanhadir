<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ScanHadir - Solusi Presensi Modern</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo.jpeg') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-white selection:bg-indigo-500 selection:text-white">
    <div class="relative min-h-screen flex flex-col items-center justify-center overflow-hidden py-20 px-4">
        <!-- Glows -->
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-indigo-600/30 rounded-full blur-[120px] -z-10 animate-pulse"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-fuchsia-600/20 rounded-full blur-[120px] -z-10 animate-pulse delay-1000"></div>

        <div class="max-w-4xl w-full text-center z-10">
            <x-app-logo size="w-24 h-24" class="mx-auto mb-8 ring-4 ring-white/10 shadow-2xl shadow-indigo-500/20" />
            <div class="inline-flex items-center px-4 py-2 bg-indigo-500/10 border border-indigo-500/20 rounded-full text-indigo-400 text-sm font-semibold mb-8 backdrop-blur-sm animate-bounce">
                🚀 Versi 1.0 Baru Saja Rilis
            </div>
            
            <h1 class="text-6xl md:text-8xl font-black tracking-tighter mb-8 bg-gradient-to-br from-white via-white to-slate-500 bg-clip-text text-transparent italic">
                ScanHadir.
            </h1>
            
            <p class="text-xl md:text-2xl text-slate-400 max-w-2xl mx-auto mb-12 leading-relaxed font-light">
                Platform management presensi sekolah berbasis <span class="text-white font-medium italic underline decoration-indigo-500 decoration-2 underline-offset-4">QR Code</span> yang profesional, cepat, dan modern.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
                <a href="/login" class="px-8 py-4 bg-white text-slate-950 font-bold rounded-2xl hover:scale-105 transition-all shadow-xl shadow-white/10 flex items-center group">
                    Portal Siswa
                    <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
                <a href="/admin" class="px-8 py-4 bg-slate-900 text-white font-bold border border-slate-800 rounded-2xl hover:bg-slate-800 transition-all">
                    Dashboard Admin
                </a>
                <a href="/scan" target="_blank" class="px-8 py-4 bg-indigo-600 text-white font-bold rounded-2xl hover:bg-indigo-500 transition-all shadow-xl shadow-indigo-600/20">
                    Buka Scanner
                </a>
            </div>
        </div>

        <footer class="absolute bottom-8 text-slate-600 text-sm">
            &copy; {{ date('Y') }} ScanHadir Tech. All rights reserved.
        </footer>
    </div>
</body>
</html>
