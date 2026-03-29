<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>{{ $title ?? 'Student Portal' }} - ScanHadir</title>
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
                        "on-primary-container": "#d0cdff",
                        "secondary": "#4648d4",
                        "secondary-container": "#6063ee",
                        "surface": "#f8f9ff",
                        "surface-bright": "#f8f9ff",
                        "surface-container": "#e6eeff",
                        "surface-container-high": "#dce9ff",
                        "surface-container-highest": "#d5e3fc",
                        "surface-container-low": "#eff4ff",
                        "surface-container-lowest": "#ffffff",
                        "surface-dim": "#ccdbf3",
                        "on-surface": "#0d1c2e",
                        "on-surface-variant": "#454652",
                        "background": "#f8f9ff",
                        "on-background": "#0d1c2e",
                        "outline": "#757684",
                        "outline-variant": "#c5c5d4",
                        "error": "#ba1a1a",
                        "error-container": "#ffdad6",
                    },
                    fontFamily: {
                        "headline": ["Manrope", "sans-serif"],
                        "body": ["Inter", "sans-serif"],
                        "label": ["Inter", "sans-serif"]
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
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
    </style>
</head>
<body class="bg-surface font-body text-on-surface antialiased overflow-x-hidden" x-data="{ sidebarCollapsed: false }">
    <!-- SideNavBar -->
    <aside class="hidden md:flex flex-col h-screen fixed left-0 top-0 bg-white dark:bg-slate-900 border-r-0 rounded-r-[32px] shadow-2xl shadow-slate-200/50 dark:shadow-none z-50 py-8 gap-2 transition-all duration-300 overflow-hidden" :class="sidebarCollapsed ? 'w-20' : 'w-72'">
        <div class="mb-10 flex flex-col items-center gap-6" :class="sidebarCollapsed ? 'px-2' : 'px-6 items-stretch'">
            <div class="flex items-center justify-between w-full" :class="sidebarCollapsed ? 'flex-col gap-4' : ''">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary to-primary-container flex-shrink-0 flex items-center justify-center text-white">
                        <span class="material-symbols-outlined text-2xl">qr_code_2</span>
                    </div>
                    <div x-show="!sidebarCollapsed" x-transition.opacity class="flex-1">
                        <h1 class="text-xl font-extrabold text-indigo-700 dark:text-indigo-400 font-headline leading-tight whitespace-nowrap">ScanHadir</h1>
                        <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold whitespace-nowrap">Student Portal</p>
                    </div>
                </div>
                <button @click="sidebarCollapsed = !sidebarCollapsed" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg text-slate-400 transition-colors hidden md:block" :class="sidebarCollapsed ? 'mx-auto' : ''" title="Toggle Sidebar">
                    <span class="material-symbols-outlined text-[20px]" x-text="sidebarCollapsed ? 'dock_to_right' : 'dock_to_left'">dock_to_left</span>
                </button>
            </div>
        </div>
        <nav class="flex-1 flex flex-col gap-2">
            <a class="flex items-center gap-4 {{ request()->routeIs('student.dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-500' }} rounded-2xl px-6 py-4 mx-4 transition-all" href="{{ route('student.dashboard') }}">
                <span class="material-symbols-outlined">dashboard</span>
                <span class="font-medium text-sm whitespace-nowrap" x-show="!sidebarCollapsed">Overview</span>
            </a>
            <a class="flex items-center gap-4 {{ request()->routeIs('student.izin') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-500' }} rounded-2xl px-6 py-4 mx-4 transition-all" href="{{ route('student.izin') }}">
                <span class="material-symbols-outlined">history</span>
                <span class="font-medium text-sm whitespace-nowrap" x-show="!sidebarCollapsed">Riwayat Absensi</span>
            </a>
            <a class="flex items-center gap-4 {{ request()->routeIs('student.profil') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-500' }} rounded-2xl px-6 py-4 mx-4 transition-all" href="{{ route('student.profil') }}">
                <span class="material-symbols-outlined">person</span>
                <span class="font-medium text-sm whitespace-nowrap" x-show="!sidebarCollapsed">Profil</span>
            </a>
            <a class="flex items-center gap-4 {{ request()->routeIs('student.manual') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-500' }} rounded-2xl px-6 py-4 mx-4 transition-all" href="{{ route('student.manual') }}">
                <span class="material-symbols-outlined">edit_calendar</span>
                <span class="font-medium text-sm whitespace-nowrap" x-show="!sidebarCollapsed">Absensi Manual</span>
            </a>
            <a class="flex items-center gap-4 text-slate-500 px-6 py-4 mx-4 hover:bg-slate-100 rounded-2xl transition-all" href="#">
                <span class="material-symbols-outlined">help</span>
                <span class="font-medium text-sm whitespace-nowrap" x-show="!sidebarCollapsed">Bantuan</span>
            </a>
        </nav>
        <div class="px-4 mt-auto">
            <a href="{{ route('student.manual') }}" class="w-full bg-gradient-to-r from-primary to-primary-container text-white py-4 rounded-2xl font-semibold shadow-lg shadow-primary/20 flex items-center justify-center gap-2 hover:scale-[0.98] transition-transform overflow-hidden px-2">
                <span class="material-symbols-outlined text-xl flex-shrink-0">camera</span>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Check In Now</span>
            </a>
        </div>
    </aside>

    <!-- Main Content Canvas -->
    <main class="min-h-screen relative transition-all duration-300" :class="sidebarCollapsed ? 'md:ml-20' : 'md:ml-72'">
        <!-- TopAppBar -->
        <header class="w-full sticky top-0 z-40 flex justify-between items-center px-8 py-4 bg-white/80 backdrop-blur-md border-b border-slate-100 shadow-sm transition-all duration-300">
            <div class="flex flex-col">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-[0.2em] font-headline">
                        @if(Request::is('student/dashboard'))
                            STUDENT PORTAL / OVERVIEW
                        @elseif(Request::is('student/izin'))
                            STUDENT PORTAL / ATTENDANCE HISTORY
                        @elseif(Request::is('student/profil'))
                            STUDENT PORTAL / PROFILE
                        @elseif(Request::is('student/manual-attendance'))
                            STUDENT PORTAL / MANUAL CHECK-IN
                        @else
                            STUDENT PORTAL
                        @endif
                    </span>
                </div>
                <h2 class="text-2xl font-extrabold text-indigo-700 font-headline tracking-tight">{{ $title ?? 'Student Portal' }}</h2>
            </div>

            <div class="flex items-center gap-6">
                <!-- Modern Search Bar Placeholder -->
                <div class="hidden lg:flex items-center gap-3 px-4 py-2 bg-slate-100/50 border border-slate-200/50 rounded-2xl w-64 focus-within:w-80 focus-within:bg-white focus-within:border-primary/20 transition-all duration-300 group shadow-inner">
                    <span class="material-symbols-outlined text-slate-400 text-xl group-focus-within:text-primary transition-colors">search</span>
                    <input type="text" placeholder="Cari riwayat atau info..." class="bg-transparent border-none text-sm outline-none w-full font-medium placeholder:text-slate-400 focus:ring-0 shadow-none"/>
                </div>

                <div class="flex items-center gap-4">
                    <button class="w-10 h-10 flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-primary rounded-xl transition-all relative">
                        <span class="material-symbols-outlined">notifications</span>
                        <span class="absolute top-2 right-2 w-2 h-2 bg-error rounded-full border-2 border-white"></span>
                    </button>
                    
                    <div class="h-8 w-px bg-slate-200"></div>

                    <div class="flex items-center gap-3 pl-2">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-bold text-on-surface leading-tight">{{ $student_name ?? 'Student' }}</p>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Student • {{ $class ?? 'Active' }}</p>
                        </div>
                        <img alt="Student Profile Avatar" class="w-10 h-10 rounded-xl object-cover ring-2 ring-primary/10 shadow-sm" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDXlLwOeOVe1zP4wmCSIX50RAmUHP7v4fQHocUPNZdY7oXGTGWDPz2JF3In0KqPsW64TrnSyUSmcZS9Clpkik0PbaWJZ33m2hU3MvthipH1s2JVPfEgbyfYGcINArLmx0NtvT9laJSwm4TlEeHAm0ryhnfx4T09PCc58ZOHr2k1nIRVaLb34ADnoAuDB_cAJfhQ3J5bfxmJU5FNYJ3fGVHP99qszRckuTK_gh183GYy413aBjeI8tL308p0oanuid1FrKUJ0bguz8wS"/>
                    </div>

                    <form action="{{ route('auth.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-10 h-10 flex items-center justify-center text-slate-400 hover:bg-rose-50 hover:text-rose-500 rounded-xl transition-all" title="Logout">
                            <span class="material-symbols-outlined">logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Main Body -->
        <div class="p-8">
            @if (isset($slot))
                {{ $slot }}
            @else
                @yield('content')
            @endif
        </div>

        <!-- Decorative Background Element -->
        <div class="fixed -bottom-32 -right-32 w-[600px] h-[600px] bg-primary/5 rounded-full blur-[100px] pointer-events-none -z-10"></div>
    </main>
</body>
</html>
