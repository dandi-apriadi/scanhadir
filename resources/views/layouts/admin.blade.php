<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>{{ $title ?? 'Admin Console' }} - ScanHadir</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    @livewireStyles
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
        [x-cloak] { display: none !important; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: rgba(129, 140, 248, 0.55) transparent;
            scrollbar-gutter: stable;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: rgba(129, 140, 248, 0.55);
            border-radius: 9999px;
            border: 2px solid transparent;
            background-clip: content-box;
        }

        .custom-scrollbar::-webkit-scrollbar-button {
            display: none;
            width: 0;
            height: 0;
        }
    </style>
</head>
<body class="bg-surface font-body text-on-surface antialiased" x-data="{ sidebarCollapsed: false, activeGroups: { main: true, master: true, system: true } }">
    <aside class="hidden md:flex flex-col h-screen fixed left-0 top-0 bg-indigo-950 text-white z-50 py-8 gap-2 transition-all duration-300 overflow-hidden" :class="sidebarCollapsed ? 'w-20' : 'w-72'">
        <div class="mb-10 flex flex-col items-center gap-6" :class="sidebarCollapsed ? 'px-2' : 'px-6 items-stretch'">
            <div class="flex items-center justify-between w-full" :class="sidebarCollapsed ? 'flex-col gap-4' : ''">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-primary-container flex-shrink-0 flex items-center justify-center text-white shadow-inner">
                        <span class="material-symbols-outlined text-2xl">admin_panel_settings</span>
                    </div>
                    <div x-show="!sidebarCollapsed" x-transition.opacity class="flex-1">
                        <h1 class="text-xl font-extrabold text-white font-headline leading-tight whitespace-nowrap">ScanHadir</h1>
                        <p class="text-[10px] uppercase tracking-widest text-indigo-300 font-bold whitespace-nowrap">Admin Console</p>
                    </div>
                </div>
                <button @click="sidebarCollapsed = !sidebarCollapsed" class="p-2 hover:bg-white/10 rounded-lg text-white transition-colors flex items-center justify-center" title="Toggle Sidebar">
                    <span class="material-symbols-outlined text-2xl" x-text="sidebarCollapsed ? 'menu_open' : 'menu'">menu</span>
                </button>
            </div>
        </div>
        <nav class="flex-1 flex flex-col gap-1 px-3 pr-1 custom-scrollbar overflow-y-auto overflow-x-hidden">
            <!-- Main Menu Section -->
            <div class="mb-4">
                <button @click="activeGroups.main = !activeGroups.main" class="w-full flex items-center justify-between px-4 py-3 text-[10px] uppercase tracking-[0.2em] text-indigo-400 font-bold hover:text-white transition-colors group">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-sm" :class="activeGroups.main ? 'text-white' : 'text-indigo-400'">grid_view</span>
                        <span x-show="!sidebarCollapsed">Main Menu</span>
                    </div>
                    <span class="material-symbols-outlined text-xs transition-transform duration-300" :class="activeGroups.main ? 'rotate-180' : ''" x-show="!sidebarCollapsed">expand_more</span>
                </button>
                <div x-show="activeGroups.main" x-collapse x-transition.opacity>
                    <a class="flex items-center gap-4 {{ request()->routeIs('admin.dashboard') ? 'bg-primary-container text-white border-l-4 border-white' : 'text-indigo-200 hover:bg-white/10' }} rounded-xl px-4 py-3 transition-all mb-1" href="{{ route('admin.dashboard') }}">
                        <span class="material-symbols-outlined">dashboard</span>
                        <span class="font-medium text-sm whitespace-nowrap" x-show="!sidebarCollapsed">Dashboard</span>
                    </a>
                    <a class="flex items-center gap-4 {{ request()->routeIs('admin.analytics') ? 'bg-primary-container text-white border-l-4 border-white' : 'text-indigo-200 hover:bg-white/10' }} rounded-xl px-4 py-3 transition-all mb-1" href="{{ route('admin.analytics') }}">
                        <span class="material-symbols-outlined">analytics</span>
                        <span class="font-medium text-sm whitespace-nowrap" x-show="!sidebarCollapsed">Analytics</span>
                    </a>
                    <a class="flex items-center gap-4 {{ request()->routeIs('admin.logs') ? 'bg-primary-container text-white border-l-4 border-white' : 'text-indigo-200 hover:bg-white/10' }} rounded-xl px-4 py-3 transition-all mb-1" href="{{ route('admin.logs') }}">
                        <span class="material-symbols-outlined">history</span>
                        <span class="font-medium text-sm whitespace-nowrap" x-show="!sidebarCollapsed">Presensi Logs</span>
                    </a>
                    <a class="flex items-center gap-4 {{ request()->routeIs('admin.izin_approval') ? 'bg-primary-container text-white border-l-4 border-white' : 'text-indigo-200 hover:bg-white/10' }} rounded-xl px-4 py-3 transition-all mb-1" href="{{ route('admin.izin_approval') }}">
                        <span class="material-symbols-outlined">assignment_turned_in</span>
                        <span class="font-medium text-sm whitespace-nowrap" x-show="!sidebarCollapsed">Izin Approval</span>
                    </a>
                </div>
            </div>

            <!-- Master Data Section -->
            <div class="mb-4">
                <button @click="activeGroups.master = !activeGroups.master" class="w-full flex items-center justify-between px-4 py-3 text-[10px] uppercase tracking-[0.2em] text-indigo-400 font-bold hover:text-white transition-colors group">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-sm" :class="activeGroups.master ? 'text-white' : 'text-indigo-400'">database</span>
                        <span x-show="!sidebarCollapsed">Master Data</span>
                    </div>
                    <span class="material-symbols-outlined text-xs transition-transform duration-300" :class="activeGroups.master ? 'rotate-180' : ''" x-show="!sidebarCollapsed">expand_more</span>
                </button>
                <div x-show="activeGroups.master" x-collapse x-transition.opacity>
                    <a class="flex items-center gap-4 {{ request()->routeIs('admin.master.guru') ? 'bg-primary-container text-white border-l-4 border-white' : 'text-indigo-200 hover:bg-white/10' }} rounded-xl px-4 py-3 transition-all mb-1" href="{{ route('admin.master.guru') }}">
                        <span class="material-symbols-outlined">record_voice_over</span>
                        <span class="font-medium text-sm whitespace-nowrap" x-show="!sidebarCollapsed">Data Guru</span>
                    </a>
                    <a class="flex items-center gap-4 {{ request()->routeIs('admin.master.siswa') ? 'bg-primary-container text-white border-l-4 border-white' : 'text-indigo-200 hover:bg-white/10' }} rounded-xl px-4 py-3 transition-all mb-1" href="{{ route('admin.master.siswa') }}">
                        <span class="material-symbols-outlined">group</span>
                        <span class="font-medium text-sm whitespace-nowrap" x-show="!sidebarCollapsed">Data Siswa</span>
                    </a>
                    <a class="flex items-center gap-4 {{ request()->routeIs('admin.master.kelas') ? 'bg-primary-container text-white border-l-4 border-white' : 'text-indigo-200 hover:bg-white/10' }} rounded-xl px-4 py-3 transition-all mb-1" href="{{ route('admin.master.kelas') }}">
                        <span class="material-symbols-outlined">meeting_room</span>
                        <span class="font-medium text-sm whitespace-nowrap" x-show="!sidebarCollapsed">Data Kelas</span>
                    </a>
                    <a class="flex items-center gap-4 {{ request()->routeIs('admin.master.jadwal*') ? 'bg-primary-container text-white border-l-4 border-white' : 'text-indigo-200 hover:bg-white/10' }} rounded-xl px-4 py-3 transition-all mb-1" href="{{ route('admin.master.jadwal') }}">
                        <span class="material-symbols-outlined">calendar_month</span>
                        <span class="font-medium text-sm whitespace-nowrap" x-show="!sidebarCollapsed">Jadwal</span>
                    </a>
                    <a class="flex items-center gap-4 {{ request()->routeIs('admin.master.mapel') ? 'bg-primary-container text-white border-l-4 border-white' : 'text-indigo-200 hover:bg-white/10' }} rounded-xl px-4 py-3 transition-all mb-1" href="{{ route('admin.master.mapel') }}">
                        <span class="material-symbols-outlined">library_books</span>
                        <span class="font-medium text-sm whitespace-nowrap" x-show="!sidebarCollapsed">Mata Pelajaran</span>
                    </a>
                </div>
            </div>

            <!-- System Section -->
            <div class="mb-12">
                <button @click="activeGroups.system = !activeGroups.system" class="w-full flex items-center justify-between px-4 py-3 text-[10px] uppercase tracking-[0.2em] text-indigo-400 font-bold hover:text-white transition-colors group">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-sm" :class="activeGroups.system ? 'text-white' : 'text-indigo-400'">settings_applications</span>
                        <span x-show="!sidebarCollapsed">System</span>
                    </div>
                    <span class="material-symbols-outlined text-xs transition-transform duration-300" :class="activeGroups.system ? 'rotate-180' : ''" x-show="!sidebarCollapsed">expand_more</span>
                </button>
                <div x-show="activeGroups.system" x-collapse x-transition.opacity class="pb-10">
                    <a class="flex items-center gap-4 {{ request()->routeIs('admin.scanner') ? 'bg-primary-container text-white border-l-4 border-white' : 'text-indigo-200 hover:bg-white/10' }} rounded-xl px-4 py-3 transition-all mb-1" href="{{ route('admin.scanner') }}">
                        <span class="material-symbols-outlined">qr_code_scanner</span>
                        <span class="font-medium text-sm whitespace-nowrap" x-show="!sidebarCollapsed">Scanner</span>
                    </a>
                    <a class="flex items-center gap-4 {{ request()->routeIs('admin.reports*') ? 'bg-primary-container text-white border-l-4 border-white' : 'text-indigo-200 hover:bg-white/10' }} rounded-xl px-4 py-3 transition-all mb-1" href="{{ route('admin.reports') }}">
                        <span class="material-symbols-outlined">summarize</span>
                        <span class="font-medium text-sm whitespace-nowrap" x-show="!sidebarCollapsed">Reports</span>
                    </a>
                    <a class="flex items-center gap-4 {{ request()->routeIs('admin.settings') ? 'bg-primary-container text-white border-l-4 border-white' : 'text-indigo-200 hover:bg-white/10' }} rounded-xl px-4 py-3 transition-all mb-1" href="{{ route('admin.settings') }}">
                        <span class="material-symbols-outlined">settings</span>
                        <span class="font-medium text-sm whitespace-nowrap" x-show="!sidebarCollapsed">Settings</span>
                    </a>
                </div>
            </div>
        </nav>
    </aside>

    <main class="min-h-screen transition-all duration-300" :class="sidebarCollapsed ? 'md:ml-20' : 'md:ml-72'">
        <header class="w-full sticky top-0 z-40 flex justify-between items-center px-8 py-4 bg-white/80 backdrop-blur-md border-b border-slate-100 shadow-sm transition-all duration-300">
            <div class="flex flex-col">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-[0.2em] font-headline">
                        @if(Request::is('admin/master/*'))
                            ADMIN CONSOLE / MASTER DATA
                        @elseif(Request::is('admin/analytics'))
                            ADMIN CONSOLE / ANALYTICS
                        @elseif(Request::is('admin/reports*'))
                            ADMIN CONSOLE / REPORTS
                        @else
                            ADMIN CONSOLE / OVERVIEW
                        @endif
                    </span>
                </div>
                <h2 class="text-2xl font-extrabold text-indigo-900 font-headline tracking-tight">{{ $title ?? 'Dashboard' }}</h2>
            </div>
            
            <div class="flex items-center gap-6">
                <!-- Functional Global Search -->
                <div class="hidden lg:block relative" x-data="adminSearch()" @keydown.escape="open=false" @click.away="open=false">
                    <div class="flex items-center gap-3 px-4 py-2 bg-slate-100/50 border border-slate-200/50 rounded-2xl w-72 focus-within:w-96 focus-within:bg-white focus-within:border-primary/20 transition-all duration-300 group shadow-inner">
                        <span class="material-symbols-outlined text-slate-400 text-xl group-focus-within:text-primary transition-colors">search</span>
                        <input x-model="q" @input.debounce.250ms="search()" @focus="open=true" type="text" placeholder="Cari siswa, guru, mapel..." class="bg-transparent border-none text-sm outline-none w-full font-medium placeholder:text-slate-400 focus:ring-0 shadow-none"/>
                        <span x-show="loading" class="material-symbols-outlined animate-spin text-slate-300 text-base">progress_activity</span>
                        <kbd x-show="!loading && q.length===0" class="text-[10px] font-bold text-slate-300 border border-slate-200 rounded px-1.5 py-0.5">/</kbd>
                    </div>
                    <div x-show="open" x-transition x-cloak class="absolute mt-2 w-96 right-0 bg-white rounded-2xl shadow-xl shadow-indigo-100/40 border border-slate-100 overflow-hidden z-50 max-h-[70vh] overflow-y-auto custom-scrollbar">
                        <template x-if="q.length===0">
                            <div class="p-2">
                                <p class="px-3 py-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">Quick Navigation</p>
                                <template x-for="nav in quickNav" :key="nav.url">
                                    <a :href="nav.url" class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-slate-50 transition-colors">
                                        <span class="material-symbols-outlined text-slate-400 text-[20px]" x-text="nav.icon"></span>
                                        <span class="text-sm font-semibold text-on-surface" x-text="nav.label"></span>
                                    </a>
                                </template>
                            </div>
                        </template>
                        <template x-if="q.length>0">
                            <div class="p-2">
                                <template x-if="!loading && results.length===0">
                                    <p class="px-3 py-6 text-center text-sm text-slate-400">Tidak ada hasil untuk "<span x-text="q" class="font-bold"></span>"</p>
                                </template>
                                <template x-for="r in results" :key="r.type + r.url">
                                    <a :href="r.url" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-indigo-50/50 transition-colors">
                                        <span class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center text-primary shrink-0">
                                            <span class="material-symbols-outlined text-[20px]" x-text="r.icon"></span>
                                        </span>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-bold text-on-surface truncate" x-text="r.label"></p>
                                            <p class="text-[11px] text-slate-400 truncate" x-text="r.sublabel"></p>
                                        </div>
                                        <span class="text-[10px] font-bold uppercase text-slate-300 shrink-0" x-text="r.type"></span>
                                    </a>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <button class="w-10 h-10 flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-primary rounded-xl transition-all relative">
                        <span class="material-symbols-outlined">notifications</span>
                        <span class="absolute top-2 right-2 w-2 h-2 bg-error rounded-full border-2 border-white"></span>
                    </button>
                    
                    <div class="h-8 w-px bg-slate-200"></div>

                    <div class="flex items-center gap-3 pl-2">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-bold text-on-surface leading-tight">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] text-primary font-bold uppercase tracking-wider">{{ auth()->user()->role_label }}</p>
                        </div>
                        @if(auth()->user()->photo_path)
                            <img src="{{ auth()->user()->photo_url }}" alt="{{ auth()->user()->name }}" class="w-10 h-10 rounded-xl object-cover shadow-inner border border-primary/5" />
                        @else
                            <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary font-bold shadow-inner border border-primary/5">
                                {{ collect(explode(' ', auth()->user()->name))->take(2)->map(fn($n) => strtoupper(substr($n, 0, 1)))->implode('') }}
                            </div>
                        @endif
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

        <div class="p-8">
            @if (isset($slot))
                {{ $slot }}
            @else
                @yield('content')
            @endif
        </div>
    </main>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('adminSearch', () => ({
                q: '',
                open: false,
                loading: false,
                results: [],
                quickNav: [
                    { label: 'Dashboard', icon: 'dashboard', url: '{{ route('admin.dashboard') }}' },
                    { label: 'Analytics', icon: 'analytics', url: '{{ route('admin.analytics') }}' },
                    { label: 'Data Siswa', icon: 'group', url: '{{ route('admin.master.siswa') }}' },
                    { label: 'Data Guru', icon: 'record_voice_over', url: '{{ route('admin.master.guru') }}' },
                    { label: 'Mata Pelajaran', icon: 'library_books', url: '{{ route('admin.master.mapel') }}' },
                    { label: 'Jadwal', icon: 'calendar_month', url: '{{ route('admin.master.jadwal') }}' },
                    { label: 'Scanner', icon: 'qr_code_scanner', url: '{{ route('admin.scanner') }}' },
                ],
                async search() {
                    if (this.q.trim().length === 0) { this.results = []; this.loading = false; return; }
                    this.loading = true;
                    this.open = true;
                    try {
                        const res = await fetch('{{ route('admin.search') }}?q=' + encodeURIComponent(this.q), { headers: { 'Accept': 'application/json' } });
                        const data = await res.json();
                        this.results = data.results || [];
                    } catch (e) {
                        this.results = [];
                    }
                    this.loading = false;
                },
            }));
        });
    </script>

    @livewireScripts

    <!-- Global Toast Container -->
    <div id="toastContainer" class="fixed top-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none"></div>
    <script>
        function showToast(message, type = 'info') {
            const container = document.getElementById('toastContainer');
            if(!container) return;
            
            const colors = {
                info: 'bg-indigo-50 border-indigo-200 text-indigo-800',
                success: 'bg-emerald-50 border-emerald-200 text-emerald-800',
                error: 'bg-rose-50 border-rose-200 text-rose-800',
                warning: 'bg-amber-50 border-amber-200 text-amber-800',
            };
            const icons = {
                info: 'info',
                success: 'check_circle',
                error: 'error',
                warning: 'warning',
            };

            const toast = document.createElement('div');
            toast.className = `flex items-center gap-3 px-4 py-3 rounded-2xl border ${colors[type] || colors.info} shadow-lg shadow-indigo-100/50 pointer-events-auto transition-all duration-300 translate-x-12 opacity-0`;
            toast.innerHTML = `
                <span class="material-symbols-outlined text-[20px]">${icons[type] || 'info'}</span>
                <span class="text-xs font-bold">${message}</span>
            `;

            container.appendChild(toast);
            setTimeout(() => {
                toast.classList.remove('translate-x-12', 'opacity-0');
            }, 100);

            setTimeout(() => {
                toast.classList.add('translate-x-12', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        /**
         * Global File Download Utility
         * Forces the browser to use a specific filename using client-side Blob triggers.
         */
        async function downloadFile(url, filename) {
            try {
                const response = await fetch(url);
                if (!response.ok) throw new Error('Network response was not ok');
                const blob = await response.blob();
                const blobUrl = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = blobUrl;
                link.download = filename;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.URL.revokeObjectURL(blobUrl);
            } catch (error) {
                console.error('Download failed:', error);
                showToast('Gagal mengunduh file.', 'error');
            }
        }
    </script>
</body>
</html>
