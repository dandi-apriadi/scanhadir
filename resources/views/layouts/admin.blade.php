<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>{{ $title ?? 'Admin Console' }} - ScanHadir</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#2f1bc8",
                        "primary-container": "#493fdf",
                        "on-primary": "#ffffff",
                        "secondary": "#4648d4",
                        "surface": "#f8f9ff",
                        "surface-container": "#e6eeff",
                        "surface-container-high": "#dce9ff",
                        "surface-container-highest": "#d5e3fc",
                        "surface-container-low": "#eff4ff",
                        "surface-container-lowest": "#ffffff",
                        "on-surface": "#0d1c2e",
                        "on-surface-variant": "#454652",
                        "background": "#f8f9ff",
                        "outline": "#757684",
                        "error": "#ba1a1a",
                    },
                    fontFamily: {
                        "headline": ["Manrope", "sans-serif"],
                        "body": ["Inter", "sans-serif"],
                    },
                    borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>
<body class="bg-surface font-body text-on-surface antialiased" x-data="{ sidebarCollapsed: false }">
    <aside class="hidden md:flex flex-col h-screen fixed left-0 top-0 bg-indigo-950 text-white z-50 py-8 gap-2 transition-all duration-300 overflow-hidden" :class="sidebarCollapsed ? 'w-20' : 'w-72'">
        <div class="px-6 mb-10 flex items-center justify-between overflow-hidden">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-primary-container flex-shrink-0 flex items-center justify-center text-white">
                    <span class="material-symbols-outlined text-2xl">admin_panel_settings</span>
                </div>
                <div x-show="!sidebarCollapsed" x-transition.opacity>
                    <h1 class="text-xl font-extrabold text-white font-headline leading-tight">ScanHadir</h1>
                    <p class="text-[10px] uppercase tracking-widest text-indigo-300 font-bold">Admin Console</p>
                </div>
            </div>
            <button @click="sidebarCollapsed = !sidebarCollapsed" class="p-1 hover:bg-white/10 rounded-lg text-indigo-300 transition-colors hidden md:block" :class="sidebarCollapsed ? 'mx-auto translate-x-1' : ''">
                <span class="material-symbols-outlined text-[20px]" x-text="sidebarCollapsed ? 'menu_open' : 'menu'">menu</span>
            </button>
        </div>
        <nav class="flex-1 flex flex-col gap-1 px-3">
            <p x-show="!sidebarCollapsed" class="text-[10px] uppercase tracking-[0.2em] text-indigo-400 font-bold mb-2 px-4 whitespace-nowrap">Main Menu</p>
            <a class="flex items-center gap-4 {{ request()->routeIs('admin.dashboard') ? 'bg-primary-container text-white' : 'text-indigo-200 hover:bg-white/10' }} rounded-xl px-4 py-3 transition-all" href="{{ route('admin.dashboard') }}">
                <span class="material-symbols-outlined">dashboard</span>
                <span class="font-medium text-sm whitespace-nowrap" x-show="!sidebarCollapsed">Dashboard</span>
            </a>
            <a class="flex items-center gap-4 {{ request()->routeIs('admin.analytics') ? 'bg-primary-container text-white' : 'text-indigo-200 hover:bg-white/10' }} rounded-xl px-4 py-3 transition-all" href="{{ route('admin.analytics') }}">
                <span class="material-symbols-outlined">analytics</span>
                <span class="font-medium text-sm whitespace-nowrap" x-show="!sidebarCollapsed">Analytics</span>
            </a>
            <a class="flex items-center gap-4 {{ request()->routeIs('admin.logs') ? 'bg-primary-container text-white' : 'text-indigo-200 hover:bg-white/10' }} rounded-xl px-4 py-3 transition-all" href="{{ route('admin.logs') }}">
                <span class="material-symbols-outlined">history</span>
                <span class="font-medium text-sm whitespace-nowrap" x-show="!sidebarCollapsed">Presensi Logs</span>
            </a>
            <a class="flex items-center gap-4 {{ request()->routeIs('admin.izin_approval') ? 'bg-primary-container text-white' : 'text-indigo-200 hover:bg-white/10' }} rounded-xl px-4 py-3 transition-all" href="{{ route('admin.izin_approval') }}">
                <span class="material-symbols-outlined">assignment_turned_in</span>
                <span class="font-medium text-sm whitespace-nowrap" x-show="!sidebarCollapsed">Izin Approval</span>
            </a>

            <p x-show="!sidebarCollapsed" class="text-[10px] uppercase tracking-[0.2em] text-indigo-400 font-bold mt-6 mb-2 px-4 whitespace-nowrap">Master Data</p>
            <a class="flex items-center gap-4 {{ request()->routeIs('admin.master.guru') ? 'bg-primary-container text-white' : 'text-indigo-200 hover:bg-white/10' }} rounded-xl px-4 py-3 transition-all" href="{{ route('admin.master.guru') }}">
                <span class="material-symbols-outlined">record_voice_over</span>
                <span class="font-medium text-sm whitespace-nowrap" x-show="!sidebarCollapsed">Data Guru</span>
            </a>
            <a class="flex items-center gap-4 {{ request()->routeIs('admin.master.siswa') ? 'bg-primary-container text-white' : 'text-indigo-200 hover:bg-white/10' }} rounded-xl px-4 py-3 transition-all" href="{{ route('admin.master.siswa') }}">
                <span class="material-symbols-outlined">group</span>
                <span class="font-medium text-sm whitespace-nowrap" x-show="!sidebarCollapsed">Data Siswa</span>
            </a>
            <a class="flex items-center gap-4 {{ request()->routeIs('admin.master.kelas') ? 'bg-primary-container text-white' : 'text-indigo-200 hover:bg-white/10' }} rounded-xl px-4 py-3 transition-all" href="{{ route('admin.master.kelas') }}">
                <span class="material-symbols-outlined">meeting_room</span>
                <span class="font-medium text-sm whitespace-nowrap" x-show="!sidebarCollapsed">Data Kelas</span>
            </a>
            <a class="flex items-center gap-4 {{ request()->routeIs('admin.master.mapel') ? 'bg-primary-container text-white' : 'text-indigo-200 hover:bg-white/10' }} rounded-xl px-4 py-3 transition-all" href="{{ route('admin.master.mapel') }}">
                <span class="material-symbols-outlined">library_books</span>
                <span class="font-medium text-sm whitespace-nowrap" x-show="!sidebarCollapsed">Mata Pelajaran</span>
            </a>

            <p x-show="!sidebarCollapsed" class="text-[10px] uppercase tracking-[0.2em] text-indigo-400 font-bold mt-6 mb-2 px-4 whitespace-nowrap">System</p>
            <a class="flex items-center gap-4 {{ request()->routeIs('admin.settings') ? 'bg-primary-container text-white' : 'text-indigo-200 hover:bg-white/10' }} rounded-xl px-4 py-3 transition-all" href="{{ route('admin.settings') }}">
                <span class="material-symbols-outlined">settings</span>
                <span class="font-medium text-sm whitespace-nowrap" x-show="!sidebarCollapsed">Settings</span>
            </a>
        </nav>
    </aside>

    <main class="min-h-screen transition-all duration-300" :class="sidebarCollapsed ? 'md:ml-20' : 'md:ml-72'">
        <header class="w-full sticky top-0 z-40 flex justify-between items-center px-8 py-4 bg-white shadow-sm">
            <div class="flex flex-col">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Admin Console</span>
                <h2 class="text-xl font-bold text-indigo-900 font-headline">{{ $title ?? 'Dashboard' }}</h2>
            </div>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-3 pr-2">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-bold text-on-surface">Super Admin</p>
                        <p class="text-[10px] text-slate-400 font-medium">Administrator</p>
                    </div>
                </div>
                <a href="{{ route('landing') }}" class="p-2 text-slate-500 hover:bg-slate-100 transition-colors rounded-full">
                    <span class="material-symbols-outlined">logout</span>
                </a>
            </div>
        </header>

        <div class="p-8">
            @yield('content')
        </div>
    </main>
</body>
</html>
