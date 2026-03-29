<div class="min-h-screen bg-slate-900 flex flex-col items-center justify-center p-4">
    <!-- Background Accents -->
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-indigo-500/10 rounded-full blur-[120px]"></div>
    </div>

    <div class="w-full max-w-md">
        <div class="text-center mb-8 animate-fadeIn">
            <h1 class="text-4xl font-extrabold text-white tracking-tight mb-2">Masuk</h1>
            <p class="text-slate-400">Portal Siswa ScanHadir</p>
        </div>

        <div class="glass p-8 rounded-[2.5rem] shadow-2xl border border-white/10 animate-slideUp">
            <form wire:submit.prevent="login" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-300 mb-2 ml-1">Email</label>
                    <input wire:model="email" type="email" id="email" required 
                        class="w-full bg-slate-800/50 border border-slate-700/50 rounded-2xl px-5 py-4 text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all outline-none placeholder:text-slate-600"
                        placeholder="nama@sekolah.sch.id">
                    @error('email') <span class="text-rose-400 text-xs mt-2 ml-1 block italic">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-300 mb-2 ml-1">Password</label>
                    <input wire:model="password" type="password" id="password" required 
                        class="w-full bg-slate-800/50 border border-slate-700/50 rounded-2xl px-5 py-4 text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all outline-none">
                </div>

                <div class="flex items-center justify-between px-1">
                    <label class="inline-flex items-center group cursor-pointer">
                        <input type="checkbox" wire:model="remember" class="w-4 h-4 rounded border-slate-700 bg-slate-800 text-indigo-600 focus:ring-0 transition-colors">
                        <span class="ml-2 text-sm text-slate-400 group-hover:text-slate-300 transition-colors">Ingat saya</span>
                    </label>
                    <a href="#" class="text-sm text-indigo-400 hover:text-indigo-300 transition-colors font-medium">Lupa password?</a>
                </div>

                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-4 rounded-2xl shadow-xl shadow-indigo-600/20 transition-all hover:scale-[1.02] active:scale-95 flex items-center justify-center group">
                    <span>Masuk ke Portal</span>
                    <svg class="w-5 h-5 ml-2 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </button>
            </form>
        </div>

        <p class="text-center mt-8 text-slate-500 text-sm animate-fadeIn animation-delay-500">
            Bermasalah saat masuk? Hubungi Admin Sekolah.
        </p>
    </div>

    <style>
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-slideUp { animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        .animation-delay-500 { animation-delay: 0.5s; }
    </style>
</div>
