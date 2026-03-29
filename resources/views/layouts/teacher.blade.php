<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'ScanHadir Teacher' }}</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2f1bc8',
                        'primary-container': '#493fdf',
                        'on-primary': '#ffffff',
                        surface: '#f8f9ff',
                        'on-surface': '#0d1c2e',
                        'surface-container-low': '#eff4ff',
                        'surface-container-highest': '#d5e3fc',
                    },
                    fontFamily: {
                        headline: ['Manrope', 'sans-serif'],
                        body: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>
<body class="bg-surface text-on-surface font-body antialiased" x-data="{ sidebarCollapsed: false }">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="bg-white border-r border-slate-100 flex flex-col fixed h-screen z-50 transition-all duration-300 overflow-hidden" :class="sidebarCollapsed ? 'w-20' : 'w-64'">
            <div class="mb-2 flex flex-col items-center gap-4" :class="sidebarCollapsed ? 'p-2' : 'p-4 items-stretch'">
                <div class="flex items-center justify-between w-full" :class="sidebarCollapsed ? 'flex-col gap-4' : ''">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-primary flex-shrink-0 rounded-xl flex items-center justify-center text-white shadow-lg shadow-primary/20">
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">qr_code_scanner</span>
                        </div>
                        <div x-show="!sidebarCollapsed" x-transition.opacity class="flex-1">
                            <h1 class="text-xl font-bold text-indigo-700 font-headline whitespace-nowrap">ScanHadir</h1>
                            <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold whitespace-nowrap">Teacher Portal</p>
                        </div>
                    </div>
                    <button @click="sidebarCollapsed = !sidebarCollapsed" class="p-2 hover:bg-slate-50 rounded-lg text-slate-400 transition-colors hidden md:block" :class="sidebarCollapsed ? 'mx-auto' : ''" title="Toggle Sidebar">
                        <span class="material-symbols-outlined text-[20px]" x-text="sidebarCollapsed ? 'menu_open' : 'menu'">menu</span>
                    </button>
                </div>
            </div>

            <nav class="flex-1 px-3 space-y-1 mt-4">
                <a href="{{ route('teacher.dashboard') }}" class="flex items-center gap-3 px-4 py-3 {{ Request::routeIs('teacher.dashboard') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-600 hover:bg-slate-50' }} rounded-xl transition-all">
                    <span class="material-symbols-outlined">dashboard</span>
                    <span class="text-xs uppercase tracking-wider font-semibold whitespace-nowrap" x-show="!sidebarCollapsed">Dashboard</span>
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-50 rounded-xl transition-all">
                    <span class="material-symbols-outlined">calendar_today</span>
                    <span class="text-xs uppercase tracking-wider font-semibold whitespace-nowrap" x-show="!sidebarCollapsed">Jadwal</span>
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-50 rounded-xl transition-all">
                    <span class="material-symbols-outlined">group</span>
                    <span class="text-xs uppercase tracking-wider font-semibold whitespace-nowrap" x-show="!sidebarCollapsed">Siswa</span>
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-50 rounded-xl transition-all">
                    <span class="material-symbols-outlined">analytics</span>
                    <span class="text-xs uppercase tracking-wider font-semibold whitespace-nowrap" x-show="!sidebarCollapsed">Laporan</span>
                </a>
            </nav>

            <div class="p-3 border-t border-slate-50">
                <div class="p-3 bg-slate-50 rounded-2xl flex items-center gap-3 overflow-hidden">
                    <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=teacher" alt="Teacher" class="w-10 h-10 flex-shrink-0 rounded-full bg-white">
                    <div class="overflow-hidden" x-show="!sidebarCollapsed">
                        <p class="text-sm font-bold truncate">Budi Santoso, S.Pd</p>
                        <p class="text-[10px] text-slate-500 uppercase font-bold">Senior Teacher</p>
                    </div>
                </div>
                <a href="{{ route('landing') }}" class="w-full mt-4 flex items-center justify-center gap-2 text-slate-400 hover:text-error py-2 text-sm font-bold transition-colors">
                    <span class="material-symbols-outlined text-sm flex-shrink-0">logout</span>
                    <span x-show="!sidebarCollapsed">LOGOUT</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 min-h-screen transition-all duration-300" :class="sidebarCollapsed ? 'md:ml-20' : 'md:ml-64'">
            <!-- Header -->
            <header class="h-16 border-b border-slate-100 bg-white/80 backdrop-blur-md sticky top-0 z-40 px-8 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                            <span class="material-symbols-outlined text-xl">search</span>
                        </span>
                        <input type="text" class="bg-slate-50 border-none rounded-full py-2 pl-10 pr-4 w-64 text-sm focus:ring-2 focus:ring-primary/20 transition-all font-medium" placeholder="Cari data...">
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <button class="p-2 rounded-full text-slate-400 hover:bg-slate-50 transition-colors relative">
                        <span class="material-symbols-outlined">notifications</span>
                        <span class="absolute top-2 right-2 w-2 h-2 bg-error rounded-full border-2 border-white"></span>
                    </button>
                    <button class="p-2 rounded-full text-slate-400 hover:bg-slate-50 transition-colors">
                        <span class="material-symbols-outlined">settings</span>
                    </button>
                </div>
            </header>

            <!-- Content Area -->
            <div class="p-8">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
