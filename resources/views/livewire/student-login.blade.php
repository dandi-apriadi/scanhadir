<div class="min-h-screen bg-slate-900 flex flex-col items-center justify-center p-4">
    <!-- Background Accents -->
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-indigo-500/10 rounded-full blur-[120px]"></div>
    </div>

    <div class="w-full max-w-md">
        <div class="text-center mb-8 animate-fadeIn">
            <h1 class="text-4xl font-extrabold text-white tracking-tight mb-2">Masuk</h1>
            <p class="text-slate-400">Portal ScanHadir</p>
        </div>

        <!-- Error Alert -->
        @if($errorMessage)
            <div class="mb-6 p-4 bg-red-500/20 border border-red-500/50 rounded-2xl animate-shake">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-red-300">{{ $errorMessage }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="glass p-8 rounded-[2.5rem] shadow-2xl border border-white/10 animate-slideUp">
            <form wire:submit.prevent="login" class="space-y-6">
                <div>
                    <label for="identifier" class="block text-sm font-medium text-slate-300 mb-2 ml-1">Email / NISN</label>
                    <input wire:model="identifier" type="text" id="identifier" required 
                        class="w-full bg-slate-800/50 border @error('identifier') border-red-500/50 @else border-slate-700/50 @enderror rounded-2xl px-5 py-4 text-white focus:ring-2 @error('identifier') focus:ring-red-500 @else focus:ring-indigo-500 @enderror focus:border-transparent transition-all outline-none placeholder:text-slate-600"
                        placeholder="email@sekolah.sch.id atau 13 digit NISN"
                        :disabled="$isLoading">
                    @error('identifier') 
                        <span class="text-red-400 text-xs mt-2 ml-1 block italic flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18.101 12.93a1 1 0 00-1.01-1.986c-.04.005-.08.01-.12.015a2 2 0 11-3.976-1.479A2.002 2.002 0 0013 8h.879a1.97 1.97 0 001.469-.612l.945-.945a1 1 0 10-1.414-1.414l-.945.945A.969.969 0 0113.879 6H13a4 4 0 00-4 4 3.975 3.975 0 00.564 1.5 1 1 0 001.732-1 1.977 1.977 0 01-.296-.5.99.99 0 01.04-.882 3.011 3.011 0 015.252-.555 1 1 0 11-1.666 1.666 1.01 1.01 0 00-1.75.185 1 1 0 001.414 1.414l.707-.707a1 1 0 011.414 0l2.121 2.121a1 1 0 01-1.414 1.414l-1.414-1.414-.707.707a1 1 0 001.414 1.414l.707-.707 1.414 1.414a1 1 0 01-1.414 1.414l-2.121-2.121a1 1 0 010-1.414l.707-.707-1.414-1.414z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </span> 
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-300 mb-2 ml-1">Password</label>
                    <input wire:model="password" type="password" id="password" required 
                        class="w-full bg-slate-800/50 border @error('password') border-red-500/50 @else border-slate-700/50 @enderror rounded-2xl px-5 py-4 text-white focus:ring-2 @error('password') focus:ring-red-500 @else focus:ring-indigo-500 @enderror focus:border-transparent transition-all outline-none"
                        placeholder="••••••••"
                        :disabled="$isLoading">
                    @error('password') 
                        <span class="text-red-400 text-xs mt-2 ml-1 block italic flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18.101 12.93a1 1 0 00-1.01-1.986c-.04.005-.08.01-.12.015a2 2 0 11-3.976-1.479A2.002 2.002 0 0013 8h.879a1.97 1.97 0 001.469-.612l.945-.945a1 1 0 10-1.414-1.414l-.945.945A.969.969 0 0113.879 6H13a4 4 0 00-4 4 3.975 3.975 0 00.564 1.5 1 1 0 001.732-1 1.977 1.977 0 01-.296-.5.99.99 0 01.04-.882 3.011 3.011 0 015.252-.555 1 1 0 11-1.666 1.666 1.01 1.01 0 00-1.75.185 1 1 0 001.414 1.414l.707-.707a1 1 0 011.414 0l2.121 2.121a1 1 0 01-1.414 1.414l-1.414-1.414-.707.707a1 1 0 001.414 1.414l.707-.707 1.414 1.414a1 1 0 01-1.414 1.414l-2.121-2.121a1 1 0 010-1.414l.707-.707-1.414-1.414z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </span> 
                    @enderror
                </div>

                <div class="flex items-center justify-between px-1">
                    <label class="inline-flex items-center group cursor-pointer">
                        <input type="checkbox" wire:model="remember" class="w-4 h-4 rounded border-slate-700 bg-slate-800 text-indigo-600 focus:ring-0 transition-colors" :disabled="$isLoading">
                        <span class="ml-2 text-sm text-slate-400 group-hover:text-slate-300 transition-colors">Ingat saya</span>
                    </label>
                    <a href="#" class="text-sm text-indigo-400 hover:text-indigo-300 transition-colors font-medium">Lupa password?</a>
                </div>

                <button type="submit" 
                    class="w-full bg-indigo-600 hover:bg-indigo-500 disabled:bg-indigo-700 disabled:opacity-50 text-white font-bold py-4 rounded-2xl shadow-xl shadow-indigo-600/20 transition-all hover:scale-[1.02] active:scale-95 disabled:hover:scale-100 flex items-center justify-center group"
                    :disabled="$isLoading">
                    @if($isLoading)
                        <svg class="animate-spin -ml-1 mr-3 w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Memproses...</span>
                    @else
                        <span>Masuk ke Portal</span>
                        <svg class="w-5 h-5 ml-2 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    @endif
                </button>
            </form>
        </div>

        <p class="text-center mt-8 text-slate-500 text-sm animate-fadeIn animation-delay-500">
            Bermasalah saat masuk? Hubungi Admin Sekolah.
        </p>

        <!-- Test Credentials Info -->
        <div class="mt-8 p-4 bg-slate-800/50 rounded-2xl border border-slate-700/50 text-xs text-slate-400">
            <p class="font-semibold text-slate-300 mb-2">📝 Akun Uji Coba:</p>
            <p class="mb-1"><span class="text-indigo-400">Admin:</span> admin@scanhadir.com / admin123</p>
            <p class="mb-1"><span class="text-indigo-400">Guru:</span> guru1@sekolah.sch.id / guru123</p>
            <p><span class="text-indigo-400">Siswa:</span> rizki@sekolah.sch.id / siswa123</p>
        </div>
    </div>

    <style>
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        .animate-slideUp { animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        .animate-shake { animation: shake 0.4s ease-in-out; }
        .animation-delay-500 { animation-delay: 0.5s; }
    </style>
</div>

